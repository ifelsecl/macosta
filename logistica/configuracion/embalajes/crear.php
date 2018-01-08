<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][EMBALAJES_CREAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}
$embalaje = new Embalaje;
require '_form.php';
