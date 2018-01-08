<?php
require "../../seguridad.php";
if (! isset($_GET['numero_identificacion']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['numero_identificacion'])) {
	include Logistica::$root."mensajes/id.php";
	exit;
}
if (! isset($_SESSION['permisos'][CONDUCTORES_EDITAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

$conductor = new Conductor;
if (! $conductor->find($_GET['numero_identificacion'])) exit('No existe el conductor');
?>
<script>
$(function() {
	$('#guardar').button({icons: {primary: "ui-icon-circle-check"}});

	$("#vencimientopase").datepicker({
		showOn: "both",
		autoSize: true,
		buttonText: 'Seleccionar...',
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});

	$('#nombreciudad').autocomplete({
		minLength: 4,
		autoFocus:true,
		source: helpers_path+'ajax.php?ciudad=si',
		select: function(event, ui) {
			$('#idciudad').val(ui.item.id);
		}
	});

	$('#EditarConductor').validate({
		rules: {
			numero_identificacion: {required: true,digits: true, minlength: 5, maxlength: 15},
			nombre: {required: true, maxlength: 100},
			primer_apellido: {required: true, maxlength: 100},
			segundo_apellido: {required: true, maxlength: 100},
			telefono: {required: true, digits: true, maxlength: 7},
			celular: {digits: true, maxlength: 10},
			direccion: {required: true, maxlength: 60},
			idciudad: 'required',
			categoria: {required: true},
			licencia: {required: true, maxlength: 15, minlength: 5},
			vencimientopase: {required: true}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("td").next("td") );
		},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function() {
			var data = 'editar=1&'+$('#EditarConductor').serialize();
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: conductores_path+'ajax.php',
        type: "POST", data: data,
        success: function(msj) {
          if (! msj) {
            alertify.success('Conductor editado correctamente');
            regresar();
          } else {
            $('#guardar').button('enable').button('option','label','Guardar');
            alertify.error(msj);
          }
        }
      });
		}
	});
	$('#nombre').focus();
});
</script>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Editar Conductor</h2>
<hr class="hr-small">
<form id="EditarConductor" name="EditarConductor" method="post" action="">
	<table>
		<tr>
			<td><b>Tipo de identificación</b></td>
			<td>
				<select id="tipo_identificacion" name="tipo_identificacion">
				<?php
				foreach (Conductor::$tipos_identificacion as $key => $value) {
					$s = $key == $conductor->tipo_identificacion ? 'selected="selected"' : '';
					echo '<option value="'.$key.'" '.$s.'>'.$value.'</option>';
				}
				?>
				</select>
			</td>
		</tr>
    	<tr>
      		<td><b>Núumero de identificación</b></td>
        	<td>
          		<input readonly="readonly" class="ui-state-disabled" maxlength="15" type="text" name="numero_identificacion" id="numero_identificacion" value="<?= $conductor->numero_identificacion ?>" />
        	</td>
        	<td></td>
      	</tr>
      	<tr>
        	<td><b>Nombre:</b></td>
        	<td>
          		<input maxlength="20" type="text" name="nombre" id="nombre" value="<?= $conductor->nombre ?>" />
        	</td>
        	<td></td>
      	</tr>
      	<tr>
      		<td><b>Primer apellido:</b></td>
      		<td><input maxlength="40" type="text" id="primer_apellido" name="primer_apellido" value="<?= $conductor->primer_apellido ?>" /></td>
      		<td></td>
      	</tr>
      	<tr>
      		<td><b>Segundo apellido:</b></td>
      		<td><input maxlength="40" type="text" id="segundo_apellido" name="segundo_apellido" value="<?= $conductor->segundo_apellido ?>" /></td>
      		<td></td>
      	</tr>
      	<tr>
      		<td><b>Teléfono:</b></td>
        	<td><input maxlength="7" type="text" name="telefono" id="telefono" value="<?= $conductor->telefono ?>" /></td>
        	<td></td>
        </tr>
        <tr>
			<td><b>Celular:</b></td>
			<td><input maxlength="10" type="text" name="celular" id="celular" value="<?= $conductor->celular ?>" /></td>
			<td></td>
		</tr>
      <tr>
    		<td><b>Dirección:</b></td>
      	<td><input maxlength="60" type="text" name="direccion" id="direccion" value="<?= $conductor->direccion ?>" /></td>
      	<td></td>
    	</tr>
    	<tr>
    		<td><b>Ciudad:</b></td>
    		<td>
    			<input type="text" id="nombreciudad" name="nombreciudad" value="<?= $conductor->ciudad()->nombre ?>" />
    			<input type="hidden" id="idciudad" name="idciudad" value="<?= $conductor->idciudad ?>" />
    		</td>
    		<td></td>
    	</tr>
    	<tr>
    		<td><b>Categoría licencia:</b><br><small>Ej. 6 - 110012211776</small></td>
    		<td>
    			<?php $licencia = explode("-", $conductor->categorialicencia) ?>
			<select class="input-medium" name="categoria" id="categoria">
			<?php
			foreach (Conductor::$categorias as $key => $value) {
				$s = $licencia[0] == $key ? 'selected="selected"' : '' ;
				echo '<option value="'.$key.'" '.$s.'>'.$value.'</option>';
			}
			?>
			</select>
			-<input type="text" class="input-small" id="licencia" name="licencia" maxlength="15" value="<?= $licencia[1] ?>" />
    		</td>
    		<td></td>
    	</tr>
    	<tr>
      	<td><b>Fecha de vencimiento:</b></td>
      	<td>
      		<input readonly="readonly" class="input-small" type="text" name="vencimientopase" id="vencimientopase" value="<?= $conductor->vencimientopase?>" />
      	</td>
      	<td></td>
    	</tr>
  </table>
  <center class="form-actions"><button id="guardar" type="submit">Guardar</button></center>
</form>
