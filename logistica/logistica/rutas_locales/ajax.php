<?php
if (! isset($_POST['ruta_local']) or ! isset($_POST['action']))
  exit('Algo ha salido mal...');

$action = $_POST['action'];
require '../../seguridad.php';

if ($action == 'save') {
  if (! isset($_POST['ruta_local']['guias'])) {
    exit('Agrega por lo menos una guía.');
  }
  if (isset($_POST['vehiculo_empresa'])) {
    $_POST['ruta_local']['placa_vehiculo_2'] = '';
  } else {
    $_POST['ruta_local']['placa_vehiculo'] = null;
  }
  if (empty($_POST['ruta_local']['id'])) {
    if ($ruta_local = RutaLocal::create($_POST['ruta_local'])) {
      Logger::ruta_local($ruta_local->id, 'creó la ruta local');
    } else {
      echo 'No se pudo guardar, por favor intentalo nuevamente.';
    }
  } else {
    if (! $ruta_local = RutaLocal::find($_POST['ruta_local']['id'])) exit('No existe la ruta local.');
    $changes = $ruta_local->updated_attributes($_POST['ruta_local']);
    if (! $changes) exit('No hay nada para actualizar');
    if ($ruta_local->update($_POST['ruta_local'])) {
      Logger::ruta_local($ruta_local->id, 'editó la ruta local'.$changes);
    } else {
      echo 'No se pudo actualizar, por favor intentalo nuevamente.';
    }
  }
  exit;
}
