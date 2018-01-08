<?php
$raiz = '../../';
require_once $raiz.'php/tcpdf/config/lang/spa.php';
require_once $raiz.'php/tcpdf/tcpdf.php';
date_default_timezone_set("America/Bogota");

class MYPDF extends TCPDF {
  static $headers = array('Guia', 'Destinatario', 'No Documento', 'Direccion', 'Ciudad', 'Telefono', 'Valor', 'Unid', 'peso');
  static $widths = array(18, 60, 40, 50, 30, 20, 17, 10, 10);

  function cabecera(){
    $html='<h2 style="text-align:center;">Relacion de Despachos</h2>';
    $this->writeHTML($html, true, false, true, false, '');
    $this->Ln(4);
    $headers = array('Guia', 'Destino', 'Direccion', 'Ciudad', 'Telefono', 'Valor', 'Unid', 'Peso');

    $this->SetFont('', 'B');
    for($i = 0; $i < count(self::$headers); ++$i) {
      $this->Cell(self::$widths[$i], 7, self::$headers[$i], 1, 0, 'C');
    }
    $this->Ln();
  }

  function ColoredTable($data) {
    $numero_identificacion = $_SESSION['numero_identificacion'];
    $nombre = $_SESSION['nombre'];
    $direccion = $_SESSION['direccion'];
    $telefono = $_SESSION['telefono'];
    $fecha = date('Y-m-d');
    $this->SetFillColor(255, 255, 255);
    $this->SetTextColor(0, 0, 0);
    $this->SetDrawColor(0, 0, 0);
    $this->SetLineWidth(0.3);
    $this->SetFont('', 'B');
    for($i = 0; $i < count(self::$headers); ++$i) {
      $this->Cell(self::$widths[$i], 7, self::$headers[$i], 1, 0, 'C', 1);
    }
    $this->Ln();
    $this->SetFillColor(0, 0, 0);
    $this->SetTextColor(0);
    $this->SetFont('');
    $i=0;
    $total_unidades = 0;
    $total_peso = 0;
    $total_valor_declarado = 0;
    foreach($data as $guia) {
      $contacto = trim($guia['nombre_contacto'].' '.$guia['primer_apellido_contacto'].' '.$guia['segundo_apellido_contacto']);
      $this->Cell(self::$widths[0], 6, $guia['id'], 'LB', 0, 'L');
      $this->Cell(self::$widths[1], 6, $contacto, 'LB', 0, 'L', $fill = false, $link = '', $stretch = 1);
      $this->Cell(self::$widths[2], 6, $guia['documentocliente'], 'LB', 0, 'L', $fill = false, $link = '', $stretch = 1);
      $this->Cell(self::$widths[3], 6, $guia['direccion_contacto'], 'LB', 0, 'L', $fill = false, $link = '', $stretch = 1);
      $this->Cell(self::$widths[4], 6, substr($guia['nombre_ciudad_contacto'], 0, 15), 'LB', 0, 'L');
      $this->Cell(self::$widths[5], 6, $guia['telefono_contacto'], 'LB', 0, 'L', $fill = false, $link = '', $stretch = 1);
      $this->Cell(self::$widths[6], 6, number_format($guia['valordeclarado']), 'LB', 0, 'R');
      $this->Cell(self::$widths[7], 6, number_format($guia['unidades']), 'LB', 0, 'R');
      $this->Cell(self::$widths[8], 6, number_format($guia['peso']), 'LBR', 0, 'R');
      $this->Ln();

      $total_unidades += $guia['unidades'];
      $total_peso += $guia['peso'];
      $total_valor_declarado += $guia['valordeclarado'];

      /* IMPRIME 20 POR PAGINA */
      if($i==20){
        $this->Cell(array_sum(self::$widths), 0, '', 'T', 1);
        $tbl=<<<EOD
<table border="0" cellpadding="2" cellspacing="2" align="center">
  <tr nobr="true">
    <td><br /><br />________________________<br />Despachador (Firma y Sello):<br />C.C</td>
    <td><br /><br />________________________<br />Recibe (Firma y Sello):<br />C.C</td>
  </tr>
</table>
<br />
ANEXAR FACTURAS CORRESPONDINTES. SI ES MERCANCIA EXTRANJERA ANEXAR MANIFIESTO

EOD;
        $this->writeHTML($tbl, true, false, false, false, '');
        $tba1=<<<EOF
ID Remitente: $numero_identificacion <br />
Nombre: $nombre <br />
Direccion: $direccion <br />
Telefono: $telefono <br />
Fecha: $fecha

EOF;
        $this->AddPage();
        $this->writeHTML($tba1, true, false, true, false, '');
        $this->cabecera();
        $i = 0;
      }else{
        $i += 1;
      }
    }
    $this->Cell(self::$widths[0], 6, '', '', 0, 'L');
    $this->Cell(self::$widths[1], 6, '', '', 0, 'L');
    $this->Cell(self::$widths[2], 6, '', '', 0, 'L');
    $this->Cell(self::$widths[3], 6, '', '', 0, 'L');
    $this->Cell(self::$widths[4], 6, '', '', 0, 'L');
    $this->Cell(self::$widths[5], 6, 'TOTAL', 'LTB', 0, 'R');
    $this->Cell(self::$widths[6], 6, number_format($total_valor_declarado), 'LTB', 0, 'R');
    $this->Cell(self::$widths[7], 6, number_format($total_unidades), 'LTB', 0, 'R');
    $this->Cell(self::$widths[8], 6, number_format($total_peso), 'LTBR', 0, 'R');
  }
}
