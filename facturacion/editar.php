<?php
require "../seguridad.php";
if (! isset($_GET['idfactura']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['idfactura'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
if (! isset($_SESSION['permisos'][FACTURACION_EDITAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
if (! $factura = Factura::find($_GET['idfactura'])) exit('No existe la factura');
?>
<button id="regresar-editar" class="btn btn-success pull-right">Regresar</button>
<h2>Editar Factura <?= $factura->id ?></h2>
<hr class="hr-small">
<?php
if ($factura->estado == 'Pagada') {
  echo '<div style="text-align:center">';
  echo '<div class="ui-widget-header" style="padding: 2px;">&iexcl;Factura Pagada!</div>';
  echo '<div style="padding: 2px;">La factura ha sido pagada y no se puede editar.</div>';
  echo '</div>';
  exit;
}
?>
<form id="EditarFactura">
  <table cellspacing="0">
    <tr>
      <td><b>Cliente</b>:</td>
      <td title="El cliente no puede ser cambiado">
        <label><?= $factura->cliente()->nombre_completo ?></label>
        <input type="hidden" id="id_cliente_editar" value="<?= $factura->idcliente ?>" />
      </td>
      <td width="30"></td>
      <td><b>Estado</b>:</td>
      <td>
        <select name="factura[estado]" id="estado">
          <?php
          foreach (Factura::$estados as $estado) {
            $s = $estado == $factura->estado ? 'selected="selected"' : '';
            echo '<option '.$s.'>'.$estado.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Fecha Emisión</b>:</td>
      <td><input readonly="readonly" id="factura__fecha_emision" type="text" class="fecha input-small" name="factura[fechaemision]" value="<?= $factura->fechaemision ?>" /></td>
      <td width="30"></td>
      <td><b>Condición de Pago</b>:</td>
      <td><input type="text" id="c_p" name="factura[condicionpago]" class="input-mini" value="<?= $factura->condicionpago ?>" /></td>
    </tr>
    <tr>
      <td><b>Tipo:</b></td>
      <td>
        <select name="factura[tipo]" id="tipo">
          <?php
          foreach (Factura::$tipos as $tipo) {
            $s = $tipo == $factura->tipo ? 'selected="selected"' : '';
            echo '<option '.$s.'>'.$tipo.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
  </table>
  <fieldset id="agregar_guias" style="width:250px">
    <legend>Agregar nueva guía</legend>
    <table class="form-inline">
      <tr>
        <td>Número:</td>
        <td><input type="text" class="input-small" id="numero" /></td>
        <td><button id="agregar" type="button">Agregar</button></td>
      </tr>
    </table>
  </fieldset>
  <div id="div_guias">
    <p class="expand">Cargando...</p>
  </div>
  <input type="hidden" id="id" name="id" value="<?= $factura->id ?>" />
  <?php nonce_create_form_input($factura->id) ?>
  <center class="form-actions"><button id="guardar" class="btn btn-primary btn-large">Guardar</button></center>
</form>
<script>
(function() {
  var $saveBtn = $('#guardar'),
      $addBtn  = $('#agregar');
  $('#regresar-editar').click(function() {
    $('#actualizar').click();
    LOGISTICA.Content.returnToMain();
  });

  $('#factura__fecha_emision').datepicker({
    changeMonth: true,
    changeYear: true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    autoSize: true,
    maxDate: "+1m"
  });

  $addBtn
    .button({icons: {primary:'ui-icon-circle-plus'},text:false})
    .click(function(e) {
      e.preventDefault();
      var numero=$('#numero').val();
      if (! $.trim(numero) || isNaN(numero)) return;
      if ($('#cantidad_guias').val()==35) {
        alert('Solo se permiten 35 guias por factura.');
        return;
      }
      $addBtn.button('disable');
      $.ajax({
        url: facturacion_path+'ajax.php',
        data: 'cg=12&id_cliente='+$('#id_cliente_editar').val()+'&id_guia='+numero,
        type: 'POST', dataType: 'json',
        success: function(r) {
          $addBtn.button('enable');
          if (r.error=='no') {
            $.ajax({
              url: facturacion_path+'asignarGuia.php',
              data: 'id_guia='+numero+'&id_factura='+$('#id').val(),
              type: 'POST',
              success: function(resp) {
                if (! resp) {
                  ObtenerGuias();
                } else {
                  LOGISTICA.Dialog.open('Error', resp, true);
                }
              }
            });
          } else {
            LOGISTICA.Dialog.open('Error', r.mensaje, true);
          }
        }
      });
    });

  /**
   * Obtiene las guias de la factura y las muestra en una tabla.
   */
  function ObtenerGuias() {
    $('#div_guias').load(facturacion_path+'guiasAsignadas.php?id='+$('#id').val());
  }

  $('#EditarFactura').validate({
    rules: {
      'factura[estado]': 'required',
      'factura{fechaemision}': 'required',
      'factura[condicionpago]': {required: true, digits: true}
    },
    messages: {
      'factura{fechaemision}': 'Selecciona la fecha'
    },
    submitHandler: function(f) {
      $saveBtn.prop('disabled', true).text('Guardando...');
      $.ajax({
        url: facturacion_path+'ajax.php',
        type: 'POST',
        data: 'editar=12&'+$(f).serialize(),
        success: function(m) {
          if (m=='ok') {
            $('#actualizar').click();
            LOGISTICA.Content.returnToMain();
          } else {
            $saveBtn.prop('disabled', false).text('Guardar');
            LOGISTICA.Dialog.open('Error',m,true);
          }
        }
      })
    }
  });
  $('#estado').change(function() {
    if ($(this).val() == 'Abierta') {
      ObtenerGuias();
      $('#div_guias,#agregar_guias').show();
      $('.estado_pagada').hide();
    } else if ($(this).val() == 'Cerrada') {
      $('#div_guias,#agregar_guias,.estado_pagada').hide();
    }
  }).change();
}());
</script>
