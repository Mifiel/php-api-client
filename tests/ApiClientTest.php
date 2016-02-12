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

  public function testBuildUrl() {
    $path = '/some-path';
    $url = ApiClient::buildUrl($path);
    $this->assertEquals($url, ApiClient::url() + $path);
  }

}
