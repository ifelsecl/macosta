<?php
if (! defined('GUIAS_IMPRIMIR')) exit;
$pdf->Image(K_PATH_IMAGES.'logo_transporte.png',$pdf->GetX()+40, $pdf->GetY()-2,0,14);
$pdf->Image(K_PATH_IMAGES.'logo.gif',$pdf->GetX()+1, $pdf->GetY()-1,0,10);
$pdf->Image(K_PATH_IMAGES.'logo_vigilado.png',$pdf->GetX()+15, $pdf->GetY()-1,0,8);
$pdf->SetFont('helvetica', 'B', 6);
$pdf->MultiCell(130, 3, '', 0, 'R', false, 0);
if ($guia->idfactura) {
  $title = 'FACTURA DE VENTA';
  $subtitle = 'BQA-'.$guia->idfactura;
} else {
  $title = 'GUIA';
  $subtitle = $guia->id;
}
$pdf->MultiCell(70, 3, $title, 0, 'R', false, 1);
$pdf->MultiCell(130, 4, '', 0, 'R', false, 0);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->MultiCell(70, 4, $subtitle, 0, 'R', false, 1);

$pdf->Ln();

$pdf->SetFont('helvetica', '', 7.5);

$h = 3.5;
$wi = 30;

$space = 4;
$col1 = 74;
$col2 = 74;
$col3 = 40;

if ($guia->idfactura) {
  $guia->resolucion();
  $facturacion = 'Régimen Común | ';
  $facturacion .= 'Res. DIAN '.$guia->resolucion->numero.' Del '.$guia->resolucion->fecha. " Rango: BQA-".$guia->resolucion->inicio.' - BQA-'.$guia->resolucion->fin.' | ';
  $facturacion .= 'Tarifa Aplicar '.$configuracion->facturacion_tarifa_aplicar.' | ';
  $facturacion .= 'Act. Serv. Cod. 302 | ';
  $facturacion .= 'Act. Principal '.$configuracion->facturacion_codigo_actividad_principal;
} else {
  $facturacion = '';
}

$pdf->MultiCell(200, 4, $facturacion, '', 'C', false, 1);

$pdf->SetFont('helvetica', 'B', 6);
$pdf->StartTransform();
$pdf->Rotate(90, $pdf->GetX()+9, $pdf->GetY()+9);
$pdf->MultiCell(18, 4, 'REMITENTE', 'TR', 'C', false, 0, '', '', true, 0, false, true, 0, 'B');
$pdf->StopTransform();

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col1, $h, substr($guia->cliente->nombre_completo, 0, 39), 'LT', '', false, 0, $pdf->GetX()-18);

$pdf->SetFont('helvetica', 'B', 6);
$pdf->StartTransform();
$pdf->Rotate(90, $pdf->GetX()+10, $pdf->GetY()+10);
$pdf->MultiCell(20, 4, 'DESTINATARIO', 'TBR', 'C', false, 0, '', '', true, 0, false, true, 0, 'B');
$pdf->StopTransform();

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, substr($guia->contacto->nombre_completo, 0, 39), 'LRT', '', false, 0, $pdf->GetX()-20);

$pdf->SetFont('helvetica', 'B', 6);
$pdf->StartTransform();
$pdf->Rotate(90, $pdf->GetX()+9, $pdf->GetY()+9);
$pdf->MultiCell(18, 4, 'MERCANCIA', 'TR', 'C', false, 0, '', '', true, 0, false, true, 0, 'B');
$pdf->StopTransform();

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col3 / 2, $h, 'Unidades', 'TLR', 'C', false, 0, $pdf->GetX()-18);
$pdf->MultiCell($col3 / 2, $h, 'Peso (Kgs)', 'TR', 'C', false, 1);

$pdf->SetFont('helvetica', '', 9);
$pdf->MultiCell($space, $h, '', 'L', '', false, 0);
$pdf->MultiCell($col1, $h, $guia->cliente->tipo_identificacion_corto().': '.$guia->cliente->numero_identificacion_completo, 'L', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, $guia->contacto->tipo_identificacion_corto().': '.$guia->contacto->numero_identificacion_completo, 'LR', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col3 / 2, $h, $guia->unidades, 'LBR', 'C', false, 0);
$pdf->MultiCell($col3 / 2, $h, $guia->peso, 'LBR', 'C', false, 1);

$pdf->MultiCell($space, $h, '', 'L', '', false, 0);
$pdf->MultiCell($col1, $h, substr($guia->cliente->direccion, 0, 39), 'L', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, substr($guia->contacto->direccion, 0, 39), 'LR', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col3 / 2, $h, '', 'L', '', false, 0);
$pdf->MultiCell($col3 / 2, $h, '', 'R', '', false, 1);

$pdf->MultiCell($space, $h, '', 'L', '', false, 0);
$pdf->MultiCell($col1, $h, 'TELS: '.$guia->cliente->telefono_completo(), 'L', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, 'TELS: '.$guia->contacto->telefono_completo(), 'LR', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col3, $h, 'Valor Asegurado', 'LR', 'C', false, 1);

$pdf->MultiCell($space, $h, '', 'L', '', false, 0);
$pdf->MultiCell($col1, $h, $guia->cliente->ciudad_nombre.' ('.substr($guia->cliente->departamento_nombre, 0, 10).')', 'L', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, $guia->contacto->ciudad_nombre.' ('.substr($guia->contacto->departamento_nombre, 0, 10).')', 'LR', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col3, $h, number_format($guia->valordeclarado,0,',','.'), 'LR', 'C', false, 1);

$pdf->SetFillColor(255);

$pdf->SetFont('helvetica', 'B', 9.5);
$observaciones = 'No. Documento: '.$guia->documentocliente.' | Guía anterior: '.$guia->numero."\r\n".'Observaciones: '.$guia->observacion;
$pdf->MultiCell(85, 14, $observaciones, 1, '', false, 0, '', '', true, 0, false, true, 14, 'T', true);
$pdf->SetFont('helvetica', '', 8);
$html='<table>
      <tr>
        <td><b>Bodega:</b></td>
        <td align="center">'.$guia->fecha_recibido_mercancia.'</td>
      </tr>
      <tr>
        <td><b>Despacho:</b></td>
        <td align="center">'.$guia->fechadespacho.'</td>
      </tr>
      <tr>
        <td><b>Entrega:</b></td>
        <td align="center">'.$guia->fechaentrega.'</td>
      </tr>
    </table>';
$pdf->writeHTMLCell(35, 14, $pdf->GetX(), $pdf->GetY(), $html, 1, 0, false);
$html='<table cellspacing="0" cellpadding="0">
      <tr>
        <td><b>Total Flete:</b></td>
        <td align="right">'.number_format($guia->total, 0, ',', '.').'</td>
      </tr>
      <tr>
        <td><b>Otros Cargos:</b></td>
        <td align="right">'.number_format($guia->valorseguro, 0, ',', '.').'</td>
      </tr>
      </table>';
$pdf->writeHTMLCell(45, 14, $pdf->GetX(), $pdf->GetY(), $html, 1, 0, false);
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
      'text' => true,
      'font' => 'helvetica',
      'fontsize' => 9,
      'stretchtext' => 0
    );
$pdf->write1DBarcode($guia->id, $barcode, '', '', 35, 14, '', $style, 'N');

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell(65, 13, 'Firma y Sello Remitente', 'LRB', 'L', false, 0);
$pdf->SetFont('helvetica', '', 8);
$html='<table>
      <tr>
        <td style="font-family: Helvetica;font-size:30px;" valign="top"><b>Firma y Sello Destinatario</b></td>
      </tr>
      <tr>
        <td style="font-size:16px;" valign="bottom">'.$configuracion->guias_pie_pagina.'</td>
      </tr>
    </table>';
$pdf->writeHTMLCell(75, 13, $pdf->GetX(), $pdf->GetY(), $html, 1, 0, true);
$pdf->SetFont('helvetica', '', 10);
$st = 'font-size:34px';
if ($guia->formapago == 'FLETE AL COBRO') {
  $st = 'font-size:38px;font-weight:bold;';
}
$html='<table>
      <tr>
        <td style="width:70px;font-size:28px;"><b>Forma Pago:</b></td>
        <td style="width:150px;'.$st.'">'.$guia->formapago.'</td>
      </tr>
      <tr>
        <td style="width:70px;font-size:28px;"><b>Total a pagar:</b></td>
        <td style="font-size:55px;font-weight:bold;">'.number_format($guia->valorseguro+$guia->total, 0, ',', '.').'</td>
      </tr>
    </table>';
$pdf->writeHTMLCell(60, 13, $pdf->GetX(), $pdf->GetY(), $html, 1, 0, false);

// contrato parte baja de la guia- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
$pdf->Ln();
$pdf->SetFont('helvetica', '', 8);
$html='<table>
      <tr>
        <td style="font-size:20px; text-align:center;">'.$configuracion->contrato.'</td>
      </tr>
    </table>';
$pdf->writeHTMLcell(200, 4, $pdf->GetX(), $pdf->GetY(), $html, 0, 0, true);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -



if ($i != 4) $pdf->Ln(28); //ESPACIADO ENTRE GUIAS
