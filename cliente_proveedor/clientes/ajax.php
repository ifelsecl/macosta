<?php
require "../../seguridad.php";

if (isset($_POST['save'])) {
  if (empty($_POST['id'])) {
    $cliente = new Cliente($_POST);
    if ($cliente->create()) {
      Logger::cliente($cliente->id, 'creó el cliente');
    } else {
      include Logistica::$root.'mensajes/guardando_error.php';
    }
    exit;
  } else {
    if (! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['id'])) {
      include Logistica::$root."mensajes/id.php";
      exit;
    }
    $cliente = new Cliente;
    if (! $cliente->find($_POST['id'])) exit('No existe el cliente');
    $changes = $cliente->updated_attributes($_POST);
    if ($changes) {
      if ($cliente->update($_POST)) {
        $cliente->update_seguro_in_lista_precios();
        Logger::cliente($cliente->id, "editó el cliente".$changes);
      } else {
        include Logistica::$root.'mensajes/guardando_error.php';
      }
    } else {
      echo 'No hay nada para actualizar';
    }
    exit;
  }
}

if (isset($_GET['buscarembalaje'])) {
  echo json_encode(Embalaje::autocomplete($_GET['term']));
  exit;
}

if (isset($_POST['borrar_lista_precios'])) {
  usleep(500000);
  $cliente = new Cliente;
  if (! $cliente->find($_POST['id_cliente'])) exit('No existe el cliente.');
  if ($cliente->delete_lista_precios()) {
    Logger::cliente($cliente->id, 'eliminó la lista de precios');
    echo 'ok';
  } else {
    echo 'error';
  }
  exit;
}

if (isset($_POST['eliminar'])) {
  if (! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['id'])) {
    include Logistica::$root.'mensajes/id.php';
    exit;
  }
  if (! isset($_SESSION['permisos'][CLIENTES_ELIMINAR])) {
    include Logistica::$root.'mensajes/permiso.php';
    exit;
  }

  $cliente = new Cliente;
  if (! $cliente->find($_POST['id'])) exit('No existe el cliente.');
  if ($cliente->has_invoices()) {
    exit('<p><i class="icon-exclamation-sign"></i> ¡El cliente tiene facturas y <b>no puede ser eliminado</b>!</p>');
  }
  if ($cliente->delete($_POST['id_nuevo_cliente'])) {
    $accion = 'eliminó el cliente y escribió: <b>'.htmlspecialchars(addslashes($_POST['comentario'])).'</b>';
    Logger::cliente($cliente->id, $accion);
    echo "ok";
  } else {
    include Logistica::$root.'mensajes/guardando_error.php';
  }
  exit;
}

if (isset($_REQUEST['cambiar_clave'])) {
  if (! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['id'])) {
    include Logistica::$root."mensajes/id.php";
    exit;
  }
  $clave  = $_REQUEST['clave'];
  $clave2 = $_REQUEST['clave2'];
  if (empty($clave)) exit('<label class="expand">Escribe una contraseña.</label>');
  if ( $clave != $clave2 ) exit('<label class="expand">Las contraseñas no coinciden.</label>');
  $cliente = new Cliente;
  if (! $cliente->find($_POST['id']) ) exit('<label class="expand">No existe el cliente</label>');

  if ( $cliente->change_password($clave) ) {
    Logger::cliente($cliente->id, "cambió la contraseña");
    exit('ok');
  } else {
    exit('Ha ocurrido un error, intentalo nuevamente.');
  }
}
