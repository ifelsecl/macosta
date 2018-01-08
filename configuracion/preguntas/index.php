<?php
require "../../seguridad.php";
if(! isset($_SESSION['permisos'][PREGUNTAS_ENTRAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
$guia = new Guia;
?>
<script>
$(function(){
	var ruta='configuracion/preguntas/';
	$('a.borrar').click(function(e){
		e.preventDefault();
		var conf=confirm('Deseas borrar esta pregunta?');
		if(!conf) return;
		$.ajax({
			url: ruta+'ajax.php',type: 'POST',
			data: this.name+'&borrar=121',
			success: function(m){
				if(m=='ok'){
					cargarPrincipal(ruta);
				}else{
					alert('Ha ocurrido un error, intentalo nuevamente.');
				}
			}
		});
	});
	$('button#crear').click(function(){
		LOGISTICA.Dialog.open('Nueva Pregunta',ruta+'crear.php');
	});
	$('a.editar').click(function(e){
		e.preventDefault();
		LOGISTICA.Dialog.open('Editar Pregunta',ruta+'editar.php?'+this.name);
	});
});
</script>
<?php if (isset($_SESSION['permisos'][PREGUNTAS_AGREGAR])) { ?>
	<button id="crear" class="btn btn-info pull-right"><i class="icon-plus"></i> Agregar</button>
<?php } ?>
<h2>Preguntas sobre la mercancía</h2>
<table class="table table-hover table-bordered">
	<thead>
		<tr>
			<th>Razón</th>
			<th>Editar</th>
			<th>Borrar</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($guia->razones_devolucion() as $razon) {
			echo '<tr>';
			echo '<td>'.$razon->nombre.'</td>';
			if(isset($_SESSION['permisos'][PREGUNTAS_EDITAR])){
				echo '<td width="16"><a href="#" class="btn editar" title="Editar" name="id='.$razon->id.'"><i class="icon-pencil"></i></a></td>';
			}else{
				echo '<td width="16"></td>';
			}
			if(isset($_SESSION['permisos'][PREGUNTAS_ELIMINAR])){
				echo '<td width="16"><a href="#" class="btn borrar btn-danger" title="Borrar" name="id='.$razon->id.'"><i class="icon-trash"></i></a></td>';
			}else{
				echo '<td width="16"></td>';
			}
			echo '</tr>';
		}
		?>
	</tbody>
</table>
