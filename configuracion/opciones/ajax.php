<?php
require "../../seguridad.php";

if (isset($_GET['buscar_aseguradora'])) {
	echo Aseguradora::autocomplete($_GET['term']);
	exit;
}
