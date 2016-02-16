<?php
namespace Mifiel;

class Certificate extends BaseObject {
  protected static $resourceName = 'keys';
  protected $multipart = true;

  public function save() {
    if (isset($this->values->cer_file)) {
      $this->cer_file = [
        'filename' => basename($this->cer_file),
        'contents' => fopen($this->cer_file, 'r')
      ];
    }
    parent::save();
  }
}
