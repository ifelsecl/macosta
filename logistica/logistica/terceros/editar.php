<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][TERCEROS_EDITAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

if (! $tercero = Tercero::find($_GET['id'])) exit('No existe el tercero');
require '_form.php';
