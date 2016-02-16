<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;
use Mifiel\Document;

class DocumentCRUDTest extends \PHPUnit_Framework_TestCase {

  const ORIGINAL_HASH = 'f4dee35b52fc06aa9d47f6297c7cff51e8bcebf90683da234a07ed507dafd57b';
  private static $id;

  public function setTokens() {
    ApiClient::setTokens(
      '44c783d37ef12d3912f911c7b3ac44d657d83b17',
      'm7MvN0kvmF4/TbYGb7ImlWtUbfQ2XSj+STzvmLBCzOI2L+Kgr2ajaOkftQevv8/KJILevxlpvFWpVbj7hczQQg=='
    );
  }

  public function getDocument() {
    $this->setTokens();
    if (self::$id) {
      return Document::get(self::$id);
    }
    $documents = Document::all();
    return reset($documents);
  }

  public function testSaveCreate() {
    $this->setTokens();
    $document = new Document([
      'original_hash' => self::ORIGINAL_HASH,
    ]);
    $document->save();
    self::$id = $document->id;
    // Fetch document again
    $document = $this->getDocument();
    $this->assertEquals(self::ORIGINAL_HASH, $document->original_hash);
  }

  public function testSaveDocCreate() {
    $this->setTokens();
    $document = new Document([
      'file_path' => './tests/fixtures/example.pdf',
    ]);
    $document->save();
    self::$id = $document->id;
    // Fetch document again
    $document = $this->getDocument();
    $this->assertEquals(self::ORIGINAL_HASH, $document->original_hash);
  }

  public function testSaveUpdate() {
    $document = $this->getDocument();
    $this->assertEquals('', $document->callback_url);
    $callback_url = 'blah';
    $document->callback_url = $callback_url;
    $document->save();
    // Fetch document again
    $document = $this->getDocument();
    $this->assertEquals($callback_url, $document->callback_url);
  }

  public function testAll() {
    $this->setTokens();
    $documents = Document::all();
    $this->assertTrue(is_array($documents));
    $this->assertEquals('Mifiel\Document', get_class(reset($documents)));
  }

  public function testGetProperties() {
    $document = $this->getDocument();
    $this->assertEquals(self::$id, $document->id);
  }

  public function testSetProperties() {
    $document = $this->getDocument();
    $this->assertEquals(self::ORIGINAL_HASH, $document->original_hash);

    $original_hash = 'blah';
    $document->original_hash = $original_hash;
    $this->assertEquals($original_hash, $document->original_hash);
  }

  public function testDelete() {
    $documents = Document::all();
    foreach ($documents as $document) {
      $document->delete();
    }
    $documents = Document::all();
    $this->assertEmpty($documents);
  }

}
