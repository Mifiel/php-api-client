<?php
namespace Mifiel\Tests;

use Mifiel\ApiClient;
use Mifiel\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase {

  public function testAll() {
    ApiClient::setTokens(
      '44c783d37ef12d3912f911c7b3ac44d657d83b17',
      'm7MvN0kvmF4/TbYGb7ImlWtUbfQ2XSj+STzvmLBCzOI2L+Kgr2ajaOkftQevv8/KJILevxlpvFWpVbj7hczQQg=='
    );
    $documents = Document::all();
    $this->assertTrue(is_array($documents));
    $this->assertTrue(get_class($documents[0]) == 'Mifiel\Document');
  }

}
