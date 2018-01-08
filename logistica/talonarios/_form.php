<button onClick="regresar()" class="btn btn-success pull-right">Regresar</button>
<h2><?= is_null($talonario->id) ? 'Asignar' : 'Editar' ?> Talonario</h2>
<hr class="hr-small">
<form id="save-talonario" method="POST">
	<?php if (! is_null($talonario->id)) {
		echo '<input type="hidden" name="id" value="'.$talonario->id.'" >';
		nonce_create_form_input($talonario->id);
	}
	?>
	<table>
		<tr>
			<th>Fecha Entrega</th>
			<td><input type="text" class="input-small" readonly="readonly" id="talonario_fecha_entrega" name="talonario[fecha_entrega]" value="<?= $talonario->fecha_entrega ?>" /></td>
		</tr>
		<tr>
			<th>Conductor</th>
			<td>
				<select name="talonario[conductor_numero_identificacion]">
					<?php
					$conductores = Conductor::all('activos');
					foreach ($conductores as $c) {
						$s = $talonario->conductor_numero_identificacion == $c->numero_identificacion ? 'selected="selected"' : '';
						echo '<option value="'.$c->numero_identificacion.'" "'.$s.'">'.$c->nombre_completo.'</option>';
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th>Rango:</th>
			<td>
				<input type="text" id="talonario_inicio" placeholder="Inicio" name="talonario[inicio]" value="<?= $talonario->inicio ?>" class="input-mini" >
				-
				<input id="talonario_fin" placeholder="Fin" type="text" value="<?= $talonario->fin ?>" name="talonario[fin]" class="input-mini" >
			</td>
		</tr>
	</table>
	<div class="form-actions">
		<button id="save-talonario" class="btn btn-primary">Guardar</button>
	</div>
</form>
<script>
(function() {
	$('#talonario_fecha_entrega').datepicker({
		autoSize: true,
		showOn: "both",
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		buttonText: 'Seleccionar...'
	});

	$('#save-talonario').validate({
		rules: {
			'talonario[fecha_entrega]': 'required',
			'talonario[conductor_numero_identificacion]': 'required',
			'talonario[inicio]': {
				required: true, digits: true,
			},
			'talonario[fin]': {
				required: true, digits: true,
				min: function() { return $('#talonario_inicio').val() }
			}
		},
		errorPlacement: function(er, el) {},
		highlight: function(el) { $(el).addClass("ui-state-highlight") },
		unhighlight: function(el) { $(el).removeClass("ui-state-highlight") },
		submitHandler: function(form) {
			$('button#save-talonario').text('Guardando...').prop('disabled', true);
			$.ajax({
				type: $(form).attr('method'),
				url: talonarios_path+'ajax.php',
				data: $(form).serialize(),
				success: function(response) {
					if (! response) {
						regresar();
					} else {
						alertify.log(response);
						$('button#save-talonario').text('Guardar').prop('disabled', false);
					}
				}
			});
		}
	});
})();
</script>
