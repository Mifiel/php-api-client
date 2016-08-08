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
}
