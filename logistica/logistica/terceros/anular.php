<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][TERCEROS_ANULAR])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
if (! $tercero = Tercero::find($_GET['id'])) exit('No existe el tercero');
if ($tercero->deactivate()) {
	Logger::tercero($tercero->id, 'anuló el tercero');
} else {
	exit('No se pudo anular el tercero');
}
