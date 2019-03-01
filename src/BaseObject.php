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

  public static function checkRequiredArgs(array $types, $args, $throw = true) {
    $errors = [];
    foreach ($types as $key => $type) {
      $arg = $args[$key];
      if (is_array($type)) {
        if (is_array($arg)) {
          $validation = self::checkRequiredArgs($type, $arg, false);
          if (!$validation['valid']) {
            $errors = array_merge($errors, $validation['errors']);
          }
        } else {
          $keys = join(', ', array_keys($type));
          array_push($errors, "Param '{$key}' must be a array with ($keys)");
        }
        continue;
      }
      if (array_key_exists($key, $args)) {
        $validation = self::validType($type, $arg);
        if (!$validation['valid']) {
          $error = "Param '{$key}' must be a {$validation['types']}";
          array_push($errors, $error);
        }
      } else {
        array_push($errors, "Param '{$key}' is required");
      }
    }
    if (!empty($errors)) {
      if (!$throw) return ['valid' => false, 'errors' => $errors];
      throw new ArgumentError(join(', ', $errors), 1);
    }
    return ['valid' => true];
  }

  public static function checkTypes(array $types, $args, $throw = true) {
    $errors = [];
    foreach ($types as $key => $type) {
      $arg = $args[$key];
      if (is_array($type)) {
        if (is_array($arg)) {
          $validation = self::checkTypes($type, $arg, false);
          if (!$validation['valid']) {
            $errors = array_merge($errors, $validation['errors']);
          }
        } else {
          $keys = join(', ', array_keys($type));
          array_push($errors, "Param '{$key}' must be a array with ($keys)");
        }
        continue;
      }
      if (array_key_exists($key, $args)) {
        $validation = self::validType($type, $arg);
        if (!$validation['valid']) {
          $error = "Param '{$key}' must be a {$validation['types']}";
          array_push($errors, $error);
        }
      }
    }
    if (!empty($errors)) {
      if (!$throw) return ['valid' => false, 'errors' => $errors];
      throw new ArgumentError(join(', ', $errors), 1);
    }
    return ['valid' => true];
  }

  private static function validType(string $type, $value) {
    $or_types = explode('|', $type);
    if (!in_array(gettype($value), $or_types)) {
      $msg = implode(' or a ', $or_types);
      return ['valid' => false, 'types' => $msg];
    }
    return ['valid' => true];
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
