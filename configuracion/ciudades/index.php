<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CIUDADES_ENTRAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$ciudades = Ciudad::search($_GET, 'sql');
$paging = new PHPPaging('right_content', $ciudades);
$paging->porPagina(25)->ejecutar();
?>
<script>
$(function(){
  $('#ciudad_buscar_nombre').focus();
  $('#buscar').button({icons: {primary: 'ui-icon-search'}});
  $('#todos').button().click(function(){
    $(this).button('disable').button('option', 'label', 'Cargando...');
    cargarPrincipal(ciudades_path);
  });
  $('#ciudad_buscar').submit(function(e){
    e.preventDefault();
    $('#buscar').button('disable').button('option', 'label', 'Buscando...');
    cargarPrincipal(ciudades_path+'index.php?'+$(this).serialize());
  });
  $('button#crear').click(function(){
    cargarExtra(ciudades_path+'crear.php');
  });
  $('button#exportar').click(function(){
    cargarExtra(ciudades_path+'exportar.php');
  });
  $('.editar').click(function(e){
    e.preventDefault();
    cargarExtra(ciudades_path+'editar.php?'+this.name);
  });
  $('table#ciudades_list').on('click', 'button.borrar', function() {
    var c = confirm('Deseas borrar la ciudad seleccionada?');
    if (! c) return;
    $.ajax({
      url: ciudades_path+'borrar.php', type: 'POST',
      data: this.name,
      success: function(m){
        if (! m) {
          cargarPrincipal(ciudades_path+'index.php?'+$('#ciudad_buscar').serialize());
        } else {
          LOGISTICA.Dialog.open('Error', m, true);
        }
      }
    });
  });
});
function fn_paginar(d, url){
  $('.'+d).load(url);
}
</script>
<div class="pull-right">
  <?php if (isset($_SESSION['permisos'][CIUDADES_AGREGAR])){ ?>
  <button id="crear" class="btn btn-info"><i class="icon-plus"></i> Crear Ciudad</button>
  <?php } ?>
  <button id="exportar" class="btn"><i class="icon-file-alt"></i> Exportar</button>
</div>
<h2>Ciudades</h2>
<form id="ciudad_buscar" class="form-inline" name="ciudad_buscar" action="#" method="post">
  <?php if($paging->numEstaPagina() > 1) { ?>
    <input type="hidden" name="pagina" id="p" value="<?= $paging->numEstaPagina() ?>" />
  <?php } ?>
  <table>
    <tr>
      <td>Población/Código:</td>
      <td>Municipio:</td>
      <td>Departamento</td>
      <td></td>
    </tr>
    <tr>
      <td><input class="input-small" type="text" id="ciudad_buscar_nombre" name="nombre" value="<?= (isset($_GET['nombre'])) ? $_GET['nombre'] : '' ?>"></td>
      <td><input class="input-small" type="text" name="municipio" value="<?= (isset($_GET['municipio'])) ? $_GET['municipio'] : '' ?>"></td>
      <td><input class="input-small" type="text" name="departamento" value="<?= (isset($_GET['departamento'])) ? $_GET['departamento'] : '' ?>"></td>
      <td>
        <button type="submit" id="buscar">Buscar</button>
        <?php if (isset($_GET['termino'])) echo '<td><button type="button" id="todos">Todas</button></td>' ?>
      </td>
    </tr>
  </table>
</form>
<table id="ciudades_list" class="table table-hover table-bordered table-condensed">
  <thead>
    <tr>
      <th>Código</th>
      <th>Población</th>
      <th>Municipio</th>
      <th>Departamento</th>
      <th>Acción</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if ( empty($ciudades) ) {
      echo '<tr class="warning"><td colspan="5" class="expand">No se encontraron ciudades...</td></tr>';
    }else{
      while( $ciudad = $paging->fetchResultado('Ciudad') ){
        if (strtolower($ciudad->nombre) != 'null') {
          echo '<tr>';
          echo  '<td>'.$ciudad->id.'</td>';
          echo  '<td>'.$ciudad->nombre.'</td>';
          echo  '<td>'.$ciudad->municipio.'</td>';
          echo  '<td>'.$ciudad->departamento_nombre.'</td>';
          $n='id='.$ciudad->id.'&'.nonce_create_query_string($ciudad->id);
          if(isset($_SESSION['permisos'][CIUDADES_ELIMINAR])){
            echo '<td width="16" align="center"><button title="Borrar" name="'.$n.'" class="btn btn-danger borrar"><i class="icon-trash"></i></button></td>';
          }else{
            echo '<td width="16"></td>';
          }
          echo '</tr>';
        }
      }
    }
    ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
