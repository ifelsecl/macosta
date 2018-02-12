<?php
class VehiculoMantenimiento extends Base {

  static $table = 'vehiculos_mantenimientos';
  static $attributes = array('id', 'vehiculo_placa', 'mantenimiento_id', 'fecha', 'tipo', 'precio', 'observacion', 'numero_revision', 'numero_factura');
  static $tipos = array('Examen', 'Cambio', 'Puesta a punto');

  function __construct($params = array()) {
    $this->set_defaults();
    parent::__construct($params);
  }

  static function find($id) {
    $sql = "SELECT vh.*, m.nombre mantenimiento_nombre, m.kilometraje mantenimiento_kilometraje
FROM ".self::$table." vh, ".Mantenimiento::$table." m
WHERE m.id=vh.mantenimiento_id AND vh.id='$id'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  static function create($params) {
    return parent::_create($params);
  }

  static function search($params, $return_sql = false) {
    $f_placa = '';
    if (isset($params['vehiculo_placa']) and ! (isset($params['is_general']) and $params['is_general'])) {
      $f_placa .= "AND vh.vehiculo_placa LIKE '%".$params['vehiculo_placa']."%'";
    }
    if (isset($params['fecha_inicio']) and ! empty($params['fecha_inicio'])) {
      $f_placa .= " AND vh.fecha >= '".$params['fecha_inicio']."'";
    }
    if (isset($params['fecha_fin']) and ! empty($params['fecha_fin'])) {
      $f_placa .= " AND vh.fecha <= '".$params['fecha_fin']."'";
    }
    $sql = "SELECT vh.*, m.nombre mantenimiento_nombre, m.kilometraje mantenimiento_kilometraje
FROM ".self::$table." vh, ".Mantenimiento::$table." m
WHERE m.id=vh.mantenimiento_id ". $f_placa;
    if(isset($params['is_general']) and $params['is_general'] )
      $sql .= " order by vh.vehiculo_placa asc";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  function mantenimiento() {
    isset($this->mantenimiento) ? $this->mantenimiento : $this->mantenimiento = Mantenimiento::find($this->mantenimiento_id);
  }

  function vehiculo() {
    isset($this->vehiculo) ? $this->vehiculo : $this->vehiculo = Vehiculo::find($this->vehiculo_placa);
  }

  function reload() {
    $this::__construct(self::find($this->id));
  }

  function __toString() {
    return <<<EOF
<tr id="mantenimiento-{$this->id}">
  <td>{$this->mantenimiento_nombre} ({$this->mantenimiento_kilometraje} km)</td>
  <td>{$this->fecha}</td>
  <td>{$this->precio}</td>
  <td><button class="btn btn-default editar" name="{$this->to_param()}"><i class="icon icon-pencil"></i></button></td>
</tr>
EOF;
  }

  function to_param() {
    return 'id='.$this->id.'&'.nonce_create_query_string($this->id);
  }
}
