<?php
// read http://www.sitepoint.com/unit-testing-guzzlephp/
// for testing without server
namespace Mifiel\Tests;

use Mifiel\ApiClient;


class MifielTests extends \PHPUnit_Framework_TestCase {

  public function setTokens() {
    ApiClient::setTokens(
      '44c783d37ef12d3912f911c7b3ac44d657d83b17',
      'm7MvN0kvmF4/TbYGb7ImlWtUbfQ2XSj+STzvmLBCzOI2L+Kgr2ajaOkftQevv8/KJILevxlpvFWpVbj7hczQQg=='
    );
    ApiClient::url('http://genaro-book.local:3000/api/v1/');
  }
}
