<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient,
    Mifiel\Certificate,
    Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CertificateTest extends \PHPUnit_Framework_TestCase {

  /**
   * @after
   **/
  public function allowMockeryAsertions() {
    if ($container = m::getContainer()) {
      $this->addToAssertionCount($container->mockery_getExpectationCount());
    }
  }

  public function testCreate() {
    $certificate = new Certificate([
      'file_path' => './tests/fixtures/FIEL_AAA010101AAA.cer'
    ]);

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('post')
      ->with('keys', m::type('Array'), true)
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    $certificate->save();
  }

  public function testUpdate() {
    $certificate = new Certificate();
    $certificate->id = 'some-id';

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('put')
      ->with('keys/some-id', array('id' => 'some-id'), true)
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    $certificate->save();
  }

  public function testAll() {
    $mockResponse = m::mock('\GuzzleHttp\Psr7\Response');
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn('[{"id": "some-id"}]');
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('keys')
      ->andReturn($mockResponse)
      ->once();

    $certificates = Certificate::all();
  }

  public function testFind() {
    $mockResponse = m::mock('\GuzzleHttp\Psr7\Response');
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn('{"id": "some-id"}');
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('keys/some-id')
      ->andReturn($mockResponse)
      ->once();

    Certificate::find('some-id');
  }

  public function testSetGetProperties() {
    $certificate = new Certificate([
      'certificate_number' => '20001000000200001410'
    ]);
    $this->assertEquals('20001000000200001410', $certificate->certificate_number);

    $certificate_number = 'blah';
    $certificate->certificate_number = $certificate_number;
    $this->assertEquals($certificate_number, $certificate->certificate_number);
  }

  public function testDelete() {
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('delete')
      ->with('keys/some-id')
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    Certificate::delete('some-id');
  }

  public function testSat() {
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('keys/sat')
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    Certificate::sat();
  }

}
