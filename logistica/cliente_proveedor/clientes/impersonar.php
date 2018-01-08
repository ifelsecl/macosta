<?php
$raiz = '../../';
require $raiz.'php/Nonce.inc.php';
require $raiz.'autoload_class.php';
DBManager::connect();

if (! isset($_SESSION)) session_start();
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  include $raiz."mensajes/id.php";
  exit;
}
$user_id = $_SESSION['userid'];
if (! isset($_SESSION['permisos']['Clientes_Impersonar'])) {
  include $raiz."mensajes/permiso.php";
  exit;
}
$cliente = new Cliente;
if (! $cliente->find($_REQUEST['id'])) exit('No existe el cliente');

require $raiz."clientes/funciones.php";
sesionIniciada();
session_regenerate_id();
$_SESSION['numero_identificacion'] = $cliente->numero_identificacion;
$_SESSION['nombre']                = $cliente->nombre_completo;
$_SESSION['id']                    = $cliente->id;
$_SESSION['userid']                = 16;
$_SESSION['direccion']             = $cliente->direccion;
$_SESSION['telefono']              = $cliente->telefono;
$_SESSION['porcentajeseguro']      = $cliente->porcentajeseguro;
$_SESSION['restriccion_peso']      = $cliente->restriccionpeso;
$_SESSION['username']              = 'cliente';
$_SESSION['ip']                    = IP();
$_SESSION['nl']                    = $cliente->nl == 'si';
$_SESSION['id_ciudad']             = $cliente->idciudad;
$_SESSION['mes']                   = date('m');
$_SESSION['ano']                   = date('Y');
$_SESSION['mes_actual']            = ucfirst(strftime('%B %Y',strtotime($_SESSION['ano'].'-'.$_SESSION['mes'].'-01')));

locale();
Logger::usuario($user_id, 'impersonó al cliente '.$cliente->nombre_completo);

redireccionar($raiz.'clientes/');
