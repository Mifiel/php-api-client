<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;

class ApiClientTest extends MifielTests {

  private $appId = 'appId';
  private $appSecret = 'appSecret';

  public function testCreation() {
    ApiClient::setTokens($this->appId, $this->appSecret);

    $this->assertEquals(ApiClient::appId(), $this->appId);
    $this->assertEquals(ApiClient::appSecret(), $this->appSecret);
  }

  public function testSetters() {
    ApiClient::appId($this->appId);
    ApiClient::appSecret($this->appSecret);

    $this->assertEquals(ApiClient::appId(), $this->appId);
    $this->assertEquals(ApiClient::appSecret(), $this->appSecret);
  }
}
