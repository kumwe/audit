<?php

declare(strict_types=1);

use Kumwe\Audit\Application\AuditArchiveStorage;
use Kumwe\Audit\Application\AuditMetadataRedactor;
use Kumwe\Audit\Application\AuditRecorder;
use Kumwe\Audit\Domain\AuditEvent;
use Kumwe\Audit\Domain\AuditAnchorDigest;
use Kumwe\Audit\Domain\StoredAuditArchive;

require $argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php';

$recorder = new class implements AuditRecorder {
    public array $events = [];
    public function record(AuditEvent $event): void
    {
        $this->events[] = $event;
    }
};
$storage = new class implements AuditArchiveStorage {
    public array $archives = [];
    public function store(string $archiveId, iterable $chunks): StoredAuditArchive
    {
        $bytes = '';
        foreach ($chunks as $chunk) {
            $bytes .= $chunk;
        }
        $this->archives[$archiveId] = $bytes;
        return new StoredAuditArchive($archiveId, strlen($bytes), hash('sha256', $bytes));
    }
};
$event = new AuditEvent('8bd4ec65-92f2-4934-afb8-b22a3cf956cd', new DateTimeImmutable('2026-08-04T12:00:00Z'), null, 'content.read', 'content', 'content-1', 'allowed', ['request_id' => 'request-1']);
$recorder->record($event);
$count = 0;
$redacted = AuditMetadataRedactor::redact(['request_id' => 'request-1', 'api_key' => 'example-secret'], $count);
$archive = $storage->store('example-archive', [json_encode($redacted, JSON_THROW_ON_ERROR), "\n"]);
$digest = hash('sha256', $event->metadataAsJson());
$rolling = AuditAnchorDigest::rolling([1 => $digest]);
if (count($recorder->events) !== 1 || $count !== 1 || str_contains($storage->archives['example-archive'], 'example-secret') || $archive->checksum !== hash('sha256', $storage->archives['example-archive']) || strlen($rolling) !== 64) {
    throw new RuntimeException('Audit consumer contract failed.');
}
echo "Recording, bounded redaction, archive evidence and rolling digest verified.\n";
