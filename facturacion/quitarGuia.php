<?php
$raiz='../';
require $raiz.'seguridad.php';
$guia = new Guia;
if (! $factura = Factura::find($_GET['idfactura'])) exit('No existe la factura.');
if (! $guia->find($_GET['idguia'])) exit('No existe la guia.');
$factura->cliente();

if ($guia->remove_factura()) {
  $flete = $guia->total;
  $seguro = $guia->valorseguro;
  $total = $flete + $seguro;
  $descuento = $factura->cliente()->descuento;
  $cliente = new Cliente;
  $cliente->ActualizarValoresFactura($factura->id, $flete, $seguro, $total, $descuento, "restar");
  echo 'ok';
} else {
  echo 'Ha ocurrido un error, intentalo nuevamente.';
}
