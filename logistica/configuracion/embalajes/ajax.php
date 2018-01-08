<?php
require "../../seguridad.php";
if (isset($_POST['guardar'])) {
  $params = array('nombre' => $_POST['nombre'], 'descripcion' => $_POST['descripcion'], 'tipo_cobro' => $_POST['tipo_cobro']);
  if ($embalaje = Embalaje::create($params)) {
    Logger::tipo_cobro($embalaje->id, 'creó el tipo de cobro "'.$embalaje->nombre.'".');
  } else {
    include Logistica::$root.'mensajes/guardando_error.php';
  }
  exit;
}
if (isset($_POST['editar'])) {
  if (! nonce_is_valid($_POST[NONCE_KEY], $_POST['id']) or ! isset($_POST['id'])) {
    include Logistica::$root.'mensajes/id.php';
    exit;
  }
  $objEmbalaje = new Embalaje;
  if ($objEmbalaje->Editar($_POST['id'], $_POST['nombre'], $_POST['descripcion'], $_POST['tipo_cobro'])) {
    Logger::tipo_cobro($_REQUEST['id'], 'editó el tipo de cobro "'.$_REQUEST['nombre'].'".');
  } else {
    include Logistica::$root.'mensajes/guardando_error.php';
  }
  exit;
}
