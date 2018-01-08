<?php
$raiz = '../../';
require $raiz.'seguridad.php';
?>
<form class="form-inline" id="manifiestos_marcar" action="#" style="padding: 5px">
	<input type="hidden" name="id" value="<?= $_GET['id'] ?>">
	<table>
		<tr>
			<td>Fecha Entrega:</td>
			<td><input readonly="readonly" type="text" class="input-small" name="fecha_entrega" id="manifiesto_fecha_entrega" value="<?= date('Y-m-d') ?>" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><button id="manifiesto_guardar">Guardar</button></td>
		</tr>
	</table>
</form>
<script>
$(function(){
	$('#manifiesto_guardar').button({icons: {primary: 'ui-icon-circle-check'}});
	$('#manifiestos_marcar').submit(function(e){
		e.preventDefault();
		$('#manifiesto_guardar').button('disable').button('option', 'label', 'Guardando...');
		$.ajax({
			url: manifiestos_path+'ajax.php',
			data: 'marcar=1&'+$(this).serialize(),
			type: 'POST', dataType: 'json',
			success: function(respuesta){
				if(respuesta.error){
					$('#manifiesto_guardar').button('enable').button('option', 'label', 'Guardar');
					alertify.log(respuesta.mensaje, "error", 8000);
				}else{
					alertify.log(respuesta.mensaje, "success", 15000);
					cerrarDialogo();
				}
			}
		});
	});
	$("#manifiesto_fecha_entrega").datepicker({
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
});
</script>
