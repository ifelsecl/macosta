<?php
require "../seguridad.php";
?>
<div class="span2 left_content">
  <ul id="menu" class="sidebarmenu">
    <?php
    if (isset($_SESSION['permisos'][CLIENTES_ENTRAR])) {
      echo '<li><a href="#Clientes/Clientes" id="clientes">Clientes</a></li>';
    }
    if (isset($_SESSION['permisos'][VENDEDORES_ENTRAR])) {
      echo '<li><a href="#Clientes/Vendedores" id="vendedores">Vendedores</a></li>';
    }
    if ($_SESSION['nombre_perfil'] == 'Administrador') {
      echo '<li><hr class="hr-small"></li>';
      echo '<li><a href="#Clientes/Cartas" id="cartas">Cartas</a>';
    }
    ?>
    <li><a href="#Clientes/Citas" id="citas">Citas Muelle</a></li>
  </ul>
  <div class="sidebar_box visible-desktop">
    <div class="sidebar_box_top">
      <img src="css/images/info.png" alt="Info" class="pull-right" />
      <h4>User help desk</h4>
    </div>
    <div class="sidebar_box_content">
      <p>Puede Hacer la gestion de sus clientes y vendedores.<br>
Todas las acciones que se realizen, tales como Anular, Editar o Crear, serán registradas.</p>
    </div>
  </div>
</div>
<div class="span10 right_content">
  <p style="height:450px;">&nbsp;</p>
</div><!-- end of right content-->
<div id="extra_content" class="span10" style="display: none"></div><!-- end of extra content-->
