<?php
require_once "../../seguridad.php";
require_once Logistica::$root."class/planillasC.class.php";
$planilla = new PlanillasC;

$id_manifiesto = $_GET['idplanilla'];
$id_guia = $_GET['idguia'];
$pos = $_GET['posicion'];

if ($planilla->AsignarGuia($id_manifiesto, $id_guia, $pos)) {
	Logger::guia($id_guia, "asignó la guía al manifiesto ".$id_manifiesto);
	echo 'idguia='.$_GET['idguia'].'&idplanilla='.$_GET['idplanilla'].'&posicion='.$_GET['posicion'];
} else {
	echo 0;
}
