<?php
require "../../seguridad.php";
require_once Logistica::$root.'funciones.php';

if (isset($_GET['buscarcliente'])) {
  Logistica::respond_as_json();
  echo Cliente::autocomplete_for($_GET['opcion'], $_GET['term']);
  exit;
}
if (isset($_POST['buscarembalaje'])) {
  echo Embalaje::ObtenerEmbalajesPorDestino($_POST['id_cliente'], $_POST['id_ciudad_cliente'],$_POST['id_ciudad_contacto']);
  exit;
}
if (isset($_POST['embalaje'])) {
  $embalajes = Embalaje::where($_POST);
  if (empty($embalajes)) exit;
  echo '<option value="">Selecciona...</option>';
  foreach ($embalajes as $embalaje) {
    echo '<option data-tipo-cobro="'.$embalaje->tipo_cobro.'" data-precio="'.$embalaje->precio.'" data-seguro="'.$embalaje->seguro.'" value="'.$embalaje->id.'" title="'.$embalaje->descripcion.'">'.$embalaje->nombre.'</option>';
  }
  exit;
}

if (isset($_POST['change_status'])) {
  Logistica::respond_as_json();
  $guia = new Guia;
  $response = array('success' => false, 'message' => '');
  if (! $guia->find($_POST['id'])) {
    $response['message'] = 'No existe la guía '.$_POST['id'];
  } else {
    $guia->change_status($_POST['idestado']);
    $response['success'] = true;
  }
  echo json_encode($response);
  exit;
}

if (isset($_POST['guardar'])) {
  if (! isset($_POST['items'])) exit('Agrega por lo menos un producto.');
  $params = $_POST['guia'];
  $params['total'] = 0;
  $params['idestado'] = 1;
  foreach ($_POST['items'] as $item) {
    $params['total'] += $item['valor'];
  }
  if ($guia = Guia::create($params)) {
    foreach ($_POST['items'] as $item) {
      $guia->add_item($item);
    }
    $cliente = new Cliente;
    $cliente->find($guia->idcliente);
    $cliente->add_contact($guia->idcontacto);
    Logger::guia($guia->id, 'creó la guía');
    if ('CREDITO' == $guia->formapago) exit;
    $total = $params['total'] + $params['valorseguro'];
    $today = date('Y-m-d');
    $factura_params = array(
      'idcliente' => $cliente->id,
      'id_usuario' => $_SESSION['userid'],
      'condicionpago' => $cliente->condicion_pago,
      'fechaemision' => $today,
      'fechavencimiento' => date('Y-m-d', strtotime('+'.$cliente->condicion_pago.' days')),
      'valorflete' => $params['total'],
      'valorseguro' => $params['valorseguro'],
      'descuento' => 0,
      'total' => $total,
      'total_pagos' => $total,
      'estado' => 'Pagada',
      'id_vendedor' => $cliente->id_vendedor,
      'tipo' => $guia->formapago,
    );
    if ($factura = Factura::create($factura_params)) {
      $guia->update_attributes(array('idfactura' => $factura->id));
      $pago_params = array('factura_id' => $factura->id, 'fecha' => $today, 'tipo' => 'efectivo', 'valor' => $total);
      $pago = Pago::create($pago_params);
      Logger::factura($factura->id, 'creó la factura');
      Logger::guia($guia->id, 'generó una factura automáticamente');
    } else {
      echo 'La guía se creo correctamente, pero la factura no se pudo generar.';
    }
  } else {
    include Logistica::$root.'mensajes/guardando_error.php';
  }
  exit;
}

if (isset($_POST['editar'])) {
  if (! isset($_POST['items'])) {
    echo '<table><tr>';
    echo '<td><img src="css/images/alert.png" alt="alert" /></td>';
    echo '<td><p>La guía no tiene <b>productos</b>, agrega por lo menos uno.</p></td>';
    echo '</tr></table>';
    exit;
  }
  $guia = new Guia;
  if ( ! $guia->find($_POST['id']) ) exit('No existe la guía.');
  $guia->total = 0;
  foreach ($_POST['items'] as $key => $item) {
    $guia->total += $item['valor'];
  }
  $guia->items();
  $changes = $guia->updated_attributes($_POST);
  if (! $changes) exit('No hay cambios para actualizar.');
  if ($guia->update($_POST)) {
    $guia->remove_items();
    foreach ($_POST['items'] as $item) $guia->add_item($item);
    Logger::guia($guia->id, 'editó la guía'.$changes);
  } else {
    echo 'No se pudo guardar la guía, intentalo nuevamente.';
  }
  exit;
}
if (isset($_POST['anular'])) {
  if (! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['id'])) {
    include Logistica::$root."mensajes/id.php";
    exit;
  }
  if (empty($_POST['comentario'])) exit('Escribe un comentario.');
  $guia = new Guia;
  if (! $guia->find($_POST['id'])) exit('No existe la guía.');
  if ($guia->deactivate()) {
    $comment = htmlspecialchars(addslashes($_POST['comentario']));
    Logger::guia($guia->id, 'anuló la guía y escribió: '.$comment);
  } else {
    echo 'Por favor, intentalo nuevamente.';
  }
  exit;
}

if (isset($_POST['eliminar'])) {
  if (! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['id'])) {
    include Logistica::$root."mensajes/id.php";
    exit;
  }
  if (empty($_POST['comentario'])) exit('Debes escribir un comentario.');
  $guia = new Guia;
  if (! $guia->find($_POST['id'])) exit('No existe la guía.');
  if (! in_array($guia->idestado, array(1, 6, 7))) {
    exit('<p>El estado de la guía <b>'.$guia->id.'</b> es '.$guia->estado().'.<br>Sólo se pueden eliminar guías en BODEGA y PREGUIA.</p>');
  }
  if ($guia->delete()) {
    $comment = htmlspecialchars(addslashes($_POST['comentario']));
    Logger::guia($guia->id, 'eliminó la guía y escribió: '.$comment);
  } else {
    echo 'No se ha podido eliminar la guía, inténtalo nuevamente.';
  }
  exit;
}

if (isset($_POST['eliminar_varias'])) {
  if (! isset($_POST['guias'])) {
    echo '<table class="expand"><tr>';
    echo '<td><img src="css/images/alert.png" /></td>';
    echo '<td>¡Selecciona por lo menos una guía!</td>';
    echo '</tr></table>';
    exit;
  }
  if (count($_POST['guias']) == 1) {
    $accion = 'eliminó la guía '.$_POST['guias'][0];
    $guias = $_POST['guias'][0];
  } else {
    $accion = 'eliminó las guías '.implode(",", $guias);
    $guias = $_POST['guias'];
  }
  if (Guia::_delete($guias)) {
    echo 'ok';
    Logger::guia('', $accion);
  } else {
    echo '<table class="expand"><tr>';
    echo '<td><img src="css/images/alert.png" /></td>';
    echo '<td>No se han podido eliminar las guías seleccionadas...<br>Intentalo nuevamente.</td>';
    echo '</tr></table>';
  }
  exit;
}
if (isset($_REQUEST['realizar_cierre'])) {
  require_once Logistica::$root."class/guias.class.php";
  $objGuia = new Guias;
  $num = mysql_num_rows($objGuia->Abiertas());
  if ($num==1) {
    $texto='Se ha cerrado 1 guía';
  } else {
    $num=number_format($num);
    $texto='Se han cerrado '.$num.' guías';
  }
  if ( $objGuia->RealizarCierre() ) {
    echo '<table class="expand"><tr>';
    echo '<td><img src="css/images/valid.png" /></td>';
    echo '<td>'.$texto.'.</td>';
    echo '</tr></table>';
    $Logger=new Logger;
    $ip=$_SERVER['REMOTE_ADDR'];
    $accion='realizó un cierre.';
    $Logger->Log($ip, $accion, 'Guias', date("Y-m-d H:i:s"), $_SESSION['userid']);
  } else {
    echo '<table class="expand"><tr>';
    echo '<td><img src="css/images/alert.png" /></td>';
    echo '<td>No se ha podido realizar el cierre...<br>Intentalo nuevamente.</td>';
    echo '</tr></table>';
  }
  exit;
}
if (isset($_REQUEST['revalorizar'])) {
  require_once Logistica::$root."class/guias.class.php";
  $objGuia = new Guias;
  $id_cliente = $_REQUEST['r_id_cliente'];
  $fecha = $_REQUEST['r_fecha'];
  $r = $objGuia->ObtenerItemsParaRevalorizar($id_cliente, $fecha);
  while ($item = mysql_fetch_object($r)) {
    $p = Precio::find($id_cliente, $item->id_ciudad_origen, $item->id_ciudad_destino, $item->idembalaje);
    $precio = $p->liquidate(30, $item->unidades, $item->peso, $item->valordeclarado);
    if ($objGuia->EditarItem($item->id, $item->idguia, $item->idproducto, $item->unidades, $item->peso, $item->kilo_vol, $item->idembalaje, $precio, FALSE, FALSE)) {
      $objGuia->ActualizarTotal($item->idguia);
    } else {
      echo 'No se pudo actualizar la guía.';
    }
  }
  $Logger=new Logger;
  $ip=$_SERVER['REMOTE_ADDR'];
  $accion='revalorizó las guías de '.$_REQUEST['r_cliente'].' a partir de '.$fecha;
  $Logger->Log($ip, $accion, 'Guias', date("Y-m-d H:i:s"), $_SESSION['userid']);
  echo 'ok';
}

if (isset($_REQUEST['marcar'])) {
  Logistica::respond_as_json();
  $fecha = $_REQUEST['m_fecha'];
  $guias = explode(',', str_replace(array("'", '"'), '', $_REQUEST['m_guias']));
  echo json_encode(Guia::mark_multiple_as_entregada($guias, $fecha));
}

if (isset($_GET['paquete'])) {
  foreach ($_POST['guias'] as $params) {
    $guia_params = $params['guia'];
    $item_params = $params['item'];
    $guia_params['idcliente'] = $_POST['paquete']['id_cliente'];
    $guia_params['formapago'] = 'CREDITO';
    $guia_params['propietario'] = 'Remitente';
    $guia_params['total'] = $item_params['valor'];
    $guia_params['idestado'] = 1;
    if ($guia = Guia::create($guia_params)) {
      Logger::guia($guia->id, 'creó la guía en un paquete');
      $item_params['kilo_vol'] = 0;
      $item_params['idembalaje'] = $_POST['paquete']['id_embalaje'];
      $guia->add_item($item_params);
    } else {
      exit('Ha ocurrido un error...');
    }
  }
  exit;
}

if (isset($_REQUEST['tiempos'])) {
  require Logistica::$root."class/guias.class.php";
  $guia = new Guias;
  $id = $_REQUEST['id'];
  $cargue['horas_pactadas'] = $_REQUEST['cargue_horas_pactadas'];
  $descargue['horas_pactadas'] = $_REQUEST['descargue_horas_pactadas'];
  $cargue['fecha_llegada'] = date('Y-m-d H:i:s', strtotime($_REQUEST['cargue_fecha_llegada']));
  $descargue['fecha_llegada'] = date('Y-m-d H:i:s', strtotime($_REQUEST['descargue_fecha_llegada']));
  $cargue['fecha_salida'] = date('Y-m-d H:i:s', strtotime($_REQUEST['cargue_fecha_salida']));
  $descargue['fecha_salida'] = date('Y-m-d H:i:s', strtotime($_REQUEST['descargue_fecha_salida']));
  if ($guia->EditarTiempos($id, $cargue, $descargue)) {
    $Logger = new Logger;
    $ip = $_SERVER['REMOTE_ADDR'];
    $accion = 'editó los tiempos de cargue/descargue de la guía '.$id.'.';
    $Logger->Log($ip, $accion, 'Guias', date("Y-m-d H:i:s"), $_SESSION['userid']);
    echo 'ok';
  } else {
    echo '<p class="expand">No se han guardado los cambios, intentalo nuevamente.<br>'.mysql_error().'</p>';
  }
}

if (isset($_POST['liquidar'])) {
  Logistica::respond_as_json();
  $id_ciudad_destino = $_POST['id_ciudad_destino'];
  $id_ciudad_origen = $_POST['id_ciudad_origen'];
  $id_embalaje = $_POST['id_embalaje'];
  $id_cliente = $_POST['id_cliente'];
  $precio = Precio::find($id_cliente, $id_ciudad_origen, $id_ciudad_destino, $id_embalaje);
  if (! $precio) {
    echo json_encode(array('success'=> false));
    exit;
  }
  $unidades = intval($_POST['unidades']);
  $peso = floatval($_POST['peso']);
  $valor_declarado = floatval($_POST['valor_declarado']);
  $restriccion_peso = intval($_POST['restriccion_peso']);
  $flete = $precio->liquidate($restriccion_peso, $unidades, $peso, $valor_declarado);

  $seguro = round($valor_declarado * $precio->seguro / 100);
  $return = array(
    'valor_declarado' => $valor_declarado,
    'unidades' => $unidades,
    'peso' => $peso,
    'flete' => $flete,
    'seguro' => $seguro,
    'descuento' => $precio->descuento
  );
  echo json_encode($return);
  exit;
}

                            