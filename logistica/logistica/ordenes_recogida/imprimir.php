<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
	exit("<h2>Algo ha salido mal, intentalo nuevamente.</h2>");
}
if (! isset($_SESSION['permisos'][ORDENES_RECOGIDA_IMPRIMIR])) {
	include Logistica::$root."/mensajes/permiso.php";
	exit;
}
if (! $orden_recogida = OrdenRecogida::find($_GET['id'])) exit('No existe la orden de recogida.');
require_once Logistica::$root."php/tcpdf/PDF.php";
$pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator('Logística');
$pdf->SetAuthor('Edgar Ortega Ramírez <eortega@asesoriasit.com>');
$pdf->SetTitle('Orden de Recogida '.$orden_recogida->id);
$pdf->SetSubject("Orden de Recogida, Transportes Mario Acosta");
$pdf->SetKeywords('Orden de Recogida, Transportes Mario Acosta');
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "TRANSPORTES MARIO ACOSTA", PDF_HEADER_STRING);
//set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(5);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//set some language-dependent strings
$pdf->setLanguageArray($l);
// set default font subsetting mode
$pdf->setFontSubsetting(FALSE);
$pdf->setPrintHeader(FALSE);

$pdf->AddPage();
$pdf->OrdenRecogida();

$pdf->SetLeftMargin(8);
$pdf->SetRightMargin(10);
$pdf->Ln(0);

$pdf->Cell(0,1,'','T',1);
$h = 6;
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(20, $h, 'Orden No:', 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, $h, $orden_recogida->id, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(20, $h, 'Ciudad:', 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(60, $h, $orden_recogida->ciudad_nombre, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(15, $h, 'Fecha:', 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(25, $h, $orden_recogida->fecha, 0, 1);

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(20, $h, 'Ruta:', 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, $h, $orden_recogida->ruta, 0, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(20, $h, 'Conductor:', 0, 0);
$pdf->SetFont('helvetica', '', 10);

$orden_recogida->conductor();
$orden_recogida->vehiculo();
$pdf->Cell(60, $h,  substr($orden_recogida->conductor->nombre_completo, 0, 26), 0, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(15, $h, 'C.C. No:', 0, 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(25, $h, $orden_recogida->conductor->numero_identificacion, 0, 1);

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(20, $h, 'Pase No:', 0, 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, $h, $orden_recogida->conductor->categorialicencia, 0, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(30, $h,'Placa Vehículo:', 0, 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(50 ,$h, $orden_recogida->placa_vehiculo, 0, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(15, $h,'Marca:', 0, 0);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(30, $h, $orden_recogida->vehiculo->marca_nombre, 0, 1);

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(22, $h, 'Ayudantes:', 0, 0);
$pdf->SetFont('helvetica', '', 10);

$orden_recogida->ayudantes();
$ayudantes_string = '';
foreach ($orden_recogida->ayudantes as $ayudante) {
	$ayudantes_string .= $ayudante->nombre.' - ';
}

$ayudantes_string = substr($ayudantes_string, 0, -3);
$pdf->MultiCell(172, $h, $ayudantes_string, 0, 'L', false, 1);

// Colores del encabezado de la tabla de la orden de recogida
$pdf->SetFillColor(255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0, 0, 0);

// Header
$pdf->SetFont('helvetica', 'B', 9);
$header=array("CLIENTE",'OBSERVACION',"REMESA","UNID","HORA",'FLETES PAGADOS','FIRMA/SELLO CLIENTE');
$w = array(55,23,16,12,16,17,55);
$num_headers = count($header);
for($i = 0; $i < $num_headers; ++$i) {
	$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1,'',1, true);
}
$pdf->Ln();
// Colors, line width and bold font
$pdf->SetFillColor(255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFont('', '', 8);

$i = 1; //Cantidad de clientes agregados en la orden
if (!empty($orden_recogida->clientes)) {
	$lineas=explode(';;', $orden_recogida->clientes);
	foreach ($lineas as $linea) {
		$campos=explode('--', $linea);
		if( !isset($campos[2]) or !isset($campos[3]) ){
			$campos[2]='';
			$campos[3]='';
			$campos[4]='';
		}
		$pdf->MultiCell($w[0], 8, $campos[0].'<br>'.$campos[1], $border=1, $align='L', $fill=false, $ln=0, $x='', $y='', $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=8, $valign='M', $fitcell=false);
		$pdf->Cell($w[1], 8, $campos[2], 1, 0, 'L', 1,'',1, true);
		$pdf->Cell($w[2], 8, '', 1, 0, 'C', 1,'',1, true);
		$pdf->Cell($w[3], 8, $campos[3], 1, 0, 'C', 1,'',1, true);
		$pdf->Cell($w[4], 8, $campos[4], 1, 0, 'C', 1,'',1, true);
		$pdf->Cell($w[5], 8, '', 1, 0, 'C', 1,'',1, true);
		$pdf->Cell($w[6], 8, '', 1, 0, 'C', 1,'',1, true);
		$pdf->Ln();
		$i+=1;
	}
}
for($j=$i;$j<=20;$j++){
	$pdf->Cell($w[0], 10, '', 1, 0, 'L', 1,'',1, true);
	$pdf->Cell($w[1], 10, '', 1, 0, 'C', 1,'',1, true);
	$pdf->Cell($w[2], 10, '', 1, 0, 'C', 1,'',1, true);
	$pdf->Cell($w[3], 10, '', 1, 0, 'C', 1,'',1, true);
	$pdf->Cell($w[4], 10, '', 1, 0, 'C', 1,'',1, true);
	$pdf->Cell($w[5], 10, '', 1, 0, 'C', 1,'',1, true);
	$pdf->Cell($w[6], 10, '', 1, 0, 'C', 1,'',1, true);
	$pdf->Ln();
}
$style = array(
	'position' => '',
	'align' => 'C',
	'stretch' => false,
	'fitwidth' => true,
	'cellfitalign' => '',
	'border' => true,
	'hpadding' => 'auto',
	'vpadding' => 'auto',
	'fgcolor' => array(0,0,0),
	'bgcolor' => false,
	'text' => false,
	'font' => 'helvetica',
	'fontsize' => 8,
	'stretchtext' => 4
);
$pdf->write1DBarcode($orden_recogida->id, 'C128B', '', '', 80, 10, '', $style, 'N');
$pdf->Output("OrdenRecogida_$orden_recogida->id", 'I');
