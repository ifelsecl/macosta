<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][TERCEROS_CREAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
$tercero = new Tercero;
require '_form.php';
