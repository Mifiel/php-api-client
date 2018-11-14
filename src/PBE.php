<?php
namespace Mifiel;

class PBE {
  private $numIterations = 1000;
  private $digestAlgorithm = 'sha256';

  function __construct($params = array()) {
    if (is_array($params)) {
      foreach ($params as $property => $value) {
        if (property_exists($this, $property)) {
          $this->$property = $value;
        }
      }
    }
  }

  public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function __set($property, $value) {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }
    return $this;
  }

  public function randomPassword($length = 32) {
    $password = '';
    while (strlen($password) < $length) {
      $randomBytes = openssl_random_pseudo_bytes(100, $safe);
      $password .= preg_replace('/[^\x20-\x7E]/', '', $randomBytes);
    }
    return substr($password, 0, $length);
  }

  public function randomSalt($size = 16) {
    return openssl_random_pseudo_bytes($size, $safe);
  }

  public function getDerivedKey($password, $salt, $size) {
    if ($size > 1000) {
      throw new \InvalidArgumentException('key lenght/size requested is too long');
    }
    return hash_pbkdf2($this->digestAlgorithm, $password, $salt, $this->numIterations, $size * 2);
  }
}
