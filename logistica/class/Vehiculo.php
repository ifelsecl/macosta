<?php
class Vehiculo extends Base {

  static $table = 'camiones';

  static $attributes = array(
    'placa', 'codigo_Marcas', 'codigo_linea', 'codigo_colores', 'codigo_carrocerias',
    'idpropietario', 'modelo', 'serie', 'num_seguro', 'f_venc_seguro', 'soat',
    'f_venc_soat', 't_operacion', 'f_venc_toperacion', 'tecnico_meca', 'f_venc_tmec',
    'peso', 'activo', 'idconfiguracion', 'registro', 'nitaseguradora', 'capacidadcarga',
    'modelo_repotenciado', 'placa_semiremolque', 'id_tenedor', 'km_inicial', 'km_actual',
    'numero_ejes', 'tipo_combustible', 'unidad_medida_capacidad_carga', 'fecha_afiliacion',
    'fecha_matricula', 'numero_chasis', 'numero_motor', 'numero_licencia_trans', 'fecha_expedicion_soat', 'numero_ficha_homologacion'
  );

  static $tipos_combustible = array(
    1 => 'Diesel o ACPM',
    2 => 'Gasolina',
    3 => 'Gas',
    4 => 'Gas/Gasolina'
  );

  static $unidades_medida_capacidad_carga = array(
    1 => "Kilogramos",
    2 => "Galones"
  );

  function history() {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE id_modulo = '$this->placa' AND modulo = 'Vehiculos' ORDER BY fecha DESC LIMIT 100";
    return $this->history = parent::build_resources($sql);
  }

  function update($params) {
    self::__construct($params);
    $this->placa = strtoupper($this->placa);
    $this->placa_semiremolque = strtoupper($this->placa_semiremolque);
    $sql = "UPDATE ".self::$table." SET
codigo_Marcas='$this->codigo_Marcas', codigo_linea='$this->codigo_linea',
codigo_colores='$this->codigo_colores', codigo_carrocerias='$this->codigo_carrocerias',
idpropietario='$this->idpropietario', modelo='$this->modelo',
serie='$this->serie', num_seguro='$this->num_seguro',
f_venc_seguro='$this->f_venc_seguro', soat='$this->soat',
f_venc_soat='$this->f_venc_soat', t_operacion='$this->t_operacion',
f_venc_toperacion='$this->f_venc_toperacion', tecnico_meca='$this->tecnico_meca',
f_venc_tmec='$this->f_venc_tmec', peso='$this->peso',
idconfiguracion='$this->idconfiguracion', registro='$this->registro',
nitaseguradora='$this->nitaseguradora', capacidadcarga='$this->capacidadcarga',
unidad_medida_capacidad_carga = '$this->unidad_medida_capacidad_carga',
modelo_repotenciado='$this->modelo_repotenciado',
placa_semiremolque='$this->placa_semiremolque', id_tenedor='$this->id_tenedor',
km_inicial='$this->km_inicial', km_actual='$this->km_actual',
numero_ejes='$this->numero_ejes', tipo_combustible='$this->tipo_combustible',
fecha_afiliacion='$this->fecha_afiliacion', numero_chasis='$this->numero_chasis',
numero_motor = '$this->numero_motor', numero_licencia_transito='$this->numero_licencia_transito',
fecha_expedicion_soat = '$this->fecha_expedicion_soat',
numero_ficha_homologacion = '$this->numero_ficha_homologacion', fecha_matricula = '$this->fecha_matricula'
WHERE placa='$this->placa'";
    return DBManager::execute($sql);
  }

  function marca() {
    $sql = "SELECT codigo_Marcas id, Descripcion nombre FROM marcas WHERE codigo_Marcas = '$this->codigo_Marcas'";
    return $this->marca = DBManager::select($sql);
  }

  function linea() {
    $sql = "SELECT descripcion nombre FROM lineas WHERE codigomarca = '$this->codigo_Marcas' AND codigo = '$this->codigo_linea'";
    return $this->linea = DBManager::select($sql);
  }

  function color() {
    $sql = "SELECT Descripcion nombre FROM colores WHERE codigo_colores = '$this->codigo_colores'";
    return $this->color = DBManager::select($sql);
  }

  function carroceria() {
    $sql = "SELECT descripcion nombre FROM carrocerias WHERE codigo_carrocerias = '$this->codigo_carrocerias'";
    return $this->carroceria = DBManager::select($sql);
  }

  function aseguradora() {
    $sql = "SELECT * FROM aseguradoras WHERE nit = '$this->nitaseguradora'";
    return $this->aseguradora = DBManager::select($sql);
  }

  function configuracion() {
    $sql = "SELECT id, configuracion nombre, tipo FROM configuraciones WHERE id = '$this->idconfiguracion'";
    return $this->configuracion = DBManager::select($sql);
  }

  function propietario() {
    return isset($this->propietario) ? $this->propietario : $this->propietario = Tercero::find($this->idpropietario);
  }

  function tenedor() {
    return isset($this->tenedor) ? $this->tenedor : $this->tenedor = Tercero::find($this->id_tenedor);
  }

  function mantenimientos() {
    return $this->mantenimientos = VehiculoMantenimiento::search(array('vehiculo_placa' => $this->placa));
  }

  function _mantenimientos() {
    return $this->mantenimientos = VehiculoMantenimiento::search(array('vehiculo_placa' => $this->placa), false);
  }
  function activo() {
    return $this->activo == 'si';
  }

  function seguro_vencido() {
    return $this->f_venc_seguro <= date('Y-m-d');
  }

  function soat_vencido() {
    return $this->f_venc_soat <= date('Y-m-d');
  }

  function tarjeta_operacion_vencida() {
    return $this->f_venc_toperacion <= date('Y-m-d');
  }

  function tecnico_mecanica_vencida() {
    return $this->f_venc_tmec <= date('Y-m-d');
  }

  function tiene_papeles_vencidos() {
    return $this->seguro_vencido() or $this->soat_vencido() or $this->tarjeta_operacion_vencida() or $this->tecnico_mecanica_vencida();
  }

  function deactivate($placa = null) {
    $params = array('activo' => 'no');
    return $this->update_attributes($params);
  }

  function activate($placa = null) {
    $params = array('activo' => 'si');
    return $this->update_attributes($params);
  }

  function to_param() {
    return 'placa='.$this->placa.'&'.nonce_create_query_string($this->placa);
  }

  function lineas() {
    $sql = "SELECT * FROM lineas WHERE codigomarca='$this->codigo_Marcas'";
    return parent::build_resources($sql);
  }

  function numero_tarjeta_operacion() {
    return $this->t_operacion;
  }

  static function sql_base() {
    return "SELECT c.*, m.Descripcion marca_nombre,
l.descripcion linea_nombre, col.Descripcion color_nombre,
car.descripcion carroceria_nombre, con.configuracion configuracion_nombre,
a.nombre aseguradora_nombre, ten.tipo_identificacion tenedor_tipo_identificacion,
ten.nombre tenedor_nombre, ten.primer_apellido tenedor_primer_apellido,
ten.numero_identificacion tenedor_numero_identificacion,
ten.segundo_apellido tenedor_segundo_apellido,
ten.razon_social tenedor_razon_social,
pro.numero_identificacion propietario_numero_identificacion,
pro.tipo_identificacion propietario_tipo_identificacion,
pro.razon_social propietario_razon_social,
pro.nombre propietario_nombre, pro.primer_apellido propietario_primer_apellido,
pro.segundo_apellido propietario_segundo_apellido
FROM ".self::$table." c, lineas l, aseguradoras a, marcas m, colores col,
carrocerias car, configuraciones con, ".Tercero::$table." ten, ".Tercero::$table." pro
WHERE ten.id=c.id_tenedor AND c.nitaseguradora=a.nit
AND pro.id=c.idpropietario AND c.codigo_linea=l.codigo
AND c.codigo_Marcas=m.codigo_Marcas AND c.codigo_colores=col.codigo_colores
AND c.codigo_carrocerias=car.codigo_carrocerias
AND l.codigomarca=m.codigo_Marcas AND c.idconfiguracion=con.id";
  }

  static function all($which = 'todos', $return_sql = false){
    $where = '';
    if ($which != 'todos') {
      $where = 'AND c.activo = ';
      $where .= $which == 'activos' ? '"si"' : '"no"';
    }
    $sql = self::sql_base().' '.$where;
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find($placa) {
    $placa = DBManager::escape($placa);
    $sql = self::sql_base()." AND c.placa='$placa'";
    return parent::find_record($sql);
  }

  static function search($params, $return_sql = false) {
    foreach ($params as $key => $value) $params[$key] = DBManager::escape($value);
    $where = "";
    if (isset($params['placa']) and ! empty($params['placa'])) {
      $where = " AND c.placa LIKE '%".$params['placa']."%'";
    }
    $sql = self::sql_base()." ".$where;
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function aseguradoras() {
    $sql = "SELECT * FROM aseguradoras ORDER BY nombre";
    return parent::build_resources($sql);
  }

  static function marcas() {
    $sql = "SELECT * FROM marcas ORDER BY Descripcion ASC";
    return parent::build_resources($sql);
  }

  static function colores() {
    $sql = "SELECT * FROM colores ORDER BY Descripcion ASC";
    return parent::build_resources($sql);
  }

  static function carrocerias() {
    $sql = "SELECT * FROM carrocerias ORDER BY descripcion ASC";
    return parent::build_resources($sql);
  }

  static function configuraciones() {
    $sql = "SELECT * FROM configuraciones WHERE activo='si' ORDER BY tipo";
    return parent::build_resources($sql);
  }

  static function all_lineas_by_marca($id_marca) {
    $sql = "SELECT * FROM lineas WHERE codigomarca='$id_marca' ORDER BY descripcion ASC";
    return parent::build_resources($sql);
  }

  static function expiring_by_type($type) {
    $sql = "SELECT * FROM ".self::$table." WHERE activo='si' AND $type <= (
SELECT DATE_FORMAT( DATE_ADD( NOW( ) , INTERVAL 30 DAY ) , '%Y-%m-%d' ))";
    return parent::build_resources($sql);
  }
}
