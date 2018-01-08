<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CONDUCTORES_CREAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
?>
<script>
$(function(){
	$("#guardar").button({icons: {primary: 'ui-icon-circle-check'}});
	$('#numero_identificacion').focus();
	$('#nombre_ciudad').autocomplete({
		minLength:4,
		autoFocus:true,
		source: helpers_path+'ajax.php?ciudad=1',
		select: function(event, ui) {
			$('#idciudad').val(ui.item.id);
			$('#categoria').focus();
		}
	});

	$("#vencimiento_pase").datepicker({
		autoSize:true,
		showOn: "both",
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		buttonText: 'Seleccionar...'
	});
	$('#CrearConductor').validate({
		rules: {
			numero_identificacion: {required: true,digits: true, minlength: 5, maxlength: 15},
			nombre: {required: true, maxlength: 100},
			primer_apellido: {required: true, maxlength: 100},
			segundo_apellido: {required: true, maxlength: 100},
			telefono: {required: true, digits: true, maxlength: 7},
			celular: {digits: true, maxlength: 10},
			direccion: {required: true, maxlength: 60},
			idciudad: 'required',
			categoria: 'required',
			licencia: {required: true, maxlength: 15, minlength: 5},
			vencimientopase: {required: true}
		},
		errorPlacement: function(error, element) {error.appendTo( element.parent("td").next("td") );},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function(form) {
			var data = 'guardar=1&'+$(form).serialize();
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: conductores_path+'ajax.php',
        type: "POST", data: data,
        success: function(msj){
          if (! msj) {
            alertify.success('Conductor creado correctamente');
            regresar();
          } else {
            $('#guardar').button('enable').button('option','label','Guardar');
            alertify.error(msj);
          }
        }
      });
		}
	});
});
</script>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Nuevo Conductor</h2>
<hr class="hr-small">
<form id="CrearConductor" name="CrearConductor" method="post" action="#">
	<table>
		<tr>
			<td><b>Tipo de identificación</b></td>
			<td>
				<select id="tipo_identificacion" name="tipo_identificacion">
				<?php
				foreach (Conductor::$tipos_identificacion as $key => $value) {
					echo '<option value="'.$key.'">'.$value.'</option>';
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><b>Número de identificación</b></td>
			<td>
				<input maxlength="15" type="text" name="numero_identificacion" id="numero_identificacion" />
			</td>
			<td></td>
		</tr>
		<tr>
			<td><b>Nombre:</b></td>
			<td>
				<input maxlength="100" type="text" name="nombre" id="nombre" />
			</td>
			<td></td>
		</tr>
		<tr>
			<td><b>Primer apellido:</b></td>
			<td><input maxlength="100" type="text" id="primer_apellido" name="primer_apellido" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Segundo apellido:</b></td>
			<td><input maxlength="100" type="text" id="segundo_apellido" name="segundo_apellido" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Teléfono:</b></td>
			<td><input maxlength="7" type="text" name="telefono" id="telefono" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Celular:</b></td>
			<td><input maxlength="10" type="text" name="celular" id="celular" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Dirección:</b></td>
			<td><input maxlength="60" type="text" name="direccion" id="direccion" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Ciudad:</b></td>
			<td>
				<input type="text" id="nombre_ciudad" name="nombre_ciudad" />
				<input type="hidden" id="idciudad" name="idciudad" />
			</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<b>Categoría-Licencia:</b><br><small> Ej. 6 - 110012211776</small>
			</td>
			<td>
				<select class="input-medium" name="categoria" id="categoria">
				<?php
				foreach (Conductor::$categorias as $key => $value) {
					echo '<option value="'.$key.'">'.$value.'</option>';
				}
				?>
				</select>
				-<input class="input-medium" type="text" id="licencia" name="licencia" maxlength="15" />
			</td>
			<td></td>
		</tr>
		<tr>
			<td><b>Fecha de vencimiento:</b></td>
			<td>
				<input readonly="readonly" class="input-small" type="text" name="vencimientopase" id="vencimiento_pase" />
			</td>
			<td></td>
		</tr>
	</table>
	<center class="form-actions"><button id="guardar" type="submit">Guardar</button></center>
</form>
