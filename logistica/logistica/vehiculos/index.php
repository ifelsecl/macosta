<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CAMIONES_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}
$sql = Vehiculo::search($_GET, 'sql');
$paging = new PHPPaging('right_content', $sql);
$paging->ejecutar();
?>
<script>
$(function(){
  $('#bBuscar').button({icons: {primary:'ui-icon-search'}});
  $('#fBuscar').submit(function(e){
    e.preventDefault();
    if ($('#iPlaca').val()=='') return;
    $('#bBuscar').button('disable').button('option', 'label', 'Buscando...');
    cargarPrincipal(vehiculos_path+"?buscar=1&"+$(this).serialize());
  });
  $("#crear, #exportar").click(function() {
    cargarExtra(vehiculos_path+this.id+".php");
  });
  $("#configurar").click(function() {
    cargarExtra(mantenimientos_path);
  });
  $('table#vehiculos_list').on('click', '.ver', function(){
    cargarExtra(vehiculos_path+'ver.php?'+this.name);
  });
  $('table#vehiculos_list').on('click', '.editar', function(){
    cargarExtra(vehiculos_path+'editar.php?'+this.name);
  });
  $('table#vehiculos_list').on('click', 'a.anular', function(){
    var btn = $(this);
    alertify.confirm("¿Desea eliminar este vehículo?", function(e){
      if (e){
        btn.find('i').removeClass('icon-trash').addClass('icon-spin icon-spinner');
        $.ajax({
          url: vehiculos_path+'anular.php',
          type: "GET",
          data: btn.attr('name'),
          success: function(msj){
            if (msj == "ok"){
              cargarPrincipal(vehiculos_path);
            }else if (msj=="id"){
              alert("Algo ha salido mal, recarga la pagina e intentalo nuevamente.");
              btn.find('i').removeClass('icon-spin icon-spinner').addClass('icon-trash');
            }else if (msj=="permiso"){
              alert("No tienes permiso para anular vehiculos.");
              btn.find('i').removeClass('icon-spin icon-spinner').addClass('icon-trash');
            }else{
              alert("Ha ocurrido un error al eliminar... intentalo nuevamente.");
              btn.find('i').removeClass('icon-spin icon-spinner').addClass('icon-trash');
            }
          }
        });
      }
    });
  });
  $('table#vehiculos_list').on('click', 'a.deshacer', function(){
    var btn = $(this);
    alertify.confirm("Desea volver a activar este vehículo?", function(e){
      if (e){
        btn.find('i').removeClass('icon-ok').addClass('icon-spin icon-spinner');
        $.ajax({
          url: vehiculos_path+'deshacer.php',
          type: "GET",
          data: btn.attr('name'),
          success: function(msj){
            if (msj=="id"){
              alert("Algo ha salido mal, recarga la pagina e intentalo nuevamente.");
              btn.find('i').removeClass('icon-spin icon-spinner').addClass('icon-ok');
            }else if (msj=="permiso"){
              alert("No tienes permisos para activar vehiculos.");
              btn.find('i').removeClass('icon-spin icon-spinner').addClass('icon-ok');
            }else if (msj=="ok"){
              cargarPrincipal(vehiculos_path);
            }else{
              alert("Ha ocurrido un error al activar... intentalo nuevamente.");
              btn.find('i').removeClass('icon-spin icon-spinner').addClass('icon-ok');
            }
          }
        });
      }
    });
  });
});
function fn_paginar(d,url) { $('.'+d).load(url); }
</script>
<div class="pull-right btn-toolbar">
  <?php if (isset($_SESSION['permisos'][CAMIONES_CREAR])) { ?>
  <button id="crear" class="btn btn-info"><i class="icon-plus"></i> Crear Vehículo</button>
  <?php }
  if (isset($_SESSION['permisos'][CAMIONES_EXPORTAR])) { ?>
  <button id="exportar" class="btn"><i class="icon-file-alt"></i> Exportar</button>
  <?php }
  if (isset($_SESSION['permisos'][CAMIONES_CONFIGURAR_MANTENIMIENTOS])) { ?>
  <button id="configurar" class="btn"><i class="icon-cog"></i> Configurar Mantenimientos</button>
  <?php } ?>
</div>
<h2>Vehículos</h2>
<form id="fBuscar" class="form-inline" method="post" action="#">
  <table>
    <tr>
      <td>Placa:</td>
      <td><input type="text" maxlength="6" class="input-mini" id="iPlaca" name="placa" value="<?php if (isset($_GET['placa'])) echo $_GET['placa'] ?>" /></td>
      <td><button type="submit" id="bBuscar">Buscar</button></td>
    </tr>
  </table>
</form>
<table id="vehiculos_list" class="table table-hover table-bordered table-condensed">
  <thead>
    <tr>
      <th style="width: 50px">Placa</th>
      <th>Marca</th>
      <th>Modelo</th>
      <th>SOAT</th>
      <th>Propietario</th>
      <th>Peso (Kg)</th>
      <th style="width: 120px">Acción</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($paging->numTotalRegistros() == 0): ?>
    <tr class="warning"><td colspan="7" class="expand">No se encontraron vehículos...</td></tr>
    <?php endif; ?>
    <?php while ($v = $paging->fetchResultado('Vehiculo')): ?>
      <?php $c = !$v->activo() ? 'anulado' : ''; ?>
      <tr class="<?= $c ?>">
        <td><?= $v->placa ?></td>
        <td><?= $v->marca_nombre ?></td>
        <td><?= $v->modelo ?></td>
        <td><?= $v->soat ?><br><span class="muted"><?= strftime('%b %d, %Y', strtotime($v->f_venc_soat)) ?></span></td>
        <td><abbr title="<?= $v->propietario()->nombre_completo ?>"><?= substr($v->propietario->nombre_completo, 0, 20) ?></abbr></td>
        <td><?= number_format($v->peso) ?></td>
        <?php $name = 'placa='.$v->placa.'&'.nonce_create_query_string($v->placa); ?>
        <td><div class="btn-group">
        <?php if (isset($_SESSION['permisos'][CAMIONES_VER])): ?>
          <button class="btn ver" title="Ver" name="<?= $v->to_param() ?>"><i class="icon-search"></i></button>
        <?php endif; ?>
        <?php if (isset($_SESSION['permisos'][CAMIONES_EDITAR]) and $v->activo == 'si'): ?>
          <button class="btn editar" title="Editar" name="<?= $v->to_param() ?>"><i class="icon-pencil"></i></button>
        <?php endif; ?>
        <?php
        if (isset($_SESSION['permisos'][CAMIONES_ANULAR]) and $v->activo()) {
          echo '<button class="btn anular btn-danger" name="'.$v->to_param().'"><i class="icon-ban-circle"></i></button>';
        }elseif (isset($_SESSION['permisos'][CAMIONES_DESHACER]) and !$v->activo()){
          echo '<button class="btn deshacer btn-success" name="'.$v->to_param().'"><i class="icon-ok"></i></button>';
        }
        if ($v->activo()):
          echo '<a target="_blank" title="Imprimir Tarjeta" class="btn imprimir" href="logistica/vehiculos/tarjeta?'.$v->to_param().'"><i class="icon-file"></i></a>';
        endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
