<?php

declare(strict_types=1);

namespace Kumwe\Audit\Tests;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Kumwe\Audit\Application\AuditMetadataRedactor;
use Kumwe\Audit\Domain\AuditAnchorDigest;
use Kumwe\Audit\Domain\AuditEnforcementState;
use Kumwe\Audit\Domain\AuditEvent;
use Kumwe\Audit\Domain\AuditEventDigest;
use Kumwe\Audit\Domain\AuditMetadata;
use Kumwe\Audit\Domain\AuditVerificationFinding;
use Kumwe\Audit\Domain\AuditVerificationReport;
use Kumwe\CanonicalJson\CanonicalEncoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AuditBoundaryTest extends TestCase
{
    public function testMetadataReferencesAreDetached(): void
    {
        $value = 'safe';
        $event = $this->event(['context' => ['value' => &$value]]);
        $value = 'changed';
        self::assertSame(['context' => ['value' => 'safe']], $event->metadata());
        self::assertSame('8bd4ec65-92f2-4934-afb8-b22a3cf956cd', $event->id());
        self::assertNull($event->actorId());
        self::assertNull($event->subjectId());
        self::assertSame('{}', $this->event([])->metadataAsJson());
    }

    public function testJsonCallbacksAreNeverInvoked(): void
    {
        $object = new class implements JsonSerializable {
            public bool $called = false;
            public function jsonSerialize(): mixed
            {
                $this->called = true;
                return 'secret';
            }
        };
        try {
            $this->event(['payload' => $object]);
            self::fail('Callback-bearing input was accepted.');
        } catch (InvalidArgumentException) {
            self::assertFalse($object->called);
        }
    }

    #[DataProvider('invalidMetadata')]
    public function testHostileMetadataRefused(array $metadata): void
    {
        $this->expectException(InvalidArgumentException::class);
        AuditMetadata::snapshot($metadata);
    }

    public static function invalidMetadata(): iterable
    {
        yield [['text' => "\xff"]];
        yield [['number' => INF]];
        yield [['number' => NAN]];
        yield [['object' => new \stdClass()]];
        yield [['text' => str_repeat('a', AuditMetadata::MAX_BYTES)]];
        yield [['entries' => array_fill(0, 512, 1)]];
        $deep = 'end';
        for ($index = 0; $index < 33; $index++) {
            $deep = ['child' => $deep];
        }
        yield [$deep];
        yield [['numeric root']];
    }

    public function testDepthEntryAndByteBoundaries(): void
    {
        $deep = 'end';
        for ($index = 0; $index < AuditMetadata::MAX_DEPTH; $index++) {
            $deep = ['child' => $deep];
        }
        self::assertSame($deep, AuditMetadata::snapshot($deep));
        $entries = ['entries' => array_fill(0, 511, 1)];
        self::assertSame($entries, AuditMetadata::snapshot($entries));
        $text = ['x' => str_repeat('a', AuditMetadata::MAX_BYTES - 8)];
        self::assertSame($text, AuditMetadata::snapshot($text));
    }

    public function testRecursiveArraysRefusedWithoutTouchingRedactionCounter(): void
    {
        $metadata = ['password' => 'secret'];
        $metadata['cycle'] = &$metadata;
        $count = 7;
        try {
            AuditMetadataRedactor::redact($metadata, $count);
            self::fail('Cyclic metadata accepted.');
        } catch (InvalidArgumentException) {
            self::assertSame(7, $count);
        }
    }

    public function testRedactionDetachesListsAndPreservesOriginalEvidence(): void
    {
        $token = 'secret';
        $metadata = ['list' => [['api_key' => &$token], ['public' => 'safe']]];
        $count = 2;
        $redacted = AuditMetadataRedactor::redact($metadata, $count);
        self::assertSame(3, $count);
        self::assertSame('secret', $metadata['list'][0]['api_key']);
        self::assertSame('[redacted]', $redacted['list'][0]['api_key']);
        self::assertSame('safe', $redacted['list'][1]['public']);
    }

    public function testEventDigestUsesExactVersionedMaterialAndExplicitEncoder(): void
    {
        $material = ['id' => 'event-1', 'occurred_at' => '2026-08-04 12:00:00', 'actor_id' => null, 'action' => 'read', 'subject_type' => 'content', 'subject_id' => 'row-1', 'outcome' => 'allowed', 'metadata' => ['a' => 1]];
        $bytes = '{"action":"read","actor_id":null,"id":"event-1","metadata":{"a":1},"occurred_at":"2026-08-04 12:00:00","outcome":"allowed","subject_id":"row-1","subject_type":"content"}';
        $encoder = $this->createMock(CanonicalEncoder::class);
        $encoder->expects(self::once())->method('encode')->with($material)->willReturn($bytes);
        self::assertSame(hash('sha256', "kumwe-audit-event-v1\n" . $bytes), AuditEventDigest::compute('event-1', '2026-08-04 12:00:00', null, 'read', 'content', 'row-1', 'allowed', ['a' => 1], $encoder));
    }

    public function testAnchorDigestBindsPreviousDigestAndArchive(): void
    {
        $sha = str_repeat('a', 64);
        $material = ['sequence' => 2, 'kind' => 'prune', 'from_position' => 1, 'to_position' => 3, 'row_count' => 3, 'rolling_digest' => $sha, 'previous_digest' => $sha, 'archive_sha256' => $sha, 'created_at' => '2026-08-04 12:00:00'];
        $encoder = $this->createMock(CanonicalEncoder::class);
        $encoder->expects(self::once())->method('encode')->with($material)->willReturn('canonical-anchor-fixture');
        self::assertSame(hash('sha256', "kumwe-audit-anchor-v1\ncanonical-anchor-fixture"), AuditAnchorDigest::compute(2, 'prune', 1, 3, 3, $sha, $sha, $sha, '2026-08-04 12:00:00', $encoder));
    }

    public function testRollingEvidenceDetectsMutationDeletionReorderingAndGap(): void
    {
        $a = hash('sha256', 'a');
        $b = hash('sha256', 'b');
        $expected = hash('sha256', "kumwe-audit-anchor-v1\n1:$a\n2:$b\n");
        self::assertSame($expected, AuditAnchorDigest::rolling([1 => $a, 2 => $b]));
        foreach ([[1 => $a, 2 => $a], [1 => $a], [2 => $b, 1 => $a], [1 => $a, 3 => $b]] as $mutated) {
            self::assertNotSame($expected, AuditAnchorDigest::rolling($mutated));
        }
        self::assertSame(hash('sha256', "kumwe-audit-anchor-v1\n"), AuditAnchorDigest::rolling([]));
    }

    public function testEvidenceDoesNotClaimHostEnforcement(): void
    {
        $intact = new AuditVerificationReport(2, 1, 2, AuditEnforcementState::NotInstalled);
        self::assertTrue($intact->intact());
        self::assertFalse($intact->guarded());
        $guarded = new AuditVerificationReport(2, 1, 2, AuditEnforcementState::Active);
        self::assertTrue($guarded->guarded());
        $finding = new AuditVerificationFinding('event.digest.mismatch', 2, 'Mismatch', 'event-2');
        $broken = new AuditVerificationReport(1, 0, 2, AuditEnforcementState::Active, $finding);
        self::assertFalse($broken->intact());
        self::assertFalse($broken->guarded());
        self::assertSame($finding, $broken->firstDivergence);
        self::assertStringContainsString('NOT installed', AuditEnforcementState::NotInstalled->summary());
    }

    private function event(array $metadata): AuditEvent
    {
        return new AuditEvent('8bd4ec65-92f2-4934-afb8-b22a3cf956cd', new DateTimeImmutable('2026-08-04T12:00:00Z'), null, 'content.read', 'content', null, 'allowed', $metadata);
    }
}
