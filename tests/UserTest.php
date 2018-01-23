<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient,
    Mifiel\User,
    Mockery as m;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UserTest extends \PHPUnit_Framework_TestCase {

  /**
   * @after
   **/
  public function allowMockeryAsertions() {
    if ($container = m::getContainer()) {
      $this->addToAssertionCount($container->mockery_getExpectationCount());
    }
  }

  public function testCreate() {
    $user = new User([
      'email' => 'some@email.com'
    ]);

    m::mock('alias:Mifiel\ApiClient')
      ->shouldReceive('post')
      ->with('users', m::type('Array'), false)
      ->andReturn(new \GuzzleHttp\Psr7\Response)
      ->once();

    $user->save();
  }

  public function testFind() {
    $mockResponse = m::mock('\GuzzleHttp\Psr7\Response');
    $mockResponse->shouldReceive('getBody')
                 ->once()
                 ->andReturn('{"id": "some-id"}');

    $this->setExpectedException('\Exception');
    User::find('some-id');
  }

  public function testDelete() {
    $this->setExpectedException('\Exception');

    User::delete('some-id');
  }
}
