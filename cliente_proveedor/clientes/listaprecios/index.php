<?php
require "../../../seguridad.php";
if (! isset($_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
if (! isset($_SESSION['permisos'][LISTA_PRECIOS_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

$cliente = new Cliente;
if (! $cliente->find($_GET['id'])) exit('No existe el cliente.');
if (isset($_GET['buscar'])) {
  $lista_precios = $cliente->lista_precios('sql', $_GET['ciudad_origen'], $_GET['ciudad_destino']);
} else {
  $lista_precios = $cliente->lista_precios('sql');
}

$paging = new PHPPaging('right_content', $lista_precios);
$paging->ejecutar();
?>
<div class="row-fluid">
  <div class="span12">
    <div class="btn-toolbar pull-right">
      <?php
      if (isset($_SESSION['permisos'][LISTA_PRECIOS_AGREGAR])) {
        echo '<button id="lista_precios__agregar" class="btn btn-info"><i class="icon-plus"></i> Agregar</button>';
      }
      if (isset($_SESSION['permisos'][LISTA_PRECIOS_MODIFICAR])) {
        echo '<button id="modificar" class="btn" title="Aumentar/Disminuir lista de precios"><i class="icon-cog"></i> Modificar</button>';
      }
      if (isset($_SESSION['permisos'][LISTA_PRECIOS_IMPORTAR])) {
        echo '<button id="importar" class="btn"><i class="icon-upload"></i> Importar</button>';
      }
      if (isset($_SESSION['permisos'][LISTA_PRECIOS_EXPORTAR])) {
        echo '<button id="exportar" class="btn"><i class="icon-download"></i> Exportar</button>';
      }
      ?>
      <button id="regresar" class="btn btn-success">Regresar</button>
    </div>
  </div>
</div>
<h3><?= $cliente->nombre_completo ?> | Lista de Precios</h3>
<form id="lista_precios__search_form" method="post" action="#">
  <input type="hidden" id="lista_precio__id_cliente" name="id" value="<?= $cliente->id ?>" />
  <input type="hidden" name="buscar" value="si" />
  <?php
  if ($paging->numEstaPagina()>1) {
    echo '<input type="hidden" name="pagina" id="p" value="'.$paging->numEstaPagina().'" />';
  }
  ?>
  <table class="form-inline">
    <tr>
      <td><label for="lista_precios__ciudad_origen">Ciudad Origen:</label></td>
      <td><label for="lista_precios__ciudad_destino">Ciudad Destino:</label></td>
    </tr>
    <tr>
      <td><input type="text" class="input-medium" id="lista_precios__ciudad_origen" name="ciudad_origen" value="<?= $cliente->ciudad()->nombre ?>" /></td>
      <td><input type="text" class="input-medium" id="lista_precios__ciudad_destino" name="ciudad_destino" value="<?php if (isset($_GET['ciudad_destino'])) echo $_GET['ciudad_destino'] ?>" /></td>
      <td><button id="buscar">Buscar</button></td>
      <td><button id="lista_precios__reload">Actualizar</button></td>
    </tr>
  </table>
</form>
<?php if (isset($_SESSION['permisos'][LISTA_PRECIOS_ELIMINAR])) { ?>
<a title="Borrar toda la lista de precios" href="#" id="lista_precio__delete_all" class="pull-right btn btn-link">Eliminar todo</a>
<?php } ?>
<table id="lista_precios__list" class="table table-hover table-bordered table-condensed">
  <thead>
    <tr>
      <th>Origen</th>
      <th>Destino</th>
      <th>Seguro (%)</th>
      <th>Embalaje</th>
      <th>Precio</th>
      <th style="width:80px">Acción</th>
    </tr>
  </thead>
  <tbody>
  <?php
  if ($paging->tieneRegistros()) {
    while ($fila =  $paging->fetchResultado()) {
      echo '<tr>';
      echo  '<td title="'.$fila['departamento_origen'].'">'.$fila['ciudadorigen'].'</td>';
      echo  '<td title="'.$fila['departamento_destino'].'">'.$fila['ciudaddestino'].'</td>';
      echo  '<td align="right">'.$fila['seguro'].'%</td>';
      echo  '<td>'.$fila['embalaje'].'</td>';
      echo  '<td align="right">'.number_format($fila['precio'],2).'</td>';
      $name = 'idcliente='.$fila['idcliente'].'&idciudadorigen='.$fila['idciudadorigen'].'&idciudaddestino='.$fila['idciudaddestino'].'&idembalaje='.$fila['idembalaje'].'&precio='.$fila['precio'];
      echo '<td><div class="btn-group">';
      if (isset($_SESSION['permisos'][LISTA_PRECIOS_EDITAR])) {
        echo '<a class="btn editar" href="#" name="'.$name.'"><i class="icon-pencil"></i></a>';
      }
      if (isset($_SESSION['permisos'][LISTA_PRECIOS_ELIMINAR])) {
        echo '<a class="btn anular btn-danger" href="#" name="'.$name.'"><i class="icon-trash"></i></a>';
      }
      echo '</div></td></tr>';
    }
  } else {
    echo '<tr><td colspan="6" class="expand"><label class="ListaPrecios">No se encontraron precios...</td></tr>';
  }
  ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
<script>
(function() {
  var idCliente = $('#lista_precio__id_cliente').val();
  $("#buscar").button({icons: {primary: 'ui-icon-search'}, text: false});
  $("#lista_precios__reload").button({icons: {primary: 'ui-icon-refresh'}, text: false}).click(function(e) {
    e.preventDefault();
    $(this).button('disable');
    cargarPrincipal(lista_precios_path+"index.php?"+$('#lista_precios__search_form').serialize());
  });
  $('#lista_precios__search_form').submit(function(e) {
    e.preventDefault();
    $('#buscar').button('disable').button('option','label','Buscando...');
    cargarPrincipal(lista_precios_path+"index.php?"+$(this).serialize());
  });
  $('#lista_precios__agregar').click(function() {
    LOGISTICA.Dialog.open('Agregar Nuevo Precio', lista_precios_path+'agregar.php?id='+idCliente);
  });

  $("#regresar").click(function() {
    cargarPrincipal(clientes_path);
  });

  $('#lista_precio__delete_all').click(function(event) {
    event.preventDefault();
    var msg = confirm("¿Deseas borrar toda la lista de precios? (esta acción será registrada)");
    if (! msg) return;

    var msg2=confirm("¿Estas seguro? (esto no se podrá deshacer)");
    if (! msg2) return;

    var html='<p class="expand"><img src="css/ajax-loader.gif" />&nbsp;Borrando toda la lista de precios...</p>';
    LOGISTICA.Dialog.open('Borrando Lista de Precios', html, true);
    $.ajax({
      url: clientes_path+'ajax.php',
      type: 'POST',
      data: 'borrar_lista_precios=101&id_cliente='+idCliente,
      success: function(msj) {
        if (msj == 'error') {
          abrirDialog('Error', 'La lista de precios no se pudo borrar, intentalo nuevamente.', true);
        } else if (msj=='ok') {
          var f=function() {
            $('#dialog').dialog('close');
          };
          cargarPrincipal(lista_precios_path+"index.php?id="+idCliente, f);
        }
      }
    });
  });

  $("#modificar").click(function() {
    LOGISTICA.Dialog.open('Modificar Lista de Precios',lista_precios_path+'modificar.php?id='+idCliente);
  });

  $("#exportar, #importar").click(function() {
    cargarExtra(lista_precios_path+this.id+'.php?id='+idCliente);
  });

  $('#lista_precios__list')
  .on('click', '.editar', function(e) {
    e.preventDefault();
    LOGISTICA.Dialog.open('Editar Precio', lista_precios_path+'editar.php?'+this.name);
  })
  .on('click', '.anular', function(e) {
    e.preventDefault();
    var msg = confirm("¿Deseas borrar este precio?");
    if (!msg) return;
    $.ajax({
      url: lista_precios_path+'borrar.php',
      type: "GET", data: this.name,
      success: function(msj) {
        if (msj == 0) {
          alert("Ha ocurrido un error al eliminar... intentalo nuevamente.");
        } else {
          cargarPrincipal(lista_precios_path+"index.php?"+$('#lista_precios__search_form').serialize());
        }
      }
    });
  });
})();
function fn_paginar(d,u) { $("."+d).load(u); }
</script>
