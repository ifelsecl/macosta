<?php
require "../../seguridad.php";
if (! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['id'])) {
  include Logistica::$root.'mensajes/id.php';
  exit;
}
if (! isset($_SESSION['permisos'][PRODUCTOS_ANULAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

$producto = new Producto;
if ($producto->Anular($_POST['id'])) {
  Logger::producto($_POST['id'], 'anuló el producto');
} else {
  echo 'No se pudo guardar el producto, intentalo nuevamento.';
}
