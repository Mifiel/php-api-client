<?php
namespace Mifiel;

class ArgumentError extends \Exception {}

abstract class BaseObject {

  private $values;

  public function __construct($values) {
    $this->values = (object) $values;
  }

  public static function all() {
    self::validateResuorceName();
    $response = ApiClient::get(static::$resourceName);
    $response_body_arr = json_decode($response->getBody());
    $return = array();
    foreach ($response_body_arr as $object) {
      $return[] = new static($object);
    }
    return $return;
  }

  private static function validateResuorceName() {
    if (static::$resourceName == null){
      throw new ArgumentError('You must declare resourceName', 1);
    }
  }

  public function save(){
    self::validateResuorceName();
    if ($this->id) {
      $response = ApiClient::put(
        static::$resourceName . '/' . $this->id,
        (array) $this->values
      );
    } else {
      $response = ApiClient::post(
        static::$resourceName,
        (array) $this->values
      );
    }
  }

  public function delete(){
    self::validateResuorceName();
    $response = ApiClient::delete(
      static::$resourceName . '/' . $this->id
    );
  }

  public function values() {
    return $this->values;
  }

  public function __get($property) {
    if ($this->values->$property) {
      return $this->values->$property;
    }
  }

  public function __set($property, $value) {
    $this->values->$property = $value;
    return $this;
  }
}
