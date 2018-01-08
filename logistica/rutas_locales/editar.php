<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][RUTAS_LOCALES_EDITAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (! $ruta_local = RutaLocal::find($_REQUEST['id'])) exit('No existe la Ruta Local.');
$ruta_local->ciudad();
$ruta_local->guias();
$conductores = Conductor::all('activos');
$camiones = Vehiculo::all('activos');

require '_form.php';
