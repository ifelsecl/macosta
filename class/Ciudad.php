<?php
class Ciudad extends Base  {

  static $table = 'ciudades';
  static $attributes = array('nombre');

  static function sql_base() {
    return "SELECT c.*, d.nombre departamento_nombre FROM ".self::$table." c, ".Departamento::$table." d
WHERE d.id=c.iddepartamento";
  }

  static function all($return_sql = false) {
    $sql = self::sql_base();
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = self::sql_base()." AND c.id = $id";
    $result = DBManager::execute($sql);
    if (DBManager::rows_count($result) == 0) return false;
    return new self(mysql_fetch_assoc($result));
  }

  function departamento() {
    return $this->departamento = Departamento::find($this->iddepartamento);
  }

  static function search($params, $return_sql = false) {
    foreach ($params as $key => $value) $params[$key] = DBManager::escape($value);
    if (! isset($params['nombre']) or empty($params['nombre'])) {
      $f_nombre = '';
    } else {
      $f_nombre = "AND (c.nombre LIKE '%".$params['nombre']."%' OR c.id LIKE '%".$params['nombre']."%')";
    }
    if (! isset($params['municipio']) or empty($params['municipio'])) {
      $f_municipio = '';
    } else {
      $f_municipio = "AND c.municipio LIKE '%".$params['municipio']."%'";
    }
    if (! isset($params['departamento']) or empty($params['departamento'])) {
      $f_departamento = '';
    } else {
      $f_departamento = "AND d.nombre LIKE '%".$params['departamento']."%'";
    }
    $sql = self::sql_base()." $f_nombre $f_municipio $f_departamento ORDER BY c.nombre";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  function destroy() {
    $sql = "DELETE FROM ".self::$table." WHERE id = $this->id";
    return DBManager::execute($sql);
  }

  static function autocomplete($term) {
    $term = DBManager::escape($term);
    $datos = array();
    $sql = "SELECT c.*, d.nombre departamento FROM ".self::$table." c, ".Departamento::$table." d
    WHERE c.iddepartamento=d.id AND (c.nombre LIKE '%$term%' OR c.id LIKE '%$term%') LIMIT 0, 100";
    $result = DBManager::execute($sql);
    while($row = mysql_fetch_assoc($result)){
      if ($row['nombre'] == $row['municipio']) {
        $value = $row['nombre']." - ".$row['departamento'];
      }else{
        $value = $row['nombre']." - ".substr($row['municipio'], 0, 10)."/".substr($row['departamento'], 0, 10);
      }
      $datos[] = array(
        "id" => $row['id'],
        "value" => $value,
        'nombre' => $row['nombre']
      );
    }
    return json_encode($datos);
  }

  static function autocomplete_by_municipio($term) {
    $term = DBManager::escape($term);
    $query = "SELECT DISTINCT(municipio) FROM ".self::$table."
WHERE municipio !='null' AND municipio LIKE '%$term%' LIMIT 0, 50";
    $result = DBManager::execute($query);
    $ciudades = array();
    while ($row = mysql_fetch_assoc($result)) {
      $ciudades[] = array("id" => $row['municipio'], "value" => $row['municipio']);
    }
    return json_encode($ciudades);
  }

  function can_be_destroyed() {
    $sql = "SELECT COUNT(*) as count FROM ".Cliente::$table." WHERE idciudad='$this->id'";
    $clientes = DBManager::count($sql);
    $sql = "SELECT COUNT(*) as count FROM listaprecios WHERE idciudaddestino='$this->id' OR idciudadorigen='$this->id'";
    $precios = DBManager::count($sql);
    return ($clientes == 0 and $precios == 0);
  }

  function create($id, $municipio, $nombre, $id_departamento) {
    $municipio = DBManager::escape(strtoupper($municipio));
    $nombre = DBManager::escape(strtoupper($nombre));
    $sql = "INSERT INTO ".self::$table."(id, municipio, nombre, iddepartamento)
VALUES ('$id', '$municipio', '$nombre', '$id_departamento')";
    return DBManager::execute($sql);
  }
}
