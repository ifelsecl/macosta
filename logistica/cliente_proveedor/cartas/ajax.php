<?php
$raiz='../../';
require_once $raiz.'seguridad.php';

if( !isset($_REQUEST['accion']) ) exit;
$accion=$_REQUEST['accion'];

require_once $raiz.'class/Carta.class.php';
$carta=new Carta;

if($accion=='crear'){
	
	$fecha=$_REQUEST['fecha'];
	$id_cliente=$_REQUEST['id_cliente'];
	$contacto=$_REQUEST['contacto'];
	$texto=$_REQUEST['texto'];
	
	if($numero=$carta->crear($fecha, $id_cliente, $contacto, $texto)){
		$r['error']=FALSE;
		$r['mensaje']='
<table class="no_resultados">
<tr>
<td><img src="img/archivos.png" /></td>
<td>&iexcl;Se ha creado la carta!</td>
<td>
</tr>
<tr>
<td colspan="2" align="center">
<a target="_blank" title="Imprimir" href="cliente_proveedor/cartas/imprimir.php?id='.$numero.'" id="imprimir">Imprimir</a></td>
</td>
</tr>
</table>';
	}else{
		$r['error']=TRUE;
		$r['mensaje']=mysql_error();
	}
	echo json_encode($r);
	exit;
}
?>