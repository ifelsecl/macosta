<?php
class Cliente extends Base{

  static $table = 'clientes';

  static $attributes = array('id', 'nombre', 'primer_apellido', 'segundo_apellido',
    'tipo_identificacion', 'numero_identificacion', 'idformajuridica', 'idregimen',
    'email', 'sitioweb', 'telefono', 'telefono2', 'celular', 'porcentajeseguro',
    'direccion', 'restriccionpeso', 'descuento', 'idciudad', 'digito_verificacion',
    'id_vendedor', 'activo', 'clave', 'credito', 'nl', 'condicion_pago', 'numero_sede', 'sede');

  static $tipos_identificacion = array(
    'C' => 'Cédula',
    'N' => 'NIT',
    'T' => 'Tarjeta de Identidad',
    'E' => 'Cédula Extranjería',
    'P' => 'Pasaporte'
  );

  static $tipos_identificacion_corto = array(
    'C' => 'C.C.',
    'N' => 'NIT',
    'T' => 'T.I.',
    'E' => 'C.E.',
    'P' => 'Ppte'
  );

  static $regimenes = array(
    0 => 'No especificado',
    1 => 'Regimen Simplificado',
    2 => 'Regimen Comun'
  );

  static $formas_juridicas = array(
    0 => 'No especificado',
    1 => 'Empresa Unipersonal',
    2 => 'Sociedad Anonima Simple',
    3 => 'Sociedad Anonima',
    4 => 'Corporacion sin animo de lucro',
    5 => 'Fundacion sin animo de lucro',
    6 => 'Cia Ltda',
    7 => 'Sociedad Limitada',
    8 => 'Sociedad en Comandita por Acciones',
    9 => 'Sociedad en Comandita'
  );

  function __construct($params = array()) {
    $this->set_defaults();
    $this->set_attributes($params);
    $this->numero_identificacion_completo = number_format($this->numero_identificacion, 0, ',', '.');
    $this->nombre_completo = trim($this->nombre.' '.$this->primer_apellido.' '.$this->segundo_apellido);
    if ($this->tipo_identificacion == 'N') {
      $this->primer_apellido = '';
      $this->segundo_apellido = '';
      $this->numero_identificacion_completo .= '-'.$this->digito_verificacion;
    } else {
      $this->idregimen = 0;
      $this->idformajuridica = 0;
      $this->digito_verificacion = 0;
    }
  }

  function create() {
    $this->set_defaults();
    $this->set_attributes(array());
    $sql1 = "INSERT INTO ".self::$table."(tipo_identificacion, numero_identificacion, nombre,
primer_apellido, segundo_apellido, direccion, idciudad, telefono, email, sitioweb,
restriccionpeso, porcentajeseguro, idformajuridica, idregimen, descuento, digito_verificacion,
id_vendedor, activo, telefono2, celular, condicion_pago,numero_sede, sede) VALUES('$this->tipo_identificacion',
'$this->numero_identificacion','$this->nombre','$this->primer_apellido',
'$this->segundo_apellido', '$this->direccion','$this->idciudad', '$this->telefono',
'$this->email', '$this->sitioweb', '$this->restriccionpeso', '$this->porcentajeseguro',
'$this->idformajuridica', '$this->idregimen', '$this->descuento','$this->digito_verificacion',
'$this->id_vendedor', 'si', '$this->telefono2', '$this->celular', '$this->condicion_pago',
'$this->numero_sede', '$this->sede')";
    $sql2 = "SELECT LAST_INSERT_ID()";
    if (! DBManager::execute($sql1) or ! $result2 = DBManager::execute($sql2)) {
      return false;
    }
    if (! $row = mysql_fetch_array($result2)) return false;
    if (! isset($this->id) or empty($this->id)) $this->id = $row[0];
    return true;
  }

  function set_defaults() {
    if (! isset($this->id)) $this->id = null;
    if (! isset($this->nombre)) $this->nombre = '';
    if (! isset($this->primer_apellido)) $this->primer_apellido = '';
    if (! isset($this->segundo_apellido)) $this->segundo_apellido = '';
    if (! isset($this->direccion)) $this->direccion = '';
    if (! isset($this->telefono)) $this->telefono = '';
    if (! isset($this->telefono2)) $this->telefono2 = '';
    if (! isset($this->celular)) $this->celular = '';
    if (! isset($this->tipo_identificacion)) $this->tipo_identificacion = '';
    if (! isset($this->numero_identificacion)) $this->numero_identificacion = 0;
    if (! isset($this->digito_verificacion)) $this->digito_verificacion = 0;
    if (! isset($this->email)) $this->email = '';
    if (! isset($this->restriccionpeso)) $this->restriccionpeso = 30;
    if (! isset($this->porcentajeseguro)) $this->porcentajeseguro = 1;
    if (! isset($this->descuento)) $this->descuento = 0;
    if (! isset($this->id_vendedor)) $this->id_vendedor = 1;
    if (! isset($this->condicion_pago)) $this->condicion_pago = 15;
    if (! isset($this->numero_sede)) $this->numero_sede= '';
    if (! isset($this->sede)) $this->sede= '';
  }

  function set_attributes($params) {
    if (! empty($params)) parent::__construct($params);
    $this->email = strtolower($this->email);
    $this->nombre = strtoupper($this->nombre);
    if ($this->tipo_identificacion == 'N') {
      $this->primer_apellido = '';
      $this->segundo_apellido = '';
    } else {
      $this->primer_apellido = strtoupper($this->primer_apellido);
      $this->segundo_apellido = strtoupper($this->segundo_apellido);
    }
    $this->direccion = strtoupper($this->direccion);
  }

  function update($params) {
    $this->set_attributes($params);
    $vars = array('nombre', 'primer_apellido', 'segundo_apellido', 'direccion');
    foreach ($vars as $key) {
      $this->$key = DBManager::escape($this->$key);
    }
    $sql = "UPDATE ".self::$table." SET
tipo_identificacion='$this->tipo_identificacion',
numero_identificacion='$this->numero_identificacion', nombre='$this->nombre',
primer_apellido='$this->primer_apellido',
segundo_apellido='$this->segundo_apellido', direccion='$this->direccion',
idciudad='$this->idciudad', telefono='$this->telefono', email='$this->email',
sitioweb='$this->sitioweb', restriccionpeso='$this->restriccionpeso',
porcentajeseguro='$this->porcentajeseguro',
idformajuridica='$this->idformajuridica', idregimen='$this->idregimen',
descuento='$this->descuento', digito_verificacion='$this->digito_verificacion',
id_vendedor='$this->id_vendedor', telefono2='$this->telefono2',
celular='$this->celular', nl='$this->nl', condicion_pago='$this->condicion_pago',
numero_sede='$this->numero_sede', sede='$this->sede'
WHERE id = '$this->id'";
    return DBManager::execute($sql);
  }

  function update_attributes($params) {
    $this->set_attributes($params);
    $sql = '';
    foreach ($params as $key => $value) {
      $sql .= $key."='$value',";
    }
    $sql = substr($sql, 0, -1);
    $sql = 'UPDATE '.self::$table.' SET '.$sql.' WHERE id="'.$this->id.'"';
    return DBManager::execute($sql);
  }

  function lista_precios($return_sql = false, $ciudad_origen = false, $ciudad_destino = false) {
    $filtro_ciudad_origen = $ciudad_origen ? "AND cio.nombre LIKE '%".DBManager::escape($ciudad_origen)."%'" : '';
    $filtro_ciudad_destino = $ciudad_destino ? "AND cid.nombre LIKE '%".DBManager::escape($ciudad_destino)."%'" : '';
    $sql = "SELECT l.*, cio.nombre ciudadorigen, cio.id idciudadorigen,
cid.nombre ciudaddestino, cid.id idciudaddestino, e.nombre embalaje, e.id idembalaje,
depo.nombre departamento_origen, depd.nombre departamento_destino, e.tipo_cobro
FROM listaprecios l, ".Ciudad::$table." cio, departamentos depo, departamentos depd, ".Ciudad::$table." cid, embalajes e
WHERE l.idembalaje=e.id AND cio.id=l.idciudadorigen AND cid.id=l.idciudaddestino
AND depo.id=cio.iddepartamento AND cid.iddepartamento=depd.id
AND l.idcliente = $this->id $filtro_ciudad_origen $filtro_ciudad_destino";
    if ($return_sql) return $sql;
    return $this->lista_precios = parent::build_resources($sql);
  }

  /**
   * Agrega un nuevo Cliente.
   */
  function Agregar($tipo_identificacion, $numero_identificacion, $nombre, $primer_apellido,
        $segundo_apellido, $direccion,
        $id_ciudad, $telefono, $email, $sitio_web, $restriccion_peso,
        $porcentaje_seguro, $id_forma_juridica, $id_regimen, $descuento,
        $digito_verificacion, $id_vendedor, $clave='', $telefono2='', $celular='', $activo='si', $numero_sede='', $sede='') {
    $direccion = strtoupper(mysql_real_escape_string(trim($direccion)));
    $direccion = str_replace('#', 'No', $direccion);
    $nombre = strtoupper(mysql_real_escape_string(trim($nombre)));
    $primer_apellido = strtoupper(mysql_real_escape_string(trim($primer_apellido)));
    $segundo_apellido = strtoupper(mysql_real_escape_string(trim($segundo_apellido)));
    $email = strtolower(mysql_real_escape_string(trim($email)));
    $telefono = mysql_real_escape_string(trim($telefono));
    $telefono2 = mysql_real_escape_string(trim($telefono2));
    $celular = mysql_real_escape_string(trim($celular));
    $sitio_web = mysql_real_escape_string(trim($sitio_web));
    $numero_sede = mysql_real_escape_string(trim($numero_sede));
    $sede = strtolower(mysql_real_escape_string(trim($sede)));
    $activo = strtolower($activo);
    $clave = md5($clave);
    $tipo_identificacion = strtoupper($tipo_identificacion);
    if ($tipo_identificacion!='N') {
      $id_forma_juridica = 0;
      $id_regimen = 0;
    }
    $query="INSERT INTO ".self::$table." (tipo_identificacion, numero_identificacion, nombre, primer_apellido, segundo_apellido,
  direccion, idciudad, telefono, email, sitioweb, restriccionpeso, porcentajeseguro, numero_sede, sede,
  idformajuridica, idregimen, descuento, digito_verificacion, id_vendedor, clave, activo, telefono2, celular) VALUES(
  '$tipo_identificacion',$numero_identificacion,'$nombre','$primer_apellido','$segundo_apellido','$direccion',$id_ciudad,
  '$telefono', '$email', '$sitio_web', '$restriccion_peso', '$porcentaje_seguro', $numero_sede, $sede,
  $id_forma_juridica,$id_regimen,'$descuento','$digito_verificacion', '$id_vendedor', '$clave', '$activo', '$telefono2', '$celular')";
    return DBManager::execute($query);
  }

  /**
   * Agrega el precio de un envío a una ciudad destino, desde una ciudad
   * origen con un tipo de embalaje.
   *
   * @author  Edgar Ortega Ramírez
   * @since Marzo 30, 2011
   */
  function AgregarPrecio($id_cliente, $id_ciudad_origen, $id_ciudad_destino, $id_embalaje, $precio, $precio_kilo, $precio_kilovol, $seguro, $descuento3, $descuento6, $descuento8) {
    $sql = "INSERT INTO listaprecios (idcliente,idciudadorigen,idciudaddestino,idembalaje,precio, precio_kilo, precio_kilovol, seguro, descuento3, descuento6, descuento8) VALUES
($id_cliente,$id_ciudad_origen,$id_ciudad_destino,$id_embalaje,'$precio', '$precio_kilo', '$precio_kilovol', '$seguro', '$descuento3', '$descuento6', '$descuento8')";
    return DBManager::execute($sql);
  }

  /**
   * Borra un precio de la lista de precios de un cliente.
   *
   * @param int $idcliente el ID del cliente.
   * @param int $idciudadorigen el ID de la ciudad origen.
   * @param int $idciudaddestino el ID de la ciudad destino.
   * @param int $idembalaje el ID del embalaje.
   */
  function BorrarPrecio($idcliente,$idciudadorigen,$idciudaddestino,$idembalaje) {
    $query="DELETE FROM listaprecios WHERE idcliente=".$idcliente." AND idciudadorigen=".$idciudadorigen." AND idciudaddestino=".$idciudaddestino." AND idembalaje=".$idembalaje;
    return DBManager::execute($query);
  }

  /**
   * Selecciona todos los datos de todos los clientes, ésta función es usada para exportar.
   *
   * @param string $formato "XLS" o "CSV", el formato indica que campos serán retornados.
   * @since Abril 21, 2011
   * @author  Edgar Ortega Ramirez
   * @version 1.2 - Julio 1, 2011
   */
  function Exportar($formato) {
    if (strtoupper($formato)=="XLS") {
      $sql = "SELECT cl.*, ci.nombre nombreciudad, de.nombre nombredepartamento,
fj.nombre formajuridica, re.nombre regimen
FROM ".self::$table." cl, ".Ciudad::$table." ci, departamentos de,formasjuridicas fj, regimenes re
WHERE cl.idciudad=ci.id AND ci.iddepartamento=de.id AND fj.id=cl.idformajuridica
AND cl.idregimen=re.id
ORDER BY id ASC";
    } else {
      $sql="SELECT * FROM ".self::$table." ORDER BY id ASC";
    }
    return DBManager::execute($sql);
  }

  /**
   * Selecciona todos los datos de la lista de precios de un cliente, esta
   * función es usada para exportar.
   *
   * @param   int $id el ID del cliente.
   * @param string $formato 'CSV' o 'XLS', indica que campos serán retornados, si no se indica ninguna se usara 'CSV'.
   * @since Abril 24, 2011
   * @version 1.2 - Julio 1, 2011
   */
  function ExportarListaPrecios($id,$formato) {
    if (strtoupper($formato)=="XLS") {
      $query="SELECT l.*, cio.nombre ciudadorigen, cio.id idciudadorigen,
          cid.nombre ciudaddestino, cid.id idciudaddestino, e.nombre embalaje,
          e.id idembalaje, e.tipo_cobro
        FROM listaprecios l, ".Ciudad::$table." cio, ".Ciudad::$table." cid, embalajes e
          WHERE l.idembalaje=e.id AND cio.id=l.idciudadorigen
          AND cid.id=l.idciudaddestino AND idcliente=$id";
    } else {
      $query = "SELECT * FROM listaprecios";
      if (! is_null($id)) $query .= " WHERE idcliente=$id";
    }
    return DBManager::execute($query);
  }

  static function autocomplete_for($option, $name) {
    $name = DBManager::escape($name);
    if ($option == 'nombre') {
      $filter = "(CONCAT(c.nombre, ' ', c.primer_apellido, ' ', c.segundo_apellido) LIKE '%$name%')";
    } elseif ($option == 'numero_identificacion') {
      $filter = "c.numero_identificacion LIKE '%$name%'";
    } elseif ($option == 'direccion') {
      $filter = "c.direccion LIKE '%$name%'";
    } else {
      $filter = "c.id='$name'";
    }
    $sql = "SELECT c.*, ci.nombre ciudad_nombre
FROM ".self::$table." c, ".Ciudad::$table." ci
WHERE c.idciudad=ci.id AND c.activo='si' AND $filter
LIMIT 0, 100";
    $result = DBManager::execute($sql);
    $datos = array();
    while ($row = mysql_fetch_assoc($result)) {
      $n = trim($row['nombre'].' '.$row['primer_apellido'].' '.$row['segundo_apellido']);
      $datos[] = array(
        "id" => $row['id'],
        "value" => $n." -".$row['ciudad_nombre'].'-'.$row['direccion'],
        "idciudad" => $row['idciudad'],
        "porcentajeseguro" => $row['porcentajeseguro'],
        'nombre_ciudad' => $row['ciudad_nombre'],
        'numero_identificacion' => $row['numero_identificacion'],
        "restriccionpeso" => $row['restriccionpeso'],
        'direccion' => $row['direccion'],
        'telefono' => $row['telefono'],
        'nombre' => $n,
        'descuento' => $row['descuento'],
        'numero_identificacion' => $row['numero_identificacion']
      );
    }
    return json_encode($datos);
  }

  /**
   * Anula un cliente sin borrarlo de la base de datos.
   *
   * @param   int $id el ID del cliente.
   * @since Junio 22, 2011
   */
  function Anular($id) {
    $fecha=date("Y-m-d H:i:s");
    $query="UPDATE ".self::$table." SET activo='no', fechamodificacion='$fecha' WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Actualiza el valor del flete, valor del seguro y el total de una factura.
   *
   * @param   int $id el ID de la factura.
   * @param   double $valorflete
   * @param   double $valorseguro
   * @param   double $total
   * @param   double $descuento el porcentaje de descuento
   * @param   string $modo 'sumar' o 'restar' los antiguos valores con los especificados.
   * @since Mayo 4, 2011
   * @author  Edgar Ortega Ramírez
   */
  function ActualizarValoresFactura($id,$valorflete,$valorseguro,$total,$descuento,$modo) {
    $query = "SELECT * FROM facturas WHERE id=$id";
    $result = DBManager::execute($query);
    $row = mysql_fetch_array($result);
    if ($modo == "sumar") { //sumar los anteriores valores con los nuevos.
      $valorflete += $row['valorflete'];
      $valorseguro += $row['valorseguro'];
      $total += $row['total'];
    } elseif ($modo == "restar") { //restar los anteriores valores con los nuevos.
      $valorflete = $row['valorflete'] - $valorflete;
      $valorseguro = $row['valorseguro'] - $valorseguro;
      $total = $row['total'] - $total;
    }
    $desc = round($valorflete * $descuento / 100);
    $query = "UPDATE facturas
SET valorflete='$valorflete',valorseguro='$valorseguro',total='$total',descuento='$desc'
WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Activa un cliente que ha sido anulado.
   *
   * @param   int $id el ID del cliente.
   * @since Junio 22, 2011
   */
  function Activar($id) {
    $fecha=date("Y-m-d H:i:s");
    $query="UPDATE clientes SET activo='si', fechamodificacion='$fecha' WHERE id=$id";
    return DBManager::execute($query);
  }

  function delete_lista_precios() {
    $sql = "DELETE FROM listaprecios WHERE idcliente=$this->id";
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

  static function credit($return_sql = false) {
    $q = "SELECT c.*, ci.nombre ciudad FROM ".self::$table." c, ".Ciudad::$table." ci WHERE ci.id=c.idciudad AND c.credito='SI' LIMIT 200";
    return DBManager::execute($q);
  }

  /**
   * Selecciona los cambios realizados en la lista de precios de un cliente.
   * @since Mayo 29, 2012
   */
  function HistorialListaPrecios($id_cliente) {
    $q="SELECT * FROM listaprecios_historial WHERE id_cliente='$id_cliente' ORDER BY id DESC LIMIT 0,4";
    return DBManager::execute($q);
  }

  /**
   * Realiza aumentos o disminuciones por porcentaje a la lista de precios de un cliente.
   * @since Mayo 29, 2012
   */
  function ModificarListaPrecios($id_cliente, $porcentaje, $modo) {
    $p=1+($porcentaje/100);
    $p=str_replace(',', '.', $p);

    $q="SELECT l.*, e.tipo_cobro FROM listaprecios l, embalajes e
WHERE l.idembalaje=e.id AND idcliente='$id_cliente'";
    $r=DBManager::execute($q);
    while ($precio=mysql_fetch_object($r)) {
      if ($modo=='Aumento') {
        $precio->precio=$precio->precio*$p;
      } else {
        $precio->precio=$precio->precio/$p;
      }
      $precio->precio=round($precio->precio);
      if ($precio->tipo_cobro=='Kilo' or $precio->tipo_cobro=='Kilo Volumen') {
        $kilo=0;
      } else {
        $centena=intval(substr($precio->precio, -2));
        if ($centena>=50) {
          $precio->precio=$precio->precio+(100-$centena);
        } else {
          $precio->precio=$precio->precio-$centena;
        }
        $kilo=round($precio->precio/30);
      }
      $q="UPDATE listaprecios SET precio='$precio->precio',
precio_kilo='$kilo', precio_kilovol='$kilo'
WHERE idcliente='$id_cliente' AND idciudadorigen='$precio->idciudadorigen'
AND idciudaddestino='$precio->idciudaddestino' AND idembalaje='$precio->idembalaje'";
      DBManager::execute($q);
    }
    return $this->RegistrarModificacionListaPrecios($id_cliente, $porcentaje, $modo);
  }

  /**
   * Permite registrar las modificaciones que sean realizadas a una lista de precios.
   * @since Mayo 29, 2012
   */
  function RegistrarModificacionListaPrecios($id_cliente, $porcentaje, $modo) {
    $fecha = date('Y-m-d H:i:s');
    $usuario = $_SESSION['username'];
    $sql = "INSERT INTO listaprecios_historial(id_cliente, porcentaje, modo, fecha, usuario)
VALUES('$id_cliente','$porcentaje','$modo','$fecha', '$usuario')";
    return DBManager::execute($sql);
  }

  /**
   * Actualiza la información básica de un cliente. Esta función es utilizada en la parte de los clientes.
   */
  function EditarBasico($id, $direccion, $telefono, $telefono2, $celular, $email, $sitioweb, $numero_sede, $sede) {
    $f=date('Y-m-d H:i:s');
    $e_direccion='';
    $e_telefono='';
    $e_telefono2='';
    $e_celular='';
    $e_email='';
    $e_sitioweb='';
    $e_numero_sede='';
    $e_sede='';    
    $sep=FALSE;
    if ($direccion) {
      $e_direccion="direccion='$direccion'";
      $sep=TRUE;
    }
    if ($telefono) {
      $e_telefono="telefono='$telefono'";
      if ($sep) $e_telefono=','.$e_telefono;
      $sep=TRUE;
    }
    if ($telefono2) {
      $e_telefono2="telefono2='$telefono2'";
      if ($sep) $e_telefono2=','.$e_telefono2;
      $sep=TRUE;
    }
    if ($celular) {
      $e_celular="celular='$celular'";
      if ($sep) $e_celular=','.$e_celular;
      $sep=TRUE;
    }
    if ($email) {
      $e_email="email='$email'";
      if ($sep) $e_email=",".$e_email;
      $sep=TRUE;
    }
    if ($numero_sede) {
      $e_numero_sede="numero_sede='$numero_sede'";
      $sep=TRUE;
    }
    if ($sede) {
      $e_sede="sede='$sede'";
      $sep=TRUE;
    }
    if ($sitioweb) {
      $e_sitioweb="sitioweb='$sitioweb'";
      if ($sep) $e_sitioweb=','.$e_sitioweb;
    }
    $sql = "UPDATE ".self::$table." SET $e_direccion $e_telefono $e_telefono2 $e_celular $e_email  $e_sitioweb,fechamodificacion='$f' WHERE id=$id";
    return DBManager::execute($sql);
  }

  function add_contact($id_contacto) {
    $sql = "INSERT INTO clientes_contactos VALUES($this->id, $id_contacto) ON DUPLICATE KEY UPDATE id_contacto = VALUES(id_contacto)";
    return DBManager::execute($sql);
  }

  static function mas_frecuentes($mes, $limit) {
    $ciud = array('nombres' => array(), 'cantidades' => array());
    $t = '';
    if ($mes == 'ACTUAL') {
      $inicio = date('Y-m-d', strtotime('first day of this month'));
      $fin = date('Y-m-d', strtotime('last day of this month'));
      $t = ucfirst(strftime('%B', strtotime('this month')));
    }
    if ($mes == 'ANTERIOR') {
      $inicio = date('Y-m-d', strtotime('first day of last month'));
      $fin = date('Y-m-d', strtotime('last day of last month'));
      $t = strftime('%B', strtotime('last month'));
    }
    if ($mes == '3MESES') {
      $s=strtotime('-3 months');
      $n=date('F Y', $s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
    }
    if ($mes == '6MESES') {
      $s=strtotime('-6 months');
      $n=date('F Y', $s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
    }
    if ($mes == '12MESES') {
      $s=strtotime('-1 year');
      $n=date('F Y', $s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
    }
    $sql = "SELECT COUNT(*) AS cantidad, cl.id, cl.tipo_identificacion AS ti,
cl.nombre AS nombre, cl.primer_apellido AS primer_apellido
FROM guias g, ".self::$table." cl
WHERE g.idcliente=cl.id AND (g.idestado!=6 AND g.idestado!=7)
AND (g.fecha_recibido_mercancia BETWEEN '$inicio' AND '$fin')
GROUP BY cl.id ORDER BY cantidad DESC LIMIT 0, $limit";
    $result = DBManager::execute($sql);
    if (DBManager::rows_count($result) != 0) {
      while ($f = mysql_fetch_assoc($result)) {
        $ciud['nombres'][] = trim($f['nombre'].' '.$f['primer_apellido']);
        $ciud['cantidades'][] = intval($f['cantidad']);
      }
    }
    $ciud['texto'] = $t;
    return $ciud;
  }

  static function contactos_mas_frecuentes($cliente, $mes, $limit) {
    $ciud = array('nombres' => array(), 'cantidades' => array());
    $t = '';
    if ($mes == 'ACTUAL') {
      $inicio = date('Y-m-d', strtotime('first day of this month'));
      $fin = date('Y-m-d', strtotime('last day of this month'));
      $t = ucfirst(strftime('%B', strtotime('this month')));
    }
    if ($mes == 'ANTERIOR') {
      $inicio = date('Y-m-d', strtotime('first day of last month'));
      $fin = date('Y-m-d', strtotime('last day of last month'));
      $t = ucfirst(strftime('%B', strtotime('last month')));
    }
    if ($mes == '3MESES') {
      $s = strtotime('-3 months');
      $n = date('F Y', $s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
    }
    if ($mes == '6MESES') {
      $s = strtotime('-6 months');
      $n = date('F Y', $s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
    }
    if ($mes == '12MESES') {
      $s = strtotime('-1 year');
      $n = date('F Y', $s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
    }
    $sql = "SELECT COUNT( co.id ) cantidad, co.tipo_identificacion co_ti,
co.nombre co_n, co.primer_apellido co_pa
FROM guias g, ".self::$table." co
WHERE (g.idestado!=6 AND g.idestado!=7)
AND g.fecha_recibido_mercancia BETWEEN '$inicio' AND '$fin'
AND g.idcontacto=co.id AND g.idcliente=$cliente
GROUP BY co.id ORDER BY cantidad DESC LIMIT 0, $limit";
    $result = DBManager::execute($sql);
    if (DBManager::rows_count($result) != 0) {
      while ($f = mysql_fetch_assoc($result)) {
        $cl = trim($f['co_n'].' '.$f['co_pa']);
        $ciud['nombres'][] = $cl;
        $ciud['cantidades'][] = intval($f['cantidad']);
      }
    }
    $ciud['texto'] = $t;
    return $ciud;
  }

  function ciudad() {
    return $this->ciudad = Ciudad::find($this->idciudad);
  }

  function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT cl.*, ci.nombre ciudad_nombre, de.nombre departamento_nombre
FROM ".self::$table." cl, ".Ciudad::$table." ci, ".Departamento::$table." de
WHERE cl.idciudad=ci.id AND de.id=ci.iddepartamento AND cl.id='$id'";
    $result = DBManager::execute($sql);
    if (DBManager::rows_count($result) == 0) return false;
    self::__construct( mysql_fetch_assoc($result));
    return $this;
  }

  static function find_by_numero_identificacion_and_clave($numero_identificacion, $clave) {
    $numero_identificacion = DBManager::escape(htmlspecialchars($numero_identificacion));
    $clave = md5($clave);
    $sql = "SELECT * FROM ".self::$table." WHERE numero_identificacion='$numero_identificacion' AND clave = '$clave'";
    $result = DBManager::execute($sql);
    if (DBManager::rows_count($result) == 0) return false;
    return new self(mysql_fetch_assoc($result));
  }

  function forma_juridica() {
    return self::$formas_juridicas[$this->idformajuridica];
  }

  function regimen() {
    return self::$regimenes[$this->idregimen];
  }

  function contactos($limit = null) {
    $limit = $limit ? "LIMIT 0, $limit" : '';
    $sql = "SELECT co.*, ciu.nombre ciudad_nombre FROM ".self::$table." co, clientes_contactos cc, ".Ciudad::$table." ciu
WHERE co.id = cc.id_contacto AND cc.id_cliente = $this->id AND ciu.id = co.idciudad $limit";
    return $this->contactos = parent::build_resources($sql, __CLASS__);
  }

  function nombre_completo() {
    return trim($this->nombre.' '.$this->primer_apellido.' '.$this->segundo_apellido);
  }

  static function search_contactos($id, $params, $return_sql = false) {
    foreach ($params as $key => $value) $params[$key] = DBManager::escape($value);
    $filtro_nombre = '';
    $filtro_numero_identificacion = '';
    $filtro_direccion = '';
    if (isset($params['nombre']) and ! empty($params['nombre'])) {
      $filtro_nombre = "AND (CONCAT_WS(' ', co.nombre, co.primer_apellido, co.segundo_apellido) LIKE '%".$params['nombre']."%')";
    }
    if (isset($params['numero_identificacion']) and ! empty($params['numero_identificacion'])) {
      $filtro_numero_identificacion = "AND co.numero_identificacion LIKE '%".$params['numero_identificacion']."%'";
    }
    if (isset($params['direccion']) and ! empty($params['direccion'])) {
      $filtro_direccion = "AND co.direccion LIKE '%".$params['direccion']."%'";
    }
    $sql = "SELECT co.*, ciu.nombre ciudad_nombre
FROM ".self::$table." co, clientes_contactos cc, ".Ciudad::$table." ciu
WHERE co.id = cc.id_contacto AND ciu.id = co.idciudad AND cc.id_cliente=$id
$filtro_nombre $filtro_numero_identificacion $filtro_direccion";
    if ($return_sql) return $sql;
    return $this->contactos = parent::build_resources($sql, __CLASS__);
  }


  function history() {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE id_modulo = '$this->id' AND modulo = 'Clientes' ORDER BY fecha DESC LIMIT 100";
    return $this->history = parent::build_resources($sql);
  }

  function change_password($password) {
    $password = md5($password);
    $sql = "UPDATE clientes SET clave='$password' WHERE id='$this->id'";
    return DBManager::execute($sql);
  }

  function deactivate() {
    $fecha = date("Y-m-d H:i:s");
    $query = "UPDATE ".self::$table." SET activo='no', fechamodificacion='$fecha' WHERE id=$this->id";
    return DBManager::execute($query);
  }

  function activate() {
    $fecha = date("Y-m-d H:i:s");
    $query = "UPDATE ".self::$table." SET activo='si', fechamodificacion='$fecha' WHERE id=$this->id";
    return DBManager::execute($query);
  }

  static function all($which = 'todos', $return_sql = false) {
    $where = $which == 'activos' ? "AND activo='si'" : '';
    $sql = "SELECT cl.*, ci.nombre ciudad_nombre FROM ".self::$table." cl, ".Ciudad::$table." ci WHERE cl.idciudad=ci.id $where";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function companies($return_sql = false) {
    $sql = "SELECT cl.* FROM ".self::$table." cl WHERE cl.tipo_identificacion='N'";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function search($params, $return_sql = false) {
    foreach ($params as $key => $value) $params[$key] = DBManager::escape($value);
    $filtro_nombre = '';
    $filtro_numero_identificacion = '';
    $filtro_ciudad = '';
    $filtro_direccion = '';
    $filtro_id = '';
    if (! empty($params['nombre'])) {
      $filtro_nombre = "AND (CONCAT_WS(' ', cl.nombre, cl.primer_apellido, cl.segundo_apellido) LIKE '%".$params['nombre']."%')";
    }
    if (! empty($params['numero_identificacion'])) {
      $filtro_numero_identificacion = "AND cl.numero_identificacion LIKE '%".$params['numero_identificacion']."%'";
    }
    if (! empty($params['ciudad'])) {
      $filtro_ciudad = "AND ci.nombre LIKE '%".$params['ciudad']."%'";
    }
    if (! empty($params['direccion'])) {
      $filtro_direccion = "AND cl.direccion LIKE '%".$params['direccion']."%'";
    }
    if (! empty($params['id'])) {
      $filtro_id = "AND cl.id = '{$params['id']}'";
    }
    $sql = "SELECT cl.*, ci.nombre ciudad_nombre FROM ".self::$table." cl, ".Ciudad::$table." ci
WHERE cl.idciudad=ci.id $filtro_nombre $filtro_numero_identificacion $filtro_ciudad $filtro_direccion $filtro_id";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function autocomplete($term) {
    $term = DBManager::escape($term);
    $sql = "SELECT c.*, ci.nombre ciudad_nombre FROM ".self::$table." c, ".Ciudad::$table." ci
WHERE c.idciudad=ci.id AND c.activo='si' AND
(CONCAT(c.nombre, ' ', c.primer_apellido, ' ', c.segundo_apellido) LIKE '%$term%'
OR c.numero_identificacion LIKE '%$term%') LIMIT 0, 100";
    $result = DBManager::execute($sql);
    $datos = array();
    while ($row = mysql_fetch_assoc($result)) {
      $n = trim($row['nombre'].' '.$row['primer_apellido'].' '.$row['segundo_apellido']);
      $datos[] = array(
        "id" => $row['id'],
        "value" => $n." -".$row['ciudad_nombre'].'-'.$row['direccion'],
        "idciudad" => $row['idciudad'],
        "porcentajeseguro" => $row['porcentajeseguro'],
        'nombre_ciudad' => $row['ciudad_nombre'],
        'numero_identificacion' => $row['numero_identificacion'],
        "numero_sede" => $row['numero_sede'],
        "sede" => $row['sede'],
        "restriccionpeso" => $row['restriccionpeso'],
        'direccion' => $row['direccion'],
        'telefono' => $row['telefono'],
        'nombre' => $n,
        'descuento' => $row['descuento'],
        'numero_identificacion' => $row['numero_identificacion'],
        'digito_verificacion' => $row['digito_verificacion'],
        'condicion_pago' => $row['condicion_pago']
      );
    }
    return json_encode($datos);
  }

  function delete($id_cliente_nuevo) {
    $sql = "DELETE FROM clientes_contactos WHERE id_cliente=$this->id OR id_contacto=$this->id";
    if (! DBManager::execute($sql)) return false;
    $sql = "DELETE FROM listaprecios WHERE idcliente=$this->id";
    if (! DBManager::execute($sql)) return false;
    $sql = "UPDATE ".Guia::$table." SET idcliente=$id_cliente_nuevo WHERE idcliente=$this->id";
    if (! DBManager::execute($sql)) return false;
    $sql = "UPDATE ".Guia::$table." SET idcontacto=$id_cliente_nuevo WHERE idcontacto=$this->id";
    if (! DBManager::execute($sql)) return false;

    $sql = "DELETE FROM ".self::$table." WHERE id=$this->id";
    return DBManager::execute($sql);
  }

  function has_invoices() {
    $sql = "SELECT COUNT(*) as count FROM facturas WHERE idcliente='$this->id'";
    return DBManager::count($sql) > 0;
  }

  function facturas($return_sql = false) {
    $sql = "SELECT * FROM facturas WHERE idcliente=$this->id ORDER BY id DESC";
    if ($return_sql) return $sql;
    return $result = DBManager::execute($sql);
  }

  function unpaid_invoices() {
    return Factura::unpaid($this->id);
  }

  function update_seguro_in_lista_precios() {
    $sql = "UPDATE listaprecios SET seguro='".$this->porcentajeseguro."' WHERE idcliente=$this->id";
    DBManager::execute($sql);
  }

  function tipo_identificacion_corto() {
    return self::$tipos_identificacion_corto[$this->tipo_identificacion];
  }

  function tipo_identificacion() {
    return self::$tipos_identificacion[$this->tipo_identificacion];
  }

  function telefono_completo() {
    $str = $this->telefono;
    if (! empty($this->telefono2)) {
      if (! empty($str)) $str .= ' - ';
      $str .= $this->telefono2;
    }
    if (! empty($this->celular)) {
      if (! empty($str)) $str .= ' - ';
      $str .= $this->celular;
    }
    return $str;
  }

  function primer_telefono() {
    if (! empty($this->telefono)) return $this->telefono;
    if (! empty($this->telefono2)) return $this->telefono2;
    if (! empty($this->celular)) return $this->celular;
  }
}
