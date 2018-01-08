<?php
if (count($_POST) > 50) exit('<h1>Posible exploit!</h1>');
$raiz = "../";
require $raiz.'clientes/funciones.php';

if (! isset($_SERVER['HTTP_REFERER'])) redireccionar('/logistica/inicio');
locale();

sesionIniciada();
if (! isset($_POST['numero_identificacion']) || ! isset($_POST['clave']))
  exit('Actualiza la página e intentalo nuevamente.');

$ni = $_POST['numero_identificacion'];
$clave = $_POST['clave'];
require $raiz.'class/Logistica.php';
Logistica::initialize();

if (! $cliente = Cliente::find_by_numero_identificacion_and_clave($ni, $clave)) exit('Por favor, revisa tus datos.');
$logger = new Logger;
if ($cliente->activo == 'no') {
  $logger->LogCliente("intentó iniciar sesión pero su cuenta está desactivada.", 'Acceso', $cliente->id);
  exit('Tu cuenta ha sido deshabilitada.');
}
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
$_SESSION['nl']                    = $cliente->nl == 'si' ? true : false;
$_SESSION['id_ciudad']             = $cliente->idciudad;
$_SESSION['mes']                   = date('m');
$_SESSION['ano']                   = date('Y');
$_SESSION['mes_actual']            = ucfirst(strftime('%B %Y',strtotime($_SESSION['ano'].'-'.$_SESSION['mes'].'-01')));
$logger->Log($_SESSION['ip'], "inició sesión.", "Clientes", date("Y-m-d H:i:s"), $cliente->id);
