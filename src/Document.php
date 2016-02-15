<?php
namespace Mifiel;

class Document extends BaseObject {
  protected static $resourceName = 'documents';

  public function save() {
    if ($this->file_path) {
      $this->file = [
        'filename' => basename($this->file_path),
        'contents' => fopen($this->file_path, 'r')
      ];
    }
    $response = ApiClient::post(
      static::$resourceName,
      (array) $this->values,
      true
    );
    $this->values = (object) json_decode($response->getBody());
  }
}
