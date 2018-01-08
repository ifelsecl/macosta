<?php
exit('<div class="page-header"><h2>Modulo Descontinuado...</h2></div>');
$raiz='../../';
require_once $raiz."seguridad.php";
if( !isset($_SESSION['permisos'][BACKUP_ENTRAR]) ){
	include $raiz."mensajes/permiso.php";
	exit;
}
//Eliminar cualquier archivo anterior.
$nombre='Backup.'.date('d.m.Y').'.zip';
@unlink($nombre);
?>
<script type="text/javascript">
$(function(){
	var ruta='configuracion/backup/';
	$('#continuar').button({icons: {primary: 'ui-icon-disk'}});
});
</script>
<h2>Copia de seguridad</h2>
<hr />
<form id="Exportar" target="_blank" action="configuracion/backup/exportar" method="post">
	<table>
		<tr>
			<td>
				<b>La siguiente informaci&oacute;n ser&aacute; guardada:</b>
				<ul>
					<li>La base de datos completa</li>
					<li>Im&aacute;genes de las gu&iacute;as escaneadas</li>
					<li>Im&aacute;genes de los conductores</li>
				</ul>
			</td>
		</tr>
	</table>
	<hr />
	<center><button id="continuar">Continuar</button></center>
</form>