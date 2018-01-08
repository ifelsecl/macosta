<?php
class Talonario extends Base {

  static $attributes = array('id', 'conductor_numero_identificacion', 'fecha_entrega', 'inicio', 'fin');
  static $table = 'talonarios';

  static function sql_base() {
    return "SELECT t.*, CONCAT_WS(' ', co.nombre, co.primer_apellido, co.segundo_apellido)
conductor_nombre_completo FROM ".self::$table." t, ".Conductor::$table." co";
  }

  function __construct($params = array()) {
    $this->set_defaults();
    parent::__construct($params);
    if (isset($this->inicio) and isset($this->fin)) {
      $this->rango = range($this->inicio, $this->fin);
    }
    if (isset($this->conductor_nombre_completo)) {
      $conductor_params = array(
        "numero_identificacion" => $this->conductor_numero_identificacion,
        "nombre_completo" => $this->conductor_nombre_completo
      );
      $this->conductor = new Conductor($conductor_params);
    }
    if (isset($this->fecha_entrega)) {
      $this->fecha_corta = strftime('%b %d, %Y', strtotime($this->fecha_entrega));
      $this->fecha_larga = strftime("%d de %B de %Y", strtotime($this->fecha_entrega));
    }
  }

  static function all($return_sql = false) {
    $sql = self::sql_base()." WHERE co.numero_identificacion=t.conductor_numero_identificacion
ORDER BY t.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function search($params, $return_sql = false) {
    foreach ($params as $key => $value) $params[$key] = DBManager::escape($value);
    $f_numero = '';
    if (isset($params['numero']) and ! empty($params['numero'])) {
      $numero = intval($params['numero']);
      $f_numero = " AND ".$numero." BETWEEN t.inicio AND t.fin";
    }
    $sql = self::sql_base()." WHERE co.numero_identificacion=t.conductor_numero_identificacion $f_numero
ORDER BY t.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = self::sql_base()." WHERE id='$id'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  static function create($params) {
    self::check_mass_assignment($params, self::$attributes);
    $columns = implode(',' , array_keys($params));
    $values = '"'.implode('","' , array_values($params)).'"';
    $sql = "INSERT INTO ".self::$table."($columns) VALUES($values)";
    return DBManager::execute($sql);
  }

  function delete() {
    $sql = "DELETE FROM ".self::$table." WHERE id=$this->id";
    return DBManager::execute($sql);
  }

  function conductor() {
    $conductor = new Conductor;
    $this->conductor = $conductor->find($this->conductor_numero_identificacion);
  }

  function guias() {
    $sql = "SELECT numero, id FROM ".Guia::$table."
WHERE numero BETWEEN $this->inicio AND $this->fin ORDER BY numero";
    $this->guias = array();
    $guias_en_sistema = array();
    $result = DBManager::execute($sql);
    while ($guia = mysql_fetch_object($result)) {
      $this->guias[] = $guia;
      $guias_en_sistema[] = $guia->numero;
    }
    $this->numeros_pendientes = array_diff($this->rango, $guias_en_sistema);
    return $this->guias;
  }
}
