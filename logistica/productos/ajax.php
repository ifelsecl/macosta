<?php
require "../../seguridad.php";

if (isset($_POST['guardar'])) {
  if (Producto::find($_POST['producto']['id']))
    exit('¡Ya existe un producto con el código <b>'.$_POST['producto']['id'].'</b>!');
  if ($producto = Producto::create($_POST['producto'])) {
    Logger::producto($producto->id, 'creó el producto');
  } else {
    include Logistica::$root.'mensajes/guardando_error.php';
  }
  exit;
}

if (isset($_POST['editar'])) {
  if (! $producto = Producto::find($_POST['producto']['id'])) exit('No existe el producto.');
  $changes = $producto->updated_attributes($_POST['producto']);
  if ($changes) {
    if ($producto->update($_POST['producto'])) {
      Logger::producto($producto->id, 'editó el producto'.$changes);
    } else {
      include Logistica::$root.'mensajes/guardando_error.php';
    }
  } else {
    echo 'No hay nada para actualizar';
  }
}
