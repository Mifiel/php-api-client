<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient,
    Mifiel\Certificate,
    Mockery as m;

class CertificateTest extends \PHPUnit_Framework_TestCase {

  /**
   * @after
   **/
  public function allowMockeryAsertions() {
    if ($container = m::getContainer()) {
      $this->addToAssertionCount($container->mockery_getExpectationCount());
    }
  }

  public function testUnitCreate() {
    $certificate = new Certificate([
      'file_path' => './tests/fixtures/FIEL_AAA010101AAA.cer'
    ]);

    $client = m::mock('ApiClient');
    $client->shouldReceive('post')
           ->with('keys', m::type('Array'), true)
           ->andReturn(new \GuzzleHttp\Psr7\Response)
           ->once();

    $certificate->setClient($client);
    $certificate->save();
  }

  public function testAll() {
    $certificates = Certificate::all();
    $this->assertTrue(is_array($certificates));
    $this->assertEquals('Mifiel\Certificate', get_class(reset($certificates)));
  }

  public function testGetProperties() {
    $certificate = $this->getCertificate();
    $this->assertEquals(self::$id, $certificate->id);
  }

  public function testSetProperties() {
    $certificate = $this->getCertificate();
    $this->assertEquals('20001000000200001410', $certificate->certificate_number);

    $certificate_number = 'blah';
    $certificate->certificate_number = $certificate_number;
    $this->assertEquals($certificate_number, $certificate->certificate_number);
  }

  public function testDelete() {
    $certificate = $this->getCertificate();
    if ($certificate)
      $certificate->delete();
    $certificates = Certificate::all();
    $this->assertEmpty($certificates);
  }

}
