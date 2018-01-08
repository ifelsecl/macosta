<form id="mantenimiento_guardar" class="form-horizontal no-margin">
  <?php
  if (! is_null($mantenimiento->id)) { ?>
  <input type="hidden" name="id" value="<?= $mantenimiento->id ?>">
  <?php } ?>
  <div class="control-group">
    <label class="control-label" for="nombre"><b>Nombre:</b></label>
    <div class="controls">
      <input type="text" name="nombre" id="nombre" value="<?= $mantenimiento->nombre ?>" />
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="km"><b>Kilometraje:</b></label>
    <div class="controls">
      <input type="text" name="km" id="km" value="<?= $mantenimiento->kilometraje ?>" />
    </div>
  </div>
  <div class="form-actions no-margin">
    <button id="g" type="submit">Guardar</button>
  </div>
</form>
<script>
(function(){
  $('#nombre').focus();
  var saveBtn = $('#g').button({icons: {primary: 'ui-icon-circle-check'}});
  $('#mantenimiento_guardar').validate({
    errorClass: "help-inline",
    errorElement: "span",
    rules: {
      nombre: 'required',
      km: {required: true, digits: true}
    },
    highlight: function(inp) {$(inp).closest('.control-group').addClass("error");},
    unhighlight: function(inp) {$(inp).closest('.control-group').removeClass("error");},
    submitHandler: function(f) {
      saveBtn.button('disable').button('option','label','Guardando...');
      $.ajax({
        url: mantenimientos_path+'ajax.php', type: 'POST',
        data: '<?= is_null($mantenimiento->id) ? "create" : "update" ?>=1&'+$(f).serialize(),
        success: function(m){
          if (! m) {
            cargarExtra(mantenimientos_path);
            cerrarDialogo();
          } else {
            saveBtn.button('enable').button('option','label','Guardar');
            alertify.error(m);
          }
        }
      })
    }
  })
})();
</script>
