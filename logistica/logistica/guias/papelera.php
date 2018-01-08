<?php
require '../../seguridad.php';
if (! isset($_SESSION['permisos'][GUIAS_ELIMINAR])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
$sql = Guia::all_by_estado(6, '', '', 'sql');
$paging = new PHPPaging('extra_content', $sql);
$paging->ejecutar();
?>
<script>
$(function() {
	$('#eliminar').click(function() {
		var sel=$( "#guias_anuladas input[type='checkbox']:checked").serialize();
		if (sel=='') {
			alert('Seleccione por lo menos una guía para eliminar.');
		} else {
			var conf = confirm('¿Desea eliminar las guías seleccionadas?\r\n(esta acción no se puede deshacer)');
			if (! conf) return false;
			var o={'title':'Eliminar','position':'center','width':'auto','height':'auto'};
			$('#dialog')
				.html('<p class="expand">Eliminando...</p>')
				.dialog('open').dialog('option',o);
			$.ajax({
				url: guias_path+'ajax.php',
				type: 'POST',
				data: 'eliminar_varias=1&'+sel,
				success: function(msj) {
					if (msj=='ok') {
						$("#extra_content").load(guias_path+'papelera.php');
						var html = '<p class="expand"><img src="css/images/active.png" />¡Guías eliminadas correctamente!</p>';
						LOGISTICA.Dialog.open('Guias', html, true);
					} else {
						LOGISTICA.Dialog.open('Error', msj, true);
					}
				}
			});
		}
	});
	$('table#guias_anuladas').on('click', 'button.ver', function() {
		LOGISTICA.Dialog.open('Guía', guias_path+'ver.php?'+this.name);
	});
	$('#check_all').click(function() {
		$("#guias_anuladas input[type='checkbox']").prop('checked', $(this).prop('checked'));
	});
});
function fn_paginar(d, r) {
	$('#'+d).load(r);
}
</script>
<div class="pull-right">
	<button id="eliminar" class="btn btn-danger" title="Eliminar guias seleccionadas"><i class="icon-trash"></i> Eliminar</button>
	<button id="regresar" class="btn btn-success" onclick="regresar();">Regresar</button>
</div>
<h2>Guías | Papelera (Anuladas)</h2>
<table id="guias_anuladas" class="table table-hover table-bordered table-condensed">
	<thead>
		<tr>
			<th><input title="Seleccionar/Deseleccionar todas" type="checkbox" id="check_all" /></th>
			<th>Fecha</th>
			<th>Número</th>
			<th>Remitente</th>
			<th>Destino</th>
			<th>Destinatario</th>
			<th>Usuario</th>
			<th>Ver</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if ($paging->numTotalRegistros() == 0) {
				echo '<tr colspan="7" class="warning"><td colspan="8" class="expand">No se encontraron guías ANULADAS</td></tr>';
			} else {
				$hoy = date('Y-m-d');
				while ($guia = $paging->fetchResultado('Guia')) {
					echo '<tr>';
					echo '<td align="center"><input type="checkbox" name="guias[]" value="'.$guia->id.'" /></td>';
					if ($hoy == $guia->fecha_recibido_mercancia) {
						echo '<td align="center">Hoy</td>';
					} else {
						echo '<td align="center">'.strftime('%b %d, %Y',strtotime($guia->fecha_recibido_mercancia)).'</td>';
					}
					echo '<td>'.$guia->id.'</td>';
					if (strlen($guia->cliente()->nombre_completo)>20) {
						echo '<td title="'.$guia->cliente->nombre_completo.'">'.substr($guia->cliente->nombre_completo, 0, 20).'...</td>';
					} else {
						echo '<td>'.$guia->cliente->nombre_completo.'</td>';
					}
					echo '<td>'.$guia->contacto()->ciudad_nombre.'</td>';
					$str = $guia->contacto->nombre_completo;
					$titulo = '';
					if (strlen($guia->contacto->nombre_completo)>18) {
						$str = substr($guia->contacto->nombre_completo, 0, 15).'...';
						$titulo = $guia->contacto->nombre_completo;
					}
					echo '<td title="'.$titulo.'">'.$str.'</td>';
					echo '<td align="center">'.$guia->idusuario.'</td>';
					$name="id=".$guia->id."&".nonce_create_query_string($guia->id);
					echo '<td width="16" align="center"><button title="Ver" class="btn ver" name="'.$name.'"><i class="icon-search"></i></button></td>';
				}
			}
		?>
	</tbody>
</table>
<?= $paging->fetchNavegacion() ?>
