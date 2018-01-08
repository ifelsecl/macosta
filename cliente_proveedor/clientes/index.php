<?php
require "../../seguridad.php";

if (! isset($_SESSION['permisos'][CLIENTES_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

if (isset($_GET['nombre'])) {
  $clientes = Cliente::search($_GET, 'sql');
} else {
  $type = isset($_SESSION['permisos'][CLIENTES_DESHACER]) ? 'todos' : 'activos';
  $clientes = Cliente::all($type, 'sql');
}

$paging = new PHPPaging('right_content', $clientes);
$paging->ejecutar();
?>
<div class="pull-right btn-toolbar">
  <?php
  if (isset($_SESSION['permisos'][CLIENTES_CREAR])) { ?>
    <button id="crear" class="btn btn-info"><i class="icon-plus"></i> Crear Cliente</button>
  <?php }
  if (isset($_SESSION['permisos'][CLIENTES_IMPORTAR])) { ?>
    <button id="importar" class="btn"><i class="icon-upload"></i> Importar</button>
  <?php }
  if (isset($_SESSION['permisos'][CLIENTES_EXPORTAR])) { ?>
    <button id="exportar" class="btn"><i class="icon-download"></i> Exportar</button>
  <?php } ?>
</div>
<h2>Clientes</h2>
<form id="BuscarClientes" class="form-inline" name="BuscarClientes" method="post" action="#">
  <input type="hidden" id="pagina" name="page" value="<?= $paging->numEstaPagina() ?>" />
  <table>
    <tr>
      <td>ID</td>
      <td>Nombre</td>
      <td>No Identificación</td>
      <td>Dirección</td>
      <td>Ciudad</td>
      <td></td>
    </tr>
    <tr>
      <td><input class="input-small" type="text" name="id" value="<?= (isset($_REQUEST['id'])) ? $_REQUEST['id'] : '' ?>"></td>
      <td><input class="input-small" type="text" name="nombre" value="<?= (isset($_REQUEST['nombre'])) ? $_REQUEST['nombre'] : '' ?>"></td>
      <td><input class="input-small" type="text" name="numero_identificacion" value="<?= (isset($_REQUEST['numero_identificacion'])) ? $_REQUEST['numero_identificacion'] : '' ?>"></td>
      <td><input class="input-small" type="text" name="direccion" value="<?= (isset($_REQUEST['direccion'])) ? $_REQUEST['direccion'] : '' ?>"></td>
      <td><input class="input-small" type="text" name="ciudad" value="<?= (isset($_REQUEST['ciudad'])) ? $_REQUEST['ciudad'] : '' ?>"></td>
      <td>
        <button type="submit" id="buscar">Buscar</button>
        <?php if (isset($_GET['termino'])) echo '<td><button type="button" id="todos">Todas</button></td>' ?>
      </td>
    </tr>
  </table>
</form>
<?= $paging->fetchNavegacion() ?>
<table id="clientes-list" class="table table-hover table-bordered table-condensed">
  <thead>
    <tr>
      <th>ID</th>
      <th>Número</th>
      <th>Nombre</th>
      <th>Ciudad</th>
      <th>Dirección</th>
      <th>Credito</th>
      <th>Acción</th>
    </tr>
  </thead>
  <tbody>
  <?php
  if ($paging->numTotalRegistros() == 0) {
    echo '<tr class="warning"><td colspan="7" class="expand">No se encontraron clientes...</td></tr>';
  } else {
    while ($cliente = $paging->fetchResultado('Cliente')) {
      $c = $cliente->activo == 'no' ? 'error' : '';
      echo '<tr class="'.$c.'"">';
      echo '<td>'.$cliente->id.'</td>';
      echo '<td>'.$cliente->numero_identificacion_completo.'</td>';
      if (strlen($cliente->nombre_completo) > 30) {
        echo '<td><abbr title="'.$cliente->nombre_completo.'">'.substr($cliente->nombre_completo, 0, 27).'...</abbr></td>';
      } else {
        echo '<td>'.$cliente->nombre_completo.'</td>';
      }
      echo '<td>'.$cliente->ciudad_nombre.'</td>';
      echo '<td>'.$cliente->direccion.'</td>';
      echo '<td align="center">'.$cliente->credito.'</td>';
      $name="id=".$cliente->id."&".nonce_create_query_string($cliente->id);
      echo '<td><div class="btn-group">';
      if (isset($_SESSION['permisos'][CLIENTES_VER])) {
        echo '<button title="Ver" class="btn ver" name="'.$name.'"><i class="icon-search"></i></button>';
      }
      if (isset($_SESSION['permisos'][CLIENTES_EDITAR]) and $cliente->activo == 'si') {
        echo '<button title="Editar" class="btn editar" name="'.$name.'"><i class="icon-pencil"></i></button>';
      }
      if (isset($_SESSION['permisos'][CLIENTES_CAMBIAR_CLAVE]) and $cliente->activo == 'si') {
        echo '<button title="Cambiar Contraseña" class="btn cambiar_clave" name="'.$name.'"><i class="icon-key"></i></button>';
      }
      if (isset($_SESSION['permisos'][CLIENTES_IMPERSONAR]) and $cliente->activo == 'si') {
        echo '<a href="cliente_proveedor/clientes/impersonar?'.$name.'" target="_blank" title="Impersonar" class="btn" name="'.$name.'"><i class="icon-user"></i></a>';
      }
      if (isset($_SESSION['permisos'][LISTA_PRECIOS_ENTRAR]) and $cliente->activo == 'si') {
        echo '<button title="Lista de Precios" class="btn lista_precios" name="'.$name.'"><i class="icon-list-alt"></i></button>';
      }
      if ((isset($_SESSION['permisos'][CLIENTES_ANULAR]) or isset($_SESSION['permisos'][CLIENTES_ELIMINAR])) and $cliente->activo == 'si') {
        echo '<button title="Anular/Eliminar" class="btn anular btn-danger" name="'.$name.'"><i class="icon-trash"></i></button>';
      } elseif (isset($_SESSION['permisos'][CLIENTES_DESHACER]) and $cliente->activo=='no') {
        echo '<button title="Activar" class="btn deshacer btn-success" name="'.$name.'"><i class="icon-upload"></i></button>';
      }
      echo '</div></td>';
      echo '</tr>';
    }
  }
  ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
<script>
(function() {
  $("#importar").click(function() {
    cargarExtra(clientes_path+"importar.php");
  });
  $("#crear").click(function() {
    cargarExtra(clientes_path+"crear.php");
  });
  $('#clientes-list')
    .on('click', 'button.ver', function() {
      cargarExtra(clientes_path+'ver.php?'+this.name);
    })
    .on('click', 'button.editar', function() {
      cargarExtra(clientes_path+'editar.php?'+this.name);
    })
    .on('click', 'button.cambiar_clave', function() {
      cargarExtra(clientes_path+'clave.php?'+this.name);
    })
    .on('click', 'button.anular', function() {
      LOGISTICA.Dialog.open('Anular/Eliminar Cliente', clientes_path+'anular_eliminar.php?'+this.name);
    })
    .on('click', 'button.deshacer', function() {
      $.ajax({
        url: clientes_path+'deshacer.php',
        type: "POST",
        data: this.name,
        success: function(msj) {
          if (msj == "ok") {
            cargarPrincipal(clientes_path+"index.php?"+$('#BuscarClientes').serialize());
          } else {
            LOGISTICA.Dialog.open('Error', msj, true);
          }
        }
      });
    })
    .on('click', 'button.lista_precios', function() {
      cargarPrincipal(clientes_path+'listaprecios/?'+this.name);
    })
  $("#exportar").click(function() {
    cargarExtra(clientes_path+"exportar.php");
  });
  $('#todos').button().click(function(e) {
    e.preventDefault();
    $(this).button('option', 'label', 'Cargando...').button('disable');
    cargarPrincipal(clientes_path);
  });
  $('#buscar').button({icons: {primary: 'ui-icon-search'}});
  $('#BuscarClientes').submit(function(e) {
    e.preventDefault();
    $('#buscar').button('option', 'label', 'Buscando...').button('disable');
    $('#pagina').val(1);
    cargarPrincipal(clientes_path+'?'+$(this).serialize());
  });
})();
function fn_paginar(d, u) { $('.'+d).load(u); }
</script>
