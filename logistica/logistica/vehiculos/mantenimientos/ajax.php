<?php
require '../../../seguridad.php';
Logistica::respond_as_json();

if (isset($_POST['crear'])) {
  if ($vehiculo_mantenimiento = VehiculoMantenimiento::create($_POST['vehiculo_mantenimiento'])) {
    $vehiculo_mantenimiento->reload();
    $response = array('success' => true, 'html' => $vehiculo_mantenimiento->__toString());
  } else {
    $response = array('success' => false, 'message' => 'Por favor, intentalo nuevamente.');
  }
  echo json_encode($response);
  exit;
}
if (isset($_POST['editar'])) {
  if (! $vehiculo_mantenimiento = VehiculoMantenimiento::find($_POST['id'])) {
    exit('No existe el mantenimiento.');
  }
  $changes = $vehiculo_mantenimiento->updated_attributes($_POST['vehiculo_mantenimiento']);
  if ($changes) {
    if ($vehiculo_mantenimiento->update_attributes($_POST['vehiculo_mantenimiento'])) {
      $vehiculo_mantenimiento->reload();
      $response = array('success' => true, 'html' => $vehiculo_mantenimiento->__toString());
    } else {
      $response = array('success' => false, 'message' => 'Por favor, intentalo nuevamente.');
    }
  } else {
    $response = array('success' => false, 'message' => 'No hay cambios para actualizar.');
  }
  echo json_encode($response);
  exit;
}
