<?php
class Conductor extends Base {

	static $table = "conductores";

	static $attributes = array('tipo_identificacion', 'numero_identificacion', 'nombre', 'primer_apellido', 'segundo_apellido');

	static $tipos_identificacion = array(
		'C' => 'Cédula',
		'E' => 'Cédula Extranjería',
		'U' => 'NUIP'
	);

	static $categorias = array(
		'4' => 'Categoría 4',
		'5' => 'Categoría 5',
		'6' => 'Categoría 6',
		'7' => 'Categoría C1',
		'8' => 'Categoría C2',
		'9' => 'Categoría C3'
	);

	function __construct($params = array()) {
		parent::__construct($params);
		if (isset($this->nombre)) {
			$this->numero_identificacion_completo = number_format($this->numero_identificacion, 0, ',', '.');
			$this->nombre_completo = trim($this->nombre . ' ' . $this->primer_apellido . ' ' .$this->segundo_apellido);
		}
	}

	static function all($which = 'todos', $return_sql = false) {
		if ($which == 'todos') {
			$where = '';
		} else {
			$where = 'AND activo = ';
			if ($which == 'activos') $where .= '"si"';
			elseif ($which == 'anulados') $where .= '"no"';
		}
		$sql = "SELECT co.*, ci.nombre ciudad_nombre
FROM ".self::$table." co, ".Ciudad::$table." ci
WHERE co.idciudad=ci.id $where";
		if ($return_sql) return $sql;
		return parent::build_resources($sql, __CLASS__);
	}

	static function autocomplete($term) {
		$term = DBManager::escape($term);
		$datos = array();
		$sql = "SELECT * FROM ".self::$table."
WHERE activo='si' AND (CONCAT(nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE '%$term%' OR numero_identificacion like '%$term%')";
		$result = DBManager::execute($sql);
		while($row = mysql_fetch_assoc($result)) {
			$datos[] = array(
				"id" => $row['numero_identificacion'],
				"value" => $row['nombre'].' '.$row['primer_apellido'].' '.$row['segundo_apellido']
			);
		}
		return json_encode($datos);
	}

	static function expiring($days = 30) {
		$sql = "SELECT * FROM ".self::$table." WHERE activo='si' AND
vencimientopase <= (SELECT DATE_FORMAT(DATE_ADD(NOW(), INTERVAL $days DAY),  '%Y-%m-%d'))";
		return parent::build_resources($sql, __CLASS__);
	}

	/**
	 * Verifica los documentos del conductor.
	 *
	 * @param int $numero_identificacion
	 * @since	Julio 6, 2011
	 */
	function VerificarDocumentos($numero_identificacion) {
		$query="SELECT * FROM conductores WHERE numero_identificacion='$numero_identificacion'";
		$result=DBManager::execute($query);
		$row=mysql_fetch_assoc($result);
		$vencidos['vencido']='no';
		if ($row['vencimientopase'] < date('Y-m-d')) {
			$vencidos['vencido']='si';
			$vencidos['nombre']=$row['nombre'].' '.$row['primer_apellido'];
			$vencidos['Licencia']=$row['vencimientopase'];
		}
		return $vencidos;
	}

	function find($id) {
		$id = DBManager::escape($id);
		$sql = "SELECT c.*, ci.nombre ciudad_nombre, de.nombre departamento_nombre
FROM ".self::$table." c, ".Ciudad::$table." ci, ".Departamento::$table." de
WHERE ci.id=c.idciudad AND de.id=ci.iddepartamento AND c.numero_identificacion=$id";
		if (! $attributes = DBManager::select($sql)) return false;
		self::__construct($attributes);
		return $this;
	}

	function ciudad() {
		return $this->ciudad = Ciudad::find($this->idciudad);
	}

  function nombre_completo() {
    return trim($this->nombre.' '.$this->primer_apellido.' '.$this->segundo_apellido);
  }

	function create() {
		$this->nombre = strtoupper($this->nombre);
		$this->primer_apellido = strtoupper($this->primer_apellido);
		$this->segundo_apellido = strtoupper($this->segundo_apellido);
		$this->direccion = strtoupper($this->direccion);
		$sql1 = "INSERT INTO ".self::$table."(tipo_identificacion, numero_identificacion, nombre,
primer_apellido, segundo_apellido, idciudad, categorialicencia, direccion, telefono, vencimientopase, activo,
celular) VALUES(
'$this->tipo_identificacion',$this->numero_identificacion,'$this->nombre','$this->primer_apellido',
'$this->segundo_apellido','$this->idciudad', '$this->categorialicencia','$this->direccion',$this->telefono,
'$this->vencimientopase', 'si', '$this->celular')";
		$sql2 = "SELECT LAST_INSERT_ID()";
		if (! DBManager::execute($sql1) or ! $result2 = DBManager::execute($sql2)) {
			return false;
		}
		if ($row = mysql_fetch_array($result2)) {
			$this->id = $row[0];
			return true;
		}else return false;
	}

	function update($params) {
		$search = array('<', '>');
		foreach ($params as $key => $value) $params[$key] = DBManager::escape(str_replace($search, '', $value));
		self::__construct($params);
		$this->nombre = strtoupper($this->nombre);
		$this->primer_apellido = strtoupper($this->primer_apellido);
		$this->segundo_apellido = strtoupper($this->segundo_apellido);
		$this->direccion = strtoupper($this->direccion);
		$sql = "UPDATE ".self::$table." SET tipo_identificacion='$this->tipo_identificacion',
numero_identificacion='$this->numero_identificacion', nombre='$this->nombre',
primer_apellido='$this->primer_apellido', segundo_apellido='$this->segundo_apellido',
idciudad='$this->idciudad', categorialicencia='$this->categorialicencia',
direccion='$this->direccion', telefono='$this->telefono', vencimientopase='$this->vencimientopase',
celular='$this->celular' WHERE numero_identificacion='$this->numero_identificacion'";
		return DBManager::execute($sql);
	}

	function history() {
		$sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE id_modulo = '$this->numero_identificacion' AND modulo = 'Conductores' ORDER BY fecha DESC LIMIT 100";
		$this->history = array();
		$result = DBManager::execute($sql);
		while ($h = mysql_fetch_object($result)) {
			$this->history[] = $h;
		}
		return $this->history;
	}

	function activate($id = null) {
		$id = $id ?: $this->numero_identificacion;
		$sql = "UPDATE ".self::$table." SET activo='si' WHERE numero_identificacion='$id'";
		return DBManager::execute($sql);
	}

	function deactivate($id = null) {
		$id = $id ?: $this->numero_identificacion;
		$sql = "UPDATE ".self::$table." SET activo='no' WHERE numero_identificacion='$id'";
		return DBManager::execute($sql);
	}
}
