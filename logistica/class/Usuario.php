<?php
class Usuario extends Base{

  static $attributes = array('id', 'nombre', 'usuario', 'activo', 'email', 'ultimo_acceso', 'cedula', 'idperfil', 'permisos', 'clave');

  static $table = 'usuarios';

  static $profiles = array(
    1 => 'Administrador',
    2 => 'Digitador',
    3 => 'Avanzado',
    4 => 'Gerente Junta'
  );

  static $modules = array(
    'Clientes', 'Facturacion', 'Lista_Precios', 'Vendedores',
    'Productos', 'Guias', 'Conductores', 'Camiones', 'Planillas',
    'Terceros', 'Ordenes_Recogida', 'Rutas_Locales', 'Talonarios', 'Ayudantes', 'Usuarios',
    'Embalajes', 'Ciudades', 'Preguntas', 'Archivos', 'Opciones', 'Resoluciones', 'Manten'
  );

  static function all($cuales = 'todos', $return_sql = false) {
    if ($cuales == 'todos') $where = '1';
    elseif ($cuales == 'activos') $where = "activo = 'si'";
    else $where = "activo = 'no'";
    $sql = "SELECT * FROM ".self::$table." WHERE $where";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function search($param, $return_sql = false) {
    $param = DBManager::escape($param);
    $sql = "SELECT * FROM ".self::$table." WHERE (nombre LIKE '%$param%' OR email LIKE '%$param%' OR usuario LIKE '%$param%')";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT * FROM ".self::$table." WHERE id='$id'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  static function find_by_usuario($usuario) {
    $usuario = DBManager::escape($usuario);
    $sql = "SELECT * FROM ".self::$table." WHERE usuario='$usuario'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  function perfil() {
    return self::$profiles[$this->idperfil];
  }

  function history($return_sql = false) {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE idusuario='$this->id' ORDER BY id DESC";
    if ($return_sql) return $sql;
    return $this->history = parent::build_resources($sql);
  }

  function update_last_login_date() {
    $fecha = date("Y-m-d H:i:s");
    $sql = "UPDATE ".self::$table." SET ultimo_acceso='$fecha' WHERE id='$this->id'";
    DBManager::execute($sql);
  }

  /**
   * Selecciona todos los datos de todos los usuarios, ésta función es usada para exportar.
   * @param string $formato "XLS" o "CSV", el formato que será generado, de este formato
   *      dependen los datos que la consulta retorne.
   * @since Abril 23, 2011
   * @author  Edgar Ortega Ramírez
   */
  function Exportar($formato) {
    if (strtoupper($formato)=="XLS") {
      $query="SELECT u.*,p.nombre perfil FROM ".self::$table." u, perfiles p WHERE u.idperfil=p.id";
    }
    if (strtoupper($formato)=="CSV") {
      $query="SELECT * FROM ".self::$table."";
    }
    return DBManager::execute($query);
  }

  /**
   * Agrega un usuario.
   *
   * @param string $nombre
   * @param string $cedula
   * @param string $usuario
   * @param string $clave no debe estar encriptada.
   * @param string $email
   * @param int $idperfil
   * @param string $permisos los permisos asociados al usuario, Crear, Ver, Editar, Eliminar, etc.
   * @since Abril 25, 2011
   * @version 1.3 - Julio 22, 2011
   */
  function Agregar($nombre,$cedula,$usuario,$clave,$email,$idperfil, $permisos) {
    $fechacreacion = date("Y-m-d");
    $clave = md5($clave);
    $query = "INSERT INTO ".self::$table."(fechacreacion, usuario, clave, nombre, cedula, email, idperfil, permisos)
VALUES('$fechacreacion','$usuario','$clave','$nombre','$cedula','$email',$idperfil,'$permisos')";
    return DBManager::execute($query);
  }

  /**
   * Comprueba si existe un usuario.
   *
   * @param string $usuario el nombre de usuario.
   * @since Abril 25, 2011
   * @author  Edgar Ortega Ramírez
   */
  function Existe($usuario) {
    $usuario = DBManager::escape($usuario);
    $sql = "SELECT count(*) count FROM ".self::$table." WHERE usuario='$usuario'";
    return DBManager::count($sql) > 0;
  }

  function activate() {
    return $this->update_attributes(array('activo' => 'si'));
  }

  function deactivate() {
    return $this->update_attributes(array('activo' => 'no'));
  }

  /**
   * Actualiza los datos de un usuario.
   * @param   int $id
   * @param   string $nombre
   * @param   string $clave la nueva contraseña o <code>null</code> si no quieres cambiarla.
   * @param   string $email
   * @param   int $idperfil
   * @param string $permisos los permisos asignados al usuario.
   * @since Abril 26, 2011
   * @author  Edgar Ortega Ramirez
   */
  function Actualizar($id,$nombre,$cedula,$clave,$email,$idperfil, $permisos) {
    if (! isset($clave) or empty($clave)) {
      $edit_password = '';
    } else {
      $clave = md5($clave);
      $edit_password = ", clave='$clave'";
    }
    $query = "UPDATE ".self::$table." SET nombre='$nombre', cedula='$cedula', email='$email',
idperfil=$idperfil, permisos='$permisos' $edit_password WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Retorna un arreglo con los permisos asignados a un usuario.
   *
   * @param   int $idusuario el ID del usuario.
   * @return  un <code>array</code> con los permisos.
   * @since Mayo 20, 2011
   * @author  Edgar Ortega Ramírez
   */
  function format_permissions() {
    if (empty($this->permisos)) return array();
    $permisos = explode(";", $this->permisos);
    foreach ($permisos as $key => $value) {
      $permisos[$value] = "si";
    }
    return $permisos;
  }

  static function ObtenerPermisosPorModulo($modulo) {
    $q = "SELECT * FROM tareas WHERE modulo='$modulo'";
    return DBManager::execute($q);
  }

  static function online() {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".online";
    return parent::build_resources($sql);
  }
}
