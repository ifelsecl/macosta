<?php
require "../seguridad.php";
?>
<div class="span2 left_content">
  <ul id="menu" class="sidebarmenu">
    <?php
    if (isset($_SESSION['permisos'][USUARIOS_ENTRAR])) {
      echo '<li><a href="#Configuracion/Usuarios" id="usuarios">Usuarios</a></li>';
    }
    if (isset($_SESSION['permisos'][EMBALAJES_ENTRAR])) {
      echo '<li><a href="#Configuracion/Embalajes" id="embalajes">Embalajes</a></li>';
    }
    if (isset($_SESSION['permisos'][OPCIONES_ENTRAR])) {
      echo '<li><a href="#Configuracion/Opciones" id="opciones">Opciones</a></li>';
    }
    if (isset($_SESSION['permisos'][RESOLUCIONES_ENTRAR])) {
      echo '<li><a href="#Configuracion/Resoluciones" id="resoluciones">Resoluciones</a></li>';
    }
    if (isset($_SESSION['permisos'][ARCHIVOS_ENTRAR])) {
      echo '<li><a href="#Configuracion/Archivos" id="archivos">Archivos Planos</a></li>';
    }
    if (isset($_SESSION['permisos'][PREGUNTAS_ENTRAR])) {
      echo '<li><a href="#Configuracion/Preguntas" id="preguntas">Preguntas</a></li>';
    }
    if (isset($_SESSION['permisos'][CIUDADES_ENTRAR])) {
      echo '<li><a href="#Configuracion/Ciudades" id="ciudades">Ciudades</a></li>';
    }
    if (isset($_SESSION['permisos'][ESTADISTICAS_ENTRAR])){
      echo '<li><a href="#Configuracion/Estadisticas" id="estadisticas">Estadísticas</a></li>';
    }
    if (isset($_SESSION['permisos'][BACKUP_ENTRAR])) {
      echo '<li><a href="#Configuracion/Backup" id="backup">Backup</a></li>';
    }
    ?>
    <li><a href="#Configuracion/Online" id="online">Usuarios Online</a></li>
  </ul>
  <div class="sidebar_box visible-desktop">
    <div class="sidebar_box_top">
      <img src="css/images/info.png" alt="Info" class="pull-right" />
      <h4>User help desk</h4>
    </div>
    <div class="sidebar_box_content">
      <p>Usted puede configurar algunos parámetros de la aplicación, gestionar los usuarios y sus permisos.
<br>Configurar el maestro de Ciudades, Embalajes y Preguntas.
<br>Generar los archivos planos para el Ministerio de Transporte y SIIGO.
<br>El listado de ciudades es suministrado por el ministerio de transporte.</p>
    </div>
  </div>
</div>

<div class="span10 right_content"></div><!-- end right content-->
<div id="extra_content" class="span10" style="display:none"></div><!-- end extra content-->
