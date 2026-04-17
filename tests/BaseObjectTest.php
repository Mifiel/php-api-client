<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;
use Mifiel\ArgumentError;
use Mifiel\BaseObject;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class BaseObjectTest extends TestCase
{
  public function testCheckRequiredArgsOK(): void
  {
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
    $this->assertTrue($resp);
  }

  public function testCheckRequiredArgsRequired(): void
  {
    $required = [
      'some' => 'string',
      'other' => 'string',
      'arg' => 'array',
    ];
    $args = [
      'some' => 'blah',
      'other' => 'blah1'
    ];
    $this->expectException(ArgumentError::class);
    BaseObject::checkRequiredArgs($required, $args);
  }

  public function testCheckRequiredArgsWrongType(): void
  {
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
    $this->expectException(ArgumentError::class);
    $this->expectExceptionMessage("Param 'other' must be 'string'");
    BaseObject::checkRequiredArgs($required, $args);
  }
}
