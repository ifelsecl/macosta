<?php
$raiz = '../../';
require $raiz.'seguridad.php';
$logger = new Logger;

if(isset($_REQUEST['g'])){
	$objVendedor=new Vendedor;
	$nombre=$_REQUEST['nombre'];
	$cedula=$_REQUEST['cedula'];
	$id_ciudad=$_REQUEST['id_ciudad'];
	$direccion=$_REQUEST['direccion'];
	$telefono=$_REQUEST['telefono'];
	$email=$_REQUEST['email'];
	$codigo_siigo=$_REQUEST['codigo_siigo'];
	if($objVendedor->Crear($nombre, $cedula, $id_ciudad, $direccion, $telefono, $email, $codigo_siigo)){
		$logger->log($_SERVER['REMOTE_ADDR'], 'ha creado el vendedor "'.$nombre.'".', "Vendedores", date("Y-m-d H:i:s"), $_SESSION['userid']);
		exit('ok');
	}else{
		include_once $raiz.'mensajes/guardando_error.php';
		exit;
	}
}
if (isset($_REQUEST['e'])) {
	if(! isset($_REQUEST['id']) or ! nonce_is_valid($_REQUEST[NONCE_KEY], $_REQUEST['id'])){
		include $raiz.'mensajes/id.php';
		exit;
	}
	$objVendedor = new Vendedor;
	$id=$_REQUEST['id'];
	$nombre=$_REQUEST['nombre'];
	$cedula=$_REQUEST['cedula'];
	$id_ciudad=$_REQUEST['id_ciudad'];
	$direccion=$_REQUEST['direccion'];
	$telefono=$_REQUEST['telefono'];
	$email=$_REQUEST['email'];
	$codigo_siigo=$_REQUEST['codigo_siigo'];
	if($objVendedor->Editar($id, $nombre, $cedula, $id_ciudad, $direccion, $telefono, $email, $codigo_siigo)){
		$logger->log($_SERVER['REMOTE_ADDR'], 'ha editado el vendedor '.$id.' "'.$nombre.'".', "Vendedores", date("Y-m-d H:i:s"), $_SESSION['userid']);
		echo 'ok';
	}else{
		include_once $raiz.'mensajes/guardando_error.php';
	}
	exit;
}
if(isset($_REQUEST['anular'])){
	if(! isset($_REQUEST['id']) or ! nonce_is_valid($_REQUEST[NONCE_KEY], $_REQUEST['id'])){
		include $raiz.'mensajes/id.php';
		exit;
	}
	if( ! isset($_SESSION['permisos'][VENDEDORES_ANULAR]) ){
		include $raiz."mensajes/permiso.php";
		exit;
	}
	$objVendedor = new Vendedor;
	$id=$_REQUEST['id'];
	if($objVendedor->Anular($id)){
		$logger->log($_SERVER['REMOTE_ADDR'], 'ha anulado el vendedor '.$id, "Vendedores", date("Y-m-d H:i:s"), $_SESSION['userid']);
		echo 'ok';
	}else{
		echo '<p>No se ha podido anular el vendedor, intentalo nuevamente.</p>';
	}
	exit;
}
if(isset($_REQUEST['deshacer'])){
	if(! isset($_REQUEST['id']) or ! nonce_is_valid($_REQUEST[NONCE_KEY], $_REQUEST['id'])){
		include $raiz.'mensajes/id.php';
		exit;
	}
	if( ! isset($_SESSION['permisos'][VENDEDORES_DESHACER]) ){
		include $raiz."mensajes/permiso.php";
		exit;
	}
	$objVendedor = new Vendedor;
	$id = $_REQUEST['id'];
	if($objVendedor->Activar($id)){
		$logger->log($_SERVER['REMOTE_ADDR'], 'ha activado el vendedor '.$id, "Vendedores", date("Y-m-d H:i:s"), $_SESSION['userid']);
		echo 'ok';
	}else{
		echo '<p>No se ha podido activado el vendedor, intentalo nuevamente.</p>';
	}
	exit;
}
