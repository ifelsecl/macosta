<?php
$raiz = "../../";
require $raiz."seguridad.php";

if( ! isset($_SESSION['permisos'][GUIAS_REVALORIZAR]) ) {
	include $raiz.'mensajes/permiso.php';
	exit;
}
?>
<script>
$(function(){
	var r='logistica/guias/';
	var rb=$('#r_revalorizar').button({icons: {primary: 'ui-icon-circle-check'}});
	$( "#r_fecha" ).datepicker({
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
	$('#r_cliente').focus().autocomplete({
		autoFocus: true, minLength: 3,
		focus: function(){return false;},
		source: helpers_path+'ajax.php?cliente=1',
		select: function(event, ui) {
			$('#r_id_cliente').val(ui.item.id);
			$('#r_fecha').focus();
		}
	});
	$('#RevalorizarGuias').validate({
		rules:{
			r_id_cliente: 'required',
			r_fecha: 'required'
		},
		messages: {
			r_id_cliente: 'Selecciona un cliente',
			r_fecha: 'Selecciona la fecha'
		},
		submitHandler: function(f){
			$('#mensaje').hide();
			rb.button('disable').button('option','label','Revalorizando...');
			$.ajax({
				url: r+'ajax.php?revalorizar=1&'+$(f).serialize(),
				type:'POST',
				success: function(m){
					if(m=='ok'){
						$('#mensaje_ok').slideDown(500);
						$('#RevalorizarGuias input').val('');
						$('#r_cliente').focus()
					}else{
						$('#mensaje_error').html(m).slideDown(500).delay(6000).slideUp(500);
					}
					rb.button('enable').button('option','label','Revalorizar');
				}
			});
		}
	});
});
</script>
<label class="help">
Revaloriza las guías de un cliente si ha realizado cambios en su lista de precios.<br>
Selecciona un cliente y la fecha a partir de la cual se revalorizarán las guías</label>
<br><br>
<form id="RevalorizarGuias" action="#">
	<table style="width:100%;font-size: 120%">
		<tr>
			<td>
				<b>Cliente:</b> (<small>Escriba el nombre</small>)<br>
				<input type="text" name="r_cliente" id="r_cliente" />
				<input type="hidden" name="r_id_cliente" id="r_id_cliente" />
			</td>
		</tr>
		<tr>
			<td>
				<b>Fecha:</b><br>
				<input type="text" readonly="readonly" value="<?= date('Y-m-d') ?>" name="r_fecha" id="r_fecha" />
			</td>
		</tr>
		<tr>
			<td align="center">
				<button id="r_revalorizar">Revalorizar</button>
			</td>
		</tr>
	</table>
</form>
<div id="mensaje_error" style="display:none;padding:4px;margin:3px;text-align:center" class="ui-state-highlight ui-corner-all"></div>
<div id="mensaje_ok" style="display:none;padding:4px;margin:3px;text-align:center" class="ui-state-active ui-corner-all">¡Las guias se han revalorizado!</div>
