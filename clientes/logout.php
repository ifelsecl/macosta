<?php
require 'funciones.php';
locale();

if (sesionIniciada()) {
  require '../class/Logger.php';
  require '../class/DBManager.php';
  DBManager::connect();
  $logger = new Logger;
  $logger->LogCliente("Cerró sesión.", "Acceso", $_SESSION['id'], 'Cliente');
  session_destroy();
  $ip = IP();
  DBManager::execute("DELETE FROM ".LOGS_DATABASE_NAME.".online WHERE tipo='Cliente' AND ip='$ip'");
}
redireccionar('../inicio');
