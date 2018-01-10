<?php
require "../../seguridad.php";
$placa = isset($_GET['placa']) ? $_GET['placa'] : 'FCD241';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$vehiculo = Vehiculo::find($placa);
$mantenimientos = $vehiculo->_mantenimientos($fecha_inicio, $fecha_fin);
$history = $vehiculo->history();
?>
<style>
.multiselect {
  width: 200px;
}

.selectBox {
  position: relative;
}

.selectBox select {
  width: 100%;
  font-weight: bold;
}

.overSelect {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
}

#checkboxes {
  display: none;
  border: 1px #dadada solid;
}

#checkboxes label {
  display: block;
}

#checkboxes label:hover {
  background-color: #1e90ff;
}
</style>
<div id="individual_container">
        <h2>Informe Individual:</h2>
        
        <form id="fBuscar" class="form-inline">
            <table>
            <tr>
                <td><label for="individual__search_form__cliente">Placa</label></td>
                <td><label for="individual__search_form__fecha_emision">Fecha Inicio</label></td>
                <td><label for="individual__search_form__fecha_fin">Fecha Fin</label></td>
            </tr>
            <tr>
                <td><input type="text" name="placa" id="placa" class="input-small" value="<?= $placa ?>"></td>
                <td><input type="text" readonly name="fecha_inicio" id="fecha_inicio" class="input-small" value="<?= $fecha_inicio ?>"></td>
                <td><input type="text" readonly name="fecha_fin" id="fecha_fin" class="input-small" value="<?= $fecha_fin ?>"</td>
                <td><button id="bBuscar" class="btn btn-info">Buscar</button></td>
            </tr>
            </table>
        </form>

        <h3>Datos del vehiculo: <?= $vehiculo->placa ?></h3>
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
            if(empty($mantenimientos))
                echo "<tr><td>No se encontraron registros...</td></tr>";
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

        <h3>Revisiones</h3>

        <div class="multiselect">
            <div class="selectBox" onclick="showCheckboxes()">
            <select>
                <option>Seleccione...</option>
            </select>
            <div class="overSelect"></div>
            </div>
            <div id="checkboxes">
            <label for="one">
                <input type="checkbox" id="one" /><a href="https://gradcollege.okstate.edu/sites/default/files/PDF_linking.pdf" target="_blank">Revisión 1</a></label>
            <label for="two">
                <input type="checkbox" id="two" />Revisión 2</label>
            <label for="three">
                <input type="checkbox" id="three" />Revision 3</label>
            </div>
        </div>

        
</div>
<script>
(function(){
    $('#fBuscar').submit(function(e){
    e.preventDefault();
    if ($('#placa').val()=='') return;
   // $('#bBuscar').button('disable').button('option', 'label', 'Buscando...');
    cargarPrincipal(individual_path+"?"+$(this).serialize());
  });


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

    $el.find('#fecha_inicio').datepicker(datepickerAttributes);
    $el.find('#fecha_fin').datepicker(datepickerAttributes);

    
})();
var expanded = false;
function showCheckboxes() {
    var checkboxes = document.getElementById("checkboxes");
    if (!expanded) {
        checkboxes.style.display = "block";
        expanded = true;
    } else {
        checkboxes.style.display = "none";
        expanded = false;
    }
    }
</script>

