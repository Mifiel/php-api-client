<?php
namespace Mifiel;

class Certificate extends BaseObject {
  protected static $resourceName = 'keys';

  public function save() {
    if ($this->values->cer_file) {
      $this->values->cer_file = [
        'filename' => 'file.cer',
        'contents' => fopen($this->values->cer_file, 'r')
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
