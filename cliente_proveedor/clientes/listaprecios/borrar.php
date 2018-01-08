<?php
require "../../../seguridad.php";
$cliente = new Cliente;
$result = $cliente->BorrarPrecio($_GET['idcliente'], $_GET['idciudadorigen'], $_GET['idciudaddestino'], $_GET['idembalaje']);
if ($result == "si") {
  Logger::precio($_GET['idcliente'], 'borró un precio');
	echo 1;
} else {
	echo 0;
}
