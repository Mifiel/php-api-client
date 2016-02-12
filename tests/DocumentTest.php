<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;
use Mifiel\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase {

  const ORIGINAL_HASH = '7cf3c80bbe595734a960b49a79d6e87f8932c21fea6665b69cfa1257f85f7dc1';

  public function setTokens() {
    ApiClient::setTokens(
      '44c783d37ef12d3912f911c7b3ac44d657d83b17',
      'm7MvN0kvmF4/TbYGb7ImlWtUbfQ2XSj+STzvmLBCzOI2L+Kgr2ajaOkftQevv8/KJILevxlpvFWpVbj7hczQQg=='
    );
  }

  public function getDocument() {
    $this->setTokens();
    $documents = Document::all();
    return $documents[0];
  }

  public function testAll() {
    $this->setTokens();
    $documents = Document::all();
    $this->assertTrue(is_array($documents));
    $this->assertEquals('Mifiel\Document', get_class($documents[0]));
  }

  public function testGetProperties() {
    $document = $this->getDocument();
    $id = '708c8b9d-5440-46ef-8b67-5297aaf058d5';
    $this->assertEquals($id, $document->id);
  }

  public function testSetProperties() {
    $document = $this->getDocument();
    $this->assertEquals(self::ORIGINAL_HASH, $document->original_hash);

    $original_hash = 'blah';
    $document->original_hash = $original_hash;
    $this->assertEquals($original_hash, $document->original_hash);
  }

  public function testSave() {
    $document = $this->getDocument();
    $this->assertEquals('', $document->callback_url);
    $callback_url = 'blah';
    $document->callback_url = $callback_url;
    $document->save();
    // Fetch document again
    $document = $this->getDocument();
    $this->assertEquals($callback_url, $document->callback_url);
  }

}
