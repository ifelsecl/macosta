<?php
exit('<div class="page-header"><h2>Modulo Descontinuado...</h2></div>');
$raiz='../../';
require_once $raiz."seguridad.php";
if( !isset($_SESSION['permisos'][ESTADISTICAS_ENTRAR]) ){
	include_once $raiz."mensajes/permiso.php";
	exit;
}
?>
<script type="text/javascript">
$(function(){
	var ruta='configuracion/estadisticas/';
	var g='<p class="expand"><img src="css/ajax-loader.gif" />&nbsp;Generando estad&iacute;sticas...</p>';
	$('#generar').button({icons: {primary: 'ui-icon-image'}});
	$('#reporte').load(ruta+'reportes.php?reporte='+$('#tipo_reporte').val());
	var dates = $("#from, #to").datepicker({
		changeMonth: true,
		changeYear: true,
		numberOfMonths: 3,
		showOn: "both",
		maxDate: '0',
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		dateFormat: 'yy-mm-dd',
		buttonText: 'Seleccionar...',
		autoSize: true,
		onSelect: function(selectedDate) {
			var option = this.id == "from" ? "minDate" : "maxDate";
			dates.not( this ).datepicker("option", option, selectedDate);
		}
	});
	$('#GenerarReporte').submit(function(e){
		e.preventDefault();
		if(!$('#from').val() && $('#tipo_reporte').val()!='global'){
			$('#from').addClass('ui-state-highlight').focus();
		}else if(!$('#to').val() && $('#tipo_reporte').val()!='global'){
			$('#from').removeClass('ui-state-highlight');
			$('#to').addClass('ui-state-highlight').focus();
		}else{
			$('#from').removeClass('ui-state-highlight');
			$('#to').removeClass('ui-state-highlight');
			$('#generar').button('disable').button('option','label','Generando...');
			$('#reporte')
				.html(g)
				.load(ruta+'reportes.php?'+$('#GenerarReporte').serialize(), function(){
					$('#generar').button('enable').button('option','label','Generar');
				});
		}
	});
});
</script>
<h2>Estad&iacute;sticas de la mercanc&iacute;a</h2>
<hr />
<!-- <div style="float: right">Ayuda</div> -->
<form id="GenerarReporte" style="display:block;">
	<table>
		<tr>
			<td><b>Reporte:</b>
				<select id="tipo_reporte" name="reporte" class="ui-widget-content">
					<option value="global">Global</option>
					<option value="dia">D&iacute;a</option>
					<option value="mes">Mes</option>
					<option value="ano">A&ntilde;o</option>
				</select>
			</td>
			<td>
				<b>Fecha:</b>
				<input type="text" id="from" name="fecha_inicio" class="ui-widget-content" />&nbsp;-&nbsp;
				<input type="text" id="to" name="fecha_fin" class="ui-widget-content" />
			</td>
			<td><button id="generar">Generar</button></td>
		</tr>
	</table>
</form>
<center id="reporte" style="display: block">
	<p class="no_resultados"><img src="css/ajax-loader.gif" />&nbsp;Generando estad&iacute;sticas...</p>
</center>