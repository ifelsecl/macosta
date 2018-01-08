<?php
if (count($_POST) > 50) exit('<h1>Posible exploit!</h1>');
$raiz = "../";
require_once $raiz.'funciones.php';

if (! isset($_SERVER['HTTP_REFERER'])) redireccionar('/logistica/inicio');
locale();

session_start();
if (! isset($_POST['username']) || ! isset($_POST['password']))
  exit('Actualiza la página e intentalo nuevamente.');

require $raiz.'class/Logistica.php';
require $raiz.'class/DBManager.php';
Logistica::initialize();

if (! $usuario = Usuario::find_by_usuario($_POST['username'])) exit('Por favor, revisa tus datos');
if ($usuario->activo == 'no') {
  Logger::usuario($usuario->id, "intentó iniciar sesión pero su cuenta está desactivada.");
  exit('Tu usuario ha sido deshabilitado');
}
if ($usuario->clave != md5($_POST['password'])) exit('Por favor, revisa tus datos');
$usuario->update_last_login_date();
$_SESSION['username']       = $usuario->usuario;
$_SESSION['userid']         = $usuario->id;
$_SESSION['nombre']         = $usuario->nombre;
$_SESSION['ip']             = IP();
$_SESSION['nombre_perfil']  = $usuario->perfil();
$_SESSION['perfil']         = $usuario->idperfil;
$_SESSION['token']          = md5($usuario->usuario);
$_SESSION['mes']            = date('m');
$_SESSION['ano']            = date('Y');
$_SESSION['mes_actual']     = ucfirst(strftime('%B %Y',strtotime($_SESSION['ano'].'-'.$_SESSION['mes'].'-01')));
$_SESSION['permisos']       = $usuario->format_permissions();
Logger::usuario($usuario->id, "inició sesión");
