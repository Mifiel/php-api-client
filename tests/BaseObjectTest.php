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
    $this->assertEquals($resp['valid'], true);
  }

  /**
   * @expectedException        Mifiel\ArgumentError
   * @expectedExceptionMessage Param 'arg' is required
   */
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
    BaseObject::checkRequiredArgs($required, $args);
  }

  /**
   * @expectedException        Mifiel\ArgumentError
   * @expectedExceptionMessage Param 'other' must be a array with (other-arg, some-arg)
   */
  public function testCheckRequiredArgsRequiredNested() {
    $required = [
      'some' => 'string',
      'other' => [
        'other-arg' => 'string',
        'some-arg' => 'array',
      ],
      'arg' => 'string',
    ];
    $args = [
      'some' => 'blah',
      'other' => 'blah1',
      'arg' => 'blah1'
    ];
    BaseObject::checkRequiredArgs($required, $args);
  }

  /**
   * @expectedException        Mifiel\ArgumentError
   * @expectedExceptionMessage Param 'other-arg' must be a string, Param 'some-arg' must be a array
   */
  public function testCheckRequiredArgsRequiredNested2() {
    $required = [
      'some' => 'string',
      'other' => [
        'other-arg' => 'string',
        'some-arg' => 'array',
      ],
      'arg' => 'string',
    ];
    $args = [
      'some' => 'blah',
      'other' => [
        'other-arg' => ['string'],
        'some-arg' => 'asom',
      ],
      'arg' => 'blah1'
    ];
    BaseObject::checkRequiredArgs($required, $args);
  }

  /**
   * @expectedException        Mifiel\ArgumentError
   * @expectedExceptionMessage Param 'other' must be a string
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

  /**
   * @expectedException        Mifiel\ArgumentError
   * @expectedExceptionMessage Param 'other' must be a string or a integer
   */
  public function testCheckRequiredArgsWrongTypeOr() {
    $required = [
      'some' => 'string',
      'other' => 'string|integer',
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
