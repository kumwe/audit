# Core contract

Audit owns bounded audit events and metadata, redaction, versioned event/anchor digests, verification/export/archive
evidence values and host recording/storage ports. Core owns authentication, authorization, database enforcement,
transactions, ledger walks, archive access, retention, key custody, restore and incident response.

## Composition and dependencies

Core explicitly binds AuditRecorder, AuditArchiveStorage, AuditTrailExporter and AuditTrailVerifier to host adapters.
Records remain outside the container. Static digest/redaction helpers need no ConfigProvider, factory or ambient
context. Pass an explicitly selected generic-v1 CanonicalEncoder to each event and anchor digest operation.
Runtime dependencies are exact Canonical JSON 0.1.1 and Access Context 0.1.2; consumer lockfiles must preserve a
compatible exact tuple. No encoder fallback or alternate byte profile is selected automatically.

## Metadata and evidence boundary

AuditMetadata accepts a string-keyed root object containing JSON scalars and nested arrays. It limits nesting to
32 levels, total entries to 512 and JSON to 65,536 bytes. Invalid UTF-8, nonfinite numbers, recursive arrays,
resources and all objects are refused; user serialization callbacks never run. Snapshots detach nested references.
Core selects safe metadata before recording and applies redaction at archive boundaries. Redaction never mutates
the original event or stored digests. Host policy must classify business secrets and ciphertext appropriately.

Versioned event digests bind exact fields and stored timestamp strings. Generic-v1 canonical bytes and the deployed
corpus/version are evidence compatibility inputs. Ordered rolling position/digest material exposes mutation,
reordering, deletion and gaps, but Core allocates sequences and decides which anchors and findings to trust.
`AuditVerificationReport::intact()` describes evidence only; `guarded()` additionally requires observed Active
enforcement. The package neither installs nor observes database guards and does not authorize export or retention.

## Runtime and test ownership

Core couples writes and audit records in the actual transaction, implements fail-closed authority and storage
behavior, runs operational verification jobs, and validates backup/restore and retention. In-memory adapters in the
example demonstrate contracts and do not supply production persistence or a privileged verifier.

The package owns metadata/digest/redaction/evidence boundaries and rolling-digest conformance vectors. Core retains
transaction atomicity, adapter parity, authorization, concurrency, database matrix, restore and recovery tests.
The [release record](release-record.md) retains source and symbol mappings at an exact baseline. Reconcile that
baseline against current Core code before changing imports or deleting duplicate portable implementation tests.

A local clean archive consumer proves composition, not publication provenance or Core integration. Consumers use
independently verified exact releases and retain external evidence. Rollback restores the previously tested package,
encoder/corpus and host composition tuple while respecting persisted evidence compatibility.

[Public API](public-api.md), [integration](integration.md) and [release standard](package-release-standard.md) supply
the detailed signatures, projections and validation requirements.
