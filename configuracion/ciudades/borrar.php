<?php
require '../../seguridad.php';
if (! isset($_REQUEST['id'])) exit;

if ($ciudad = Ciudad::find($_REQUEST['id'])) {
	if ($ciudad->can_be_destroyed()) {
		if ($ciudad->destroy()) {
			Logger::ciudad($ciudad->id, 'eliminó la ciudad');
			exit;
		} else {
			$msj = "<i class='icon-remove'></i>Ha ocurrido un error, intentalo nuevamente.";
		}
	} else {
		$msj = '<i class="icon-warning-sign icon-2x"></i> Esta ciudad no se puede eliminar, existen registros asociados a ella.';
	}
} else {
	$msj = 'No existe ninguna ciudad con el código '.$_REQUEST['id'];
}
exit('<p class="expand">'.$msj.'</p>');
