<?php
class Ruta extends Base {

 	static $table = "rutas";

	static function activas() {
		$sql = "SELECT * FROM ".self::$table." WHERE activa='si'";
		return DBManager::execute($sql);
	}
}
