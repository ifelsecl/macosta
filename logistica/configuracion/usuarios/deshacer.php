<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][USUARIOS_DESHACER])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
if (! $usuario = Usuario::find($_GET['id'])) exit('No existe el usuario.');
if ($usuario->activate()) {
	echo 'ok';
	Logger::usuario($usuario->id, 'activó el usuario.');
} else {
	echo "Por favor, intentalo nuevamente.";
}
