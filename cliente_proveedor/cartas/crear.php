<?php
$raiz = "../../";
require_once $raiz."seguridad.php";
?>
<script>
$(function(){
	var ruta='cliente_proveedor/cartas/';
	$( "#regresar" ).click(function() {
		regresar();
	});
	$('#fecha').datepicker({
		autoSize: true,
		showOn: "both",
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		buttonText: 'Seleccionar...',
		gotoCurrent: true,
		hideIfNoPrevNext: true,
		minDate: 0
	});
	$('#texto').focus();
	$('#Crear').validate({
		rules: {
			fecha: 'required',
			texto: 'required',
			firmante: 'required',
			cargo_firmante: 'required'
		},
		messages: {
			fecha: 'Selecciona la fecha',
			texto: 'Escribe algún texto',
			firmante: 'Escribe el nombre del firmante',
			cargo_firmante: 'Escribe el cargo'
		},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function(form){
			form.submit();
		}
	});
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Nueva Carta</h2>
<hr class="hr-small">
<form enctype="multipart/form-data" target="_blank" id="Crear" action="cliente_proveedor/cartas/imprimir.php" method="post">
	<table style="width:100%" class="" cellpadding="0" cellspacing="10">
		<tr>
			<td><b>Fecha:</b></td>
			<td>
				<input type="text" class="input-small" name="fecha" id="fecha" value="<?= date('Y-m-d') ?>" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>Escriba el texto de la carta:</b><br>
				<textarea style="width: 95%;height: 250px" name="texto" id="texto"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>Firmante:</b><br>
				<input type="text" name="firmante" id="firmante" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>Cargo firmante:</b><br>
				<input type="text" name="cargo_firmante" id="cargo_firmante" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b>Firma:</b> (PNG, JPG y GIF)<br>
				<input type="file" name="firma" id="firma" />
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2">
				<button class="btn btn-info btn-large" type="submit"><i class="icon-file"></i> Generar</button>
			</td>
		</tr>
	</table>
</form>
