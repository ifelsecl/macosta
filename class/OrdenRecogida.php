<?php
class OrdenRecogida extends Base {

  static $attributes = array('id', 'id_ruta', 'id_ciudad', 'numero_identificacion_conductor', 'placa_vehiculo');

  static $table = "ordenesrecogida";

  function __construct($params = array()) {
    parent::__construct($params);
    if(isset($this->fecha)) {
      $this->fecha_larga = utf8_encode(strftime("%d/%B/%Y", strtotime($this->fecha)));
      $this->fecha_corta = strftime('%b %d, %Y', strtotime($this->fecha));
    }
  }

  function all($which = 'todos', $return_sql = false) {
    $where = $which == 'activas' ? "AND o.activa='si'" : '';
    $sql = "SELECT o.*, r.nombre ruta FROM ".self::$table." o, rutas r
WHERE r.id=o.id_ruta $where ORDER BY o.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT o.*, r.nombre ruta, ci.nombre ciudad_nombre
FROM ".self::$table." o, ".Ciudad::$table." ci, rutas r WHERE ci.id=o.id_ciudad
AND o.id_ruta = r.id AND o.id='$id'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self( $attributes );
  }

  function ciudad() {
    return $this->ciudad = Ciudad::find($this->id_ciudad);
  }

  function conductor() {
    if (isset($this->conductor)) return $this->conductor;
    $conductor = new Conductor;
    return $this->conductor = $conductor->find($this->numero_identificacion_conductor);
  }

  function vehiculo() {
    return isset($this->vehiculo) ? $this->vehiculo : $this->vehiculo = Vehiculo::find($this->placa_vehiculo);
  }

  function ayudantes() {
    $ayudante = new Ayudante;
    if (empty($this->ayudantes)) {
      return $this->ayudantes = array();
    }
    $ayudantes = explode(";", $this->ayudantes);
    $this->ayudantes = array();
    foreach ($ayudantes as $a) {
      $this->ayudantes[$a] = Ayudante::find($a);
    }
    return $this->ayudantes;
  }

  /**
   * Anula una orden de recogida, no es eliminada de la base de datos.
   * @param int $id el ID de la orden.
   * @since Abril 21, 2011
   * @author  Edgar Ortega Ramirez
   * @version 1.1 - Junio 23, 2011
   */
  function Anular($id) {
    $fecha=date("Y-m-d H:i:s");
    $query="UPDATE ".self::$table." SET activa='no', fecha_modificacion='$fecha' WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Agrega una nueva orden de recogida.
   *
   * @param   int $id_ciudad
   * @param   date $fecha
   * @param   string $placa_vehiculo
   * @param   int $numero_identificacion_conductor
   * @param   string $ayudantes el ID de los ayudantes separadas por punto y coma (;).
   * @param   unknown_type $id_ruta
   * @since Junio 23, 2011
   */
  function Agregar($id_ciudad, $fecha, $placa_vehiculo, $numero_identificacion_conductor, $ayudantes, $id_ruta, $clientes) {
    $query="INSERT INTO ".self::$table."(id_ciudad, fecha, placa_vehiculo, numero_identificacion_conductor,
        ayudantes, id_ruta, clientes) VALUES($id_ciudad,'$fecha','$placa_vehiculo','$numero_identificacion_conductor',
        '$ayudantes','$id_ruta', '$clientes')";
    return DBManager::execute($query);
  }

  /**
   * Activa una orden de recogida que ha sido anulada.
   * @param int $id el ID de la orden.
   * @since Junio 23, 2011
   */
  function Activar($id) {
    $fecha=date("Y-m-d H:i:s");
    $query="UPDATE ".self::$table." SET activa='si', fecha_modificacion='$fecha' WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Edita los datos de una orden de recogida.
   *
   * @param   int $id
   * @param   int(8) $id_ciudad
   * @param   date $fecha
   * @param   string(6) $placa_vehiculo
   * @param   int $numero_identificacion_conductor
   * @param   string $ayudantes
   * @param   int $id_ruta
   * @since Junio 23, 2011
   */
  function Editar($id, $id_ciudad, $fecha, $placa_vehiculo, $numero_identificacion_conductor, $ayudantes, $id_ruta, $clientes) {
    $fecha=date("Y-m-d H:i:s");
    $query="UPDATE ".self::$table." SET id_ciudad=$id_ciudad, fecha='$fecha', placa_vehiculo='$placa_vehiculo',
        numero_identificacion_conductor='$numero_identificacion_conductor', ayudantes='$ayudantes',
        id_ruta='$id_ruta', fecha_modificacion='$fecha', clientes='$clientes' WHERE id=$id";
    return DBManager::execute($query);
  }
}
