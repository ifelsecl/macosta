<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][FACTURACION_CREAR_RELACION])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

$relaciones = Relacion::search($_GET, true);
$paging = new PHPPaging('right_content', $relaciones, true);
$paging->ejecutar();
?>
<div id="relaciones_container">
  <div class="btn-toolbar pull-right">
    <button class="btn btn-info" id="relaciones__new"><i class="icon-plus"></i> Crear Relación</button>
  </div>
  <h2>Relaciones</h2>
  <form class="form-inline">
    <table>
      <tr>
        <td><label for="relaciones__search_form__cliente">Cliente</label></td>
        <td><label for="relaciones__search_form__fecha_emision">Fecha Emisión</label></td>
      </tr>
      <tr>
        <td><input type="text" name="cliente" id="relaciones__search_form__cliente" class="input-small" value="<?= isset($_GET['cliente']) ? $_GET['cliente'] : '' ?>"></td>
        <td><input type="text" readonly name="fecha_emision" id="relaciones__search_form__fecha_emision" class="input-small" value="<?= isset($_GET['fecha_emision']) ? $_GET['fecha_emision'] : '' ?>"></td>
        <td><button class="btn btn-info">Buscar</button></td>
      </tr>
    </table>
  </form>
  <table class="table table-bordered table-hover table-condensed">
    <thead>
      <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Emisión</th>
        <th>Periodo</th>
        <th style="width: 60px">Imprimir</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($paging->numTotalRegistros() == 0) {
        echo '<tr class="warning"><td colspan="5" class="expand">No se encontraron relaciones...</td></tr>';
      } else {
        while ($relacion = $paging->fetchResultado('Relacion')) {
          echo '<tr>';
          echo '<td>'.$relacion->id.'</td>';
          echo '<td>'.$relacion->cliente_nombre_completo.'</td>';
          echo '<td align="center">'.$relacion->fecha_emision_corta().'</td>';
          echo '<td align="center">'.$relacion->periodo.'</td>';
          $name = "id=".$relacion->id."&".nonce_create_query_string($relacion->id);
          echo '<td><div class="btn-group">';
          echo '<a href="facturacion/relaciones/imprimir?'.$name.'" target="_blank" class="btn imprimir" title="Imprimir"><i class="icon-print"></i></a>';
          echo '</div></td>';
          echo '</tr>';
        }
      }
      ?>
    </tbody>
  </table>
  <?= $paging->fetchNavegacion() ?>
</div>
<script>
(function(){
  LOGISTICA.facturacion.relaciones = function() {
    var $el = $('#relaciones_container');
    var $searchBtn = $el.find('form button');

    var openForm = function() {
      cargarExtra(relaciones_path+'crear.php');
    };

    var loadData = function(e) {
      e.preventDefault();
      $searchBtn.prop('disabled', true).text('Buscando...');
      cargarPrincipal(relaciones_path + '?' + $(this).serialize());
    };

    var datepickerAttributes = {
      changeMonth: true,
      changeYear: true,
      showOn: "both",
      buttonImage: "css/images/calendar.gif",
      buttonImageOnly: true,
      dateFormat: 'yy-mm-dd',
      buttonText: 'Seleccionar...',
      autoSize: true,
      maxDate: 0
    };

    var init = function() {
      $el.find('#relaciones__search_form__fecha_emision').datepicker(datepickerAttributes);
      $el.find('#relaciones__new').click(openForm);
      $el.find('form').submit(loadData);
    };
    return {init: init}
  }();

  LOGISTICA.facturacion.relaciones.init();
})();
function fn_paginar(d, u){ $('.'+d).load(u); }
</script>
