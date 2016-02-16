<?php
namespace Mifiel\Tests\Integration;

use Mifiel\ApiClient,
    Mifiel\Document;

class DocumentCRUDTest extends MifielTests {

  const ORIGINAL_HASH = 'f4dee35b52fc06aa9d47f6297c7cff51e8bcebf90683da234a07ed507dafd57b';
  private static $id;

  public function getDocument() {
    $this->setTokens();
    if (self::$id) {
      return Document::find(self::$id);
    }
    $documents = Document::all();
    return reset($documents);
  }

  /**
   * @group internet
   */
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

  /**
   * @group internet
   */
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

  /**
   * @group internet
   */
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

  /**
   * @group internet
   */
  public function testAll() {
    $this->setTokens();
    $documents = Document::all();
    $this->assertTrue(is_array($documents));
    $this->assertEquals('Mifiel\Document', get_class(reset($documents)));
  }

  /**
   * @group internet
   */
  public function testGetProperties() {
    $document = $this->getDocument();
    $this->assertEquals(self::$id, $document->id);
  }

  /**
   * @group internet
   */
  public function testSetProperties() {
    $document = $this->getDocument();
    $this->assertEquals(self::ORIGINAL_HASH, $document->original_hash);

    $original_hash = 'blah';
    $document->original_hash = $original_hash;
    $this->assertEquals($original_hash, $document->original_hash);
  }

  /**
   * @group internet
   */
  public function testDelete() {
    $documents = Document::all();
    foreach ($documents as $document) {
      $document->delete();
    }
    $documents = Document::all();
    $this->assertEmpty($documents);
  }

}
