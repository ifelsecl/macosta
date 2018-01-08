<?php
require_once "../../seguridad.php";
if (! isset($_GET['placa']) or ! nonce_is_valid($_GET[NONCE_KEY],$_GET['placa'])) {
	exit("id");
}
if (! isset($_SESSION['permisos'][CAMIONES_ANULAR])) {
	exit('permiso');
}
$vehiculo = new Vehiculo;
if ($vehiculo->deactivate($_GET['placa'])) {
	echo 'ok';
} else {
	echo 'error';
}
