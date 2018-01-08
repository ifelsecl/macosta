<?php
require '../../seguridad.php';
if (! isset($_REQUEST['id']) or ! nonce_is_valid($_REQUEST[NONCE_KEY], $_REQUEST['id'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][VENDEDORES_VER])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

if (! $vendedor = Vendedor::find($_REQUEST['id'])) {
	include Logistica::$root.'mensajes/error_cargando.php';
	exit;
}
?>
<script>
$(function() {
	$("#regresar" ).click(function() {
		cargarPrincipal(vendedores_path);
	});
});
function fn_paginar(d,u) { $("."+d).load(u); }
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Información del Vendedor</h2>
<hr class="hr-small">
<table cellpadding="2" cellspacing="1">
	<tr>
		<td><b>Código:</b></td>
		<td><?= $vendedor->id ?></td>
	</tr>
	<tr>
		<td><b>Nombre:</b></td>
		<td><?= $vendedor->nombre?></td>
	</tr>
	<tr>
		<td><b>Cédula:</b></td>
		<td><?= $vendedor->cedula ?></td>
	</tr>
	<tr>
		<td><b>Ciudad:</b></td>
		<td>
			<?= $vendedor->ciudad?>
		</td>
	</tr>
	<tr>
		<td><b>Dirección:</b></td>
		<td>
			<?= $vendedor->direccion ?>
		</td>
	</tr>
    <tr>
      <td><b>Teléfono:</b></td>
      <td><label>
        <?= $vendedor->telefono ?>
      </label></td>
    </tr>
    <tr>
		<td><b>E-mail:</b></td>
		<td>
			<a href="mailto:<?= $vendedor->email ?>"><?= $vendedor->email ?></a>
		</td>
    </tr>
    <tr>
   		<td><b>Código SIIGO:</b></td>
   		<td><?= $vendedor->codigo_siigo ?></td>
    </tr>
    <tr>
   		<td><b>Activo:</b></td>
   		<td><?= strtoupper($vendedor->activo)?></td>
    </tr>
</table>
<?php
$consulta = $vendedor->ObtenerClientes($vendedor->id, 'SQL');
$paging = new PHPPaging('right_content', $consulta);
$paging->ejecutar();
if ($paging->numTotalRegistros() == 0) {
	echo '<p style="text-align:center; padding: 10px;">No tiene clientes asociados</p>';
} else {
	echo '<table class="table table-bordered table-hover table-condensed">';
	echo '<thead>';
	echo '<tr><th>ID</th><th>Cliente</th><th>Ciudad</th><th>Total vendido</th></tr>';
	echo '</thead>';
	while ($cliente = $paging->fetchResultado()) {
		echo '<tr>';
		echo '<td>'.$cliente['id'].'</td>';
		echo '<td>'.$cliente['nombre'].' '.$cliente['primer_apellido'].' '.$cliente['segundo_apellido'].'</td>';
		echo '<td>'.$cliente['ciudad'].'</td>';
		echo '<td align="right">'.number_format($cliente['total'],2).'</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo $paging->fetchNavegacion();
}
