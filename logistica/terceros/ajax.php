<?php
require '../../seguridad.php';

if (! isset($_POST['action'])) exit('Algo ha salido mal');

if ($_POST['action'] == 'save') {
	if (empty($_POST['id'])) {
		if ($tercero = Tercero::find_by_numero_identificacion($_POST['numero_identificacion'])) {
			exit('Ya existe un tercero con el numero de identificacón '.$_POST['numero_identificacion']);
		}
		$tercero = new Tercero($_POST);
		if ($tercero->create()) {
			Logger::tercero($tercero->id, 'creó el tercero');
		} else {
			include Logistica::$root.'mensajes/guardando_error.php';
		}
	} else {
		if (! $tercero = Tercero::find($_POST['id'])) exit('No existe el tercero');
		$changes = $tercero->updated_attributes($_POST);
		if ($changes) {
			if ($tercero->update($_POST)) {
				Logger::tercero($tercero->id, 'editó el tercero'.$changes);
			} else {
				include Logistica::$root.'mensajes/guardando_error.php';
			}
		} else {
			echo 'No hay nada para actualzar';
		}
	}
}
