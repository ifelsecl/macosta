<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  include Logistica::$root.'/mensajes/id.php';
  exit;
}
if (! isset($_SESSION['permisos'][ORDENES_RECOGIDA_EDITAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (! $orden_recogida = OrdenRecogida::find($_GET['id'])) exit('No existe la orden de recogida');
require Logistica::$root."class/camiones.class.php";

$objCamiones  = new Camiones;

$conductores  = Conductor::all('activos');
$camiones     = $objCamiones->Obtener('Activos', 'array');
$rutas        = Ruta::activas();
$ayudantes    = Ayudante::all('activos');
?>
<script>
$(function() {
  var n = 50;
  $('#ciudad').focus();
  $('#guardar').button({icons:{primary:'ui-icon-circle-check'}});
  $('#EditarOrden').validate({
    rules: {
      id_ciudad: {required: true},
      fecha: {required: true},
      placa_vehiculo: {required: true},
      numero_identificacion_conductor: {required: true},
      id_ruta: {required: true}
    },
    errorPlacement: function(er, el){er.appendTo(el.parent("td").next("td") );},
    highlight: function(i) {$(i).addClass("ui-state-highlight");},
    unhighlight: function(i) {$(i).removeClass("ui-state-highlight");},
    submitHandler: function(f) {
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: ordenes_recogida_path+'ajax.php',
        type: "POST",
        data: 'editar=1&'+$('#EditarOrden').serialize(),
        success: function(msj){
          if(msj=="ok"){
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
  $('#ciudad').autocomplete({
    minLength: 3,
    autoFocus:true,
    source: helpers_path+'ajax.php?ciudad=1',
    select: function(event, ui) {
      $('#id_ciudad').val(ui.item.id);
      $('#placa_vehiculo').focus();
    }
  });
  $('#agregar').button({icons: {primary: 'ui-icon-circle-plus'}, text: false}).click(function(e){
    e.preventDefault();
    e.preventDefault();
    if ( !$('#cliente').val() ){
      $('#cliente').addClass('ui-state-highlight').focus();
      return false;
    };
    $('#cliente').removeClass('ui-state-highlight');
    var cliente=$('#cliente').val().toUpperCase();
    var direccion=$('#direccion').val();
    if($('#hora').val()==''){
      var hora='';
    }else{
      var hora=$('#hora').val()+':'+$('#minutos').val()+' '+$('#ampm').val();
    }
    var f='<tr>';
    f+='<td>';
    f+='<input class="cliente" type="hidden" name="clientes[]" value="'+n+'" />'+cliente+'<br>'+direccion;
    f+='<input class="cliente" class="input-medium" type="hidden" name="cliente'+n+'[]" value="'+cliente+'" />';
    f+='<input class="cliente" class="input-medium" type="hidden" name="cliente'+n+'[]" value="'+direccion+'" />';
    f+='</td>';
    f+='<td><input type="text" class="input-medium" name="cliente'+n+'[]" value="'+$('#observaciones').val()+'" /></td>';
    f+='<td><input type="text" class="input-mini" name="cliente'+n+'[]" value="'+$('#unidades').val()+'" /></td>';
    f+='<td><input type="text" class="input-mini" name="cliente'+n+'[]" value="'+hora+'" /></td>';
    f+='<td align="center" width="16"><button title="Borrar" class="btn borrar btn-danger"><i class="icon-remove"></i></button></td></tr>';
    n++;
    $('#orden_recogida_clientes tbody').append(f);
    $('#hora,#observaciones,#direccion,#cliente,#unidades,#minutos,#ampm').val('');
    $('#cliente').focus();
  });
  $('table#orden_recogida_clientes').on('click', 'button.borrar',function(e){
    e.preventDefault();
    $(this).parent().parent().remove();
  });
});
</script>
<button class="btn btn-success pull-right" id="regresar" onclick="regresar()">Regresar</button>
<h2>Editar Orden de Recogida <?= $orden_recogida->id ?></h2>
<hr class="hr-small">
<form id="EditarOrden" name="EditarOrden" method="post" action="#">
  <table>
    <tr>
      <td><b>Fecha</b></td>
      <td>
        <input class="fecha input-small" readonly="readonly" type="text" name="fecha" id="fecha" value="<?= $orden_recogida->fecha ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ciudad</b></td>
      <td>
        <input size="40" type="text" name="ciudad" id="ciudad" value="<?= $orden_recogida->ciudad_nombre ?>" />
        <input type="hidden" name="id_ciudad" id="id_ciudad" value="<?= $orden_recogida->id_ciudad ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Vehículo</b></td>
      <td>
        <select name="placa_vehiculo" id="placa_vehiculo">
          <option selected="selected"></option>
          <?php
            while($camion = mysql_fetch_array($camiones) ){
              $s = $camion['placa']==$orden_recogida->placa_vehiculo ? 'selected="selected"' : '';
              echo '<option value="'.$camion['placa'].'" '.$s.'>'.$camion['placa'].'</option>';
            }
                ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Conductor</b></td>
      <td>
        <select id="numero_identificacion_conductor" name="numero_identificacion_conductor">
          <option selected="selected"></option>
          <?php
          foreach ($conductores as $conductor) {
            $s = $conductor->numero_identificacion == $orden_recogida->numero_identificacion_conductor ? 'selected="selected"' : '';
            echo '<option value="'.$conductor->numero_identificacion.'" '.$s.'>'.$conductor->nombre_completo().'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ruta</b></td>
      <td>
        <select title="Seleccione la ruta" id="id_ruta" name="id_ruta">
          <option selected="selected"></option>
          <?php
          while ($ruta=mysql_fetch_array($rutas)) {
            $s = $ruta['id']==$orden_recogida->id_ruta ? 'selected="selected"' : '';
            echo '<option value="'.$ruta['id'].'" '.$s.'>'.$ruta['nombre'].'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ayudantes</b></td>
      <td>
        <fieldset>
          <?php
          $orden_recogida->ayudantes();
          if (count($ayudantes) == 0) {
            echo 'No existen ayudantes, Ve al módulo de <b>Ayudantes</b> para crear algunos.';
          } else {
            $f = true;
            echo '<table>';
            foreach ($ayudantes as $ayudante) {
              if ($f) echo '<tr>';
              $c = isset($orden_recogida->ayudantes[$ayudante->id]) ? 'checked="checked"' : '';
              echo '<td><label><input type="checkbox" '.$c.' name="ayudantes[]" value="'.$ayudante->id.'" />'.$ayudante->nombre.'</label></td>';
              if (! $f) echo '</tr>';
              $f = !$f;
            }
            echo '</table>';
          }
          ?>
        </fieldset>
      </td>
      <td></td>
    </tr>
  </table>
  <hr />
  <table>
    <tr>
      <td><b>Cliente</b></td>
      <td>
        <input type="text" id="cliente" />
      </td>
      <td><b>Direccion</b></td>
      <td>
        <input type="text" name="direccion" id="direccion" />
      </td>
      <td rowspan="3"><button id="agregar">Agregar</button></td>
    </tr>
    <tr>
      <td><b>Observaciones</b></td>
      <td colspan="3">
        <input type="text" name="observaciones" id="observaciones" />
      </td>
    </tr>
    <tr>
      <td><b>Unidades</b></td><td><input class="input-mini" type="text" maxlength="3" id="unidades" /></td>
      <td><b>Hora</b></td>
      <td>
        <select class="input-mini" id="hora">
          <option></option>
          <?php
          for ($i=1; $i <= 12; $i++) {
            if (strlen($i)==1) {echo '<option>0'.$i.'</option>';}
            else{echo '<option>'.$i.'</option>';}
          }
          ?>
        </select>
        <select class="input-mini" id="minutos">
          <option></option>
          <?php
          for ($i=0; $i <= 59; $i++) {
            if (strlen($i)==1) {echo '<option>0'.$i.'</option>';}
            else{echo '<option>'.$i.'</option>';}
          }
          ?>
        </select>
        <select class="input-mini" id="ampm">
          <option></option>
          <option>AM</option>
          <option>PM</option>
        </select>
      </td>
  </table>
  <table id="orden_recogida_clientes" class="table table-bordered">
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
      <?php
      $n = 1;
      if ( ! empty($orden_recogida->clientes) ) {
        $lineas = explode(';;', $orden_recogida->clientes);
        foreach ($lineas as $linea) {
          $campos = explode('--', $linea);
          if( ! isset($campos[3]) ) {
            $campos[3] = '';
            $campos[4] = '';
          }
          echo '<tr>';
          echo '
            <td>
              <input class="cliente" type="hidden" name="clientes[]" value="'.$n.'" />
              <input placeholder="Nombre" class="cliente" class="input-medium" type="text" name="cliente'.$n.'[]" value="'.$campos[0].'" /><br>
              <input placeholder="Dirección" class="cliente" class="input-medium" type="text" name="cliente'.$n.'[]" value="'.$campos[1].'" />
            </td>';
          echo '<td><input type="text" class="input-medium" name="cliente'.$n.'[]" value="'.$campos[2].'" /></td>';
          echo '<td><input type="text" class="input-mini" name="cliente'.$n.'[]" value="'.$campos[3].'" /></td>';
          echo '<td><input type="text" class="input-mini" name="cliente'.$n.'[]" value="'.$campos[4].'" /></td>';
          echo '<td align="center" width="16"><button class="btn borrar btn-danger" title="Borrar"><i class="icon-remove"></i></button></td>';
          echo '</tr>';
          $n++;
        }
      }
      ?>
    </tbody>
  </table>
  <center class="form-actions"><button id="guardar">Guardar</button></center>
  <input type="hidden" id="id" name="id" value="<?= $orden_recogida->id ?>" />
  <?php nonce_create_form_input($orden_recogida->id) ?>
</form>
