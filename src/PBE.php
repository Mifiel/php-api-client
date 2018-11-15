<?php
namespace Mifiel;

class PBE {
  private $numIterations = 1000;
  private $digestAlgorithm = 'sha256';

  function __construct($params = null) {
    if (is_array($params)) {
      if (array_key_exists('iterations', $params)) {
        $this->numIterations = $params['iterations'];
      }
      if (array_key_exists('digestAlgorithm', $params)) {
        $this->digestAlgorithm = $params['digestAlgorithm'];
      }
    } else if ($params !== null) {
      throw new \InvalidArgumentException('PBE construct expects an (object)[] of params');
    }
  }

  public function setIterations($num) {
    $this->numIterations = $num;
  }

  public function getIterations() {
    return $this->numIterations;
  }

  public function setDigestAlgorithm($alg) {
    $this->digestAlgorithm = $alg;
  }

  public function getDigestAlgorithm() {
    return $this->digestAlgorithm;
  }

  public static function randomPassword($length = 32) {
    $password = '';
    while (strlen($password) < $length) {
      $randomBytes = openssl_random_pseudo_bytes(100, $safe);
      $password .= preg_replace('/[^\x20-\x7E]/', '', $randomBytes);
    }
    return substr($password, 0, $length);
  }

  public static function randomSalt($size = 16) {
    return openssl_random_pseudo_bytes($size, $safe);
  }

  public function getDerivedKey($password, $salt, $size) {
    if ($size > 1000) {
      throw new \InvalidArgumentException('key lenght/size requested is too long');
    }
    return hash_pbkdf2($this->digestAlgorithm, $password, $salt, $this->numIterations, $size * 2);
  }
}
