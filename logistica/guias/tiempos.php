<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
	include Logistica::$root."mensajes/id.php";
	exit;
}
if (! isset($_SESSION['permisos'][GUIAS_EDITAR_TIEMPOS])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

$guia = new Guia;
if (! $guia->find($_GET['id'])) exit('No existe la guía.');
if (in_array($guia->idestado, array(1, 2, 6))) {
	exit('<h2>Los tiempos se pueden modificar si la guia está en Bodega, Facturada o Anulada</h2>');
}
?>
<script>
$(function(){
	var b=$('#guardar-tiempos').button({icons:{primary: 'ui-icon-circle-check'}});
	$('#cargue_horas_pactadas').focus();
	$('#dialog').dialog('option','title','Editar Tiempos <?= $guia->id ?>');
	$('#EditarTiempos').validate({
		rules: {
			cargue_horas_pactadas: 'required',
			cargue_fecha_llegada: 'required',
			cargue_fecha_salida: 'required',
			descargue_horas_pactadas: 'required',
			descargue_fecha_llegada: 'required',
			descargue_fecha_salida: 'required'
		},
		errorPlacement: function(error, element) {return;},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function(f){
			$.ajax({
				type: 'POST',
				data: 'tiempos=1&'+$(f).serialize(),
				url: guias_path+'ajax.php',
				success: function(m){
					if(m=='ok'){
						cerrarDialogo();
					}else{
						$('#dialog').html(m);
					}
				}
			});
		}
	});
	$( ".t_fecha" ).datetimepicker({
		showOn: 'both',
		dateFormat: 'yy-mm-dd',
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		showampm: true,
		timeText: 'Hora',
		closeText: 'Cerrar',
		buttonText: 'Seleccionar...'
	});
});
</script>
<form id="EditarTiempos" action="#" method="post">
	<input type="hidden" name="id" value="<?= $guia->id ?>" />
	<table cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th></th>
				<th>Hrs Pactadas</th>
				<th>Fecha/Hora Llegada</th>
				<th>Fecha/Hora Salida</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>Cargue</th>
				<td><input class="input-mini" name="cargue_horas_pactadas" id="cargue_horas_pactadas" type="text" size="3" value="<?= $guia->cargue_horas_pactadas ?>" /></td>
				<td>
					<?php
					$cfl = '';
					if($guia->cargue_llegada) $cfl = date('m/d/Y g:i a', strtotime($guia->cargue_llegada));
					$cfs = '';
					if($guia->cargue_salida) $cfs = date('m/d/Y g:i a', strtotime($guia->cargue_salida));
					?>
					<input class="input-medium t_fecha" name="cargue_fecha_llegada" id="cargue_fecha_llegada" type="text" value="<?= $cfl ?>" />
				</td>
				<td><input class="input-medium t_fecha" name="cargue_fecha_salida" type="text" value="<?= $cfs ?>" /></td>
			</tr>
			<tr>
				<th>Descargue</th>
					<?php
					$dfl='';
					if($guia->descargue_llegada) $dfl=date('m/d/Y g:i a',strtotime($guia->descargue_llegada));
					$dfs='';
					if($guia->descargue_salida) $dfs=date('m/d/Y g:i a',strtotime($guia->descargue_salida));
					?>
				<td><input class="input-mini" name="descargue_horas_pactadas" type="text" size="3" value="<?= $guia->descargue_horas_pactadas ?>" /></td>
				<td><input class="input-medium t_fecha" name="descargue_fecha_llegada" type="text" value="<?= $dfl ?>" /></td>
				<td><input class="input-medium t_fecha" name="descargue_fecha_salida" type="text" value="<?= $dfs ?>" /></td>
			</tr>
		</tbody>
	</table>
	<center>
		<button id="guardar-tiempos">Guardar</button>
	</center>
</form>
