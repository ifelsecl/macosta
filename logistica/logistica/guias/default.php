<?php
if (! defined('GUIAS_IMPRIMIR')) exit;
$pdf->Image(K_PATH_IMAGES.'logo_transporte.png',$pdf->GetX()+40, $pdf->GetY()-4,0,14);
$pdf->Image(K_PATH_IMAGES.'logo.gif',$pdf->GetX()+1, $pdf->GetY()-2,0,10);
$pdf->Image(K_PATH_IMAGES.'logo_vigilado.png',$pdf->GetX()+14, $pdf->GetY()-1,0,6);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->MultiCell(130, 5, '', 0, 'R', false, 0);
$pdf->MultiCell(70, 5, 'Guía '.$guia->id, 0, 'R', false, 1);
$pdf->SetFont('helvetica', 'B', 8);
if ($guia->idplanilla) {
  $pdf->MultiCell(150, 5, '', 0, 'R', false, 0);
  $pdf->MultiCell(50, 5, 'Manifiesto: '.$guia->idplanilla, 0, 'R', false, 1);
} else {
  $pdf->MultiCell(0, 5, '', 0, '', false, 1);
}

$h = 3.5;
$wi = 30;

$space = 4;
$col1 = 96;
$col2 = 96;

$pdf->SetFont('helvetica', 'B', 6);
$pdf->StartTransform();
$pdf->Rotate(90, $pdf->GetX()+9, $pdf->GetY()+9);
$pdf->MultiCell(18, 4, 'REMITENTE', 'TR', 'C', false, 0, '', '', true, 0, false, true, 0, 'B');
$pdf->StopTransform();

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col1, $h, substr($guia->cliente->nombre_completo, 0, 50), 'LT', '', false, 0, $pdf->GetX()-18);

$pdf->SetFont('helvetica', 'B', 6);
$pdf->StartTransform();
$pdf->Rotate(90, $pdf->GetX()+9, $pdf->GetY()+9);
$pdf->MultiCell(18, 4, 'DESTINATARIO', 'TR', 'C', false, 0, '', '', true, 0, false, true, 0, 'B');
$pdf->StopTransform();

$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, substr($guia->contacto->nombre_completo, 0, 50), 'LRT', '', false, 1, $pdf->GetX()-18);

$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell($space, $h, '', 'L', '', false, 0);
$pdf->MultiCell($col1, $h, $guia->cliente->tipo_identificacion_corto().': '.$guia->cliente->numero_identificacion_completo, 'L', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, $guia->contacto->tipo_identificacion_corto().': '.$guia->contacto->numero_identificacion_completo, 'LR', '', false, 1);

$pdf->MultiCell($space, $h, '', 'L', '', false, 0);
$pdf->MultiCell($col1, $h, $guia->cliente->direccion, 'L', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, $guia->contacto->direccion, 'LR', '', false, 1);

$pdf->MultiCell($space, $h, '', 'L', '', false, 0);
$pdf->MultiCell($col1, $h, 'TELS: '.$guia->cliente->telefono_completo(), 'L', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, 'TELS: '.$guia->contacto->telefono_completo(), 'LR', '', false, 1);

$pdf->MultiCell($space, $h, '', 'L', '', false, 0);
$pdf->MultiCell($col1, $h, $guia->cliente->ciudad_nombre.' ('.substr($guia->cliente->departamento_nombre, 0, 10).')', 'L', '', false, 0);
$pdf->MultiCell($space, $h, '', '', '', false, 0);
$pdf->MultiCell($col2, $h, $guia->contacto->ciudad_nombre.' ('.substr($guia->contacto->departamento_nombre, 0, 10).')', 'LR', '', false, 1);

$pdf->SetFillColor(100, 100, 100);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0, 0, 0);

$fill = false;

$pdf->MultiCell(15, $h, 'Codigo', 1, 'C', $fill, 0);
$pdf->MultiCell(100, $h, 'Producto', 1, 'C', $fill, 0);
$pdf->MultiCell(10, $h, 'Unid', 1, 'C', $fill, 0);
$pdf->MultiCell(12, $h, 'Kg', 1, 'C', $fill, 0);
$pdf->MultiCell(8, $h, 'Vol', 1, 'C', $fill, 0);
$pdf->MultiCell(20, $h, 'Valor Merc', 1, 'C', $fill, 0);
$pdf->MultiCell(15, $h, 'Seguro', 1, 'C', $fill, 0);
$pdf->MultiCell(20, $h, 'Flete', 1, 'C', $fill, 1);

$pdf->SetFillColor(255);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 7);

$h = 3.5;
$guia->items($guia->id);
$unidades = 0;
$items_count = count($guia->items);
if ($items_count == 0) {
  $pdf->MultiCell(200, $h, '', 1, 'R', false, 1);
  $pdf->MultiCell(200, $h, '', 1, 'L', false, 1);
  $pdf->MultiCell(200, $h, '', 1, 'R', false, 1);
  $pdf->MultiCell(200, $h, '', 1, 'R', false, 1);
} else {
  foreach ($guia->items as $item) {
    $unidades += $item->unidades;
    $pdf->MultiCell(15, $h, $item->idproducto, 1, 'R', false, 0);
    $pdf->MultiCell(100, $h, substr($item->producto, 0, 55), 1, 'L', false, 0);
    $pdf->MultiCell(10, $h, $item->unidades, 1, 'R', false, 0);
    $pdf->MultiCell(12, $h, $item->peso, 1, 'R', false, 0);
    $pdf->MultiCell(8, $h, $item->kilo_vol, 1, 'R', false, 0);
    $pdf->MultiCell(20, $h, number_format($item->valor_declarado,0,',','.'), 1, 'R', false, 0);
    $pdf->MultiCell(15, $h, number_format($item->seguro,0,',','.'), 1, 'R', false, 0);
    $pdf->MultiCell(20, $h, number_format($item->valor,0,',','.'), 1, 'R', false, 1);
  }
  for ($j=1; $j <= 4 - $items_count; $j++) {
    $pdf->MultiCell(200, $h, '', 1, 'L', false, 1);
  }
}

$pdf->SetFont('helvetica', 'B', 9);
$observaciones = 'No. Documento: '.$guia->documentocliente.' | Guía anterior: '.$guia->numero."\r\n".'Observaciones: '.$guia->observacion;
$pdf->MultiCell(85, 14, $observaciones, 1, '', false, 0, '','',true, 0, false, true, 14, 'TOP', true);
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
      <tr>
        <td><b>Total Merc:</b></td>
        <td align="right">'.number_format($guia->valordeclarado, 0, ',', '.').'</td>
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
      'text' => false,
      'font' => 'helvetica',
      'fontsize' => 7,
      'stretchtext' => 4
    );
$pdf->write1DBarcode($guia->id, $barcode, '', '', 35, 14, '', $style, 'N');
$pdf->MultiCell(65, 13, 'Firma y Sello Remitente', 'LRB', 'L', false, 0);
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
        <td style="font-size:55px;">'.number_format($guia->valorseguro+$guia->total, 0, ',', '.').'</td>
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

if ($i != 4) $pdf->Ln(15); //ESPACIADO ENTRE GUIAS
