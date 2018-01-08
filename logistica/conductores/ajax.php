<?php
require '../../seguridad.php';
if (! isset($_POST['numero_identificacion'])) {
  include Logistica::$root.'mensajes/id.php';
  exit;
}
if (isset($_POST['editar'])) {
  $conductor = new Conductor;
  if (! $conductor->find($_POST['numero_identificacion'])) exit('No existe el conductor');
  $changes = $conductor->updated_attributes($_POST);
  if ($changes) {
    if ($conductor->update($_POST)) {
      Logger::conductor($conductor->numero_identificacion, 'editó el conductor'.$changes);
    } else {
      include Logistica::$root.'mensajes/guardando_error.php';
    }
  } else echo 'No hay nada para actualizar';
  exit;
}
if (isset($_POST['guardar'])) {
  $_POST['categorialicencia'] = $_POST['categoria'].'-'.$_POST['licencia'];
  $conductor = new Conductor($_POST);
  if ($conductor->find($_POST['numero_identificacion'])) {
    exit('Ya existe un conductor con el número de identificación '.$conductor->numero_identificacion);
  }
  if ($conductor->create()) {
    Logger::conductor($conductor->numero_identificacion, 'creó el conductor');
  } else {
    include Logistica::$root.'mensajes/guardando_error.php';
  }
  exit;
}
