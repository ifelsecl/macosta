<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][AYUDANTES_ENTRAR])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
$type = isset($_SESSION['permisos'][AYUDANTES_DESHACER]) ? 'todos' : 'activos';
$sql = Ayudante::all($type, true);
$paging = new PHPPaging('right_content', $sql);
$paging->ejecutar();
?>
<script>
$(function(){
	$('#crear').click(function(){
		cargarExtra(ayudantes_path+'crear.php');
	});
	$('.ver').click(function(e){
		e.preventDefault();
		cargarExtra(ayudantes_path+'ver.php?'+this.name);
	});
	$('.editar').click(function(e){
		e.preventDefault();
		cargarExtra(ayudantes_path+'editar.php?'+this.name);
	});
	$('.anular').click(function(e){
		e.preventDefault();
		var btn = $(this);
		alertify.confirm('¿Deseas eliminar este ayudante?', function(e){
			if(e){
				btn.find('i').removeClass('icon-trash').addClass('icon-spinner icon-spin');
				$.ajax({
					url: ayudantes_path+'anular.php',
					type: 'POST',
					data: btn.attr('name'),
					success: function(msj){
						if(msj=='ok'){
							cargarPrincipal(ayudantes_path);
						}else{
							btn.find('i').removeClass('icon-spinner icon-spin').addClass('icon-trash');
							LOGISTICA.Dialog.open('Error', msj, true);
						}
					}
				});
			}
		});
	});
	$('.deshacer').click(function(e){
		e.preventDefault();
		var btn = $(this);
		alertify.confirm('¿Deseas activar este ayudante?', function(e){
			if(e){
				btn.find('i').removeClass('icon-ok').addClass('icon-spinner icon-spin');
				$.ajax({
					url: ayudantes_path+'deshacer.php',
					type: 'POST',
					data: btn.attr('name'),
					success: function(msj){
						if(msj=='ok'){
							cargarPrincipal(ayudantes_path);
						}else{
							btn.find('i').removeClass('icon-spinner icon-spin').addClass('icon-ok');
							LOGISTICA.Dialog.open('Error', msj, true);
						}
					}
				});
			}
		});
	});
});
function fn_paginar(d, url){
	$('.'+d).load(url);
}
</script>
<?php if (isset($_SESSION['permisos'][AYUDANTES_CREAR])) { ?>
<button id="crear" class="btn btn-info pull-right" title="Crear Nuevo Ayudante"><i class="icon-plus"></i> Crear Ayudante</button>
<?php } ?>
<h2>Lista de Ayudantes</h2>
<table class="table table-bordered table-condensed table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Nombre</th>
			<th>Número Identificación</th>
			<th>Ciudad</th>
			<th style="width: 110px">Acción</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($paging->numTotalRegistros()==0) {
			echo '<tr class="warning"><td colspan="6" class="expand">No se encontraron ayudantes... Puedes crear uno nuevo usando el botón Crear.</td></tr>';
		}else{
			while ($a=$paging->fetchResultado('Ayudante')) {
				$c = $a->activo == 'no' ? 'anulado' : '';
				echo '<tr class="'.$c.'">';
				echo 	'<td>'.$a->id.'</td>';
				echo 	'<td>'.$a->nombre.'</td>';
				echo 	'<td>'.$a->numero_identificacion.'</td>';
				echo 	'<td>'.$a->ciudad.'</td>';
				$name='id='.$a->id.'&'.nonce_create_query_string($a->id);
				echo '<td><div class="btn-group">';
				if (isset($_SESSION['permisos'][AYUDANTES_VER])) {
					echo '<a class="btn ver" href="#" name="'.$name.'"><i class="icon-search"></i></a>';
				}
				if (isset($_SESSION['permisos'][AYUDANTES_EDITAR]) and $a->activo=='si'){
					echo '<a class="btn editar" href="#" name="'.$name.'"><i class="icon-pencil"></i></a>';
				}
				if (isset($_SESSION['permisos'][AYUDANTES_ANULAR]) and $a->activo=='si')
					echo '<a class="btn anular btn-danger" href="#" name="'.$name.'"><i class="icon-trash"></i></a>';
				elseif(isset($_SESSION['permisos'][AYUDANTES_DESHACER]) and $a->activo=='no')
					echo '<a class="btn deshacer btn-success" href="#" name="'.$name.'"><i class="icon-ok"></i></a>';
				echo '</div></td></tr>';
			}
		}
		?>
	</tbody>
</table>
<?= $paging->fetchNavegacion() ?>
<input type="hidden" id="pagina" name="pagina" value="<?= $paging->numEstaPagina() ?>" />
