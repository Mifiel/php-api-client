<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient as Mifiel;

class ApiClientTest extends \PHPUnit_Framework_TestCase {

  private $appId = 'appId';
  private $appSecret = 'appSecret';
  private $url = 'http://www.example.com/api/v1/';

  public function testCreation() {
    Mifiel::setTokens($this->appId, $this->appSecret);

    $this->assertEquals($this->appId, Mifiel::appId());
    $this->assertEquals($this->appSecret, Mifiel::appSecret());
  }

  public function testSetters() {
    Mifiel::appId($this->appId);
    Mifiel::appSecret($this->appSecret);
    Mifiel::url($this->url);

    $this->assertEquals($this->appId, Mifiel::appId());
    $this->assertEquals($this->appSecret, Mifiel::appSecret());
    $this->assertEquals($this->url, Mifiel::url());
  }

  public function testGetClient() {
    $this->assertEquals('GuzzleHttp\Client', get_class(Mifiel::getClient()));
  }
}
