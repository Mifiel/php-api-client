<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;
use Mifiel\User;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UserTest extends TestCase
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

  public function testFind(): void
  {
    $this->expectException(\Exception::class);
    User::find('some-id');
  }

  public function testDelete(): void
  {
    $this->expectException(\Exception::class);
    User::delete('some-id');
  }
}
