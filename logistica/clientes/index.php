<?php
require 'funciones.php';
if (! sesionIniciada()) redireccionar('../inicio');

locale();
require '../php/Nonce.inc.php';
require '../helpers/html.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Transportes Mario Acosta ─ Logística ─ Clientes</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="Description" content="Sistema de Informacion Logistica de transporte terrestre de carga, integracion con Manifiestos y FTP del Ministerio de Transportes de Colombia.">
  <meta name="author" content="Mario Alberto Acosta Palacio <amariop2685@gmail.com>" />
  <link rel="stylesheet" media="all" href="http://fonts.googleapis.com/css?family=Open+Sans:400,700">
  <link rel="stylesheet" media="all" href="../bower_components/bootstrap2.3.2/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" media="all" href="../bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" media="all" href="../bower_components/jquery-ui/themes/redmond/jquery-ui.min.css" />
  <link rel="stylesheet" media="all" href="../bower_components/alertify/themes/alertify.core.css" />
  <link rel="stylesheet" media="all" href="../bower_components/alertify/themes/alertify.default.css" />
  <link rel="stylesheet" media="all" href="../css/style.clientes.css?_=123" />
  <link rel="stylesheet" media="all" href="../css/menu.clientes.css?_=123" />
  <link rel="stylesheet" media="all" href="../css/base.css?_=123" />
  <style>
  .ui-autocomplete {max-height: 300px;overflow-y: auto;}
  .ui-autocomplete-loading {background: transparent url('../css/ajax-loader.gif') right center no-repeat;}
  </style>
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
</head>
<body>
  <div class="navbar navbar-static-top navbar-inverse">
    <div class="navbar-inner">
      <a href="." class="brand transition" title="Inicio" id="logo">LOGÍSTICA</a>
      <ul class="nav">
        <li><a class="" title="Mes de trabajo" id="mes_trabajo" name="cambiar_mes"><?= $_SESSION['mes_actual'] ?></a></li>
      </ul>
      <ul class="nav pull-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle transition" data-toggle="dropdown">
            <i class="icon-user"></i> <?= $_SESSION['nombre'] ?>
            <b class="caret"></b>
          </a>
          <ul class="dropdown-menu pull-right">
            <li>
              <a class="transition" href="logout?<?= nonce_create_query_string() ?>"><i class="icon-signout"></i> Salir</a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
  <div id="topmenu">
    <ul>
      <li><a id="mInicio" title="Inicio" href="#Inicio" onclick="ga('send', 'event', 'MenuClientes', 'Inicio');">Inicio</a></li>
      <li><a id="mGuias" title="Mis Guías" href="#Guias" onclick="ga('send', 'event', 'MenuClientes', 'Guias');">Mis Guías</a></li>
      <li><a id="mContactos" title="Mis Contactos" href="#Contactos" onclick="ga('send', 'event', 'MenuClientes', 'Contactos');">Mis Contactos</a></li>
      <li><a id="mCuenta" title="Mi Cuenta" href="#Cuenta" onclick="ga('send', 'event', 'MenuClientes', 'Cuenta');">Mi Cuenta</a></li>
    </ul>
  </div>
  
  <div id="main_container">
   <div id="main_content" class="main_content">
      <div id="center_content" class="center_content">
        <p class="expand"><img src="../css/ajax-loader.gif" /> Cargando...</p>
      </div>
      <div id="extra_content"></div><!-- end of Extra content-->
      <div class="clear"></div>
    </div> <!-- end of Main content-->
    <div class="footer">
      <div class="left_footer">Transportes Mario Acosta & Cia Ltda ─ <a href="https://facebook.com/TransportesMarioAcosta" class="btn btn-mini btn-primary" target="_blank">Facebook</a></div>
      <div class="right_footer">Tels: 379 0808 - 370 7478 - 379 4880</div>
    </div>
  </div>
  <div id="dialog"></div>
  <!-- <div id="notification_message"></div> -->
  <script src="../bower_components/jquery/dist/jquery.min.js"></script>
  <script src="../bower_components/bootstrap2.3.2/bootstrap/js/bootstrap.min.js"></script>
  <script src="../bower_components/jquery-ui/jquery-ui.min.js"></script>
  <script src="../bower_components/jquery-ui/ui/i18n/datepicker-es.js"></script>
  <script src="../bower_components/jquery-sapzxc-hashchange/jquery.ba-hashchange.min.js"></script>
  <script src="../bower_components/jquery-validation/dist/jquery.validate.js"></script>
  <script src="../bower_components/jquery-validation/src/localization/messages_es.js"></script>
  <script src="../bower_components/highcharts/highcharts.js"></script>
  <script src="../bower_components/highcharts/modules/exporting.js"></script>
  <script src="../bower_components/highcharts/modules/no-data-to-display.js"></script>
  <script src="../bower_components/pdfobject/pdfobject.min.js"></script>
  <script src="../bower_components/alertify/alertify.min.js"></script>
  <script src="../js/client-app-67b4eabb.js"></script>
  <?= google_analytics() ?>
</body>
</html>
