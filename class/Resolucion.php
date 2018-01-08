<?php
class Resolucion extends Base {

	static $table = 'resoluciones';
	static $attributes = array('id', 'tipo', 'numero', 'inicio', 'fin', 'fecha');
	static $tipos = array('manifiestos', 'facturacion');

	function __construct($params = array()) {
		$this->set_defaults();
		parent::__construct($params);
	}

	static function find($id) {
		$id = DBManager::escape($id);
		$sql = "SELECT * FROM ".self::$table." WHERE id='$id'";
		if (! $attributes = DBManager::select($sql)) return false;
		return new self($attributes);
	}

	static function find_by_tipo_and_numero($tipo, $numero) {
		$sql = "SELECT * FROM ".self::$table." WHERE tipo='$tipo' AND '$numero' BETWEEN inicio AND fin";
		if (! $attributes = DBManager::select($sql)) return false;
		return new self($attributes);
	}

	static function all($return_sql = false) {
		$sql = "SELECT * FROM ".self::$table;
		if ($return_sql) return $sql;
		return parent::build_resources($sql, __CLASS__);
	}

	function rango() {
		return $this->inicio.' - '.$this->fin;
	}

	function delete() {
		$sql = "DELETE FROM ".self::$table." WHERE id='$this->id'";
		return DBManager::execute($sql);
	}
}
