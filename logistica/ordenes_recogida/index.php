<?php
$raiz = "../../";
require_once $raiz."seguridad.php";

if( ! isset($_SESSION['permisos'][ORDENES_RECOGIDA_ENTRAR]) ) {
	include $raiz.'mensajes/permiso.php';
	exit;
}

$orden_recogida = new OrdenRecogida;
$type = isset($_SESSION['permisos'][ORDENES_RECOGIDA_DESHACER]) ? 'todas' : 'activas';
$consulta = $orden_recogida->all($type, true);
$paging = new PHPPaging('right_content', $consulta, true);
$paging->ejecutar();
?>
<script>
$(function(){
	$('#ordenesRecogida_Crear').click(function(){
		cargarExtra(ordenes_recogida_path+'crear.php');
	});
	$(".ver").click(function(e){
		e.preventDefault();
		cargarExtra(ordenes_recogida_path+'ver.php?'+this.name);
	});
	$(".editar").click(function(e){
		e.preventDefault();
		cargarExtra(ordenes_recogida_path+'editar.php?'+this.name);
	});
	$('#ordenes_recogida_list').on('click', 'a.anular', function(e){
		e.preventDefault();
		var msg = confirm("¿Deseas borrar esta orden de recogida?");
		if(!msg) return;
		$.ajax({
			url: ordenes_recogida_path+'anular.php',
			type: "POST",
			data: this.name,
			success: function(msj){
				if(msj=='id'){
					alert('Algo ha salido mal, recarga la página e intentalo nuevamente.');
				}else if(msj=='permiso'){
					alert('Ahora no tienes permisos para borrar ordenes de recogida.');
				}else if(msj=='ok'){
					cargarPrincipal(ordenes_recogida_path+'index.php?pagina='+$('#pagina').val());
				}else{
					alert("Ha ocurrido un error al borrar, intentalo nuevamente.");
				}
			}
		});
	});
	$('#ordenes_recogida_list').on('click', 'a.deshacer', function(e){
		e.preventDefault();
		var conf = confirm("¿Deseas activar esta orden de recogida?");
		if(!conf) return;
		$.ajax({
			url: ordenes_recogida_path+'deshacer.php',
			type: "POST",
			data: this.name,
			success: function(msj){
				if(msj=='id'){
					alert('Algo ha salido mal, recarga la página e intentalo nuevamente.');
				}else if(msj=='permiso'){
					alert('Ahora no tienes permisos para activar ordenes de recogida.');
				}else if(msj=='ok'){
					cargarPrincipal(ordenes_recogida_path+'index.php?pagina='+$('#pagina').val());
				}else{
					alert("Ha ocurrido un error al activar, intentalo nuevamente.");
				}
			}
		});
	});
});

function fn_paginar(d, url){
	$('#cargando').slideDown();
	$('.'+d).load(url);
}
</script>
<div class="btn-toolbar pull-right">
<?php if( isset($_SESSION['permisos'][ORDENES_RECOGIDA_CREAR]) )
	echo '<button class="btn btn-info" id="ordenesRecogida_Crear"><i class="icon-plus"></i> Crear Orden de Recogida</button>';
?>
</div>
<h2>Ordenes de Recogida</h2>
<table id="ordenes_recogida_list" class="table table-hover table-condensed table-bordered">
	<thead>
		<tr>
			<th>ID</th>
			<th>Fecha</th>
			<th>Vehículo</th>
			<th>Conductor</th>
			<th>Ruta</th>
			<th style="width: 120px">Acción</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ($paging->numTotalRegistros() == 0) {
			echo '<tr class="warning"><td class="expand" colspan="6">No se encontraron ordenes...</td></tr>';
		}else{
			while( $orden = $paging->fetchResultado('OrdenRecogida') ){
				$c = $orden->activa == 'no' ? 'error' : '';
				echo '<tr class="'.$c.'">';
				echo '<td>'.$orden->id.'</td>';
				echo '<td>'.$orden->fecha.'</td>';
				echo '<td>'.$orden->placa_vehiculo.'</td>';
				echo '<td>'.$orden->conductor()->nombre_completo.'</td>';
				echo '<td>'.$orden->ruta.'</td>';
				$name="id=".$orden->id."&".nonce_create_query_string($orden->id);
				echo '<td><div class="btn-group">';
				if ( isset($_SESSION['permisos'][ORDENES_RECOGIDA_VER]) ) {
					echo '<a href="#" name="'.$name.'" class="btn ver" title="Ver"><i class="icon-search"></i></a>';
				}
				if ( isset($_SESSION['permisos'][ORDENES_RECOGIDA_EDITAR]) and $orden->activa=='si') {
					echo '<a href="#" name="'.$name.'" class="btn editar" title="Editar"><i class="icon-pencil"></i></a>';
				}
				if ( isset($_SESSION['permisos'][ORDENES_RECOGIDA_IMPRIMIR]) and $orden->activa=='si') {
					echo '<a target="_blank" href="logistica/ordenes_recogida/imprimir?'.$name.'" class="btn imprimir" title="Imprimir"><i class="icon-print"></i></a>';
				}
				if ( isset($_SESSION['permisos'][ORDENES_RECOGIDA_ANULAR]) and $orden->activa=='si') {
					echo '<a href="#" name="'.$name.'" class="btn anular btn-danger" title="Borrar"><i class="icon-trash"></i></a>';
				}elseif( isset($_SESSION['permisos'][ORDENES_RECOGIDA_DESHACER]) and $orden->activa=='no'){
					echo '<a href="#" name="'.$name.'" class="btn deshacer btn-success" title="Activar"><i class="icon-ok"></i></a>';
				}
				echo '</div></td>';
				echo '</tr>';
			}
    	}?>
    </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
<input type="hidden" id="pagina" value="<?= $paging->numEstaPagina() ?>" />
