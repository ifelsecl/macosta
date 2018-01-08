<?php
require "../../seguridad.php";
if (isset($_REQUEST['bm'])) {
	echo Ciudad::autocomplete_by_municipio($_GET['term']);
	exit;
}

if (isset($_REQUEST['g'])) {
	if ($ciudad = Ciudad::find($_REQUEST['codigo'])) {
		exit('Existe una ciudad con el código '.$_REQUEST['codigo'].'.');
	} else {
		if ($ciudad->create($_REQUEST['codigo'], $_REQUEST['municipio'], $_REQUEST['nombre'], $_REQUEST['departamento'])) {
			Logger::ciudad($_REQUEST['codigo'], 'creó la ciudad');
		} else {
			include Logistica::$root.'mensajes/guardando_error.php';
		}
	}
	exit;
}
