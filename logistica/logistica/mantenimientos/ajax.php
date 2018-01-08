<?php
require '../../seguridad.php';

if (isset($_POST['create'])) {
  $params = array('nombre' => $_POST['nombre'], 'kilometraje' => $_POST['km']);
  if (! $mantenimiento = Mantenimiento::create($params)) {
    exit('No se pudo guardar la informacion');
  }
}

if (isset($_POST['update'])) {
  if (! $mantenimiento = Mantenimiento::find($_POST['id'])) exit('No existe el mantenimiento.');
  $params = array('nombre' => $_POST['nombre'], 'kilometraje' => $_POST['km']);
  $changes = $mantenimiento->updated_attributes($params);
  if ($changes) {
    if (! $mantenimiento->update_attributes($params)) {
      exit('Por favor, intentalo nuevamente.');
    }
  } else {
    exit('No hay cambios para actualizar.');
  }
}
