<?php

declare(strict_types=1);

namespace Kumwe\Audit\Domain;

/**
 * Evidence for one audit archive file written to private storage: its key, byte size and checksum.
 *
 * The values are captured while the bytes stream through the writer, so the checksum covers exactly
 * what landed on disk. Callers persist all three inside the audit trail itself — an export event or a
 * prune mark — which is what lets an operator later prove an archive file is the one the trail names.
 *
 * @since  0.1.0
 */
final readonly class StoredAuditArchive
{
    /**
     * Capture the stored-object evidence.
     *
     * @param  string  $key       Opaque storage key of the archive inside the private audit directory.
     * @param  int     $size      Exact byte size of the stored archive.
     * @param  string  $checksum  Lowercase hexadecimal SHA-256 of the stored bytes.
     *
     * @since  0.1.0
     */
    public function __construct(
        public string $key,
        public int $size,
        public string $checksum,
    ) {
        if (
            $key === '' || strlen($key) > 1024 || preg_match('//u', $key) !== 1
            || preg_match('/[\x00-\x1F\x7F]/', $key) === 1
            || $size < 0 || preg_match('/^[a-f0-9]{64}$/D', $checksum) !== 1
        ) {
            throw new \InvalidArgumentException('Stored audit archive evidence is invalid.');
        }
    }
}
