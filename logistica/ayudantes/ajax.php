<?php
$raiz = '../../';
require_once $raiz.'/seguridad.php';

if (isset($_POST['save'])) {
	$ayudante = new Ayudante;

	$tipo_identificacion 	= $_POST['tipo_identificacion'];
	$numero_identificacion 	= $_POST['numero_identificacion'];
	$nombre 				= $_POST['nombre'];
	$id_ciudad 				= $_POST['id_ciudad'];

	if(empty($_POST['id'])) {
		if ( ! $ayudante->Agregar($tipo_identificacion, $numero_identificacion, $nombre, $id_ciudad) ) {
			include_once $raiz.'mensajes/guardando_error.php';
		}
	}else{
		$id = $_POST['id'];
		if ( ! $ayudante->Editar($id, $tipo_identificacion, $numero_identificacion, $nombre, $id_ciudad)) {
			include_once $raiz.'mensajes/guardando_error.php';
		}
	}
}
