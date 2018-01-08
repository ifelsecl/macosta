<?php
require '../seguridad.php';
if (! $factura = Factura::find($_GET['id'])) exit('No existe la factura.');
$factura->guias();
?>
<script>
$(function(){
	$('a.quitar').click(function(e){
		e.preventDefault();
		var b = $(this);
		$.ajax({
			url: facturacion_path+'quitarGuia.php',
			data: $(this).attr("name"),
			success: function(msj){
				if(msj!=0){
					b.parents('tr').remove();
				}else{
					LOGISTICA.Dialog.open('Error', msj, true);
				}
			}
		});
	});
});
</script>
<table class="table table-condensed table-bordered table-hover">
	<thead>
		<tr>
			<th>Destinatario</th>
			<th>Unidades</th>
			<th>Guía</th>
			<th>Ciudad Destino</th>
			<th>Total</th>
			<th>Quitar</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( empty($factura->guias) ){
		echo '<tr class="warning"><td colspan="6" class="expand">La factura no tiene guías.</td></tr>';
	}else{
		foreach ($factura->guias as $guia) {
			echo '<tr>';
			echo '<td>'.$guia->contacto()->nombre_completo.'</td>';
			echo '<td>'.$guia->unidades.'</td>';
			echo '<td>'.$guia->id.'</td>';
			echo '<td>'.$guia->contacto->ciudad_nombre.' ('.$guia->contacto->departamento_nombre.')</td>';
			echo '<td>'.number_format($guia->total + $guia->valorseguro).'</td>';
			echo '<td><a class="btn quitar btn-danger" title="Quitar" href="#" name="idguia='.$guia->id.'&idfactura='.$factura->id.'"><i class="icon-remove"></i></a></td>';
			echo '</tr>';
		}
	}
	?>
	</tbody>
</table>
