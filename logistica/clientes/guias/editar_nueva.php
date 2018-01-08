<button class="btn btn-success pull-right" id="regresar">Regresar</button>
<h2>Editar Guía <?= $guia->id ?></h2>
<hr class="hr-small">
<form id="FormCrearGuia" name="FormCrearGuia" method="post" style="width: 100%">
  <table cellpadding="0">
    <tr>
      <td colspan="3">
        <table style="width: auto;" cellpadding="0">
          <tr>
            <td width="10"></td>
            <td style="padding: 2px;">
              <fieldset>
                <legend><b>Destinatario</b></legend>
                <table style="width: auto;" cellpadding="0">
                  <tr>
                    <td>Nombre:</td>
                    <td>
                      <input type="text" name="nombre_contacto" id="nombre_contacto" value="<?= $guia->contacto()->nombre_completo ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td><abbr title="Número de Identificación">No. Ident</abbr>:</td>
                    <td><input type="text" id="ni_contacto" readonly="readonly" value="<?= $guia->contacto->numero_identificacion ?>" /></td>
                  </tr>
                  <tr>
                    <td>Direccion:</td>
                    <td><input type="text" readonly="readonly" size="45" name="direccion_contacto" id="direccion_contacto" value="<?= $guia->contacto->direccion ?>" /></td>
                  </tr>
                  <tr>
                    <td>Ciudad:</td>
                    <td><input type="text" readonly="readonly" name="ciudad_contacto" id="ciudad_contacto" value="<?= $guia->contacto->ciudad_nombre ?>" /></td>
                  </tr>
                  <tr>
                    <td>Telefono:</td>
                    <td><input type="text" readonly="readonly" name="telefono_contacto" id="telefono_contacto" value="<?= $guia->contacto->telefono ?>" /></td>
                  </tr>
                </table>
              </fieldset>
              <input type="hidden" name="id_contacto" id="id_contacto" value="<?= $guia->idcontacto ?>" />
              <input type="hidden" name="id_ciudad_contacto" id="id_ciudad_contacto" value="<?= $guia->contacto->idciudad ?>" />
            </td>
            <td></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td>
        <b>Propietario de la carga</b><br>
        <select name="propietario" id="propietario">
          <option <?php if($guia->propietario == 'Remitente') echo 'selected="selected"'; ?>>Remitente</option>
          <option <?php if($guia->propietario == 'Destinatario') echo 'selected="selected"'; ?>>Destinatario</option>
        </select>
      </td>
      <td>
        <b>Forma de pago:</b><br>
        <select id="forma_pago" name="forma_pago">
          <option value="">Selecciona...</option>
          <?
          foreach (Guia::$formas_pago as $fp) {
            $s = $fp == $guia->formapago ? 'selected="selected"' : '';
            echo '<option '.$s.'>'.$fp.'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td>
        <b>No. documento:</b><br>
        <input type="text" size="35" name="documento_cliente" id="documento_cliente" value="<?= $guia->documentocliente ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td valign="top">
        <b>La mercancía contiene:</b><br>
        <textarea id="observacion" name="observacion" cols="32" rows="3"><?= $guia->observacion ?></textarea>
      </td>
      <td></td>
    </tr>
  </table>
</form>
<form id="guia_agregar_item" name="guia[agregar_item]" action="#" method="post" class="form-inline">
  <table cellpadding="0">
    <tr>
      <td><b>Cobrar por:</b></td>
      <td>
        <select id="tipo_cobro" class="input-small" name="tipo_cobro">
          <option value="">...</option>
        </select>
      </td>
      <td><b>Valor declarado:</b></td>
      <td>
        <input type="text" class="input-small" name="valor_declarado" id="valor_declarado" />
      </td>
      <td></td>
      <td><b>Unidades:</b></td>
      <td><input maxlength="10" id="unidades" class="input-mini" name="unidades" value="" type="text" /></td>
      <td></td>
      <td><b>Peso (Kg):</b></td>
      <td><input maxlength="10" id="peso" class="input-mini" value="" type="text" name="peso" /></td>
      <td></td>
      <td>
        <button type="submit" id="guia_agregar">Agregar</button>
      </td>
    </tr>
  </table>
</form>
<table id="guia_items" class="table table-hover table-condensed table-bordered">
  <thead>
    <tr>
      <th>Unidades</th>
      <th>Peso (Kgs)</th>
      <th>Valor Declarado</th>
      <th>Seguro</th>
      <th>Flete</th>
      <th>Total</th>
      <th>Quitar</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($guia->items as $item) { ?>
    <tr>
      <td align="right">
        <?= $item->unidades ?>
        <input type="hidden" name="items[item_<?= $item->id ?>][unidades]" value="<?= $item->unidades ?>" />
        <input type="hidden" name="items[item_<?= $item->id ?>][id_embalaje]" value="<?= $item->idembalaje ?>" />
      </td>
      <td align="right">
        <?= $item->peso ?>
        <input type="hidden" name="items[item_<?= $item->id ?>][peso]" value="<?= $item->peso ?>" />
      </td>
      <td align="right">
        <?= $item->valor_declarado ?>
        <input type="hidden" name="items[item_<?= $item->id ?>][valor_declarado]" value="<?= $item->valor_declarado ?>" />
      </td>
      <td align="right">
        <?= $item->seguro ?>
        <input type="hidden" name="items[item_<?= $item->id ?>][seguro]" value="<?= $item->seguro ?>" />
      </td>
      <td align="right">
        <?= $item->valor ?>
        <input type="hidden" name="items[item_<?= $item->id ?>][flete]" value="<?= $item->valor ?>" />
      </td>
      <td align="right"><?= $item->valor + $item->seguro ?></td>
      <td align="center"><button class="btn borrar btn-danger btn-mini"><i class="icon-remove"></i></button></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<center class="form-actions"><button id="guia_guardar" class="btn btn-info"><i class="icon icon-save"></i> Guardar</button></center>
<script>
(function() {
  $('#guia_guardar').click(function(){
    $('#FormCrearGuia').submit();
  });
  $('#guia_agregar').button();
  $('#regresar').click(function(){
    regresar();
  });

  $('#nombre_contacto').autocomplete({
    autoFocus:true,
    minLength: 3,
    focus: function(event, ui) {
      ui.item.value=ui.item.value+' '+ui.item.direccion;
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
        if (msj == 'no') {
          alert('No tiene precios disponibles para '+$('#ciudad_contacto').val()+".\r\nPor favor, seleccione otro destinatario.");
          $('#nombre_contacto').focus();
          $('#tipo_cobro').html('');
        } else {
          $('#tipo_cobro').html(msj);
        }
      }
    });
  }
  ObtenerEmbalajes(false);

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
      var idembalaje = $('#tipo_cobro').val(),
      data = {
        liquidar: 1,
        id_ciudad_destino: $('#id_ciudad_contacto').val(),
        id_embalaje: idembalaje,
        unidades: $('#unidades').val(),
        peso: $('#peso').val(),
        valor_declarado: $('#valor_declarado').val()
      };

      $.ajax({
        url:guias_path+'ajax.php', type:'POST', dataType:'json', data: data,
        success: function(m){
          $('#guia_agregar').button('enable').button('option', 'label', 'Agregar');
          if (! m) {
            alert('No se encontró un precio para el cobro seleccionado.');
            return false;
          }
          NuevaFila(m.unidades, m.peso, idembalaje, m.flete, m.valor_declarado, m.seguro);
        }
      });
    }
  });

  var i=1;
  function NuevaFila(unidades, peso, tipo_cobro, precio, valor_declarado, seguro){
    var fila='<tr>';
    fila+='<td>'+unidades+'<input type="hidden" name="items[item_'+i+'][unidades]" value="'+unidades+'" /><input type="hidden" name="items[item_'+i+'][id_embalaje]" value="'+tipo_cobro+'" /></td>';
    fila+='<td>'+peso+'<input type="hidden" name="items[item_'+i+'][peso]" value="'+peso+'" /></td>';
    fila+='<td><input type="hidden" name="items[item_'+i+'][valor_declarado]" value="'+valor_declarado.toString()+'" />'+valor_declarado.toString()+'</td>';
    fila+='<td><input type="hidden" name="items[item_'+i+'][seguro]" value="'+seguro.toString()+'" />'+seguro.toString()+'</td>';
    fila+='<td><input type="hidden" name="items[item_'+i+'][flete]" value="'+precio.toString()+'" />'+precio.toString()+'</td>';
    fila+='<td>'+(parseInt(precio)+parseInt(seguro)).toString()+'</td>';
    fila+='<td><button class="btn borrar btn-danger btn-mini"><i class="icon-remove"></i></button></td>';
    fila+='</tr>';
    $("#guia_items tbody").append(fila);
    i+=1;
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
    errorPlacement: function(error, element) {error.appendTo( element.parent("td").next("td") );},
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(f){
      $('#guia_guardar').prop('disabled', true).text('Guardando...');
      $.ajax({
        url: guias_path+'ajax.php',
        type: 'POST', data: 'editar=121&'+$('#FormCrearGuia').serialize()+'&'+$('#guia_items input').serialize(),
        success: function(m){
          if(m=='ok'){
            $('#regresar').click();
          }else{
            $('#guia_guardar').prop('disabled', false).text('Guardar');
            alert(m);
          }
        }
      });
    }
  });
}());
</script>
