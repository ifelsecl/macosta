<script>
$(function() {
	$('#guardar').button({icons: {primary: "ui-icon-circle-check"}});
	$('#nombre_ciudad').autocomplete({
		autoFocus: true,
		minLength: 3,
		source: helpers_path+"ajax.php?ciudad=si",
		select: function(event, ui) {
			$('#id_ciudad').val(ui.item.id);
		}
	});

	<?= Tercero::validar_tipo_identificacion() ?>

	$('#numero_identificacion').change(function() {
		$('#digito_verificacion').val( calcular_dv($(this).val()) );
	});

	$('form#editar_tercero').validate({
		rules: {
			numero_identificacion: {required: true,digits: true, minlength: 5, maxlength: 15},
			primer_apellido: {required: true, maxlength: 100},
			segundo_apellido: {required: true, maxlength: 100},
			nombre: {required: true, maxlength: 100},
			razon_social: {required: true, maxlength: 200},
			telefono: {required: true, maxlength: 7},
			celular: {digits: true, maxlength: 10},
			direccion: {required: true, maxlength: 60},
			id_ciudad: 'required',
			nombre_ciudad: 'required',
			email: {email: true},
			digito_verificacion: {required: true, digits: true, maxlength: 1}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("td").next("td") );
		},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function(form) {
			$('#guardar').button('disable').button('option','label','Guardando...');
			$.ajax({
				type: 'POST',
				url: terceros_path+'ajax.php',
				data: 'action=save&'+$(form).serialize(),
				success: function(response) {
					if (! response) {
						alertify.success('Tercero guardado correctamente');
						regresar();
					} else {
						$('#guardar').button('enable').button('option','label','Guardar');
						alertify.error(response);
					}
				}
			});
		}
	});
});
</script>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2><?= isset($tercero->id) ? "Editar Tercero $tercero->id" : 'Nuevo Tercero' ?></h2>
<hr class="hr-small">
<form id="editar_tercero" action="#">
	<table>
		<tr>
			<td><b>Tipo de identificación</b></td>
			<td>
				<select id="tipo_identificacion" name="tipo_identificacion">
				<?php
				foreach (Tercero::$tipos_identificacion as $id => $value) {
					$s = (isset($tercero->tipo_identificacion) and $tercero->tipo_identificacion == $id) ? 'selected="selected"' : '' ;
					echo '<option value="'.$id.'" '.$s.'>'.$value.'</option>';
				}
				?>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td><b>Número de identificación</b></td>
			<td><input type="text" name="numero_identificacion" id="numero_identificacion" maxlength="15" value="<?= isset($tercero->numero_identificacion) ? $tercero->numero_identificacion : '' ?>" /></td>
			<td></td>
		</tr>
		<tr class="persona">
			<td><b>Primer apellido</b></td>
			<td><input type="text" name="primer_apellido" id="primer_apellido" maxlength="100" value="<?= isset($tercero->primer_apellido) ? $tercero->primer_apellido : '' ?>" /></td>
			<td></td>
		</tr>
		<tr class="persona">
			<td><b>Segundo apellido</b></td>
			<td><input type="text" name="segundo_apellido" id="segundo_apellido" maxlength="100" value="<?= isset($tercero->segundo_apellido) ? $tercero->segundo_apellido : '' ?>" /></td>
			<td></td>
		</tr>
		<tr class="persona">
			<td><b>Nombre</b></td>
			<td><input type="text" name="nombre" id="nombre" maxlength="100" value="<?= isset($tercero->nombre) ? $tercero->nombre : '' ?>" /></td>
			<td></td>
		</tr>
		<tr class="empresa">
			<td><b>Dígito Verificación</b></td>
			<td><input type="text" class="input-mini" name="digito_verificacion" id="digito_verificacion" maxlength="1" value="<?= isset($tercero->digito_verificacion) ? $tercero->digito_verificacion : '' ?>" /></td>
			<td></td>
		</tr>
		<tr class="empresa">
			<td><b>Razón Social</b></td>
			<td><input type="text" name="razon_social" maxlength="100" id="razon_social" value="<?= isset($tercero->razon_social) ? $tercero->razon_social : '' ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Teléfono</b></td>
			<td><input type="text" name="telefono" id="telefono" maxlength="7" value="<?= isset($tercero->telefono) ? $tercero->telefono : '' ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Celular</b></td>
			<td><input type="text" name="celular" id="celular" maxlength="10" value="<?= isset($tercero->celular) ? $tercero->celular : '' ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Dirección</b></td>
			<td><input type="text" name="direccion" id="direccion" maxlength="60" value="<?= isset($tercero->direccion) ? $tercero->direccion : '' ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Ciudad</b></td>
			<td>
				<input type="text" name="nombre_ciudad" id="nombre_ciudad" value="<?= isset($tercero->id_ciudad) ? $tercero->ciudad_nombre : '' ?>" />
				<input type="hidden" name="id_ciudad" id="id_ciudad" value="<?= isset($tercero->id_ciudad) ? $tercero->id_ciudad : '' ?>" />
			</td>
			<td></td>
		</tr>
		<tr>
			<td><b>Email</b></td>
			<td><input type="text" name="email" id="email" value="<?= isset($tercero->email) ? $tercero->email : '' ?>" /></td>
			<td></td>
		</tr>
	</table>
	<center class="form-actions"><button id="guardar" type="submit">Guardar</button></center>
	<input type="hidden" name="id" value="<?= isset($tercero->id) ? $tercero->id : '' ?>" />
</form>
