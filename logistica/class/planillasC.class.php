<?php
require_once "DBManager.php";

/**
 * La clase PlanillasC permite crear una planilla de carga.
 * @author Edgar Ortega Ramírez
 */
class PlanillasC {

  /**
   * Mantiene la conexión con la base de datos.
   * @var DBManager
   */
  var $con;

  /**
   * Crea un nuevo objeto Planilla y establece los datos de conexión.
   */
  function PlanillasC(){
    $this->con=new DBManager;
  }

  /**
   * Anula una planilla sin eliminarla de la base de datos, la planilla no
   * podrá ser usada ni mostrada, las guias asignadas a esa planilla quedarán disponibles.
   *
   * @param   int $id el ID de la planilla.
   * @param string $comentario el comentario de porqué se anuló.
   * @param string $usuario el nombre de usuario del usuario que anuló la planilla.
   * @return  true si anuló la planilla o false si hubo un error.
   * @since Marzo 21, 2011
   * @version 1.2 (Agosto 20, 2011)- Se agregaron los parametros $comentario y $usuario.
   */
  function Anular($id, $comentario, $usuario, $motivo) {
    $fecha=date("Y-m-d H:i:s");
    $f=date('Y-m-d g:i:s a');
    $comentario=addslashes($comentario);
    $cambios="<br>($f) <b>$usuario</b> anuló el manifiesto y escribió: $comentario";
    $query = "UPDATE planillas SET activa='no', motivo_anulacion='$motivo', estado='A', historial_cambios=CONCAT(historial_cambios, '$cambios'), fechamodificacion='$fecha' WHERE id=$id";
    return DBManager::execute($query);
  }

  function mostrar_conductor(){
    $sql = "SELECT c.numero_identificacion ,c.nombre,c.primer_apellido,c.segundo_apellido
    FROM conductores c
    WHERE c.activo='si'";
    return DBManager::execute($sql);
  }

  /**
   * Agrega una planilla y retorna su ID.
   *
   * @param unknown_type $fecha
   * @param unknown_type $placacamion
   * @param unknown_type $numero_identificacion_conductor
   * @param unknown_type $idciudadorigen
   * @param unknown_type $idciudaddestino
   * @param unknown_type $valor_flete
   * @param unknown_type $descuento
   * @param unknown_type $anticipos
   * @param unknown_type $id_ciudad_pago_saldo
   * @param unknown_type $fecha_pago_saldo
   * @param unknown_type $cargue_pagado_por
   * @param unknown_type $descargue_pagado_por
   * @param unknown_type $observaciones
   * @param unknown_type $estado
   * @since Junio 9, 2011
   */
  function Agregar($fecha,$placacamion,$numero_identificacion_conductor,$idciudadorigen,$idciudaddestino,
            $valor_flete,$anticipo,$id_ciudad_pago_saldo,$fecha_pago_saldo,
            $cargue_pagado_por,$descargue_pagado_por,$observaciones,$tipo,$id_titular,
            $fecha_limite_entrega,$estado='R'){
    $retefuente=$valor_flete*0.01;
    $descuento=$valor_flete*0.01;
    $reteica=$valor_flete*0.0054;
    $query1="LOCK TABLES planillas WRITE";
    $query2="INSERT INTO planillas(fecha,placacamion,cedulaconductor,idciudadorigen,idciudaddestino,
valor_flete,descuento,retencion_fuente,ica,anticipo,id_ciudad_pago_saldo,
fecha_pago_saldo,cargue_pagado_por,descargue_pagado_por, observaciones, tipo, id_titular, fecha_limite_entrega, estado) VALUES(
'$fecha','$placacamion','$numero_identificacion_conductor',$idciudadorigen,
$idciudaddestino, '$valor_flete', '$descuento', '$retefuente','$reteica','$anticipo', '$id_ciudad_pago_saldo',
'$fecha_pago_saldo','$cargue_pagado_por','$descargue_pagado_por',
'$observaciones', '$tipo', '$id_titular', '$fecha_limite_entrega', '$estado')";
    $query3="SELECT LAST_INSERT_ID()";
    $query4="UNLOCK TABLES";
    DBManager::execute($query1);
    DBManager::execute($query2);
    $result3=DBManager::execute($query3);
    DBManager::execute($query4);
    if($row=mysql_fetch_array($result3)){
      return $row[0];
    }else{
      return false;
    }
  }

  /**
   * Selecciona toda la información de una planilla de carga, el resultado
   * debe ser pasado a la función mysql_fetch_array.
   *
   * @param   int $idplanilla el ID de la planilla.
   * @since Mayo 11, 2011
   * @version 1.2 - Julio 22, 2011
   */
  function ObtenerPlanilla($idplanilla) {
    $query="SELECT p.*, con.numero_identificacion numero_identificacion_conductor, con.nombre nombreconductor,
con.primer_apellido apellido1conductor,
con.segundo_apellido apellido2conductor, ciuo.nombre ciudadorigen,
ciud.nombre ciudaddestino, m.descripcion marcacamion,
l.descripcion lineacamion, conf.configuracion configuracion,
col.descripcion color, carr.descripcion carroceria,
cam.registro registrocarga, cam.modelo modelocamion, cam.serie seriecamion,
cam.peso pesocamion, cam.soat, cam.modelo_repotenciado, cam.capacidadcarga camion_capacidad_carga,
cam.placa_semiremolque, aseg.nombre aseguradora, cam.f_venc_soat fechasoat,
prop.tipo_identificacion tipo_identificacion_propietario,
prop.nombre nombre_propietario, prop.primer_apellido primer_apellido_propietario,
prop.segundo_apellido segundo_apellido_propietario, prop.razon_social razon_social_propietario,
prop.numero_identificacion numero_identificacion_propietario, prop.digito_verificacion dv_propietario,
prop.direccion direccion_propietario, prop.telefono telefono_propietario,
ciuprop.nombre ciudad_propietario,
ciucon.nombre ciudadconductor, con.direccion direccionconductor,
con.telefono telefonoconductor, con.categorialicencia,
ten.tipo_identificacion tipo_identificacion_tenedor,
ten.numero_identificacion numero_identificacion_tenedor, ten.nombre nombre_tenedor,
ten.primer_apellido primer_apellido_tenedor, ten.segundo_apellido segundo_apellido_tenedor,
ten.razon_social razon_social_tenedor, ten.direccion direccion_tenedor,
ten.telefono telefono_tenedor, ten.digito_verificacion dv_tenedor, ciuten.nombre ciudad_tenedor,
ciupago.nombre ciudad_pago_saldo, do.nombre departamento_origen, dd.nombre departamento_destino,
tit.nombre titular_nombre, tit.primer_apellido titular_primer_apellido,
tit.segundo_apellido titular_segundo_apellido, tit.razon_social titular_razon_social,
tit.tipo_identificacion titular_tipo_identificacion,
tit.numero_identificacion titular_numero_identificacion, tit.digito_verificacion titular_dv,
tit.direccion titular_direccion, tit.telefono titular_telefono,
ciutit.nombre titular_ciudad, dt.nombre titular_departamento,
dp.nombre propietario_departamento, dten.nombre tenedor_departamento,
dcon.nombre conductor_departamento
FROM planillas p, camiones cam, conductores con, ciudades ciuo, ciudades ciud, lineas l,
marcas  m, colores col, carrocerias carr, configuraciones conf, aseguradoras aseg,
terceros prop, ciudades ciuprop, ciudades ciucon, terceros ten, ciudades ciuten,
ciudades ciupago, aseguradoras asegmerc, departamentos do, departamentos dd,
terceros tit, ciudades ciutit, departamentos dt, departamentos dp,
departamentos dten, departamentos dcon
WHERE cam.id_tenedor=ten.id AND ten.id_ciudad=ciuten.id AND con.idciudad=ciucon.id
AND ciupago.id=p.id_ciudad_pago_saldo AND prop.id=cam.idpropietario AND prop.id_ciudad=ciuprop.id
AND cam.nitaseguradora=aseg.nit AND conf.id=cam.idconfiguracion AND
do.id=ciuo.iddepartamento AND dd.id=ciud.iddepartamento AND
carr.codigo_carrocerias=cam.codigo_carrocerias AND col.codigo_colores=cam.codigo_colores
AND cam.codigo_Marcas=m.codigo_Marcas AND m.codigo_Marcas=l.codigomarca AND
cam.codigo_linea=l.codigo AND p.placacamion=cam.placa AND p.cedulaconductor=con.numero_identificacion
AND p.idciudadorigen=ciuo.id AND p.idciudaddestino=ciud.id AND tit.id=p.id_titular AND
tit.id_ciudad=ciutit.id AND dt.id=ciutit.iddepartamento AND
dp.id=ciuprop.iddepartamento AND dten.id=ciuten.iddepartamento AND
dcon.id=ciucon.iddepartamento AND
p.id='$idplanilla'";
    return DBManager::execute($query);
  }

  /**
   * Selecciona todas las guias de una planilla, el resultado debe ser pasado
   * a la función mysql_fetch_array.
   *
   * @param   int $idplanilla el ID de la planilla.
   * @param int $inicio inicio del limite de resultados.
   * @param int $fin fin del limite de resultados.
   * @since Mayo 11, 2011
   * @author  Edgar Ortega Ramírez
   * @version 1.3
   */
  function ObtenerGuias($idplanilla, $inicio=null, $fin=null) {
    $sql = "SELECT g.*, SUM( i.unidades ) cantidad, SUM( i.peso ) peso,
SUM(i.kilo_vol) kilo_vol, p.id idproducto, p.nombre producto,
cl.nombre nombrecliente, cl.nombre cliente_nombre,
cl.primer_apellido cliente_primer_apellido,
cl.segundo_apellido cliente_segundo_apellido,
cl.numero_identificacion cliente_numero_identificacion, cl.digito_verificacion cliente_dv,
cl.direccion cliente_direccion, cl.tipo_identificacion cliente_tipo_identificacion,
cl.idciudad cliente_id_ciudad, co.nombre nombrecontacto, co.nombre nombre_contacto,
co.numero_identificacion numero_identificacion_contacto,
co.tipo_identificacion tipo_identificacion_contacto,
co.primer_apellido primer_apellido_contacto, co.segundo_apellido segundo_apellido_contacto,
co.direccion direccion_contacto, co.digito_verificacion contacto_dv,
co.idciudad id_ciudad_contacto, ci.nombre ciudaddestino, cd.nombre ciudadorigen,
e.nombre estado, COUNT(i.id) items
FROM guias g, ".Cliente::$table." co, ".Cliente::$table." cl, ".Ciudad::$table." ci, ".Ciudad::$table." cd, items i, productos p, estadosguias e
WHERE p.id = i.idproducto AND e.id=g.idestado
AND i.idguia = g.id AND g.idcliente = cl.id
AND g.idcontacto = co.id AND co.idciudad = ci.id
AND cl.idciudad=cd.id AND g.idplanilla =$idplanilla
GROUP BY g.id
ORDER BY g.posicionplanilla";
    if (isset($inicio) and isset($fin)) {
      $sql .= "  LIMIT $inicio,$fin";
    }
    return DBManager::execute($sql);
  }

  /**
   * Activa una planilla que ha sido anulada.
   *
   * @param   int $idplanilla el ID de la planilla.
   * @param string $usuario el nombre de usuario del usuario que activa la planilla.
   * @since Junio 16, 2011
   * @version 1.2 (Agosto 20, 2011) - Se agregó el parámetro $usuario.
   */
  function Activar($idplanilla, $usuario) {
    $fecha=date("Y-m-d H:i:s");
    $f=date('Y-m-d g:i:s a');
    $cambios="<br />($f) <b>$usuario</b> activó la planilla.";
    $query="UPDATE planillas SET activa='si',estado='R', historial_cambios=CONCAT(historial_cambios, '$cambios'), fechamodificacion='$fecha' WHERE id=$idplanilla";
    return DBManager::execute($query);
  }

  /**
   * Actualiza los datos de una planilla.
   *
   * @param int $idplanilla el ID de la planilla.
   * @param string $fecha
   * @param string $placacamion
   * @param int $numero_identificacion_conductor
   * @param int $idciudadorigen
   * @param int $idciudaddestino
   * @param double $valor_flete
   * @param double $descuento
   * @param double $anticipo
   * @param int $id_ciudad_pago_saldo
   * @param string $fecha_pago_saldo
   * @param string $cargue_pagado_por
   * @param string $descargue_pagado_por
   * @param string $observaciones
   * @param string $estado
   * @since Junio 16, 2011
   * @author  Edgar Ortega Ramírez
   */
  function Editar($idplanilla, $fecha, $placacamion, $numero_identificacion_conductor, $idciudadorigen, $idciudaddestino,
          $valor_flete, $anticipo, $id_ciudad_pago_saldo, $fecha_pago_saldo,
          $cargue_pagado_por, $descargue_pagado_por, $observaciones, $fecha_limite_entrega, $id_titular) {
    $retencion_fuente=$valor_flete*0.01; //1%
    $ica=$valor_flete*0.0054; //5.4% x 1000
    $descuento=$valor_flete*0.01;
    $fecha_mod=date("Y-m-d H:i:s");
    $query="UPDATE planillas
SET fecha='$fecha', placacamion='$placacamion', cedulaconductor='$numero_identificacion_conductor',
idciudadorigen='$idciudadorigen', idciudaddestino='$idciudaddestino', valor_flete='$valor_flete',
descuento='$descuento', retencion_fuente='$retencion_fuente', ica='$ica', anticipo='$anticipo',
fechamodificacion='$fecha_mod', id_ciudad_pago_saldo='$id_ciudad_pago_saldo',
fecha_pago_saldo='$fecha_pago_saldo', cargue_pagado_por='$cargue_pagado_por',
descargue_pagado_por='$descargue_pagado_por', observaciones='$observaciones', fecha_limite_entrega='$fecha_limite_entrega',
id_titular='$id_titular'
WHERE id=$idplanilla";
    return DBManager::execute($query);
  }

  /**
   * Selecciona los datos necesarios para el archivo que se debe enviar al Ministerio de Transporte.
   *
   * @since Junio 21, 2011
   * @version 1.3 - Noviembre 24, 2011
   */
  function ExportarMT($fecha) {
    $query="SELECT p.*, c.tipo_identificacion tipo_identificacion_conductor,
c.numero_identificacion numero_identificacion_conductor, cam.placa_semiremolque placa_semirremolque,
cam.codigo_carrocerias tipo_carroceria, conf.configuracion, cam.peso,
ti.id titular_id, ti.numero_identificacion titular_numero_identificacion,
ti.tipo_identificacion titular_tipo_identificacion, ti.nombre titular_nombre,
ti.primer_apellido titular_primer_apellido, ti.segundo_apellido titular_segundo_apellido,
ti.razon_social titular_razon_social
FROM planillas p, conductores c, camiones cam, configuraciones conf, terceros ti
WHERE cam.placa=p.placacamion AND p.cedulaconductor=c.numero_identificacion AND
cam.idconfiguracion=conf.id AND ti.id=p.id_titular AND p.activa='si' AND p.fecha='$fecha'
ORDER BY p.id";
    return DBManager::execute($query);
  }

  /**
   * Asigna una guía a una planilla en la posición indicada.
   *
   * @param   int $id_planilla
   * @param   int $id_guia
   * @param   int $posicion
   * @since Agosto 22, 2011
   */
  function AsignarGuia($id_planilla, $id_guia, $posicion) {
    $sql = "UPDATE guias SET id_estado_anterior=idestado, idestado=3,
idplanilla=$id_planilla, posicionplanilla=$posicion, fechadespacho='".date('Y-m-d')."'
WHERE id=$id_guia";
    return DBManager::execute($sql);
  }

  /**
   * Selecciona las planillas de acuerdo a las opciones.
   * @since Diciembre 12, 2011
   */
  function Exportar($tipo, $inicio, $fin, $modo=NULL){
    if($tipo=='fecha'){
      $where="p.fecha BETWEEN '$inicio' AND '$fin'";
    }else{//rango
      $where="p.id BETWEEN '$inicio' AND '$fin'";
    }
    $q="SELECT p.*, ciu.nombre ciudad_destino, m.Descripcion, co.nombre,
co.primer_apellido,co.segundo_apellido, co.direccion,c.idpropietario, c.placa
FROM planillas p, camiones c,conductores co, marcas m, ciudades ciu
WHERE p.idciudaddestino=ciu.id AND p.cedulaconductor = co.numero_identificacion AND
p.placacamion = c.placa AND c.codigo_Marcas = m.codigo_Marcas AND $where
ORDER BY p.id";
    if(!isset($modo)){
      return DBManager::execute($q);
    }else{
      if($modo=='SQL'){
        return $q;
      }
    }
  }

  /**
   * Retorna las opciones válidas para el pago del cargue y el descargue de
   * la mercancia.
   */
  static function ObtenerOpcionesCargueDescargue(){
    return array(
      'R'=>'REMITENTE',
      'D'=>'DESTINATARIO'
    );
  }

  /**
   * Selecciona las ciudades más visitadas en los manifiestos de carga
   * emitidos en la fecha indicada para todos los conductores o un solo conductor.
   *
   * @since Octubre 2, 2012
   * @param int $cedula FALSE o la cedula de un conductor.
   */
  function DestinosMasVisitados($cedula, $mes,$limit){
    $ciud=array('nombres'=>array(),'cantidades'=>array());
    $t='';
    if($mes=='ACTUAL'){
      $inicio=date('Y-m-d',strtotime('first day of this month'));
      $fin=date('Y-m-d',strtotime('last day of this month'));
      $t=ucfirst(strftime('%B',strtotime('this month')));
    }
    if($mes=='ANTERIOR'){
      $inicio=date('Y-m-d',strtotime('first day of last month'));
      $fin=date('Y-m-d',strtotime('last day of last month'));
      $t=ucfirst(strftime('%B',strtotime('last month')));
      $t=strftime('%B %Y',strtotime('-1 month'));
    }
    if($mes=='3MESES'){
      $s=strtotime('-3 months');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if($mes=='6MESES'){
      $s=strtotime('-6 months');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if($mes=='12MESES'){
      $s=strtotime('-1 year');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if($cedula) $f_cedula="AND p.cedulaconductor='$cedula'";
    else $f_cedula='';
    $q="SELECT COUNT( * ) cantidad, c.nombre ciudad
FROM planillas p, ciudades c
WHERE p.idciudaddestino = c.id $f_cedula AND p.fecha BETWEEN '$inicio' AND '$fin'
GROUP BY ciudad
ORDER BY cantidad DESC
LIMIT 0 , $limit";
    $result=DBManager::execute($q);
    if (mysql_num_rows($result) != 0) {
      while($f=mysql_fetch_assoc($result)){
        $ciud['nombres'][]=$f['ciudad'];
        $ciud['cantidades'][]=intval($f['cantidad']);
      }
    }
    $ciud['texto']=$t;
    return $ciud;
  }
}
