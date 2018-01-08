<?php
require "../../../seguridad.php";
if (! isset($_SESSION['permisos'][LISTA_PRECIOS_EXPORTAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$cliente = new Cliente;
if (! $cliente->find($_REQUEST['id'])) exit('No existe el cliente.');

$nombrecorto = str_replace(" ", "_", substr($cliente->nombre_completo, 0,15));
$nombre = 'Lista_precios_'.$nombrecorto.'_'.strftime('%B_%Y');
if ((! isset($_REQUEST['token']) or (isset($_REQUEST['token']) and $_REQUEST['token']!=$_SESSION['token'])) and isset($_REQUEST['f'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
if (isset($_REQUEST['f'])) {
  if ($_REQUEST['f'] == 'PDF') {
    if ($_FILES['firma']['error'] != 4) {
      $tipo = $_FILES['firma']['type'];
      $firma = $_FILES['firma']['tmp_name'];
      $peso = $_FILES['firma']['size'];
      $imagenes_permitidas = array('image/png', 'image/gif', 'image/jpeg', 'image/jpg');
      if (! in_array($tipo, $imagenes_permitidas)) {
        echo '<title>Logistica</title>
  <h1>Debes seleccionar una imagen con la firma</h1>
  <p>Solo se aceptan imagenes en formato JPG, PNG o GIF</p>';
        exit;
      }
    }

    require_once Logistica::$root.'php/tcpdf/PDF.php';
    require_once Logistica::$root.'class/Configuracion.php';
    $f = date('Y-m-d');

    $fecha = strtoupper(strftime('%B %d de %Y',strtotime($f)));
    $cartapdf = new CartaPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', FALSE);
    $cartapdf->setInfo();
    $cartapdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $cartapdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $cartapdf->setPrintFooter(TRUE);
    $cartapdf->SetMargins(30, 40);
    $cartapdf->SetFooterMargin(25);
    $cartapdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $conf=new Configuracion;
    global $configuracion;
    $configuracion=$conf->ObtenerConfiguracion();
    unset($conf);

    $cartapdf->AddPage();
    $cartapdf->MultiCell(0, 5, strtoupper($cliente->ciudad_nombre).', '.$fecha, 0, 'L');
    $cartapdf->Ln();
    $cartapdf->MultiCell(0, 5, 'SEÑORES:', 0, 'L');
    $cartapdf->setFont('','B');
    $cartapdf->MultiCell(0, 5, strtoupper($cliente->nombre_completo), 0, 'L');
    $cartapdf->MultiCell(0, 5, '', 0);

    $cartapdf->setFont('','');
    $cartapdf->MultiCell(0, 20, $_REQUEST['concepto'], 0, 'J');

    $cartapdf->SetFillColor(255); //Blanco
    $cartapdf->SetTextColor(0); //Negro
    $cartapdf->SetDrawColor(0, 0, 0); //Negro
    $cartapdf->SetFont('', 'B',7);
    $header = array('ORIGEN', 'DESTINO', 'FORMA COBRO', 'VALOR', 'VLR KILO', 'SEGURO');
    $w = array(35, 35, 25, 20, 20, 15); //total debe ser igual a 200
    $num_headers = count($header);
    for($i = 0; $i < $num_headers; ++$i) {
      $cartapdf->Cell($w[$i], 6, $header[$i], 1, 0, 'C', 1);
    }
    /* Restaurar la fuente */
    $cartapdf->SetTextColor(0);
    $cartapdf->SetFont('', '',8);
    $cartapdf->Ln();

    $result = $cliente->ExportarListaPrecios($_REQUEST['id'],"XLS");
    $h=5;
    $fill=FALSE;
    while ($l=mysql_fetch_object($result)) {
      $cartapdf->Cell($w[0], $h, substr($l->ciudadorigen,0,20), 'LR', 0, 'L', $fill);
      $cartapdf->Cell($w[1], $h, substr($l->ciudaddestino,0,20), 'LR', 0, 'L', $fill);
      $cartapdf->Cell($w[2], $h, $l->embalaje, 'LR', 0, 'L', $fill);
      $cartapdf->Cell($w[3], $h, number_format($l->precio), 'LR', 0, 'R', $fill);

      if ($l->tipo_cobro=='Caja') {
        $cartapdf->Cell($w[4], $h, $l->precio_kilo, 'LR', 0, 'R', $fill);
      }else{
        $cartapdf->Cell($w[4], $h, '-', 'LR', 0, 'L', $fill);
      }
      $cartapdf->Cell($w[5], $h, $l->seguro.'%', 'LR', 0, 'R', $fill);
      $cartapdf->Ln();
      $fill=!$fill;
    }
    $cartapdf->Cell(array_sum($w), 0, '', 'T',1);

    $cartapdf->Ln();
    if (isset($firma)) {
      $cartapdf->Image($firma, $cartapdf->GetX(), $cartapdf->GetY(), 0, 20, '', '','');
      $cartapdf->Ln(20);
    }

    $cartapdf->MultiCell(150, 5, $_REQUEST['firmante'], 0, 'L');
    $cartapdf->MultiCell(150, 5, $_REQUEST['cargo_firmante'], 0, 'L');

    $cartapdf->Output('Cartas', 'I');
    exit;
  }
  if ($_REQUEST['f'] == 'XLS') {
    require_once Logistica::$root."class/Configuracion.php";
    $configuracion = new Configuracion;
    Logistica::unregister_autoloaders();
    require_once Logistica::$root.'php/excel/PHPExcel.php';
    $objPHPExcel = new PHPExcel;
    $objPHPExcel->getProperties()
      ->setCreator('Edgar Ortega Ramírez')
      ->setLastModifiedBy('Logística')
      ->setTitle('Lista de Precios '.$nombrecorto)
      ->setSubject('Lista de Precios '.$nombrecorto)
      ->setDescription('Lista de Precios '.$nombrecorto)
      ->setKeywords('Lista de Precios '.$nombrecorto)
      ->setCategory('Lista de Precios');
    $hoja=$objPHPExcel->setActiveSheetIndex(0);
    $hoja->getHeaderFooter()->setOddHeader("&L&G&C&H".$configuracion->nombre_empresa);
    $objDrawing = new PHPExcel_Worksheet_HeaderFooterDrawing();
    $objDrawing->setName('TMA Logo');
    $objDrawing->setPath(Logistica::$root.'img/logo.jpg');
    $objDrawing->setHeight(36);
    $hoja->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);
    $hoja->setTitle('Lista de Precios');
    $hoja->setCellValue('A1',$cliente->nombre_completo);
    $i = 3;
    if (! empty($_REQUEST['concepto'])) {
      $objRichText = new PHPExcel_RichText();
      $objRichText->createText($_REQUEST['concepto']);
      $hoja->getCell('A'.$i)->setValue($objRichText);

      $hoja->getStyle('A'.$i)->getAlignment()->setWrapText(true);
      $hoja->getRowDimension($i)->setRowHeight(80);
      $hoja->mergeCells('A'.$i.':F'.$i);
      $i = 5;
    }

    $hoja->setCellValue('A'.$i, 'ORIGEN');
    $hoja->setCellValue('B'.$i, 'DESTINO');
    $hoja->setCellValue('C'.$i, 'SEGURO %');
    $hoja->setCellValue('D'.$i, 'FORMA DE COBRO');
    $hoja->setCellValue('E'.$i, 'PRECIO');
    $hoja->setCellValue('F'.$i, 'PRECIO KILO');

    $result = $cliente->ExportarListaPrecios($_REQUEST['id'],"XLS");
    $i++;
    while ($l = mysql_fetch_object($result)) {
      $hoja->setCellValueByColumnAndRow(0,$i,$l->ciudadorigen);
      $hoja->setCellValueByColumnAndRow(1,$i,$l->ciudaddestino);
      $hoja->setCellValueByColumnAndRow(2,$i,$l->seguro);
      $hoja->setCellValueByColumnAndRow(3,$i,$l->embalaje, TRUE);
      $hoja->setCellValueByColumnAndRow(4,$i,$l->precio);
      if ($l->tipo_cobro == 'Caja') {
        $hoja->setCellValueByColumnAndRow(5,$i,$l->precio_kilo);
      }
      $i++;
    }
    $hoja->getColumnDimension('A')->setAutoSize(TRUE);
    $hoja->getColumnDimension('B')->setAutoSize(TRUE);
    $hoja->getColumnDimension('C')->setAutoSize(TRUE);
    $hoja->getColumnDimension('D')->setAutoSize(TRUE);

    $hoja->setCellValueByColumnAndRow(3, $i+2, $_REQUEST['firmante']);
    $hoja->setCellValueByColumnAndRow(3, $i+3, $_REQUEST['cargo_firmante']);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$nombre.'.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
  }
  if ($_GET['f'] == 'CSV') {
    $nombre .= '.csv';
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-type: text/comma-separated-values; charset=UTF-8');
    header("Content-Disposition: attachment; filename=$nombre");
    $result = $cliente->ExportarListaPrecios($_GET['id'],"CSV");
    $sep = ";";
    while ($row = mysql_fetch_assoc($result)) {
      $linea = '';
      foreach ($row as $key => $value) {
        if ($key != 'idcliente')
          $linea .= $value.$sep;
      }
      echo substr($linea, 0, -1)."\r\n";
    }
    exit;
  }
}
?>
<button id="lista_precios_regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h3><?= $cliente->nombre_completo ?> | Exportar Lista de Precios</h3>
<hr class="hr-small">
<form method="post" enctype="multipart/form-data" target="_blank" action="cliente_proveedor/clientes/listaprecios/exportar">
  <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
  <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>" />
  <table>
    <tbody>
      <tr>
        <td colspan="2">
          <b>Concepto (opcional):</b><br>
          <textarea style="width: 100%;height: 200px" name="concepto" id="concepto"></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <b>Firmante (opcional):</b><br>
          <input type="text" name="firmante" id="firmante" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <b>Cargo firmante (opcional):</b><br>
          <input type="text" name="cargo_firmante" id="cargo_firmante" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <b>Firma:</b> (PNG, JPG y GIF)<br>
          <input type="file" name="firma" id="firma" size="60" />
        </td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <label class="checkbox inline" title="Exportar en PDF"><input type="radio" name="f" value="PDF" checked="checked" /><img src="img/pdf.png" /></label>
          <label class="checkbox inline" title="Exportar en Excel 2007"><input type="radio" name="f" value="XLS" /><img src="img/xls.png" /></label>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <button class="btn btn-info"><i class="icon-cloud-download"></i> Exportar</button>
        </td>
      </tr>
    </tbody>
  </table>
</form>
<hr>
<div class="row-fuild">
  <div class="span10">
    <table>
      <tbody>
        <tr>
          <td width="32"><img src="img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
          <td>
            <b>Formato CSV</b><br /> Adicionalmente puedes exportar la lista de precios en formato CSV para realizar cambios que posteriormente puedes importar.
          </td>
          <td>
            <a class="btn btn-info" href="cliente_proveedor/clientes/listaprecios/exportar?f=CSV&id=<?= $_GET['id'] ?>&token=<?= $_SESSION['token'] ?>"><i class="icon-cloud-download"></i> Exportar</a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
