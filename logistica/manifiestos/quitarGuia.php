<?php
require "../../seguridad.php";
Logistica::respond_as_json();

$return = array('success' => false, 'message' => '');
if (! $manifiesto = Manifiesto::find($_GET['idplanilla'])) {
	$return['message'] = 'No existe el manifiesto.';
	echo json_encode($return);
	exit;
}
if (! $manifiesto->remove_guia($_GET['idguia'])) {
	$return['message'] = 'No existe la guía.';
	echo json_encode($return);
	exit;
}
Logger::guia($_GET['idguia'], 'quitó la guía del manifiesto '.$manifiesto->id);
$return['success'] = true;
echo json_encode($return);
