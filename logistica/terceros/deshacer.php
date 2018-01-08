<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][TERCEROS_DESHACER])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
if (! $tercero = Tercero::find($_GET['id'])) exit('No existe el tercero');
if ($tercero->activate()) {
	Logger::tercero($tercero->id, 'activó el tercero');
} else {
	exit('No se pudo activar el tercero');
}
