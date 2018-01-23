<?php
// read http://www.sitepoint.com/unit-testing-guzzlephp/
// for testing without server
namespace Mifiel\Tests\Integration;

use Mifiel\ApiClient as Mifiel;

class MifielTests extends \PHPUnit_Framework_TestCase {

  public function setTokens() {
    Mifiel::setTokens(
      '44c783d37ef12d3912f911c7b3ac44d657d83b17',
      'm7MvN0kvmF4/TbYGb7ImlWtUbfQ2XSj+STzvmLBCzOI2L+Kgr2ajaOkftQevv8/KJILevxlpvFWpVbj7hczQQg=='
    );
    Mifiel::url('http://localhost:3000/api/v1/');
  }
}
