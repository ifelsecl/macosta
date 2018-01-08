<?php
//require "../seguridad.php";
/*if (! isset($_SESSION['permisos'][MANTEN_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}*/
?>
<div class="span2 left_content">
  <ul id="menu" class="sidebarmenu">
    <?php
      echo '<li><a href="#Manten/Individual" id="vehiculos">Individual</a></li>';
      echo '<li><a href="#Configuracion/Embalajes" id="talonarios">General</a></li>';
    ?>
  </ul>
  <div class="sidebar_box visible-desktop">
    <div class="sidebar_box_top">
      <img src="css/images/info.png" alt="Info" class="pull-right" />
      <h4>Mantenimientos</h4>
    </div>
    <div class="sidebar_box_content">
      <p>Usted puede ejecutar reportes y solicitar información referente a mantenimientos.</p>
    </div>
  </div>
</div>

<div class="span10 right_content"></div><!-- end right content-->
<div id="extra_content" class="span10" style="display:none"></div><!-- end extra content-->