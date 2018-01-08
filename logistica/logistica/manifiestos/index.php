<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PLANILLAS_ENTRAR]) ) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$manifiestos = Manifiesto::search($_GET, 'sql');
$paging = new PHPPaging('right_content', $manifiestos);
$paging->ejecutar();
?>
<div class="row-fluid">
  <span class="span12">
    <div class="btn-toolbar pull-right">
      <?php if (isset($_SESSION['permisos'][PLANILLAS_CREAR])) { ?>
        <button id="manifiestos__new" class="btn btn-info" title="Crear un nuevo Manifiesto"><i class="icon-plus"></i> Crear Manifiesto</button>
      <?php }
      if (isset($_SESSION['permisos'][PLANILLAS_CREAR_INFORME])) { ?>
        <button id="manifiestos__report" class="btn" title="Crear un informe de Manifiestos"><i class="icon-file-alt"></i> Informe</button>
      <?php } ?>
    </div>
    <h2>Manifiestos</h2>
    <form id="manifiestos__search_form" action="#" method="post">
      <?php if ($paging->numEstaPagina() > 1) { ?>
      <input type="hidden" id="pagina" name="pagina" value="<?= $paging->numEstaPagina() ?>" />
      <?php } ?>
      <table cellpadding="0">
        <tr>
          <td><label for="manifiestos__search_form__id">Número:</label></td>
          <td><input class="input-small" type="text" name="id" id="manifiestos__search_form__id" value="<?php if (isset($_REQUEST['id'])) echo $_REQUEST['id']?>" /></td>
          <td rowspan="5">
            <button id="manifiestos__search_form__search">Buscar</button>
            <?php if (isset($_REQUEST['buscar'])) {echo '<button type="button" id="manifiestos__search_form__show_all">Todas</button>';}?>
          </td>
        </tr>
        <tr>
          <td><label for="manifiestos__search_form__placa">Placa:</label></td>
          <td><input class="input-small" type="text" id="manifiestos__search_form__placa" name="placa" value="<?php if (isset($_REQUEST['placa'])) echo $_REQUEST['placa']?>" /></td>
        </tr>
        <tr>
          <td><label for="manifiestos__search_form__conductor">Conductor:</label></td>
          <td><input class="input-small" type="text" id="manifiestos__search_form__conductor" name="conductor" value="<?php if (isset($_REQUEST['conductor'])) echo $_REQUEST['conductor']?>" /></td>
        </tr>
        <tr>
          <td><label for="manifiestos__search_form__ciudad_destino">Ciudad destino:</label></td>
          <td><input class="input-small" type="text" id="manifiestos__search_form__ciudad_destino" name="ciudad_destino" value="<?php if (isset($_REQUEST['ciudad_destino'])) echo $_REQUEST['ciudad_destino']?>" /></td>
        </tr>
        <tr>
          <td><label for="manifiestos__search_form__fecha_inicio">Fecha:</label></td>
          <td>
            <input class="input-small fecha" type="text" id="manifiestos__search_form__fecha_inicio" name="fecha_inicio" value="<?php if (isset($_REQUEST['fecha_inicio'])) echo $_REQUEST['fecha_inicio']?>" />
            -
            <input type="text" class="input-small" name="fecha_fin" id="manifiestos__search_form__fecha_fin" value="<?php if (isset($_REQUEST['fecha_fin'])) echo $_REQUEST['fecha_fin'] ?>" />
          </td>
        </tr>
      </table>
    </form>
    <table id="manifiestos__list" class="table table-hover table-condensed table-bordered">
      <thead>
        <tr>
          <th>No.</th>
          <th>Placa</th>
          <th>Conductor</th>
          <th>Destino</th>
          <th>Fecha</th>
          <th style="width:110px">Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($paging->numTotalRegistros() == 0) {
          echo '<tr class="warning"><td colspan="10" class="expand">No se encontraron manifiestos...</td></tr>';
        } else {
          while ($m = $paging->fetchResultado('Manifiesto')) {
            $s = '';
            if (! $m->activo()) {
              $s = 'error';
            } elseif (count($m->guias('pendientes')) > 0) {
              $s = 'warning';
            }
            echo '<tr class="'.$s.'">';
            echo '<td>'.$m->id.'</td>';
            echo '<td align="center">'.$m->placacamion.'</td>';
            echo '<td>'.$m->conductor_nombre_completo.'</td>';
            echo '<td>'.$m->ciudad_destino_nombre.'</td>';
            echo '<td align="center" title="'.htmlentities(strftime("%A, %d de %B de %Y", strtotime($m->fecha))).'">'.strftime("%b %d, %Y", strtotime($m->fecha)).'</td>';
            $id = $m->to_param();
            echo '<td><div class="btn-group text-center">';
            if (isset($_SESSION['permisos'][PLANILLAS_VER])) {
              echo '<button class="btn manifiesto-ver" name="'.$id.'" title="Ver"><i class="icon-search"></i></button>';
            }
            if (isset($_SESSION['permisos'][PLANILLAS_EDITAR]) and $m->activo()) {
              echo '<button class="btn manifiesto-editar" name="'.$id.'" title="Editar"><i class="icon-pencil"></i></button>';
            }
            if (isset($_SESSION['permisos'][PLANILLAS_IMPRIMIR]) and $m->activo()) {
              echo '<a class="btn imprimir" href="logistica/manifiestos/imprimir2?'.$id.'" name="'.$id.'" target="_blank" title="Imprimir"><i class="icon-print"></i></a>';
            }
            if (isset($_SESSION['permisos'][PLANILLAS_ANULAR]) and $m->activo()) {
              echo '<button class="btn manifiesto-anular btn-danger" name="'.$id.'" title="Anular"><i class="icon-trash"></i></button>';
            } elseif (isset($_SESSION['permisos'][PLANILLAS_DESHACER]) and !$m->activo()) {
              echo '<button class="btn manifiesto-deshacer btn-success" name="'.$id.'" title="Activar"><i class="icon-ok"></i></button>';
            } else {
              echo '</td>';
            }
          }
        }
        ?>
      </tbody>
    </table>
    <?= $paging->fetchNavegacion()?>
  </span>
</div>
<script>
(function() {
  $("#manifiestos__new, #manifiestos__report").click(function() {
    cargarExtra(manifiestos_path + this.id.replace('manifiestos__', '') + ".php");
  });
  $('#manifiestos__list')
    .on('click', 'button.manifiesto-editar', function() {
      cargarPrincipal(manifiestos_path+'edit.php?'+this.name);
    })
    .on('click', 'button.manifiesto-ver', function() {
      cargarExtra(manifiestos_path+'show.php?'+this.name);
    })
    .on('click', 'button.manifiesto-anular', function() {
      LOGISTICA.Dialog.open('Anular Manifiesto',manifiestos_path+'anular.php?'+this.name);
    })
    .on('click', 'button.manifiesto-deshacer', function() {
      var msg = confirm("¿Desea volver a activar este manifiesto?");
      if (! msg) return;
      $.ajax({
        url: manifiestos_path+'deshacer.php?'+this.name,
        success: function(msj) {
          if (msj == "ok") {
            cargarPrincipal(manifiestos_path+'index.php?'+$('#manifiestos__search_form').serialize());
          }else if (msj == "id") {
            alert("Algo ha salido mal, recarga la página.");
          }else if (msj == "permiso") {
            alert("No tienes permisos para activar planillas.");
          } else {
            alert("Ha ocurrido un error al eliminar, intentalo nuevamente.");
          }
        }
      });
    });
  var $searchBtn = $('#manifiestos__search_form__search').button({icons: {primary: "ui-icon-search"}});
  $('#manifiestos__search_form').submit(function(e) {
    e.preventDefault();
    $searchBtn.button('disable').button('option','label','Buscando...');
    cargarPrincipal(manifiestos_path+'index.php?'+$(this).serialize());
  });
  $('#manifiestos__search_form__show_all').button().click(function(e) {
    e.preventDefault();
    cargarPrincipal(manifiestos_path);
  });

  var dates = $("#manifiestos__search_form__fecha_inicio, #manifiestos__search_form__fecha_fin").datepicker({
    changeMonth: true,
    changeYear: true,
    numberOfMonths: 2,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    onSelect: function(selectedDate) {
      var option = this.id == "manifiestos__search_form__fecha_inicio" ? "minDate" : "maxDate";
      dates.not(this).datepicker("option", option, selectedDate);
    }
  });
})();
function fn_paginar(d,u) { $("."+d).load(u); }
</script>
