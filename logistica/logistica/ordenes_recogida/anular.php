<?php
require "../../seguridad.php";
if ( ! isset($_POST['id']) or ! nonce_is_valid($_POST[NONCE_KEY] ,$_POST['id']) ) {
	echo "id";
	exit;
}
if( ! isset($_SESSION['permisos'][ORDENES_RECOGIDA_ANULAR]) ) {
	exit("permiso");
}
$orden = new OrdenRecogida;
if( $orden->Anular($_POST['id']) ) {
	echo "ok";
} else {
	echo 'error';
}
