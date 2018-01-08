<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PLANILLAS_DESHACER])) {
	exit('permiso');
}
if (! isset($_GET['idplanilla']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['idplanilla'])) {
	exit("id");
}
$idplanilla = $_GET['idplanilla'];
require_once Logistica::$root."class/planillasC.class.php";
$objPlanilla = new PlanillasC;
if ($objPlanilla->Activar($idplanilla, $_SESSION['username'])) {
	$logger = new Logger;
	$logger->Log($_SERVER['REMOTE_ADDR'], 'ha activado la planilla "'.$idplanilla.'"', 'Planillas', date("Y-m-d H:i:s"), $_SESSION['userid']);
	echo "ok";
} else {
	echo "error";
}
