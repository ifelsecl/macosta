<?php
require '../../seguridad.php';
if (! isset($_SESSION['permisos'][RUTAS_LOCALES_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

$rutas_locales = RutaLocal::all('sql');
$paging = new PHPPaging('right_content', $rutas_locales);
$paging->ejecutar();
?>
<script>
$(function(){
  $("#crear").click(function() {
    cargarExtra(rutas_locales_path+"crear.php");
  });
  $(".ver").click(function(event){
    event.preventDefault();
    cargarExtra(rutas_locales_path+'ver.php?'+this.name);
  });
  $(".editar").click(function(event){
    event.preventDefault();
    cargarExtra(rutas_locales_path+'editar.php?'+this.name);
  });
  $(".anular").click(function(e){
    e.preventDefault();
    var msg = confirm("¿Deseas borrar esta ruta local?");
    if(!msg) return;
    $btn = $(this).find('i').removeClass('icon-trash').addClass('icon-spinner icon-spin');
    $.ajax({
      url: rutas_locales_path+'anular.php',
      type: "POST",
      data: this.name,
      success: function(msj){
        if (msj) {
          $btn.removeClass('icon-spinner icon-spin').addClass('icon-trash');
          LOGISTICA.Dialog.open('Error', msj, true);
        }else{
          cargarPrincipal(rutas_locales_path+'?pagina='+$('#pagina').val());
        }
      }
    });
  });
  $(".deshacer").click(function(e){
    e.preventDefault();
    var msg = confirm("¿Deseas activar esta ruta local?");
    if(!msg) return;
    $btn = $(this).find('i').removeClass('icon-upload').addClass('icon-spinner icon-spin');
    $.ajax({
      url: rutas_locales_path+'deshacer.php',
      type: "POST",
      data: this.name,
      success: function(msj){
        if (msj) {
          LOGISTICA.Dialog.open('Error', msj, true);
          $btn.removeClass('icon-spinner icon-spin').addClass('icon-upload');
        }else{
          cargarPrincipal(rutas_locales_path+'?pagina='+$('#pagina').val());
        }
      }
    });
  });
  $('.imprimir').click(function(event){
    $(this).attr("target","_blank")
      .attr("href",rutas_locales_path+"imprimir?"+this.name);
  });
});

function fn_paginar(d, url){
  $('.'+d).load(url);
}
</script>
<div class="pull-right">
  <?php if( isset($_SESSION['permisos'][RUTAS_LOCALES_CREAR]) ) { ?>
    <button id="crear" class="btn btn-info"><i class="icon-plus"></i> Crear Ruta Local</button>
  <?php } ?>
</div>
<h2>Rutas Locales</h2>
<table class="table table-bordered table-condensed table-hover">
  <thead>
    <tr>
      <th>ID</th>
      <th>Fecha</th>
      <th>Vehículo</th>
      <th>Conductor</th>
      <th>Ciudad</th>
      <th style="width: 120px">Acción</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if ($paging->numTotalRegistros() == 0) {
      echo '<tr class="warning"><td class="expand" colspan="6">No se encontraron rutas locales...</td></tr>';
    } else {
      while ($rl = $paging->fetchResultado('RutaLocal')) {
        $c = '';
        if ($rl->activo == 'no') $c = 'error';
      echo '<tr class="'.$c.'">';
      echo '<td>'.$rl->id.'</td>';
      echo '<td>'.$rl->fecha_corta.'</td>';
      echo '<td>'.$rl->placa().'</td>';
      echo '<td>'.$rl->conductor_nombre_completo.'</td>';
      echo '<td>'.$rl->ciudad_nombre.'</td>';
      $name = "id=".$rl->id."&".nonce_create_query_string($rl->id);
      echo '<td><div class="btn-group">';
      if ( isset($_SESSION['permisos'][RUTAS_LOCALES_VER]) ) {
        echo '<a href="#" name="'.$name.'" class="btn ver" title="Ver"><i class="icon-search"></i></a>';
      }
      if ( isset($_SESSION['permisos'][RUTAS_LOCALES_EDITAR]) and $rl->activo == 'si') {
        echo '<a href="#" name="'.$name.'" class="btn editar" title="Editar"><i class="icon-pencil"></i/></a>';
      }
      if ( isset($_SESSION['permisos'][RUTAS_LOCALES_IMPRIMIR]) and $rl->activo == 'si') {
        echo '<a href="#" name="'.$name.'" class="btn imprimir" title="Imprimir"><i class="icon-print"></i></a>';
      }
      if ( isset($_SESSION['permisos'][RUTAS_LOCALES_ANULAR]) and $rl->activo == 'si') {
        echo '<a href="#" name="'.$name.'" class="btn anular btn-danger" title="Borrar"><i class="icon-trash"></i></a>';
      }elseif( isset($_SESSION['permisos'][RUTAS_LOCALES_DESHACER]) and $rl->activo == 'no'){
        echo '<a href="#" name="'.$name.'" class="btn deshacer btn-success" title="Activar"><i class="icon-upload"></i></a>';
      }
      echo '</div></td>';
      echo '</tr>';
    }
    }?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
<input type="hidden" id="pagina" value="<?= $paging->numEstaPagina() ?>" />
