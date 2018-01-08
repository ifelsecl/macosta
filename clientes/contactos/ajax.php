<?php
$raiz = "../../";
require "../seguridad.php";
require_once $raiz."php/Nonce.inc.php";

if (isset($_REQUEST['editar'])) {
  $cliente = new Cliente;
  if (! $cliente->find($_POST['id'])) exit('No existe el cliente.');

  $changes = $cliente->updated_attributes($_POST);
  if ($changes) {
    if ($cliente->update($_POST)) {
      Logger::cliente($cliente->id, "edito el cliente".$changes);
    } else {
      include $raiz.'mensajes/guardando_error.php';
    }
  } else {
    echo 'No hay nada para actualizar';
  }
  exit;
}
