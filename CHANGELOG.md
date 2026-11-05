# Changelog

## v4.2.0 - Unreleased

### Features

- Add `Document::getState()` for signing groups and stakeholders.
- Add `Document::addStakeholder()`, `updateStakeholder()` and `deleteStakeholder()`.
- Add `Document::saveZIP()` for signed PDF/XML archives.
- Add `Query::execute()` for advanced searches, preserving records and metadata.
- Add PHP examples and a PHP 8.3/8.4/8.5 × Guzzle 7/8 unit-test workflow.

### Fixes

- Preserve explicit false, zero, empty strings and empty arrays in multipart requests;
  only null fields are omitted. This affects options such as `send_invites=false`
  and `remind_every=0`.
- Send GET parameters in the query string rather than a JSON body.
- Return the PSR-7 response from both template-generation methods, rather than null.
- Synchronize the User-Agent version with the package version.

### Compatibility

- PHP >=8.3 and Guzzle ^7.0 or ^8.0 remain supported.
- New state, stakeholder and query methods return decoded objects. Stakeholder
  deletion returns a PSR-7 response; ZIP download writes to the supplied path.
- Existing template request paths and required arguments are unchanged.

## v4.1.0 - 2026-10-11

- Add account-level webhook subscriptions and queued/instant triggering.
- Allow Guzzle 8 alongside Guzzle 7.

## v4.0.0 - 2026-08-25

### Breaking changes

- Default API base URL after `setTokens()` changed from `https://www.mifiel.com/api/v1/` to `https://app.mifiel.com/api/v1/`.
- Sandbox documentation and examples now use `https://app-sandbox.mifiel.com/api/v1/` instead of `https://sandbox.mifiel.com/api/v1/`.

### Features

- Send a standardized `User-Agent` on API requests, e.g. `PHP/8.3.0 mifiel/api-client/4.0.0 guzzle/7.9.2 (Linux/6.8.0)`.

### Migration

```php
use Mifiel\ApiClient as Mifiel;

Mifiel::setTokens('APP_ID', 'APP_SECRET');
// Production requests now go to https://app.mifiel.com/api/v1/

// Sandbox
Mifiel::url('https://app-sandbox.mifiel.com/api/v1/');
```

If you previously overrode the URL with a legacy host, update those overrides or remove them to pick up the new defaults.

## v3.0.0 - 2026-04-17

### Breaking changes

- Require PHP >=8.3 (including the replacement HMAC middleware).
