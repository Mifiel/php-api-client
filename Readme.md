# Mifiel PHP API Client

[![Latest Stable Version][packagist-image]][packagist-url]
[![Build Status][travis-image]][travis-url]
[![Coverage Status][coveralls-image]][coveralls-url]

PHP SDK for [Mifiel](https://www.mifiel.com) API.
Please read our [documentation](http://docs.mifiel.com/) for instructions on how to start using the API.

## Installation

Just run `composer require mifiel/api-client` in the root your project or add `mifiel/api-client` to your [composer.json](https://getcomposer.org/).

```json
  "require": {
    "mifiel/api-client": "^1.0"
  }
```

And then execute `composer install`.

## Usage

For your convenience Mifiel offers a Sandbox environment where you can confidently test your code.

To start using the API in the Sandbox environment you need to first create an account at [sandbox.mifiel.com](https://sandbox.mifiel.com).

Once you have an account you will need an APP_ID and an APP_SECRET which you can generate in [sandbox.mifiel.com/access_tokens](https://sandbox.mifiel.com/access_tokens).

Then you can configure the library with:

```php
  use Mifiel\ApiClient as Mifiel;
  Mifiel::setTokens('APP_ID', 'APP_SECRET');
  // if you want to use our sandbox environment use:
  Mifiel::url('https://sandbox.mifiel.com/api/v1/');
```

Document methods:

- Find:

  ```php
    use Mifiel\Document;
    $document = Document::find('id');
    $document->original_hash;
    $document->file;
    $document->file_signed;
    # ...
  ```

- Find all:

  ```php
    use Mifiel\Document;
    $documents = Document::all();
  ```

- Create:

> Use only **original_hash** if you dont want us to have the file.<br>
> Only **file** or **original_hash** must be provided.

  ```php
    use Mifiel\Document;
    $document = new Document([
      'file_path' => 'path/to/my-file.pdf',
      'signatories' => [
        [ 
          'name' => 'Signer 1', 
          'email' => 'signer1@email.com', 
          'tax_id' =>  'AAA010101AAA' 
        ],
        [ 
          'name' => 'Signer 2', 
          'email' => 'signer2@email.com', 
          'tax_id' =>  'AAA010102AAA'
        ]
      ]
    ]);
    // if you dont want us to have the PDF, you can just send us 
    // the original_hash and the name of the document. Both are required
    $document = new Document([
      'original_hash' => hash('sha256', file_get_contents('path/to/my-file.pdf')),
      'name' => 'my-file.pdf',
      'signatories' => ...
    ]);

    $document->save();
  ```

- Save Document related files

```php
  use Mifiel\Document;
  $document = Document::find('id');

  # save the original file
  $document->saveFile('path/to/save/file.pdf');
  # save the signed file (original file + signatures page)
  $document->saveFileSigned('path/to/save/file-signed.pdf');
  # save the signed xml file
  $document->saveXML('path/to/save/xml.xml');
```

- Delete

  ```php
    use Mifiel\Document;
    Document::delete('id');
  ```

Certificate methods:

- Sat Certificates

  ```php
    use Mifiel\Certificate;
    $sat_certificates = Certificate::sat();
  ```

- Find:

  ```php
    use Mifiel\Certificate;
    $certificate = Certificate::find('id');
    $certificate->cer_hex;
    $certificate->type_of;
    # ...
  ```

- Find all:

  ```php
    use Mifiel\Certificate;
    $certificates = Certificate::all();
  ```

- Create
  
  ```php
    use Mifiel\Certificate;
    $certificate = new Certificate([
      'file' => 'path/to/my-certificate.cer'
    ])
    $certificate->save();
  ```

- Delete

  ```php
    use Mifiel\Certificate;
    Certificate::delete('id');
  ```

## Development

Install [grunt](http://gruntjs.com/) and run `grunt` in the terminal. This will run all tests whenever a change is detected to any file in the project.

## Contributing

1. Fork it ( https://github.com/Mifiel/php-api-client/fork )
2. Create your feature branch (`git checkout -b feature/my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin feature/my-new-feature`)
5. Create a new Pull Request

[travis-image]: https://travis-ci.org/Mifiel/php-api-client.svg?branch=master
[travis-url]: https://travis-ci.org/Mifiel/php-api-client
[coveralls-image]: https://coveralls.io/repos/github/Mifiel/php-api-client/badge.svg?branch=master
[coveralls-url]: https://coveralls.io/github/Mifiel/php-api-client?branch=master

[packagist-image]: https://img.shields.io/packagist/v/mifiel/api-client.svg
[packagist-url]: https://packagist.org/packages/mifiel/api-client
