<?php
require "../../seguridad.php";
if (! isset($_GET['numero_identificacion']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['numero_identificacion'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][CONDUCTORES_VER])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

$conductor = new Conductor;
if (! $conductor->find($_GET['numero_identificacion'])) exit('No existe el conductor');
$_SESSION['id_conductor'] = $_GET['numero_identificacion'];
?>
<script>
$(function() {
	$(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
		.find(".portlet-header")
		.addClass("ui-widget-header ui-corner-all")
		.end()
		.find(".portlet-content");
	function ActualizarGrafico(g, mes){
		var options = {
			chart: {renderTo: ''},
			title: {text: ''},
			subtitle: {text: ''},
			xAxis: {categories: []},
			yAxis: {min: 0,title: {text: ''}},
			credits: {enabled: false},
			legend: {enabled:false},
			tooltip: {
				formatter: function() {
					return '<b>'+ this.x +'</b><br>'+ Highcharts.numberFormat(this.y,0,',','.');
				}
			},
			plotOptions: {column: {pointPadding: 0.2,borderWidth: 0}},series: []
		};
		$.ajax({
			url: 'ajax.php?accion='+g+'&mes='+mes,
			type: 'POST', dataType: 'json',
			success: function(r){
				var serie1 = {
					data: r.cantidades,
					name: r.texto,
					type: 'bar'
				};
				options.series.push(serie1);
				options.chart.renderTo='g'+g;
				options.title.text='Destinos mas frecuentes';
				options.xAxis.categories=r.nombres;
				options.subtitle.text= r.texto;
				var chart = new Highcharts.Chart(options);
			}
		});
	}

	$('#sDestinosConductor').change(function(){
		ActualizarGrafico('DestinosConductor',$(this).val());
	}).change();
});
</script>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2><?= $conductor->nombre_completo ?></h2>
<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#tab_info">Información</a></li>
		<li><a data-toggle="tab" href="#tab_history">Historial</a></li>
	</ul>
	<div class="tab-content">
		<div id="tab_info" class="tab-pane active">
			<table>
				<tr>
					<td><b>Tipo de identificación</b></td>
					<td><?= $conductor->tipo_identificacion ?></td>
				</tr>
				<tr>
					<td><b>Número de identificación</b></td>
					<td><?= $conductor->numero_identificacion_completo ?></td>
				</tr>
				<tr>
					<td><b>Ciudad:</b></td>
					<td><?= $conductor->ciudad()->nombre ?></td>
				</tr>
				<tr>
					<td><b>Categoría-Licencia:</b></td>
					<td><?= $conductor->categorialicencia ?></td>
				</tr>
				<tr>
					<td><b>Fecha Vencimiento del pase:</b></td>
					<td><?= $conductor->vencimientopase ?></td>
				</tr>
				<tr>
					<td><b>Dirección:</b></td>
					<td><?= $conductor->direccion ?></td>
				</tr>
				<tr>
					<td><b>Teléfono</b></td>
					<td><?= $conductor->telefono ?></td>
				</tr>
				<tr>
					<td><b>Celular</b></td>
					<td><?= $conductor->celular ?></td>
				</tr>
			</table>
			<?php $img = '<p class="expand"><img src="css/ajax-loader.gif" /></p>' ?>
			<div class="portlet">
				<div class="portlet-header">Destinos Frecuentes&nbsp;
					<select class="input-medium" id="sDestinosConductor">
						<option value="ACTUAL">Mes Actual</option>
						<option value="ANTERIOR">Mes Anterior</option>
						<option value="3MESES">Hace 3 Meses</option>
						<option value="6MESES">Hace 6 Meses</option>
						<option value="12MESES">Hace 1 Año</option>
					</select>
				</div>
				<div class="portlet-content">
					<div id="gDestinosConductor" style="min-width:300px;width:100%;min-height:250px;height:auto;margin:0 auto">
						<?= $img ?>
					</div>
				</div>
			</div>
		</div>
		<div id="tab_history" class="tab-pane">
			<table class="table table-hover table-condensed table-bordered">
				<thead>
					<tr>
						<th>Fecha</th>
						<th>Usuario</th>
						<th>Acción</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$conductor->history();
					if (empty($conductor->history)) {
						echo '<tr class="warning"><td colspan="3" class="expand">No se han realizado modificaciones</td></tr>';
					} else {
						foreach ($conductor->history as $h) {
							echo '<tr>';
							echo '<td>'.$h->fecha.'</td>';
							echo '<td>'.$h->usuario.'</td>';
							echo '<td>'.$h->accion.'</td>';
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
