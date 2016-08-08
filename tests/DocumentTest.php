<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient,
    Mifiel\Document,
    Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DocumentTest extends \PHPUnit_Framework_TestCase {

  /**
   * @after
   **/
  public function allowMockeryAsertions() {
    if ($container = m::getContainer()) {
      $this->addToAssertionCount($container->mockery_getExpectationCount());
    }
  }

  public function testCreate() {
    $document = new Document([
      'file_path' => './tests/fixtures/FIEL_AAA010101AAA.cer'
    ]);

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('post')
      ->with('documents', m::type('Array'), true)
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    $document->save();
  }

  public function testSaveFile() {
    $document = new Document(['id' => 'some-id']);
    $path = 'tmp/the-file.pdf';
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('documents/some-id/file')
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    $document->saveFile($path);
  }

  public function testSaveFileSigned() {
    $document = new Document(['id' => 'some-id']);
    $path = 'tmp/the-file-signed.pdf';
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('documents/some-id/file_signed')
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();
    $document->saveFileSigned($path);
  }

  public function testSaveXml() {
    $document = new Document(['id' => 'some-id']);
    $path = 'tmp/the-file.xml';
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('documents/some-id/xml')
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();
    $document->saveXML($path);
  }

  public function testUpdate() {
    $document = new Document();
    $document->id = 'some-id';

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('put')
      ->with('documents/some-id', array('id' => 'some-id'), true)
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    $document->save();
  }

  public function testAll() {
    $mockResponse = m::mock('\GuzzleHttp\Psr7\Response');
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn('[{"id": "some-id"}]');
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('documents')
      ->andReturn($mockResponse)
      ->once();

    $documents = Document::all();
  }

  public function testFind() {
    $mockResponse = m::mock('\GuzzleHttp\Psr7\Response');
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn('{"id": "some-id"}');
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('documents/some-id')
      ->andReturn($mockResponse)
      ->once();

    Document::find('some-id');
  }

  public function testSetGetProperties() {
    $original_hash = hash('sha256', 'some-document-contents');
    $document = new Document([
      'original_hash' => $original_hash
    ]);
    $this->assertEquals($original_hash, $document->original_hash);

    $new_original_hash = 'blah';
    $document->original_hash = $new_original_hash;
    $this->assertEquals($new_original_hash, $document->original_hash);
  }

  public function testDelete() {
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('delete')
      ->with('documents/some-id')
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    Document::delete('some-id');
  }

}
