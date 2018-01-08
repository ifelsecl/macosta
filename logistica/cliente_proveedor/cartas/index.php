<?php
$raiz = "../../";
require $raiz."seguridad.php";
require $raiz."class/Carta.class.php";
$carta = new Carta;

$consulta = $carta->todas('SQL');
$paging = new PHPPaging('right_content', $consulta, true);
$paging->ejecutar();
?>
<script>
$(function(){
	var ruta="cliente_proveedor/cartas/";
	$( "#crear" ).click(function() {
		cargarExtra(ruta+'crear.php');
	});
});
function fn_paginar(d, u){
	$('#cargando').slideDown();
	$('.'+d).load(u);
}
</script>
<button class="btn btn-info pull-right" id="crear"><i class="icon-plus"></i> Nueva Carta</button>
<h2>Cartas</h2>
<table class="table table-hover table-bordered">
	<thead>
		<tr>
			<th>Numero</th>
			<th>Fecha</th>
			<th>Cliente</th>
			<th>Contacto</th>
			<th>Acción</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if ($paging->numTotalRegistros()==0) {
		echo '<tr class="warning"><td colspan="6" class="expand">No se encontraron cartas...</td></tr>';
	}else{
		while( $c = $paging->fetchResultado()){
			echo '<td align="right">'.$c['id'].'</td>';
			echo '<td align="center">'.$c['fecha'].'</td>';
			echo '<td>'.$c['cliente'].'</td>';
			if(empty($c['contacto'])){
				echo '<td align="center">--</td>';
			}else{
				echo '<td>'.$c['contacto'].'</td>';
			}
			$name="id=".$c['id']."&".nonce_create_query_string($c['id']);
			//echo '<td width="16"><a target="_blank" title="Imprimir" class="ver" href="cliente_proveedor/cartas/imprimir.php?'.$name.'"><img src="css/images/print.png" alt="Imprimir" title="Imprimir" border="0" /></a></td>';
			echo '<td width="16"><a class="editar" name="'.$name.'" href="#"><img src="css/images/edit.png" alt="Editar" title="Editar" border="0" /></a></td>';
			echo '<td width="16"><a class="anular" name="'.$name.'" href="#"><img src="css/images/trash.png" alt="Borrar" title="Borrar" border="0" /></a></td>';
			echo '</tr>';
		}
	}
	?>
	</tbody>
</table>
<?= $paging->fetchNavegacion() ?>
