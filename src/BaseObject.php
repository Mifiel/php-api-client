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

  public static function checkRequiredArgs(array $required, $args) {
    $e_required = [];
    $e_type = [];
    foreach ($required as $key => $type) {
      if (array_key_exists($key, $args)) {
        if (gettype($args[$key]) != $type) {
          array_push($e_type, "Param '{$key}' must be '$type'");
        }
      } else {
        array_push($e_required, $key);
      }
    }
    if (!empty($e_required) || !empty($e_type)) {
      $errors = [];
      if (!empty($e_required)) {
        $e_required = join(', ', $e_required);
        array_push($errors, "Params '{$e_required}' are required");
      }
      $errors = $errors + $e_type;

      throw new ArgumentError(join(', ', $errors), 1);
    }
    return true;
  }

  public static function find($id) {
    self::validateResuorceName();
    $response = ApiClient::get(static::$resourceName . '/' . $id);
    $response_body = json_decode($response->getBody());
    return new static($response_body);
  }

  public static function resourceName() {
    return static::$resourceName;
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
