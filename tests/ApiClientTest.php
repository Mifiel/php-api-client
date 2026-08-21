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
    $this->assertEquals('https://app.mifiel.com/api/v1/', Mifiel::url());
  }

  public function testUserAgent(): void
  {
    $ua = Mifiel::userAgent();
    $parts = explode(' ', $ua);

    $this->assertStringStartsWith('PHP/', $parts[0]);
    $this->assertStringStartsWith('mifiel/api-client/', $parts[1]);
    $this->assertStringStartsWith('guzzle/', $parts[2]);
    $this->assertStringStartsWith('(', $parts[3]);
    $this->assertStringEndsWith(')', $parts[3]);
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
