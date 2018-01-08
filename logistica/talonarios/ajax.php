<?php
require '../../seguridad.php';

class TalonariosController {

	static $talonario;

	static function get_talonario($params) {
		if (! isset($params['id']) or ! nonce_is_valid($params[NONCE_KEY], $params['id'])) {
			include Logistica::$root."mensajes/id.php";
			exit;
		}
		if (! self::$talonario = Talonario::find($params['id'])) exit('No existe el talonario.');
	}

	static function create_or_update() {
		if (isset($_POST['id'])) {
			self::update();
		} else {
			self::create();
		}
	}

	static function create() {
		if (! Talonario::create($_POST['talonario'])) {
			echo 'No se pudo guardar el talonario, intentalo nuevamente.';
		}
	}

	static function update() {
		self::get_talonario($_POST);
		$changes = self::$talonario->updated_attributes($_POST['talonario'], false);
		if ($changes) {
			self::$talonario->update_attributes($changes);
		} else {
			echo 'No hay nada para actualizar.';
		}
	}

	static function destroy() {
		self::get_talonario($_GET);
		if (! self::$talonario->delete()) {
			echo 'No se pudo eliminar el talonario, intentalo nuevamente.';
		}
	}
}

if ('DELETE' == $_SERVER['REQUEST_METHOD']) {
	TalonariosController::destroy();
	exit;
}
if ('POST' == $_SERVER['REQUEST_METHOD']) {
	TalonariosController::create_or_update();
	exit;
}
