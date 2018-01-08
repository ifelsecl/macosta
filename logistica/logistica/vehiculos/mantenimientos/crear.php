<?php
require '../../../seguridad.php';
if (! $vehiculo = Vehiculo::find($_GET['placa'])) exit('No existe el vehículo.');
$vehiculo_mantenimiento = new VehiculoMantenimiento(array('vehiculo_placa' => $vehiculo->placa));
require '_form.php';
