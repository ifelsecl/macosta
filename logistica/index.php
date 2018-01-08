<?php
require '../seguridad.php';
?>
<div class="span2 left_content">
  <ul id="menu" class="sidebarmenu">
    <?php
    if (isset($_SESSION['permisos'][GUIAS_ENTRAR])) {
      echo '<li><a href="#Logistica/Guias" id="guias">Guías</a></li>';
    }
    if (isset($_SESSION['permisos'][PLANILLAS_ENTRAR])) {
      echo '<li><a href="#Logistica/Manifiestos" id="manifiestos">Manifiestos</a></li>';
    }
    if (isset($_SESSION['permisos'][PRODUCTOS_ENTRAR])) {
      echo '<li><a href="#Logistica/Productos" id="productos">Productos</a></li>';
    }
    if (isset($_SESSION['permisos'][CAMIONES_ENTRAR]) or isset($_SESSION['permisos'][CONDUCTORES_ENTRAR]) or isset($_SESSION['permisos'][TERCEROS_ENTRAR])) {
      echo '<li><hr class="hr-small"></li>';
    }
    if (isset($_SESSION['permisos'][CAMIONES_ENTRAR])) {
      echo '<li><a href="#Logistica/Vehiculos" id="vehiculos">Vehículos</a></li>';
    }
    if (isset($_SESSION['permisos'][CONDUCTORES_ENTRAR])) {
      echo '<li><a href="#Logistica/Conductores" id="conductores">Conductores</a></li>';
    }
    if (isset($_SESSION['permisos'][TERCEROS_ENTRAR])) {
      echo '<li><a href="#Logistica/Terceros" id="terceros" title="Propietarios, Tenedores y Titulares">Terceros</a></li>';
    }
    if (isset($_SESSION['permisos'][ORDENES_RECOGIDA_ENTRAR]) or isset($_SESSION['permisos'][RUTAS_LOCALES_ENTRAR]) or isset($_SESSION['permisos'][AYUDANTES_ENTRAR])) {
      echo '<li><hr class="hr-small"></li>';
    }
    if (isset($_SESSION['permisos'][ORDENES_RECOGIDA_ENTRAR])) {
      echo '<li><a href="#Logistica/OrdenesRecogida" id="ordenes_recogida">Ordenes de Recogida</a></li>';
    }
    if (isset($_SESSION['permisos'][RUTAS_LOCALES_ENTRAR])) {
      echo '<li><a href="#Logistica/RutasLocales" id="rutas_locales">Rutas Locales</a></li>';
    }
    if (isset($_SESSION['permisos'][AYUDANTES_ENTRAR])) {
      echo '<li><a href="#Logistica/Ayudantes" id="ayudantes">Ayudantes</a></li>';
    }
    if (isset($_SESSION['permisos'][TALONARIOS_ENTRAR])) {
      echo '<li><a href="#Logistica/Talonarios" id="talonarios">Control Guías</a></li>';
    }
    ?>
  </ul>
  <div class="sidebar_box visible-desktop">
    <div class="sidebar_box_top">
      <img src="css/images/info.png" alt="Info" class="pull-right" />
      <h4>User Help Desk</h4>
    </div>
    <div class="sidebar_box_content">
      <p>Puede gestionar la logistica de su empresa, administrar los
conductores, propietarios, tenedores, titulares, vehículos, manifiestos, guías
 y las ordenes de recogida.</p>
    </div>
  </div>
</div>
<div class="span10 right_content"></div><!-- end of right content-->
<div id="extra_content" class="span10" style="display:none"></div><!-- end of extra content-->
