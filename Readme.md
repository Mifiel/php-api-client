# Mifiel PHP Api Client

## Installation

Add `mifiel/api-client` to your [composer.json](https://getcomposer.org/).

```json
  "require": {
    "mifiel/api-client": "^1.0"
  }
```

And then execute `composer install`.

Or [download](http://example.com) the source of this repo and include it in your project.

## Usage

To start using the API you will need an `APP_ID` and a `APP_SECRET` which will be provided upon request (contact us at hola@mifiel.com).

You will first need to create an account in mifiel.com since the `APP_ID` and `APP_SECRET` will be linked to your account.

### Config

```php
  Mifiel::setTokens('app-id', 'app-secret');
```
