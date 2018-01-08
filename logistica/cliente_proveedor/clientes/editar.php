<?php
require "../../seguridad.php";
if (isset($_GET['dialog'])) {
  if (! isset($_GET['id'])) {
    include Logistica::$root."mensajes/id.php";
    exit;
  }
} else {
  if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
    include Logistica::$root."mensajes/id.php";
    exit;
  }
}

if (! isset($_SESSION['permisos'][CLIENTES_EDITAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$cliente = new Cliente;
if (! $cliente->find($_GET['id'])) exit('No existe el cliente');
require '_form.php';
