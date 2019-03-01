<?php
namespace Mifiel;

class Document extends BaseObject {
  protected static $resourceName = 'documents';
  protected $multipart = true;

  public function save() {
    unset($this->values->file);
    if (isset($this->values->file_path)) {
      $this->file = [
        'filename' => basename($this->file_path),
        'contents' => fopen($this->file_path, 'r')
      ];
      unset($this->values->file_path);
    }
    parent::save();
  }

  public function transfer($args) {
    $optionalKeys = [
      'template_id' => 'string',
      'fields' => 'string',
      'callback_url' => 'string',
      'receiver' => 'array|string',
      'signatories' => 'array',
      'from' => 'string', # RFC of the issuer
      'to' => 'string', # RFC of the receiver
      'external_id' => 'string',
      'file_path' => 'string',
    ];
    self::checkTypes($optionalKeys, $args);
    $multipart = false;
    if ($args['file_path']) {
      $args['file'] = [
        'filename' => basename($args['file_path']),
        'contents' => fopen($args['file_path'], 'r')
      ];
      $multipart = true;
      unset($args['file_path']);
    }
    $response = ApiClient::post(
      static::$resourceName . '/' . $this->id . '/transfer',
      $args,
      $multipart
    );
    return json_decode($response->getBody());
  }

  public function saveFile($path) {
    $response = ApiClient::get(
      static::$resourceName . '/' . $this->id . '/file'
    );
    file_put_contents($path, $response->getBody());
  }

  public function saveFileSigned($path) {
    $response = ApiClient::get(
      static::$resourceName . '/' . $this->id . '/file_signed'
    );
    file_put_contents($path, $response->getBody());
  }

  public function saveXML($path) {
    $response = ApiClient::get(
      static::$resourceName . '/' . $this->id . '/xml'
    );
    file_put_contents($path, $response->getBody());
  }

  public static function createFromTemplate($args) {
    $requiredKeys = [
      'template_id' => 'string',
      'name' => 'string',
      'fields' => 'array',
      'signatories' => 'array',
      'external_id' => 'string'
    ];
    self::checkRequiredArgs($requiredKeys, $args);
    ApiClient::post(
      Template::resourceName() . '/' . $args['template_id'] . '/generate_document',
      $args,
      false
    );
  }

  public static function createManyFromTemplate($args) {
    $requiredKeys = [
      'template_id' => 'string',
      'identifier' => 'string',
      'callback_url' => 'string',
      'documents' => 'array'
    ];
    self::checkRequiredArgs($requiredKeys, $args);
    ApiClient::post(
      Template::resourceName() . '/' . $args['template_id'] . '/generate_documents',
      $args,
      false
    );
  }
}
