<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][TERCEROS_ENTRAR])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
$tercero = new Tercero;
$type = isset($_SESSION['permisos'][TERCEROS_DESHACER]) ? 'todos' : 'activos';
$sql = $tercero->all($type, 'sql');
$paging = new PHPPaging('right_content', $sql);
$paging->ejecutar();
?>
<script>
$(function(){
	$("#crear").click(function() {
		cargarExtra(terceros_path+"crear.php");
	});
	$("#exportar").click(function(){
		cargarExtra(terceros_path+'exportar.php');
	});
	$('table#terceros_list').on('click', '.ver', function(e){
		e.preventDefault();
		cargarExtra(terceros_path+"ver.php?"+this.name);
	});
	$('table#terceros_list').on('click', 'a.editar', function(e){
		e.preventDefault();
		cargarExtra(terceros_path+"editar.php?"+this.name);
	});
	$('table#terceros_list').on('click', 'a.anular', function(e){
		e.preventDefault();
		var msg = confirm("¿Desea eliminar este tercero?");
		if(!msg) return;
		var btn = $(this);
		btn.find('i').removeClass('icon-ban-circle').addClass('icon-spinner icon-spin');
		$.ajax({
			url: terceros_path+'anular.php',
			type: "GET",
			data: this.name,
			success: function(msj){
				if(msj == ""){
					cargarPrincipal(terceros_path+'?pagina='+$('#terceros_page').val());
				}else{
					btn.find('i').removeClass('icon-spinner icon-spin').addClass('icon-ban-circle');
					LOGISTICA.Dialog.open('Error', msj, true);
				}
			}
		});
	});
	$('table#terceros_list').on('click', 'a.deshacer', function(e){
		event.preventDefault();
		var msg = confirm("¿Desea activar este tercero?");
		if(!msg) return;
		var btn = $(this);
		btn.find('i').removeClass('icon-ok').addClass('icon-spinner icon-spin');
		$.ajax({
			url: terceros_path+'deshacer.php',
			data: this.name,
			type: "GET",
			success: function(msj){
				if(msj == ""){
					cargarPrincipal(terceros_path+'?pagina='+$('#terceros_page').val());
				}else{
					btn.find('i').removeClass('icon-spinner icon-spin').addClass('icon-ok');
					LOGISTICA.Dialog.open('Error', msj, true);
				}
			}
		});
	});
});
function fn_paginar(d, url){$('.'+d).load(url);}
</script>
<div class="btn-toolbar pull-right">
<?php if ( isset($_SESSION['permisos'][TERCEROS_CREAR]) ) { ?>
	<button id="crear" class="btn btn-info"><i class="icon-plus"></i> Crear Tercero</button>
<?php }
if ( isset($_SESSION['permisos'][TERCEROS_EXPORTAR]) ) { ?>
	<button id="exportar" class="btn"><i class="icon-file-alt"></i> Exportar</button>
<?php } ?>
</div>
<h2>Terceros</h2>
<table id="terceros_list" class="table table-hover table-bordered table-condensed">
	<thead>
		<tr>
			<th>ID</th>
			<th>Identificación</th>
			<th>Nombre</th>
			<th>Ciudad</th>
			<th>Teléfono</th>
			<th>Acción</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if ($paging->numTotalRegistros() == 0) {
		echo '<tr class="warning"><td colspan="6" class="expand">No se encontraron terceros</td></tr>';
	} else {
		while ($t = $paging->fetchResultado('Tercero')) { ?>
			<tr class="<?= $t->activo == 'no' ? 'error' : '' ?>">
				<td><?= $t->id ?></td>
				<td width="90"><?= $t->numero_identificacion_completo ?></td>
				<td><?= $t->nombre_completo ?></td>
				<td><?= $t->ciudad_nombre ?></td>
				<td><?= $t->telefono ?></td>
				<td><div class="btn-group">
				<?php
				$name = 'id='.$t->id.'&'.nonce_create_query_string($t->id);
				if ( isset($_SESSION['permisos'][TERCEROS_VER]) ) { ?>
					<a class="btn ver" name="<?= $name ?>" title="Ver" href="#"><i class="icon-search"></i></a>
				<?php }
				if ( isset($_SESSION['permisos'][TERCEROS_EDITAR]) and $t->activo == 'si' ) { ?>
					<a class="btn editar" name="<?= $name ?>" title="Editar" href="#"><i class="icon-pencil"></i></a>
				<?php }
				if ( isset($_SESSION['permisos'][TERCEROS_ANULAR]) and $t->activo == 'si' ) { ?>
					<a class="btn anular btn-danger" name="<?= $name ?>" title="Anular" href="#"><i class="icon-ban-circle"></i></a>
				<?php
				}elseif( isset($_SESSION['permisos'][TERCEROS_DESHACER]) and $t->activo=='no' ) { ?>
					<a class="btn deshacer btn-success" name="<?= $name ?>" title="Activara" href="#"><i class="icon-ok"></i></a>
				<?php } ?>
				</div></td>
			</tr>
		<?php }
	}
	?>
	</tbody>
</table>
<input type="hidden" id="terceros_page" value="<?= $paging->numEstaPagina() ?>">
<?= $paging->fetchNavegacion() ?>
