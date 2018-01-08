<?php
$raiz = "../../";
include '../seguridad.php';

$guia = new Guia;
if (! $guia->find($_GET['id'])) exit('No existe la guía.');

$_SESSION['id_guia'] = $guia->id;
$guia->items();

$formas_pago = Guias::formas_pago();

$file = $_SESSION['nl'] ? 'editar_nueva.php' : 'editar_anterior.php' ;
require $file;
