<?php
$raiz='../..';
require_once "$raiz/seguridad.php";
if (!isset($_SESSION['permisos'][PREGUNTAS_AGREGAR])) {
	include "$raiz/mensajes/permiso.php";
	exit;
}
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
				data: 'guardar=191&nombre='+$('#nombre').val(),
				success: function(m){
					if(m=='ok'){
						$(".right_content").load(ruta, function(){
							$('#dialog').dialog('close');
						});
					}else alert('Ha ocurrido un error, intentalo nuevamente.');
				}
			});
		}else{
			$('#nombre').addClass('ui-state-highlight').focus();
		}
	});
});
</script>
<form id="CrearRazon" action="#" method="post">
	<table>
		<tr>
			<td><b>Nombre</b></td>
			<td><input type="text" id="nombre" name="nombre" /></td>
		</tr>
	</table>
	<hr class="hr-small">
	<center><button id="guardar">Guardar</button></center>
</form>