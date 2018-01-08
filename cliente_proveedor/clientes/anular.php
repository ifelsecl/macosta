<?php
require "../../seguridad.php";
if (! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY],$_POST['id'])) {
  include Logistica::$root.'mensajes/id.php';
  exit;
}
if (! isset($_SESSION['permisos'][CLIENTES_ANULAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

$cliente = new Cliente;
if (! $cliente->find($_POST['id'])) exit('No existe el cliente');
$logger = new Logger;
if ($cliente->deactivate()) {
  $accion = 'anuló el cliente y escribió: '.htmlspecialchars(addslashes($_REQUEST['comentario']));
  $logger->Log($_SESSION['ip'], $accion, 'Clientes', date("Y-m-d H:i:s"), $_SESSION['userid'], $cliente->id);
  echo "ok";
}else{
  include Logistica::$root.'mensajes/guardando_error.php';
}
