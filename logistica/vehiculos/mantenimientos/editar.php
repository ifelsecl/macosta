<?php
require '../../../seguridad.php';
Logistica::check_nonce($_GET['id'], $_GET[NONCE_KEY]);
if (! $vehiculo_mantenimiento = VehiculoMantenimiento::find($_GET['id'])) {
  exit('No existe el mantenimiento.');
}
require '_form.php';
