<form id="guardar-embalaje" class="form-horizontal no-margin">
  <div class="control-group">
    <label class="control-label" for="nombre"><b>Nombre:</b></label>
    <div class="controls">
      <input type="text" name="nombre" id="nombre" value="<?= $embalaje->nombre ?>">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="descripcion">
      <b>Descripción:</b><br>
      <small class="muted">Opcional</small>
    </label>
    <div class="controls">
      <textarea cols="30" rows="4" name="descripcion" id="descripcion"><?= $embalaje->descripcion ?></textarea>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="tipo_cobro"><b>Tipo de Cobro:</b></label>
    <div class="controls">
      <select name="tipo_cobro" id="tipo_cobro">
        <?php
        foreach(Embalaje::$types as $type) {
          $s = $type['nombre'] == $embalaje->tipo_cobro ? 'selected="selected"' : '';
          echo '<option title="'.$type['descripcion'].'" '.$s.'>'.$type['nombre'].'</option>';
        }
        ?>
      </select>
      <span><small class="help-block">Mantén el ratón sobre las opciones para ver su descripción.</small></span>
    </div>
  </div>
  <div class="form-actions no-margin">
    <button type="submit" id="guardar">Guardar</button>
  </div>
  <?php
  if (! is_null($embalaje->id)) {
    echo '<input type="hidden" name="id" value="'.$embalaje->id.'" />';
    nonce_create_form_input($embalaje->id);
  }
  ?>
</form>
<script>
(function() {
  $('#guardar').button({ icons: {primary: 'ui-icon-circle-check'}});
  $('#nombre').focus();
  $('#guardar-embalaje').validate({
    rules: {nombre: 'required', tipo_cobro: 'required'},
    errorPlacement: function(er, el) {},
    highlight: function(inp) {$(inp).closest('.control-group').addClass("error");},
    unhighlight: function(inp) {$(inp).closest('.control-group').removeClass("error");},
    submitHandler: function(form) {
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: embalajes_path+'ajax.php',
        type: 'POST',
        data: '<?= is_null($embalaje->id) ? "guardar" : "editar" ?>=1&'+$(form).serialize(),
        success: function(m) {
          if (! m) {
            cargarPrincipal(embalajes_path+'index.php?pagina='+$('#pag').val(), cerrarDialogo);
          } else {
            $('#guardar').button('enable').button('option','label','Guardar');
            LOGISTICA.Dialog.open('Error',m,true);
          }
        }
      });
    }
  });
})();
</script>
