<?php
$raiz = '../../';
require '../seguridad.php';
require_once $raiz."class/guias.class.php";

$formas_pago = Guias::formas_pago();

require $_SESSION['nl'] ? 'crear_nueva.php' : 'crear_anterior.php';
