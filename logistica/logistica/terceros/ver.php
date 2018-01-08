<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][TERCEROS_VER])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
if (! $tercero = Tercero::find($_GET['id'])) exit('No existe el tercero');
?>
<button class="btn btn-success pull-right" onClick="regresar();" id="regresar">Regresar</button>
<h2><?= $tercero->nombre_completo ?></h2>
<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#tab_info">Información</a></li>
		<li><a data-toggle="tab" href="#tab_vehicles">Vehículos</a></li>
		<li><a data-toggle="tab" href="#tab_history">Historial</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_info">
			<table>
				<tr>
					<td><b>Tipo de identificación</b></td>
					<td><?= $tercero->tipo_identificacion ?></td>
				</tr>
				<tr>
					<td><b>Número de identificación</b></td>
					<td><?= $tercero->numero_identificacion_completo ?></td>
				</tr>
				<tr>
					<td><b>Ciudad:</b></td>
					<td><?= $tercero->ciudad_nombre ?></td>
				</tr>
				<tr>
					<td><b>Dirección:</b></td>
					<td><?= $tercero->direccion ?></td>
				</tr>
				<tr>
					<td><b>Teléfono</b></td>
					<td><?= $tercero->telefono ?></td>
				</tr>
				<tr>
					<td><b>Celular</b></td>
					<td><?= $tercero->celular ?></td>
				</tr>
				<tr>
					<td><b>Email</b></td>
					<td><?= $tercero->email ?></td>
				</tr>
				<tr>
					<td><b>Activo</b></td>
					<td><?= $tercero->activo?></td>
				</tr>
			</table>
		</div>
		<div class="tab-pane" id="tab_vehicles">
			<div style="width:45%" class="pull-left">
				<h4>Propietario</h4>
				<?php
				$vp = $tercero->vehiculos('propietario');
				if (empty($vp)) echo '<p class="expand">No es propietario de ningún vehículo</p>';
				else {
					echo '<table class="table table-condensed">';
					echo '<tr><th>Placa</th><th>Marca</th><th>Modelo</th></tr>';
					foreach ($vp as $v) {
						echo '<tr><td>'.$v->placa.'</td>';
						echo '<td>'.$v->marca_nombre.'</td>';
						echo '<td>'.$v->modelo.'</td></tr>';
					}
					echo '</table>';
				}
				?>
			</div>
			<div style="width:45%" class="pull-right">
				<h4>Tenedor</h4>
				<?php
				$vp = $tercero->vehiculos('tenedor');
				if (empty($vp)) echo '<p class="expand">No es tenedor de ningún vehículo</p>';
				else {
					echo '<table class="table table-condensed">';
					echo '<tr><th>Placa</th><th>Marca</th><th>Modelo</th></tr>';
					foreach ($vp as $v) {
						echo '<tr><td>'.$v->placa.'</td>';
						echo '<td>'.$v->marca_nombre.'</td>';
						echo '<td>'.$v->modelo.'</td></tr>';
					}
					echo '</table>';
				}
				?>
			</div>
		</div>
		<div class="tab-pane" id="tab_history">
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
					$tercero->history();
					if ( empty($tercero->history) ) {
						echo '<tr class="warning"><td colspan="3" class="expand">No se han realizado modificaciones</td></tr>';
					} else {
						foreach ($tercero->history as $h) {
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
