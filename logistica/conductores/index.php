<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CONDUCTORES_ENTRAR])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}

$type = isset($_SESSION['permisos'][CONDUCTORES_DESHACER]) ? 'todos' : 'activos';
$consulta = Conductor::all($type, 'sql');
$paging = new PHPPaging('right_content', $consulta);
$paging->ejecutar();
?>
<script>
$(function() {
	$("#crear").click(function() {
		cargarExtra(conductores_path+"crear.php");
	});
	$("#exportar").click(function() {
		cargarExtra(conductores_path+'exportar.php');
	});
	$('table#conductores_list')
		.on('click', 'a.ver', function(e) {
			e.preventDefault();
			cargarExtra(conductores_path+'ver.php?'+this.name);
		})
		.on('click', 'a.editar', function(e) {
			e.preventDefault();
			cargarExtra(conductores_path+'editar.php?'+this.name);
		})
		.on('click', 'a.anular', function(e) {
			e.preventDefault();
			var msg = confirm("¿Desea eliminar este conductor?");
			if(!msg) return;
			var btn = $(this);
			btn.find('i').removeClass('icon-ban-circle').addClass('icon-spinner icon-spin');
			$.ajax({
				url: conductores_path+'anular.php',
				data: this.name, type: 'POST',
				success: function(msj) {
					if(!msj) {
						cargarPrincipal(conductores_path);
					}else{
						btn.find('i').removeClass('icon-spinner icon-spin').addClass('icon-ban-circle');
						alertify.error(msj);
					}
				}
			});
		})
		.on('click', 'a.deshacer', function(e) {
			e.preventDefault();
			var msg = confirm("¿Desea volver a activar este conductor?");
			if(!msg) return;
			var btn = $(this);
			btn.find('i').removeClass('icon-ok').addClass('icon-spinner icon-spin');
			$.ajax({
				url: conductores_path+'deshacer.php',
				type: "POST", data: this.name,
				success: function(msj) {
					if(!msj) {
						cargarPrincipal(conductores_path);
					}else{
						btn.find('i').removeClass('icon-spinner icon-spin').addClass('icon-ok');
						alertify.error(msj);
					}
				}
			});
		});
});
function fn_paginar(d, url) {
	$('.'+d).load(url);
}
</script>
<div class="btn-toolbar pull-right">
	<?php if ($_SESSION['permisos'][CONDUCTORES_CREAR]) { ?>
	<button id="crear" class="btn btn-info"><i class="icon-plus"></i> Crear Conductor</button>
	<?php }
	if ($_SESSION['permisos'][CONDUCTORES_EXPORTAR]) { ?>
	<button id="exportar" class="btn"><i class="icon-file-alt"></i> Exportar</button>
	<?php } ?>
</div>
<h2>Conductores</h2>
<table id="conductores_list" class="table table-hover table-bordered table-condensed">
	<thead>
		<tr>
			<th>Número</th>
			<th>Nombre</th>
			<th>Ciudad</th>
			<th>Direccion</th>
			<th style="width: 110px">Acción</th>
		</tr>
	</thead>
	<tbody>
	<?php
	while ($conductor = $paging->fetchResultado('Conductor')) {
		$s = $conductor->activo == 'no' ? 'error' : '';
		echo '<tr class="'.$s.'">';
		echo '<td>'.$conductor->numero_identificacion_completo.'</td>';
		echo '<td>'.$conductor->nombre_completo.'</td>';
		echo '<td>'.$conductor->ciudad_nombre.'</td>';
		echo '<td>'.$conductor->direccion.'</td>';
		$name="numero_identificacion=".$conductor->numero_identificacion."&".nonce_create_query_string($conductor->numero_identificacion);
		echo '<td><div class="btn-group">';
		if (isset($_SESSION['permisos'][CONDUCTORES_VER])) {
			echo '<a class="btn ver" title="Ver" href="#" name="'.$name.'"><i class="icon-search"></i></a>';
		}
		if (isset($_SESSION['permisos'][CONDUCTORES_EDITAR]) and $conductor->activo == 'si') {
			echo '<a class="btn editar" title="Editar" href="#" name="'.$name.'"><i class="icon-pencil"></i></a>';
		}
		if (isset($_SESSION['permisos'][CONDUCTORES_ANULAR]) and $conductor->activo == 'si') {
			echo '<a class="btn anular btn-danger" href="#" title="Anular" name="'.$name.'"><i class="icon-ban-circle"></i></a>';
		}elseif (isset($_SESSION['permisos'][CONDUCTORES_DESHACER]) and $conductor->activo == 'no') {
			echo '<a class="btn deshacer btn-success" href="#" title="Activar" name="'.$name.'"><i class="icon-ok"></i></a>';
		}
		echo '</div></td>';
	}
	?>
	</tbody>
</table>
<?= $paging->fetchNavegacion() ?>
