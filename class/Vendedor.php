<?php
class Vendedor extends Base{

  static $table = 'vendedores';
  static $attributes = array('nombre', 'cedula', 'id_ciudad', 'direccion', 'telefonos');

  static function all($return_sql = false) {
    $sql = "SELECT v.*, c.nombre ciudad FROM ".self::$table." v, ".Ciudad::$table." c
WHERE v.id_ciudad=c.id";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT v.*, c.nombre ciudad FROM ".self::$table." v, ciudades c
WHERE v.id_ciudad=c.id AND v.id='$id'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  /**
   * Anula un vendedor.
   *
   * @param   int $id el ID del vendedor.
   * @since Marzo 29, 2011
   * @author  Edgar Ortega Ramírez
   */
  function Anular($id) {
    $hoy = date('Y-m-d');
    $query = "UPDATE vendedores SET activo = 'no' WHERE id = '$id'";
    return DBManager::execute($query);
  }

  /**
   * Crea un nuevo Vendedor.
   * @since Octubre 3, 2011
   */
  function Crear($nombre, $cedula, $id_ciudad, $direccion, $telefono, $email, $codigo_siigo) {
    $query="INSERT INTO vendedores(nombre, cedula, id_ciudad, direccion, telefono, email, codigo_siigo)
VALUES('$nombre', '$cedula', '$id_ciudad', '$direccion', '$telefono', '$email', '$codigo_siigo')";
    return DBManager::execute($query);
  }

  /**
   * Edita un Vendedor.
   *
   * @since Noviembre 21, 2011
   */
  function Editar($id, $nombre, $cedula, $id_ciudad, $direccion, $telefono, $email, $codigo_siigo) {
    $nombre=addslashes($nombre);
    $cedula=addslashes($cedula);
    $direccion=addslashes($direccion);
    $telefono=addslashes($telefono);
    $email=addslashes($email);
    $query="UPDATE vendedores SET nombre='$nombre', cedula='$cedula',
id_ciudad='$id_ciudad', direccion='$direccion', telefono='$telefono',
email='$email', codigo_siigo='$codigo_siigo' WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Selecciona los clientes asociados a un Vendedor.
   *
   * @since Noviembre 21, 2011
   */
  function ObtenerClientes($id_vendedor, $modo=NULL) {
    $q="SELECT c.*, ciu.nombre ciudad, SUM(f.total) total
FROM clientes c, ciudades ciu, facturas f
WHERE c.idciudad=ciu.id AND f.idcliente=c.id AND c.id_vendedor=$id_vendedor
GROUP BY c.id ORDER BY c.id";
    if (! isset($modo)) {
      return DBManager::execute($q);
    } else {
      return $q;
    }
  }

  /**
   * Activa un vendedor.
   *
   * @param   int $id el ID del vendedor.
   * @since Diciembre 31, 2011
   * @author  Edgar Ortega Ramírez
   */
  function Activar($id) {
    $query="UPDATE vendedores SET activo = 'si' WHERE id = '$id'";
    return DBManager::execute($query);
  }
}
