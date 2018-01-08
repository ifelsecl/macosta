<?php
require '../../seguridad.php';
if (!isset($_REQUEST['id']) or ! nonce_is_valid($_REQUEST[NONCE_KEY], $_REQUEST['id'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][VENDEDORES_EDITAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

if (! $vendedor = Vendedor::find($_REQUEST['id'])) {
	include Logistica::$root.'mensajes/error_cargando.php';
	exit;
}
?>
<script>
$(function() {
	$("#guardar").button({icons: {primary: 'ui-icon-circle-check'}});
	$('#nombre').focus();
	$('#nombre_ciudad').autocomplete({
		autoFocus:true, minLength:4,
		selectFirst: true,
		source: helpers_path+"ajax.php?ciudad=1",
		select: function(event,ui) {
			$('#id_ciudad').val(ui.item.id);
		}
	});

	$("#regresar").click(function() {
		$('#cargando').show();
		cargarPrincipal(vendedores_path);
	});
	$('#Editar').validate({
		rules: {
			nombre: 'required',
			cedula: {required: true, digits: true},
			id_ciudad: 'required',
			direccion: 'required',
			telefono: 'required',
			email: {email: true},
			codigo_siigo: {required: true, digits: true, rangelength: [4, 4]}
		},
		messages: {
			codigo_siigo: {
				required: 'Escribe el código del vendedor en SIIGO',
				digits: 'Solo numeros',
				rangelength: '4 números, rellena con 0 a la <b>izquierda</b>.'
			}
		},
		errorPlacement: function(er, el) {er.appendTo(el.parent("td").next("td") );},
		highlight: function(i) {$(i).addClass("ui-state-highlight");},
		unhighlight: function(i) {$(i).removeClass("ui-state-highlight");},
		submitHandler: function(form) {
			$('#m').hide();
			$('#guardar').button('disable').button('option','label','Guardando...');
			$.ajax({
				url: vendedores_path+'ajax.php', type: "POST",
				data: 'e=101&'+$("#Editar").serialize(),
				success: function(msj) {
					if (msj == 'ok') {
						cargarPrincipal(vendedores_path);
					}else{
						$('#m').html(msj).slideDown(600).delay(6000).slideUp(600);
						$('#guardar').button('enable').button('option','label','Guardar');
					}
				}
			});
		}
	});
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Editar Vendedor</h2>
<hr class="hr-small">
<form id="Editar" method="post" action="#">
	<table>
		<tr>
			<td><b>Nombre:</b></td>
			<td><input type="text" name="nombre" id="nombre" value="<?= $vendedor->nombre ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Cédula:</b></td>
			<td><input type="text" name="cedula" id="cedula" value="<?= $vendedor->cedula ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Ciudad:</b></td>
			<td>
				<input title="Escribe para empezar a buscar..." type="text" id="nombre_ciudad" name="nombre_ciudad" value="<?= $vendedor->ciudad ?>" />
				<input type="hidden" id="id_ciudad" name="id_ciudad" value="<?= $vendedor->id_ciudad ?>" />
			</td>
			<td></td>
		</tr>
		<tr>
			<td><b>Direccion:</b></td>
			<td><input type="text" name="direccion" id="direccion" value="<?= $vendedor->direccion ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Teléfono:</b></td>
			<td><input type="text" name="telefono" id="telefono" value="<?= $vendedor->telefono ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Email:</b></td>
			<td><input type="text" name="email" id="email" value="<?= $vendedor->email ?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Código SIIGO:</b></td>
			<td><input type="text" name="codigo_siigo" maxlength="4" size="4" id="codigo_siigo" value="<?= $vendedor->codigo_siigo ?>" /></td>
			<td></td>
		</tr>
	</table>
	<center class="form-actions">
		<button id="guardar" type="submit">Guardar</button>
	</center>
	<input type="hidden" name="id" value="<?= $vendedor->id ?>" />
	<?php nonce_create_form_input($vendedor->id) ?>
</form>
<div class="ui-state-highlight ui-corner-all" style="display: none; padding: 2px 7px;" id="m"></div>
