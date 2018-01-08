<?php
require_once "DBManager.php";
require_once "Cliente.php";
require_once "Contacto.php";

/**
 * Clase Guias contiene las propiedas de una guía y metodos para
 * crear nuevas guias, borrar, anular, buscar, entre otros.
 * @author Edgar Ortega
 *
 */
class Guias {

  /**
   * Mantiene la conexión con la base de datos.
   * @var DBManager
   */
  var $con;

  public function __set($name, $value) {
    if (gettype($value) == 'string') $value = addslashes($value);
    $this->$name = $value;
  }

  public function __get($name) {
    return $this->$name;
  }

  /**
   * Crea un nuevo objeto 'Guía'.
   */
  function __construct($params = array()) {
    foreach ($params as $name => $value) {
      $this->$name = $value;
    }
    $this->con = new DBManager;
  }

  /**
   * Agrega una nueva guia sin items.
   *
   * @param double $valor_declarado
   * @param double $porcentaje_seguro
   * @param string $foma_pago
   * @param string $observacion
   * @param double $total
   * @param int $unidad_medida
   * @param int $unidad_empaque
   * @param int $naturaleza_carga
   * @param string $documento_cliente
   * @param int $id_cliente
   * @param int $id_contacto
   * @param int $id_usuario el ID del usuario que creó la guía.
   * @param int $numero número de la guía anterior.
   * @param int $id_estado
   * @since Julio 12, 2011
   */
  function Agregar($valor_declarado, $valor_seguro,
          $forma_pago, $observacion, $total, $unidad_medida, $unidad_empaque,
          $naturaleza_carga, $documento_cliente, $id_cliente, $id_contacto,
          $id_usuario, $numero, $peso_contenedor, $propietario='Remitente',
          $recogida,$id_tipo_operacion, $id_estado=1) {
    $año = date("Y");
    $fecha_recibido_mercancia = date('Y-m-d');

    $query1="INSERT INTO guias (año, fecha_recibido_mercancia,
valordeclarado, valorseguro, formapago, observacion, total, unidadmedida, empaque,
naturaleza, documentocliente, idcliente, idcontacto, idestado,
idusuario, numero, propietario, recogida, id_tipo_operacion) VALUES(
'$año', '$fecha_recibido_mercancia', '$valor_declarado',
'$valor_seguro', '$forma_pago','$observacion', '$total', '$unidad_medida',
'$unidad_empaque', '$naturaleza_carga','$documento_cliente',
'$id_cliente','$id_contacto', '$id_estado', '$id_usuario', '$numero',
'$propietario', '$recogida', '$id_tipo_operacion')";
    $query2="SELECT LAST_INSERT_ID()";
    if (!DBManager::execute($query1)) return false;
    if (!$result2=DBManager::execute($query2)) return false;

    if ($row=mysql_fetch_array($result2)) {
      return $row[0];
    } else {
      return false;
    }
  }

  /**
   * Edita una guía.
   *
   * @param int $id
   * @param int $unidad_medida
   * @param int $unidad_empaque
   * @param int $naturaleza_carga
   * @param int $documento_cliente
   * @param date $fecha_entrega
   * @param string $forma_pago 'FLETE AL COBRO', 'CREDITO' o 'CONTADO'.
   * @param string $observacion
   * @param double $total
   * @param int $id_estado
   * @param int $id_usuario
   * @param int $numero número de la guía anterior.
   * @since Julio 21, 2011
   */
  function Editar($id, $id_cliente, $id_contacto, $unidad_medida, $unidad_empaque, $naturaleza_carga, $valor_declarado,
          $valorseguro, $documento_cliente, $fecha_entrega, $forma_pago, $observacion,
          $total, $id_estado, $id_usuario, $id_razon_devolucion, $numero, $edicion, $recogida) {
    $filtro = '';
    $usuario = $_SESSION['username'];
    $fecha = date('Y-m-d h:i:s a');
    if ($id_estado==4)
      $cambios = "marcó la guía como ENTREGADA.<br />";
    elseif ($id_estado==5) {
      //$filtro = "idplanilla=NULL,";
      $cambios = "indicó que la guía fue DEVUELTA.<br />";
    } elseif ($id_estado==6) {
      $filtro = ", idplanilla=NULL";
      $cambios = "anuló la guía.<br />";
    } elseif ($id_estado==1) {
      $filtro = ", idplanilla=NULL";
      $cambios = "editó la guía (BODEGA).<br />";
    } elseif ($id_estado==3) {
      $cambios = "editó la guía (TRANSITO).<br />";
    } else {
      $cambios = "editó la guía.<br />";
    }
    $cambios = "($fecha) <b>$usuario</b> ".$cambios;
    $query="UPDATE guias SET unidadmedida=$unidad_medida, empaque=$unidad_empaque,
naturaleza=$naturaleza_carga, documentocliente='$documento_cliente',
fechaentrega='$fecha_entrega', formapago='$forma_pago', observacion='$observacion',
idestado=$id_estado,  id_razon_devolucion='$id_razon_devolucion',
recogida='$recogida', idcliente='$id_cliente', idcontacto='$id_contacto',
numero='$numero', valordeclarado='$valor_declarado', valorseguro='$valorseguro',
total='$total', edicion='$edicion' $filtro
WHERE id=$id";
    return DBManager::execute($query);
  }

  function Editar2($id, $id_contacto, $valor_declarado, $valor_seguro, $documento_cliente, $forma_pago, $observacion, $propietario, $total = false) {
    $documento_cliente = addslashes($documento_cliente);
    $e_fp = $forma_pago ? "formapago='$forma_pago'," : '';
    $e_total = $total ? ", total = '$total'" : '';

    if ($documento_cliente) $e_dc = "documentocliente='$documento_cliente',";
    else $e_dc = '';


    $sql = "UPDATE guias SET
$e_dc $e_fp propietario='$propietario',
observacion='$observacion', edicion='0', idcontacto='$id_contacto',
valordeclarado='$valor_declarado', valorseguro='$valor_seguro' $e_total
WHERE id=$id";
    return DBManager::execute($sql);
  }
  /**
   * Obtiene toda la información de una guía.
   *
   * @param   int $id el ID de la guía.
   * @since   Marzo 29, 2011
   */
  function ObtenerInfo($id) {
    $query="SELECT g.*, cl.nombre nombre_cliente,
cl.primer_apellido primer_apellido_cliente,
cl.segundo_apellido segundo_apellido_cliente, cl.digito_verificacion dv_cliente,
ciuo.nombre nombre_ciudad_cliente, ciuo.id id_ciudad_cliente,
ciud.id id_ciudad_contacto, ciud.nombre nombre_ciudad_contacto,
cl.direccion direccion_cliente, cl.tipo_identificacion tipo_identificacion_cliente,
cl.numero_identificacion numero_identificacion_cliente, cl.telefono telefono_cliente,
cl.telefono2 telefono2_cliente, cl.celular celular_cliente,
cl.digito_verificacion digito_verificacion_cliente, cl.porcentajeseguro porcentaje_seguro,
co.nombre nombre_contacto, co.direccion direccion_contacto,
co.primer_apellido primer_apellido_contacto, co.segundo_apellido segundo_apellido_contacto,
co.tipo_identificacion tipo_identificacion_contacto,
co.numero_identificacion numero_identificacion_contacto, co.digito_verificacion dv_contacto,
co.telefono telefono_contacto, co.digito_verificacion digito_verificacion_contacto,
e.nombre estado, SUM(i.peso) peso, SUM(i.kilo_vol) kilo_vol, SUM(i.unidades) unidades,
do.nombre departamento_cliente, dd.nombre departamento_destinatario
FROM guias g, ".Cliente::$table." co, clientes cl, ".Ciudad::$table." ciuo, ciudades ciud,
estadosguias e, items i, departamentos do, departamentos dd
WHERE g.idcliente=cl.id AND i.idguia=g.id AND g.idcontacto=co.id
AND cl.idciudad=ciuo.id AND do.id=ciuo.iddepartamento
AND dd.id=ciud.iddepartamento AND co.idciudad=ciud.id
AND g.idestado=e.id AND g.id='$id'
GROUP BY g.id";
    return DBManager::execute($query);
  }

  /**
   * Agrega un item a una guia.
   *
   * @param int $idguia
   * @param int $idproducto
   * @param int $unidades
   * @param double $peso
   * @param double $kilo_vol
   * @param int $idembalaje
   * @param double $valor
   * @version 1.3 - Julio 23, 2011
   */
  function AgregarItem($idguia,$idproducto,$unidades,$peso, $kilo_vol, $idembalaje,$valor, $declarado, $seguro) {
    $query="INSERT INTO items (idguia, idproducto, unidades, peso, kilo_vol, idembalaje, valor, valor_declarado, seguro)
    VALUES('$idguia','$idproducto','$unidades','$peso', '$kilo_vol', '$idembalaje', '$valor', '$declarado', '$seguro')";
    return DBManager::execute($query);
  }

  /**
   * Calcula el total de los items agregados a una guia y actualiza el total.
   *
   * @param   int $guia ID de la guía que contiene los items.
   */
  function ActualizarTotal($guia) {
    $sql = "SELECT sum(valor) total FROM items i WHERE i.idguia='$guia'";
    $r=DBManager::execute($sql);
    $g=mysql_fetch_object($r);
    $q="UPDATE guias SET total='$g->total' WHERE id='$guia'";
    return DBManager::execute($q);
  }

  /**
   * Selecciona todas las guías asociadas a una planilla ordenadas por la posicion en la planilla.
   *
   * @param   int $idplanilla el ID de la planilla.
   * @param string $modo 'SQL' retorna la consulta, cualquier otro valor retorna el resultado de la consulta.
   * @since Abril 28, 2011
   * @version 1.2 - Agosto 12, 2011
   */
  function ObtenerGuiasEnPlanilla($idplanilla) {
    $query="SELECT g.*, cl.nombre nombrecliente, cl.nombre cliente_no,
cl.tipo_identificacion cliente_ti, cl.primer_apellido cliente_pa,
co.nombre contacto_nombre, co.primer_apellido contacto_primer_apellido,
co.segundo_apellido contacto_segundo_apellido,
co.tipo_identificacion contacto_tipo_identificacion, ci.nombre ciudaddestino,
SUM(i.peso) peso, SUM(i.kilo_vol) kilo_vol
FROM guias g, ".Cliente::$table." co, ".Cliente::$table." cl,
".Ciudad::$table." ci, items i
WHERE g.idcliente=cl.id AND g.idcontacto=co.id AND co.idciudad=ci.id AND
i.idguia=g.id AND g.idplanilla='$idplanilla'
GROUP BY g.id
ORDER BY g.posicionplanilla";
    return DBManager::execute($query);
  }

  /**
   * Quita una guía de una factura.
   *
   * @param   int $idguia el ID de la guia.
   * @since Mayo 2, 2011
   * @author  Edgar Ortega Ramírez
   */
  function QuitarFactura($idguia) {
    $query="UPDATE guias
SET idfactura=NULL, idestado=id_estado_anterior, id_estado_anterior=idestado
WHERE id=$idguia";
    return DBManager::execute($query);
  }

  /**
   * Asigna la posición en la que debe estar una guía en la planilla.
   *
   * @param int $idguia el ID de la guia.
   * @param int $posicion la posicion, desde 1.
   * @since Mayo 7, 2011
   * @version 1.2 - Julio 21, 2011
   */
  function AsignarPosicionEnPlanilla($idguia,$posicion) {
    $query="UPDATE guias SET posicionplanilla=$posicion WHERE id=$idguia";
    return DBManager::execute($query);
  }

  /**
   * Desactiva una Razón de devolución.
   * @param int $id el ID.
   * @since Julio 22, 2011
   */
  function BorrarRazonDevolucion($id) {
    $query="UPDATE razones_devolucion SET activo='no' WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Agrega una razón de devolución.
   *
   * @param   string $nombre el nombre
   * @param   string $activo 'si' o 'no', por defecto 'si'.
   * @since Julio 23, 2011
   */
  function AgregarRazonDevolucion($nombre,$activo='si') {
    $nombre=htmlspecialchars($nombre);
    $activo=strtolower($activo);
    $query="INSERT INTO razones_devolucion(nombre, activo) VALUES('$nombre', '$activo')";
    return DBManager::execute($query);
  }

  /**
   * Selecciona la información de una razón de devolución.
   * @param int $id el ID.
   * @since Julio 23, 2011
   */
  function ObtenerInfoRazonDevolucion($id) {
    $query="SELECT * FROM razones_devolucion WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Edita una razón de devolución.
   *
   * @param int $id el ID.
   * @param   string $nombre el nombre
   * @param   string $activo 'si' o 'no', por defecto 'si'.
   * @since Julio 23, 2011
   */
  function EditarRazonDevolucion($id, $nombre, $activo='si') {
    $nombre=htmlspecialchars($nombre);
    $activo=strtolower($activo);
    $query="UPDATE razones_devolucion SET nombre='$nombre', activo='$activo' WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Borra todos los items de una guía.
   *
   * @param   int $id el ID de la guía.
   * @since Julio 27, 2011
   */
  function BorrarItems($id) {
    $query="DELETE FROM items WHERE idguia=$id";
    return DBManager::execute($query);
  }

  /**
   * Selecciona guias por estado y opcionalmente filtra por fecha.
   */
  function ObtenerPorEstado($id_estado, $fecha_inicio, $fecha_fin, $modo=NULL) {
    if (empty($fecha_inicio) and empty($fecha_fin)) {
      $filtro_fecha="";
    } else {
      if (!empty($fecha_fin) and !empty($fecha_inicio)) {
        $filtro_fecha="AND g.fecha_recibido_mercancia BETWEEN '$fecha_inicio' AND '$fecha_fin'";
      } elseif (!empty($fecha_inicio) and empty($fecha_fin)) {
        $filtro_fecha="AND g.fecha_recibido_mercancia >= '$fecha_inicio'";
      } else {
        $filtro_fecha="AND g.fecha_recibido_mercancia <= '$fecha_fin'";
      }
    }
    $q="SELECT g.*, cl.nombre nombrecliente,
cl.nombre cliente_n, cl.primer_apellido cliente_pa, cl.segundo_apellido cliente_sa,
cl.tipo_identificacion cliente_ti, e.nombre estado,
ciudestino.nombre nombreciudaddestino, u.usuario,
co.nombre contacto_nombre, co.tipo_identificacion contacto_tipo_identificacion,
co.primer_apellido contacto_primer_apellido,
co.segundo_apellido contacto_segundo_apellido
FROM guias g, estadosguias e, ".Cliente::$table." cl, ".Ciudad::$table." ciudestino,
".Cliente::$table." co, usuarios u
WHERE g.idestado=e.id AND cl.id=g.idcliente AND g.idcontacto=co.id
AND co.idciudad=ciudestino.id AND u.id=g.idusuario
AND g.idestado=$id_estado $filtro_fecha
ORDER BY g.id";
    if (!isset($modo))
      return DBManager::execute($q);
    else {
      if ($modo=='SQL') return $q;
    }
  }

  /**
   * Cierra las guias que no han sido cerradas para que no puedan ser
   * editadas al importar una nueva lista de precios.
   * @since Marzo 15, 2012
   */
  function RealizarCierre() {
    $f = date('Y-m-d');
    $q = "UPDATE guias SET cierre='$f' WHERE cierre IS NULL";
    return DBManager::execute($q);
  }

  /**
   * Selecciona las guías que no han sido cerradas.
   * @since Marzo 15, 2012
   */
  function Abiertas() {
    $q="SELECT id FROM guias WHERE cierre IS NULL";
    return DBManager::execute($q);
  }

  function UltimoCierre() {
    $q="SELECT MAX(cierre) ultimo FROM guias";
    $result=DBManager::execute($q);
    $cierre=mysql_fetch_object($result);
    if (is_null($cierre->ultimo)) {
      return FALSE;
    } else {
      return $cierre->ultimo;
    }
  }

  /**
   * Selecciona todas los items de las guias que pueden ser revalorizadas.
   * @since Junio 9, 2012
   */
  function ObtenerItemsParaRevalorizar($id_cliente, $fecha) {
    $q="SELECT i.*, g.valordeclarado, cl.idciudad id_ciudad_origen, co.idciudad id_ciudad_destino
FROM items i, guias g, ".Cliente::$table." cl, ".Cliente::$table." co
WHERE g.id=i.idguia AND cl.id=g.idcliente AND co.id=g.idcontacto
AND g.idcliente='$id_cliente' AND (g.idestado!=6 AND g.idestado!=2)
AND g.fecha_recibido_mercancia>='$fecha'";
    return DBManager::execute($q);
  }

  /**
   * Edita un item de una guia.
   * @since Septiembre 26, 2012
   */
  function EditarItem($id, $idguia, $id_producto, $unidades, $peso, $kilo_vol, $idembalaje, $valor, $valor_declarado, $seguro) {
    $sep=FALSE;
    if ($unidades) {
      $e_unidades="unidades='$unidades'";
      $sep=TRUE;
    } else $e_unidades='';
    if ($peso) {
      $e_peso="peso='$peso'";
      if ($sep) $e_peso=','.$e_peso;
      $sep=TRUE;
    } else $e_peso='';
    if ($kilo_vol) {
      $e_kilo_vol="kilo_vol='$kilo_vol'";
      if ($sep) $e_kilo_vol=','.$e_kilo_vol;
      $sep=TRUE;
    } else $e_kilo_vol='';
    if ($idembalaje) {
      $e_idembalaje="idembalaje='$idembalaje'";
      if ($sep) $e_idembalaje=','.$e_idembalaje;
      $sep=TRUE;
    } else $e_idembalaje='';
    if ($valor) {
      $e_valor="valor='$valor'";
      if ($sep) $e_valor=','.$e_valor;
      $sep=TRUE;
    } else $e_valor='';
    if ($valor_declarado) {
      $e_valor_declarado="valor_declarado='$valor_declarado'";
      if ($sep) $e_valor_declarado=','.$e_valor_declarado;
      $sep=TRUE;
    } else $e_valor_declarado='';
    if ($seguro) {
      $e_seguro="seguro='$seguro'";
      if ($sep) $e_seguro=','.$e_seguro;
    } else $e_seguro='';
    $q="UPDATE items SET $e_unidades $e_peso $e_kilo_vol $e_idembalaje $e_valor $e_valor_declarado $e_seguro WHERE id=$id";
    return DBManager::execute($q);
  }

  /**
   * Edita los tiempos de una guía.
   * @since Julio 3, 2012
   */
  function EditarTiempos($id, $cargue, $descargue) {
    $chp=$cargue['horas_pactadas'];
    $dhp=$descargue['horas_pactadas'];
    $cfl=$cargue['fecha_llegada'];
    $cfs=$cargue['fecha_salida'];
    $dfl=$descargue['fecha_llegada'];
    $dfs=$descargue['fecha_salida'];
    $q="UPDATE guias SET
cargue_horas_pactadas='$chp', descargue_horas_pactadas='$dhp',
cargue_llegada='$cfl', cargue_salida='$cfs',
descargue_llegada='$dfl', descargue_salida='$dfs'
WHERE id='$id'";
  return DBManager::execute($q);
  }

  /**
   * Selecciona las ciudades más visitadas en las guias (no incluye Anuladas ni Preguias)
   *
   * @since Octubre 2, 2012
   */
  function DestinosMasVisitados($cliente, $mes,$limit) {
    $ciud=array('nombres'=>array(),'cantidades'=>array());
    $t='';
    if ($mes=='ACTUAL') {
      $inicio=date('Y-m-d',strtotime('first day of this month'));
      $fin=date('Y-m-d',strtotime('last day of this month'));
      $t=ucfirst(strftime('%B',strtotime('this month')));
    }
    if ($mes=='ANTERIOR') {
      $inicio=date('Y-m-d',strtotime('first day of last month'));
      $fin=date('Y-m-d',strtotime('last day of last month'));
      $t=ucfirst(strftime('%B',strtotime('last month')));
    }
    if ($mes=='3MESES') {
      $s=strtotime('-3 months');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if ($mes=='6MESES') {
      $s=strtotime('-6 months');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if ($mes=='12MESES') {
      $s=strtotime('-1 year');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if ($mes=='2014ANO') {
      $s=strtotime('january 2014');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));    
      $p=strtotime('december 2014');
      $d=date('F Y',$p);
      $fin=date('Y-m-d',strtotime('last day of '.$d));
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y',$p);
    }
    if (!$cliente) {
      $f_c='';
    } else {
      $f_c="AND g.idcliente=$cliente";
    }
    $q="SELECT COUNT(*) cantidad, ci.nombre ciudad
FROM guias g, ".Cliente::$table." co, ".Ciudad::$table." ci
WHERE (g.idestado!=7 AND g.idestado!=6)
AND g.fecha_recibido_mercancia BETWEEN '$inicio' AND '$fin'
AND g.idcontacto=co.id AND co.idciudad = ci.id $f_c
GROUP BY ciudad
ORDER BY cantidad DESC
LIMIT 0, $limit";
    $result=DBManager::execute($q);
    if (DBManager::rows_count($result) != 0) {
      while($f=mysql_fetch_assoc($result)) {
        $ciud['nombres'][]=$f['ciudad'];
        $ciud['cantidades'][]=intval($f['cantidad']);
      }
    }
    $ciud['texto']=$t;
    return $ciud;
  }

  /**
   * Obtiene el total de unidades y kilos transportados en el mes indicado.
   * @since Octubre 8, 2012
   * @param int $cliente el ID del cliente
   * @param string $mes ACTUAL, ANTERIOR, 3MESES, 6MESES Y 12MESES
   */
  function TotalMercanciaTransportada($cliente, $mes, $tipo='MES') {
    $ciud=array(
      'nombres' => array(),
      'unidades' => array(),
      'kilos' => array()
    );
    $fecha = $this->date_info($mes);

    if ($mes=='3MESES') {
      $tipo='MES';
    } elseif ($mes=='6MESES') {
      $tipo='MES';
    } elseif ($mes=='12MESES') {
      $tipo='MES';
    }
    if (!$cliente) {
      $f_c='';
    } else {
      $f_c="AND g.idcliente=$cliente";
    }
    if ($tipo=='DIA') {
      $select='SELECT g.fecha_recibido_mercancia dia';
      $groupby='dia';
    } else {
      $select='SELECT YEAR(g.fecha_recibido_mercancia) ano,
MONTH(g.fecha_recibido_mercancia) mes';
      $groupby='ano, mes';
    }
    $q="$select ,SUM(i.unidades) und, SUM(i.peso) kgs
FROM guias g, items i
WHERE g.id=i.idguia AND (g.idestado=2 OR g.idestado=4) $f_c
AND g.fecha_recibido_mercancia BETWEEN '".$fecha['inicio']."' AND '".$fecha['fin']."'
GROUP BY $groupby";
    $result=DBManager::execute($q);
    if (DBManager::rows_count($result) != 0) {
      while($f=mysql_fetch_assoc($result)) {
        if ($tipo=='DIA') {
          $mes=strftime('%d',strtotime($f['dia']));
        } else {
          $mes=strftime('%b %y',strtotime($f['ano'].'-'.$f['mes'].'-01'));
        }
        $ciud['nombres'][]=$mes;
        $ciud['unidades'][]=intval($f['und']);
        $ciud['kilos'][]=floatval($f['kgs']);
      }
    }
    $ciud['texto']=$fecha['t'];
    return $ciud;
  }

  function date_info($mes) {
    $t='';
    $r = array();
    if ($mes=='ACTUAL') {
      $r['inicio']=date('Y-m-d',strtotime('first day of this month'));
      $r['fin']=date('Y-m-d',strtotime('last day of this month'));
      $r['t']=ucfirst(strftime('%B',strtotime('this month')));
    }
    if ($mes=='ANTERIOR') {
      $r['inicio']=date('Y-m-d',strtotime('first day of last month'));
      $r['fin']=date('Y-m-d',strtotime('last day of last month'));
      $r['t']=strftime('%B %Y', strtotime('-1 month'));
    }
    if ($mes=='3MESES') {
      $s=strtotime('-3 months');
      $n=date('F Y',$s);
      $r['inicio']=date('Y-m-d',strtotime('first day of '.$n));
      $r['fin']=date('Y-m-d');
      $r['t']=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if ($mes=='6MESES') {
      $s=strtotime('-6 months');
      $n=date('F Y',$s);
      $r['inicio']=date('Y-m-d',strtotime('first day of '.$n));
      $r['fin']=date('Y-m-d');
      $r['t']=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if ($mes=='12MESES') {
      $s=strtotime('-1 year');
      $n=date('F Y',$s);
      $r['inicio']=date('Y-m-d',strtotime('first day of '.$n));
      $r['fin']=date('Y-m-d');
      $r['t']=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if ($mes=='2014ANO') {
      $s=strtotime('january 2014');
      $n=date('F Y',$s);
      $r['inicio']=date('Y-m-d',strtotime('first day of '.$n));
      $p=strtotime('december 2014');
      $b=date('Y-m-d',$p);
      $r['fin']=date('Y-m-d',strtotime('last day of '.$b));
      $r['t']=strftime('%B %Y',$s).' - '.strftime('%B %Y',$p);
    }
    return $r;
  }

  function find($id) {
    $query = "SELECT g.*, cl.nombre nombre_cliente,
cl.primer_apellido primer_apellido_cliente, cl.segundo_apellido segundo_apellido_cliente,
cl.digito_verificacion dv_cliente, ciuo.nombre ciudad_cliente,
ciuo.id id_ciudad_cliente, ciud.id id_ciudad_contacto,
ciud.nombre ciudad_contacto, cl.direccion direccion_cliente,
cl.tipo_identificacion tipo_identificacion_cliente,
cl.numero_identificacion numero_identificacion_cliente, cl.telefono telefono_cliente,
cl.telefono2 telefono2_cliente, cl.celular celular_cliente,
cl.digito_verificacion digito_verificacion_cliente, cl.porcentajeseguro porcentaje_seguro,
co.nombre nombre_contacto, co.direccion direccion_contacto,
co.primer_apellido primer_apellido_contacto, co.segundo_apellido segundo_apellido_contacto,
co.tipo_identificacion tipo_identificacion_contacto,
co.numero_identificacion numero_identificacion_contacto, co.digito_verificacion dv_contacto,
co.telefono telefono_contacto, co.digito_verificacion digito_verificacion_contacto,
e.nombre estado, SUM(i.peso) peso, SUM(i.kilo_vol) kilo_vol, SUM(i.unidades) unidades,
do.nombre departamento_cliente, dd.nombre departamento_contacto
FROM guias g, ".Cliente::$table." co, ".Cliente::$table." cl, ".Ciudad::$table." ciuo, ".Ciudad::$table." ciud,
estadosguias e, items i, departamentos do, departamentos dd
WHERE g.idcliente=cl.id AND i.idguia=g.id AND g.idcontacto=co.id
AND cl.idciudad=ciuo.id AND do.id=ciuo.iddepartamento
AND dd.id=ciud.iddepartamento AND co.idciudad=ciud.id
AND g.idestado=e.id AND g.id=$id
GROUP BY g.id";
    if (!$result = DBManager::execute($query)) return FALSE;
    if (DBManager::rows_count($result) == 0) return FALSE;

    $guia = mysql_fetch_object($result);
    $guia->cliente = trim($guia->nombre_cliente.' '.$guia->primer_apellido_cliente.' '.$guia->segundo_apellido_cliente);
    $guia->contacto = trim($guia->nombre_contacto . ' ' . $guia->primer_apellido_contacto . ' ' . $guia->segundo_apellido_contacto);
    return $guia;
  }

  function cliente() {
    $sql = "SELECT * FROM ".Cliente::$table." WHERE id=$this->idcliente";
    $result = DBManager::execute($sql);
    return $this->cliente = mysql_fetch_object($result, 'Cliente');
  }

  function contacto() {
    $sql = "SELECT * FROM ".Cliente::$table." WHERE id=$this->idcontacto";
    $result = DBManager::execute($sql);
    return $this->contacto = mysql_fetch_object($result, 'Contacto');
  }

  static function formas_pago() {
    return array('FLETE AL COBRO', 'CREDITO', 'CONTADO');
  }
}
