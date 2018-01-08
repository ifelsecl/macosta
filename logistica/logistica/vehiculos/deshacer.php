<?php
require "../../seguridad.php";
if (! isset($_GET['placa']) OR !nonce_is_valid($_GET[NONCE_KEY], $_GET['placa']) ) {
	echo "id";
	exit;
}
if (! isset($_SESSION['permisos'][CAMIONES_DESHACER])) {
	exit('permiso');
}
$vehiculo = new Vehiculo;
if ($vehiculo->activate($_GET['placa'])) {
	echo 'ok';
} else {
	echo 'error';
}
