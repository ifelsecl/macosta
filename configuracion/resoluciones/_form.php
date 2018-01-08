<button class="btn btn-success pull-right" onclick="regresar();">Regresar</button>
<h2><?= is_null($resolucion->id) ? 'Nueva Resolución' : 'Editar Resolución' ?></h2>
<hr class="hr-small">
<form id="form_resolucion" method="post" action="#">
	<?php
	if (! is_null($resolucion->id)) {
		echo '<input type="hidden" name="id" value="'.$resolucion->id.'">';
		nonce_create_form_input($resolucion->id);
	}
	?>
	<dl class="dl-horizontal">
		<dt>Número:</dt>
		<dd><input type="text" name="resolucion[numero]" value="<?= $resolucion->numero ?>"></dd>
		<dt>Tipo:</dt>
		<dd>
			<select name="resolucion[tipo]">
				<option value="">Selecciona...</option>
				<?php
				foreach (Resolucion::$tipos as $tipo) {
					$s = $tipo == $resolucion->tipo ? 'selected="selected"' : '';
					echo '<option value="'.$tipo.'" '.$s.'>'.ucfirst($tipo).'</option>';
				}
				?>
			</select>
		</dd>
		<dt>Fecha:</dt>
		<dd><input type="text" name="resolucion[fecha]" id="resolucion_fecha" class="input-small" value="<?= $resolucion->fecha ?>"></dd>
		<dt>Rango:</dt>
		<dd>
			<input type="text" name="resolucion[inicio]" id="resolucion_inicio" class="input-small" placeholder="Inicio" value="<?= $resolucion->inicio ?>">
			<input type="text" name="resolucion[fin]" id="resolucion_fin" class="input-small" placeholder="Fin" value="<?= $resolucion->fin ?>">
		</dd>
	</dl>
	<div class="form-actions text-center">
		<button class="btn btn-primary">Guardar</button>
	</div>
</form>
<script>
(function() {
	var resolucion = {
		$el: $('#form_resolucion'),
		init: function() {
			this.$el.find("#resolucion_fecha").datepicker({
				autoSize: true,
				showOn: "both",
				buttonImage: "css/images/calendar.gif",
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				dateFormat: 'yy-mm-dd',
				buttonText: 'Seleccionar...',
				maxDate: 0
			});
			this.$el.validate({
				rules: {
					'resolucion[numero]': {required: true, digits: true},
					'resolucion[tipo]': {required: true},
					'resolucion[fecha]': {required: true},
					'resolucion[inicio]': {
						required: true,
						digits: true,
						max: function() {return $('#resolucion_fin').val()}
					},
					'resolucion[fin]': {
						required: true,
						digits: true,
						min: function() {return $('#resolucion_inicio').val()}
					}
				},
				errorPlacement: function(error, element) {return false;},
				highlight: function(input) {$(input).addClass("ui-state-highlight");},
				unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
				submitHandler: function(form) {
					var btn = resolucion.$el.find('button');
					btn.attr('disabled', 'disabled').text('Guardando...');
					$.ajax({
						url: resoluciones_path+'ajax.php',
						type: 'POST',
						data: $(form).serialize(),
						success: function(response) {
							if (! response) {
								cargarPrincipal(resoluciones_path);
								regresar();
							} else {
								btn.removeAttr('disabled').text('Guardar');
								alertify.error(response);
							}
						}
					});
				}
			});
			this.$el.find('input:first').focus();
		}
	};
	resolucion.init();
})();
</script>
