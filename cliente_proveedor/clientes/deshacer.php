<?php
$raiz = "../../";
require $raiz."seguridad.php";
if ( ! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY],$_POST['id']) ) {
  include $raiz.'mensajes/id.php';
  exit;
}
if ( ! isset($_SESSION['permisos'][CLIENTES_DESHACER]) ) {
  include $raiz.'mensajes/permiso.php';
  exit;
}
$cliente = new Cliente;
if ( ! $cliente->find($_POST['id']) ) exit('No existe el cliente');
if( $cliente->activate() ) {
  $logger = new Logger;
  $logger->Log($_SESSION['ip'], 'activó el cliente', 'Clientes', date("Y-m-d H:i:s"), $_SESSION['userid'], $cliente->id);
  echo "ok";
}else{
  echo "Error: No se ha podido activar el cliente.";
}
