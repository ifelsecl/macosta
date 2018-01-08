<?php
require '../../seguridad.php';
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
	include Logistica::$root."mensajes/id.php";
	exit;
}
if (! isset($_SESSION['permisos'][RESOLUCIONES_ENTRAR]))  {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
if (! $resolucion = Resolucion::find($_GET['id'])) exit('No existe la resolución.');
require '_form.php';
