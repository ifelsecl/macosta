<?php
require '../../seguridad.php';
class ResolucionesController {

	static $resolucion;

	static function get_resolucion($params) {
		if (! isset($params['id']) or ! nonce_is_valid($params[NONCE_KEY], $params['id'])) {
			include Logistica::$root."mensajes/id.php";
			exit;
		}
		if (! self::$resolucion = Resolucion::find($params['id'])) exit('No existe la resolucion.');
	}

	static function create_or_update() {
		if (isset($_POST['id'])) {
			self::update($_POST);
		} else {
			self::create($_POST);
		}
	}

	static function create($params) {
		if (! Resolucion::_create($params['resolucion'])) {
			echo 'No se pudo guardar la resolución, intentalo nuevamente.';
		}
	}

	static function update($params) {
		self::get_resolucion($params);
		$changes = self::$resolucion->updated_attributes($params['resolucion'], false);
		if ($changes) {
			self::$resolucion->update_attributes($changes);
		} else {
			echo 'No hay nada para actualizar.';
		}
	}

	static function destroy() {
		self::get_resolucion($_GET);
		if (! self::$resolucion->delete()) {
			echo 'No se pudo eliminar la resolución, intentalo nuevamente.';
		}
	}
}

if ('POST' == $_SERVER['REQUEST_METHOD']) {
	ResolucionesController::create_or_update();
	exit;
}
if ('DELETE' == $_SERVER['REQUEST_METHOD']) {
	ResolucionesController::destroy();
	exit;
}
