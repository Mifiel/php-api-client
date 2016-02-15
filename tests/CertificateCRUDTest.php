<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;
use Mifiel\Certificate;

class CertificateCRUDTest extends \PHPUnit_Framework_TestCase {

  private static $id;

  public function setTokens() {
    ApiClient::setTokens(
      '44c783d37ef12d3912f911c7b3ac44d657d83b17',
      'm7MvN0kvmF4/TbYGb7ImlWtUbfQ2XSj+STzvmLBCzOI2L+Kgr2ajaOkftQevv8/KJILevxlpvFWpVbj7hczQQg=='
    );
  }

  public function getCertificate() {
    $this->setTokens();
    $certificates = Certificate::all();
    return $certificates[count($certificates) - 1];
  }

  public function testCreate() {
    $this->setTokens();
    $certificate = new Certificate([
      'cer_file' => './tests/fixtures/FIEL_AAA010101AAA.cer'
    ]);
    $certificate->save();
    self::$id = $certificate->id;
    // Fetch document again
    $certificate = $this->getCertificate();
    $this->assertEquals(self::$id, $certificate->id);
  }

  public function testAll() {
    $this->setTokens();
    $certificates = Certificate::all();
    $this->assertTrue(is_array($certificates));
    $this->assertEquals('Mifiel\Certificate', get_class($certificates[0]));
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
    $certificate->delete();
    $certificates = Certificate::all();
    $this->assertEmpty($certificates);
  }

}
