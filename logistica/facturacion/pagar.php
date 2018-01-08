<?php
require '../seguridad.php';
$factura = Factura::find($_GET['idfactura']);
$payment_types = Pago::$types;
?>
<div class="row-fluid" id="factura__pagar">
  <div class="span12">
    <div class="row-fluid">
      <div class="span9">
        <h2>Pagar Factura <?= $factura->id ?></h2>
      </div>
      <div class="span3 btn-toolbar">
        <button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
      </div>
    </div>

    <div class="row-fluid">
      <div class="span12">
        <?php if ($factura->is_paid()) { ?>
        <p class="alert alert-success"><b>LA FACTURA HA SIDO PAGADA</b></p>
        <?php } ?>
        <form class="form-horizontal">
          <input type="hidden" name="pago[factura_id]" value="<?= $factura->id ?>">
          <div class="control-group">
            <label class="control-label"><b>Cliente</b></label>
            <div class="controls">
              <?= $factura->cliente_nombre_completo ?>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label"><b>Total</b></label>
            <div class="controls">
              $ <?= number_format($factura->total) ?>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label"><b>Pagos</b></label>
            <div class="controls">
              $ <?= number_format($factura->total_pagos) ?>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label"><b>Saldo</b></label>
            <div class="controls">
              $ <?= number_format($factura->saldo()) ?>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="pago__valor"><b>Valor</b></label>
            <div class="controls">
              <input type="text" id="pago__valor" name="pago[valor]">
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="pago__tipo"><b>Tipo</b></label>
            <div class="controls">
              <select id="pago__tipo" name="pago[tipo]">
                <?php
                foreach ($payment_types as $type) {
                  echo '<option value="'.$type.'">'.ucfirst($type).'</option>';
                }
                ?>
              </select>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="pago__fecha"><b>Fecha</b></label>
            <div class="controls">
              <input type="text" id="pago__fecha" name="pago[fecha]">
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="pago__notas"><b>Notas</b></label>
            <div class="controls">
              <textarea type="text" id="pago__notas" name="pago[notas]"></textarea>
            </div>
          </div>

          <div class="form-actions">
            <button class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
(function() {
  LOGISTICA.PagarFactura = (function() {
    var $el      = $('#factura__pagar'),
        $form    = $el.find('form'),
        $saveBtn = $form.find('button'),
        isPaid   = <?= $factura->is_paid() ? 'true' : 'false' ?>;

    var init = function() {
      initFormValidation();
      initDatepicker();
      removeFormIfPaid();
    };

    var initDatepicker = function() {
      $el.find('#pago__fecha').datepicker({
        changeMonth: true,
        changeYear: true,
        showOn: "both",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        dateFormat: 'yy-mm-dd',
        buttonText: 'Seleccionar...',
        autoSize: true,
        maxDate: 0
      });
    };

    var initFormValidation = function() {
      $form.validate({
        rules: {
          'pago[valor]': { required: true, number: true },
          'pago[fecha]': 'required',
          'pago[tipo]': 'required',
        },
        submitHandler: function(form) {
          $saveBtn.prop('disabled', true);
          $.ajax({
            type: 'post',
            url: facturacion_path + 'ajax.php',
            data: 'pagar=1&' + $(form).serialize()
          })
          .done(function(response) {
            if (response.success) {
              LOGISTICA.Content.returnToMain();
            } else {
              alert(response.message);
            }
          })
          .always(function() {
            $saveBtn.prop('disabled', false);
          });
        }
      });
    };

    var removeFormIfPaid = function() {
      if (isPaid) $form.remove();
    };

    return {init: init}
  }());

  LOGISTICA.PagarFactura.init();
}());
</script>
