<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient,
    Mifiel\Template,
    Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TemplateTest extends \PHPUnit_Framework_TestCase {

  /**
   * @after
   **/
  public function allowMockeryAsertions() {
    if ($container = m::getContainer()) {
      $this->addToAssertionCount($container->mockery_getExpectationCount());
    }
  }

  public function testCreate() {
    $template = new Template([
      'name' => 'some template name',
      'content' => '<field name="some">SOME</field>'
    ]);

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('post')
      ->with('templates', m::type('Array'), false)
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    $template->save();
  }

  public function testUpdate() {
    $template = new Template();
    $template->id = 'some-id';

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('put')
      ->with('templates/some-id', array('id' => 'some-id'), false)
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    $template->save();
  }

  public function testAll() {
    $mockResponse = m::mock('\GuzzleHttp\Psr7\Response');
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn('[{"id": "some-id"}]');
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('templates')
      ->andReturn($mockResponse)
      ->once();

    $templates = Template::all();
  }

  public function testFind() {
    $mockResponse = m::mock('\GuzzleHttp\Psr7\Response');
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn('{"id": "some-id"}');
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('templates/some-id')
      ->andReturn($mockResponse)
      ->once();

    Template::find('some-id');
  }

  public function testSetGetProperties() {
    $original_hash = hash('sha256', 'some-template-contents');
    $template = new Template([
      'original_hash' => $original_hash
    ]);
    $this->assertEquals($original_hash, $template->original_hash);

    $new_original_hash = 'blah';
    $template->original_hash = $new_original_hash;
    $this->assertEquals($new_original_hash, $template->original_hash);
  }

  public function testDelete() {
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('delete')
      ->with('templates/some-id')
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    Template::delete('some-id');
  }
}
