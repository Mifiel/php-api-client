<?php
namespace Mifiel;

class User extends BaseObject {
  protected static $resourceName = 'users';

  public static function all() {
    throw new \Exception('Method not allowed', 1);
  }

  public static function find($id) {
    throw new \Exception('Method not allowed', 1);
  }

  public static function delete($id){
    throw new \Exception('Method not allowed', 1);
  }
}
