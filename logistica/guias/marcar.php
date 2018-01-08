<?php require "../../seguridad.php" ?>
<script>
$(function(){
	$('#m_numero').focus();
	$('#m_guardar').button({icons: {primary: 'ui-icon-circle-check'}});
	$('#m_agregar').button({icons: {primary: 'ui-icon-plus'},text:false});
	$("#m_fecha").datepicker({
		autoSize:true,
		showOn: "both",
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		buttonText: 'Seleccionar...',
		maxDate: 0
	});
	$('#F_Agregar').submit(function(e){
		e.preventDefault();
		var g=$('#m_numero').val();
		if(isNaN(g) || g==''){
			$('#m_numero').focus();
			return;
		}
		var t=$('#m_guias').val();
		if(t==''){
			$('#m_guias').val(g);
		}else{
			$('#m_guias').val(t+','+g);
		}
		$('#m_numero').val('').focus();
	});
	$('#F_Guardar').submit(function(e){
		e.preventDefault();
		if($('#m_guias').val()==''){
			alert('No has indicado las guias que quieres marcar como entregadas.');
			$('#m_numero').focus();
			return;
		}
		$('#m_guardar').button('option','label','Guardando...').button('disable');
		$.ajax({
			url: 'logistica/guias/ajax.php', type: 'POST', dataType: 'json',
			data: 'marcar=1&'+$('#F_Guardar').serialize(),
			success: function(m){
				if(m.ok){
					$('#dialog').slideDown(300, function(){
						$('#dialog').html('<p class="expand">¡Las guias se han marcado como entregadas!</p>');
					});
				}else{
					$('#m_guardar').button('option','label','Guardar').button('enable');
					$('#dialog').html('<p>Ha ocurrido un error, verifica los numeros de las guias ingresadas e intentalo nuevamente</p>'+m.mensaje);
				}
			}
		});
	});
});
</script>
<form id="F_Agregar" action="#" class="form-inline">
	<table>
		<tr>
			<td><input type="text" placeholder="Número de guía" id="m_numero" name="guia" /></td>
			<td><button id="m_agregar">Agregar</button></td>
		</tr>
	</table>
</form>
<form id="F_Guardar" action="#">
	<table>
		<tr>
			<td class="form-inline">
				<b>Fecha de Entrega:</b>
				<input type="text" readonly="readonly" class="input-small" id="m_fecha" name="m_fecha" value="<?= date('Y-m-d') ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<b>Guias agregadas:</b><br>
				<textarea style="width:400px;height:220px" id="m_guias" name="m_guias"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<button id="m_guardar">Guardar</button>
			</td>
		</tr>
	</table>
</form>
