<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][AYUDANTES_EDITAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
if (! $ayudante = Ayudante::find($_GET['id'])) exit('No existe el ayudante');
require '_form.php';
