# Architecture

Kumwe\Audit owns the portable contracts and state rules catalogued in [the API](public-api.md). Dependencies are limited to the Composer require section and verified by the token boundary gate. No App/SDK namespaces, Doctrine, Symfony or Illuminate implementation may enter source.

Thirteen extracted types plus the new AuditMetadata snapshot helper form fourteen public types. The original brief estimated fifteen to sixteen types; Doctrine verification/export implementations stay in App because they couple storage, privileges and retention.

[Integration](integration.md) defines scalar serialization, explicit collaborators and host concurrency requirements.
