<?php

declare(strict_types=1);

namespace Kumwe\Audit\Tests;

use Kumwe\Audit\Domain\AuditAnchorDigest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AuditCorpusTest extends TestCase
{
    #[DataProvider('vectors')]
    public function testLanguageNeutralRollingEvidence(array $vector): void
    {
        $stream = static function () use ($vector): iterable {
            foreach ($vector['digests'] as [$position, $digest]) {
                yield $position => $digest;
            }
        };
        self::assertSame($vector['sha256'], hash('sha256', $vector['material']));
        self::assertSame($vector['sha256'], AuditAnchorDigest::rolling($stream()));
    }

    public static function vectors(): iterable
    {
        $corpus = json_decode(file_get_contents(__DIR__ . '/../resources/audit-corpus/rolling-v1.json'),
            true, 512, JSON_THROW_ON_ERROR);
        foreach ($corpus['cases'] as $vector) {
            yield $vector['id'] => [$vector];
        }
    }
}
