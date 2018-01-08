<?php
require "../../seguridad.php";
if (! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['id'])) {
  require Logistica::$root."mensajes/id.php";
  exit;
}

if (! isset($_SESSION['permisos'][RUTAS_LOCALES_ANULAR])) {
  require Logistica::$root."mensajes/permiso.php";
  exit;
}
$ruta_local = new RutaLocal;
if (! $ruta_local->activate($_POST['id'])) {
  echo '<p class="expand">No se pudo activar la ruta local, intentalo nuevamente.</p>';
}
