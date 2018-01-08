<?php
class Ayudante extends Base {

  static $attributes = array('id', 'nombre', 'id_ciudad', 'numero_identificacion', 'tipo_identificacion');

  static $table = "ayudantes";

  static $tipos_identificacion = array(
    'C' => 'Cédula',
    'T' => 'Tarjeta de Identidad',
    'E' => 'Cédula Extranjería',
    'P' => 'Pasaporte'
  );

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT c.*, ciu.nombre ciudad FROM ".self::$table." c, ciudades ciu WHERE ciu.id=c.id_ciudad AND c.id='$id'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  static function all($which = 'todos', $return_sql = false) {
    $where = ($which == 'activos') ? "AND a.activo='si'" : '';
    $sql = "SELECT a.*, c.nombre ciudad FROM ".self::$table." a, ciudades c WHERE c.id=a.id_ciudad $where";
    if ($return_sql) return $sql;
    return self::build_resources($sql, __CLASS__);
  }

  /**
   * Agrega un nuevo Ayudante.
   *
   * @param string $tipo_identificacion 'C' o 'T'.
   * @param int $numero_identificacion
   * @param string $nombre
   * @param string $activo 'si' o 'no'.
   * @since Junio 14, 2011
   */
  function Agregar($tipo_identificacion, $numero_identificacion, $nombre, $id_ciudad, $activo='si') {
    $activo = strtolower($activo);
    $nombre = strtoupper($nombre);
    $query = "INSERT INTO ".self::$table. " VALUES
(NULL,'$nombre','$tipo_identificacion','$numero_identificacion', $id_ciudad,'$activo','')";
    if ($activo != 'si' and $activo != 'no'){
      return false;
    } else {
      return DBManager::execute($query);
    }
  }

  /**
   * Edita los datos de un Ayudante.
   *
   * @param int $id
   * @param string $tipo_identificacion
   * @param int $numero_identificacion
   * @param string $nombre
   * @param int $id_ciudad
   */
  function Editar($id, $tipo_identificacion, $numero_identificacion, $nombre, $id_ciudad) {
    $nombre=strtoupper($nombre);
    $query="UPDATE ".self::$table." SET
        nombre='$nombre', tipo_identificacion='$tipo_identificacion',
        numero_identificacion='$numero_identificacion',
        id_ciudad='$id_ciudad'
        WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Anula un Ayudante, sin borrarlo de la base de datos.
   *
   * @param int $id el ID del ayudante.
   * @since Junio 14, 2011
   */
  function Anular($id) {
    $fecha=date("Y-m-d H:i:s");
    $query="UPDATE ".self::$table." SET activo='no', fecha_modificacion='$fecha' WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Activa un ayudante que ha sido anulado.
   * @param int $id el ID del ayudante.
   * @since Junio 21, 2011
   */
  function Activar($id) {
    $query="UPDATE ".self::$table." SET activo='si' WHERE id=$id";
    return DBManager::execute($query);
  }
}
