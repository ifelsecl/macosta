<?php
class Mantenimiento extends Base {
  static $table = 'mantenimientos';
  static $attributes = array('id', 'nombre', 'kilometraje');

  function __construct($params = array()) {
    $this->set_defaults();
    parent::__construct($params);
  }

  static function find($id) {
    $sql = "SELECT * FROM ".self::$table." WHERE id='$id'";
    if (! $attrs = DBManager::select($sql)) return false;
    return new self($attrs);
  }

  static function all($return_sql = false) {
    $sql = "SELECT * FROM ".self::$table." ORDER BY nombre";
    return parent::build_resources($sql, __CLASS__);
  }

  static function create($params) {
    return parent::_create($params);
  }
}
