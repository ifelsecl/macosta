<?php
class Aseguradora extends Base {

	static $table = 'aseguradoras';

	static function autocomplete($term) {
		$term = DBManager::escape($term);
		$sql = "SELECT * FROM ".self::$table." WHERE
nombre LIKE '%$term%' OR nit LIKE '%$term%'";
		$aseguradoras = array();
		$result = DBManager::execute($sql);
		while ($row = mysql_fetch_assoc($result)) {
			$aseguradoras[] = array("id" => $row['nit'], "value" => $row['nombre']);
		}
		return json_encode($aseguradoras);
	}
}

                            