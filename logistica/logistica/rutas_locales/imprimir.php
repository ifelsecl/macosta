<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  require Logistica::$root.'mensajes/id.php';
  exit;
}
if (! isset($_SESSION['permisos'][RUTAS_LOCALES_IMPRIMIR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}
if (! $ruta_local = RutaLocal::find($_GET['id'])) exit('No existe la Ruta Local.');

require "pdf.php";
$pdf = new RutaLocalPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
$pdf->info();
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage();

$height = 8;
$pdf->SetFont('', 'B');
$pdf->Cell(25, $height, 'Ruta Local:', 'TLB', 0);
$pdf->SetFont('');
$pdf->Cell(25, $height, $ruta_local->id, 'TB', 0);

$pdf->SetFont('', 'B');
$pdf->Cell(20, $height, 'Ciudad:', 'LTB', 0);
$pdf->SetFont('');
$pdf->Cell(70, $height, $ruta_local->ciudad_nombre, 'TB', 0);

$pdf->SetFont('', 'B');
$pdf->Cell(20, $height, 'Fecha:', 'TBL', 0);
$pdf->SetFont('');
$pdf->Cell(30, $height, $ruta_local->fecha_corta, 'TBR', 1);

$pdf->SetFont('', 'B');
$pdf->Cell(30, $height, 'Conductor:', 'BL', 0);
$pdf->SetFont('');
$pdf->Cell(110, $height, $ruta_local->conductor()->nombre_completo, 'BR', 0);

$pdf->SetFont('', 'B');
$pdf->Cell(25, $height, 'Vehículo:', 'BL', 0);
$pdf->SetFont('');
$pdf->Cell(25, $height, $ruta_local->placa(), 'BR', 1);

$pdf->Ln(2);
$pdf->SetFont('', 'B', 8);
$header = array("GUIA", 'CLIENTE', "CONTACTO", "DESTINO", "UNIDS", 'VLR MERC.', 'FLETE AL COBRO');
$w = array(18, 41, 41, 32, 12, 19, 27);
$num_headers = count($header);
for($i = 0; $i < $num_headers; ++$i) {
  $pdf->Cell($w[$i], 6, $header[$i], 1, 0);
}
$pdf->Ln();
$pdf->SetFont('', '', 8);

$guias = $ruta_local->guias();
if (empty($guias)) {
  $pdf->Cell(array_sum($w), 15, 'NO TIENE GUIAS ASIGNADAS...', 'LBR', 1, 'C');
} else {
  $total_flete  = 0;
  $total_unidades = 0;
  foreach ($guias as $g) {
    $pdf->Cell($w[0], 6, $g->id, 'LB');
    $pdf->Cell($w[1], 6, substr($g->cliente_nombre_completo, 0, 22), 'LB');
    $pdf->Cell($w[2], 6, substr($g->contacto_nombre_completo,0, 22), 'LB');
    $pdf->Cell($w[3], 6, substr($g->contacto_ciudad_nombre, 0, 15), 'LB');
    $pdf->Cell($w[4], 6, number_format($g->unidades), 'LB', 0, 'R');
    $pdf->Cell($w[5], 6, number_format($g->valordeclarado), 'LB', 0, 'R');
    $flete = "-";
    if ($g->formapago == 'FLETE AL COBRO') {
      $flete = number_format($g->total + $g->valorseguro);
      $total_flete += $g->total + $g->valorseguro;
    }
    $pdf->Cell($w[6], 6, $flete, 'LBR', 0, 'R');
    $pdf->Ln();
    $total_unidades += $g->unidades;
  }
  $pdf->SetFont('', 'B', 10);
  $pdf->Cell($w[0]+$w[1]+$w[2]+$w[3], 8, 'TOTAL', 'LB', 0, 'R');
  $pdf->Cell($w[4], 8, number_format($total_unidades), 'LB', 0, 'R');
  $pdf->Cell($w[5], 8, '', 'LB');
  $pdf->Cell($w[6], 8, number_format($total_flete), 'LBR', 1, 'R');
}
$pdf->SetFont('', '', 9);
$pdf->Ln(3);
$pdf->MultiCell(190, 14, 'Observaciones: '.$ruta_local->observaciones, 0, '', false, 0, '','',true, 0, false, true, 30, 'TOP', false);
$pdf->Output("Ruta_Local_$ruta_local->id", 'I');
