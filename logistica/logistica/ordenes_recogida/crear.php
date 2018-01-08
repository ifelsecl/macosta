<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][ORDENES_RECOGIDA_CREAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

require_once Logistica::$root."class/camiones.class.php";

$objCamiones  = new Camiones;

$conductores  = Conductor::all('activos');
$camiones     = $objCamiones->Obtener('Activos', 'array');
$rutas        = Ruta::activas();
$ayudantes    = Ayudante::all('activos');
?>
<script>
$(function() {
  var n = 1;
  $('#guardar').button({icons: {primary: 'ui-icon-circle-check'}});
  $('#agregar').button({icons: {primary: 'ui-icon-circle-plus'}, text: false}).click(function(e) {
    e.preventDefault();
    var cl = $.trim( $('#cliente').val() );
    if (! cl) {
      $('#cliente').addClass('ui-state-highlight').focus();
      return false;
    }
    $('#cliente').removeClass('ui-state-highlight');
    var cliente = $('#cliente').val().toUpperCase();
    var direccion = $('#direccion').val();
    var hora = '';
    if ($('#hora').val() != '') {
      hora = $('#hora').val()+':'+$('#minutos').val()+' '+$('#ampm').val();
    }
    var f = '<tr>';
    f+='<td>';
    f+='<input class="cliente" type="hidden" name="clientes[]" value="'+n+'" />';
    f+='<input class="cliente" placeholder="Nombre" class="input-medium" type="text" name="cliente'+n+'[]" value="'+cliente+'" /><br>';
    f+='<input class="cliente" placeholder="Dirección" class="input-medium" type="text" name="cliente'+n+'[]" value="'+direccion+'" />';
    f+='</td>';
    f+='<td><input type="text" class="input-medium" name="cliente'+n+'[]" value="'+$('#observaciones').val()+'" /></td>';
    f+='<td><input type="text" class="input-mini" name="cliente'+n+'[]" value="'+$('#unidades').val()+'" /></td>';
    f+='<td><input type="text" class="input-small" name="cliente'+n+'[]" value="'+hora+'" /></td>';
    f+='<td align="center" width="16"><a title="Borrar" class="btn borrar btn-danger" href="#"><i class="icon-remove"></i></a></td></tr>';
    n++;
    $('#orden_recogida_clientes tbody').append(f);
    $('#hora,#observaciones,#direccion,#cliente,#unidades,#minutos,#ampm').val('');
    $('#cliente').focus();
  });

  $('table#orden_recogida_clientes').on('click', 'a.borrar', function(e) {
    e.preventDefault();
    $(this).parent().parent().remove();
  });

  $('#ciudad').focus().autocomplete({
    minLength: 3,
    autoFocus: true,
    source: helpers_path+'ajax.php?ciudad=1',
    select: function(event, ui) {
      $('#id_ciudad').val(ui.item.id);
      $('#placa_vehiculo').focus();
    }
  });
  $('#cliente').autocomplete({
    minLength: 2,
    autoFocus:true,
    source: helpers_path+'ajax.php?cliente=1',
    select: function(event, ui) {
      $('#cliente').val(ui.item.nombre);
      $('#direccion').val(ui.item.direccion);
      $('#observaciones').focus();
      return false;
    }
  });
  $('#CrearOrden').validate({
    rules: {
      id_ciudad: {required: true},
      fecha: {required: true},
      placa_vehiculo: {required: true},
      numero_identificacion_conductor: {required: true},
      id_ruta: {required: true},
      ayudantes: 'required'
    },
    errorPlacement: function(error, element) {
      error.appendTo(element.parent("td").next("td") );
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(f) {
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: ordenes_recogida_path+'ajax.php',
        type: "POST",
        data: 'guardar=1&'+$('#CrearOrden').serialize(),
        success: function(msj) {
          if (msj=="ok") {
            cargarPrincipal(ordenes_recogida_path);
            regresar();
          }else{
            $('#guardar').button('enable').button('option','label','Guardar');
            LOGISTICA.Dialog.open('Error',msj,true);
          }
        }
      });
    }
  });
  $( ".fecha" ).datepicker({
    autoSize:true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...'
  });
});
</script>
<button class="btn btn-success pull-right" id="ordenes_recogida_regresar" onclick="regresar();">Regresar</button>
<h2>Nueva Orden de Recogida</h2>
<hr class="hr-small">
<form id="CrearOrden" name="CrearOrden" method="post" action="#">
  <table>
    <tr>
      <td><b>Fecha</b></td>
      <td>
        <input class="input-small fecha" readonly="readonly" type="text" name="fecha" id="fecha" value="<?=date("Y-m-d");?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ciudad</b></td>
      <td>
        <input class="input-medium" type="text" name="ciudad" id="ciudad" />
        <input type="hidden" name="id_ciudad" id="id_ciudad" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Vehículo</b></td>
      <td>
        <select class="input-medium" title="Seleccione el vehículo" name="placa_vehiculo" id="placa_vehiculo">
          <option selected="selected"></option>
          <?php
          while($camion = mysql_fetch_array($camiones) ) {
            echo '<option value="'.$camion['placa'].'">'.$camion['placa'].'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Conductor</b></td>
      <td>
        <select class="input-medium" title="Seleccione el conductor" id="numero_identificacion_conductor" name="numero_identificacion_conductor">
          <option selected="selected"></option>
          <?php
          foreach($conductores as $conductor) {
            echo '<option value="'.$conductor->numero_identificacion.'">'.$conductor->nombre_completo().'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ruta</b></td>
      <td>
        <select class="input-medium" title="Seleccione la ruta" id="id_ruta" name="id_ruta">
          <option selected="selected"></option>
          <?php
          while ($ruta = mysql_fetch_array($rutas)) {
            echo '<option value="'.$ruta['id'].'">'.$ruta['nombre'].'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ayudantes</b></td>
      <td>
        <?php
        if (count($ayudantes) == 0) {
          echo 'No existen ayudantes, Ve al módulo de <b>Ayudantes</b> para crearlos.';
        }
        echo '<table>';
        $f = true;
        foreach ($ayudantes as $ayudante) {
          if ($f) echo '<tr>';
          echo '<td><label><input type="checkbox" name="ayudantes[]" value="'.$ayudante->id.'" /> '.$ayudante->nombre.'</label></td>';
          if (! $f) echo '</tr>';
          $f = !$f;
        }
        echo '</table>';
        ?>
      </td>
      <td></td>
    </tr>
  </table>
  <hr class="hr-small">
  <table>
    <tr>
      <td><b>Cliente</b></td>
      <td>
        <input class="input-small" type="text" id="cliente" />
      </td>
      <td><b>Dirección</b></td>
      <td>
        <input class="input-small" type="text" name="direccion" id="direccion" />
      </td>
      <td rowspan="3"><button id="agregar">Agregar</button></td>
    </tr>
    <tr>
      <td><b>Observaciones</b></td>
      <td colspan="3">
        <input class="input-medium" type="text" name="observaciones" id="observaciones" />
      </td>
    </tr>
    <tr>
      <td><b>Unidades</b></td><td><input class="input-mini" type="text" maxlength="3" id="unidades" /></td>
      <td><b>Hora</b></td>
      <td>
        <select id="hora" class="input-mini">
          <option></option>
          <?php
          for ($i=1; $i <= 12; $i++) {
            if (strlen($i)==1) {echo '<option>0'.$i.'</option>';}
            else{echo '<option>'.$i.'</option>';}
          }
          ?>
        </select>
        <select id="minutos" class="input-mini">
          <option></option>
          <?php
          for ($i=0; $i <= 59; $i++) {
            if (strlen($i)==1) {echo '<option>0'.$i.'</option>';}
            else{echo '<option>'.$i.'</option>';}
          }
          ?>
        </select>
        <select id="ampm" class="input-mini">
          <option></option>
          <option>AM</option>
          <option>PM</option>
        </select>
      </td>
    </tr>
  </table>
  <table id="orden_recogida_clientes" class="table table-bordered table-condensed table-hover">
    <thead>
      <tr>
        <th>Cliente</th>
        <th>Observaciones</th>
        <th>Unidades</th>
        <th>Hora</th>
        <th>Borrar</th>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
  <center class="form-actions"><button id="guardar">Guardar</button></center>
</form>
