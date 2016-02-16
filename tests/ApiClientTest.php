<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;

class ApiClientTest extends \PHPUnit_Framework_TestCase {

  public function testCreation() {
    $appId = 'appId';
    $appSecret = 'appSecret';
    ApiClient::setTokens($appId, $appSecret);

    $this->assertEquals(ApiClient::appId(), $appId);
    $this->assertEquals(ApiClient::appSecret(), $appSecret);
  }

  public function testSetters() {
    $appId = 'appId';
    $appSecret = 'appSecret';
    ApiClient::appId($appId);
    ApiClient::appSecret($appSecret);

    $this->assertEquals(ApiClient::appId(), $appId);
    $this->assertEquals(ApiClient::appSecret(), $appSecret);
  }
}
