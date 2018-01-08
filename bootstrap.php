<?php
require 'class/Logistica.php';
Logistica::initialize();
Logistica::$root = dirname(__FILE__) . '/';

$current_user = Usuario::find($_SESSION['userid']);
unset($current_user->clave);
$current_user->permisos = $current_user->format_permissions();
require 'php/Nonce.inc.php';
require 'online.php';
