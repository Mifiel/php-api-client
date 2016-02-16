<?php
namespace Mifiel\Tests\Integration;

use Mifiel\ApiClient,
    Mifiel\Certificate;

class CertificateCRUDTest extends MifielTests {

  private static $id;

  public function getCertificate() {
    $this->setTokens();
    $certificates = Certificate::all();
    return end($certificates);
  }

  /**
   * @group internet
   */
  public function testCreate() {
    $this->setTokens();
    $certificate = new Certificate([
      'file_path' => './tests/fixtures/FIEL_AAA010101AAA.cer'
    ]);
    $certificate->save();
    self::$id = $certificate->id;
    // Fetch document again
    $certificate = $this->getCertificate();
    $this->assertEquals(self::$id, $certificate->id);
  }

  /**
   * @group internet
   */
  public function testAll() {
    $this->setTokens();
    $certificates = Certificate::all();
    $this->assertTrue(is_array($certificates));
    $this->assertEquals('Mifiel\Certificate', get_class(reset($certificates)));
  }

  /**
   * @group internet
   */
  public function testGetProperties() {
    $certificate = $this->getCertificate();
    $this->assertEquals(self::$id, $certificate->id);
  }

  /**
   * @group internet
   */
  public function testSetProperties() {
    $certificate = $this->getCertificate();
    $this->assertEquals('20001000000200001410', $certificate->certificate_number);

    $certificate_number = 'blah';
    $certificate->certificate_number = $certificate_number;
    $this->assertEquals($certificate_number, $certificate->certificate_number);
  }

  /**
   * @group internet
   */
  public function testDelete() {
    $certificate = $this->getCertificate();
    if ($certificate)
      Certificate::delete($certificate->id);
    $certificates = Certificate::all();
    $this->assertEmpty($certificates);
  }

}
