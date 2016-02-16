<?php
namespace Mifiel;

class ArgumentError extends \Exception {}

abstract class BaseObject {

  protected $values;
  protected $multipart = false;

  public function __construct($values=array()) {
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

  public static function find($id) {
    self::validateResuorceName();
    $response = ApiClient::get(static::$resourceName . '/' . $id);
    $response_body = json_decode($response->getBody());
    return new static($response_body);
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
        (array) $this->values,
        $this->multipart
      );
    } else {
      $response = ApiClient::post(
        static::$resourceName,
        (array) $this->values,
        $this->multipart
      );
    }
    $this->values = (object) json_decode($response->getBody());
  }

  public static function delete($id){
    self::validateResuorceName();
    $response = ApiClient::delete(
      static::$resourceName . '/' . $id
    );
  }

  public function __get($property) {
    if (isset($this->values->$property)) {
      return $this->values->$property;
    }
  }

  public function __set($property, $value) {
    $this->values->$property = $value;
    return $this;
  }
}
