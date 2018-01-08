<?php
class Guia extends Base {

  static $attributes = array('id', 'año', 'fechadespacho', 'fechaentrega',
    'fecha_recibido_mercancia', 'valordeclarado', 'valorseguro', 'formapago',
    'observacion', 'total', 'unidadmedida', 'empaque', 'naturaleza',
    'documentocliente', 'idcliente', 'idcontacto', 'idestado', 'id_estado_anterior',
    'idusuario', 'idplanilla', 'idfactura', 'posicionplanilla',
    'id_razon_devolucion', 'reportada', 'numero',
    'impresa', 'edicion', 'peso_contenedor', 'cierre', 'propietario',
    'cargue_horas_pactadas', 'descargue_horas_pactadas', 'cargue_llegada',
    'descargue_llegada', 'cargue_salida', 'descargue_salida', 'recogida',
    'id_ruta_local', 'id_tipo_operacion');

  static $table = 'guias';

  static $tipos_operacion = array(
    'P' => 'PAQUETEO',
    'G' => 'GENERAL',
    'C' => 'CONTENEDOR CARGADO',
    'V' => 'CONTENEDOR VACIO'
  );

  static $estados = array(
    1 => 'BODEGA',
    2 => 'FACTURADA',
    3 => 'TRANSITO',
    4 => 'ENTREGADA',
    5 => 'DEVUELTA',
    6 => 'ANULADA',
    7 => 'PREGUIA'
  );

  static $unidades_medida = array(
    1 => 'KILOGRAMOS',
    2 => 'GALONES'
  );

  static $naturalezas_carga = array(
    1 => 'CARGA NORMAL',
    2 => 'CARGA PELIGROSA',
    3 => 'CARGA EXTRADIMENSIONADA',
    4 => 'CARGA EXTRAPESADA',
    5 => 'DESECHOS PELIGROSOS',
    6 => 'SEMOVIENTES',
    7 => 'REFRIGERADA'
  );

  static $unidades_empaque = array(
    0 => 'PAQUETES',
    4 => 'BULTO',
    6 => 'GRANEL LIQUIDO',
    7 => '1 CONTENEDOR DE 20 PIES',
    8 => '2 CONTENEDORES DE 20 PIES',
    9 => '1 CONTENEDOR DE 40 PIES',
    12 => 'CILINDROS',
    15 => 'GRANEL SOLIDO',
    17 => 'VARIOS',
    18 => 'NO APLICA (AUTOS, GANADO O MAQUINARIA)',
    19 => 'CARGA ESTIBADA'
  );

  static $propietarios_carga = array('Remitente', 'Destinatario');

  static $formas_pago = array('FLETE AL COBRO', 'CREDITO', 'CONTADO');

  static function sql_base() {
    return "SELECT g.*, SUM(i.peso) peso, SUM(i.kilo_vol) kilo_vol, SUM(i.unidades) unidades,
cl.nombre cliente_nombre, cl.primer_apellido cliente_primer_apellido,
cl.segundo_apellido cliente_segundo_apellido, cl.direccion cliente_direccion,
cl.numero_identificacion cliente_numero_identificacion, cl.digito_verificacion cliente_digito_verificacion,
cl.telefono cliente_telefono, cio.nombre cliente_ciudad_nombre, co.nombre contacto_nombre,
co.primer_apellido contacto_primer_apellido, co.segundo_apellido contacto_segundo_apellido,
co.direccion contacto_direccion, co.numero_identificacion contacto_numero_identificacion,
co.digito_verificacion contacto_digito_verificacion, co.telefono contacto_telefono,
cid.nombre contacto_ciudad_nombre
FROM ".self::$table." g, ".Cliente::$table." cl, ".Cliente::$table." co,
items i, ".Ciudad::$table." cio, ".Ciudad::$table." cid";
  }

  function __construct($params = array()) {
    parent::__construct($params);
    if (isset($this->cliente_nombre)) {
      $client_params = array(
        "id" => $this->idcliente,
        "nombre" => $this->cliente_nombre,
        "primer_apellido" => $this->cliente_primer_apellido,
        "segundo_apellido" => $this->cliente_segundo_apellido
      );
      if (isset($this->cliente_direccion)) $client_params["direccion"] = $this->cliente_direccion;
      if (isset($this->cliente_numero_identificacion)) $client_params["cliente_numero_identificacion"] = $this->cliente_numero_identificacion;
      if (isset($this->cliente_telefono)) $client_params["telefono"] = $this->cliente_telefono;
      if (isset($this->cliente_digito_verificacion)) $client_params["digito_verificacion"] = $this->cliente_digito_verificacion;
      $this->cliente = new Cliente($client_params);
      if (isset($this->cliente_ciudad_nombre)) {
        $city_params = array('nombre' => $this->cliente_ciudad_nombre);
        $this->cliente->ciudad = new Ciudad($city_params);
      }
    }
    if (isset($this->contacto_nombre)) {
      $client_params = array(
        'id' => $this->idcontacto,
        "nombre" => $this->contacto_nombre,
        "primer_apellido" => $this->contacto_primer_apellido,
        "segundo_apellido" => $this->contacto_segundo_apellido
      );
      if (isset($this->contacto_direccion)) $client_params["direccion"] = $this->contacto_direccion;
      if (isset($this->contacto_numero_identificacion)) $client_params["numero_identificacion"] = $this->contacto_numero_identificacion;
      if (isset($this->contacto_telefono)) $client_params["telefono"] = $this->contacto_telefono;
      if (isset($this->contacto_digito_verificacion)) $client_params["digito_verificacion"] = $this->contacto_digito_verificacion;
      $this->contacto = new Cliente($client_params);
      if (isset($this->contacto_ciudad_nombre)) {
        $city_params = array('nombre' => $this->contacto_ciudad_nombre);
        $this->contacto->ciudad = new Ciudad($city_params);
      }
    }
    if (isset($this->idestado)) $this->estado = self::$estados[$this->idestado];
  }

  static function create($params) {
    $params['idusuario'] = $_SESSION['userid'];
    $params['observacion'] = str_replace(array("\r\n", "\n", "\r"), ', ', $params['observacion']);
    $params['peso_contenedor'] = isset($params['peso_contenedor']) ? $params['peso_contenedor'] : 0;
    $params['fecha_recibido_mercancia'] = date('Y-m-d');
    $params['año'] = date('Y');
    return self::_create($params);
  }

  static function all($which = 'todas', $return_sql = false) {
    $f_cuales = ($which == 'activas') ? "AND g.idestado != 6" : '';
    $inicio = date('Y-m-d', strtotime('first day of this month'));
    $fin = date('Y-m-d', strtotime('last day of this month'));
    $sql = "SELECT g.*, co_ci.nombre contacto_ciudad_nombre,
CONCAT_WS(' ', cl.nombre, cl.primer_apellido, cl.segundo_apellido) cliente_nombre_completo,
CONCAT_WS(' ', co.nombre, co.primer_apellido, co.segundo_apellido) contacto_nombre_completo
FROM ".self::$table." g, ".Cliente::$table." cl, ".Cliente::$table." co, ".Ciudad::$table." co_ci
WHERE cl.id=g.idcliente AND co.id=g.idcontacto AND co_ci.id=co.idciudad AND g.fecha_recibido_mercancia BETWEEN '".$inicio."' AND '".$fin."' $f_cuales
ORDER BY g.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function search($params, $return_sql = false) {
    foreach ($params as $key => $value) {
      $params[$key] = addslashes(trim($value));
    }

    $ff = date('Y-m-d');
    $fi = date('Y-m-d', strtotime( isset($params['seis_meses']) ? '-6 months' : 'first day of this month'));
    $f_todos_meses = "AND (g.fecha_recibido_mercancia BETWEEN '$fi' AND '$ff')";

    if (empty($params['id'])) {
      $f_id = '';
    } else {
      $f_id = "AND g.id='".$params['id']."'";
      $f_todos_meses = '';
    }

    if (empty($params['numero_anterior'])) $f_numero='';
    else {
      $f_numero="AND g.numero LIKE '%".$params['numero_anterior']."%'";
      $f_todos_meses='';
    }

    if (empty($params['documento'])) $f_documento = '';
    else {
      $f_documento = "AND g.documentocliente LIKE '%".$params['documento']."%'";
      $f_todos_meses = '';
    }

    if (empty($params['manifiesto'])) $f_planilla = "";
    else {
      $f_planilla = "AND g.idplanilla='".$params['manifiesto']."'";
      $f_todos_meses = '';
    }

    if (empty($params['fecha'])) $f_fecha = '';
    else {
      $f_todos_meses = '';
      $f_fecha = "AND g.fecha_recibido_mercancia LIKE '%".$params['fecha']."%'";
    }

    if (empty($params['ciudad_origen'])) $f_ciudad_origen = '';
    else $f_ciudad_origen = "AND ci_cl.nombre LIKE '%".$params['ciudad_origen']."%'";

    if (empty($params['ciudad_destino'])) $f_ciudad_destino = '';
    else $f_ciudad_destino = "AND co_ci.nombre LIKE '%".$params['ciudad_destino']."%'";

    if (empty($params['cliente'])) $f_cliente = '';
    else $f_cliente = "AND (CONCAT_WS(' ', cl.nombre, cl.primer_apellido, cl.segundo_apellido) LIKE '%".$params['cliente']."%')";

    if (empty($params['contacto'])) $f_contacto = '';
    else $f_contacto = "AND (CONCAT_WS(' ', co.nombre, co.primer_apellido, co.segundo_apellido) LIKE '%".$params['contacto']."%')";

    if (empty($params['usuario'])) $f_usuario = '';
    else $f_usuario = "AND u.usuario LIKE '%".$params['usuario']."%'";

    if (empty($params['estado'])) $f_estado = '';
    else $f_estado = "AND e.nombre LIKE '%".$params['estado']."%'";

    if (isset($params['recogida'])) $f_recogida = "AND g.recogida = 'si'";
    else $f_recogida = '';
    $sql = "SELECT g.*, co_ci.nombre contacto_ciudad_nombre,
CONCAT_WS(' ', cl.nombre, cl.primer_apellido, cl.segundo_apellido) cliente_nombre_completo,
CONCAT_WS(' ', co.nombre, co.primer_apellido, co.segundo_apellido) contacto_nombre_completo
FROM ".self::$table." g, ".Cliente::$table." cl, ".Cliente::$table." co, ".Ciudad::$table." co_ci,
".Ciudad::$table." cl_ci, estadosguias e, ".Usuario::$table." u
WHERE g.idestado=e.id AND g.idusuario=u.id AND cl.id=g.idcliente AND co.id=g.idcontacto
AND co_ci.id=co.idciudad AND cl_ci.id=cl.idciudad
$f_id $f_fecha $f_planilla $f_numero $f_ciudad_origen $f_ciudad_destino
$f_cliente $f_contacto $f_documento $f_estado $f_recogida $f_todos_meses $f_usuario
ORDER BY g.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function search_in_bodega($term, $option, $return_sql = true) {
    $term = DBManager::escape($term);
    if ($option == 'numero') {
      $filtro = " g.id LIKE '%$term%'";
    } elseif ($option == 'ciudad_destino') {
      $filtro = " ciudestino.nombre LIKE '%$term%'";
    } elseif ($option == 'ciudad_origen') {
      $filtro = " ciudorigen.nombre LIKE '%$term%'";
    } elseif ($option == 'cliente') {
      $filtro = " CONCAT(cl.nombre, ' ', cl.primer_apellido, ' ', cl.segundo_apellido) LIKE '%$term%'";
    } elseif ($option == 'estado') {
      $filtro = " e.nombre LIKE '%$term%'";
    } else {
      $filtro = " g.numero LIKE '%$term%'";
    }
    $sql = "SELECT g.*, cl.nombre nombrecliente, cl.nombre cliente_n,
cl.tipo_identificacion cliente_ti, cl.primer_apellido cliente_pa,
cl.segundo_apellido cliente_sa, ciudestino.nombre nombreciudaddestino,
co.nombre nombrecontacto, co.tipo_identificacion tipo_identificacion_contacto,
co.primer_apellido primer_apellido_contacto,
co.segundo_apellido segundo_apellido_contacto, co.direccion direccion_contacto,
SUM(i.peso) peso, SUM(i.kilo_vol) kilo_vol
FROM guias g, clientes cl, ".Ciudad::$table." ciudestino, ".Cliente::$table." co, items i
WHERE i.idguia=g.id AND cl.id=g.idcliente AND co.idciudad=ciudestino.id
AND g.idcontacto=co.id AND g.idestado=1 AND $filtro
GROUP BY g.id
ORDER BY g.id DESC";
    if ($return_sql) return $sql;
    return DBManager::execute($sql);
  }

  function find($id) {
    $sql = self::sql_base()." WHERE g.idcliente=cl.id AND co.id=g.idcontacto AND
cio.id=cl.idciudad AND cid.id=co.idciudad AND i.idguia=g.id AND
g.id=$id GROUP BY g.id";
    if (! $result = DBManager::execute($sql)) return false;
    if (DBManager::rows_count($result) == 0) return false;
    self::__construct( mysql_fetch_assoc($result) );
    return $this;
  }

  static function find_by_id($ids) {
    if (empty($ids)) return array();
    $ids = str_replace(' ', '', strip_tags($ids));
    $ids = explode('-', $ids);
    foreach ($ids as $key => $value) {
      if (! is_numeric($value)) unset($ids[$key]);
    }
    $ids = implode(',', $ids);
    $sql = self::sql_base()." WHERE g.idcliente=cl.id AND co.id=g.idcontacto AND
cio.id=cl.idciudad AND cid.id=co.idciudad AND i.idguia=g.id AND
g.id IN ($ids) GROUP BY g.id ORDER BY FIELD(g.id, $ids)";
    return parent::build_resources($sql, __CLASS__);
  }

  static function all_by_id_cliente_and_numero_documento($id_cliente, $documento) {
    $documento = DBManager::escape($documento);
    $id_cliente = DBManager::escape($id_cliente);
    $sql = "SELECT * FROM ".self::$table." WHERE idcliente='$id_cliente' AND documentocliente LIKE '%$documento%'";
    return parent::build_resources($sql, __CLASS__);
  }

  function cliente() {
    $cliente = new Cliente;
    return $this->cliente = $cliente->find($this->idcliente);
  }

  function contacto() {
    $cliente = new Cliente;
    return $this->contacto = $cliente->find($this->idcontacto);
  }

  function estado() {
    return $this->estado = self::$estados[$this->idestado];
  }

  function razon_devolucion() {
    $sql = "SELECT rd.* FROM ".self::$table." g, razones_devolucion rd WHERE rd.id=g.id_razon_devolucion AND g.id = $this->id";
    $result = DBManager::execute($sql);
    return $this->razon_devolucion = mysql_fetch_object($result);
  }

  function items() {
    $sql = "SELECT i.*, p.nombre producto, e.nombre embalaje, e.tipo_cobro
FROM items i, ".Producto::$table." p, embalajes e
WHERE i.idproducto=p.id AND i.idembalaje=e.id AND i.idguia='$this->id'";
    return $this->items = parent::build_resources($sql);
  }

  static function all_by_estado($id_estado, $fecha_inicio, $fecha_fin, $return_sql = false) {
    if (empty($fecha_inicio) and empty($fecha_fin)) {
      $filtro_fecha = '';
    } elseif (!empty($fecha_fin) and !empty($fecha_inicio)) {
      $filtro_fecha="AND g.fecha_recibido_mercancia BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    } elseif (!empty($fecha_inicio) and empty($fecha_fin)) {
      $filtro_fecha="AND g.fecha_recibido_mercancia >= '$fecha_inicio'";
    } else {
      $filtro_fecha="AND g.fecha_recibido_mercancia <= '$fecha_fin'";
    }
    $sql = "SELECT g.* FROM ".self::$table." g WHERE g.idestado='$id_estado' $filtro_fecha ORDER BY g.id";
    if ($return_sql) return $sql;
  }

  function flete_al_cobro() {
    return $this->formapago == 'FLETE AL COBRO';
  }

  static function all_by_cliente($id_cliente, $params = array(), $return_sql = false) {
    foreach ($params as $key => $value) {
      $params[$key] = DBManager::escape($value);
    }
    $f_estado = '';
    if (! isset($params['id']) or empty($params['id'])) {
      $f_id = '';
      $f_estado = "AND g.idestado!=6";
    } else {
      $f_id = "AND g.id='".$params['id']."'";
    }
    if (! isset($params['fecha']) or empty($params['fecha'])) {
      $f_fecha = '';
    } else {
      $f_fecha = "AND g.fecha_recibido_mercancia LIKE '".$params['fecha']."'";
    }
    if (! isset($params['documento']) or empty($params['documento'])) {
      $f_documento = '';
    } else {
      $f_documento = "AND g.documentocliente LIKE '%".$params['documento']."%'";
    }
    if (! isset($params['contacto']) or empty($params['contacto'])) {
      $f_contacto = '';
    } else {
      $f_contacto = "AND (CONCAT(co.nombre, ' ', co.primer_apellido, ' ', co.segundo_apellido) LIKE '%".$params['contacto']."%')";
    }
    if (! isset($params['no_anterior']) or empty($params['no_anterior'])) {
      $f_no_anterior = '';
    } else {
      $f_no_anterior = "AND g.numero LIKE '%".$params['no_anterior']."%'";
    }
    $sql = "SELECT g.*, cl.nombre cliente_nombre,
cl.primer_apellido cliente_primer_apellido, cl.segundo_apellido cliente_segundo_apellido,
ciudestino.nombre contacto_ciudad_nombre, co.nombre contacto_nombre,
co.primer_apellido contacto_primer_apellido, co.segundo_apellido contacto_segundo_apellido
FROM ".self::$table." g, ".Cliente::$table." cl, ".Ciudad::$table." ciudestino, ".Cliente::$table." co
WHERE cl.id=g.idcliente AND co.idciudad=ciudestino.id AND
g.idcontacto=co.id AND g.idcliente='$id_cliente' $f_id $f_fecha
$f_contacto $f_documento $f_estado $f_no_anterior ORDER BY g.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function all_by_manifiesto($id_manifiesto, $which = 'todas') {
    $filter = $which == 'pendientes' ? " AND idestado IN (1,3)" : '';
    $sql = "SELECT g.*, SUM(i.peso) peso, SUM(i.kilo_vol) kilo_vol, SUM(i.unidades) unidades,
CONCAT_WS(' ', cl.nombre, cl.primer_apellido, cl.segundo_apellido) cliente_nombre_completo,
cl.numero_identificacion cliente_numero_identificacion, cl.direccion cliente_direccion,
CONCAT_WS(' ', co.nombre, co.primer_apellido, co.segundo_apellido) contacto_nombre_completo,
co.direccion contacto_direccion, co.numero_identificacion contacto_numero_identificacion,
p.nombre producto_nombre, p.id producto_id, ciud.nombre contacto_ciudad_nombre,
ciuo.nombre cliente_ciudad_nombre
FROM ".self::$table." g, items i, ".Cliente::$table." cl, ".Cliente::$table." co,
".Producto::$table." p, ".Ciudad::$table." ciud, ".Ciudad::$table." ciuo
WHERE cl.id=g.idcliente AND i.idguia=g.id AND co.id=g.idcontacto AND ciuo.id=cl.idciudad AND ciud.id=co.idciudad AND p.id=i.idproducto AND
g.idplanilla = '$id_manifiesto' $filter GROUP BY g.id";
    return parent::build_resources($sql, __CLASS__);
  }

  function razones_devolucion() {
    $sql = "SELECT * FROM razones_devolucion WHERE activo='si'";
    return $this->razones_devolucion = parent::build_resources($sql);
  }

  function update($params) {
    $filtro = '';
    self::__construct($params);
    $filtro_estado = ", idestado='$this->idestado'";
    if (in_array($this->idestado, array(1, 6, 7))) {
      $filtro = ", idplanilla=NULL";
    }
    if (isset($params['idfactura'])) {
      $filtro .= ", idfactura=NULL";
      $filtro_estado = ", idestado=4";
    }

    $e_fecha_entrega = "fechaentrega=";
    if (empty($this->fechaentrega)) $e_fecha_entrega .= "NULL,";
    else $e_fecha_entrega .= "'$this->fechaentrega',";
    $sql = "UPDATE ".self::$table." SET unidadmedida=$this->unidadmedida, empaque=$this->empaque,
naturaleza=$this->naturaleza, documentocliente='$this->documentocliente',
fecha_recibido_mercancia='$this->fecha_recibido_mercancia', $e_fecha_entrega formapago='$this->formapago', observacion='$this->observacion',
id_razon_devolucion='$this->id_razon_devolucion', recogida='$this->recogida',
idcliente='$this->idcliente', idcontacto='$this->idcontacto', numero='$this->numero',
valordeclarado='$this->valordeclarado', valorseguro='$this->valorseguro',
total='$this->total', edicion='$this->edicion', id_estado_anterior=idestado $filtro_estado $filtro
WHERE id='$this->id'";
    return DBManager::execute($sql);
  }

  function updated_attributes($params, $return_html = true) {
    $changed = false;
    $changes = '<ul>';
    foreach ($params as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $i => $item) {
          $i--;
          if (isset($this->items[$i])) {
            foreach ($item as $y => $z) {
              if ($this->items[$i]->$y != $z) {
                $changed = true;
                $changes .= '<li>'.$y.' era '.$this->items[$i]->$y.' ahora '.$z.'</li>';
              }
            }
          } else {
            $changed = true;
            $changes .= '<li>agregó un item: '.$item['idproducto'].' | '.$item['unidades'].' | '.$item['peso'].' | '.$item['valor'].'</li>';
          }
        }
        if ( count($params['items']) < count($this->items) ) {
          $changed = true;
          $changes .= '<li>eliminó un item.</li>';
        }
      } else {
        if ((property_exists($this, $key) and $this->$key != $value) or "idfactura" == $key) {
          $changed = true;
          if ($key == 'idestado') {
            $this->estado = self::$estados[$this->$key];
            $value = self::$estados[$value];
            $key = 'estado';
          }
          $changes .= '<li>'.ucwords(str_replace('_', ' ', $key)).': ';
          if (empty($this->$key)) $this->$key = 'vacio';
          if (empty($value)) $value = 'vacio';
          $changes .= "era <i>".$this->$key."</i> ahora <i>".filter_var($value, FILTER_SANITIZE_STRING)."</i></li>";
        }
      }
    }
    $changes .= '</ul>';
    return $changed ? $changes : false;
  }

  function add_item($item) {
    $sql = "INSERT INTO items (idguia, idproducto, unidades, peso, kilo_vol, idembalaje, valor)
VALUES('$this->id','".$item['idproducto']."','".$item['unidades']."',
'".$item['peso']."', '".$item['kilo_vol']."', '".$item['idembalaje']."',
'".$item['valor']."')";
    return DBManager::execute($sql);
  }

  function remove_items() {
    $sql = "DELETE FROM items WHERE idguia=$this->id";
    return DBManager::execute($sql);
  }

  function history() {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE ROUND(id_modulo) = '".intval($this->id)."'
AND modulo = 'Guias' ORDER BY fecha DESC LIMIT 20";
    return $this->history = parent::build_resources($sql);
  }

  static function mark_as_printed($ids) {
    if (! is_array($ids)) $ids = array($ids);
    foreach($ids as $id) {
      $id = DBManager::escape($id);
      $sql = "UPDATE ".self::$table." SET impresa='si' WHERE id='$id'";
      DBManager::execute($sql);
    }
    return true;
  }

  function all_by_formas_de_pago($id_cliente, $formas_pago, $fecha_inicio, $fecha_fin) {
    $sql = "SELECT g.*, SUM(i.unidades) unidades FROM ".self::$table." g, items i
WHERE g.idcliente=$id_cliente AND i.idguia=g.id AND g.formapago IN ('".implode("','", $formas_pago)."')
AND g.idestado IN (1, 2, 3, 4) AND g.fecha_recibido_mercancia BETWEEN '$fecha_inicio' AND '$fecha_fin'
GROUP BY g.id";
    return parent::build_resources($sql, __CLASS__);
  }

  function mark_as_entregada($fecha) {
    $params = array(
      'id_estado_anterior' => 'idestado',
      'idestado' => 4,
      'fechaentrega' => $fecha
    );
    return $this->update_attributes($params);
  }

  static function mark_multiple_as_entregada($ids, $fecha) {
    $r = array('ok' => false, 'mensaje' => 'No se han indicado guias');
    $html = '';
    $guia = new self;
    foreach ($ids as $id) {
      if ($guia->find($id)) {
        $params = array(
          'id_estado_anterior' => 'idestado',
          'idestado' => 4,
          'id_razon_devolucion' => 5,
          'fechaentrega' => $fecha
        );
        $guia->update_attributes($params);
        Logger::guia($guia->id, 'marcó la guía como ENTREGADA');
        $r['ok'] = true;
      } else {
        $html .= '<li>'.$id.': no existe</li>';
      }
    }
    $r['mensaje'] = '<ul>'.$html.'</ul>';
    return $r;
  }

  function deactivate($id = null) {
    if (! is_null($id)) $this->id = $id;
    $params = array(
      'id_estado_anterior' => 'idestado',
      'idestado' => 6
    );
    return $this->update_attributes($params);
  }

  function activate($id = null) {
    if (! is_null($id)) $this->id = $id;
    $params = array('idestado' => 'id_estado_anterior');
    return $this->update_attributes($params);
  }

  static function informe_cliente($id_cliente, $estados, $fecha_inicio, $fecha_fin) {
    if (empty($fecha_inicio) and empty($fecha_fin)) {
      $filtro_fecha = "";
    } else {
      if (! empty($fecha_fin) and ! empty($fecha_inicio)) {
        $filtro_fecha = "AND g.fecha_recibido_mercancia BETWEEN '$fecha_inicio' AND '$fecha_fin'";
      } elseif (! empty($fecha_inicio) and empty($fecha_fin)) {
        $filtro_fecha = "AND g.fecha_recibido_mercancia >= '$fecha_inicio'";
      } else {
        $filtro_fecha = "AND g.fecha_recibido_mercancia <= '$fecha_fin'";
      }
    }
    $sql = "SELECT g.* FROM ".self::$table." g
WHERE g.idcliente='$id_cliente' AND g.idestado IN (".implode(',', $estados).")
$filtro_fecha ORDER BY g.id";
    return parent::build_resources($sql, __CLASS__);
  }

  function delete() {
    return self::_delete($this->id);
  }

  function assign_factura($id_factura) {
    $params = array(
      'idfactura' => $id_factura,
      'id_estado_anterior' => 'idestado',
      'idestado' => 2
    );
    return $this->update_attributes($params);
  }

  function remove_factura() {
    $params = array(
      'idfactura' => null,
      'idestado' => 'id_estado_anterior',
      'id_estado_anterior' => 2
    );
    return $this->update_attributes($params);
  }

  static function _delete($id) {
    if (! is_array($id)) $id = array($id);
    $id = implode(',', $id);
    DBManager::execute("BEGIN");
    $sql = "DELETE FROM items WHERE idguia IN ($id)";
    if (! DBManager::execute($sql)) return false;
    $sql = "DELETE FROM ".self::$table." WHERE id IN ($id)";
    if (! DBManager::execute($sql)) return false;
    return DBManager::execute("COMMIT");
  }

  static function all_by_rango($start, $end) {
    $sql = self::sql_base()." WHERE g.idcliente=cl.id AND co.id=g.idcontacto AND
cio.id=cl.idciudad AND cid.id=co.idciudad AND i.idguia=g.id AND
g.idestado!=6 AND g.id BETWEEN '$start' AND '$end'
GROUP BY g.id
ORDER BY g.id";
    return parent::build_resources($sql, __CLASS__);
  }

  static function all_by_usuario_and_fecha($id_usuario, $fecha, $include_printed = false) {
    $filter = $include_printed ? "" : "AND g.impresa='no'";
    $sql = self::sql_base()." WHERE g.idcliente=cl.id AND co.id=g.idcontacto AND
cio.id=cl.idciudad AND cid.id=co.idciudad AND i.idguia=g.id AND
g.fecha_recibido_mercancia='$fecha' $filter AND g.idestado!=6 AND
g.idusuario='$id_usuario'
GROUP BY g.id
ORDER BY g.id";
    return parent::build_resources($sql, __CLASS__);
  }

  static function all_by_cliente_and_fecha($id_cliente, $fecha, $include_printed = false) {
    $filtro = $include_printed ? '' : "AND g.impresa='no'";
    $sql = self::sql_base()." WHERE g.idcliente=cl.id AND co.id=g.idcontacto AND
cio.id=cl.idciudad AND cid.id=co.idciudad AND i.idguia=g.id AND
g.fecha_recibido_mercancia='$fecha' AND
g.idcliente='$id_cliente' AND g.idestado IN (1,7) $filtro
GROUP BY g.id
ORDER BY g.id";
    return parent::build_resources($sql, __CLASS__);
  }

  static function count_by_status($id_estado) {
    $sql = "SELECT COUNT(id) count FROM guias WHERE idestado='$id_estado'";
    return DBManager::count($sql);
  }

  static function count_from($date) {
    $sql = "SELECT COUNT(*) count FROM guias WHERE idestado!=6 AND fecha_recibido_mercancia >= '$date'";
    return DBManager::count($sql);
  }

  function change_status($id_estado) {
    $params = array(
      'id_estado_anterior' => 'idestado',
      'idestado' => $id_estado,
      'idplanilla' => null
    );
    return $this->update_attributes($params);
  }

  function resolucion() {
    $resolucion = Resolucion::find_by_tipo_and_numero('facturacion', $this->idfactura);
    if (! $resolucion) exit('No se encontró una resolución para la factura.');
    return $this->resolucion = $resolucion;
  }
}

                            
                            