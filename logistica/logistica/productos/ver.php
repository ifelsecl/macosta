<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][PRODUCTOS_VER])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
if (! $producto = Producto::find($_GET['id'])) exit('No existe el producto');
?>
<script>
$(function() {
	$('#regresar').click(function() {
		regresar();
	});
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Producto <?= $producto->id ?></h2>
<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#tab_info">Información</a></li>
		<li><a data-toggle="tab" href="#tab_history">Historial</a></li>
	</ul>
	<div class="tab-content">
		<div id="tab_info" class="tab-pane active">
			<table class="table">
				<tr>
					<td>Producto</td>
					<td><?= $producto->nombre ?></td>
				</tr>
				<tr>
					<td>Tipo</td>
					<td><?= $producto->tipo ?></td>
				</tr>
				<tr>
					<td>Activo?</td>
					<td><?= $producto->activo ?></td>
				</tr>
			</table>
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
					$producto->history();
					if (empty($producto->history)) {
						echo '<tr class="warning"><td class="expand" colspan="3">No se han realizado modificaciones</td></tr>';
					} else {
						foreach ($producto->history as $h) {
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
