<?php
class Configuracion extends Base {

	static $attributes = array('calKiloVolumen');

	/**
	 * Crea un nuevo objeto 'Configuracion'.
	 */
	function __construct($params = array()) {
		parent::__construct($params);
		$sql = "SELECT * FROM configuracion";
		$result = DBManager::execute($sql);
		while($row = mysql_fetch_assoc($result)) {
			$this->$row['name'] = $row['value'];
		}
	}

	/**
	 * Retorna el valor de la constante Kilo/Volumen.
	 *
	 * @return el valor de la constante Kilo/Volumen.
	 */
	function ObtenerConstanteKiloVolumen() {
		$query = "SELECT value FROM configuracion WHERE name='calKiloVolumen'";
		if ($result = DBManager::execute($query)) {
			while ($row = mysql_fetch_array($result)) {
				 return $row[0];
			}
		}
	}

	static function save($params) {
		foreach ($params as $name => $value) {
			$value = DBManager::escape($value);
			$sql = "INSERT INTO configuracion (name, value) VALUES('$name', '$value') ON DUPLICATE KEY UPDATE value=VALUES(value)";
			DBManager::execute($sql);
		}
		return true;
	}

	/**
	 * Obtiene toda la configuración.
	 *
	 * @return	un array con toda la configuración o false si ocurre un error.
	 * @version	1.2 - Julio 25, 2011
	 * @since	Junio 6, 2011
	 */
	function ObtenerConfiguracion() {
		$query = "SELECT * FROM configuracion";
		$result=DBManager::execute($query);
		while ($row = mysql_fetch_assoc($result)){
			$configuracion[$row['name']] = $row['value'];
		}
		return $configuracion;
	}

	/**
	 * Obtiene si se debe calcular automáticamente el precio de Kilo y Kilo/Vol para las listas de precios.
	 *
	 * @return	true si se debe calcular, false en caso contrario.
	 * @since	Agosto 18, 2011
	 */
	function CalcularAutomaticamenteKiloyKiloVol() {
		$query = "SELECT * FROM configuracion WHERE name='lp_cal_kilo_kilovol'";
		$result = DBManager::execute($query);
		$row = mysql_fetch_array($result);
		return $row['value'] == 'si';
	}
}
