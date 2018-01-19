<?php
require_once 'funciones.php';
if (! sesionIniciada()) redireccionar('inicio');
require 'bootstrap.php';
require_once 'helpers/html.php';
require_once 'php/Nonce.inc.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Transportes Mario Acosta ─ Logística</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="Description" content="Sistema de Informacion Logistica de transporte terrestre de carga, integracion con Manifiestos y FTP del Ministerio de Transportes de Colombia.">
  <meta name="author" content="Mario A. Acosta Palacio" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" media="all" href="http://fonts.googleapis.com/css?family=Open+Sans:400,700|PT+Sans:400,700">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">

  <?php
  echo bower_css_component('bootstrap2.3.2/bootstrap/css/bootstrap.min');
  echo bower_css_component('bootstrap2.3.2/bootstrap/css/bootstrap-responsive.min');
  echo bower_css_component('font-awesome/css/font-awesome.min');
  echo bower_css_component('jquery-ui/themes/redmond/jquery-ui.min');
  echo bower_css_component('alertify/themes/alertify.core');
  echo bower_css_component('alertify/themes/alertify.default');
  echo bower_css_component('jakobmattsson-uploadify/uploadify');
  echo bower_css_component('bootstrap-switch/dist/css/bootstrap2/bootstrap-switch.min');
  echo bower_css_component('jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min');

  echo css_tag('base');
  echo css_tag('menu.administracion');
  echo css_tag('style.administracion');
  ?>
  <style>
  .ui-autocomplete {max-height: 300px;overflow-y: auto;}
  .ui-autocomplete-loading {background: transparent url('css/ajax-loader.gif') right center no-repeat !important;}
  </style>
</head>
<body>
  <div class="navbar navbar-static-top navbar-inverse">
    <div class="navbar-inner">
      <div class="container">
        <a href="." class="brand transition" title="Inicio" id="logo">LOGISTICA</a>
        <ul class="nav">
          <li><a class="transition" title="Mes de trabajo" id="mes_trabajo" href="#cambiar_mes"><?= $_SESSION['mes_actual'] ?></a></li>
        </ul>
        <ul class="nav pull-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle transition" data-toggle="dropdown">
              <i class="icon-user"></i> <?= $current_user->nombre ?>
              <b class="caret"></b>
            </a>
            <ul class="dropdown-menu pull-right">
              <li>
                <a class="transition" title="Cerrar Sesión" href="logout?<?= nonce_create_query_string() ?>" id="sessionKiller"><i class="icon-signout"></i> Salir</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div id="topmenu">
    <div class="container">
      <ul>
        <li><a id="mInicio" title="Inicio" onclick="ga('send', 'event', 'MenuAdministrativo', 'Click', 'Inicio');" href="#Inicio">Inicio</a></li>
        <li><a id="mClientes" href="#Clientes" title="Terceros" onclick="ga('send', 'event', 'MenuAdministrativo', 'Click', 'Clientes');">Clientes</a></li>
        <?php if (isset($current_user->permisos['Facturacion_Entrar'])) { ?>
        <li><a id="mFacturacion" href="#Facturacion" title="Facturación" onclick="ga('send', 'event', 'MenuAdministrativo', 'Click', 'Facturacion');">Facturación</a></li>
        <?php } ?>
        <li><a id="mLogistica" href="#Logistica" title="Logistica" onclick="ga('send', 'event', 'MenuAdministrativo', 'Click', 'Logistica');">Logística</a></li>
        <li><a id="mConfiguracion" href="#Configuracion" title="Configuración" onclick="ga('send', 'event', 'MenuAdministrativo', 'Click', 'Configuracion');">Configuración</a></li>
        <li><a id="mManten"  title="Manten" onclick="ga('send', 'event', 'MenuAdministrativo','Click', 'Manten');" href="#Manten">Mantenimiento</a></li>
      </ul>
    </div>
  </div>
  <div id="main-content" class="container-fluid main_content">
    <div id="center-content" class="row-fluid center_content">
      <div class="span2">
        <!--Sidebar content-->
      </div>
      <div class="span10">
        <p style="height:300px" class="expand"><img src="css/ajax-loader.gif" /> Cargando...</p>
        <!--Body content-->
      </div>
    </div>
    <div id="footer" class="row-fluid">
      <div id="footer-content" class="row-fluid">
        <div class="span6 offset6">
          <div class="left-footer muted">Transportes Mario Acosta & Cia Ltda &copy; <?= date('Y') ?></div>
        </div>
      </div>
    </div>
  </div>
  <div id="dialog"></div>
  <?php
  echo bower_js_component('jquery/dist/jquery.min');
  echo bower_js_component('bootstrap2.3.2/bootstrap/js/bootstrap.min');
  echo bower_js_component('jquery-ui/jquery-ui.min');
  echo bower_js_component('jquery-ui/ui/i18n/datepicker-es');
  echo bower_js_component('jquery-validation/dist/jquery.validate.min');
  echo bower_js_component('jquery-validation/src/localization/messages_es');
  echo bower_js_component('jquery-sapzxc-hashchange/jquery.ba-hashchange.min');
  echo bower_js_component('jQuery.serializeObject/dist/jquery.serializeObject.min');
  echo bower_js_component('highcharts/highcharts');
  echo bower_js_component('highcharts/modules/exporting');
  echo bower_js_component('highcharts/modules/no-data-to-display');
  echo bower_js_component('jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min');
  echo bower_js_component('alertify/alertify.min');
  echo bower_js_component('pdfobject/pdfobject.min');
  echo bower_js_component('jakobmattsson-uploadify/swfobject');
  echo bower_js_component('jakobmattsson-uploadify/jquery.uploadify.v2.1.4.min');
  echo bower_js_component('bootstrap-switch/dist/js/bootstrap-switch.min');
  echo bower_js_component('hogan/web/builds/3.0.2/hogan-3.0.2.min');
  echo bower_js_component('typeahead.js/dist/typeahead.min');
  ?>  

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

  <script src="js/main-app-ad10b071.js"></script>
  <?= google_analytics() ?>
</body>
</html>

                            