# Kumwe Audit

[![Packagist version][version-badge]][package]
[![Audit CI][ci-badge]][ci]
[![PHP requirement][php-badge]][package]
[![License][license-badge]](LICENSE)

[version-badge]: https://img.shields.io/packagist/v/kumwe/audit
[package]: https://packagist.org/packages/kumwe/audit
[ci-badge]: https://github.com/kumwe/audit/actions/workflows/ci.yml/badge.svg?branch=main
[ci]: https://github.com/kumwe/audit/actions/workflows/ci.yml
[php-badge]: https://img.shields.io/packagist/dependency-v/kumwe/audit/php
[license-badge]: https://img.shields.io/packagist/l/kumwe/audit

Bounded immutable audit events, secret redaction, versioned event and anchor digests, evidence values and host
storage ports under `Kumwe\Audit`. Requires PHP 8.5, JSON, exact Canonical JSON 0.1.1 and Access Context 0.1.2.

## Installation and use

Install the published package with an exact pre-1.0 pin:

```sh
composer require kumwe/audit:0.1.2
```

```php
<?php

require 'vendor/autoload.php';

use Kumwe\Audit\Application\AuditMetadataRedactor;

$redactedCount = 0;
$safeMetadata = AuditMetadataRedactor::redact([
    'request_id' => 'request-1',
    'api_key' => 'example-secret',
], $redactedCount);
assert($safeMetadata['api_key'] === '[redacted]');
assert($redactedCount === 1);
```

Digest operations require an explicitly supplied `Kumwe\CanonicalJson\CanonicalEncoder` conforming to
`kumwe-canonical-json/generic-v1`. There is no encoder fallback. The
[typed consumer example](examples/typed-consumer.php) demonstrates recorder/storage adapters, redaction and evidence.

## Core integration

Core binds AuditRecorder, AuditArchiveStorage, AuditTrailExporter and AuditTrailVerifier to its own adapters.
Values and static helpers are constructed or called directly; no ConfigProvider is needed. Core owns authorization,
transaction coupling, database guards, privileged export, retention, key custody and operational verification.
The [Core contract](docs/core-contract.md) and [integration guide](docs/integration.md) define those boundaries.

[Public API](docs/public-api.md), [architecture](docs/architecture.md), [charter](CHARTER.md) and
[release record](docs/release-record.md) describe the package and its consumer compatibility contract.
Published versions and source CI status are linked above; Core validates its own integration against selected versions.

## Development and releases

```sh
composer install
composer check
composer examples
```

`composer clean-consumer` builds isolated ZIPs from this checkout and its installed declared Kumwe dependencies,
installs a fresh no-dev authoritative consumer, and executes the shipped example. That proves composition;
independent release verification binds the actual published source and archives.
The [package release standard](docs/package-release-standard.md) describes publication and verification requirements.
Licensed under [Apache-2.0](LICENSE).
