<?php
require 'seguridad.php';
if (! isset($_REQUEST['accion'])) exit('Algo ha salido mal...');
$a = $_REQUEST['accion'];
$cliente = $_SESSION['id'];

switch ($a) {

  case 'Mercancia':
    require_once '../class/guias.class.php';
    $g=new Guias;
    $mes=$_REQUEST['mes'];
    if($mes=='ACTUAL') $tipo='DIA';
    else $tipo='MES';

    echo json_encode($g->TotalMercanciaTransportada($cliente,$mes,$tipo));
    break;

  case 'Destinos':
    require_once '../class/guias.class.php';
    $g=new Guias;
    $mes=$_REQUEST['mes'];
    echo json_encode($g->DestinosMasVisitados($cliente,$mes,7));
    break;

  case 'Contactos':
    $mes = $_REQUEST['mes'];
    echo json_encode(Cliente::contactos_mas_frecuentes($cliente, $mes, 10));
    break;

  default:

    break;
}
