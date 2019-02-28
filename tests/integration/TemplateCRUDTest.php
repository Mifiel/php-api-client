<?php
namespace Mifiel\Tests\Integration;

use Mifiel\ApiClient,
    Mifiel\Template;

class TemplateCRUDTest extends MifielTests {
  private static $id;

  public function getTemplate() {
    $this->setTokens();
    if (self::$id) {
      return Template::find(self::$id);
    }
    $templates = Template::all();
    return reset($templates);
  }

  /**
   * @group internet
   */
  public function testCreate() {
    $this->setTokens();
    $template = new Template([
      'name' => 'some template name',
      'content' => '<field name="some">SOME</field>'
    ]);
    $template->save();
    self::$id = $template->id;
    // Fetch template again
    $template = $this->getTemplate();
    $this->assertEquals(self::$id, $template->id);
  }

  /**
   * @group internet
   */
  public function testSaveUpdate() {
    $template = $this->getTemplate();
    $this->assertEquals('some template name', $template->name);
    $name = 'blah';
    $template->name = $name;
    $template->save();
    // Fetch template again
    $template = $this->getTemplate();
    $this->assertEquals($name, $template->name);
  }

    /**
   * @group internet
   */
  public function testAll() {
    $this->setTokens();
    $templates = Template::all();
    $this->assertTrue(is_array($templates));
    $this->assertEquals('Mifiel\Template', get_class(reset($templates)));
  }

  /**
   * @group internet
   */
  public function testDelete() {
    $template = $this->getTemplate();
    if ($template)
      Template::delete($template->id);
    $templates = Template::all();
    $this->assertEmpty($templates);
  }
}
