<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient,
    Mifiel\BaseObject,
    Mifiel\ArgumentError;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class BaseObjectTest extends \PHPUnit_Framework_TestCase {
  public function testCheckRequiredArgsOK() {
    $required = [
      'some' => 'string',
      'other' => 'string',
      'arg' => 'array',
    ];
    $args = [
      'some' => 'blah',
      'other' => 'blah1',
      'arg' => ['some' => 'arg']
    ];
    $resp = BaseObject::checkRequiredArgs($required, $args);
    $this->assertEquals($resp, true);
  }

  public function testCheckRequiredArgsRequired() {
    $required = [
      'some' => 'string',
      'other' => 'string',
      'arg' => 'array',
    ];
    $args = [
      'some' => 'blah',
      'other' => 'blah1'
    ];
    $this->setExpectedException('Mifiel\ArgumentError');
    BaseObject::checkRequiredArgs($required, $args);
  }

  /**
   * @expectedException        Mifiel\ArgumentError
   * @expectedExceptionMessage Param 'other' must be 'string'
   */
  public function testCheckRequiredArgsWrongType() {
    $required = [
      'some' => 'string',
      'other' => 'string',
      'arg' => 'array',
    ];
    $args = [
      'some' => 'blah',
      'other' => ['blah1'],
      'arg' => ['some']
    ];
    BaseObject::checkRequiredArgs($required, $args);
  }
}
