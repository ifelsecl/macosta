<?php
$raiz = "../..";
require "../../seguridad.php";

/**
 * Muestra informacion acerca de los documentos vencidos de un vehículo.
 * @param string $placa
 * @since Enero 10, 2011
 */
function DocumentosVencidosCamion($placa) {
  $html='<table><tr>';
  $html.='<td><img src="css/images/alert.png" /></td>';
  $html.='<td><p>El vehículo <b>'.$placa.'</b><br>tiene los siguientes documentos vencidos:</p></td></tr></table>';
  $html.='<table>';
  require_once Logistica::$root.'class/camiones.class.php';
  $camion=new Camiones;
  $documentos_vencidos=$camion->VerificarDocumentos($placa);
  if (isset($documentos_vencidos['SOAT'])) {
    $html.='<tr><td><b>SOAT</b></td><td>'.$documentos_vencidos['SOAT'].'</td></tr>';
  }
  if (isset($documentos_vencidos['Tarjeta de operacion'])) {
    $html.='<tr><td><b>Tarjeta de operación</b></td><td>'.$documentos_vencidos['Tarjeta de operacion'].'</td></tr>';
  }
  if (isset($documentos_vencidos['Tecnico mecanica'])) {
    $html.='<tr><td><b>Técnico mecánica</b></td><td>'.$documentos_vencidos['Tecnico mecanica'].'</td></tr>';
  }
  if (isset($documentos_vencidos['Seguro'])) {
    $html.='<tr><td><b>Seguro</b></td><td>'.$documentos_vencidos['Seguro'].'</td></tr>';
  }
  $html.='</table>';
  return $html;
}

/**
 * Muestra informacion acerca de los documentos vencidos de un conductor.
 * @param string $numero_identificacion
 * @since Enero 10, 2011
 */
function DocumentosVencidosConductor($numero_identificacion) {
  require_once Logistica::$root.'class/Conductor.php';
  $conductor=new Conductor;

  $documentos_vencidos=$conductor->VerificarDocumentos($numero_identificacion);
  $html='<table><tr><td><img src="css/images/alert.png" /></td><td><p>El conductor <b>'.$documentos_vencidos['nombre'].'</b><br>tiene los siguientes documentos vencidos:</p></td></tr></table>';
  $html.='<table>';
  if (isset($documentos_vencidos['Licencia'])) {
    $html.='<tr><td><b>Licencia</b></td><td>'.$documentos_vencidos['Licencia'].'</td></tr>';
  }
  $html.='</table>';
  return $html;
}

if (isset($_REQUEST['ig'])) { //Información de la guía
  $guia = new Guia;
  if (! $guia->find($_REQUEST['id'])) {
    $response['error'] = true;
    $response['mensaje'] = '<table><tr><td><img src="css/images/notice.png" /></td><td>La guía <b>'.$_REQUEST['id'].'</b> no existe.</td></tr></table>';
  } else {
    if ($guia->idestado != 1) {
      $response['error'] = true;
      $response['mensaje'] = '<table><tr>
      <td><img src="css/images/info.png" /></td>
      <td>El estado de la guía <b>'.$guia->id.'</b> es <b>'.$guia->estado().'</b><br>Sólo se pueden agregar guías en BODEGA.</td>
    </tr>
    <tr><td colspan="2" align="center"><a href="#" name="'.$guia->id.'" class="btn to_bodega">Pasar a Bodega</a href="#"></td></tr>
  </table>';
} else {
  $guia->error = false;
  $guia->nonce = nonce_create_query_string($guia->id);
  $response = $guia;
}
}
echo json_encode($response);
exit;
}

if (isset($_POST['guardar'])) {
  require_once $raiz."/class/planillasC.class.php";
  require_once $raiz."/class/camiones.class.php";

  $fecha = $_POST['fecha_planilla'];
  $placacamion = $_POST['placa'];
  $numero_identificacion_conductor = $_POST['numero_identificacion_conductor'];
  $idciudadorigen = $_POST['id_ciudad_origen'];
  $idciudaddestino = $_POST['id_ciudad_destino'];
  $valor_flete = $_POST['valor_viaje'];
  $anticipo = $_POST['anticipo'];
  $fecha_limite_entrega = $_REQUEST['fecha_limite_entrega'];
  $tipo = $_REQUEST['tipo'];
  $id_titular = $_REQUEST['id_titular'];
  $id_ciudad_pago_saldo = $_POST['id_ciudad_pago_saldo'];
  $fecha_pago_saldo = $_POST['fecha_pago_saldo'];
  $cargue_pagado_por = $_POST['cargue_pagado_por'];
  $descargue_pagado_por = $_POST['descargue_pagado_por'];
  $observaciones = $_POST['observaciones'];
  $planilla = new PlanillasC;
  $objCamion = new Camiones;
  $objConductor = new Conductor;

  $documentos_vencidos_camion = $objCamion->VerificarDocumentos($placacamion);
  if ($documentos_vencidos_camion['vencido'] == 'si') {
    $r['error'] = TRUE;
    $r['titulo'] = "Documentos vencidos del vehículo $placacamion";
    $r['mensaje'] = DocumentosVencidosCamion($placacamion);
    echo json_encode($r);
    exit;
  }
  $documentos_vencidos_conductor=$objConductor->VerificarDocumentos($numero_identificacion_conductor);
  if ($documentos_vencidos_conductor['vencido'] == 'si') {
    $r['error'] = TRUE;
    $r['titulo'] = "Documentos vencidos del conductor";
    $r['mensaje'] = DocumentosVencidosConductor($numero_identificacion_conductor);
    echo json_encode($r);
    exit;
  }
  if (!isset($_POST['ids'])) {
    $r['error']=TRUE;
    $r['titulo']="Error";
    $r['mensaje']='<table class="no_resultados">
    <tr><td><img src="css/images/alert.png" alt="Alert!" /></td>
      <td>No has asignado ninguna guía al manifiesto.<br>Agrega por lo menos una para continuar</td></tr></table>';
      echo json_encode($r);
      exit;
    }
    $idplanilla=$planilla->Agregar($fecha, $placacamion, $numero_identificacion_conductor, $idciudadorigen, $idciudaddestino, $valor_flete, $anticipo, $id_ciudad_pago_saldo, $fecha_pago_saldo, $cargue_pagado_por, $descargue_pagado_por, $observaciones, $tipo, $id_titular, $fecha_limite_entrega, 'R');
    if ($idplanilla) {
      if (isset($_POST['ids'])) {
        foreach ($_POST['ids'] as $pos => $id) {
          $planilla->AsignarGuia($idplanilla, $id, $pos+1);
          Logger::guia($id, "asignó la guía al manifiesto ".$idplanilla);
        }
      }
      $logger = new Logger;
      $logger->Log($_SERVER['REMOTE_ADDR'], 'creó el manifiesto', 'Manifiestos', date("Y-m-d H:i:s"), $_SESSION['userid'], $idplanilla);
      $r['error']=FALSE;
      $r['id']=$idplanilla;
    } else {
      $r['error']=FALSE;
      $r['titulo']="Error";
      $r['mensaje']="Ha ocurrido un error... Intentalo nuevamente.";
    }
    echo json_encode($r);
    exit;
  }
  if (isset($_POST['ActualizarPosiciones'])) {
    require_once $raiz."/class/guias.class.php";
    $guia=new Guias;
    foreach ($_POST['idguia'] as $posicion => $valor) {
      if ($guia->AsignarPosicionEnPlanilla($valor, $posicion+1)) {
        echo "1-";
      } else {
        echo "0-";
      }
    }
    exit;
  }
  if (isset($_POST['editar'])) {
    require_once $raiz.'/class/planillasC.class.php';
    $planilla=new PlanillasC;
    $idplanilla=$_POST['idplanilla'];
    $fecha=$_POST['fecha_planilla'];
    $placacamion=$_POST['placa'];
    $numero_identificacion_conductor=$_POST['numero_identificacion_conductor'];
    $idciudadorigen=$_POST['id_ciudad_origen'];
    $idciudaddestino=$_POST['id_ciudad_destino'];
    $valor_flete=$_POST['valor_flete'];
    $anticipo=$_POST['anticipo'];
    $id_ciudad_pago_saldo=$_POST['id_ciudad_pago_saldo'];
    $fecha_pago_saldo=$_POST['fecha_pago_saldo'];
    $cargue_pagado_por=$_POST['cargue_pagado_por'];
    $descargue_pagado_por=$_POST['descargue_pagado_por'];
    $fecha_limite_entrega=$_REQUEST['fecha_limite_entrega'];
    $observaciones=$_POST['observaciones'];
    $id_titular=$_REQUEST['id_titular'];
    if ($planilla->Editar($idplanilla, $fecha, $placacamion, $numero_identificacion_conductor, $idciudadorigen, $idciudaddestino, $valor_flete, $anticipo, $id_ciudad_pago_saldo, $fecha_pago_saldo, $cargue_pagado_por, $descargue_pagado_por, $observaciones, $fecha_limite_entrega, $id_titular)) {
      $logger=new Logger;
      $logger->Log($_SERVER['REMOTE_ADDR'], 'ha editado el manifiesto "'.$idplanilla.'"', 'Manifiestos', date("Y-m-d H:i:s"), $_SESSION['userid']);
      $r['error']=FALSE;
      $r['manifiesto']=$idplanilla;
    } else {
      $r['error']=TRUE;
      $r['mensaje']="Error: ".mysql_error();
    }
    echo json_encode($r);
    exit;
  }
  if (isset($_POST['anular'])) {
    if (! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['id'])) {
      require_once $raiz.'/mensajes/id.php';
      exit;
    }
    if (! $manifiesto = Manifiesto::find($_POST['id'])) exit('No existe el manifiesto.');
    if ($manifiesto->deactivate($_POST['motivo'])) {
      $logger = new Logger;
      $comment = htmlspecialchars(addslashes($_POST['comentario']));
      $logger->Log($_SESSION['ip'], 'anuló el manifiesto y escribió: '.$comment, 'Manifiestos', date("Y-m-d H:i:s"), $_SESSION['userid'], $_POST['id']);
      echo "ok";
    } else {
      echo '<p class="expand">Ha ocurrido un error.<br />intentalo nuevamente.</p>';
    }
    exit;
  }

  if (isset($_POST['marcar'])) {
    Logistica::respond_as_json();
    $response = array('error' => true, 'mensaje' => '');
    if (! $manifiesto = Manifiesto::find($_POST['id'])) {
      $response['mensaje'] = 'No existe el manifiesto';
      echo json_encode($response);
      exit;
    }
    $logger = new Logger;
    $manifiesto->guias();
    $guias_entregadas   = array();
    $guias_sin_entregar = array();
    $guias_manifiesto   = array();
    // foreach ($manifiesto->guias as $guia) {
    //   if ($guia->idestado == 3) $guias_manifiesto[] = $guia->id;
    // }
    // $pdfs = RemoteFile::process($guias_manifiesto);
    // print_r($pdfs);
    foreach ($manifiesto->guias as $guia) {
      if ($guia->idestado == 3) {
        $pdfs = RemoteFile::process("g", array($guia->id));
        $pdf = $pdfs[0];
        if ($pdf->found and $guia->mark_as_entregada($_POST['fecha_entrega'])) {
          Logger::guia($guia->id, 'marcó la guía como ENTREGADA');
          $guias_entregadas[] = $guia->id;
        } else {
          $guias_sin_entregar[] = $guia->id;
        }
      }
    }

    if (count($manifiesto->guias) == count($guias_entregadas)) {
      $logger->Log($_SESSION['ip'], 'marcó todas las '.count($manifiesto->guias).' guias del manifiesto como ENTREGADAS.', 'Manifiestos', date("Y-m-d H:i:s"), $_SESSION['userid'], $manifiesto->id);
      $response['error'] = false;
      $response['mensaje'] = 'Todas las '.count($manifiesto->guias).' guías se marcaron como ENTREGADAS.';
    } elseif (count($guias_sin_entregar) == count($manifiesto->guias)) {
      $response['error'] = true;
      $response['mensaje'] = '<h6>Ninguna guía fue marcada como ENTREGADA!</h6>Verifica que existan los archivos PDF.';
    } elseif (count($guias_entregadas) > 0) {
      $logger->Log($_SESSION['ip'], 'marcó '.count($guias_entregadas).' de '.count($manifiesto->guias).' guías del manifiesto como ENTREGADAS.', 'Manifiestos', date("Y-m-d H:i:s"), $_SESSION['userid'], $manifiesto->id);
      $response['error'] = false;
      $str = wordwrap(implode('<br>',$guias_sin_entregar), 200);
      $response['mensaje'] = 'Se marcaron '.count($guias_entregadas).' guías como ENTREGADAS.';
      if ($guias_sin_entregar > 0) $response['mensaje'] .= '<br>Quedaron sin marcar: '.count($guias_sin_entregar).'.<br>Verifica que existan los archivos PDF.';
    } else {
      $response['error'] = true;
      $response['mensaje'] = 'No se marcaron las guías, por favor inténtalo nuevamente.<br>Sin marcar: '.count($guias_sin_entregar).'<br>En Manifiesto: '.count($manifiesto->guias).'<br>Entregadas: '.count($guias_entregadas);
    }
    echo json_encode($response);
    exit;
  }
