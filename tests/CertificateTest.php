<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;
use Mifiel\Certificate;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CertificateTest extends TestCase
{
  /**
   * @after
   */
  public function allowMockeryAssertions(): void
  {
    if ($container = m::getContainer()) {
      $this->addToAssertionCount($container->mockery_getExpectationCount());
    }
  }

  public function testCreate(): void
  {
    $certificate = new Certificate([
      'file_path' => './tests/fixtures/FIEL_CARF7606076K1.cer'
    ]);

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('post')
      ->with('keys', m::type('Array'), true)
      ->andReturn(new \GuzzleHttp\Psr7\Response())
      ->once();

    $certificate->save();
  }

  public function testUpdate(): void
  {
    $certificate = new Certificate();
    $certificate->id = 'some-id';

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('put')
      ->with('keys/some-id', array('id' => 'some-id'), true)
      ->andReturn(new \GuzzleHttp\Psr7\Response())
      ->once();

    $certificate->save();
  }

  public function testAll(): void
  {
    $mockResponse = m::mock(\GuzzleHttp\Psr7\Response::class);
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn(\GuzzleHttp\Psr7\Utils::streamFor('[{"id": "some-id"}]'));
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('keys')
      ->andReturn($mockResponse)
      ->once();

    $certificates = Certificate::all();
  }

  public function testFind(): void
  {
    $mockResponse = m::mock(\GuzzleHttp\Psr7\Response::class);
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn(\GuzzleHttp\Psr7\Utils::streamFor('{"id": "some-id"}'));
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('keys/some-id')
      ->andReturn($mockResponse)
      ->once();

    Certificate::find('some-id');
  }

  public function testSetGetProperties(): void
  {
    $certificate = new Certificate([
      'certificate_number' => '1FB6'
    ]);
    $this->assertEquals('1FB6', $certificate->certificate_number);

    $certificate_number = 'blah';
    $certificate->certificate_number = $certificate_number;
    $this->assertEquals($certificate_number, $certificate->certificate_number);
  }

  public function testDelete(): void
  {
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('delete')
      ->with('keys/some-id')
      ->andReturn(new \GuzzleHttp\Psr7\Response())
      ->once();

    Certificate::delete('some-id');
  }

  public function testSat(): void
  {
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('keys/sat')
      ->andReturn(new \GuzzleHttp\Psr7\Response())
      ->once();

    Certificate::sat();
  }

}
