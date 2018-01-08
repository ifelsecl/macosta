<button class="btn btn-success pull-right" id="regresar">Regresar</button>
<h2>Editar Guía <?= $guia->id ?></h2>
<hr class="hr-small">
<form id="EditarGuia" name="EditarGuia" method="post">
  <table cellpadding="0">
    <tr>
      <td><b>Destinatario</b></td>
      <td>
        <input type="text" size="30" name="nombre_contacto" id="nombre_contacto" value="<?= $guia->contacto()->nombre_completo ?>" />
        <input type="hidden" name="id_contacto" id="id_contacto" value="<?= $guia->idcontacto ?>" />
        <input type="hidden" name="id_ciudad_contacto" id="id_ciudad_contacto" value="<?= $guia->contacto->idciudad ?>" />
      </td>
    </tr>
    <tr>
      <td><b>Propietario de la carga</b></td>
      <td>
        <select name="propietario" id="propietario">
          <option <?php if($guia->propietario=='Remitente') echo 'selected="selected"'; ?>>Remitente</option>
          <option <?php if($guia->propietario=='Destinatario') echo 'selected="selected"'; ?>>Destinatario</option>
        </select>
      </td>
    </tr>
    <td><b>No. documento:</b></td>
      <td><input type="text" name="documento_cliente" id="documento_cliente" value="<?= $guia->documentocliente ?>" /></td>
      <td></td>
    <tr>
      <td><b>Forma de pago:</b></td>
      <td>
        <select id="forma_pago" name="forma_pago">
          <option value="">Selecciona...</option>
          <option value="FLETE AL COBRO" <?php if($guia->formapago=="FLETE AL COBRO") echo 'selected="selected"' ?>>FLETE AL COBRO</option>
          <option value="CREDITO" <?php if($guia->formapago=="CREDITO") echo 'selected="selected"' ?>>CREDITO</option>
          <option value="CONTADO" <?php if($guia->formapago=="CONTADO") echo 'selected="selected"' ?>>CONTADO</option>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Valor Declarado:</b></td>
      <td><input type="text" name="valor_declarado" id="valor_declarado" value="<?= $guia->valordeclarado ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td valign="top"><b>Unidades:</b></td>
      <td><input class="input-small" maxlength="4" id="unidades" name="unidades" value="<?= $guia->items[0]->unidades ?>" type="text" /></td>
      <td></td>
    </tr>
    <tr>
      <td valign="top"><b>Peso (Kg):</b></td>
      <td><input class="input-small" maxlength="6" id="peso" value="<?= $guia->items[0]->peso ?>" type="text" name="peso" /></td>
      <td></td>
    </tr>
    <tr>
      <td valign="top"><b>Observación:</b></td>
      <td>
        <textarea id="observacion" name="observacion" cols="32" rows="3"><?= $guia->observacion ?></textarea>
      </td>
      <td></td>
    </tr>
  </table>
  <input type="hidden" name="id_item" id="id_item" value="<?= $guia->items[0]->id ?>" />
  <center class="form-actions"><button id="guardar" class="btn btn-info"><i class="icon icon-save"></i> Guardar</button></center>
</form>
<script>
(function() {
  $('#regresar').click(function(){
    $('#actualizar').click();
    regresar();
  });
  $('#nombre_contacto').autocomplete({
    autoFocus:true,
    minLength: 3,
    source: helpers_path+'ajax.php?contacto=1',
    select: function(event, ui) {
      $('#id_contacto').val(ui.item.id);
      $('#documento_cliente').focus();
    }
  }).focus();

  function Guardar(){
    var data='editar=110&'+$('#EditarGuia').serialize()+'&';
    $('#guardar').prop('disabled', true).text('Guardando...');
    $.ajax({
      url: 'guias/ajax.php', type: 'POST', data: data,
      success: function(resp){
        if(resp=='ok'){
          regresar();
        }else{
          $('#guardar').prop('disabled', false).text('Guardar');
          LOGISTICA.Dialog.open('Error', resp, true);
        }
      }
    });
  }

  $('#EditarGuia').validate({
    rules: {
      forma_pago: 'required',
      valor_declarado: {required: true, number: true},
      observacion: {required: true, rangelength: [5, 120]},
      unidades: {required: true, digits: true, min:1},
      peso: {required: true, number: true, min:1}
    },
    errorPlacement: function(error, element) {error.appendTo( element.parent("td").next("td") );},
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form){
      Guardar();
    }
  });
}());
</script>
