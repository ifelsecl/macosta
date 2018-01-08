<?php
require "../seguridad.php";
if (! isset($_GET['idfactura']) or ! isset($_GET[NONCE_KEY]) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['idfactura'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][FACTURACION_ELIMINAR]) and ! isset($_SESSION['permisos'][FACTURACION_ANULAR])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
if (! $factura = Factura::find($_GET['idfactura'])) exit('No existe la factura');
?>
<script>
$(function() {
	$('#comentario').focus();
	function Eliminar(accion) {
		$('#mensaje').hide();
		$('#anular, #eliminar').button('disable');
		var label = $('#'+accion).button('option', 'label');
		var text = 'eliminar' == accion ? 'Eliminando...' : 'Anulando...' ;
		$('#'+accion).button('option','label', text);
		$.ajax({
			url: facturacion_path+'ajax.php', dataType: 'json', type: 'POST',
			data: accion+'=si&'+$('#FormEliminar').serialize(),
			success: function(r) {
				if (r.error == 'no') {
					$('#cargando').show();
					$(".center_content").load(facturacion_path,function() {
						$('#dialog').dialog('close');
					});
				} else {
					$('#'+accion).button('option', 'label', label);
					$('#mensaje').html(r.mensaje).slideDown(600);
				}
			}
		});
	}
	$('#eliminar').button({icons: {primary: 'ui-icon-trash'}});
	$('#anular').button({icons: {primary: 'ui-icon-locked'}});
	$('#eliminar, #anular').click(function() {
		if ($('#FormEliminar').valid()) {
			Eliminar(this.id);
		}
	});

	$('#FormEliminar').validate({
		rules: {comentario: {required: true, minlength: 20}},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");}
	});
});
</script>
<form id="FormEliminar" action="#">
	<table cellspacing="0" cellpadding="2" border="0">
		<tr class="text-center">
			<td><b>¿Porqué quieres eliminar la factura <?= $factura->id ?>?</b></td>
		</tr>
		<tr>
			<td class="text-center"><textarea style="width: 95%" id="comentario" name="comentario" rows="6" cols="40"></textarea></td>
		</tr>
		<tr>
			<td class="text-center"><small>Ten en cuenta que ésta acción será registrada.</small></td>
		</tr>
	</table>
	<hr class="hr-small">
	<?= nonce_create_form_input($_GET['idfactura']) ?>
	<input type="hidden" name="idfactura" value="<?= $factura->id ?>" />
</form>
<center>
	<?php
	if (isset($_SESSION['permisos'][FACTURACION_ANULAR]) and $factura->activa == 'si') {
		echo '<button title="La factura no será elminada, las guías serán liberadas, pero la factura no podrá ser usada." id="anular">Anular</button>';
	}
	if (isset($_SESSION['permisos'][FACTURACION_ELIMINAR])) {
		echo '<button title="La factura será eliminada definitivamente (Esta acción no se puede deshacer). Podrás reutilizar su número para generar una nueva factura." id="eliminar">Eliminar</button>';
	}
	?>
</center>
<div id="mensaje" class="ui-state-highlight" style="display: none;"></div>
