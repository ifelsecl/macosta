<?php
/**
 * La clase Logger Permite crear un log de las acciones realizadas por un
 * usuario en el sistema.
 *
 * @author  Edgar Ortega Ramírez
 * @since Abril 29, 2011
 * @version 1.0
 */
class Logger {

  /**
   * Registra una acción de los usuarios administrativos en la base de datos.
   *
   * @param   string $ip la IP que realizó la acción, ejemplo: 106.36.54.100.
   * @param   string $accion la acción realizada.
   * @param   string $modulo el modulo relacionado con la acción realizada.
   * @param   date $fecha la fecha cuando se realizó la acción.
   * @param string $idusuario el ID del usuario que realizó la acción.
   * @param   int $id_modulo el ID del objecto afectado en el modulo.
   * @since Abril 29, 2011
   */
  function Log($ip, $accion, $modulo, $fecha, $idusuario, $id_modulo = '', $usuario = '') {
    if(empty($usuario)) $usuario = $_SESSION['nombre'];
    $sql = "INSERT INTO ".LOGS_DATABASE_NAME.".log_logistica (ip, accion, modulo, fecha, idusuario, id_modulo, usuario) VALUES('$ip','$accion','$modulo','$fecha','$idusuario', '$id_modulo', '$usuario')";
    return DBManager::execute($sql);
  }

  /**
   * Registra una acción de los clientes en la base de datos.
   *
   * @param   string $accion la acción realizada.
   * @param   string $modulo el modulo relacionado con la acción realizada.
   * @param string $id_cliente el ID del usuario que realizó la acción.
   * @since Marzo 03, 2012
   * @version 1.0
   */
  function LogCliente($accion, $modulo, $id_cliente, $usuario = 'Cliente') {
    $fecha = date('Y-m-d H:i:s');
    $ip = $_SESSION['ip'];
    $sql = "INSERT INTO ".LOGS_DATABASE_NAME.".log_clientes (ip, accion, modulo, fecha, id_cliente, usuario) VALUES('$ip', '$accion', '$modulo', '$fecha', '$id_cliente', '$usuario')";
    return DBManager::execute($sql);
  }

  static function guia($id, $accion) {
    return self::create($id, 'Guias', $accion);
  }

  static function cliente($id, $accion) {
    return self::create($id, 'Clientes', $accion);
  }

  static function ciudad($id, $accion) {
    return self::create($id, 'Ciudades', $accion);
  }

  static function producto($id, $accion) {
    return self::create($id, 'Productos', $accion);
  }

  static function tercero($id, $accion) {
    return self::create($id, 'Terceros', $accion);
  }

  static function conductor($id, $accion) {
    return self::create($id, 'Conductores', $accion);
  }

  static function factura($id, $accion) {
    return self::create($id, 'Facturacion', $accion);
  }

  static function opciones($accion) {
    return self::create('', 'Opciones', $accion);
  }

  static function usuario($id, $accion) {
    return self::create($id, 'Usuarios', $accion);
  }

  static function vehiculo($placa, $accion) {
    return self::create($placa, 'Vehiculos', $accion);
  }

  static function archivos($accion) {
    return self::create('', 'Archivos', $accion);
  }

  static function ruta_local($id, $accion) {
    return self::create($id, 'RutasLocales', $accion);
  }

  static function precio($id, $accion) {
    return self::create($id, 'ListaPrecios', $accion);
  }

  static function tipo_cobro($id, $accion) {
    return self::create($id, 'TipoCobro', $accion);
  }

  private static function create($id, $modulo, $accion) {
    $usuario    = $_SESSION['nombre'];
    $id_usuario = $_SESSION['userid'];
    $ip         = $_SESSION['ip'];
    $fecha      = date('Y-m-d H:i:s');
    $accion     = addslashes($accion);
    $sql = "INSERT INTO ".LOGS_DATABASE_NAME.".log_logistica (ip, accion, modulo, fecha, idusuario, id_modulo, usuario) VALUES(
'$ip','$accion','$modulo','$fecha','$id_usuario', '$id', '$usuario')";
    return DBManager::execute($sql);
  }
}
