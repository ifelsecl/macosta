<?php
require "../../seguridad.php";
if (! isset($_POST['numero_identificacion']) OR ! nonce_is_valid($_POST[NONCE_KEY], $_POST['numero_identificacion'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][CONDUCTORES_DESHACER])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
$conductor = new Conductor;
$logger = new Logger;
if(! $conductor->find($_POST['numero_identificacion'])) exit('No existe el conductor');
if(! $conductor->activate()) exit('Ocurrio un error, intentalo nuevamente.');
$logger->Log($_SESSION['ip'], 'activó el conductor', 'Conductores', date("Y-m-d H:i:s"), $_SESSION['userid'], $conductor->numero_identificacion);
