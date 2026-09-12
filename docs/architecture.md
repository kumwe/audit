# Architecture

Kumwe\Audit owns the portable contracts and state rules catalogued in the [public API](public-api.md).
Dependencies are limited to the Composer require section and verified by the token boundary gate. No Core/SDK
namespaces, Doctrine, Symfony or Illuminate implementation may enter production source.

Fourteen public types cover audit events, bounded AuditMetadata snapshots, redaction, versioned digest material,
evidence values and recording/archive/export/verification ports. Doctrine verification and export implementations
remain in Core because they couple storage, privileges and retention.

[Integration](integration.md) defines scalar serialization, explicit collaborators and host concurrency requirements.
The [Core contract](core-contract.md) records authority, persistence, lifecycle and test ownership.
