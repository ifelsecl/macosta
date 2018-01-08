<?php
require "../../seguridad.php";
Logistica::check_nonce($_GET['placa'], $_GET[NONCE_KEY]);
require_once Logistica::$root."php/tcpdf/PDF.php";
$configuracion = new Configuracion;
$pdf = new TarjetaVehiculo;

if (! $vehiculo = Vehiculo::find($_GET['placa'])) exit('No existe el vehículo');

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(2, PDF_MARGIN_TOP, 2);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->SetFillColor(255,255,255);
$pdf->setPrintFooter(false);
$pdf->setPrintHeader(false);

$pdf->AddPage();
$pdf->setInfo();
$pdf->SetFont('', 'B', 10);
$pdf->SetTextColor(39, 97, 156);
$pdf->Image(K_PATH_IMAGES.'logo.gif', 2, 2, 12, 12);
$pdf->MultiCell(80, 5, strtoupper($configuracion->nombre_empresa), 0, 'C', FALSE, TRUE, 15, 2);
$pdf->SetFont('', '',7);

$pdf->Ln(0);
$y = $pdf->GetY();
$x = 16;
$left_margin = 2;
$pdf->MultiCell(22, 2, $configuracion->direccion_empresa, 0, 'C', FALSE, FALSE, $x, $y);
$pdf->MultiCell(63, 2, 'Tels: '.$configuracion->telefono_empresa, 0, 'C', FALSE, TRUE, $x+20, $y);
$pdf->MultiCell(50, 2, $configuracion->email_empresa, 0, 'C', FALSE, FALSE, $x);
$pdf->MultiCell(35, 2, $configuracion->ciudad_empresa, 0, 'C', FALSE, TRUE);

$pdf->SetFont('', 'B', 9);
$pdf->MultiCell(50, 6, 'TARJETA NUMERO: '.$vehiculo->t_operacion, 0, 'C', FALSE, TRUE, 35, 14);

$pdf->SetFont('', '', 6);

//Rectangulo Superior
$pdf->RoundedRect(2, 20, 100, 20, 1);
$pdf->MultiCell(34, 3, 'FECHA DE EXPEDICION', 'R', 'C', FALSE, FALSE, 2);
$pdf->MultiCell(34, 3, 'FECHA DE VENCIMIENTO', 'R', 'C', FALSE, FALSE, 36);
$pdf->MultiCell(34, 3, 'PLACA', '', 'C', FALSE, TRUE, 70);
$pdf->Ln(0);
$pdf->SetTextColor(0);
$pdf->SetFont('', '',8);
$pdf->MultiCell(34, 3, date('Y/m/d', strtotime($vehiculo->fecha_afiliacion)), 'RB', 'C', FALSE, FALSE, 2);
$pdf->MultiCell(34, 3, date('Y/m/d', strtotime($vehiculo->f_venc_toperacion)), 'LRB', 'C', FALSE, FALSE, 36);
$pdf->MultiCell(32, 3, $vehiculo->placa, 'LB', 'C', FALSE, TRUE, 70);
$pdf->Ln(0);
$pdf->SetTextColor(39, 97, 156);
$pdf->SetFont('', '',6);
$pdf->MultiCell(36, 3, 'MARCA', 'R', 'C', FALSE, FALSE, 2);
$pdf->MultiCell(20, 3, 'CLASE', 'R', 'C', FALSE, FALSE, 38);
$pdf->MultiCell(26, 3, 'COLOR', 'R', 'C', FALSE, FALSE, 58);
$pdf->MultiCell(18, 3, 'MODELO', '', 'C', FALSE, TRUE, 82);
$pdf->Ln(0);
$pdf->SetTextColor(0);
$pdf->SetFont('', '',8);
$pdf->MultiCell(36, 3, $vehiculo->marca_nombre, 'RB', 'C', FALSE, FALSE, 2);
$pdf->MultiCell(20, 3, 'CAMION', 'RB', 'C', FALSE, FALSE, 38);
$pdf->MultiCell(26, 3, $vehiculo->color_nombre, 'RB', 'C', FALSE, FALSE, 58);
$pdf->MultiCell(20, 3, $vehiculo->modelo, 'B', 'C', FALSE, TRUE, 82);

$pdf->Ln(0);
$pdf->SetTextColor(39, 97, 156);
$pdf->SetFont('', '',6);

$pdf->MultiCell(68, 3, 'PROPIETARIO', 'R', 'C', FALSE, FALSE, 2);
$pdf->MultiCell(34, 3, 'NO DE SERIE', '', 'C', FALSE, TRUE, 70);
$pdf->Ln(0);
$pdf->SetTextColor(0);
$pdf->SetFont('', '',8);
$pdf->MultiCell(68, 3, $vehiculo->propietario()->nombre_completo, 'R', 'C', FALSE, FALSE, 2);
$pdf->MultiCell(34, 3, $vehiculo->serie, 0, 'C', FALSE, TRUE, 70);
$pdf->Ln(3);
$pdf->SetTextColor(39, 97, 156);

//Rectangulo Inferior
$pdf->RoundedRect(5, 44, 30, 12, 1);

$pdf->Ln(1);
$pdf->SetFont('', '',6);
$pdf->MultiCell(30, 2, 'SEDE', 0, 'C', FALSE, true, 5);
$pdf->Ln(0);
$pdf->SetTextColor(0);
$pdf->SetFont('', '',7);
$pdf->MultiCell(30, 2, 'BARRANQUILLA', 'B', 'C', FALSE, TRUE, 5);
$pdf->Ln(0);
$pdf->SetTextColor(39, 97, 156);
$pdf->SetFont('', '',6);
$pdf->MultiCell(30, 2, 'RADIO DE ACCION', 0, 'C', FALSE, TRUE, 5);
$pdf->Ln(0);
$pdf->SetTextColor(0);
$pdf->SetFont('', '', 7);
$pdf->MultiCell(30, 2, 'NACIONAL', '', 'C', FALSE, TRUE, 5);

//Rectangulo Principal Frontal
$pdf->RoundedRect(0, 0, 104, 60, 0);

//Rectangulo Principal Trasero
$pdf->RoundedRect(109, 0, 101, 60, 0);

$pdf->SetFont('', '', 7);
$pdf->MultiCell(70, 3, 'Propietario: '.$vehiculo->propietario->nombre_completo, '', 'L', FALSE, FALSE, 123, 4);
$pdf->MultiCell(70, 3, 'Dirección: '.$vehiculo->propietario->direccion, '', 'L', FALSE, FALSE, 123, 7);
$pdf->MultiCell(70, 3, 'Teléfonos: '.$vehiculo->propietario->telefono, '', 'L', FALSE, FALSE, 123, 10);

$pdf->SetFont('', '', 8);
$pdf->Image(K_PATH_IMAGES.'firma.jpg', 133, 18, 50, 20);
$pdf->MultiCell(70, 3, 'FIRMA AUTORIZADA Y SELLO', 'T', 'C', FALSE, FALSE, 123, 40);
$pdf->SetFont('', '', 7);
$pdf->MultiCell(70, 3, 'Cualquier irregularidad por favor informar a: ', '', 'C', FALSE, FALSE, 123, 50);
$pdf->MultiCell(70, 3, 'Tels: '.$configuracion->telefono_empresa, '', 'C', FALSE, FALSE, 123, 54);


$pdf->SetTextColor(120);
$pdf->SetFont('', '', 10);
$pdf->StartTransform();
$pdf->Rotate(330);
$pdf->Text(20, 20, 'TARJETA DE AFILIACIÓN');
$pdf->StopTransform();

$pdf->Output("Tarjeta_".$vehiculo->placa, 'I');
