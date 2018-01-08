<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PRODUCTOS_EDITAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
	include Logistica::$root."mensajes/id.php";
	exit;
}
if (! $producto = Producto::find($_GET['id'])) exit('No existe el producto.');
?>
<script>
$(function() {
	$('#nombre').focus();
	$( "#guardar" ).button({icons: {primary:'ui-icon-circle-check'}});

	$( "#regresar" ).click(function() {
		regresar();
	});

	$('#EditarProducto').validate({
		rules: {
			'producto[id]': {required: true, digits: true},
			'producto[nombre]': 'required',
			'producto[tipo]': 'required'
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("td").next("td") );
		},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function() {
			$('#guardar').button('disable').button('option','label','Guardando...');
			$.ajax({
				url: productos_path+'ajax.php',
				type: "POST",
				data: 'editar=101&'+$('#EditarProducto').serialize(),
				success: function(msj) {
					if (! msj) {
						regresar();
					} else {
						$('#guardar').button('enable').button('option','label','Guardar');
						$('#mensaje').html(msj).slideDown(600).delay(6000).fadeOut(600);
					}
				}
			});
		}
	});
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Editar Producto <?= $producto->id ?></h2>
<hr class="hr-small">
<form id="EditarProducto" name="EditarProducto" method="post" action="#">
	<input type="hidden" name="producto[id]" readonly="readonly" id="id" value="<?= $producto->id ?>" />
	<table>
		<tr>
			<td><b>Nombre del Producto:</b></td>
			<td>
				<textarea rows="6" cols="50" id="nombre" name="producto[nombre]"><?= $producto->nombre ?></textarea>
			</td>
			<td></td>
		</tr>
		<tr>
			<td><b>Tipo</b></td>
			<td>
				<select id="tipo" name="producto[tipo]">
					<?php
					foreach (Producto::$tipos as $tipo) {
						$s = $tipo == $producto->tipo ? 'selected="selected"' : '';
						echo '<option '.$s.'>'.$tipo.'</option>';
					}
					?>
				</select>
			</td>
			<td></td>
		</tr>
	</table>
	<center class="form-actions"><button id="guardar">Guardar</button></center>
</form>
<div id="mensaje" style="display: none;padding:5px;margin:2px" class="ui-state-highlight center ui-corner-all"></div>
