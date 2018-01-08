<?php
require '../class/Logistica.php';
Logistica::initialize();
Logistica::respond_as_json();

if (isset($_GET['ciudad'])) {
  echo Ciudad::autocomplete($_GET['term']);
  exit;
}
if (isset($_GET['cliente'])) {
  echo Cliente::autocomplete($_GET['term']);
  exit;
}
if (isset($_GET['contacto'])) {
  echo Contacto::autocomplete($_GET['term']);
  exit;
}
if (isset($_GET['guia'])) {
  $guia = new Guia;
  if ($guia->find($_GET['guia']['id'])) {
    $guia->cliente();
    $guia->contacto();
  } else {
    $guia = array('error' => true, 'message' => 'No existe la guía');
  }
  echo json_encode($guia);
  exit;
}
if (isset($_GET['tercero'])) {
  echo Tercero::autocomplete($_GET['term']);
  exit;
}
if (isset($_GET['conductor'])) {
  echo Conductor::autocomplete($_GET['term']);
  exit;
}
if (isset($_GET['producto'])) {
  echo Producto::autocomplete($_GET['term']);
  exit;
}
