<?php
namespace Mifiel;

class Document extends BaseObject {
  protected static $resourceName = 'documents';
  protected $multipart = true;

  public function save() {
    if ($this->file_path) {
      $this->file = [
        'filename' => basename($this->file_path),
        'contents' => fopen($this->file_path, 'r')
      ];
    }
    parent::save();
  }
}
