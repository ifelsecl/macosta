<?php
require "../seguridad.php";
if (! isset($_SESSION['permisos'][FACTURACION_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

$facturas = Factura::search($_GET, 'sql');
$paging = new PHPPaging('center_content', $facturas);
$paging->ejecutar();
?>
<div class="span2 left_content">
  <ul id="menu" class="sidebarmenu">
    <li><a id="facturacion" class="actual" title="Facturación" href="#Facturacion">Facturación</a></li>
    <?php
    if (isset($_SESSION['permisos'][FACTURACION_CREAR_RELACION])) {
      echo '<li><a id="relaciones" title="Relaciones" href="#Facturacion/Relaciones">Relaciones</a></li>';
    }
    ?>
    <li><a id="notas_credito" title="Notas Credito" href="#Facturacion/NotasCredito">Notas Credito</a></li>
    <li><a id="cartera" title="Cartera" href="#Facturacion/Cartera">Cartera</a></li>
  </ul>
  <div class="sidebar_box visible-desktop">
    <div class="sidebar_box_top">
      <img src="css/images/info.png" alt="Info" class="pull-right" />
      <h4>User Help Desk</h4>
    </div>
    <div class="sidebar_box_content">
      <p>Gestione la facturación de su empresa, administre las notas credito y genere informes.</p>
    </div>
  </div>
</div>
<div class="span10 right_content">
  <div class="pull-right btn-toolbar">
    <?php
    if (isset($current_user->permisos[FACTURACION_FACTURAR])) {
      echo '<button id="facturar" class="btn btn-info"><i class="icon-plus"></i> Facturar</button>';
    }
    if (isset($current_user->permisos[FACTURACION_IMPRIMIR])) {
      echo '<button id="imprimir" class="btn btn-default"><i class="icon-print"></i> Imprimir</button>';
    }
    if (isset($current_user->permisos[FACTURACION_CERRAR])) {
      echo '<button id="cerrar" class="btn btn-default"><i class="icon-lock"></i> Cerrar</button>';
    }
    if (isset($current_user->permisos[FACTURACION_GENERAR_NOTA_CREDITO])) {
      echo '<button id="nc" class="btn btn-default" title="Generar o Imprimir Nota Credito">Nota Credito</button>';
    }
    if (isset($current_user->permisos[FACTURACION_CREAR_INFORMES])) {
      echo '<button id="informe" class="btn btn-default"><i class="icon-file-alt"></i> Informe</button>';
    }
    ?>
    <button id="estadisticas" class="btn btn-default"><i class="icon-bar-chart"></i> Estadisticas</button>
  </div>
  <table>
    <tr>
      <td><h2>Facturación</h2></td>
      <td>
        <img id="cargando" style="display:none;" src="css/ajax-loader.gif" alt="Cargando..." />
      </td>
    </tr>
  </table>
  <form id="FormBuscar" action="#" method="post">
    <?php
    if ($paging->numEstaPagina() > 1) echo '<input type="hidden" id="p" name="pagina" value="'.$paging->numEstaPagina().'" />';
    ?>
    <input type="hidden" name="buscar" value="si" />
    <table class="form-inline">
      <tr>
        <td>Número</td>
        <td>Cliente</td>
        <td>Fecha Emisión</td>
        <td>Estado</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td><input type="text" class="input-small" name="id" value="<?php if (isset($_REQUEST['id'])) echo $_REQUEST['id'];?>" /></td>
        <td><input type="text" class="input-small" name="cliente" value="<?php if (isset($_REQUEST['cliente'])) echo $_REQUEST['cliente'];?>" /></td>
        <td><input class="input-small" id="buscar_fecha_emision" name="fecha_emision" type="text" value="<?php if (isset($_REQUEST['fecha_emision'])) {echo $_GET['fecha_emision'];}?>" /></td>
        <td><input class="input-small" name="estado" type="text" value="<?php if (isset($_REQUEST['estado'])) {echo $_GET['estado'];}?>" /></td>
        <td><button id="buscar">Buscar</button></td>
        <td><button id="actualizar">Actualizar</button></td>
      </tr>
    </table>
  </form>
  <table id="facturas-list" class="table table-bordered table-hover table-condensed">
    <thead>
      <tr>
        <th>Número</th>
        <th>Cliente</th>
        <th>Emisión</th>
        <th>Vencimiento</th>
        <th>Tipo</th>
        <th>Estado</th>
        <th style="width: 120px">Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($paging->numTotalRegistros() == 0) {
        echo '<tr class="warning"><td colspan="7" class="expand">No se encontraron facturas...</td></tr>';
      } else {
        while ($factura = $paging->fetchResultado('Factura')) {
          require '_factura.php';
        }
      }
      ?>
    </tbody>
  </table>
  <?= $paging->fetchNavegacion() ?>
</div><!-- end of right content-->
<div id="extra_content" class="span10" style="display:none"></div><!-- end of extra content-->
<script>
(function() {
  $('#buscar').button({icons: {primary: "ui-icon-search"}});
  $("#buscar_fecha_emision").datepicker({
    changeMonth: true,
    changeYear: true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    autoSize: true,
    maxDate: 0
  });
  $('#FormBuscar').submit(function(e) {
    e.preventDefault();
    $('#buscar').button('disable').button('option','label','Buscando...');
    $('#p').val(1);
    $(".center_content").load(facturacion_path+"?"+$(this).serialize());
  });
  $('#todos').button().click(function(e) {
    e.preventDefault();
    $(this).button('disable').button('option','label','Cargando...');
    $(".center_content").load(facturacion_path);
  });
  $('table#facturas-list')
    .on('click', '.editar', function(e) {
      e.preventDefault();
      LOGISTICA.Content.loadExtra(facturacion_path+"editar.php?"+this.name);
    })
    .on('click', '.eliminar', function(e) {
      e.preventDefault();
      LOGISTICA.Dialog.open('Eliminar Factura',facturacion_path+'eliminar.php?'+this.name);
    })
    .on('click', '.ver', function(e) {
      e.preventDefault();
      LOGISTICA.Content.loadExtra(facturacion_path+"ver.php?"+this.name);
    })
    .on('click', '.pagar', function(e) {
      e.preventDefault();
      LOGISTICA.Content.loadExtra(facturacion_path+"pagar.php?"+this.name);
    });
  $('#imprimir').click(function() {
    LOGISTICA.Dialog.open('Imprimir Facturas',facturacion_path+'imprimir_varias.php');
  });
  $('#nc, #informe, #facturar, #estadisticas').click(function() {
    LOGISTICA.Content.loadExtra(facturacion_path+this.id+'.php');
  });
  $('#cerrar').click(function() {
    var c = confirm('¿Cerrar todas las facturas abiertas?');
    if (!c) return;
    var html='<p class="expand"><img src="css/ajax-loader.gif" /> Cerrando facturas abiertas</p>';
    LOGISTICA.Dialog.open('Cerrando Facturas',html,true);
    $.ajax({
      url: facturacion_path+'ajax.php',
      data: 'c_f=121', type: 'POST',
      success: function (r) {
        if (r=='ok') {
          $(".center_content").load(facturacion_path, function() {
            LOGISTICA.Dialog.close();
          });
        } else {
          LOGISTICA.Dialog.open('Error',r,true);
        }
      }
    });
  });
  $('#actualizar')
    .button({icons: {primary:'ui-icon-refresh'}, text: false})
    .click(function(e) {
      e.preventDefault();
      $(this).button('disable');
      $(".center_content").load(facturacion_path+"?"+$('#FormBuscar').serialize());
    });
})();
function fn_paginar(d, u) {
  $('#cargando').show();
  $('.'+d).load(u);
}
</script>
