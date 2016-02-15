<?php
namespace Mifiel;

class Document extends BaseObject {
  protected static $resourceName = 'documents';

  public function save() {
    if ($this->file && file_exists($this->file)) {
      $this->file = [
        'filename' => basename($this->file),
        'contents' => fopen($this->file, 'r')
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
