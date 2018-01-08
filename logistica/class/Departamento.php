<?php
class Departamento extends Base {

  static $table = 'departamentos';

  static function all() {
    $sql = "SELECT * FROM ".self::$table;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT * FROM ".self::$table." WHERE id = $id";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }
}
