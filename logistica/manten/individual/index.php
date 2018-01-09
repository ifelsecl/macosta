<?php
require "../../seguridad.php";
$vehiculo = Vehiculo::find('FCD241');
$mantenimientos = $vehiculo->_mantenimientos();
$history = $vehiculo->history();
?>
<div id="individual_container">
        <h2>Informe Individual</h2>
        <h3>Datos del vehiculo</h3>
        <form class="form-inline">
            <table>
            <tr>
                <td><label for="individual__search_form__cliente">Placa</label></td>
                <td><label for="individual__search_form__fecha_emision">Fecha Inicio</label></td>
                <td><label for="individual__search_form__fecha_fin">Fecha Fin</label></td>
            </tr>
            <tr>
                <td><input type="text" name="placa" id="individual__search_form" class="input-small" value=""></td>
                <td><input type="text" readonly name="fecha_inicio" id="individual__search_form__fecha_inicio" class="input-small" value=""></td>
                <td><input type="text" readonly name="fecha_fin" id="individual__search_form__fecha_fin" class="input-small" value=""></td>
                <td><button class="btn btn-info">Buscar</button></td>
            </tr>
            </table>
        </form>


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

        <div class="tabbable">
            <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab_soat">SOAT</a></li>
            <li><a data-toggle="tab" href="#tab_seguro">Seguro</a></li>
            <li><a data-toggle="tab" href="#tab_top">Tarjeta de Operacion</a></li>
            <li><a data-toggle="tab" href="#tab_rtm">Revisión Téc. Mec.</a></li>
            </ul>
            <div class="tab-content">
                    <div class="tab-pane active" id="tab_soat">
                        <div class="row-fluid">
                            <fieldset class="span6 table">
                                <table class="table table-condensed">
                                <tr>
                                    <td>Número:</td>
                                    <td><?= $vehiculo->soat ?></td>
                                </tr>
                                <tr>
                                    <td>Fecha vencimiento:</td>
                                    <td>
                                    <?= strftime('%B %d, %Y',strtotime($vehiculo->f_venc_soat)) ?>
                                    </td>
                                </tr>
                                </table>
                            </fieldset><!-- SOAT -->
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_seguro">
                        <div class="row-fluid">
                            <fieldset class="span6 table">
                                <table class="table">
                                <tr>
                                    <td>Número:</td>
                                    <td><?= $vehiculo->num_seguro ?></td>
                                </tr>
                                <tr>
                                    <td>Fecha vencimiento:</td>
                                    <td><?= strftime('%B %d, %Y',strtotime($vehiculo->f_venc_seguro)) ?></td>
                                </tr>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_top">
                        <div class="row-fluid">
                            <fieldset class="span6 table">
                                <table class="table">
                                <tr>
                                    <td>Número:</td>
                                    <td><?= $vehiculo->t_operacion ?></td>
                                </tr>
                                <tr>
                                    <td>Fecha afiliacón:</td>
                                    <td><?= strftime('%B %d, %Y', strtotime($vehiculo->fecha_afiliacion)) ?></td>
                                </tr>
                                <tr>
                                    <td>Fecha vencimiento:</td>
                                    <td><?= strftime('%B %d, %Y', strtotime($vehiculo->f_venc_toperacion)) ?></td>
                                </tr>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab_rtm">
                        <div class="row-fluid">
                            <fieldset class="span6 table">
                                <table class="table">
                                <tr>
                                    <td>Número:</td>
                                    <td><?= $vehiculo->tecnico_meca ?></td>
                                </tr>
                                <tr>
                                    <td>Fecha vencimiento:</td>
                                    <td>
                                    <?= strftime('%B %d, %Y',strtotime($vehiculo->f_venc_tmec)) ?>
                                    </td>
                                </tr>
                                </table>
                            </fieldset>
                        </div>
                    </div>
        </div>
        <h3>Mantenimientos</h3>
        <table id="vehiculo-mantenimientos" class="table table-hover table-condensed table-bordered">
          <thead>
            <tr>
              <th>Mantenimiento (Trabajo)</th>
              <th>Fecha</th>
              <th>KM</th>
              <th>Tipo</th>
              <th>Precio</th>
              <th>Factura</th>
              <th>Observación</th>
              <th>Adjunto</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($mantenimientos as $m) {
              echo "<tr><td>".$m->mantenimiento_nombre."</td>";
              echo "<td>".$m->fecha."</td>";
              echo "<td>".$m->mantenimiento_kilometraje."</td>";
              echo "<td>".$m->tipo."</td>";
              echo "<td>".$m->precio."</td>";
              echo "<td></td>";
              echo "<td>".$m->observacion."</td>";
              echo "<td><a href='#'>ADJUNTO</a></td></tr>";
            }
            ?>
          </tbody>
        </table>

        
</div>
<script>
(function(){
    var $el = $('#individual_container');
    var $searchBtn = $el.find('form button');


    var loadData = function(e) {
      e.preventDefault();
      //cargarPrincipal(individual_path + '?' + $(this).serialize());
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

    $el.find('#individual__search_form__fecha_inicio').datepicker(datepickerAttributes);
    $el.find('#individual__search_form__fecha_fin').datepicker(datepickerAttributes);

})();
</script>

