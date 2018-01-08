<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][ARCHIVOS_ENTRAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
?>
<script>
$(function(){
	$("#fecha1, #fecha2").datepicker({
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
});
</script>
<table style="width: 100%">
	<tr>
		<td style="width: 50%"><h2>FTP Ministerio de Transporte</h2></td>
		<td style="width: 50%"><h3>Registro</h3></td>
	</tr>
</table>
<form action="configuracion/archivos/generar" method="post" target="_blank">
	<table>
		<tr>
			<td>Selecciona la fecha:</td>
			<td>
				<input class="input-small" type="text" id="fecha1" name="fecha" value="<?= date('Y-m-d') ?>" />
			</td>
		</tr>
	</table>
	<div class="muted" style="float:right;width:49%">
		<?php
		if(file_exists('log')) echo file_get_contents('log');
		else echo 'No se han enviado los archivos al ministerio';
		?>
	</div>
	<table class="table table-condensed" style="width: 50%" summary="Archivos" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td style="width: 16px"><i class="icon-file icon-2x"></i></td>
			<td>
				<b>Archivo de Manifiestos</b>
			</td>
		</tr>
		<tr>
			<td style="width: 16px"><i class="icon-file icon-2x"></i></td>
			<td>
				<b>Archivo de Remesas</b>
			</td>
		</tr>
		<tr>
			<td style="width: 16px"><i class="icon-file icon-2x"></i></td>
			<td>
				<b>Archivo de Vehículos</b>
			</td>
		</tr>
		<tr>
			<td style="width: 16px"><i class="icon-file icon-2x"></i></td>
			<td>
				<b>Archivo de Personas</b>
			</td>
		</tr>
		<tr>
			<td style="width: 16px"><i class="icon-file icon-2x"></i></td>
			<td>
				<b>Archivo de Empresas</b>
			</td>
		</tr>
		<tr>
			<td style="width: 16px"><i class="icon-file icon-2x"></i></td>
			<td>
				<b>Archivo de Plazos y Tiempos de Cargue y Descargue</b>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: center">
				<button class="btn btn-info btn-large"><i class="icon-cloud-upload"></i> Generar y Subir</button>
			</td>
		</tr>
	</table>
</form>
<hr class="hr-small">
<form action="configuracion/archivos/generar" method="post" target="_blank">
	<h2>SIIGO</h2>
	<table>
		<tr>
			<td>Selecciona la fecha:</td>
			<td>
				<input type="text" class="input-small" id="fecha2" name="fecha" value="<?php echo date('Y-m-d') ?>" />
			</td>
		</tr>
	</table>
	<table cellpadding="5">
		<tr>
			<td><i class="icon-file icon-2x"></i></td>
			<td>
				<b>Archivo para SIIGO</b><br />
				Entrada de comprobantes para SIIGO.
			</td>
			<td>
				<input type="hidden" id="" name="siigo" value="s" />
				<button title="Descargar" class="btn btn-info btn-large"><i class="icon-download-alt"></i> Descargar</button>
			</td>
		</tr>
	</table>
</form>
