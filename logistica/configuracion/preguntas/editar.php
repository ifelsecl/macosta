<?php
$raiz='../..';
require_once "$raiz/seguridad.php";
if (!isset($_SESSION['permisos'][PREGUNTAS_EDITAR])) {
	include "$raiz/mensajes/permiso.php";
	exit;
}

require_once "$raiz/class/guias.class.php";
$objGuia=new Guias;
if(!$result=$objGuia->ObtenerInfoRazonDevolucion($_REQUEST['id'])){
	exit('<h2>No se pudo cargar la informaci&oacute;n, intentalo nuevamente.</h2>');
}
$razon=mysql_fetch_assoc($result);
?>
<script>
$(function(){
	var ruta = 'configuracion/preguntas/';
	$('#guardar').button({icons: {primary: 'ui-icon-circle-check'}});
	$('#nombre').focus();
	$('#CrearRazon').submit(function(e){
		e.preventDefault();
		if($.trim($('#nombre').val())){
			$.ajax({
				url: ruta+'ajax.php',
				type: 'POST',
				data: 'editar=191&'+$(this).serialize(),
				success: function(m){
					if(m=='ok'){
						cargarPrincipal(ruta, cerrarDialogo());
					}else alert('Ha ocurrido un error, intentalo nuevamente.');
				}
			});
		}else{
			$('#nombre').addClass('ui-state-highlight').focus();
		}
	});
});
</script>
<h5>Editar '<?= $razon['nombre'] ?>'</h5>
<form id="CrearRazon" action="#" method="post">
	<table>
		<tr>
			<td><b>Nombre</b></td>
			<td>
				<input type="text" id="nombre" name="nombre" value="<?= $razon['nombre'] ?>" />
				<input type="hidden" id="id" name="id" value="<?= $razon['id'] ?>" />
			</td>
		</tr>
	</table>
	<hr class="hr-small">
	<center><button id="guardar">Guardar</button></center>
</form>