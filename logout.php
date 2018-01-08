<?php
require 'funciones.php';
require 'autoload_class.php';
DBManager::connect();

if (sesionIniciada()) {
  locale();
  $logger = new Logger;
  $ip = $_SESSION['ip'];
  $logger->Log($ip, "cerró sesión.", "Acceso", date("Y-m-d H:i:s"), $_SESSION['userid']);
  DBManager::execute("DELETE FROM ".LOGS_DATABASE_NAME.".online WHERE tipo='Usuario' AND ip='$ip'");
  session_destroy();
}
header('Location: inicio');
