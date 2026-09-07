# Public API

Evidence constructors reject malformed state with InvalidArgumentException. Archive keys are nonempty valid UTF-8, control-free and at most 1024 bytes; size is nonnegative and checksum is lowercase SHA-256. Export ranges are positive and ordered, with 1..range-size events, nonnegative redactions and a positive optional anchor sequence. Findings use bounded lowercase machine tokens, nonnegative positions and nonempty UTF-8 details of at most 4096 bytes. Report counts are nonnegative and verified events cannot exceed the observed head. The rolling evidence corpus is resources/audit-corpus/rolling-v1.json.

The source contracts below define parameters, return values, exceptions and invariants. [The machine manifest](../resources/public-api/v1.json) freezes signatures and is checked by `composer api`. All values are immutable; static helpers have no retained state and perform no I/O. Host port implementations own their documented side effects and concurrency guarantees. The injected canonical encoder must implement GenericV1; no service is resolved globally.

## Kumwe\Audit\Application\AuditArchiveStorage

Source: [src/Application/AuditArchiveStorage.php](../src/Application/AuditArchiveStorage.php).

### `store`

```php
public function store(string $archiveId, iterable $chunks): StoredAuditArchive
```

```text
Write, checksum and atomically publish one private audit archive.

@param   string            $archiveId  Canonical UUID naming this archive.
@param   iterable<string>  $chunks     Ordered NDJSON chunks to persist.

@return  StoredAuditArchive  Key, size and checksum evidence for the stored bytes.

@throws  \RuntimeException  When a chunk is invalid or the filesystem refuses a safe write.

@since   0.1.0
```

## Kumwe\Audit\Application\AuditMetadataRedactor

Source: [src/Application/AuditMetadataRedactor.php](../src/Application/AuditMetadataRedactor.php).

### `redact`

```php
public static function redact(array $metadata, int &$redacted): array
```

```text
Redact credential-shaped entries anywhere in one metadata document.

@param   array<string, mixed>  $metadata  Decoded metadata object as the row stores it.
@param   int                   $redacted  Running count of replaced values, raised in place.

@return  array<string, mixed>  The document with every matched value replaced by the placeholder.

@since   0.1.0
```

## Kumwe\Audit\Application\AuditRecorder

Source: [src/Application/AuditRecorder.php](../src/Application/AuditRecorder.php).

### `record`

```php
public function record(AuditEvent $event): void
```

```text
Store one audit record durably.

@param   AuditEvent  $event  Validated record of who did what to which subject, and how it ended.

@return  void

@since   0.1.0
```

## Kumwe\Audit\Application\AuditTrailExport

Source: [src/Application/AuditTrailExport.php](../src/Application/AuditTrailExport.php).

### `__construct`

```php
public function __construct(
        public StoredAuditArchive $archive,
        public int $fromPosition,
        public int $toPosition,
        public int $eventCount,
        public int $redactedCount,
        public ?int $anchorSequence,
    )
```

```text
Capture the export manifest.

@param  StoredAuditArchive  $archive         Stored file evidence: key, byte size and checksum.
@param  int                 $fromPosition    First audit position the export covers, inclusive.
@param  int                 $toPosition      Last audit position the export covers, inclusive.
@param  int                 $eventCount      Number of events written into the archive.
@param  int                 $redactedCount   Number of metadata values redacted on the way out.
@param  ?int                $anchorSequence  Newest anchor sequence at export time, or null when
        the ledger holds no anchor yet.

@since  0.1.0
```

## Kumwe\Audit\Application\AuditTrailExporter

Source: [src/Application/AuditTrailExporter.php](../src/Application/AuditTrailExporter.php).

### `export`

```php
public function export(
        ExecutionContext $context,
        ?int $fromPosition = null,
        ?int $toPosition = null,
    ): AuditTrailExport
```

```text
Export one position range of the trail into a checksummed private NDJSON archive.

@param   ExecutionContext  $context       Actor the export is authorized and audited under.
@param   ?int              $fromPosition  First position to include, or null for the trail's start.
@param   ?int              $toPosition    Last position to include, or null for the current head.

@return  AuditTrailExport  Manifest naming the archive, its range, counts and anchor reference.

@throws  \InvalidArgumentException  When the requested range is inverted or not positive.
@throws  \RuntimeException  When the range holds no events or the archive cannot be written.
@throws  \RuntimeException  When the actor may not export
         the audit trail.

@since   0.1.0
```

## Kumwe\Audit\Application\AuditTrailVerifier

Source: [src/Application/AuditTrailVerifier.php](../src/Application/AuditTrailVerifier.php).

### `verify`

```php
public function verify(ExecutionContext $context, int $batchSize = 1000): AuditVerificationReport
```

```text
Walk the whole trail and its anchor ledger, stopping at the first divergence.

@param   ExecutionContext  $context    Actor the verification is authorized and audited under.
@param   int               $batchSize  Rows fetched per batch during the walk, from 1 to 10000.

@return  AuditVerificationReport  Counts of what was re-checked, and the first divergence if any.

@throws  \InvalidArgumentException  When the batch size is outside its bounds.
@throws  \RuntimeException  When the actor may not verify
         the audit trail.

@since   0.1.0
```

## Kumwe\Audit\Domain\AuditAnchorDigest

Source: [src/Domain/AuditAnchorDigest.php](../src/Domain/AuditAnchorDigest.php).

### `rolling`

```php
public static function rolling(iterable $digestsByPosition): string
```

```text
Fold one ordered sequence of `position => event digest` pairs into the range's rolling digest.

@param   iterable<int, string>  $digestsByPosition  Event digests keyed by position, in ascending
         position order exactly as the range stores them.

@return  string  Lowercase hexadecimal SHA-256 binding both the digests and their order.

@since   0.1.0
```

### `compute`

```php
public static function compute(
        int $sequence,
        string $kind,
        int $fromPosition,
        int $toPosition,
        int $rowCount,
        string $rollingDigest,
        ?string $previousDigest,
        ?string $archiveSha256,
        string $createdAt,
        CanonicalEncoder $encoder,
    ): string
```

```text
Compute the self-digest one anchor row stores, chaining it to its predecessor.

@param   int      $sequence        Gapless ledger sequence number of this anchor row.
@param   string   $kind            Anchor kind: `anchor` for a seal, `prune` for a retention mark.
@param   int      $fromPosition    First audit position the range covers, inclusive.
@param   int      $toPosition      Last audit position the range covers, inclusive.
@param   int      $rowCount        Number of audit rows the range held when it was sealed.
@param   string   $rollingDigest   Rolling digest of the covered range.
@param   ?string  $previousDigest  Digest of the preceding anchor row, or null for the first.
@param   ?string  $archiveSha256   Checksum of the archive a prune mark preserved, or null.
@param   string   $createdAt       Creation instant formatted as `Y-m-d H:i:s`.

@return  string  Lowercase hexadecimal SHA-256 of the canonical anchor document.

@throws  InvalidArgumentException  When a field cannot be represented as canonical JSON.

@since   0.1.0
```

## Kumwe\Audit\Domain\AuditEnforcementState

Source: [src/Domain/AuditEnforcementState.php](../src/Domain/AuditEnforcementState.php).

### `installed`

```php
public function installed(): bool
```

```text
Report whether database-level prevention is in force.

@return  bool  True only when the guards are installed on this server.

@since   0.1.0
```

### `summary`

```php
public function summary(): string
```

```text
Describe the state in the words an operator reading a verification report needs.

@return  string  One sentence naming what is and is not being enforced.

@since   0.1.0
```

## Kumwe\Audit\Domain\AuditEvent

Source: [src/Domain/AuditEvent.php](../src/Domain/AuditEvent.php).

### `__construct`

```php
public function __construct(
        private string $id,
        private DateTimeImmutable $occurredAt,
        private ?string $actorId,
        private string $action,
        private string $subjectType,
        private ?string $subjectId,
        private string $outcome,
        array $metadata = [],
    )
```

```text
Build a validated audit record.

Validation happens here so that every later reader — recorder, exporter, administration screen —
can trust the fields without repeating the checks. Identifier fields are matched against
deliberately narrow patterns: an action, subject type or outcome is a lowercase machine token
such as `content.transition` or `success`, not a sentence an operator wrote.

@param   string             $id           Canonical UUID identifying this record, unique per action.
@param   DateTimeImmutable  $occurredAt   Instant the action happened, not the instant it is written.
@param   ?string            $actorId      Opaque id of the accountable actor, or null for a system action.
@param   string             $action       Machine token naming what was done, at most 127 bytes.
@param   string             $subjectType  Machine token naming the kind of thing acted on, up to 63 bytes.
@param   ?string            $subjectId    Opaque id of the thing acted on, or null when there is none.
@param   string             $outcome      Machine token for how the action ended, at most 31 bytes.
@param   array<mixed>       $metadata     Values must be JSON-serializable. String keys, safe context only.

@throws  InvalidArgumentException  When any field is malformed or the metadata is not a JSON object.

@since   0.1.0
```

### `id`

```php
public function id(): string
```

```text
Returns the record's own identifier.

@return  string  Canonical UUID chosen when the event was built; the primary key of the stored row.

@since   0.1.0
```

### `occurredAt`

```php
public function occurredAt(): DateTimeImmutable
```

```text
Returns the moment the audited action took place.

@return  DateTimeImmutable  Time taken from the use case's clock, not the time of the write.

@since   0.1.0
```

### `actorId`

```php
public function actorId(): ?string
```

```text
Returns the actor held accountable for the action.

@return  ?string  Opaque actor id, or null when the platform acted with no user behind it.

@since   0.1.0
```

### `action`

```php
public function action(): string
```

```text
Returns the token naming what was done.

@return  string  Lowercase machine token such as `content.transition`, safe to filter the trail on.

@since   0.1.0
```

### `subjectType`

```php
public function subjectType(): string
```

```text
Returns the kind of thing the action was performed on.

@return  string  Machine token such as `content`, which gives the subject id its namespace.

@since   0.1.0
```

### `subjectId`

```php
public function subjectId(): ?string
```

```text
Returns the identifier of the thing acted on.

@return  ?string  Opaque subject id, or null for an action with no single subject.

@since   0.1.0
```

### `outcome`

```php
public function outcome(): string
```

```text
Returns how the action ended.

@return  string  Machine token such as `success` or `allowed`, at most 31 bytes.

@since   0.1.0
```

### `metadata`

```php
public function metadata(): array
```

```text
Returns the context captured alongside the action.

@return  array<string, mixed>  Caller-supplied detail keyed by string; empty when the caller gave none.

@since   0.1.0
```

### `metadataAsJson`

```php
public function metadataAsJson(): string
```

```text
Renders the metadata as a JSON document, for a recorder whose column takes an encoded string.

Object notation is forced, so empty metadata serializes to `{}` rather than `[]` and every stored
row keeps one shape. The constructor already proved this payload encodes, so the call cannot fail.

@return  string  JSON object literal of the metadata.

@since   0.1.0
```

## Kumwe\Audit\Domain\AuditEventDigest

Source: [src/Domain/AuditEventDigest.php](../src/Domain/AuditEventDigest.php).

### `compute`

```php
public static function compute(
        string $id,
        string $occurredAt,
        ?string $actorId,
        string $action,
        string $subjectType,
        ?string $subjectId,
        string $outcome,
        array $metadata,
        CanonicalEncoder $encoder,
    ): string
```

```text
Compute the canonical digest for one audit event's stored fields.

@param   string                $id           Canonical UUID of the event row.
@param   string                $occurredAt   Occurrence instant formatted as `Y-m-d H:i:s`.
@param   ?string               $actorId      Opaque accountable actor id, or null for a system action.
@param   string                $action       Machine token naming what was done.
@param   string                $subjectType  Machine token naming the kind of thing acted on.
@param   ?string               $subjectId    Opaque id of the thing acted on, or null when none exists.
@param   string                $outcome      Machine token for how the action ended.
@param   array<string, mixed>  $metadata     Decoded metadata object captured with the event.

@return  string  Lowercase hexadecimal SHA-256, 64 characters wide.

@throws  InvalidArgumentException  When the metadata cannot be represented as canonical JSON.

@since   0.1.0
```

## Kumwe\Audit\Domain\AuditMetadata

Source: [src/Domain/AuditMetadata.php](../src/Domain/AuditMetadata.php).

### `snapshot`

```php
public static function snapshot(array $metadata): array
```

```text
Validate and detach JSON metadata. Empty metadata remains an empty PHP array.
@param array<string, mixed> $metadata Safe context, with string keys at the root.
@return array<string, mixed> Snapshot containing only arrays and JSON scalars.
@throws InvalidArgumentException On invalid UTF-8, objects, resources, recursion or a bound violation.
@since 0.1.0
```

## Kumwe\Audit\Domain\AuditVerificationFinding

Source: [src/Domain/AuditVerificationFinding.php](../src/Domain/AuditVerificationFinding.php).

### `__construct`

```php
public function __construct(
        public string $code,
        public int $position,
        public string $detail,
        public ?string $eventId = null,
    )
```

```text
Capture one divergence.

@param  string   $code      Machine token classifying the divergence, such as `event.digest.mismatch`.
@param  int      $position  Audit or anchor position the divergence anchors to; zero when none applies.
@param  string   $detail    Operator-facing explanation of what disagreed.
@param  ?string  $eventId   UUID of the divergent audit row when the divergence names one.

@since  0.1.0
```

## Kumwe\Audit\Domain\AuditVerificationReport

Source: [src/Domain/AuditVerificationReport.php](../src/Domain/AuditVerificationReport.php).

### `__construct`

```php
public function __construct(
        public int $eventsVerified,
        public int $anchorsVerified,
        public int $headPosition,
        public AuditEnforcementState $enforcement,
        public ?AuditVerificationFinding $firstDivergence = null,
    )
```

```text
Capture the outcome of one verification pass.

@param  int                        $eventsVerified   Audit rows whose digests and links were re-checked.
@param  int                        $anchorsVerified  Anchor rows whose chain and ranges were re-derived.
@param  int                        $headPosition     Highest audit position that existed during the walk.
@param  AuditEnforcementState      $enforcement      Append-only enforcement observed on this server.
@param  ?AuditVerificationFinding  $firstDivergence  First divergence found, or null for an intact trail.

@since  0.1.0
```

### `intact`

```php
public function intact(): bool
```

```text
Report whether the walk completed without finding a divergence.

This is a statement about the evidence only. A caller deciding whether the installation is in the
posture it is supposed to be in wants `guarded()`, which additionally requires that the database
is refusing rewrites rather than merely recording them.

@return  bool  True when every checked event and anchor agreed with its recomputation.

@since   0.1.0
```

### `guarded`

```php
public function guarded(): bool
```

```text
Report whether the trail both verifies and is being prevented from changing.

@return  bool  True only when the chain is intact and the append-only guards are installed.

@since   0.1.0
```

## Kumwe\Audit\Domain\StoredAuditArchive

Source: [src/Domain/StoredAuditArchive.php](../src/Domain/StoredAuditArchive.php).

### `__construct`

```php
public function __construct(
        public string $key,
        public int $size,
        public string $checksum,
    )
```

```text
Capture the stored-object evidence.

@param  string  $key       Opaque storage key of the archive inside the private audit directory.
@param  int     $size      Exact byte size of the stored archive.
@param  string  $checksum  Lowercase hexadecimal SHA-256 of the stored bytes.

@since  0.1.0
```

