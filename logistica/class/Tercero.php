<?php
class Tercero extends Base {
  static $table = 'terceros';
  static $attributes = array('id', 'tipo_identificacion', 'numero_identificacion', 'nombre', 'primer_apellido', 'segundo_apellido', 'id_ciudad', 'direccion', 'telefono', 'razon_social', 'digito_verificacion', 'email', 'celular');

  static $tipos_identificacion = array(
    'C' => 'Cédula',
    'N' => 'NIT',
    'T' => 'Tarjeta de Identidad',
    'E' => 'Cédula Extranjería',
    'P' => 'Pasaporte'
  );

  function __construct($params = array()) {
    parent::__construct($params);
    if (isset($this->tipo_identificacion)) {
      $this->numero_identificacion_completo = number_format($this->numero_identificacion, 0, ',', '.');
      if ($this->tipo_identificacion == 'N') {
        $this->nombre = '';
        $this->primer_apellido = '';
        $this->segundo_apellido = '';
        $this->nombre_completo = $this->razon_social;
        $this->numero_identificacion_completo .= '-'.$this->digito_verificacion;
      } else {
        $this->nombre_completo = trim($this->nombre.' '.$this->primer_apellido.' '.$this->segundo_apellido);
        $this->razon_social = '';
        $this->digito_verificacion = 0;
      }
    }
  }

  function create() {
    $this->email = strtolower($this->email);
    $this->nombre = strtoupper($this->nombre);
    $this->primer_apellido = strtoupper($this->primer_apellido);
    $this->segundo_apellido = strtoupper($this->segundo_apellido);
    $this->razon_social = strtoupper($this->razon_social);
    $this->direccion = strtoupper($this->direccion);
    $sql1 = "INSERT INTO ".self::$table."(tipo_identificacion, numero_identificacion, nombre,
primer_apellido, segundo_apellido, id_ciudad, direccion, telefono, razon_social, digito_verificacion, email, celular) VALUES(
'$this->tipo_identificacion','$this->numero_identificacion','$this->nombre','$this->primer_apellido',
'$this->segundo_apellido','$this->id_ciudad','$this->direccion','$this->telefono',
'$this->razon_social', '$this->digito_verificacion', '$this->email', '$this->celular')";
    $sql2 = "SELECT LAST_INSERT_ID()";
    if ( ! DBManager::execute($sql1) or ! $result2 = DBManager::execute($sql2) ) return false;
    if ( $row = mysql_fetch_array($result2) ) {
      $this->id = $row[0];
      return true;
    }else return false;
  }

  function update($params) {
    $search = array('<', '>');
    foreach ($params as $key => $value) $params[$key] = addslashes(str_replace($search, '', $value));
    self::__construct($params);
    $this->email = strtolower($this->email);
    $this->nombre = strtoupper($this->nombre);
    $this->primer_apellido = strtoupper($this->primer_apellido);
    $this->segundo_apellido = strtoupper($this->segundo_apellido);
    $this->razon_social = strtoupper($this->razon_social);
    $this->direccion = strtoupper($this->direccion);
    $sql = "UPDATE ".self::$table." SET tipo_identificacion='$this->tipo_identificacion',
numero_identificacion='$this->numero_identificacion', nombre='$this->nombre',
primer_apellido='$this->primer_apellido', segundo_apellido='$this->segundo_apellido',
id_ciudad='$this->id_ciudad', direccion='$this->direccion', telefono='$this->telefono',
razon_social='$this->razon_social', digito_verificacion='$this->digito_verificacion',
email='$this->email', celular='$this->celular'
WHERE id = '$this->id'";
    return DBManager::execute($sql);
  }

  function deactivate() {
    $query = "UPDATE ".self::$table." SET activo='no' WHERE id=$this->id";
    return DBManager::execute($query);
  }

  function activate() {
    $query = "UPDATE ".self::$table." SET activo='si' WHERE id=$this->id";
    return DBManager::execute($query);
  }

  function all($which = 'todos', $return_sql = false) {
    $where = '';
    if ($which == 'activos') $where = "AND activo='si'";
    $sql = "SELECT t.*, c.nombre ciudad_nombre FROM ".self::$table." t, ".Ciudad::$table." c WHERE c.id=t.id_ciudad $where";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  function history() {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE id_modulo = '$this->id' AND modulo = 'Terceros' ORDER BY fecha DESC LIMIT 30";
    return $this->history = parent::build_resources($sql);
  }

  function ciudad() {
    return isset($this->ciudad) ? $this->ciudad : $this->ciudad = Ciudad::find($this->id_ciudad);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT t.*, c.nombre ciudad_nombre, d.nombre departamento_nombre
FROM ".self::$table." t, ".Ciudad::$table." c, ".Departamento::$table." d
WHERE t.id_ciudad=c.id AND d.id=c.iddepartamento AND t.id='$id'";
    return parent::find_record($sql);
  }

  static function find_by_numero_identificacion($numero_identificacion) {
    $numero_identificacion = DBManager::escape($numero_identificacion);
    $sql = "SELECT * FROM ".self::$table." WHERE numero_identificacion='$numero_identificacion'";
    return parent::find_record($sql);
  }

  static function validar_tipo_identificacion() {
    echo "
$('#tipo_identificacion').change(function() {
  if ($('#tipo_identificacion').val() == 'N') {
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

  static function autocomplete($term) {
    $term = DBManager::escape($term);
    $datos = array();
    $sql = "SELECT * FROM ".self::$table." WHERE activo='si'
AND (CONCAT(nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE '%$term%' OR razon_social LIKE '%$term%' OR numero_identificacion like '%$term%')";
    $result= DBManager::execute($sql);
    while ($row = mysql_fetch_assoc($result)) {
      if ($row['tipo_identificacion'] == 'N') {
        $nombre = $row['razon_social'];
      } else {
        $nombre = trim($row['nombre']." ".$row['primer_apellido']." ".$row['segundo_apellido']);
      }
      $datos[] = array("id" => $row['id'], "value" => $nombre);
    }
    return json_encode($datos);
  }

  /**
   * Selecciona todos los datos de todos los Terceros, ésta función es usada para exportar.
   * @param string $formato "XLS" o "CSV", indica que campos serán retornados.
   * @since Abril 21, 2011
   * @author  Edgar Ortega Ramírez
   */
  function Exportar($formato) {
    $sql = "SELECT * FROM ".self::$table;
    return DBManager::execute($sql);
  }

  function vehiculos($type = 'todos') {
    $sql = "SELECT v.*, m.Descripcion marca_nombre FROM ".Vehiculo::$table." v, marcas m
WHERE m.codigo_Marcas = v.codigo_Marcas";
    if ($type != 'todos') {
      $sql .= ' AND ';
      $sql .= $type == 'propietario' ? "v.idpropietario='$this->id'" : "v.id_tenedor='$this->id'";
    }
    return parent::build_resources($sql, 'Vehiculo');
  }
}
