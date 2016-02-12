<?php
namespace Mifiel;

use HttpSignatures\Context;
use Symfony\Component\HttpFoundation\Request;

class ApiClient {

  private static $appId;
  private static $appSecret;
  private static $context;
  private static $url;

  public static function setTokens($appId, $appSecret) {
    self::$appId = $appId;
    self::$appSecret = $appSecret;
    self::$url = 'http://genaro-book.local';
    self::setContext();
  }

  public static function get($path, $params=array()) {
    $url = self::buildUrl($path);
    $request = Request::create($url, 'GET', $params);
    $request->headers->replace(array(
      'HTTP-METHOD'   => 'GET',
      'CONTENT-TYPE'  => 'application/json',
      'CONTENT-MD5'   => md5(json_encode($params)),
      'REQUEST-URI'   => $path,
      'TIMESTAMP'     => gmdate('D, d M Y H:i:s T')
    ));
    self::$context->signer()->sign($request);
    return $request;
  }

  public static function url(){
    return self::$url;
  }

  public static function buildUrl($path) {
    return self::$url + $path;
  }

  public static function appId($appId=null) {
    if ($appId) {
      self::$appId = $appId;
      setContext();
      return;
    }
    return self::$appId;
  }

  public static function appSecret($appSecret=null) {
    if ($appSecret) {
      self::$appSecret = $appSecret;
      setContext();
      return;
    }
    return self::$appSecret;
  }

  public static function context(){
    return self::$context;
  }

  private static function setContext() {
    self::$context = new Context(array(
      'keys'      => array(self::$appId => self::$appSecret),
      'algorithm' => 'hmac-sha1',
      'headers'   => array(
        'HTTP-METHOD',
        'CONTENT-TYPE',
        'CONTENT-MD5',
        'REQUEST-URI',
        'TIMESTAMP'
      ),
    ));
  }
}
