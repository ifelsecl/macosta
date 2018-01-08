<?php
class Contacto extends Base {

  static $tipos_identificacion = array(
    'C' => 'Cédula',
    'N' => 'NIT',
    'T' => 'Tarjeta de Identidad',
    'E' => 'Cédula Extranjería',
    'P' => 'Pasaporte'
  );

  static $table = 'clientes';

  function __construct($params = array()) {
    parent::__construct($params);
    if (isset($this->tipo_identificacion)) {
      $this->nombre_completo = trim($this->nombre.' '.$this->primer_apellido.' '.$this->segundo_apellido);
      $this->numero_identificacion_completo = number_format($this->numero_identificacion, 0, ',', '.');
      if ($this->tipo_identificacion == 'N') {
        $this->primer_apellido = '';
        $this->segundo_apellido = '';
        $this->numero_identificacion_completo .= '-'.$this->digito_verificacion;
      } else {
        $this->id_regimen = 0;
        $this->id_forma_juridica = 0;
        $this->digito_verificacion = 0;
      }
    }
  }

  static function limit($n, $m, $return_sql = false) {
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  function all($which = 'todos', $return_sql = false) {
    $where = '';
    if ($which == 'activos') {
      $where = "WHERE activo='no'";
    }
    $sql = "SELECT * FROM ".self::$table." $where";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  function search($params, $sql = false) {
    $params['termino'] = addslashes($params['termino']);
    $params['opcion'] = addslashes($params['opcion']);
    if ($params['opcion'] == 'nombre') {
      $filtro = "(CONCAT(co.nombre, ' ', co.primer_apellido, ' ', co.segundo_apellido) LIKE '%".$params['termino']."%')";
    }elseif ($params['opcion'] == 'numero_identificacion') {
      $filtro = "co.numero_identificacion LIKE '%".$params['termino']."%'";
    }elseif ($params['opcion'] == 'ciudad') {
      $filtro = "ci.nombre LIKE '%".$params['termino']."%'";
    } else {
      $filtro = "co.direccion LIKE '%".$params['termino']."%'";
    }
    $sql = "SELECT co.* FROM ".self::$table." co, ciudades ci WHERE co.idciudad=ci.id AND $filtro";
    if ($sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT * FROM ".self::$table." WHERE id='$id'";
    $result = DBManager::execute($sql);
    if (DBManager::rows_count($result) == 0) return false;
    self::__construct( mysql_fetch_assoc($result) );
    return $this;
  }

  function ciudad() {
    return $this->ciudad = Ciudad::find($this->idciudad);
  }

  function add_cliente($id_cliente) {
    $sql = "INSERT INTO clientes_contactos VALUES($id_cliente, $this->id)";
    return DBManager::execute($sql);
  }

  static function validar_tipo_identificacion() {
    echo "
$('#tipo_identificacion').change(function() {
  if ($('#tipo_identificacion').val()=='N') {
    $('.empresa input, .empresa select').removeAttr('disabled');
    $('.empresa').fadeIn(400);
    $('.persona input').attr('disabled','true');
    $('.persona').fadeOut(400);
  } else {
    $('.persona').fadeIn(400);
    $('.empresa').fadeOut(400);
    $('.empresa input, .empresa select').attr('disabled','true');
    $('.persona input').removeAttr('disabled');
  }
  $('#numero_identificacion').focus();
}).change();";
  }

  function clientes() {
    $sql = "SELECT c.* FROM clientes c, clientes_contactos cc WHERE c.id=cc.id_cliente AND cc.id_contacto = $this->id LIMIT 100";
    $this->clientes = array();
    $result = DBManager::execute($sql);
    while ($c = mysql_fetch_object($result, 'Cliente')) {
      $this->clientes[] = $c;
    }
    return $this->clientes;
  }

  function history() {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE id_modulo = '$this->id' AND modulo = 'Contactos' ORDER BY fecha DESC LIMIT 100";
    $this->history = array();
    $result = DBManager::execute($sql);
    while ($h = mysql_fetch_object($result)) {
      $this->history[] = $h;
    }
    return $this->history;
  }

  function activate($id = null) {
    $id = $id ?: $this->id;
    $sql = "UPDATE ".self::$table." SET activo = 'si' WHERE id=$id";
    return DBManager::execute($sql);
  }

  function deactivate($id = null) {
    $id = $id ?: $this->id;
    $sql = "UPDATE ".self::$table." SET activo = 'no' WHERE id=$id";
    return DBManager::execute($sql);
  }

  function destroy($id_new_contacto, $id = null) {
    $id = $id ?: $this->id;
    $sql = "DELETE FROM clientes_contactos WHERE id_contacto=$this->id";
    if (! DBManager::execute($sql) ) return FALSE;

    $sql = "UPDATE guias SET idcontacto = $id_new_contacto WHERE idcontacto=$this->id";
    if (! DBManager::execute($sql) ) return FALSE;

    $sql = "DELETE FROM ".self::$table." WHERE id = $this->id";
    return DBManager::execute($sql);
  }

  function create() {
    $this->email      = strtolower($this->email);
    $this->nombre       = strtoupper($this->nombre);
    $this->primer_apellido  = strtoupper($this->primer_apellido);
    $this->segundo_apellido = strtoupper($this->segundo_apellido);
    $this->direccion    = strtoupper($this->direccion);
    $sql1 = "INSERT INTO ".self::$table." (tipo_identificacion,
numero_identificacion, nombre, primer_apellido, segundo_apellido, direccion,
idciudad, telefono, email, sitio_web, digito_verificacion, activo)
VALUES('$this->tipo_identificacion','$this->numero_identificacion',
'$this->nombre', '$this->primer_apellido', '$this->segundo_apellido',
'$this->direccion', $this->idciudad,'$this->telefono', '$this->email',
'$this->sitio_web', '$this->digito_verificacion','si')";
    $sql2 = "SELECT LAST_INSERT_ID()";
    if (! DBManager::execute($sql1) or (! $result2 = DBManager::execute($sql2))) {
      return false;
    }
    if ($row = mysql_fetch_array($result2)) {
      $this->id = $row[0];
      return true;
    } else {
      return false;
    }
  }

  function update($params) {
    self::__construct($params);
    $this->email      = strtolower($this->email);
    $this->nombre       = strtoupper($this->nombre);
    $this->primer_apellido  = strtoupper($this->primer_apellido);
    $this->segundo_apellido = strtoupper($this->segundo_apellido);
    $this->direccion    = strtoupper($this->direccion);
    $sql = "UPDATE ".self::$table." SET
tipo_identificacion='$this->tipo_identificacion',
numero_identificacion='$this->numero_identificacion', nombre='$this->nombre',
primer_apellido='$this->primer_apellido', segundo_apellido='$this->segundo_apellido',
direccion='$this->direccion', idciudad='$this->idciudad',
telefono='$this->telefono', email='$this->email', sitioweb='$this->sitioweb',
digito_verificacion='$this->digito_verificacion'
WHERE id=$this->id";
    return DBManager::execute($sql);
  }

  static function autocomplete($term) {
    $datos = array();
    $term = explode(',', htmlspecialchars(addslashes($term)) );
    $nombre = $term[0];
    $ciudad = isset($term[1]) ? "AND ci.nombre LIKE '%".trim($term[1])."%'" : '';
    $sql = "SELECT c.*, ci.nombre ciudad
FROM ".self::$table." c, ".Ciudad::$table." ci
WHERE c.idciudad=ci.id AND c.activo='si' AND
(CONCAT(c.nombre, ' ', c.primer_apellido, ' ', c.segundo_apellido) LIKE '%$nombre%'
OR c.id like '%$nombre%' OR c.numero_identificacion LIKE '%$nombre%'
OR c.telefono LIKE '%$nombre%' OR c.direccion LIKE '%$nombre%')
$ciudad LIMIT 0, 100";
    $result = DBManager::execute($sql);
    while ($row = mysql_fetch_assoc($result)) {
      $nombre = trim($row['nombre'].' '.$row['primer_apellido'].' '.$row['segundo_apellido']);
      $datos[] = array(
        "id" => $row['id'],
        "value" => $nombre.' - '.$row['ciudad'].' - '.$row['direccion'],
        'id_ciudad' => $row['idciudad'],
        'direccion' => $row['direccion'],
        'telefono' => $row['telefono'],
        'ciudad' => $row['ciudad'],
        'nombre' => $nombre,
        'numero_identificacion' => $row['numero_identificacion']
      );
    }
    return json_encode($datos);
  }

}
