<?php
class Manifiesto extends Base {

  static $table = 'planillas';
  static $attributes = array('id');

  static $tipos = array(
    'G' => 'GENERAL',
    'Y' => 'CONTENEDOR',
    'C' => 'CONTRATO INTEGRAL',
    'V' => 'CONSOLIDADOR'
  );

  static $opciones_cargue = array(
    'R' => 'REMITENTE',
    'D' => 'DESTINATARIO'
  );

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT m.* FROM ".self::$table." m WHERE  m.id='$id'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  function conductor() {
    $conductor = new Conductor;
    return $this->conductor = $conductor->find($this->cedulaconductor);
  }

  function titular() {
    return isset($this->titular) ? $this->titular : $this->titular = Tercero::find($this->id_titular);
  }

  function ciudad_destino() {
    return isset($this->ciudad_destino) ? $this->ciudad_destino : $this->ciudad_destino = Ciudad::find($this->idciudaddestino);
  }

  function ciudad_origen() {
    return isset($this->ciudad_origen) ? $this->ciudad_origen : $this->ciudad_origen = Ciudad::find($this->idciudadorigen);
  }

  function ciudad_pago_saldo() {
    return isset($this->ciudad_pago_saldo) ? $this->ciudad_pago_saldo : $this->ciudad_pago_saldo = Ciudad::find($this->id_ciudad_pago_saldo);
  }

  function guias($which = 'todas') {
    return $this->guias = Guia::all_by_manifiesto($this->id, $which);
  }

  function resolucion() {
    return isset($this->resolucion) ? $this->resolucion : $this->resolucion = Resolucion::find_by_tipo_and_numero('manifiestos', $this->id);
  }

  function vehiculo() {
    return isset($this->vehiculo) ? $this->vehiculo : $this->vehiculo = Vehiculo::find($this->placacamion);
  }

  function deactivate($motivo) {
    $params = array(
      'activa' => 'no',
      'motivo_anulacion' => DBManager::escape($motivo),
      'estado' => 'A'
    );
    return $this->update_attributes($params);
  }

  function remove_guia($id_guia) {
    $guia = new Guia;
    if (! $guia->find($id_guia)) return false;
    $params = array(
      'idplanilla' => null,
      'posicionplanilla' => 0,
      'fechadespacho' => null,
      'idestado' => 'id_estado_anterior',
      'id_estado_anterior' => 1
    );
    return $guia->update_attributes($params);
  }

  function tipo() {
    return self::$tipos[$this->tipo];
  }

  function cargue_pagado_por() {
    return self::$opciones_cargue[$this->cargue_pagado_por];
  }

  function descargue_pagado_por() {
    return self::$opciones_cargue[$this->descargue_pagado_por];
  }

  function activo() {
    return $this->activa == 'si';
  }

  function to_param() {
    return 'idplanilla='.$this->id."&".nonce_create_query_string($this->id);
  }

  static function search($params = array(), $return_sql = false) {
    foreach ($params as $key => $value) {
      $params[$key] = DBManager::escape($value);
    }
    $default_params = array('id', 'ciudad_destino', 'placa', 'fecha_inicio', 'fecha_fin');
    foreach ($default_params as $p) {
      if (! isset($params[$p])) $params[$p] = '';
    }
    $filtro_id = '';
    $filtro_ciudad_destino = '';
    $filtro_conductor = '';
    $filtro_placa = '';
    $filtro_fecha = '';
    if (! empty($params['id'])) {
      $filtro_id = "AND p.id LIKE '%".$params['id']."%'";
    }
    if (! empty($params['ciudad_destino'])) {
      $filtro_ciudad_destino = "AND cid.nombre LIKE '%".$params['ciudad_destino']."%'";
    }
    if (! empty($params['conductor'])) {
      $filtro_conductor = "AND CONCAT(co.nombre, ' ', co.primer_apellido, ' ', co.segundo_apellido) LIKE '%".$params['conductor']."%'";
    }
    if (! empty($params['placa'])) {
      $filtro_placa = "AND p.placacamion LIKE '%".$params['placa']."%'";
    }
    if (!empty($params['fecha_inicio']) or !empty($params['fecha_fin'])) {
      if (empty($params['fecha_inicio']) and !empty($params['fecha_fin'])) {
        $filtro_fecha = "AND p.fecha <= '".$params['fecha_fin']."'";
      } elseif (empty($params['fecha_fin']) and !empty($params['fecha_inicio'])) {
        $filtro_fecha = "AND p.fecha >= '".$params['fecha_inicio']."'";
      } else {
        $filtro_fecha = "AND (p.fecha BETWEEN '".$params['fecha_inicio']."' AND '".$params['fecha_fin']."')";
      }
    }
    $sql = "SELECT p.*, cid.nombre ciudad_destino_nombre,
CONCAT_WS(' ', co.nombre, co.primer_apellido, co.segundo_apellido) conductor_nombre_completo
FROM ".self::$table." p, conductores co, ciudades cid
WHERE p.idciudaddestino = cid.id AND p.cedulaconductor = co.numero_identificacion
$filtro_id $filtro_placa $filtro_conductor $filtro_ciudad_destino $filtro_fecha
ORDER BY p.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function all($cuales = 'todos', $return_sql = false) {
    $f_inicio = date('Y-m-d', strtotime('first day of this month'));
    $f_fin = date('Y-m-d', strtotime('last day of this month'));
    $where = 'WHERE 1';
    if ($cuales != 'todos') {
      if ($cuales == 'activos') {
        $where .= " AND activa='si'";
      } else {
        $where .= " AND activa='no'";
      }
    }
    $where .= " AND fecha BETWEEN '$f_inicio' AND '$f_fin'";
    $sql = "SELECT * FROM ".self::$table." $where ORDER BY id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function export($params, $return_sql = false) {
    foreach ($params as $key => $value) {
      $params[$key] = DBManager::escape($value);
    }
    $where = $params['tipo'] == 'fecha' ? 'p.fecha' : 'p.id' ;
    $where .= " BETWEEN '".$params['inicio']."' AND '".$params['fin']."'";
    $sql = "SELECT p.*, ciu.nombre ciudad_destino_nombre, m.Descripcion marca_nombre,
CONCAT_WS(' ', co.nombre, co.primer_apellido, co.segundo_apellido) conductor_nombre_completo,
co.direccion conductor_direccion, c.idpropietario, c.placa
FROM ".self::$table." p, ".Vehiculo::$table." c, ".Conductor::$table." co,
marcas m,".Ciudad::$table." ciu
WHERE p.idciudaddestino=ciu.id AND p.cedulaconductor = co.numero_identificacion AND
p.placacamion = c.placa AND c.codigo_Marcas = m.codigo_Marcas AND $where
ORDER BY p.id";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }
}
