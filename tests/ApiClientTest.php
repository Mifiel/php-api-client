<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient as Mifiel;

class ApiClientTest extends MifielTests {

  private $appId = 'appId';
  private $appSecret = 'appSecret';

  public function testCreation() {
    Mifiel::setTokens($this->appId, $this->appSecret);

    $this->assertEquals(Mifiel::appId(), $this->appId);
    $this->assertEquals(Mifiel::appSecret(), $this->appSecret);
  }

  public function testSetters() {
    Mifiel::appId($this->appId);
    Mifiel::appSecret($this->appSecret);

    $this->assertEquals(Mifiel::appId(), $this->appId);
    $this->assertEquals(Mifiel::appSecret(), $this->appSecret);
  }
}
