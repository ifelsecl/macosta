<?php
require "../../seguridad.php";
$vehiculo = Vehiculo::find('UYU272');
$mantenimientos = $vehiculo->mantenimientos();
$history = $vehiculo->history();
?>
<div id="individual__show">
  <button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
  <h2>Informe Individual</h2>
  <form class="form-inline">
    <table>
      <tr>
        <td><label for="relaciones__search_form__cliente">Placa</label></td>
        <td><label for="relaciones__search_form__fecha_emision">Fecha Inicio</label></td>
        <td><label for="relaciones__search_form__fecha_emision">Fecha Fin</label></td>
      </tr>
      <tr>
        <td><input type="text" name="placa" id="individual__search_form" class="input-small" value="<?= isset($_GET['cliente']) ? $_GET['cliente'] : '' ?>"></td>
        <td><input type="text" readonly name="fecha_inicio" id="individual__search_form__fecha_inicio" class="input-small" value="<?= isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '' ?>"></td>
        <td><input type="text" readonly name="fecha_fin" id="individual__search_form__fecha_fin" class="input-small" value="<?= isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '' ?>"></td>
        <td><button class="btn btn-info">Buscar</button></td>
      </tr>
    </table>
  </form>
  <div class="tabbable">
    <div class="tab-content">
      <div class="tab-pane active" id="tab_info">
        <table cellspacing="2" cellpadding="4">
          <tr>
            <td><b>Placa Semiremolque</b></td>
            <td><?= $vehiculo->placa_semiremolque ?></td>
            <td><b>Marca</b></td>
            <td colspan="2"><?= $vehiculo->marca_nombre ?></td>
          </tr>
          <tr>
            <td><b>Línea</b></td>
            <td><?= $vehiculo->linea_nombre ?></td>
            <td><b>Color</b></td>
            <td colspan="3"><?= $vehiculo->color_nombre ?></td>
          </tr>
          <tr>
            <td><b>Modelo</b></td>
            <td><?= $vehiculo->modelo ?></td>
            <td><b>Modelo Repotenciado A</b></td>
            <td><?= $vehiculo->modelo_repotenciado ?></td>
          </tr>
          <tr>
            <td><b>Número de la serie</b></td>
            <td><?= $vehiculo->serie ?></td>
            <td><b>Carrocería</b></td>
            <td><?= $vehiculo->carroceria_nombre ?></td>
          </tr>
          <tr>
            <td><b>Configuración</b></td>
            <td><?= $vehiculo->configuracion_nombre ?></td>
            <td><b>Peso</b></td>
            <td><?= number_format($vehiculo->peso)?> kg</td>
          </tr>
          <tr>
            <td><b>Registro de carga</b></td>
            <td><?= $vehiculo->registro ?></td>
            <td><b>Capacidad de carga</b></td>
            <td><?= number_format($vehiculo->capacidadcarga)?> kg</td>
          </tr>
          <tr>
            <td><b>Aseguradora</b></td>
            <td colspan="3">
              <?= $vehiculo->nitaseguradora." - ".$vehiculo->aseguradora_nombre ?>
            </td>
          </tr>
          <tr>
            <td><b>Propietario</b></td>
            <td colspan="3">
              <?= $vehiculo->propietario()->nombre_completo ?>
            </td>
          </tr>
          <tr>
            <td><b>Tenedor</b></td>
            <td colspan="3"><?= $vehiculo->tenedor()->nombre_completo ?></td>
          </tr>
          <tr>
            <td><b>Kilometraje Inicial</b></td>
            <td><?= number_format($vehiculo->km_inicial)?></td>
            <td><b>Kilometraje Actual</b></td>
            <td><?= number_format($vehiculo->km_actual)?></td>
          </tr>
          <tr>
            <td><b>Fecha Matricula</b></td>
            <td><?= $vehiculo->fecha_matricula ?></td>
          </tr>
        </table>
      </div>
      </div>
    </div>
  </div>
</div>
<script>
(function(){
  LOGISTICA.mantenimientos.individual = function() {
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

  LOGISTICA.mantenimientos.individual.init();
})();
</script>

