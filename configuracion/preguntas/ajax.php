<?php
$raiz='../..';
require_once "$raiz/seguridad.php";
require_once "$raiz/class/guias.class.php";
$objGuia=new Guias();
if (isset($_POST['borrar'])) {
	$id=$_POST['id'];
	if ($objGuia->BorrarRazonDevolucion($id)) echo 'ok';
	else echo 'error';
	exit();
}
if (isset($_POST['guardar'])) {
	if ($objGuia->AgregarRazonDevolucion($_POST['nombre'])) echo 'ok';
	else echo 'error';
	exit();
}
if (isset($_POST['editar'])) {
	if ($objGuia->EditarRazonDevolucion($_POST['id'], $_POST['nombre'])) echo 'ok';
	else echo 'error';
	exit();
}
?>