<?php
$raiz="../../";
require_once $raiz."seguridad.php";
if ( !isset($_SESSION['permisos'][AYUDANTES_CREAR]) ) {
	include $raiz."mensajes/permiso.php";
	exit;
}
require '_form.php';
