<?php
$raiz="../../";
require_once $raiz."seguridad.php";

if( !isset($_SESSION['permisos'][PLANILLAS_ANULAR]) ) {
	include_once $raiz."mensajes/permiso.php";
	exit;
}
if (!isset($_GET['idplanilla']) or !nonce_is_valid($_GET[NONCE_KEY],$_GET['idplanilla'])) {
	include_once $raiz."mensajes/id.php";
	exit;
}
?>
<script>
$(function(){
	var ruta = 'logistica/manifiestos/';
	$('#anular').button({icons: {primary: 'ui-icon-circle-check'}});
	$('#AnularPlanilla').validate({
		rules: {
			comentario: {required: true, minlength: 20},
			motivo: 'required'
		},
		messages: {
			comentario: {required: 'Escribe un comentario.', minlength: 'Minimo 20 caracteres.'},
			motivo: 'Selecciona un motivo'
		},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function(form) {
			$('#mensaje').slideUp();
			$('#anular').button('disable').button('option','label','Anulando...');
			$.ajax({
				url: ruta+'ajax.php',
				type: "POST",
				data: 'anular=101&'+$(form).serialize(),
				success: function(msj){
					if(msj == "ok"){
						$(".right_content").load(ruta+'?'+$('#BuscarPlanillas').serialize(), function(){
							$('#dialog').dialog('close');
						});
					}else{
						$('#anular').button('enable').button('option','label','Anular');
						$('#mensaje').html(msj).slideDown(800);
					}
				}
			});
		}
	});
	$('#comentario').focus();
});
</script>
<form id="AnularPlanilla">
	<table>
		<tr>
			<td><b>¿Porqué quieres anular este manifiesto?</b><br>
			<textarea name="comentario" id="comentario" style="width: 94%" rows="6"></textarea></td>
		</tr>
		<tr>
			<td>
				<b>Motivo Anulación</b><br>
				<select name="motivo" id="motivo">
					<option value="">Selecciona...</option>
					<option value="A">Accidente</option>
					<option value="D">Varada</option>
					<option value="C">Cambio del conductor</option>
					<option value="R">Cambio del Remolque/Semiremolque</option>
					<option value="T">Cambio total del vehículo</option>
					<option value="V">Cambio del cabezote del vehículo</option>
					<option value="CR">Cambio de remesas</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><small>Ten en cuenta que ésta acción será registrada.</small></td>
		</tr>
	</table>
	<hr class="hr-small">
	<button type="submit" id="anular">Anular</button>
	<input type="hidden" name="id" value="<?= $_GET['idplanilla'] ?>" />
	<?php nonce_create_form_input($_GET['idplanilla']) ?>
</form>
<div id="mensaje" class="ui-state-highlight" style="display: none;"></div>
