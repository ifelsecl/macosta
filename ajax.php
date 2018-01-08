<?php
require 'seguridad.php';
if (! isset($_REQUEST['accion'])) exit('Algo ha salido mal...');
$a = $_REQUEST['accion'];
Logistica::respond_as_json();

switch ($a) {
  case 'Manifiestos':
    require_once 'class/planillasC.class.php';
    $man=new PlanillasC;
    $mes=$_REQUEST['mes'];
    echo json_encode($man->DestinosMasVisitados(false, $mes,10));
    break;

  case 'Guias':
    require_once 'class/guias.class.php';
    $g=new Guias;
    $mes=$_REQUEST['mes'];
    $cliente=false;
    echo json_encode($g->DestinosMasVisitados($cliente,$mes,10));
    break;

  case 'Mercancia':
    require_once 'class/guias.class.php';
    $g = new Guias;
    $mes = $_REQUEST['mes'];
    if($mes == 'ACTUAL' or $mes == 'ANTERIOR') $tipo='DIA';
    else $tipo = 'MES';

    $cliente = false;
    echo json_encode($g->TotalMercanciaTransportada($cliente,$mes,$tipo));
    break;

  case 'Clientes':
    echo json_encode( Cliente::mas_frecuentes($_REQUEST['mes'], 10) );
    break;

  case 'Facturacion':
    echo json_encode(Factura::FacturacionMensual(false, $_REQUEST['mes'], 'MES'));
    break;

  case 'FacturacionCliente':
    echo json_encode(Factura::FacturacionMensual($_SESSION['id_cliente'], $_REQUEST['mes'], 'MES'));
    break;

  case 'ClientesMasFacturados':
    echo json_encode(Factura::ClientesMasFacturados($_REQUEST['mes'], 10));
    break;

  case 'DestinosCliente':
    require_once 'class/guias.class.php';
    $g=new Guias;
    $cliente=$_SESSION['id_cliente'];
    $mes=$_REQUEST['mes'];
    echo json_encode($g->DestinosMasVisitados($cliente,$mes,8));
    break;

  case 'ContactosCliente':
    $cliente = $_SESSION['id_cliente'];
    $mes = $_REQUEST['mes'];
    echo json_encode(Cliente::contactos_mas_frecuentes($cliente, $mes, 10));
    break;

  case 'MercanciaCliente':
    require_once 'class/guias.class.php';
    $g=new Guias;
    $mes=$_REQUEST['mes'];
    if($mes=='ACTUAL') $tipo='DIA';
    else $tipo='MES';

    $cliente=$_SESSION['id_cliente'];
    echo json_encode($g->TotalMercanciaTransportada($cliente,$mes,$tipo));
    break;

  case 'DestinosConductor':
    require_once 'class/planillasC.class.php';
    $man=new PlanillasC;
    $mes=$_REQUEST['mes'];
    echo json_encode($man->DestinosMasVisitados($_SESSION['id_conductor'], $mes,7));
    break;

  default:

    break;
}
