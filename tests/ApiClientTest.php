<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient as Mifiel;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
  private string $appId = 'appId';
  private string $appSecret = 'appSecret';
  private string $url = 'http://www.example.com/api/v1/';

  public function testCreation(): void
  {
    Mifiel::setTokens($this->appId, $this->appSecret);

    $this->assertEquals($this->appId, Mifiel::appId());
    $this->assertEquals($this->appSecret, Mifiel::appSecret());
  }

  public function testSetters(): void
  {
    Mifiel::appId($this->appId);
    Mifiel::appSecret($this->appSecret);
    Mifiel::url($this->url);

    $this->assertEquals($this->appId, Mifiel::appId());
    $this->assertEquals($this->appSecret, Mifiel::appSecret());
    $this->assertEquals($this->url, Mifiel::url());
  }

  public function testGetClient(): void
  {
    $this->assertEquals(\GuzzleHttp\Client::class, get_class(Mifiel::getClient()));
  }
}
