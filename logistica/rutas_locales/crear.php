<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][RUTAS_LOCALES_CREAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$ruta_local = new RutaLocal;

$conductores = Conductor::all('activos');
$camiones = Vehiculo::all('activos');

require '_form.php';
