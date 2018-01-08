<?php
require "../../seguridad.php";
if ( !isset($_POST['id']) or !nonce_is_valid($_POST[NONCE_KEY],$_POST['id']) ) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if(! isset($_SESSION['permisos'][AYUDANTES_ANULAR])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
$id = $_POST['id'];
$ayudante = new Ayudante;
if ($ayudante->Anular($id)) echo "ok";
else{
	echo '<p class="expand">No se ha podido anular el ayudante, intentalo nuevamente.</p>';
}
