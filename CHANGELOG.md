# Changelog

## Unreleased

- Standardize linked version, CI, PHP and license badges and current installation/dependency guidance.
- Replace obsolete process documentation with a maintained Core contract and package release record.
- Update archive and governed-manifest verification for the release record without changing runtime behavior.

## 0.1.2

- Select Access Context 0.1.2 so every consumer receives its malformed UTF-8 identity refusal.
- Keep dependency-readiness coordinates synchronized with the exact production requirements.
  Reject stale, missing, duplicate or mismatched evidence entries in the package gate.
- Preserve package API ownership and require independent release verification before consumer adoption.

## 0.1.1

- Validate archive checksums/keys, export ranges/counts, verification findings and report counters. Add evidence boundary tests and a versioned language-neutral rolling-digest corpus; advance Access Context to published 0.1.1.
- Refresh extraction handoff and library-owned validation evidence; App adoption remains a separate task.

## 0.1.0

- Extract bounded immutable audit events, secret redaction, versioned event and anchor digests, evidence values and host storage ports.
- Require the explicit generic-v1 CanonicalEncoder port; no encoder fallback.
- Add package behavior and hostile-input tests, API drift checks and archive consumer verification.

