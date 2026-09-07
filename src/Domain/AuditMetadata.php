<?php

declare(strict_types=1);

namespace Kumwe\Audit\Domain;

use InvalidArgumentException;

/** Bounded, detached metadata snapshots without invoking user callbacks. @since 0.1.0 */
final class AuditMetadata
{
    public const int MAX_DEPTH = 32;
    public const int MAX_ENTRIES = 512;
    public const int MAX_BYTES = 65536;

    /**
     * Validate and detach JSON metadata. Empty metadata remains an empty PHP array.
     * @param array<string, mixed> $metadata Safe context, with string keys at the root.
     * @return array<string, mixed> Snapshot containing only arrays and JSON scalars.
     * @throws InvalidArgumentException On invalid UTF-8, objects, resources, recursion or a bound violation.
     * @since 0.1.0
     */
    public static function snapshot(array $metadata): array
    {
        foreach (array_keys($metadata) as $key) {
            if (!is_string($key)) {
                throw new InvalidArgumentException('Audit metadata must be an object with string keys.');
            }
        }
        $entries = 0;
        self::validate($metadata, 0, $entries);
        try {
            $bytes = json_encode($metadata, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
            if (strlen($bytes) > self::MAX_BYTES) {
                throw new InvalidArgumentException('Audit metadata exceeds its byte budget.');
            }
            /** @var array<string, mixed> $snapshot */
            $snapshot = json_decode($bytes, true, 512, JSON_THROW_ON_ERROR);
            return $snapshot;
        } catch (\JsonException $error) {
            throw new InvalidArgumentException('Audit metadata must be JSON-serializable.', 0, $error);
        }
    }

    private static function validate(mixed $value, int $depth, int &$entries): void
    {
        if ($depth > self::MAX_DEPTH) {
            throw new InvalidArgumentException('Audit metadata exceeds its depth budget.');
        }
        if (is_array($value)) {
            foreach ($value as $key => $entry) {
                if (++$entries > self::MAX_ENTRIES || (is_string($key) && strlen($key) > self::MAX_BYTES)) {
                    throw new InvalidArgumentException('Audit metadata exceeds its entry budget.');
                }
                self::validate($entry, $depth + 1, $entries);
            }
            return;
        }
        if (is_object($value) || is_resource($value) || (is_float($value) && !is_finite($value))) {
            throw new InvalidArgumentException('Audit metadata accepts only finite JSON scalar values and arrays.');
        }
        if (is_string($value) && strlen($value) > self::MAX_BYTES) {
            throw new InvalidArgumentException('Audit metadata exceeds its byte budget.');
        }
    }
}
