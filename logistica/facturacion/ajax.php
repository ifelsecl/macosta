<?php
require "../seguridad.php";

if (isset($_POST['facturar'])) {
  $respuesta = array('error' => false, 'mensaje' => '');

  $objFactura = new Factura;
  $guia = new Guia;
  $guias = array();

  $fecha_emision = $_POST['fecha_emision'];

  if ($_POST['tipo'] == 'numeros') { // Facturar por Números de guía
    if (! isset($_POST['guias'])) {
      $respuesta = array('error' => true, 'mensaje' => 'No has indicado ninguna guía para facturar.');
      echo json_encode($respuesta);
      exit;
    }
    foreach($_POST['guias'] as $id_guia) {
      $error = false;
      if (! $guia->find($id_guia)) {
        $error = true;
        $respuesta = array('error' => true, 'mensaje' => 'La guía '.$id_guia.' no existe.');
      }
      if ($guia->idfactura) {
        $error = true;
        $respuesta = array(
          'error' => true,
          'mensaje' => 'La guía <b>'.$guia->id.'</b> ya fue facturada. El número de la factura es <b>'.$guia->idfactura.'</b>.'
        );
      }
      if ($guia->idcliente != $_POST['id_cliente']) {
        $error = true;
        $respuesta = array(
          'error' => true,
          'mensaje' => 'La guía '.$guia->id.' pertenece a un cliente diferente.'
        );
      }
      if ($error) {
        echo json_encode($respuesta);
        exit;
      }
      $guias[] = $guia->id;
    }
    if (empty($guias)) {
      $respuesta = array('error' => true, 'mensaje' => 'Algo ha salido mal...');
    } else {
      $cliente = new Cliente;
      if (! $cliente->find($_POST['id_cliente'])) exit('No existe el cliente.');
      if ($id_factura = $objFactura->GuardarFacturaInicial($cliente->id, $_SESSION['userid'], $fecha_emision, $cliente->condicion_pago, $cliente->id_vendedor, 'si')) {
        Logger::factura($id_factura, 'creó la factura');
        $error = $objFactura->AsignarGuiasArray($id_factura, $guias);

        if ($error['error'] == 'no') {
          $result2 = $objFactura->ObtenerTotales($id_factura);
          $row = mysql_fetch_array($result2);
          $valor_flete = round($row['total']);
          $valor_seguro = round($row['total_seguro']);
          $descuento = round($valor_flete * ($cliente->descuento / 100));
          if ($objFactura->Actualizar($id_factura, $valor_flete, $valor_seguro, $descuento)) {
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '<p class="expand">
Se ha creado la factura '.$id_factura.' <a target="_blank" class="btn btn-info" title="Imprimir Factura" href="facturacion/imprimir?idfactura='.$id_factura.'&'.nonce_create_query_string($id_factura).'"><i class="icon-print"></i></a></p>';
            $respuesta['factura'] = $id_factura;
          }
        } else {
          $respuesta['error'] = true;
          $respuesta['mensaje'] = 'Se ha creado la factura <b>'.$id_factura.'</b>, pero no se han podido agregar las guias indicadas. '.$error['mensaje'];
        }
      } else {
        $respuesta['error']=true;
        $respuesta['mensaje']='Ha ocurrido un error al facturar, intentalo nuevamente, si el problema persiste visita el <a href="http://soporte.asesoriasit.com" target="_blank">Portal de soporte</a>.<br>'.mysql_error();
      }
    }
    echo json_encode($respuesta);
    exit;
  }
}
if (isset($_REQUEST['cerrar'])) {//Cerrar factura
  if (! isset($_REQUEST['id']) or ! isset($_REQUEST[NONCE_KEY]) or ! nonce_is_valid($_REQUEST[NONCE_KEY], $_REQUEST['id'])) {
    include Logistica::$root.'mensajes/id.php';
    exit;
  }
  $objFactura = new Factura;
  if ($objFactura->CambiarEstado($_REQUEST['id'], 'Cerrada')) {
    Logger::factura($_REQUEST['id'], 'cerró la factura');
    echo 'ok';
  } else {
    include Logistica::$root.'mensajes/guardando_error.php';
  }
  exit;
}
if (isset($_REQUEST['cg'])) {
  //FACTURAR->Comprobar que la guía pertenezca al cliente seleccionado.
  if (! isset($_REQUEST['id_guia']) or ! isset($_REQUEST['id_cliente'])) {
    $r['error'] = 'si';
    $r['mensaje'] = 'Algo no está bien...';
  } else {
    require_once Logistica::$root."class/Guia.php";
    $guia = new Guia;
    if ( ! $guia->find($_REQUEST['id_guia']) ) {
      $r['error'] = 'si';
      $r['mensaje'] = '<p class="expand">La guía <b>'.$_REQUEST['id_guia'].'</b> no existe.</b></p>';
    } else {
      if (! is_null($guia->idfactura) ) {
        $r['error'] = 'si';
        $r['mensaje'] = '<p>La guía <b>'.$guia->id.'</b> ya fue facturada.<br>El número de la factura es <b>'.$guia->idfactura.'</b>.</p>';
      } elseif ($guia->idestado == 6) {
        $r['error'] = 'si';
        $r['mensaje'] = '<p>La guía <b>'.$guia->id.'</b> está ANULADA.</p>';
      } elseif ($guia->idcliente == $_REQUEST['id_cliente']) {
        $r['error'] = 'no';
        $r['valor'] = $guia->total+$guia->valorseguro;
        $r['mensaje']='
<tr>
  <td><input class="guias_asignadas" type="hidden" name="guias[]" value="'.$guia->id.'" readonly="readonly" />'.$guia->id.'</td>
  <td>'.$guia->contacto()->nombre_completo.'</td>
  <td>'.$guia->contacto->ciudad_nombre.'</td>
  <td align="right">'.$guia->valorseguro.'</td>
  <td align="right">'.$guia->total.'</td>
  <td align="right">'.$r['valor'].'</td>
  <td align="center" width="18"><button type="button" title="Quitar" name="'.round($guia->id).'" class="btn quitar btn-danger"><i class="icon-remove"></i></button></td>
</tr>';
      } else {
        $r['error'] = 'si';
        $r['mensaje'] = '<p style="padding-top: 5px;text-align:center;">La guía <b>'.$guia->id.'</b> pertenece a <b>'.$guia->cliente()->nombre_completo.'</b></p>';
      }
    }
  }
  echo json_encode($r);
  exit;
}
if (isset($_REQUEST['anular'])) {
  $r = array('error' => 'no', 'mensaje' => '');
  if (! isset($_POST['idfactura']) or ! isset($_POST[NONCE_KEY]) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['idfactura'])) {
    $r = array('error' => 'si', 'mensaje' => 'Algo ha salido mal...');
    echo json_encode($r);
    exit;
  }
  if (! isset($_SESSION['permisos'][FACTURACION_ANULAR])) {
    $r = array('erro' => 'si', 'mensaje' => '¡Ahora no tienes permisos para ANULAR facturas!');
    echo json_encode($r);
    exit;
  }
  $objFactura = new Factura;
  if ($objFactura->Anular($_POST['idfactura'])) {
    $comentario = htmlspecialchars(addslashes($_POST['comentario']));
    Logger::factura($_POST['idfactura'], 'anuló la factura y escribió: '.$comentario);
    $r = array('error' => 'no', 'mensaje' => '');
  } else {
    $r = array('error' => 'si', 'mensaje' => 'No se pudo anular la factura, intentalo nuevamente.');
  }
  echo json_encode($r);
  exit;
}
if (isset($_REQUEST['eliminar'])) {
  $r = array('error' => false, 'mensaje' => '');
  if (! isset($_POST['idfactura']) or ! isset($_POST[NONCE_KEY]) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['idfactura'])) {
    $r = array('error' => 'si', 'mensaje' => 'Algo ha salido mal...');
    echo json_encode($r);
    exit;
  }
  if (! isset($_SESSION['permisos'][FACTURACION_ELIMINAR])) {
    $r = array('error' => 'si', 'mensaje' => 'No tienes permisos para ELIMINAR facturas!');
    echo json_encode($r);
    exit;
  }
  if (! $factura = Factura::find($_POST['idfactura'])) {
    $r = array('error' => 'si', 'mensaje' => 'La factura no existe.');
    echo json_encode($r);
    exit;
  }
  if ($factura->destroy()) {
    $comment = htmlspecialchars(addslashes($_POST['comentario']));
    Logger::factura($factura->id, 'eliminó la factura y escribió: '.$comment);
    $r = array('error' => 'no', 'mensaje' => '');
    echo json_encode($r);
    exit;
  }
}
if (isset($_REQUEST['c_f'])) { //cerrar facturas abiertas
  $objFactura=new Factura;
  if ($objFactura->FacturasAbiertas()>0) {
    if ($objFactura->Cerrar()) {
      echo 'ok';
    } else {
      echo 'Ha ocurrido un error y las facturas no se han cerrado, intentalo nuevamente';
    }
  } else {
    echo '<p style="expand">No hay facturas abiertas...</p>';
  }
  exit;
}

if (isset($_POST['editar'])) {
  if (! isset($_POST['id'])) exit('error');
  if (! $factura = Factura::find($_POST['id'])) exit('No existe la factura.');
  $estado_anterior = $factura->estado;
  $changes = $factura->updated_attributes($_POST['factura']);
  if (! $changes) exit('ok');
  if ($factura->update_attributes($_POST['factura'])) {
    Logger::factura($factura->id, "editó la factura".$changes);
    echo 'ok';
  } else {
    echo 'Ha ocurrido un error, intentalo nuevamente.';
  }
  exit;
}

if (isset($_GET['if'])) { //Informacion de Factura
  if (! isset($_GET['id'])) {
    include Logistica::$root.'mensajes/id.php';
    exit;
  }
  Logistica::respond_as_json();
  if (! $factura = Factura::find($_GET['id'])) {
    echo json_encode(false);
    exit;
  }
  if ($factura->nc) {
    $nc = Factura::ObtenerNotaCredito($factura->nc);
    $nc['id'] = $factura->id;
    $nc['nc'] = $factura->nc;
    echo json_encode($nc);
  } else {
    echo json_encode($factura);
  }
  exit;
}
if (isset($_REQUEST['gnc'])) { //Guardar Nota Credito
  $factura = new Factura;
  $factura  = $_REQUEST['factura'];
  $fecha    = $_REQUEST['fecha'];
  $concepto   = $_REQUEST['concepto'];
  $valor    = $_REQUEST['valor'];
  $accion   = $_REQUEST['accion'];
  if ($accion == 'Crear') {
    if (Factura::CrearNotaCredito($factura, $fecha, $concepto, $valor)) {
      echo 'ok';
    } else {
      echo '<p class="expand">No se pudo crear la Nota Credito, intentalo nuevamente.</p>';
    }
  } else {
    $id=$_REQUEST['numero'];
    if (Factura::EditarNotaCredito($id, $fecha, $concepto, $valor)) {
      echo 'ok';
    } else {
      echo '<p class="expand">No se pudo editar la Nota Credito, intentalo nuevamente.</p>';
    }
  }
  exit;
}
if (isset($_POST['pagar'])) {
  Logistica::respond_as_json();

  $response = array('success' => false, 'message' => '');
  if (! $factura = Factura::find($_POST['pago']['factura_id'])) {
    $response['message'] = 'No existe la factura.';
    echo json_encode($response);
    exit;
  }

  if ($payment = Pago::create($_POST['pago'])) {
    $factura->add_payment($payment);
    $response['success'] = true;
    Logger::factura($factura->id, 'agregó un pago de '.number_format($payment->valor).' ('.$payment->tipo.')');
  } else {
    $response['message'] = 'No se pudo guardar el pago.';
  }
  echo json_encode($response);
  exit;
}

                            