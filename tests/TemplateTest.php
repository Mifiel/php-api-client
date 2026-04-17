<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;
use Mifiel\Template;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class TemplateTest extends TestCase
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
    $template = new Template([
      'name' => 'some template name',
      'content' => '<field name="some">SOME</field>'
    ]);

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('post')
      ->with('templates', m::type('Array'), false)
      ->andReturn(new \GuzzleHttp\Psr7\Response())
      ->once();

    $template->save();
  }

  public function testUpdate(): void
  {
    $template = new Template();
    $template->id = 'some-id';

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('put')
      ->with('templates/some-id', array('id' => 'some-id'), false)
      ->andReturn(new \GuzzleHttp\Psr7\Response())
      ->once();

    $template->save();
  }

  public function testAll(): void
  {
    $mockResponse = m::mock(\GuzzleHttp\Psr7\Response::class);
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn(\GuzzleHttp\Psr7\Utils::streamFor('[{"id": "some-id"}]'));
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('templates')
      ->andReturn($mockResponse)
      ->once();

    $templates = Template::all();
  }

  public function testFind(): void
  {
    $mockResponse = m::mock(\GuzzleHttp\Psr7\Response::class);
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn(\GuzzleHttp\Psr7\Utils::streamFor('{"id": "some-id"}'));
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('get')
      ->with('templates/some-id')
      ->andReturn($mockResponse)
      ->once();

    Template::find('some-id');
  }

  public function testSetGetProperties(): void
  {
    $original_hash = hash('sha256', 'some-template-contents');
    $template = new Template([
      'original_hash' => $original_hash
    ]);
    $this->assertEquals($original_hash, $template->original_hash);

    $new_original_hash = 'blah';
    $template->original_hash = $new_original_hash;
    $this->assertEquals($new_original_hash, $template->original_hash);
  }

  public function testDelete(): void
  {
    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('delete')
      ->with('templates/some-id')
      ->andReturn(new \GuzzleHttp\Psr7\Response())
      ->once();

    Template::delete('some-id');
  }
}
