<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][GUIAS_DESHACER])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
if(! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY], $_POST['id'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}

$guia = new Guia;
if(! $guia->find($_POST['id']) ) exit('No existe la guía.');
if($guia->activate()) {
	Logger::guia($_POST['id'], 'activó la guía');
	echo 'ok';
}else{
	echo "error";
}
