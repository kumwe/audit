<?php

declare(strict_types=1);

namespace Kumwe\Audit\Domain;

/**
 * The first divergence a verification pass found in the audit trail or its anchor ledger.
 *
 * A finding names the machine-readable divergence class, the position it anchors to and an operator
 * message. Verification stops at the first divergence because everything after a broken link or a
 * mismatched digest is unreliable evidence: the point of the report is where trust ends, not an
 * inventory of every consequence downstream of it.
 *
 * @since  0.1.0
 */
final readonly class AuditVerificationFinding
{
    /**
     * Capture one divergence.
     *
     * @param  string   $code      Machine token classifying the divergence, such as `event.digest.mismatch`.
     * @param  int      $position  Audit or anchor position the divergence anchors to; zero when none applies.
     * @param  string   $detail    Operator-facing explanation of what disagreed.
     * @param  ?string  $eventId   UUID of the divergent audit row when the divergence names one.
     *
     * @since  0.1.0
     */
    public function __construct(
        public string $code,
        public int $position,
        public string $detail,
        public ?string $eventId = null,
    ) {
        if (
            preg_match('/^[a-z][a-z0-9._:-]{0,126}$/D', $code) !== 1 || $position < 0
            || $detail === '' || strlen($detail) > 4096 || preg_match('//u', $detail) !== 1
            || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $detail) === 1
            || ($eventId !== null && (
                $eventId === '' || strlen($eventId) > 191 || preg_match('//u', $eventId) !== 1
                || preg_match('/[\x00-\x1F\x7F]/', $eventId) === 1
            ))
        ) {
            throw new \InvalidArgumentException('Audit verification finding is invalid.');
        }
    }
}
