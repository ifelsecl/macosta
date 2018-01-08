<?php
class Producto extends Base {

  static $attributes = array('id', 'nombre', 'tipo');

  static $tipos = array('CARGA NORMAL', 'CARGA PELIGROSA', 'DESECHOS PELIGROSOS');

  static $table = 'productos';

  static function all($which = 'todos', $return_sql = false) {
    $where = $which == 'todos' ? '' : "WHERE activo = 'si'";
    $sql = "SELECT * FROM ".self::$table." $where";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT * FROM ".self::$table." WHERE id='$id'";
    $result = DBManager::execute($sql);
    if (DBManager::rows_count($result) == 0) return false;
    return new self(mysql_fetch_assoc($result));
  }

  static function search($params, $return_sql = false) {
    $params['termino'] = DBManager::escape($params['termino']);
    if ($params['opcion'] == 'nombre') {
      $filtro = "nombre LIKE '%".$params['termino']."%'";
    }else{
      $filtro = "id LIKE '%".$params['termino']."%'";
    }
    $sql = "SELECT * FROM ".self::$table." WHERE $filtro ORDER BY id ASC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  function update($params) {
    self::check_mass_assignment($params, self::$attributes);
    self::__construct($params);
    $sql = "UPDATE " . self::$table . " SET
nombre='$this->nombre', activo='$this->activo', tipo='$this->tipo'
WHERE id=$this->id";
    return DBManager::execute($sql);
  }

  static function create($params) {
    if (empty($params)) return false;
    self::check_mass_assignment($params, self::$attributes);
    foreach ($params as $key => $value) {
      $params[$key] = DBManager::escape($value);
    }
    $sql = 'INSERT INTO '.self::$table.' ('.implode(',', array_keys($params)).')
VALUES("'.implode('","', array_values($params)).'")';
    if (! DBManager::execute($sql)) return false;
    return new self($params);
  }

  function history() {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE id_modulo = '$this->id' AND modulo = 'Productos' ORDER BY fecha DESC LIMIT 100";
    return $this->history = parent::build_resources($sql);
  }

  /**
   * Selecciona todos los datos de todos los productos, ésta función es usada para exportar.
   * @since Abril 21, 2011
   * @author  Edgar Ortega Ramírez
   */
  function Exportar() {
    $sql = 'SELECT * FROM productos';
    return DBManager::execute($sql);
  }

  /**
   * Anula un producto, no es borrado de la base de datos pero no podrá ser utilizado.
   *
   * @param   int $id el ID del producto.
   * @since Junio 22, 2011
   */
  function Anular($id){
    $fecha=date("Y-m-d H:i:s");
    return DBManager::execute("UPDATE productos SET activo = 'no', fechamodificacion='$fecha' WHERE id='$id'");
  }

  /**
   * Activa un producto que ha sido anulado.
   * @param int $id el ID del producto.
   * @since Junio 22, 2011
   */
  function Activar($id) {
    $fecha=date("Y-m-d H:i:s");
    $query="UPDATE ".self::$table." SET activo='si', fechamodificacion='$fecha' WHERE id=$id";
    return DBManager::execute($query);
  }

  static function autocomplete($term) {
    $term = DBManager::escape($term);
    $sql = "SELECT * FROM ".self::$table." WHERE activo='si'
AND (nombre LIKE '%$term%' OR id LIKE '%$term%') LIMIT 0, 100";
    $productos = parent::build_resources($sql);
    $data = array();
    foreach ($productos as $producto) {
      $nombre = strlen($producto->nombre) > 80 ? substr($producto->nombre, 0, 80) : $producto->nombre;
      $data[] = array(
        "id" => $producto->id,
        "value" => $producto->id.'-'.$nombre
      );
    }
    return json_encode($data);
  }
}
