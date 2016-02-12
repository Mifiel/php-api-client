<?php
namespace Mifiel;

class ArgumentError extends \Exception {}

abstract class BaseObject {

  private $values;

  public function __constuct($values) {
    $this->values = $values;
  }

  public static function all() {
    if (static::$resourceName == null){
      throw new ArgumentError('You must declare resourceName', 1);
    }
    $response = ApiClient::get(static::$resourceName);
    $response_body_arr = json_decode($response->getBody());
    $return = array();
    foreach ($response_body_arr as $document) {
      $return[] = new Document($document);
    }
    return $return;
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
