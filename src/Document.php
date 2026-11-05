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

  /** Download the signed PDF and XML archive. */
  public function saveZIP($path) {
    $response = ApiClient::get($this->documentPath() . '/zip');
    if (file_put_contents($path, $response->getBody()) === false) {
      throw new \RuntimeException('Unable to save document ZIP');
    }
  }

  /** Return the current state, signing groups and stakeholders as an object. */
  public function getState() {
    $response = ApiClient::get($this->documentPath() . '/state');
    return json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
  }

  /** Add a signer or reviewer and return the stakeholder object. */
  public function addStakeholder(array $params) {
    $path = $this->documentPath() . '/stakeholders';
    self::checkRequiredArgs(['email' => 'string', 'type' => 'string'], $params);
    $response = ApiClient::post($path, $params);
    return json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
  }

  /** Update a stakeholder and return the updated object. */
  public function updateStakeholder($id, array $params) {
    $path = $this->stakeholderPath($id);
    self::checkRequiredArgs(['email' => 'string', 'type' => 'string'], $params);
    $response = ApiClient::put($path, $params);
    return json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
  }

  /** Delete a stakeholder and return the HTTP response (204 or 205). */
  public function deleteStakeholder($id) {
    return ApiClient::delete($this->stakeholderPath($id));
  }

  private function documentPath() {
    if (!is_string($this->id) || $this->id === '') {
      throw new ArgumentError('Document id is required', 1);
    }
    return static::$resourceName . '/' . rawurlencode($this->id);
  }

  private function stakeholderPath($id) {
    $path = $this->documentPath();
    if (!is_string($id) || $id === '') {
      throw new ArgumentError('Stakeholder id is required', 1);
    }
    return $path . '/stakeholders/' . rawurlencode($id);
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
    return ApiClient::post(
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
    return ApiClient::post(
      Template::resourceName() . '/' . $args['template_id'] . '/generate_documents',
      $args,
      false
    );
  }
}
