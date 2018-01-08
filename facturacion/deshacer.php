<?php
require "../seguridad.php";
if (!isset($_POST['idfactura']) or !nonce_is_valid($_POST[NONCE_KEY],$_POST['idfactura'])) {
	echo "id";
	exit;
}
if(! isset($_SESSION['permisos'][FACTURACION_DESHACER])) {
	echo "permiso";
	exit;
}

$id = $_POST['idfactura'];
$factura = new Factura;
if ($factura->Activar($id)) {
	$logger = new Logger;
	$logger->Log($_SERVER['REMOTE_ADDR'], 'ha activado la factura '.$id, 'Facturacion', date("Y-m-d H:i:s"), $_SESSION['userid']);
	echo "ok";
}else{
	echo "error";
}
