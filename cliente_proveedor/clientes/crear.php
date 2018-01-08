<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CLIENTES_CREAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$cliente = new Cliente;
require '_form.php';
