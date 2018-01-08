<?php
require '../../seguridad.php';
if (! isset($_SESSION['permisos'][RESOLUCIONES_ENTRAR])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
$resoluciones = Resolucion::all(true);
$paging = new PHPPaging('right_content', $resoluciones);
$paging->ejecutar();
?>
<div id="container">
	<div class="btn-toolbar pull-right">
		<button class="btn btn-info" id="resolucion_crear">Crear Resolución</button>
	</div>
	<h2>Resoluciones</h2>
	<table class="table table-condensed table-bordered table-hover">
		<thead>
			<tr>
				<?php
				foreach (array('Tipo', 'Número', 'Fecha', 'Rango') as $m) {
					echo '<th>'.$m.'</th>';
				}
				?>
				<th style="width:80px">Acciones</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ($paging->numTotalRegistros() == 0) {
				echo '<tr class="warning"><td colspan="5" class="expand">No se encontraron resoluciones...</td></tr>';
			} else {
				while ($resolucion = $paging->fetchResultado('Resolucion')) {
					echo '<tr>';
					echo '<td>'.ucfirst($resolucion->tipo).'</td>';
					echo '<td>'.$resolucion->numero.'</td>';
					echo '<td>'.$resolucion->fecha.'</td>';
					echo '<td>'.$resolucion->rango().'</td>';
					echo '<td><div class="btn-group">';
					$name = "id=".$resolucion->id.'&'.nonce_create_query_string($resolucion->id);
					echo '<button class="btn editar" title="Editar" name="'.$name.'"><i class="icon-pencil"></i></button>';
					echo '<button class="btn eliminar btn-danger" title="Eliminar" name="'.$name.'"><i class="icon-trash"></i></button>';
					echo '</div></td>';
					echo '</tr>';
				}
			}
			?>
		</tbody>
	</table>
</div>
<script>
(function(){
	var resolucion = {
		$el: $('#container'),
		init: function() {
			this.$el.find('button#resolucion_crear').click(function() {
				cargarExtra(resoluciones_path+'crear.php');
			});
			this.$el.on('click', 'button.editar', function() {
				cargarExtra(resoluciones_path+'editar.php?'+this.name);
			});
			this.$el.on('click', 'button.eliminar', function() {
				var self = this;
				$(self).attr('disabled', 'disabled');
				$.ajax({
					url: resoluciones_path+'ajax.php?'+self.name,
					type: 'DELETE',
					success: function(response) {
						if (! response) {
							$(self).closest('tr').remove();
						} else {
							$(self).removeAttr('disabled');
							alertify.error(response);
						}
					}
				});
			});
		}
	};
	resolucion.init();
})();
</script>
