# Changelog

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

- Drop support for PHP < 7
