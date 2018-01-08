<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][AYUDANTES_VER])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

if (! $ayudante = Ayudante::find($_GET['id'])) exit('No existe el ayudante.');
?>
<script type="text/javascript">
$(function(){
	$('#regresar').click(function(){
		regresar();
	});
});
</script>
<table style="width:100%">
	<tr>
		<td style="width:80%"><h2><?=$ayudante->nombre?></h2></td>
		<td style="width:20%">
			<button id="regresar" class="btn btn-success">Regresar</button>
		</td>
	</tr>
</table>
<hr class="hr-small">
<table border="0" cellspacing="2" cellpadding="2">
	<tr>
		<td><b>Tipo de Identificación</b></td>
		<td>
			<?= $ayudante->tipo_identificacion ?>
		</td>
	</tr>
	<tr>
		<td><b>Número de Identificación</b></td>
		<td><?= $ayudante->numero_identificacion ?></td>
	</tr>
	<tr>
		<td><b>Ciudad</b></td>
		<td><?= $ayudante->ciudad ?></td>
	</tr>
</table>
