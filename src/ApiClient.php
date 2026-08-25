<?php
namespace Mifiel;

use Composer\InstalledVersions;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use Mifiel\Http\HmacAuthMiddleware;

class ApiClient {

  private static $appId;
  private static $appSecret;
  private static $client;
  private static $url;

  public static function setTokens($appId, $appSecret) {
    self::$appId = $appId;
    self::$appSecret = $appSecret;
    self::$url = 'https://app.mifiel.com/api/v1/';
    self::setClient();
  }

  public static function get($path, $params=array()) {
    return self::request('GET', $path, $params);
  }

  public static function post($path, $params=array(), $multipart=false) {
    return self::request('POST', $path, $params, $multipart);
  }

  public static function delete($path) {
    return self::request('DELETE', $path);
  }

  public static function put($path, $params=array(), $multipart=false) {
    return self::request('PUT', $path, $params, $multipart);
  }

  private static function request($type, $path, $params=array(), $multipart=false) {
    $options = [];
    if ($multipart) {
      $options['multipart'] = self::build_multipart($params);
    } elseif(!empty($params)) {
      $options['json'] = $params;
    }
    // $options['headers'] = [
    //   'content-md5' => base64_encode(md5(json_encode($params), true))
    // ];
    return self::$client->request(strtoupper($type), $path, $options);
  }

  private static function build_multipart($params) {
    $multipart_arr = array();
    foreach ($params as $name => $value) {
      $field = self::build_field($name, $value);
      if ($field){
        array_push($multipart_arr, $field);
      }
    }
    return $multipart_arr;
  }

  private static function build_field($name, $value) {
    if (is_array($value) && isset($value['filename'])) {
      return [
        'name'      => $name,
        'contents'  => $value['contents'],
        'filename'  => $value['filename']
      ];
    }
    if (!empty($value) && gettype($value) != 'NULL') {
      if (is_bool($value)){
        $value = $value === true ? '1' : '0';
      } elseif (is_array($value) || is_object($value)) {
        $value = json_encode($value);
      }

      return [
        'name' => $name,
        'contents' => $value
      ];
    }
    return false;
  }

  public static function url($url=null){
    if ($url){
      self::$url = $url;
      self::setClient();
    } else {
      return self::$url;
    }
  }

  public static function appId($appId=null) {
    if ($appId) {
      self::$appId = $appId;
      self::setClient();
      return;
    }
    return self::$appId;
  }

  public static function appSecret($appSecret=null) {
    if ($appSecret) {
      self::$appSecret = $appSecret;
      self::setClient();
      return;
    }
    return self::$appSecret;
  }

  private static function setClient() {
    $middleware = new HmacAuthMiddleware(self::$appId, self::$appSecret);

    $stack = HandlerStack::create();
    $stack->push($middleware);

    self::$client = new Client([
      'base_uri' => self::url(),
      'handler' => $stack,
      'headers' => [
        'User-Agent' => self::userAgent(),
      ],
    ]);
  }

  /**
   * Example: PHP/8.3.0 mifiel/api-client/4.0.0 guzzle/7.9.2 (Linux/6.8.0)
   */
  public static function userAgent(): string
  {
    $guzzleVersion = self::packageVersion('guzzlehttp/guzzle', (string) ClientInterface::MAJOR_VERSION);
    $os = str_replace(' ', '_', php_uname('s'));
    $osRelease = str_replace(' ', '_', php_uname('r'));

    return implode(' ', [
      'PHP/' . PHP_VERSION,
      'mifiel/api-client/' . Version::STRING,
      'guzzle/' . $guzzleVersion,
      sprintf('(%s/%s)', $os, $osRelease),
    ]);
  }

  private static function packageVersion(string $package, string $fallback): string
  {
    if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled($package)) {
      $version = InstalledVersions::getPrettyVersion($package);
      if (is_string($version) && $version !== '') {
        return $version;
      }
    }

    return $fallback;
  }

  public static function getClient() {
    return self::$client;
  }
}
