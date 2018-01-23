<?php
namespace Mifiel\Tests\Integration;

use Mifiel\ApiClient,
    Mifiel\User;

class UserTest extends MifielTests {

  /**
   * @group internet
   */
  public function testCreateUser() {
    $this->setTokens();
    $user = new User([
      'email' => 'some'.rand(1000).'@gmail.com'
    ]);
    $user->save();
    $this->assertEquals(null, $user->message);
    $this->assertEquals('success', $user->status);
  }
}
