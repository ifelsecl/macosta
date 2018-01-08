<?php
$raiz = "../../";
require $raiz.'seguridad.php';

$guia = new Guia;
if (isset($_GET['id'])) {
  if (! $relacion = Relacion::find($_GET['id']) ) exit('No existe la relacion.');
} else {
  $id_cliente = $_POST['relacion']['id_cliente'];
  $periodo = '';
  if (in_array($_POST['tipo'], array('numeros', 'documento'))) {
    if (! isset($_POST['relacion']['guias']) ) exit('Agrega por lo menos una guia.');
    foreach($_POST['relacion']['guias'] as $id) {
      $guias[$id] = $guia->find($id);
    }
  } else {
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $f_fecha_inicio = strftime('%B %d, %Y', strtotime($fecha_inicio));
    $f_fecha_fin = strftime('%B %d, %Y', strtotime($fecha_fin));
    $periodo = $f_fecha_inicio.' - '.$f_fecha_fin;
    $formas_pago = $_POST['tipos'];
    $guias = $guia->all_by_formas_de_pago($id_cliente, $formas_pago, $fecha_inicio, $fecha_fin);
  }
  if (empty($guias)) exit('No se encontraron guias para crear la relacion.');
  $_POST['relacion']['periodo'] = $periodo;
  $_POST['relacion']['guias'] = implode(',', array_keys($guias));
  $relacion = new Relacion($_POST['relacion']);
  if (strtoupper($_POST['save']) == 'SI') $relacion->create();
}

$total_flete = 0;
$total_seguro = 0;
$total_peso = 0;
$data = array();
$guias = explode(',', $relacion->guias);
foreach ($guias as $id) {
  if ($g = $guia->find($id)){
    $total_flete += $g->total;
    $total_seguro += $g->valorseguro;
    $total_peso += $g->peso;
    $data[] = array($g->contacto()->nombre_completo, $g->unidades, $g->id, $g->documentocliente, $g->contacto->ciudad_nombre, $g->idfactura, number_format($g->valorseguro), number_format($g->total), number_format($g->total+$g->valorseguro));
  }
}

require_once $raiz."php/tcpdf/PDF.php";

$pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Logística');
$pdf->SetAuthor('Edgar Ortega Ramírez');
$pdf->SetTitle("Relación ".$relacion->cliente()->nombre_completo);
$pdf->SetSubject("Relación - Transportes Mario Acosta");
$pdf->SetKeywords('Relación, Transportes Mario Acosta, Facturación');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "TRANSPORTES MARIO ACOSTA & CIA LTDA", PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(10, PDF_MARGIN_TOP, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->setFontSubsetting(false);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);
$tipo_identificacion = Cliente::$tipos_identificacion[$relacion->cliente->tipo_identificacion];

$h = 5;
$formato_fecha = "%B %d, %Y";

$pdf->MultiCell(20, $h, 'No: '.$relacion->id, 'TLR', '', false, 0);
$pdf->MultiCell(110, $h, 'Cliente: '.$relacion->cliente->id.' - '.$relacion->cliente->nombre_completo, 'TR', '', false, 0);
$pdf->MultiCell(60, $h, $tipo_identificacion.': '.$relacion->cliente->numero_identificacion_completo, 'RT', '', false, 1);
if (empty($relacion->periodo)){
  $pdf->MultiCell(130, $h, 'Dirección: '.substr($relacion->cliente->direccion, 0, 35), 1, 'RB', false, 0);
  $pdf->MultiCell(60, $h, 'Fecha emisión: '.strftime($formato_fecha, strtotime($relacion->fecha_emision)), 1, 'LRB', false, 1);
} else {
  $pdf->MultiCell(130, $h, 'Dirección: '.substr($relacion->cliente->direccion, 0, 35), 1, 'RB', false, 0);
  $pdf->MultiCell(60, $h, 'Fecha emisión: '.strftime($formato_fecha, strtotime($relacion->fecha_emision)), 1, 'LRB', false, 1);
  $pdf->MultiCell(190, $h, 'Período: '.$relacion->periodo, 1, 'RB', false, 1);
}

$pdf->Ln(2);
$pdf->Relacion($data);
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(100, 4, '', 0, '', false, 0);
$pdf->MultiCell(30, 4, 'Peso Total', 1, 'R', false, 0);
$pdf->MultiCell(40, 4, number_format($total_peso).' Kg', 1, 'R', false, 1);
$pdf->Ln(2);
$pdf->MultiCell(100, 4, '', 0, '', false, 0);
$pdf->MultiCell(30, 4, 'Total Flete', 1, 'R', false, 0);
$pdf->MultiCell(40, 4, number_format($total_flete), 1, 'R', false, 1);
$pdf->MultiCell(100, 4, '', 0, '', false, 0);
$pdf->MultiCell(30, 4, '(+) Total Seguro', 1, 'R', false, 0);
$pdf->MultiCell(40, 4, number_format($total_seguro), 1, 'R', false, 1);

$pdf->SetFillColor(100,100,100);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.3);
$pdf->MultiCell(100, 4, '', 0, '', false, 0);
$pdf->MultiCell(30, 4, 'Total', 1, 'R', true, 0);
$pdf->MultiCell(40, 4, number_format($total_flete+$total_seguro), 1, 'R', true, 1);

if (! headers_sent()) {
  $accion = 'creó una relación para '.$relacion->cliente->nombre_completo;
  if (! empty($relacion->periodo) ){
    $accion .= ' desde '.$relacion->periodo;
  }
  $Logger = new Logger;
  $Logger->Log($_SESSION['ip'], $accion, 'Facturacion', date("Y-m-d H:i:s"), $_SESSION['userid']);
  $pdf->Output("Relacion_".$relacion->cliente->nombre_completo, 'I');
}
