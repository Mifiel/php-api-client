<?php
namespace Mifiel\Tests;

use Mifiel\Client;
use Mifiel\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase {

  public function testAll() {
    Client::setTokens($app_id, $app_secret);
    $documents = Document::all();
    $this->assertTrue(is_array($documents));
  }

}
