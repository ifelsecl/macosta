<?php
$raiz = '../../';
require $raiz.'seguridad.php';
$tipo = $_FILES['firma']['type'];
$firma = $_FILES['firma']['tmp_name'];
$peso = $_FILES['firma']['size'];
$imagenes_permitidas = array('image/png', 'image/gif', 'image/jpeg', 'image/jpg');
if ($_FILES['firma']['error'] == 4 or !in_array($tipo, $imagenes_permitidas)) {
  echo '<title>Logistica | Cartas</title>
<h1>Debes seleccionar una imagen con la firma</h1>
<p>Solo se aceptan imagenes en formato JPG, PNG o GIF</p>';
  exit;
}

$clientes = Cliente::credit();
if (empty($clientes)) {
  echo '<h1>No se encontraron clientes CREDITO</h1>';
  exit;
}
require_once $raiz.'php/tcpdf/PDF.php';
require_once $raiz.'class/Configuracion.php';
$f = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : date('Y-m-d');

$fecha = strtoupper(strftime('%d de %B de %Y', strtotime($f)));
$cartapdf = new CartaPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', FALSE);
$cartapdf->setInfo();
$cartapdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$cartapdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$cartapdf->setPrintFooter(TRUE);
$cartapdf->SetMargins(30, 40);
$cartapdf->SetFooterMargin(27);
$cartapdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM+5);

$conf = new Configuracion;
global $configuracion;
$configuracion = $conf->ObtenerConfiguracion();
unset($conf);

while ($cliente = mysql_fetch_object($clientes, 'Cliente')) {
  $cartapdf->AddPage();
  $cartapdf->setFont('','B', 9);
  $cartapdf->MultiCell(0, 5, 'FECHA:', 0, 'L');
  $cartapdf->MultiCell(0, 5, $fecha, 0, 'L');
  $cartapdf->MultiCell(0, 5, '', 0);

  $cartapdf->MultiCell(0, 5, 'SEÑORES:', 0, 'L');
  $cartapdf->MultiCell(0, 5, strtoupper($cliente->nombre_completo()), 0, 'L');
  $cartapdf->MultiCell(0, 5, '', 0);

  $cartapdf->MultiCell(0, 5, 'CIUDAD:', 0, 'L');
  $cartapdf->MultiCell(0, 5, strtoupper($cliente->ciudad), 0, 'L');
  $cartapdf->MultiCell(0, 5, '', 0);

  $cartapdf->setFont('','');
  $cartapdf->MultiCell(0, 20, $_REQUEST['texto'], 0, 'J');

  $cliente->lista_precios();
  if (! empty($cliente->lista_precios)) {
    $cartapdf->SetFillColor(255);
    $cartapdf->SetTextColor(0);
    $cartapdf->SetDrawColor(0);
    $cartapdf->SetFont('', 'B',7);
    $header = array('ORIGEN', 'DESTINO', 'FORMA COBRO', 'VALOR', 'VLR KILO', 'SEGURO');
    $w = array(35, 35, 25, 20, 20, 15); //total debe ser igual a 200
    for ($i = 0; $i < count($header); ++$i) {
      $cartapdf->Cell($w[$i], 6, $header[$i], 1, 0, 'C', 1);
    }
    $cartapdf->Ln();
    $cartapdf->SetTextColor(0);
    $cartapdf->SetFont('', '',8);
    $h = 5;
    $fill = false;
    foreach ($cliente->lista_precios as $precio) {
      $cartapdf->Cell($w[0], $h, substr($precio->ciudadorigen, 0, 20), 'LRB', 0, 'L', $fill);
      $cartapdf->Cell($w[1], $h, substr($precio->ciudaddestino, 0, 20), 'RB', 0, 'L', $fill);
      $cartapdf->Cell($w[2], $h, $precio->embalaje, 'RB', 0, 'L', $fill);
      $cartapdf->Cell($w[3], $h, number_format($precio->precio), 'RB', 0, 'R', $fill);

      if ($precio->tipo_cobro == 'Caja') {
        $cartapdf->Cell($w[4], $h, $precio->precio_kilo, 'RB', 0, 'R', $fill);
      } else {
        $cartapdf->Cell($w[4], $h, '-', 'RB', 0, 'L', $fill);
      }
      $cartapdf->Cell($w[5], $h, $precio->seguro.'%', 'RB', 0, 'R', $fill);
      $cartapdf->Ln();
      // $fill = !$fill;
    }
    $cartapdf->Cell(array_sum($w), 0, '', 'T',1);
    $cartapdf->Ln();
  }

  $info = getimagesize($firma);
  $h = $info[1] > 24 ? 25 : $info[1];
  $cartapdf->Image($firma, $cartapdf->GetX(), $cartapdf->GetY(), 0, $h, '', '','');
  $cartapdf->Ln($h-5);
  $cartapdf->MultiCell(150, 5, '', 'B', 'L');
  $cartapdf->MultiCell(150, 5, $_REQUEST['firmante'], 0, 'L');
  $cartapdf->MultiCell(150, 5, $_REQUEST['cargo_firmante'], 0, 'L');
}
$cartapdf->Output('Cartas', 'I');
