<?php
/*
Este archivo no permite que algunos archivos sean ejecutados directamente desde el navegador.
Inicia y comprueba la sesión
Establece la zona horaria a Bogotá/Colombia
Establece la localización a Español de Colombia para PHP.
*/
require_once 'funciones.php';
if (! isset($_SERVER['HTTP_REFERER'])) {
  $script_url = explode('/', $_SERVER['SCRIPT_NAME']);
  $url = 'http://'.$_SERVER['SERVER_NAME'].'/'.$script_url[1];
  redireccionar($url);
}
define('LOGISTICA_ROOT', realpath(dirname(__FILE__).'/../').'/');
if (! sesionIniciada()) {
  include LOGISTICA_ROOT."mensajes/sesion.php";
  exit;
}

locale();

require LOGISTICA_ROOT.'autoload_class.php';
require LOGISTICA_ROOT."php/Nonce.inc.php";

DBManager::connect();
include LOGISTICA_ROOT.'online.php';
