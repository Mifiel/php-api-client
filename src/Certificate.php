<?php
namespace Mifiel;

class Certificate extends BaseObject {
  protected static $resourceName = 'keys';

  public function save() {
    if ($this->cer_file) {
      $this->cer_file = [
        'filename' => basename($this->cer_file),
        'contents' => fopen($this->cer_file, 'r')
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
