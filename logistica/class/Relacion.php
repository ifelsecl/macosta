<?php
class Relacion extends Base {

  static $table = "relaciones";
  static $attributes = array('fecha_emision', 'id_cliente', 'periodo', 'guias');

  function __construct($params = array()) {
    parent::__construct($params);
  }

  function create() {
    $values = array($this->fecha_emision, $this->id_cliente, $this->periodo, $this->guias);
    $sql1 = "INSERT INTO ".self::$table." (".implode(', ', self::$attributes).") VALUES('".implode("', '", $values)."')";
    $sql2 = "SELECT LAST_INSERT_ID()";
    if (! DBManager::execute($sql1) or ! $result = DBManager::execute($sql2)) {
      return false;
    }
    if ($row = mysql_fetch_array($result)) {
      $this->id = $row[0];
      return true;
    }else return false;
  }

  static function all($return_sql = false) {
    $sql = "SELECT * FROM ".self::$table." ORDER BY id DESC";
    if ($return_sql) return $sql;
  }

  static function search($params = array(), $return_sql = false) {
    foreach ($params as $key => $value) $params[$key] = DBManager::escape($value);
    $where = '';
    if (isset($params['fecha_emision']) && !empty($params['fecha_emision']))
      $where .= ' AND r.fecha_emision="'.$params['fecha_emision'].'"';
    if (isset($params['cliente']) && !empty($params['cliente']))
      $where .= ' AND CONCAT_WS(" ", cl.nombre, cl.primer_apellido, cl.segundo_apellido) LIKE "%'.$params['cliente'].'%"';
    $sql = "SELECT r.*, CONCAT_WS(' ', cl.nombre, cl.primer_apellido, cl.segundo_apellido) cliente_nombre_completo
FROM ".self::$table." r, ".Cliente::$table." cl WHERE r.id_cliente=cl.id $where";
    if ($return_sql) return $sql;
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT * FROM ".self::$table." WHERE id='$id'";
    $result = DBManager::execute($sql);
    if (mysql_num_rows($result) == 0) return false;
    return new self( mysql_fetch_assoc($result) );
  }

  function cliente() {
    $cliente = new Cliente;
    return $this->cliente = $cliente->find($this->id_cliente);
  }

  function fecha_emision_corta() {
    return isset($this->fecha_emision_corta) ? $this->fecha_emision_corta : $this->fecha_emision_corta = strftime('%b %d, %Y', strtotime($this->fecha_emision));
  }
}
