<?php
namespace Mifiel\Tests;

use Mifiel\Client;

class ClientTest extends \PHPUnit_Framework_TestCase {

  public function testCreation() {
    $appId = 'appId';
    $appSecret = 'appSecret';
    Client::setTokens($appId, $appSecret);

    $this->assertEquals(Client::appId(), $appId);
    $this->assertEquals(Client::appSecret(), $appSecret);
  }

  public function testBuildUrl() {
    $path = '/some-path';
    $url = Client::buildUrl($path);
    $this->assertEquals($url, Client::url() + $path);
  }

}
