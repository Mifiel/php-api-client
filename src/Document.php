<?php
namespace Mifiel;

class Document {

  private $values;

  public function __constuct($values) {
    $this->values = $values;
  }

  public static function all() {
    $request = Client::get('/documents');
    echo "Content: " . $request->getPathInfo();
    return array('' => ',' );
  }

  public function __get($property) {
    if ($values[$property]) {
      return $this->values[$property];
    }
  }

  public function __set($property, $value) {
    $this->values[$property] = $value;
    return $this;
  }
}
