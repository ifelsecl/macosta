<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][USUARIOS_ENTRAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

if (isset($_REQUEST['termino'])) {
	$_REQUEST['termino'] = trim($_REQUEST['termino']);
	$usuarios = Usuario::search($_REQUEST['termino'], 'sql');
} else {
	$type = isset($_SESSION['permisos'][USUARIOS_DESHACER]) ? 'todos' : 'activos';
	$usuarios = Usuario::all($type, 'sql');
}
$paging = new PHPPaging('right_content', $usuarios);
$paging->ejecutar();
?>
<script>
$(function() {
	$('#exportar').click(function() {
		cargarExtra(usuarios_path+"exportar.php");
	});
	$('#crear').click(function() {
		cargarExtra(usuarios_path+"crear.php");
	});
	$('table#usuarios_list')
		.on('click', '.ver', function(e) {
			e.preventDefault();
			cargarExtra(usuarios_path+'ver.php?'+this.name);
		})
		.on('click', '.editar', function(e) {
			e.preventDefault();
			cargarExtra(usuarios_path+'editar.php?'+this.name);
		})
		.on('click', '.anular', function(e) {
			e.preventDefault();
			var msg = confirm("¿Desea anular este usuario?");
			if (!msg) return;
			$.ajax({
				url: usuarios_path+'anular.php?'+this.name,
				success: function(msj) {
					if (msj=="ok") {
						cargarPrincipal(usuarios_path+'index.php?'+$('#BuscarUsuarios').serialize());
					} else {
						LOGISTICA.Dialog.open('Error',msj,true);
					}
				}
			});
		})
		.on('click', '.deshacer', function(e) {
			e.preventDefault();
			var msg = confirm("¿Desea activar este usuario?");
			if (! msg) return;
			$.ajax({
				url: usuarios_path+'deshacer.php?'+this.name, type: 'POST',
				success: function(msj) {
					if (msj=="ok") {
						cargarPrincipal(usuarios_path+'index.php?'+$('#BuscarUsuarios').serialize());
					} else {
						LOGISTICA.Dialog.open('Error',msj,true);
					}
				}
			});
		});

	$('#buscar').button({icons: {primary: 'ui-icon-search'}, text: false});
	$('#BuscarUsuarios').submit(function(e) {
		e.preventDefault();
		if ($.trim($('#termino').val())) {
			$('#buscar').button('disable');
			cargarPrincipal(usuarios_path+'index.php?'+$('#BuscarUsuarios').serialize());
		} else {
			$('#termino').addClass('ui-state-highlight').focus();
		}
	});
	$('#todos').button().click(function(e) {
		e.preventDefault();
		$(this).button('disable');
		cargarPrincipal(usuarios_path);
	});
	$('#termino').focus();
});
</script>
<div class="pull-right">
	<?php if (isset($_SESSION['permisos'][USUARIOS_CREAR])) { ?>
		<button id="crear" class="btn btn-info" title="Crear un usuario"><i class="icon-plus"></i> Crear Usuario</button>
	<?php }
	if (isset($_SESSION['permisos'][USUARIOS_EXPORTAR])) { ?>
		<button id="exportar" title="Exportar la lista de usuarios" class="btn "><i class="icon-file-alt"></i> Exportar</button>
	<?php } ?>
</div>
<h2>Usuarios</h2>
<form class="form-inline" id="BuscarUsuarios" name="BuscarUsuarios" action="#" method="post">
	<?php if ($paging->numEstaPagina() > 1) { ?>
	<input type="hidden" id="pagina" name="pagina" value="<?= $paging->numEstaPagina() ?>" />
	<?php } ?>
	<table>
		<tr>
			<td>Buscar:</td>
			<td><input type="text" id="termino" name="termino" <?php if (isset($_GET['termino'])) echo 'value="'.$_GET['termino'].'"'?> /></td>
			<td><button type="submit" id="buscar">Buscar</button></td>
			<?php if (isset($_GET['termino'])) echo '<td><button type="button" id="todos">Todos</button></td>' ?>
		</tr>
	</table>
</form>
<table id="usuarios_list" class="table table-bordered table-condensed table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Nombre</th>
			<th>Usuario</th>
			<th>Email</th>
			<th>Último acceso</th>
			<th style="width:112px">Acción</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if (empty($usuarios)) {
			echo '<tr class="warning"><td colspan="6" class="expand">No se encontraron usuarios...</td></tr>';
		} else {
			while ($usuario = $paging->fetchResultado('Usuario')) {
				$c = $usuario->activo == 'no' ? 'error' : '';
				echo '<tr class="'.$c.'">';
				echo '<td>'.$usuario->id.'</td>';
				echo '<td>'.$usuario->nombre.'</td>';
				echo '<td>'.$usuario->usuario.'</td>';
				echo '<td>'.$usuario->email.'</td>';
				echo '<td title="'.strftime('%A, %B %d, %Y - %I:%M:%S %p',strtotime($usuario->ultimo_acceso)).'">'.strftime('%b %d - %I:%M %p',strtotime($usuario->ultimo_acceso)).'</td>';
				$name='id='.$usuario->id.'&'.nonce_create_query_string($usuario->id);
				echo '<td><div class="btn-group">';
				if (isset($_SESSION['permisos'][USUARIOS_VER])) {
					echo '<a class="btn ver" href="#" name="'.$name.'" title="Ver"><i class="icon-search"></i></a>';
				}
				if (isset($_SESSION['permisos'][USUARIOS_EDITAR]) and $usuario->activo == 'si') {
					echo '<a class="btn editar" title="Editar Usuario" href="#" name="'.$name.'"><i class="icon-pencil"></i></a>';
				}
				if (isset($_SESSION['permisos'][USUARIOS_ANULAR]) and $usuario->activo == 'si' ) {
					echo '<a class="btn anular btn-danger" title="Anular" href="#" name="'.$name.'"><i class="icon-trash"></i></a>';
				}elseif (isset($_SESSION['permisos'][USUARIOS_DESHACER]) and $usuario->activo == 'no') {
					echo '<a class="btn deshacer btn-success" title="Activar Usuario" href="#" name="'.$name.'"><i class="icon-upload"></i></a>';
				}
				echo '</div></td>';
				echo '</tr>';
			}
		}
		?>
	</tbody>
</table>
<?= $paging->fetchNavegacion() ?>
