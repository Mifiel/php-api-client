<?php
namespace Mifiel;

class Certificate extends BaseObject {
  protected static $resourceName = 'keys';
  protected $multipart = true;

  public function save() {
    unset($this->values->cer_file);
    if (isset($this->values->file_path)) {
      $this->cer_file = [
        'filename' => basename($this->file_path),
        'contents' => fopen($this->file_path, 'r')
      ];
      unset($this->values->file_path);
    }
    parent::save();
  }

  public static function sat() {
    $response = ApiClient::get(static::$resourceName . '/sat');
    return json_decode($response->getBody());
  }
}
