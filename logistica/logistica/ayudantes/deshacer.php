<?php
$raiz = "../../";
require $raiz."seguridad.php";
if ( !isset($_POST['id']) or !nonce_is_valid($_POST[NONCE_KEY],$_POST['id']) ) {
	include $raiz.'mensajes/id.php';
	exit;
}
if(! isset($_SESSION['permisos'][AYUDANTES_DESHACER])) {
	include $raiz.'mensajes/permiso.php';
	exit;
}
$objAyudante = new Ayudante;
if ($objAyudante->Activar($_POST['id'])) {
	echo "ok";
}else{
	echo '<p class="expand">No se ha podido activar el ayudante, intentalo nuevamente.</p>';
}
