<?php

declare(strict_types=1);

namespace Kumwe\Audit\Tests;

use InvalidArgumentException;
use Kumwe\Audit\Application\AuditTrailExport;
use Kumwe\Audit\Domain\AuditEnforcementState;
use Kumwe\Audit\Domain\AuditVerificationFinding;
use Kumwe\Audit\Domain\AuditVerificationReport;
use Kumwe\Audit\Domain\StoredAuditArchive;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AuditEvidenceTest extends TestCase
{
    #[DataProvider('invalidEvidence')]
    public function testMalformedEvidenceIsRefusedAtTheBoundary(callable $create): void
    {
        $this->expectException(InvalidArgumentException::class);
        $create();
    }

    public static function invalidEvidence(): iterable
    {
        $archive = new StoredAuditArchive('archive.jsonl', 0, hash('sha256', ''));
        foreach (['', "\xff", "archive\0.jsonl", str_repeat('a', 1025)] as $key) {
            yield [static fn () => new StoredAuditArchive($key, 0, hash('sha256', ''))];
        }
        yield [static fn () => new StoredAuditArchive('archive', -1, hash('sha256', ''))];
        foreach (['', str_repeat('A', 64), str_repeat('a', 63), 'sha256:bad'] as $checksum) {
            yield [static fn () => new StoredAuditArchive('archive', 0, $checksum)];
        }
        foreach ([[0, 1, 1, 0, null], [2, 1, 1, 0, null], [1, 2, 0, 0, null],
            [1, 2, 3, 0, null], [1, 2, 1, -1, null], [1, 2, 1, 0, 0]] as $args) {
            yield [static fn () => new AuditTrailExport($archive, ...$args)];
        }
        foreach ([[-1, 0, 0], [0, -1, 0], [0, 0, -1], [2, 0, 1]] as $counts) {
            yield [static fn () => new AuditVerificationReport(...[...$counts, AuditEnforcementState::Active])];
        }
        foreach ([['bad code', 1, 'Mismatch', null], ['event.mismatch', -1, 'Mismatch', null],
            ['event.mismatch', 1, '', null], ['event.mismatch', 1, "\xff", null],
            ['event.mismatch', 1, str_repeat('a', 4097), null], ['event.mismatch', 1, 'Mismatch', "event\n2"]] as $args) {
            yield [static fn () => new AuditVerificationFinding(...$args)];
        }
    }

    public function testExactBoundsAndSparseExportRangesRemainValid(): void
    {
        $archive = new StoredAuditArchive(str_repeat('a', 1024), 0, hash('sha256', ''));
        $export = new AuditTrailExport($archive, 1, 10, 2, 0, 1);
        self::assertSame(2, $export->eventCount);
        $report = new AuditVerificationReport(0, 0, 0, AuditEnforcementState::NotInstalled);
        self::assertTrue($report->intact());
        self::assertFalse($report->guarded());
        $finding = new AuditVerificationFinding('event.mismatch', 0, str_repeat('é', 2048), str_repeat('a', 191));
        self::assertSame(4096, strlen($finding->detail));
    }
}
