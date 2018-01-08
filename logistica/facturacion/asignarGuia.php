<?php
require "../seguridad.php";
$guia = new Guia;
if (! $guia->find($_REQUEST['id_guia'])) exit('No existe la guía.');
if ($guia->assign_factura($_REQUEST['id_factura'])) {
	$valorflete = $guia->total;
	$valorseguro = $guia->valorseguro;
	$total = $valorflete + $valorseguro;
	$descuento = $guia->cliente()->descuento;
	$cliente = new Cliente;
	$cliente->ActualizarValoresFactura($_REQUEST['id_factura'], $valorflete, $valorseguro, $total, $descuento, "sumar");
} else {
	echo '<table style="text-align:center"><tr>';
	echo '<td><img src="css/images/notice.png" /></td>';
	echo '<td>No se pudo quitar la guía de la factura...<br>Intentalo nuevamente.</td>';
	echo '</tr></table>';
}
