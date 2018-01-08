<?php
require '../../seguridad.php';

if (! isset($_SESSION['permisos'][VENDEDORES_ENTRAR]) ) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}
$sql = Vendedor::all('sql');
$paging = new PHPPaging('right_content', $sql);
$paging->ejecutar();
?>
<script>
$(function(){
	$( "#crear" ).click(function() {
		cargarPrincipal(vendedores_path+'crear.php');
	});

	$(".ver").click(function(event){
		event.preventDefault();
		cargarPrincipal(vendedores_path+'ver.php?'+this.name);
	});

	$(".editar").click(function(event){
		event.preventDefault();
		cargarPrincipal(vendedores_path+'editar.php?'+this.name);
	});

	$(".anular").click(function(event){
		event.preventDefault();
		var msg = confirm("¿Desea anular este vendedor?");
		if(msg){
			$.ajax({
				url: vendedores_path+'ajax.php',
				type: "POST",
				data: 'anular=1&'+this.name,
				success: function(msj){
					if(msj == 'ok'){
						cargarPrincipal(vendedores_path);
					}else{
						LOGISTICA.Dialog.open('Error',msj,true);
					}
				}
			});
		}
	});
	$(".deshacer").click(function(event){
		event.preventDefault();
		var msg = confirm("¿Desea activar este vendedor?");
		if(msg){
			$.ajax({
				url: vendedores_path+'ajax.php',
				type: "POST",
				data: 'deshacer=1&'+this.name,
				success: function(msj){
					if(msj == 'ok'){
						cargarPrincipal(vendedores_path);
					}else{
						LOGISTICA.Dialog.open('Error',msj,true);
					}
				}
			});
		}
	});
});
function fn_paginar(d,u){ $("."+d).load(u); }
</script>
<?php
if(isset($permisos[VENDEDORES_CREAR])){
	echo '<button id="crear" class="btn btn-info">Crear Vendedor</button>';
}
?>
<h2>Lista de Vendedores</h2>
<table class="table table-condensed table-bordered table-hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Nombre</th>
			<th>Cédula</th>
            <th>Ciudad</th>
            <th>Cod. SIIGO</th>
            <th style="width: 110px">Acción</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($paging->numTotalRegistros()==0) {
		echo '<tr><td colspan="6" class="expand">No se encontraron vendedores</td></tr>';
	}else{
		while( $vendedor = $paging->fetchResultado() ){
			if ($vendedor['activo']=='si') {
				echo '<tr id="fila-'.$vendedor['id'].'">';
			}else{
				echo '<tr id="fila-'.$vendedor['id'].'" class="anulado">';
			}
	    	echo '<td>'.$vendedor['id'].'</td>';
			echo '<td>'.$vendedor['nombre'].'</td>';
			echo '<td>'.$vendedor['cedula'].'</td>';
			echo '<td>'.$vendedor['ciudad'].'</td>';
			echo '<td align="center">'.$vendedor['codigo_siigo'].'</td>';
			$n='id='.$vendedor['id'].'&'.nonce_create_query_string($vendedor['id']);
			echo '<td><div class="btn-group">';
			if(isset($_SESSION['permisos'][VENDEDORES_VER])){
				echo '<a class="btn ver" name="'.$n.'" href="#"><i class="icon-search"></i></a>';
			}
			if(isset($_SESSION['permisos'][VENDEDORES_EDITAR])){
				echo '<a class="btn editar" name="'.$n.'" href="#"><i class="icon-pencil"></i></a>';
			}
	      	if(isset($_SESSION['permisos'][VENDEDORES_ANULAR]) and $vendedor['activo']=='si'){
				echo '<a class="btn anular btn-danger" name="'.$n.'" href="#"><i class="icon-trash"></i></a>';
			}elseif(isset($_SESSION['permisos'][VENDEDORES_DESHACER]) and $vendedor['activo']=='no'){
				echo '<a class="btn deshacer btn-success" name="'.$n.'" href="#"><i class="icon-ok"></i></a>';
			}
	        echo '</div></td></tr>';
		}
	}
    ?>
    </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
