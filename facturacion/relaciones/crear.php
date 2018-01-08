<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][FACTURACION_CREAR_RELACION])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
?>
<button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Nueva Relación</h2>
<hr class="hr-small">
<form id="CrearRelacion" target="_blank" action="facturacion/relaciones/imprimir" method="post">
  <div class="pull-right">
    <b>¿Guardar?</b>
    <select name="save" class="input-mini">
      <option>SI</option>
      <option>NO</option>
    </select>
  </div>
  <table>
    <tr>
      <td><b>Cliente:</b></td>
      <td>
        <input type="text" placeholder="Nombre de Cliente" name="nombre_cliente" id="nombre_cliente" />
        <input type="hidden" name="relacion[id_cliente]" id="id_cliente" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Fecha emisión:</b></td>
      <td><input type="text" value="<?= date('Y-m-d') ?>" class="input-small" readonly="readonly" id="fecha_emision" name="relacion[fecha_emision]" /></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="3">
        <ul class="unstyled inline">
          <li>
            <label for="tipo_fecha">
              <input title="Fecha" id="tipo_fecha" type="radio" name="tipo" value="fecha" checked="checked" />
              Rango de Fecha
            </label>
          </li>
          <li>
            <label for="tipo_numeros">
              <input title="Guias" id="tipo_numeros" type="radio" name="tipo" value="numeros" />
              Número de Guías
            </label>
          </li>
          <li>
            <label for="tipo_documento">
              <input title="Número de documentos" id="tipo_documento" type="radio" name="tipo" value="documento" />
              Número de Documento
            </label>
          </li>
        </ul>
      </td>
    </tr>
    <tr class="fecha">
      <td><b>Forma de pago:</b></td>
      <td>
        <?php
        foreach (Guia::$formas_pago as $key => $forma) {
          $ch = $forma == 'CREDITO' ? 'checked="checked"' : '';
          echo '<label class="checkbox inline" for="tipo_'.$key.'"><input type="checkbox" name="tipos[]" id="tipo_'.$key.'" value="'.$forma.'" '.$ch.' />'.$forma.'</label>';
        }
        ?>
      </td>
      <td></td>
    </tr>
    <tr class="fecha">
      <td><b>Seleccionar desde:</b></td>
      <td><input type="text" readonly="readonly" class="input-small fecha" id="from" name="fecha_inicio" /></td>
      <td></td>
    </tr>
    <tr class="fecha">
      <td><b>Seleccionar hasta:</b></td>
      <td><input type="text" readonly="readonly" class="input-small fecha" id="to" name="fecha_fin" /></td>
      <td></td>
    </tr>
    <tr class="numeros" style="display:none;">
      <td valign="top"><b>Número de Guía</b></td>
      <td class="form-inline">
        <input type="text" class="input-small" disabled="disabled" name="id_guia" id="id_guia" />
        <button id="agregar">Agregar</button>
        <table id="t_guias_numeros"></table>
      </td>
    </tr>
    <tr class="documento" style="display:none;">
      <td valign="top"><b>Número de Documento</b></td>
      <td class="form-inline">
        <input type="text" class="input-small" disabled="disabled" name="numero_documento" id="numero_documento" />
        <table id="t_guias_documento"></table>
      </td>
    </tr>
  </table>
  <center class="form-actions"><button id="crear" class="btn btn-info"><i class="icon-file-alt"></i> Crear Relación</button></center>
</form>
<script>
$(function(){
  function clientSelected(){
    if ($('#id_cliente').val()) return true;
    alertify.log('Selecciona el cliente');
    $('#nombre_cliente').focus();
    return false;
  }
  $('#nombre_cliente')
    .typeahead({
      name: 'clientes',
      valueKey: 'nombre',
      minLength: 4,
      limit: 10,
      remote: helpers_path+'ajax.php?cliente=1&term=%QUERY',
      template: [
        '<p class="client-name">{{nombre}}</p>',
        '<p class="client-address">{{direccion}} - {{nombre_ciudad}}</p>'
      ].join(''),
      engine: Hogan
    })
    .on('typeahead:selected', function(object, cliente) {
      $('#id_cliente').val(cliente.id);
      $('#fecha_emision').focus();
    })
    .focus();

  $('#fecha_emision').datepicker({
    changeMonth: true,
    changeYear: true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    autoSize: true
  });

  var dates = $("#from, #to").datepicker({
    changeMonth: true,
    changeYear: true,
    numberOfMonths: 3,
    showOn: "both",
    maxDate: 0,
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    autoSize: true,
    onSelect: function(selectedDate) {
      var option = this.id == "from" ? "minDate" : "maxDate";
      dates.not( this ).datepicker("option", option, selectedDate);
    }
  });

  $('#agregar')
    .button({icons: {primary: 'ui-icon-circle-plus'}})
    .click(function(e){
      e.preventDefault();
      if(! clientSelected()) return false;
      if( !$.trim($('#id_guia').val()) || isNaN($('#id_guia').val()) ){
        $('#id_guia').focus();
        return false;
      }
      $('#agregar').button('disable');
      var data='accion=comprobar_guia&id_cliente='+$('#id_cliente').val()+'&id_guia='+$('#id_guia').val();
      $.getJSON(relaciones_path+'ajax.php', data, function(r){
        $('#agregar').button('enable');
        if(!r) return false;
        if( !r.error ){
          $('#t_guias_numeros').append(r.mensaje);
          $('#id_guia').val('').focus();
        }else{
          alertify.error(r.mensaje);
        }
      });
    });

  $('#t_guias_numeros, #t_guias_documento').on('click', 'button.quitar', function(){
    $(this).parent().parent().remove();
  });

  $('#numero_documento').autocomplete({
    source: function(request, response){
      if(! clientSelected()){
        $('#numero_documento').val('');
        response([]);
      }
      $.ajax({
        url: relaciones_path+'ajax.php', dataType: 'json',
        data: {
          id_cliente: $('#id_cliente').val(),
          accion: 'numero_documento',
          term: request.term
        },
        success: function(data){
          response(data);
        }
      });
    },
    focus: function() {return false;},
    autoFocus:true,
    minLength: 4,
    select: function(event, ui) {
      $('#t_guias_documento').append(ui.item.html);
      $('#numero_documento').val('');
      return false;
    }
  });
  var tipo = 'fecha';
  $('#tipo_fecha').click(function(){
    tipo = 'fecha';
    $('.numeros, .documento').fadeOut(200);
    $('.numeros input, .documento input').attr('disabled','disabled');
    $('.fecha').fadeIn(200);
    $('.fecha input').removeAttr('disabled');
  });
  $('#tipo_numeros').click(function(){
    tipo = 'numeros';
    $('.fecha, .documento').fadeOut(200);
    $('.fecha input, .documento input').attr('disabled','disabled');
    $('.numeros').fadeIn(200);
    $('.numeros input').removeAttr('disabled');
  });
  $('#tipo_documento').click(function(){
    tipo = 'documento';
    $('.fecha, .numeros').fadeOut(200);
    $('.fecha input, .numeros input').attr('disabled','disabled');
    $('.documento').fadeIn(200);
    $('.documento input').removeAttr('disabled');
  });
  $('#CrearRelacion').submit(function(e){
    if (! clientSelected()) e.preventDefault();
    if(tipo == 'fecha'){
      if($('input[name="tipos[]"]').serialize() == ''){
        e.preventDefault();
        alertify.log('Selecciona la forma de pago de las guías que deseas relacionar');
      }
    }
  });
});
</script>
