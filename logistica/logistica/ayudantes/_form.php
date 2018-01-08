<script>
$(function(){
	$('#guardar').button({icons: {primary:'ui-icon-circle-check'}});
	$('#regresar').click(function(){
		regresar();
	});
	$('#ciudad').autocomplete({
		minLength:4,
		autoFocus:true,
		source: helpers_path+'ajax.php?ciudad=1',
		select: function(event, ui) {
			$('#id_ciudad').val(ui.item.id);
		}
	});
	$('#numero_identificacion').focus();
	$('#form_ayudante').validate({
		rules: {
			numero_identificacion: {required: true, digits: true},
			nombre: {required: true},
			id_ciudad: {required: true}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("td").next("td") );
		},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function(form) {
			$('#guardar').button('disable').button('option','label','Guardando...');
			$.ajax({
				url: ayudantes_path+'ajax.php',
				type: "POST",
				data: 'save=1&'+$(form).serialize(),
				success: function(msj){
					if(!msj){
						regresar();
					}else{
						$('#guardar').button('enable').button('option','label','Guardar');
						LOGISTICA.Dialog.open('Error', msj, true);
					}
				}
			});
		}
	});
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2><?= isset($ayudante->id) ? 'Editar Ayudante' : 'Nuevo Ayudante' ?></h2>
<hr class="hr-small">
<form id="form_ayudante" name="form_ayudante" method="post" action="#">
	<table>
		<tr>
			<td><b>Tipo de Identificación</b></td>
			<td>
				<select id="tipo_identificacion" name="tipo_identificacion">
					<?php
					foreach (Ayudante::$tipos_identificacion as $key => $value) {
						$s = (isset($ayudante->id) and $ayudante->tipo_identificacion == $key) ? 'selected="selected"' : '' ;
						echo '<option value="'.$key.'" '.$s.'>'.$value.'</option>';
					}
					?>
				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td><b>Número de Identificación</b></td>
			<td><input type="text" maxlength="12" id="numero_identificacion" name="numero_identificacion" value="<?= isset($ayudante->id) ? $ayudante->numero_identificacion : '' ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Nombre</b></td>
			<td><input type="text" id="nombre" name="nombre" value="<?= isset($ayudante->id) ? $ayudante->nombre : '' ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Ciudad</b></td>
			<td>
				<input type="text" id="ciudad" name="ciudad" value="<?= isset($ayudante->id) ? $ayudante->ciudad : '' ?>" />
				<input type="hidden" name="id_ciudad" id="id_ciudad" value="<?= isset($ayudante->id) ? $ayudante->id_ciudad : '' ?>" />
			</td>
			<td></td>
		</tr>
	</table>
	<center class="form-actions"><button id="guardar">Guardar</button></center>
	<input type="hidden" id="id" name="id" value="<?= isset($ayudante->id) ? $ayudante->id : '' ?>" />
	<?php nonce_create_form_input(isset($ayudante->id) ? $ayudante->id: 'nuevo') ?>
</form>
