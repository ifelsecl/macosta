<?php
$tipos_mantenimiento = VehiculoMantenimiento::$tipos;
$mantenimientos = Mantenimiento::all();
?>
<form id="form-mantenimiento" class="form-horizontal no-margin">
  <?php if (! is_null($vehiculo_mantenimiento->id)): ?>
  <input type="hidden" name="id" id="vehiculo_mantenimiento_id" value="<?= $vehiculo_mantenimiento->id ?>">
  <?php endif; ?>
  <input type="hidden" name="vehiculo_mantenimiento[vehiculo_placa]" value="<?= $vehiculo_mantenimiento->vehiculo_placa ?>">
  <div class="text-center">
    <h2>Vehículo <?= $vehiculo_mantenimiento->vehiculo_placa ?></h2>
  </div>
  <div class="control-group">
    <label class="control-label" for="vehiculo_mantenimiento_mantenimiento_id">Mantenimiento:</label>
    <div class="controls">
      <select id="vehiculo_mantenimiento_mantenimiento_id" name="vehiculo_mantenimiento[mantenimiento_id]">
        <option value="">Selecciona...</option>
        <?php
        foreach ($mantenimientos as $m) {
          $s = $vehiculo_mantenimiento->mantenimiento_id == $m->id ? 'selected="selected"' : '';
          echo '<option value="'.$m->id.'" '.$s.'>'.$m->nombre.' ('.$m->kilometraje.' km)</option>';
        }
        ?>
      </select>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="vehiculo_mantenimiento_fecha">Fecha:</label>
    <div class="controls">
      <input type="text" id="vehiculo_mantenimiento_fecha" name="vehiculo_mantenimiento[fecha]" readonly="readonly" class="input-small" value="<?= $vehiculo_mantenimiento->fecha ?>" />
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="vehiculo_mantenimiento_tipo">Tipo:</label>
    <div class="controls">
      <select id="vehiculo_mantenimiento_tipo" name="vehiculo_mantenimiento[tipo]">
        <option value="">Selecciona...</option>
        <?php
        foreach($tipos_mantenimiento as $tipo) {
          $s = $vehiculo_mantenimiento->tipo == $tipo ? 'selected="selected"' : '';
          echo '<option '.$s.'>'.$tipo.'</option>';
        }
        ?>
      </select>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="vehiculo_mantenimiento_precio">Precio:</label>
    <div class="controls">
      <input type="text" id="vehiculo_mantenimiento_precio" name="vehiculo_mantenimiento[precio]" value="<?= $vehiculo_mantenimiento->precio ?>" />
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="vehiculo_mantenimiento_observacion">Observación:</label>
    <div class="controls">
      <textarea id="vehiculo_mantenimiento_observacion" name="vehiculo_mantenimiento[observacion]"><?= $vehiculo_mantenimiento->observacion ?></textarea>
    </div>
  </div>
  <div class="form-actions no-margin">
  <button class="btn btn-primary" id="save-mantenimiento">Guardar</button>
  </div>
</form>
<script>
(function() {
  var Mantenimiento = {
    id: $('#vehiculo_mantenimiento_id').val(),
    $el: $('#form-mantenimiento'),
    $saveBtn: $('#form-mantenimiento #save-mantenimiento'),
    init: function() {
      this.initForm();
      this.initDatePicker();
    },
    initDatePicker: function() {
      this.$el.find('#vehiculo_mantenimiento_fecha').datepicker({
        autoSize: true,
        showOn: "both",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        buttonText: 'Seleccionar...',
        gotoCurrent: true,
        hideIfNoPrevNext: true,
        maxDate: 0
      });
    },
    initForm: function() {
      var _this = this;
      _this.$el.validate({
        rules: {
          'vehiculo_mantenimiento[mantenimiento_id]': 'required',
          'vehiculo_mantenimiento[fecha]': 'required',
          'vehiculo_mantenimiento[tipo]': 'required',
          'vehiculo_mantenimiento[precio]': {required: true, number: true},
          'vehiculo_mantenimiento[observacion]': 'required'
        },
        errorPlacement: function(er, el) {},
        highlight: function(inp) {$(inp).closest('.control-group').addClass("error");},
        unhighlight: function(inp) {$(inp).closest('.control-group').removeClass("error");},
        submitHandler: function(form) {
          _this.$saveBtn.text('Guardando...').prop('disabled', true);
          $.ajax({
            url: vehiculos_path+'mantenimientos/ajax.php',
            type: 'POST', dataType: 'json',
            data: '<?= is_null($vehiculo_mantenimiento->id) ? "crear" : "editar" ?>=1&'+$(form).serialize(),
            success: function(response) {
              if (response.success) {
                alertify.success('Mantenimiento guardado correctamente.');
                cerrarDialogo();
                if (_this.id) {
                  $('table#vehiculo-mantenimientos tr#mantenimiento-'+_this.id).replaceWith(response.html);
                } else {
                  $('table#vehiculo-mantenimientos tbody').append(response.html);
                }
              } else {
                _this.$saveBtn.text('Guardar').prop('disabled', false);
                alertify.error(response.message);
              }
            }
          });
        }
      });
    }
  };
  Mantenimiento.init();
})();
</script>
