<button class="btn btn-success pull-right" id="regresar">Regresar</button>
<h2>Crear Nueva Guía</h2>
<hr class="hr-small">
<form id="FormCrearGuia" name="FormCrearGuia" method="post" style="width: 100%; ">
  <table cellpadding="2" cellspacing="0" border="0">
    <tr>
      <td colspan="3">
        <table style="width: auto;" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="10"></td>
            <td style="padding: 2px;">
              <button class="btn pull-right" type="button" id="crear_contacto"><i class="icon icon-plus-sign"></i> Nuevo Destinatario</button>
              <fieldset>
                <legend>Destinatario</legend>
                <table cellpadding="0">
                  <tr>
                    <td>Nombre:</td>
                    <td><input type="text" name="nombre_contacto" id="nombre_contacto" /></td>
                  </tr>
                  <tr>
                    <td><abbr title="Numero de Identificación">No. Ident</abbr>:</td>
                    <td><input type="text" id="ni_contacto" readonly="readonly" /></td>
                  </tr>
                  <tr>
                    <td>Direccion:</td>
                    <td><input type="text" readonly="readonly" name="direccion_contacto" id="direccion_contacto" /></td>
                  </tr>
                  <tr>
                    <td>Ciudad:</td>
                    <td><input type="text" readonly="readonly" name="ciudad_contacto" id="ciudad_contacto" /></td>
                  </tr>
                  <tr>
                    <td>Telefono:</td>
                    <td><input type="text" readonly="readonly" name="telefono_contacto" id="telefono_contacto" /></td>
                  </tr>
                </table>
              </fieldset>
              <input type="hidden" name="id_contacto" id="id_contacto" />
              <input type="hidden" name="id_ciudad_contacto" id="id_ciudad_contacto" />
            </td>
            <td id=""></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>
        <b>Propietario de la carga</b><br>
        <select name="propietario" id="propietario">
          <option selected="selected">Remitente</option>
          <option>Destinatario</option>
        </select>
      </td>
      <td>
        <b>Forma de pago:</b><br>
        <select id="forma_pago" name="forma_pago">
          <option value="">Selecciona...</option>
          <?php
          foreach ($formas_pago as $fp)
            echo '<option '.$fp.'>'.$fp.'</option>';
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td>
        <b>No. documento:</b><br>
        <input type="text" size="35" name="documento_cliente" id="documento_cliente" value="" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td valign="top">
        <b>La mercancía contiene:</b><br>
        <textarea id="observacion" name="observacion" cols="32" rows="3"></textarea>
      </td>
      <td></td>
    </tr>
  </table>
</form>
<form id="guia_agregar_item" name="guia[agregar_item]" action="#" method="post">
  <table cellpadding="0">
    <tr class="form-inline">
      <td><b>Cobrar por:</b></td>
      <td>
        <select class="input-small" id="tipo_cobro" name="tipo_cobro">
          <option value="">...</option>
        </select>
      </td>
      <td><b>Valor declarado:</b></td>
      <td>
        <input type="text" class="input-small" name="valor_declarado" id="valor_declarado" />
      </td>
      <td></td>
      <td><b>Unidades:</b></td>
      <td><input class="input-mini" maxlength="5" id="unidades" name="unidades" value="" type="text" /></td>
      <td></td>
      <td><b>Peso (Kg):</b></td>
      <td><input class="input-mini" maxlength="10" id="peso" value="" type="text" name="peso" /></td>
      <td></td>
      <td>
        <button type="submit" id="guia_agregar">Agregar</button>
      </td>
    </tr>
  </table>
</form>
<table id="guia_items" class="table table-hover table-bordered table-condensed">
  <thead>
    <tr>
      <th>Unidades</th>
      <th>Peso (Kgs)</th>
      <th>Valor Declarado</th>
      <th>Seguro</th>
      <th>Flete</th>
      <th>Descuento</th>
      <th>Total</th>
      <th>Quitar</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<center class="form-actions"><button id="guia_guardar" class="btn btn-info"><i class="icon icon-save"></i> Guardar</button></center>
<script>
(function() {
  $('#guia_guardar').click(function(){
    $('#FormCrearGuia').submit();
  });
  $('#guia_agregar').button({icons: {primary: 'ui-icon-circle-plus'}, text: false});
  $('#regresar').click(function(){
    $('#actualizar').click();
    regresar();
  });
  $('#nombre_contacto').autocomplete({
    autoFocus:true,
    minLength: 4,
    focus: function(event, ui) {
      ui.item.value = ui.item.value+' '+ui.item.direccion;
    },
    source: helpers_path+'ajax.php?contacto=1',
    select: function(event, ui) {
      $('#id_contacto').val(ui.item.id);
      $('#ni_contacto').val(ui.item.numero_identificacion);
      $('#nombre_contacto').val(ui.item.nombre);
      $('#direccion_contacto').val(ui.item.direccion);
      $('#telefono_contacto').val(ui.item.telefono);
      $('#ciudad_contacto').val(ui.item.ciudad);
      $('#id_ciudad_contacto').val(ui.item.id_ciudad);
      $('#forma_pago').focus();
      ObtenerEmbalajes();
      return false;
    }
  }).focus();

  function ObtenerEmbalajes(alerta){
    $.ajax({
      url: guias_path+'ajax.php', type: 'POST',
      data:'buscarembalaje=si&id_ciudad_contacto='+$('#id_ciudad_contacto').val(),
      success: function(msj){
        if(msj=='no'){
          alert('No tiene precios disponibles para '+$('#ciudad_contacto').val()+".\r\nLa guía no será liquidada automaticamente.");
          $('#nombre_contacto').focus();
          $('#tipo_cobro').html('');
        }else{
          $('#tipo_cobro').html(msj);
        }
      }
    });
  }

  $('#guia_agregar_item').validate({
    rules: {
      tipo_cobro: 'required',
      valor_declarado: {required: true, number: true, min: 0},
      unidades: {required: true, digits: true, min:1},
      peso: {required: true, number: true, min: 1}
    },
    errorPlacement: function(error, element) { return; },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(f){
      $('#guia_agregar').button('disable').button('option','label','Agregando...');
      var idembalaje=$('#tipo_cobro').val();

      var data = {
        liquidar: 1,
        id_ciudad_destino: $('#id_ciudad_contacto').val(),
        id_embalaje: idembalaje,
        unidades: $('#unidades').val(),
        peso: $('#peso').val(),
        valor_declarado: $('#valor_declarado').val()
      };
      $.ajax({
        url: guias_path+'ajax.php', type: 'POST', dataType: 'json', data: data,
        success: function(m) {
          $('#guia_agregar').button('enable').button('option', 'label', 'Agregar');
          if (! m) {
            alert('No se encontró un precio para el cobro seleccionado.');
            return false;
          }
          NuevaFila(m.unidades, m.peso, idembalaje, m.flete, m.valor_declarado, m.seguro, m.descuento);
        }
      });
    }
  });

  var i = 1; //cantidad de productos agregados
  function NuevaFila(unidades, peso, tipo_cobro, flete, valor_declarado, seguro, descuento) {
    var fila = '<tr>';
    fila += '<td>'+unidades+'<input type="hidden" name="items[item_'+i+'][unidades]" value="'+unidades+'" /><input type="hidden" name="items[item_'+i+'][id_embalaje]" value="'+tipo_cobro+'" /></td>';
    fila += '<td>'+peso+'<input type="hidden" name="items[item_'+i+'][peso]" value="'+peso+'" /></td>';
    fila += '<td><input type="hidden" name="items[item_'+i+'][valor_declarado]" value="'+valor_declarado.toString()+'" />'+valor_declarado.toString()+'</td>';
    fila += '<td><input type="hidden" name="items[item_'+i+'][seguro]" value="'+seguro.toString()+'" />'+seguro.toString()+'</td>';
    fila += '<td><input type="hidden" name="items[item_'+i+'][flete]" value="'+flete.toString()+'" />'+(parseInt(flete) + parseInt(descuento))+'</td>';
    fila += '<td>'+descuento.toString()+'</td>';
    fila += '<td>'+(parseInt(flete) + parseInt(seguro)).toString()+'</td>';
    fila += '<td><button class="borrar btn btn-danger"><i class="icon-remove"></i></button></td>';
    fila += '</tr>';
    $("#guia_items tbody").append(fila);
    i++;
  }

  $('table#guia_items').on('click', 'button.borrar', function(){
    $(this).parent().parent().remove();
  });

  $('#FormCrearGuia').validate({
    rules:{
      id_contacto: 'required',
      forma_pago: 'required',
      observacion: {required: true, rangelength: [5, 120]}
    },
    messages: {
      id_contacto: 'Selecciona el destinatario.'
    },
    errorPlacement: function(error, element) {
      error.appendTo( element.parent("td").next("td") );
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(f){
      $('#guia_guardar').prop('disabled', true).text('Guardando...');
      $.ajax({
        url: guias_path+'ajax.php',
        type: 'POST', data: 'guardar=121&'+$('#FormCrearGuia').serialize()+'&'+$('#guia_items input').serialize(),
        success: function(m){
          if (m == 'ok') {
            var c = confirm("La guia se ha guardado correctamente, ¿desea crear otra?\r\nPresione Esc para cancelar");
            if (c) {
              $('#extra_content').load(guias_path+'crear.php');
            } else {
              $('#regresar').click();
            }
          } else {
            $('#guia_guardar').prop('disabled', false).text('Guardar');
            alert(m);
          }
        }
      });
    }
  });

  $('#crear_contacto').click(function(){
    LOGISTICA.Dialog.open('Crear nuevo contacto', guias_path+'crear_contacto.php');
  });
}());
</script>
