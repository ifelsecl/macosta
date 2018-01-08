<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PRODUCTOS_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

if (isset($_GET['termino'])) {
  $productos = Producto::search($_GET, 'sql');
} else {
  $type = isset($_SESSION['permisos'][PRODUCTOS_DESHACER]) ? 'todos' : 'activos';
  $productos = Producto::all($type, 'sql');
}
$paging = new PHPPaging('right_content', $productos);
$paging->ejecutar();
?>
<script>
$(function() {
  function productos_update() {
    cargarPrincipal(productos_path+"?"+$('#BuscarProductos').serialize());
  }
  $("#crear" ).click(function() {
    cargarExtra(productos_path+'crear.php');
  });
  $(".editar").click(function(event) {
    event.preventDefault();
    cargarExtra(productos_path+'editar.php?'+this.name);
  });
  $(".ver").click(function(event) {
    event.preventDefault();
    cargarExtra(productos_path+'ver.php?'+this.name);
  });
  $("#exportar").click(function() {
    cargarExtra(productos_path+'exportar.php');
  });
  $(".deshacer").click(function(event) {
    event.preventDefault();
    var btn = $(this);
    btn.find('i').removeClass('icon-ok').addClass('icon-spinner icon-spin');
    $.ajax({
      url: productos_path+'deshacer.php',
      type: "POST",
      data: $(this).attr("name"),
      success: function(msj) {
        if (msj == "") {
          alertify.success('Producto '+btn.parents('tr').prop('id')+' activado');
          productos_update();
        } else {
          LOGISTICA.Dialog.open('Error', msj, true);
          btn.find('i').removeClass('icon-spinner icon-spin').addClass('icon-trash');
        }
      }
    });
  });
  $(".anular").click(function(e) {
    e.preventDefault();
    var btn = $(this);
    btn.find('i').removeClass('icon-trash').addClass('icon-spinner icon-spin');
    $.ajax({
      url: productos_path+'anular.php',
      type: "POST",
      data: $(this).attr("name"),
      success: function(msj) {
        if (msj == "") {
          alertify.success('Producto '+btn.parents('tr').prop('id')+' anulado');
          productos_update();
        } else {
          LOGISTICA.Dialog.open('Error', msj, true);
          btn.find('i').removeClass('icon-spinner icon-spin').addClass('icon-trash');
        }
      }
    });
  });

  $('#buscar').button({icons: {primary: 'ui-icon-search'}})
  $('#BuscarProductos').submit(function(event) {
    event.preventDefault();
    if ( $.trim($('#termino').val()) ) {
      $('#buscar').button('disable').button('option', 'label', 'Buscando...');
      cargarPrincipal(productos_path+"index.php?"+$('#BuscarProductos').serialize());
    } else {
      $('#termino').addClass('ui-state-highlight').focus();
    }
  });

  $('#todos').button().click(function(event) {
    event.preventDefault();
    $(this).button('disable').button('option', 'label', 'Cargando...');
    cargarPrincipal(productos_path);
  });
});

function fn_paginar(d,u) {$('.'+d).load(u);}
</script>
<div class="btn-toolbar pull-right">
  <?php if (isset($_SESSION['permisos'][PRODUCTOS_CREAR])) { ?>
    <button id="crear" class="btn btn-info"><i class="icon-plus"></i> Crear Producto</button>
  <?php } ?>
  <?php if (isset($_SESSION['permisos'][PRODUCTOS_EXPORTAR])) { ?>
    <button id="exportar" class="btn"><i class="icon-file-alt"></i> Exportar</button>
  <?php } ?>
</div>
<h2>Productos (Ministerio de Transporte)</h2>
<form class="form-inline" action="#" id="BuscarProductos" name="BuscarProductos" method="post">
  <input type="hidden" id="pagina" name="pagina" value="<?= $paging->numEstaPagina() ?>" />
  <table>
    <tr>
      <td>Buscar por:</td>
      <td>
        <select class="input-small" name="opcion" id="opcion">
          <option value="nombre" <?php if (isset($_GET['opcion']) and $_GET['opcion']=='nombre') echo 'selected="selected"'?>>Nombre</option>
          <option value="id" <?php if (isset($_GET['opcion']) and $_GET['opcion']=='id') echo 'selected="selected"'?>>Código</option>
        </select>
      </td>
      <td><input class="input-medium" type="text" id="termino" name="termino" value="<?php if (isset($_GET['termino'])) echo $_GET['termino'];?>" /></td>
      <td><button id="buscar">Buscar</button></td>
      <?php if (isset($_GET['termino'])) { echo '<td><button id="todos">Todos</button></td>';}?>
    </tr>
  </table>
</form>
<table class="table table-hover table-condensed table-bordered">
  <thead>
    <tr>
      <th>Código</th>
      <th>Nombre</th>
      <th style="width: 110px">Acción</th>
    </tr>
  </thead>
  <tbody>
  <?php
  if ($paging->numTotalRegistros() == 0) {
    echo '<tr class="warning"><td colspan="3" class="expand">No se encontraron productos...</td></tr>';
  } else {
    while ($producto =  $paging->fetchResultado('Producto')) {
      $c = $producto->activo == 'no' ? 'error' : '';
      echo '<tr class="'.$c.'" id="'.$producto->id.'">';
      echo '<td>'.$producto->id.'</td>';
      echo '<td>'.$producto->nombre.'</td>';
      $name='id='.$producto->id.'&'.nonce_create_query_string($producto->id);
      echo '<td><div class="btn-group">';
      if (isset($_SESSION['permisos'][PRODUCTOS_VER])) {
        echo '<a class="btn ver" href="#" title="Ver" name="'.$name.'"><i class="icon-search"></i></a>';
      }
      if (isset($_SESSION['permisos'][PRODUCTOS_EDITAR]) and $producto->activo == 'si') {
        echo '<a class="btn editar" href="#" title="Editar" name="'.$name.'"><i class="icon-pencil"></i></a>';
      }
      if (isset($_SESSION['permisos'][PRODUCTOS_ANULAR]) and $producto->activo == 'si') {
        echo '<a class="btn anular btn-danger" title="Anular" href="#" name="'.$name.'"><i class="icon-ban-circle"></i></a>';
      } else {
        echo '<a class="btn deshacer btn-success" href="#" title="Activar" name="'.$name.'"><i class="icon-ok"></i></a>';
      }
      echo '</div></td>';
      echo '</tr>';
    }
  }
  ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
