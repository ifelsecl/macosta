<?php
class RutaLocal extends Base {

  static $attributes = array('id', 'id_ciudad', 'fecha', 'placa_vehiculo',
    'placa_vehiculo_2', 'numero_identificacion_conductor', 'observaciones');

  static $table = "rutas_locales";

  function __construct($params = array()) {
    parent::__construct($params);
    if (isset($this->fecha)) {
      $this->fecha_corta = strftime('%b %d, %Y', strtotime($this->fecha));
      $this->fecha_larga = strftime("%d de %B de %Y", strtotime($this->fecha));
    }
  }

  function ciudad() {
    return $this->ciudad = Ciudad::find($this->id_ciudad);
  }

  function conductor() {
    $conductor = new Conductor;
    return $this->conductor = $conductor->find($this->numero_identificacion_conductor);
  }

  function vehiculo() {
    return $this->vehiculo = Vehiculo::find($this->placa_vehiculo);
  }

  function placa() {
    return $this->placa_vehiculo ? $this->placa_vehiculo : $this->placa_vehiculo_2 ;
  }

  function vehiculo_empresa() {
    return empty($this->placa_vehiculo_2);
  }

  function guias() {
    $sql = "SELECT g.*, SUM(i.peso) peso, SUM(i.kilo_vol) kilo_vol, SUM(i.unidades) unidades,
CONCAT_WS(cl.nombre, cl.primer_apellido, cl.segundo_apellido) cliente_nombre_completo,
CONCAT_WS(co.nombre, co.primer_apellido, co.segundo_apellido) contacto_nombre_completo,
cico.nombre contacto_ciudad_nombre
FROM ".Guia::$table." g, items i, ".Cliente::$table." cl, ".Cliente::$table." co, ".Ciudad::$table." cico
WHERE i.idguia=g.id AND g.idcliente=cl.id AND g.idcontacto=co.id AND co.idciudad=cico.id
AND g.id_ruta_local = $this->id GROUP BY g.id";
    return $this->guias = parent::build_resources($sql, 'Guia');
  }

  static function create($params) {
    $guias = $params['guias'];
    unset($params['guias']);
    if (! $ruta_local = self::_create($params)) return false;
    foreach ($guias as $key => $id_guia) {
      $ruta_local->add_guia($id_guia);
    }
    return $ruta_local;
  }

  function update($params) {
    if (empty($this->id)) return false;
    $guias = array();
    foreach ($this->guias() as $guia) {
      $guia->id = round($guia->id);
      $guias[] = $guia->id;
      if (! in_array($guia->id, $params['guias'])) $this->remove_guia($guia->id);
    }
    foreach ($params['guias'] as $id_guia) {
      if (! in_array($id_guia, $guias)) $this->add_guia($id_guia);
    }
    self::__construct($params);
    $sql = "UPDATE " . self::$table . " SET
id_ciudad='$this->id_ciudad', fecha='$this->fecha',
placa_vehiculo=".($this->placa_vehiculo ? "'$this->placa_vehiculo'" : 'NULL').", placa_vehiculo_2='$this->placa_vehiculo_2',
numero_identificacion_conductor='$this->numero_identificacion_conductor',
observaciones='$this->observaciones'
WHERE id=$this->id";
    return DBManager::execute($sql);
  }

  static function all($return_sql = false) {
    $sql = "SELECT rl.*, ci.nombre ciudad_nombre,
CONCAT_WS(' ', co.nombre, co.primer_apellido, co.segundo_apellido) conductor_nombre_completo
FROM ".self::$table." rl, ".Ciudad::$table." ci, ".Conductor::$table." co
WHERE ci.id=rl.id_ciudad AND co.numero_identificacion=rl.numero_identificacion_conductor
ORDER BY rl.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT rl.*, ci.nombre ciudad_nombre FROM ".self::$table." rl, ".Ciudad::$table." ci
WHERE ci.id=rl.id_ciudad AND rl.id = '$id'";
    $result = DBManager::execute($sql);
    if (DBManager::rows_count($result) == 0) return false;
    return new self(mysql_fetch_assoc($result));
  }

  function deactivate($id) {
    if (is_nan($id)) return false;
    $sql = "UPDATE ".self::$table." SET activo = 'no' WHERE id=$id";
    return DBManager::execute($sql);
  }

  function activate($id) {
    if (is_nan($id)) return false;
    $sql = "UPDATE ".self::$table." SET activo = 'si' WHERE id=$id";
    return DBManager::execute($sql);
  }

  function add_guia($id_guia) {
    $sql = "UPDATE ".Guia::$table." SET id_ruta_local = '$this->id',
id_estado_anterior = idestado, idestado = 3 WHERE id = $id_guia";
    Logger::guia($id_guia, 'agregó la guía a la Ruta Local '.$this->id);
    return DBManager::execute($sql);
  }

  function remove_guia($id_guia) {
    $sql = "UPDATE ".Guia::$table." SET id_ruta_local = NULL,
idestado = id_estado_anterior WHERE id = $id_guia";
    Logger::guia($id_guia, 'quitó la guía de la Ruta Local '.$this->id);
    return DBManager::execute($sql);
  }

  function history() {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE id_modulo = '$this->id' AND modulo = 'RutasLocales' ORDER BY fecha DESC LIMIT 100";
    return $this->history = parent::build_resources($sql);
  }
}
