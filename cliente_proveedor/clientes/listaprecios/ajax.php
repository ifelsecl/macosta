<?php
require '../../../seguridad.php';

if (isset($_POST['guardar'])) {
  $cliente = new Cliente;
  $id_cliente     = $_POST['id_cliente'];
  $id_ciudad_origen   = $_POST['id_ciudad_origen'];
  $id_ciudad_destino  = $_POST['id_ciudad_destino'];
  $id_embalaje    = $_POST['id_embalaje'];
  $precio       = $_POST['precio'];
  $seguro       = $_REQUEST['seguro'];
  if ($_REQUEST['tipo_cobro'] == 'Caja') {
    $precio_kilo = $_POST['precio_kilo'];
    $precio_kilovol = $_POST['precio_kilovol'];
    $descuento3 = 0;
    $descuento6 = 0;
    $descuento8 = 0;
  } else {
    $precio_kilo = 0;
    $precio_kilovol = 0;
    $descuento3 = $_REQUEST['descuento3'];
    $descuento6 = $_REQUEST['descuento6'];
    $descuento8 = $_REQUEST['descuento8'];
  }
  if (Precio::find($id_cliente, $id_ciudad_origen, $id_ciudad_destino, $id_embalaje)) {
    echo '<label><img src="css/images/notice.png" alt="notice" />';
    echo 'Ya existe un precio con la información indicada.</label>';
    exit;
  }
  if ($cliente->AgregarPrecio($id_cliente, $id_ciudad_origen, $id_ciudad_destino, $id_embalaje, $precio, $precio_kilo, $precio_kilovol, $seguro, $descuento3, $descuento6, $descuento8)) {
    echo 'ok';
    Logger::precio($id_cliente, "agregó un precio de $precio");
  } else {
    include Logistica::$root.'mensajes/guardando_error.php';
  }
  exit;
}

if (isset($_POST['editar'])) {
  Logistica::respond_as_json();
  $re = array('success' => true, 'message' => '');
  if (! $precio = Precio::find($_POST['id_cliente'], $_POST['id_ciudad_origen_old'], $_POST['id_ciudad_destino_old'], $_POST['id_embalaje_old'])) {
    $re['success'] = false;
    $re['message'] = 'No existe el precio.';
    echo json_encode($re);
    exit;
  }
  $params = array(
    'idciudadorigen' => $_POST['id_ciudad_origen'],
    'idciudaddestino' => $_POST['id_ciudad_destino'],
    'idembalaje'=> $_POST['id_embalaje'],
    'precio' => $_POST['precio'],
    'precio_kilo' => $_POST['precio_kilo'],
    'precio_kilovol' => $_POST['precio_kilovol'],
    'seguro' => $_POST['seguro'],
    'descuento3' => $_POST['descuento3'],
    'descuento6' => $_POST['descuento6'],
    'descuento8' => $_POST['descuento8']
  );
  $changes = $precio->updated_attributes($params, false);
  $update = false;
  if ($changes) {
    if ($_POST['id_ciudad_origen'] == $_POST['id_ciudad_origen_old']
      and $_POST['id_ciudad_destino'] == $_POST['id_ciudad_destino_old']
      and $_POST['id_embalaje'] == $_POST['id_embalaje_old']) {
      $update = true;
    } else {
      if (Precio::find($_POST['id_cliente'], $_POST['id_ciudad_origen'], $_POST['id_ciudad_destino'], $_POST['id_embalaje'])) {
        $re['success'] = false;
        $re['message'] = 'Ya existe un precio con las mismas especificaciones.';
      } else {
        $update = true;
      }
    }
    if ($update) {
      if ($precio->update_attributes($params)) {
        Logger::precio($_POST['id_cliente'], 'editó un precio.');
      } else {
        $re['success'] = false;
        $re['message'] = 'Por favor, intentalo nuevamente.';
      }
    }
  } else {
    $re['success'] = false;
    $re['message'] = 'No hay cambios para actualizar.';
  }
  echo json_encode($re);
  exit;
}

if (isset($_REQUEST['modificar'])) {
  $r = array('ok' => false, 'mensaje' => '');
  $id_cliente = $_REQUEST['id_cliente'];
  $cliente = new Cliente;
  $modo = $_REQUEST['modo'];
  $porcentaje = $_REQUEST['porcentaje'];
  if ($cliente->ModificarListaPrecios($id_cliente, $porcentaje, $modo)) {
    $r['ok'] = true;
    $modo = $modo == 'Aumento' ? 'Aumentó' : 'Disminuyó' ;
    $accion = "$modo $porcentaje% a la lista del cliente";
    Logger::precio($id_cliente, $accion);
  } else {
    $r['ok'] = false;
    $r['mensaje'] = 'Ha ocurrido un error, intentalo nuevamente.';
  }
  echo json_encode($r);
  exit;
}
