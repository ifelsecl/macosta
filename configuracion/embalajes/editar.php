<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
if (! isset($_SESSION['permisos'][EMBALAJES_EDITAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}
if (! $embalaje = Embalaje::find($_GET['id'])) exit('No existe el embalaje.');
require '_form.php';
