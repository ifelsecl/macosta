<?php
require "../../seguridad.php";

if (isset($_POST['guardar'])) {
	if (!isset($_REQUEST['clientes'])) {
		echo '<h3><p>Agrega por lo menos un cliente...</p></h3>';
		exit;
	}
	$objOrden = new OrdenRecogida;
	$id_ciudad = $_POST['id_ciudad'];
	$fecha = $_POST['fecha'];
	$placa_vehiculo = $_POST['placa_vehiculo'];
	$numero_identificacion_conductor = $_POST['numero_identificacion_conductor'];
	$ayudantes = '';
	if(isset($_POST['ayudantes'])){
		foreach ($_POST['ayudantes'] as $ay) {
			$ayudantes .= "$ay;";
		}
		$ayudantes = substr($ayudantes, 0, -1);
	}
	$id_ruta = $_POST['id_ruta'];
	$clientes = '';
	foreach($_REQUEST['clientes'] as $cliente) {
		$n = "cliente$cliente";
		$clientes.=implode('--',$_REQUEST[$n]).';;';
	}
	$clientes =substr($clientes, 0, -2);
	if ($objOrden->Agregar($id_ciudad, $fecha, $placa_vehiculo, $numero_identificacion_conductor, $ayudantes, $id_ruta, $clientes)) {
		$logger = new Logger;
		$logger->Log($_SERVER['REMOTE_ADDR'], 'ha creado la orden de recogida '.$fecha.' - '.$placa_vehiculo.'.', 'OrdenesRecogida', date("Y-m-d H:i:s"), $_SESSION['userid']);
		echo "ok";
	}else{
		include $raiz.'mensajes/guardando_error.php';
	}
	exit;
}
if (isset($_POST['editar'])) {
	if (!isset($_REQUEST['clientes'])) {
		echo '<h3><p>Agrega por lo menos un cliente...</p></h3>';
		exit;
	}
	require_once Logistica::$root."class/OrdenRecogida.php";
	$objOrden = new OrdenRecogida;
	$id_ciudad = $_POST['id_ciudad'];
	$fecha = $_POST['fecha'];
	$placa_vehiculo = $_POST['placa_vehiculo'];
	$numero_identificacion_conductor = $_POST['numero_identificacion_conductor'];
	$ayudantes = '';
	if(isset($_POST['ayudantes'])){
		foreach ($_POST['ayudantes'] as $ay) {
			$ayudantes .= "$ay;";
		}
		$ayudantes = substr($ayudantes, 0, -1);
	}
	$id_ruta = $_POST['id_ruta'];
	$id = $_POST['id'];
	$clientes = '';
	foreach($_REQUEST['clientes'] as $cliente) {
		$n = "cliente$cliente";
		$clientes .= implode('--',$_REQUEST[$n]).';;';
	}
	$clientes = substr($clientes, 0, -2);
	if ($objOrden->Editar($id, $id_ciudad, $fecha, $placa_vehiculo, $numero_identificacion_conductor, $ayudantes, $id_ruta, $clientes)) {
		$logger = new Logger;
		$logger->Log($_SERVER['REMOTE_ADDR'], 'ha editado la orden de recogida <b>'.$id.'.</b>', 'OrdenesRecogida', date("Y-m-d H:i:s"), $_SESSION['userid']);
		echo 'ok';
	}else{
		include $raiz.'/mensajes/guardando_error.php';
	}
}
