<?php
require "../../seguridad.php";
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$mantenimientos = VehiculoMantenimiento::search(array("fecha_inicio" => $fecha_inicio, "fecha_fin" => $fecha_fin, "is_general" => true));
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
        <h2>Informe General:</h2>
        
        <form id="fBuscar" class="form-inline">
            <table>
            <tr>
                <td><label for="individual__search_form__fecha_emision">Fecha Inicio</label></td>
                <td><label for="individual__search_form__fecha_fin">Fecha Fin</label></td>
            </tr>
            <tr>
                <td><input type="text" readonly name="fecha_inicio" id="fecha_inicio" class="input-small" value="<?= $fecha_inicio ?>"></td>
                <td><input type="text" readonly name="fecha_fin" id="fecha_fin" class="input-small" value="<?= $fecha_fin ?>"</td>
                <td><button id="bBuscar" class="btn btn-info">Buscar</button></td>
            </tr>
            </table>
        </form>

        <h3>Mantenimientos</h3>
        <table id="vehiculo-mantenimientos" class="table table-hover table-condensed table-bordered">
          <thead>
            <tr>
                <th>Placa Vehiculo</th>
                <th>Mantenimiento</th>
                <th>Fecha</th>
                <th>KM</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Observación</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if(empty($mantenimientos))
                echo "<tr><td>No se encontraron registros...</td></tr>";
            foreach ($mantenimientos as $m) {
                echo "<tr><td>".$m->vehiculo_placa."</td>";
                echo "<td>".$m->mantenimiento_nombre."</td>";
                echo "<td>".$m->fecha."</td>";
                echo "<td>".$m->mantenimiento_kilometraje."</td>";
                echo "<td>".$m->tipo."</td>";
                echo "<td>".$m->precio."</td>";
                echo "<td>".$m->observacion."</td></tr>";
            }
            ?>
          </tbody>
        </table>
</div>
<script>
(function(){
    $('#fBuscar').submit(function(e){
    e.preventDefault();
    if ($('#placa').val()=='') return;
   // $('#bBuscar').button('disable').button('option', 'label', 'Buscando...');
    cargarPrincipal(general_path+"?"+$(this).serialize());
  });


    var $el = $('#individual_container');
    var $searchBtn = $el.find('form button');


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

