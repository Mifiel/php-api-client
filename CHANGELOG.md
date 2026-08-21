# Changelog

## v3.0.0 - 2026-08-21

### Breaking changes

- Default API base URL after `setTokens()` changed from `https://www.mifiel.com/api/v1/` to `https://app.mifiel.com/api/v1/`.
- Sandbox documentation and examples now use `https://app-sandbox.mifiel.com/api/v1/` instead of `https://sandbox.mifiel.com/api/v1/`.

### Migration

```php
use Mifiel\ApiClient as Mifiel;

Mifiel::setTokens('APP_ID', 'APP_SECRET');
// Production requests now go to https://app.mifiel.com/api/v1/

// Sandbox
Mifiel::url('https://app-sandbox.mifiel.com/api/v1/');
```

If you previously overrode the URL with a legacy host, update those overrides or remove them to pick up the new defaults.
