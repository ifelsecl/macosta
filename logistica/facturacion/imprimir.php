<?php
require "../seguridad.php";

if (! isset($_SESSION['permisos'][FACTURACION_IMPRIMIR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
require Logistica::$root."php/tcpdf/PDF.php";

/**
 * Se utilizan en el metodo Header de la clase FacturaPDF
 * contiene la información del encabezado de la factura.
 */
global $factura;
global $configuracion;

$configuracion = new Configuracion;
$id_facturas = array();

if (isset($_REQUEST['multiple'])) {
  if (! isset($_REQUEST['inicio']) or ! isset($_REQUEST['fin'])) {
    exit('<h1>Algo ha salido mal... comunicate con soporte (V).</h1>');//error imprimiendo varias
  }
  for ($i = $_REQUEST['inicio']; $i <= $_REQUEST['fin']; $i++) {
    $id_facturas[] = $i;
  }
} else {
  if (! isset($_REQUEST['idfactura']) or ! nonce_is_valid($_REQUEST[NONCE_KEY], $_REQUEST['idfactura'])) {
    exit('<h1>Algo ha salido mal... comunicate con soporte.</h1>');
  }
  $id_facturas[] = $_REQUEST['idfactura'];
}
if (empty($id_facturas)) {
  exit('<h2>No se ha podido seleccionar ninguna factura, comunicate con soporte.</h2>');
}
$pdf = new FacturaPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Logística');
$pdf->SetAuthor('Edgar Ortega Ramírez');
if (count($id_facturas) == 1)
  $pdf->SetTitle("Factura ".$id_facturas[0]);
else
  $pdf->SetTitle("Facturas");

$pdf->SetSubject("Facturación - Transportes Mario Acosta");
$pdf->SetKeywords('Factura, Transportes Mario Acosta');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(5, 48, 5);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$margin_footer = 3;
$pdf->SetFooterMargin($margin_footer);
$pdf->SetAutoPageBreak(TRUE, $margin_footer);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->setFontSubsetting(FALSE);
$pdf->setPrintFooter(FALSE);

$facturas = Factura::find_by_ids($id_facturas);
foreach ($facturas as $factura) {
  $factura->cliente();
  $factura->guias();
  $factura->resolucion();
  for($i = 1; $i <= 3; $i++) {
    $pdf->AddPage();
    $data         = array();
    $valor_flete  = 0;
    $valor_seguro = 0;
    if (empty($factura->guias)) {
      $data[] = array('No se encontraron guias...', '', '', '', '', 0);
    } else {
      foreach ($factura->guias as $guia) {
        $data[] = array(
          $guia->contacto->nombre_completo,
          $guia->unidades, $guia->id,
          substr($guia->documentocliente, 0, 12),
          $guia->contacto->ciudad->nombre,
          number_format($guia->valorseguro + $guia->total)
        );
        $valor_flete  += $guia->total;
        $valor_seguro   += $guia->valorseguro;
      }
    }

    $pdf->FacturaBody($data);

    $pdf->SetFont('helvetica', '', 10);

    if ($factura->valorflete == 0 or $valor_flete == 0) {
      $descuento = 0;
    } else {
      $descuento = round($factura->descuento*100/$valor_flete, 1);
    }

    //Bajar el apuntador si quedó muy arriba
    if ($pdf->GetY()<239) {
      $pdf->SetY(230);
    } else {
      $pdf->AddPage();
      $pdf->Ln(10);
    }
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(130, 4, 'CONSIGNAR CTA CTE No. 815135009 BANCO AV VILLAS', 0, '', false, 0);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(40, 4, 'Total Flete', 1, 'R', false, 0);
    $pdf->MultiCell(30, 4, number_format($valor_flete), 1, 'R', false, 1);

    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(130, 4, 'CONSIGNAR CTA CTE No. 025769998565 BANCO DAVIVIENDA', 0, '', false, 0);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(40, 4, "(+) Otros cargos", 1, 'R', false, 0);
    $pdf->MultiCell(30, 4, number_format($valor_seguro), 1, 'R', false, 1);

    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(130, 4, 'CONSIGNAR CTA CTE No. 681-12808-8 BANCO POPULAR', 0, '', false, 0);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(40, 4, "(-) Descuento ($descuento%)", 1, 'R', false, 0);
    $pdf->MultiCell(30, 4, number_format($factura->descuento), 1, 'R', false, 1);

    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(130, 4, 'ANEXAR COPIA COMPROBANTE DE PAGO', 0, '', false, 0);

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(40, 4, 'TOTAL A PAGAR', 1, 'R', true, 0);
    $pdf->MultiCell(30, 4, number_format($valor_flete+$valor_seguro-$factura->descuento), 1, 'R', true, 1);

    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell(130, 4, 'PAGAR CON CHEQUE A PRIMER BENEFICIARIO Y CRUZADO', 0, '', false, 1);
    $pdf->MultiCell(130, 4, 'COD. ACTIVIDAD PRINCIPAL (RTE FTE CREE): '.$configuracion->facturacion_codigo_actividad_principal, 0, '', false, 1);

    $pdf->Ln(3);
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->Image(K_PATH_IMAGES.'firma.jpg', $pdf->GetX() + 18, $pdf->GetY() - 2, 50, 20);
    $pdf->MultiCell(80, 6, strtoupper($configuracion->nombre_empresa), '', 'C', false, 0);

    $pdf->Ln(11);
    $pdf->SetTextColor(0);
    $pdf->MultiCell(80, 6, 'FIRMA AUTORIZADA', 'T', 'C', false, 0);
    $pdf->SetTextColor(255, 0, 0);

    $pdf->SetFont('helvetica', '', 10);
    // 3 COPIAS
    if ($i == 1) {
      $x1 = $pdf->GetX() + 12.5;
      $y1 = $pdf->GetY() + 1;
      $x2 = $x1 + 1.5;
      $y2 = $y1 + 1.5;
      $pdf->Line($x1, $y1, $x2, $y2, array());
      $pdf->Line($x1-0.5, $y1 + 1, $x2 - 0.4, $y2, array());
      $texto = 'ORIGINAL';
    } elseif ($i == 2) {
      $texto = 'ORIGINAL CLIENTE';
    } elseif ($i == 3) {
      $texto = 'COPIA';
    }

    $pdf->MultiCell(40, 6, $texto, 0, 'C', false, 0);
    $pdf->SetTextColor(0);
    $pdf->MultiCell(80, 6, 'Recibí', 'T', 'C', false, 1);

    $pdf->Ln(2);
    $pdf->MultiCell(200, 5, 'Esta Factura de Venta se asimila en todos sus efectos a la Letra de Cambio, Articulo 774 del Código de Comercio.',0,'C', false, 1);
    $pdf->MultiCell(200, 5, 'Páguese con cheque cruzado a nombre de <b>'.strtoupper($configuracion->nombre_empresa).'</b> ©', 0, 'C', false, 1, $pdf->GetX(), $pdf->GetY()-1, true, 0, true);

    $pdf->SetFont('helvetica', '', 6);
    $pdf->MultiCell(200, 3, 'Factura impresa en computador por '.strtoupper($configuracion->nombre_empresa).' '.$configuracion->nit_empresa,0,'C', false, 1);
  }
}
if (count($id_facturas) == 1) $n = 'Factura_'.$id_facturas[0];
else $n = 'Facturas';

$pdf->Output($n, 'I');
