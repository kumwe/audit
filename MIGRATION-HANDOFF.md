---
schema: "kumwe-migration-handoff/v2"
artifact_kind: "framework_php"
migration_id: "KUMWE-MIG-2026-021"
change_set: "KUMWE-CS-2026-021"
state: "draft_pr_open"
source:
  app:
    repository: "https://github.com/kumwe/app"
    baseline_commit: "24ecf956423c18933e824b43cea1bfb9127a79a9"
    examined_paths:
      - "app/src/Audit/Application/AuditArchiveStorage.php"
      - "app/src/Audit/Application/AuditMetadataRedactor.php"
      - "app/src/Audit/Application/AuditRecorder.php"
      - "app/src/Audit/Application/AuditTrailExport.php"
      - "app/src/Audit/Application/AuditTrailExporter.php"
      - "app/src/Audit/Application/AuditTrailVerifier.php"
      - "app/src/Audit/Domain/AuditAnchorDigest.php"
      - "app/src/Audit/Domain/AuditEnforcementState.php"
      - "app/src/Audit/Domain/AuditEvent.php"
      - "app/src/Audit/Domain/AuditEventDigest.php"
      - "app/src/Audit/Domain/AuditVerificationFinding.php"
      - "app/src/Audit/Domain/AuditVerificationReport.php"
      - "app/src/Audit/Domain/StoredAuditArchive.php"
    old_namespace_roots:
      - "Kumwe\\App\\Audit\\"
    capability_index_sha256: null
  semantic_inputs:
    -
      owner: "kumwe/canonical-json"
      version_or_commit: "e7006a2580a49a1c8ab507b0d7b9c3403b4f9f58"
      manifest_or_corpus: "resources/corpus/v1.json"
      sha256: "84d21b12e7a2bfd752356d9a6e664bcb332e209d19017e7634e7485a4fa4e250"
  examined_dependencies:
    - "kumwe/canonical-json"
    - "kumwe/access-context 0.1.1"
  active_related_pull_requests: []
target:
  repository: "https://github.com/kumwe/audit"
  artifact_identity: "kumwe/audit"
  canonical_namespace_or_abi: "Kumwe\\Audit"
  branch: codex/integration-readiness-20260908
  pull_request: "https://github.com/kumwe/audit/pull/5"
ownership:
  responsibility: "Bounded immutable audit events, secret redaction, versioned event and anchor digests, evidence values and host storage ports."
  non_responsibilities:
    - "database adapters"
    - "authorization"
    - "transaction coupling"
    - "retention"
    - "external effects"
  allowed_dependency_ceiling:
    - "kumwe/canonical-json"
    - "kumwe/access-context"
  implementation_owner: "kumwe/audit"
  next_consumer: "kumwe/app"
  public_manifests:
    -
      path: "resources/capabilities/v1.json"
      sha256: "40d0b9d532edc419c22513022a9ce97eb22ed5544f1e7ba927136049bd651a1e"
    -
      path: "resources/service-map/v1.json"
      sha256: "01464f2aa14a01add0e74b24a2b516257c8d614af9ed4a6498a5875a731d7225"
    -
      path: "resources/public-api/v1.json"
      sha256: "2c6dc77bf3f6ae1d1d23a5a6cc137639a12cdab895dfeeb26804cdbeead985b0"
  intentionally_excluded:
    - "App infrastructure, middleware, operational scheduling and consumer integration tests"
framework_php:
  composer_package: "kumwe/audit"
  canonical_namespace: "Kumwe\\Audit"
  public_api_manifest: "resources/public-api/v1.json"
  capability_manifest: "resources/capabilities/v1.json"
  service_map: "resources/service-map/v1.json"
  extracted_symbols:
    -
      old_fqcn: "Kumwe\\App\\Audit\\Application\\AuditArchiveStorage"
      new_fqcn: "Kumwe\\Audit\\Application\\AuditArchiveStorage"
      source_path: "app/src/Audit/Application/AuditArchiveStorage.php"
      target_path: "src/Application/AuditArchiveStorage.php"
      kind: "interface"
      public_methods:
        - "store"
      public_properties: []
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Application\\AuditMetadataRedactor"
      new_fqcn: "Kumwe\\Audit\\Application\\AuditMetadataRedactor"
      source_path: "app/src/Audit/Application/AuditMetadataRedactor.php"
      target_path: "src/Application/AuditMetadataRedactor.php"
      kind: "class"
      public_methods:
        - "redact"
      public_properties: []
      public_constants:
        - "PLACEHOLDER"
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Application\\AuditRecorder"
      new_fqcn: "Kumwe\\Audit\\Application\\AuditRecorder"
      source_path: "app/src/Audit/Application/AuditRecorder.php"
      target_path: "src/Application/AuditRecorder.php"
      kind: "interface"
      public_methods:
        - "record"
      public_properties: []
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Application\\AuditTrailExport"
      new_fqcn: "Kumwe\\Audit\\Application\\AuditTrailExport"
      source_path: "app/src/Audit/Application/AuditTrailExport.php"
      target_path: "src/Application/AuditTrailExport.php"
      kind: "class"
      public_methods:
        - "__construct"
      public_properties:
        - "archive"
        - "fromPosition"
        - "toPosition"
        - "eventCount"
        - "redactedCount"
        - "anchorSequence"
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Application\\AuditTrailExporter"
      new_fqcn: "Kumwe\\Audit\\Application\\AuditTrailExporter"
      source_path: "app/src/Audit/Application/AuditTrailExporter.php"
      target_path: "src/Application/AuditTrailExporter.php"
      kind: "interface"
      public_methods:
        - "export"
      public_properties: []
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Application\\AuditTrailVerifier"
      new_fqcn: "Kumwe\\Audit\\Application\\AuditTrailVerifier"
      source_path: "app/src/Audit/Application/AuditTrailVerifier.php"
      target_path: "src/Application/AuditTrailVerifier.php"
      kind: "interface"
      public_methods:
        - "verify"
      public_properties: []
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Domain\\AuditAnchorDigest"
      new_fqcn: "Kumwe\\Audit\\Domain\\AuditAnchorDigest"
      source_path: "app/src/Audit/Domain/AuditAnchorDigest.php"
      target_path: "src/Domain/AuditAnchorDigest.php"
      kind: "class"
      public_methods:
        - "rolling"
        - "compute"
      public_properties: []
      public_constants:
        - "CHAIN_CONTEXT"
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Domain\\AuditEnforcementState"
      new_fqcn: "Kumwe\\Audit\\Domain\\AuditEnforcementState"
      source_path: "app/src/Audit/Domain/AuditEnforcementState.php"
      target_path: "src/Domain/AuditEnforcementState.php"
      kind: "enum"
      public_methods:
        - "installed"
        - "summary"
      public_properties: []
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Domain\\AuditEvent"
      new_fqcn: "Kumwe\\Audit\\Domain\\AuditEvent"
      source_path: "app/src/Audit/Domain/AuditEvent.php"
      target_path: "src/Domain/AuditEvent.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "id"
        - "occurredAt"
        - "actorId"
        - "action"
        - "subjectType"
        - "subjectId"
        - "outcome"
        - "metadata"
        - "metadataAsJson"
      public_properties: []
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Domain\\AuditEventDigest"
      new_fqcn: "Kumwe\\Audit\\Domain\\AuditEventDigest"
      source_path: "app/src/Audit/Domain/AuditEventDigest.php"
      target_path: "src/Domain/AuditEventDigest.php"
      kind: "class"
      public_methods:
        - "compute"
      public_properties: []
      public_constants:
        - "CHAIN_CONTEXT"
        - "INSTANT_FORMAT"
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Domain\\AuditVerificationFinding"
      new_fqcn: "Kumwe\\Audit\\Domain\\AuditVerificationFinding"
      source_path: "app/src/Audit/Domain/AuditVerificationFinding.php"
      target_path: "src/Domain/AuditVerificationFinding.php"
      kind: "class"
      public_methods:
        - "__construct"
      public_properties:
        - "code"
        - "position"
        - "detail"
        - "eventId"
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Domain\\AuditVerificationReport"
      new_fqcn: "Kumwe\\Audit\\Domain\\AuditVerificationReport"
      source_path: "app/src/Audit/Domain/AuditVerificationReport.php"
      target_path: "src/Domain/AuditVerificationReport.php"
      kind: "class"
      public_methods:
        - "__construct"
        - "intact"
        - "guarded"
      public_properties:
        - "eventsVerified"
        - "anchorsVerified"
        - "headPosition"
        - "enforcement"
        - "firstDivergence"
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
    -
      old_fqcn: "Kumwe\\App\\Audit\\Domain\\StoredAuditArchive"
      new_fqcn: "Kumwe\\Audit\\Domain\\StoredAuditArchive"
      source_path: "app/src/Audit/Domain/StoredAuditArchive.php"
      target_path: "src/Domain/StoredAuditArchive.php"
      kind: "class"
      public_methods:
        - "__construct"
      public_properties:
        - "key"
        - "size"
        - "checksum"
      public_constants: []
      exceptions:
        - "InvalidArgumentException"
      serialization_contract: "Documented PHP scalar/array projections; no native PHP serialized object is a durable wire contract."
      compatibility: "Explicit required CanonicalEncoder on digest operations; detached snapshot semantics."
  consumers:
    app_code:
      - "Source namespace imports and host adapters named in the integration guide"
    configuration_and_di:
      - "Host port bindings and existing canonical encoder service"
    reflection_and_string_references:
      - "Recompute namespace closure before adoption"
    fixtures_and_examples:
      - "examples/typed-consumer.php"
    external: []
  dependency_injection:
    mode: "direct"
    provider: null
    factories: []
    aliases: []
    service_lifetimes: []
    configuration_keys: []
    provider_absence_reason: "Pure immutable values, port contracts and directly callable static helpers; no injected runtime service is exported."
native_cpp: null
php_extension: null
tests:
  moved_or_added:
    - "tests/AuditBoundaryTest.php"
    - "tests/AuditEventTest.php"
    - "tests/AuditMetadataRedactorTest.php"
  remain_in_app_or_consumer:
    - "Host transaction atomicity, adapter parity, authorization, concurrency, recovery and database matrix"
  split_tests: []
  prohibited_duplicates:
    - "Remove implementation-unit counterparts only during separately verified App adoption"
  corpora:
    - "Literal canonical material and hostile metadata/replay vectors in package tests"
    - "resources/audit-corpus/rolling-v1.json (SHA-256 9120c884ea88d42f134452dcaa9c3369fb88afd3e1fc85dfc281e4f1321d77c8)"
documentation:
  charter: "CHARTER.md"
  readme: "README.md"
  public_api: "docs/public-api.md"
  architecture: "docs/architecture.md"
  integration_or_consumer: "docs/integration.md"
  examples:
    - "examples/typed-consumer.php"
  changelog_record: "CHANGELOG.md ## 0.1.2"
release_expectations:
  version_policy: "Pre-1.0 exact immutable pin only after reviewed release; no release claimed."
  expected_artifact_types:
    - "Composer ZIP"
  required_checks:
    - "composer check"
    - "bash tools/check-release-dependencies.sh"
    - "external release attestation"
  required_registry_or_installer: "Composer"
  required_external_attestation: true
next_task:
  phase_name: "Immutable dependency admission and human package review, then separate release verification"
  permitted_only_when:
    - "All package gates pass"
    - "CanonicalEncoder successor has exact immutable version and external attestation"
    - "Human review and merge"
  consumer_repository: "kumwe/app"
  dependency_or_native_change: "Replace dev canonical dependency with independently verified exact release before publication; later adopt kumwe/audit"
  namespace_or_api_replacements:
    - "Kumwe\\App\\Audit\\Application\\AuditArchiveStorage => Kumwe\\Audit\\Application\\AuditArchiveStorage"
    - "Kumwe\\App\\Audit\\Application\\AuditMetadataRedactor => Kumwe\\Audit\\Application\\AuditMetadataRedactor"
    - "Kumwe\\App\\Audit\\Application\\AuditRecorder => Kumwe\\Audit\\Application\\AuditRecorder"
    - "Kumwe\\App\\Audit\\Application\\AuditTrailExport => Kumwe\\Audit\\Application\\AuditTrailExport"
    - "Kumwe\\App\\Audit\\Application\\AuditTrailExporter => Kumwe\\Audit\\Application\\AuditTrailExporter"
    - "Kumwe\\App\\Audit\\Application\\AuditTrailVerifier => Kumwe\\Audit\\Application\\AuditTrailVerifier"
    - "Kumwe\\App\\Audit\\Domain\\AuditAnchorDigest => Kumwe\\Audit\\Domain\\AuditAnchorDigest"
    - "Kumwe\\App\\Audit\\Domain\\AuditEnforcementState => Kumwe\\Audit\\Domain\\AuditEnforcementState"
    - "Kumwe\\App\\Audit\\Domain\\AuditEvent => Kumwe\\Audit\\Domain\\AuditEvent"
    - "Kumwe\\App\\Audit\\Domain\\AuditEventDigest => Kumwe\\Audit\\Domain\\AuditEventDigest"
    - "Kumwe\\App\\Audit\\Domain\\AuditVerificationFinding => Kumwe\\Audit\\Domain\\AuditVerificationFinding"
    - "Kumwe\\App\\Audit\\Domain\\AuditVerificationReport => Kumwe\\Audit\\Domain\\AuditVerificationReport"
    - "Kumwe\\App\\Audit\\Domain\\StoredAuditArchive => Kumwe\\Audit\\Domain\\StoredAuditArchive"
  files_to_update:
    - "composer.json"
    - "composer.lock"
    - "App explicit adapter/encoder service bindings"
  files_to_remove:
    - "app/src/Audit/Application/AuditArchiveStorage.php"
    - "app/src/Audit/Application/AuditMetadataRedactor.php"
    - "app/src/Audit/Application/AuditRecorder.php"
    - "app/src/Audit/Application/AuditTrailExport.php"
    - "app/src/Audit/Application/AuditTrailExporter.php"
    - "app/src/Audit/Application/AuditTrailVerifier.php"
    - "app/src/Audit/Domain/AuditAnchorDigest.php"
    - "app/src/Audit/Domain/AuditEnforcementState.php"
    - "app/src/Audit/Domain/AuditEvent.php"
    - "app/src/Audit/Domain/AuditEventDigest.php"
    - "app/src/Audit/Domain/AuditVerificationFinding.php"
    - "app/src/Audit/Domain/AuditVerificationReport.php"
    - "app/src/Audit/Domain/StoredAuditArchive.php"
  tests_to_remove:
    - "Implementation-unit tests corresponding to package tests after verified adoption"
  tests_to_retain_or_add:
    - "All host database, authorization, transaction, concurrency and recovery tests"
  di_or_provisioning_changes:
    - "Bind ports to host adapters; inject CanonicalEncoder explicitly"
  capability_index_changes:
    - "Record verified package ownership only during consumer adoption"
  changelog_and_evidence_changes:
    - "Record exact verified source and artifacts in external attestation"
  verification_commands:
    - "composer check"
    - "bash tools/check-release-dependencies.sh"
concurrency:
  likely_conflict_files:
    - "App composer.json and composer.lock"
    - "Host DI bindings"
  related_migrations:
    - "KUMWE-MIG-2026-007"
    - "KUMWE-MIG-2026-004"
  ownership_conflicts: []
  integration_train: null
  resolution_rule: "semantic-preservation"
governance:
  roadmap_source_sha256: "a202155ef1a65f5ab293d4f8397ebf4ac430db7f1e877c776bbe7851e6fe18d8"
  roadmap_refs: []
  non_roadmap_refs: []
  completion_claim: false
decisions:
  - "CanonicalEncoder is required and never defaulted."
  - "No App adoption, merge, tag or release in this task."
  - "Candidate dependency ZIP isolation is not release provenance."
blockers:
  - "Independent verification of this successor and exact dependencies remains required before App adoption."
---

## Migration/implementation summary

14 types; bounded detached audit metadata, digest profiles, redaction, evidence values and recording/archive/export/verification ports. Archive, export, finding and report validation plus language-neutral rolling digest vectors are library-owned. Doctrine ledger walks remain host adapters.

## Public API and responsibility

The symbol map above and [public API](docs/public-api.md) define every exported contract. [Architecture](docs/architecture.md) and [integration](docs/integration.md) retain the host boundaries.

## Capability reuse/semantic input review

This successor selects Context 0.1.1 and retains Canonical JSON 0.1.1. Published Audit 0.1.0 still pins Context 0.1.0. [Current release/dependency observations](docs/readiness-review.md) supersede obsolete initial-extraction publication blockers. No independent attestation is fabricated.

## Consumer inventory

The source/consumer mappings above remain the adoption inventory. Compare every mapped file and public signature against the recorded full App baseline and current App before consumer changes. Any newer portable behavior goes upstream first. Preserve App authority, adapters and workflows.

## Test ownership

Package tests own portable behavior, boundary/conformance, API and construction. App retains actual authorization, transaction atomicity, persistence, concurrency, recovery and delivery tests. Remove only duplicate portable implementation tests during the separate verified adoption.

## Next-task execution notes

The selected runtime dependencies are kumwe/canonical-json 0.1.1, kumwe/access-context 0.1.2.
Access Context 0.1.2 was observed at source 132c3cd7c229ceda4398e19140d1477512c27ebf.
Run the package dependency-readiness gate before selecting the coordinated consumer graph.
Independent release attestations remain external and are not inferred from these version pins.

Review [PR #5](https://github.com/kumwe/audit/pull/5), require its complete package gate, then let the maintainer merge. Independently verify the published successor and exact dependency graph before App adoption. Existing published releases stay intact. This task does not implement the App runtime cutover.

## Drift check

Reconcile mapped source and tests against the recorded App baseline and current App before any adoption.
Newer portable behavior must move upstream first; preserve App authority, persistence and integration tests.

## Validation recipe and observed local results

Run `composer check` and the repository release automation regressions. Runtime suites, strict static analysis, coding standards, manifest/API checks and the no-dev authoritative archive consumer remain required. Final tested source and archive identities belong in external CI/attestation evidence.
