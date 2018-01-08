<?php
require_once 'funciones.php';

if (! isset($_SERVER['HTTP_REFERER'])) {
  $url = explode('/', $_SERVER['SCRIPT_NAME']);
  redireccionar("Location: http://".$_SERVER['SERVER_NAME']."/".$url[1]);
}

if (! sesionIniciada()) {
  include "mensajes/sesion.php";
  exit;
}

require 'bootstrap.php';
