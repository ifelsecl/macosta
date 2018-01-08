<?php
$raiz = '../../';
include '../seguridad.php';

if (isset($_REQUEST['guardarcontacto'])) {
  $_POST['idformajuridica'] = 0;
  $_POST['idregimen'] = 0;
  $cliente = new Cliente($_POST);
  if ($cliente->create()) {
    Logger::cliente($cliente->id, 'creó el cliente');
  } else {
    include $raiz.'mensajes/guardando_error.php';
  }
  exit;
}

$logger = new Logger;
$id_cliente = $_SESSION['id'];

if (isset($_REQUEST['guardar'])) {
  require_once $raiz."class/guias.class.php";
  if (! isset($_REQUEST['forma_pago'])) exit('Intento de hackeo');

  $forma_pago   = $_POST['forma_pago'];
  $formas_pago  = Guias::formas_pago();
  if (! in_array($forma_pago, $formas_pago) ) {
    exit('Este intento de hackeo ha sido registrado!.
Se ha enviado un mensaje a los administradores con tu información.');
  }
  $order              = array("\r\n", "\n", "\r");
  $observacion        = trim(str_replace($order, ', ', $_POST['observacion']));
  $unidad_medida      = 1;
  $unidad_empaque     = 0;
  $naturaleza_carga   = 1;
  $documento_cliente  = htmlspecialchars(trim($_POST['documento_cliente']));
  $id_ciudad_origen   = $_SESSION['id_ciudad'];
  $id_contacto        = $_REQUEST['id_contacto'];
  $id_ciudad_destino  = $_REQUEST['id_ciudad_contacto'];
  $propietario        = $_REQUEST['propietario'];
  $id_usuario         = 16;
  $peso_contenedor    = 0;
  $recogida           = 'no';
  $numero             = '';
  $id_estado          = 7;//cambio de 1 a 7

  $guia     = new Guias;
  $cliente  = new Cliente(array('id' => $id_cliente));

  if ($_SESSION['nl']) {
    if (! isset($_REQUEST['items'])) {
      exit('No se han indicado items, agrega por lo menos uno.');
    }
    $valor_declarado  = 0;
    $valor_seguro     = 0;
    $total            = 0;

    foreach ($_REQUEST['items'] as $key1 => $item) {
      foreach ($item as $key2 => $value) {
        $_REQUEST['items'][$key1][$key2] = addslashes($value);
      }
      $valor_declarado  += $item['valor_declarado'];
      $valor_seguro     += $item['seguro'];
      $total            += $item['flete'];
    }
    if ($total == 0) $id_estado = 7;

    if ($id = $guia->Agregar($valor_declarado, $valor_seguro, $forma_pago, $observacion, $total, $unidad_medida, $unidad_empaque, $naturaleza_carga, $documento_cliente, $id_cliente, $id_contacto,
      $id_usuario, $numero, $peso_contenedor, $propietario, $recogida, $id_estado)) {
      foreach ($_REQUEST['items'] as $item) {
        $guia->AgregarItem($id, '9980', $item['unidades'], $item['peso'], 0, $item['id_embalaje'], $item['flete'], $item['valor_declarado'], $item['seguro']);
      }
      echo 'ok';
      $accion = "creó la guía (nl)";
      $logger->Log($_SESSION['ip'], $accion, 'Guias', date("Y-m-d H:i:s"), $id_usuario, $id);
      $cliente->add_contact($id_contacto);
    } else {
      echo 'No se pudo crear la guia, intentalo nuevamente.';
    }
  } else {
    $valor_declarado = $_POST['valor_declarado'];

    $unidades = $_POST['unidades'];
    if (is_nan($unidades) or $unidades<1) {
      exit('Las unidades no son validas, Minimo 1 unidad.');
    }
    $peso = $_POST['peso'];
    if (is_nan($peso) or $peso<1) {
      exit('El peso no es valido, minimo 1 Kg.');
    }

    $total             = 0;
    $id_estado         = 7; //PREGUIA
    $id_embalaje       = 1;
    $porcentaje_seguro = $_SESSION['porcentajeseguro'];
    $precios = Precio::available($id_cliente, $id_ciudad_origen, $id_ciudad_destino);
    if (count($precios) == 1) {
      $precio             = $precios[0];
      $id_estado          = 7; //BODEGA cambio de 1 a 7.
      $id_embalaje        = $precio->idembalaje;
      $porcentaje_seguro  = $precio->seguro;
      $total = $precio->liquidate($_SESSION['restriccion_peso'], $unidades, $peso, $valor_declarado);
    }
    $valor_seguro = round($valor_declarado * $porcentaje_seguro / 100);
    $params = array(
      'valordeclarado' => $valor_declarado,
      'valorseguro' => $valor_seguro,
      'formapago' => $forma_pago,
      'observacion' => $observacion,
      'total' => $total,
      'unidadmedida' => $unidad_medida,
      'empaque' => $unidad_empaque,
      'naturaleza' => $naturaleza_carga,
      'documentocliente' => $documento_cliente,
      'idcliente' => $id_cliente,
      'idcontacto' => $id_contacto,
      'numero' => $numero,
      'peso_contenedor' => $peso_contenedor,
      'propietario' => $propietario,
      'recogida' => $recogida,
      'idestado' => $id_estado
    );
    if ($guia = Guia::create($params)) {
      $item = array(
        'idproducto' => '9980',
        'unidades' => $unidades,
        'peso' => $peso,
        'kilo_vol' => 0,
        'idembalaje' => $id_embalaje,
        'valor' => $total
      );
      if ($guia->add_item($item)) {
        echo 'ok';
        if (count($precios) == 1) $accion = 'creó la guía (liquidada)';
        else $accion = 'creó la preguía. No se pudo liquidar por que existen '.count($precios).' precios ';
        Logger::guia($guia->id, $accion);
        $cliente->add_contact($id_contacto);
      } else {
        echo 'Se creo la guía '.$guia->id.' pero no se pudieron agregar los productos. Por favor editala o eliminala';
      }
    } else {
      echo 'No se pudo crear la guia, intentalo nuevamente.';
    }
  }
  exit;
}

if (isset($_POST['editar'])) {
  require_once $raiz."class/guias.class.php";

  $hack = 'INTENTO DE HACKEO';
  if (! isset($_REQUEST['forma_pago']) or ! isset($_REQUEST['documento_cliente']) or ! isset($_REQUEST['observacion'])) {
    exit($hack);
  }

  $forma_pago = $_POST['forma_pago'];
  $formas_pago = Guias::formas_pago();
  if (! in_array($forma_pago, $formas_pago)) {
    exit('Este intento de hackeo ha sido registrado!.
Se ha enviado un mensaje a los administradores con tu información.');
  }

  $documento_cliente = htmlspecialchars(trim($_POST['documento_cliente']));
  $order = array("\r\n", "\n", "\r");
  $observacion = htmlspecialchars(trim(str_replace($order, ' ', $_POST['observacion'])));

  $objGuia = new Guias;
  $cliente = new Cliente;

  $id                 = $_SESSION['id_guia'];
  $id_ciudad_origen   = $_SESSION['id_ciudad'];
  $id_contacto        = $_REQUEST['id_contacto'];
  $propietario        = $_REQUEST['propietario'];
  $id_ciudad_destino  = $_REQUEST['id_ciudad_contacto'];

  if ($_SESSION['nl']) {
    if (! isset($_REQUEST['items'])) {
      exit('No se han indicado items, agrega por lo menos uno.');
    }
    $valor_declarado = 0;
    $valor_seguro = 0;
    $total = 0;

    foreach ($_REQUEST['items'] as $key1 => $item) {
      foreach ($item as $key2 => $value) {
        $_REQUEST['items'][$key1][$key2] = addslashes($value);
      }
      $valor_declarado += $item['valor_declarado'];
      $valor_seguro += $item['seguro'];
      $total += $item['flete'];
    }
    if ($total == 0) {
      $id_estado = 7;
    }

    if ($objGuia->Editar2($id, $id_contacto, $valor_declarado, $valor_seguro, $documento_cliente, $forma_pago, $observacion, $propietario, $total)) {
      if ($objGuia->BorrarItems($id)) {
        foreach ($_REQUEST['items'] as $item) {
          if (!$objGuia->AgregarItem($id, '9980', $item['unidades'], $item['peso'], 0, $item['id_embalaje'], $item['flete'], $item['valor_declarado'], $item['seguro'])) {
            echo 'No se pudo agregar productos a la guia, intentalo nuevamente.';
          }
        }
        echo 'ok';
        Logger::guia($id, "Editó la guía $id (nl)");
      } else {
        echo 'No se pudieron editar los items, por favor intentalo nuevamente';
      }
    } else {
      echo 'No se pudo editar la guia, intentalo nuevamente.';
    }
  } else {
    if (! isset($_REQUEST['unidades']) or ! isset($_REQUEST['peso'])) exit($hack);
    $unidades = intval($_REQUEST['unidades']);
    $peso = floatval($_REQUEST['peso']);
    $valor_declarado = floatval($_POST['valor_declarado']);

    $total             = 0;
    $id_estado         = 7; //PREGUIA
    $id_embalaje       = 1;
    $porcentaje_seguro = $_SESSION['porcentajeseguro'];
    $precios = Precio::available($id_cliente, $id_ciudad_origen, $id_ciudad_destino);
    if (count($precios) == 1) {
      $precio             = $precios[0];
      $id_estado          = 7; //BODEGA cambio de 1 a 7.
      $id_embalaje        = $precio->idembalaje;
      $porcentaje_seguro  = $precio->seguro;
      $total = $precio->liquidate($_SESSION['restriccion_peso'], $unidades, $peso, $valor_declarado);
    }
    $valor_seguro = round($valor_declarado * $porcentaje_seguro / 100);

    if ($objGuia->Editar2($id, $id_contacto, $valor_declarado, $valor_seguro,
      $documento_cliente, $forma_pago, $observacion, $propietario, $total)) {
      $iditem = $_REQUEST['id_item'];
      if ($objGuia->EditarItem($iditem, $id, FALSE, $unidades, $peso, FALSE, $id_embalaje, $total, $valor_declarado, $valor_seguro)) {
        echo 'ok';
        Logger::guia($id, 'editó la guía');
      } else {
        echo 'Ha ocurrido un error al actualizar los productos, intentalo nuevamente...';
      }
    } else {
      echo 'No se pudo actualizar la guía, por favor inténtalo nuevamente.';
    }
  }
  exit;
}

if (isset($_POST['buscarembalaje'])) {
  echo Embalaje::ObtenerEmbalajesPorDestino($_SESSION['id'], $_SESSION['id_ciudad'], $_POST['id_ciudad_contacto']);
  exit;
}

if (isset($_REQUEST['liquidar'])) {
  Logistica::respond_as_json();
  $id_ciudad_destino = $_REQUEST['id_ciudad_destino'];
  $id_ciudad_origen = $_SESSION['id_ciudad'];
  $id_embalaje = $_REQUEST['id_embalaje'];
  $precio = Precio::find($id_cliente, $id_ciudad_origen, $id_ciudad_destino, $id_embalaje);
  if (! $precio) {
    echo json_encode(array('success'=> false));
    exit;
  }
  $unidades = intval($_REQUEST['unidades']);
  $peso = floatval($_REQUEST['peso']);
  $valor_declarado = floatval($_REQUEST['valor_declarado']);
  $flete = $precio->liquidate($_SESSION['restriccion_peso'], $unidades, $peso, $valor_declarado);

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

if (isset($_POST['anular'])) {
  $guia = new Guia;
  if (! $guia->find($_POST['id'])) exit('La guía no existe');
  $comment = DBManager::escape($_POST['comentario']);
  if ($guia->deactivate()) {
    $action = 'anuló la guía y escribió: "'.$comment.'"';
    Logger::guia($guia->id, $action);
  } else {
    echo '<p class="expand">No se pudo anular la guía.</p>';
  }
  exit;
}
