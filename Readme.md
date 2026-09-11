# Mifiel PHP API Client

[![Latest Stable Version][packagist-image]][packagist-url]
[![Build Status][travis-image]][travis-url]

PHP SDK for the [Mifiel](https://www.mifiel.com) API.

## Documentation

API reference, guides, and examples:

- English: https://docs.mifiel.com/en/
- Español: https://docs.mifiel.com/es/

This README covers installation and client setup only.

## Installation

```bash
composer require mifiel/api-client
```

Or add it to your `composer.json`:

```json
{
  "require": {
    "mifiel/api-client": "^3.0"
  }
}
```

## Setup

1. Create an account (production or [sandbox](https://app-sandbox.mifiel.com)).
2. Generate an `APP_ID` and `APP_SECRET` in [Access Tokens](https://app-sandbox.mifiel.com/settings/access-tokens).
3. Configure the client:

```php
use Mifiel\ApiClient as Mifiel;

Mifiel::setTokens('APP_ID', 'APP_SECRET');
// Production is the default (https://app.mifiel.com/api/v1/).
// For sandbox:
Mifiel::url('https://app-sandbox.mifiel.com/api/v1/');
```

## Development & Tests

```bash
# Unit
php vendor/bin/phpunit --exclude-group internet
# e2e
php vendor/bin/phpunit --group internet
```

## Contributing

1. Fork it (https://github.com/Mifiel/php-api-client/fork)
2. Create your feature branch (`git checkout -b feature/my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin feature/my-new-feature`)
5. Create a new Pull Request

[travis-image]: https://travis-ci.org/Mifiel/php-api-client.svg?branch=master
[travis-url]: https://travis-ci.org/Mifiel/php-api-client
[packagist-image]: https://img.shields.io/packagist/v/mifiel/api-client.svg
[packagist-url]: https://packagist.org/packages/mifiel/api-client
