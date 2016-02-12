<?php
namespace Mifiel;

use GuzzleHttp\Psr7\Request;
use Acquia\Hmac\Guzzle\HmacAuthMiddleware;
use Acquia\Hmac\RequestSigner;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Mifiel\Digest\ApiAuthGemDigest;

class ApiClient {

  private static $appId;
  private static $appSecret;
  private static $client;
  private static $url;

  public static function setTokens($appId, $appSecret) {
    self::$appId = $appId;
    self::$appSecret = $appSecret;
    self::$url = 'http://genaro-book.local:3000/api/v1/';
    self::setClient();
  }

  public static function get($path, $params=array()) {
    $headers = array(
      'http-method'  => 'GET',
      'content-type' => 'application/json',
      'content-md5'  => md5(json_encode($params)),
      'request-uri'  => $path,
      'timestamp'    => gmdate('D, d M Y H:i:s T')
    );
    $request = new Request('GET', $path, $headers, $params);

    return self::$client->send($request);
  }

  public static function url(){
    return self::$url;
  }

  public static function appId($appId=null) {
    if ($appId) {
      self::$appId = $appId;
      setClient();
      return;
    }
    return self::$appId;
  }

  public static function appSecret($appSecret=null) {
    if ($appSecret) {
      self::$appSecret = $appSecret;
      setClient();
      return;
    }
    return self::$appSecret;
  }

  public static function context(){
    return self::$client;
  }

  private static function setClient() {
    $signer = new RequestSigner(new ApiAuthGemDigest());

    $middleware = new HmacAuthMiddleware(
      $signer,
      self::$appId,
      self::$appSecret
    );

    $stack = HandlerStack::create();
    $stack->push($middleware);

    self::$client = new Client([
      'base_uri' => self::$url,
      'handler' => $stack,
    ]);
  }
}
