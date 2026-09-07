# audit charter

Bounded immutable audit events, secret redaction, versioned event and anchor digests, evidence values and host storage ports.

The package owns portable semantics and contracts. The host owns authentication, authorization, database adapters, transaction coupling, retention, scheduling and external-effect recovery. No service locator, connection, command dispatcher or production canonical encoder is included.
