<div class="btn-toolbar pull-right">
  <button class="btn btn-success" id="regresar">Regresar</button>
</div>
<h2>Crear Nueva Guía</h2><!--/*esta es crear guía del lado del cliente*/-->
<hr class="hr-small">
<form id="FormCrearGuia" name="FormCrearGuia" method="post" style="width: 100%; ">
  <table cellpadding="0">
    <tr>
      <td colspan="3">
        <table cellpadding="0">
          <tr>
            <td width="10"></td>
            <td>
              <button class="btn pull-right" type="button" id="crear_contacto"><i class="icon icon-plus-sign"></i> Nuevo Contacto</button>
              <fieldset>
                <legend>Destinatario</legend>
                <table style="width: auto;" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>Nombre:</td>
                    <td><input type="text" size="45" name="nombre_contacto" id="nombre_contacto" /></td>
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
            <td></td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td><b>Propietario de la carga</b></td>
      <td>
        <select name="propietario" id="propietario">
          <option selected="selected">Remitente</option>
          <option>Destinatario</option>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Forma de pago:</b></td>
      <td>
        <select id="forma_pago" name="forma_pago">
          <option value="">Selecciona...</option>
          <?
          foreach ($formas_pago as $fp)
            echo '<option '.$fp.'>'.$fp.'</option>';
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td valign="top"><b>Valor declarado:</b></td>
      <td>
        <input type="text" name="valor_declarado" id="valor_declarado" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Unidades:</b></td>
      <td><input type="text" class="input-small" name="unidades" id="unidades" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Peso (Kg):</b></td>
      <td><input type="text" class="input-small" name="peso" id="peso" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>No. documento:</b></td>
      <td><input type="text" size="40" name="documento_cliente" id="documento_cliente" value="" /></td>
      <td></td>
    </tr>
    <tr>
      <td valign="top"><b>La mercancía contiene:</b></td>
      <td>
        <textarea id="observacion" name="observacion" cols="32" rows="3"></textarea>
      </td>
      <td></td>
    </tr>
  </table>
  <hr class="hr-small">
  <center><button id="guardar" class="btn btn-info"><i class="icon icon-save"></i> Guardar</button></center>
</form>
<script type="text/javascript">
(function() {
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
      return false;
    }
  }).focus();

  $('#FormCrearGuia').validate({
    rules:{
      id_contacto: 'required',
      valor_declarado: {required:true, number:true, min:10000},
      forma_pago: 'required',
      unidades: {required: true, digits: true, min:1},
      peso: {required: true, number: true, min: 1},
      observacion: {required: true, rangelength: [5, 120]}
    },
    messages: {
      id_contacto: 'Selecciona el destinatario.',
      valor_declarado:{min:'Minimo 10.000'},
      peso: {number: 'Solo numeros, para decimales usar el punto (.)', min: 'Minimo 1 Kg'}
    },
    errorPlacement: function(error, element) {error.appendTo( element.parent("td").next("td") );},
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(f){
      $('#guardar').prop('disabled', true).text('Guardando...');
      $.ajax({
        url: guias_path+'ajax.php',
        type: 'POST', data: 'guardar=121&'+$('#FormCrearGuia').serialize(),
        success: function(m){
          if (m == 'ok') {
            var c = confirm("La guia se ha guardado correctamente, ¿desea crear otra?\r\nPresione Esc para cancelar");
            if (c) {
              cargarExtra(guias_path+'crear.php');
            } else {
              $('#regresar').click();
            }
          } else {
            $('#guardar').prop('disabled', false).text('Guardar');
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
