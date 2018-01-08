<?php
require "../../seguridad.php";

if (! isset($_SESSION['permisos'][GUIAS_IMPRIMIR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$guia = new Guia;
$cantidad = 4;

$debe_actualizar_fecha_recibido = false;
if (isset($_REQUEST['varias'])) {
  $cantidad = $_REQUEST['cantidad'];
  if (isset($_REQUEST['cantidad'])) {
    if ($_REQUEST['cantidad'] > 4 or $_REQUEST['cantidad'] < 1 or is_nan($_REQUEST['cantidad'])) {
      $cantidad = 2;
    }
  }
  $include_printed = isset($_POST['incluir_impresas']);
  if ($_POST['imprimir'] == 'IR') { //Rango
    $guias = Guia::all_by_rango($_POST['inicio'], $_POST['fin']);
  } elseif ($_POST['imprimir'] == 'IU') { //Usuario
    $guias = Guia::all_by_usuario_and_fecha($_POST['id_usuario'], $_POST['fecha'], $include_printed);
  } elseif ($_POST['imprimir'] == 'IC') { //Cliente
    $guias = Guia::all_by_cliente_and_fecha($_POST['id_cliente'], $_POST['fecha'], $include_printed);
  } else {
    if (empty($_POST['numeros'])) exit('Escribe los n&uacute;meros de gu&iacute;as separados por coma.');
    $debe_actualizar_fecha_recibido = true;
    $guias = Guia::find_by_id($_POST['numeros']);
  }
  if (count($guias) == 0) {
    exit('<h2>No se encontraron gu&iacute;as...</h2>');
  }
} else {
  if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
    include Logistica::$root."mensajes/id.php";
    exit;
  }
  if (! $guia->find($_GET['id'])) exit('No existe la guía.');
  $guias = array($guia);
}

require_once Logistica::$root."php/tcpdf/PDF.php";

$pdf = new PDF;
$configuracion = new Configuracion;
$barcode = 'C128B';

$pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LEGAL', true, 'UTF-8', false);

$pdf->SetCreator('Logística');
$pdf->SetAuthor('Mario Alberto Acosta Palacio <amariop2685@gmail.com>');
$title = 'Guías';
if (count($guias) == 1) {
  $title = 'Guía '.$guias[0]->id;
}
$pdf->SetTitle($title);
$pdf->SetSubject("Guías - Transportes Mario Acosta");
$pdf->SetKeywords('Guías, Transportes Mario Acosta');
$pdf->Guia();

foreach ($guias as $guia) {
  $id_guias[] = $guia->id;
  $guia->cliente();
  $guia->contacto();
  $pdf->AddPage();
  $template = 'CREDITO' == $guia->formapago ? 'default.php' : 'custom.php';
  if ($debe_actualizar_fecha_recibido and in_array($guia->estado(), array('BODEGA', 'PREGUIA'))) {
    $guia->update_attributes(array('fecha_recibido_mercancia' => date('Y-m-d')));
    Logger::guia($guia->id, 'llegó la mercancia a bodega.');
  }
  for ($i = 1; $i <= $cantidad; $i++) {
    require $template;
  }
}
$pdf->Output($title, 'I');
if (isset($_REQUEST['varias'])) Guia::mark_as_printed($id_guias);
