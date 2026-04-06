<?php
// read http://www.sitepoint.com/unit-testing-guzzlephp/
// for testing without server
namespace Mifiel\Tests\Integration;

use Mifiel\ApiClient as Mifiel;
use PHPUnit\Framework\TestCase;

class MifielTests extends TestCase
{
  public function setTokens(): void
  {
    Mifiel::setTokens(
      'b041efb8db49308e496b70e3bdf51d0005c184d3',
      'M5DHRLCdYCqOs2PELvizW6qr/yXIBj0CAAk9/OR8UbwadFjBeeKn774sf3IVO7G8H2wUJsLsn3uL3H8SJCdd4w==',
    );
    Mifiel::url('http://localhost:3000/api/v1/');
  }
}
