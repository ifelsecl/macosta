<?php
$raiz = '../../';
$raiz_cliente = '../';
require $raiz_cliente.'seguridad.php';

locale();

require_once LOGISTICA_ROOT."class/guias.class.php";

if (! sesionIniciada()) {
  include LOGISTICA_ROOT.'mensajes/sesion.php';
  exit;
}

if (! isset($_POST['guias']))
  exit('<h1>Selecciona las guias que quieres imprimir</h1><p>Usa la casilla al principio de cada fila para seleccionarla</p>');

if (isset($_POST['imprimir'])) {
  $objGuia = new Guias;
  $data = array();
  $ids = array();
  foreach ($_POST['guias'] as $id_guia) {
    $result = $objGuia->ObtenerInfo($id_guia);
    if (mysql_num_rows($result) != 0) {
      $row = mysql_fetch_assoc($result);
      $data[] = $row;
      $ids[] = $id_guia;
    }
  }

  if (empty($data)) {
    exit('<h1>Algo ha salido mal, por favor actualiza la pagina e intentalo nuevamente.</h1>');
  }

  require_once 'pdf.php';
  $pdf = new MYPDF('L', PDF_UNIT, 'Letter', true, 'UTF-8', false);

  $pdf->SetCreator(PDF_CREATOR);
  $pdf->SetAuthor('Transportes Mario Acosta');
  $pdf->SetTitle('Relación de Despachos');
  $pdf->SetSubject('Relación de Despachos');
  $pdf->SetKeywords('Relación de Despachos, Logística');
  $pdf->setPrintFooter(false);

  $str=' Barranquilla-Colombia  Carrera 26 #30-09, Barrio Montes, Tel. 3700808-3794880';
  $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'TRANSPORTES MARIO ACOSTA & CIA LTDA', $str);
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(10, PDF_MARGIN_TOP, 10);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $pdf->setLanguageArray($l);
  $pdf->setPrintFooter(TRUE);
  $pdf->SetFont('helvetica', '', 8);
  $pdf->AddPage();

  $pdf->SetFont('', 'B', 15);
  $pdf->Cell(0, 0, 'Relación de Despachos', 0, 1, 'C');
  $pdf->SetFont('', '', 8);
  $pdf->Ln(2);
  $pdf->MultiCell(30, 5, 'Fecha: '.date('Y-m-d'), 0, '', false, 0);
  $pdf->MultiCell(30, 5, 'NIT/CC: '.$_SESSION['numero_identificacion'], 0, '', false, 0);
  $pdf->MultiCell(70, 5, 'Nombre: '.$_SESSION['nombre'], 0, '', false, 0);
  $pdf->MultiCell(70, 5, 'Direccion: '.$_SESSION['direccion'], 0, '', false, 0);
  $pdf->MultiCell(60, 5, 'Tels: '.$_SESSION['telefono'], 0, '', false, 1);

  $pdf->ColoredTable($data);
  $pdf->Ln(10);
  $html = <<<EOD
<table border="0" cellpadding="2" cellspacing="2" align="center">
  <tr>
    <td><br /><br />________________________<br />Despachador (Firma y Sello):<br />C.C</td>
    <td><br /><br />________________________<br />Recibe (Firma y Sello):<br />C.C</td>
  </tr>
</table>
<br />
ANEXAR FACTURAS CORRESPONDINTES. SI ES MERCANCIA EXTRANJERA ANEXAR MANIFIESTO

EOD;
  $pdf->writeHTML($html, true, false, false, false, '');

  $pdf->Output('Relacion_Guias', 'I');
  // Guia::mark_as_printed($ids);
}
