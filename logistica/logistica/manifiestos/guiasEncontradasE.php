<?php
/**
 * Guias encontradas, este archivo es usado en EDITAR PLANILLAS
 */

require_once "../../seguridad.php";
require_once Logistica::$root."class/guias.class.php";
$guias_encontradas = Guia::search_in_bodega($_GET['termino'], $_GET['opcion']);
$paging = new PHPPaging('GuiasEncontradas', $guias_encontradas);
$paging->agregarConsulta($guias_encontradas);
$paging->funcion = 'paginar_encontradas';
$paging->ejecutar();
$cantidad = $paging->numTotalRegistros();
?>
<script>
$(function(){
	$('.asignar').click(function(event){
		event.preventDefault();
		$('#cargando2').slideDown();
		var posicion=parseInt($('#cantidad').val())+1;
		$.ajax({
			url: manifiestos_path+'asignarGuia.php',
			data: this.name+'&posicion='+posicion,
			success: function(msj){
				if(msj!=0){
					$('#cargando3').slideDown();
					$('#GuiasEncontradas').load(manifiestos_path+'guiasEncontradasE.php?'+$('#formbuscar').serialize()+'&idplanilla='+$('#idplanilla').val(),function(){
						$('#cargando2').fadeOut(600);
					});
					$('#GuiasAsignadas').load(manifiestos_path+'guiasAsignadas.php?id='+$('#idplanilla').val(), function(){
						$('#cargando3').fadeOut(600);
					});
				}else{
					alert("Ha ocurrido un error al asignar la guia... intentalo nuevamente.");
				}
			}
		});
	});
});
function paginar_encontradas(d,url){
	$('#'+d).load(url);
}
</script>
<table>
	<tr>
		<td>Guías encontradas: <?= $cantidad ?></td>
		<td><div id="cargando2" style="display: none;"><img src="css/ajax-loader.gif" alt="cargando" /></div></td>
	</tr>
</table>
<table class="table table-condensed table-bordered table-hover">
	<thead>
		<tr>
			<th>No.</th>
			<th>Remitente</th>
			<th>Destinatario</th>
			<th>Mercancía Recibida</th>
			<th width="16"></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if ($cantidad > 0) {
		while ($gui = $paging->fetchResultado()) {
      $cliente = trim($gui['cliente_n'].' '.$gui['cliente_pa'].' '.$gui['cliente_sa']);
      $contacto = trim($gui['nombrecontacto'].' '.$gui['primer_apellido_contacto'].' '.$gui['segundo_apellido_contacto']);
  ?>
			<tr id="fila-<?= $gui['id'] ?>">
				<!-- <td><input type="checkbox" id="<?= $gui['id'] ?>" /></td> -->
				<td title="Numero anterior: <?= $gui['numero'] ?>"><?= $gui['id'] ?></td>
				<td><abbr title="<?= $cliente ?>"><?= substr($cliente, 0, 30) ?></abbr></td>
				<td><abbr title="<?= $contacto ?>"><?= substr($contacto, 0, 30) ?></abbr></td>
				<?= '<td><abbr title="'.htmlentities(strftime('%A, %d de %B de %Y', strtotime($gui['fecha_recibido_mercancia']))).'">'.$gui['fecha_recibido_mercancia'].'</abbr></td>' ?>
				<td>
				<?php
				if ($gui['fecha_recibido_mercancia']<=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-3,date("Y")))) {
					echo '<img src="img/red16.png" alt="rojo" title="" />';
				}elseif($gui['fecha_recibido_mercancia']>date("Y-m-d",mktime(0,0,0,date("m"),date("d")-2,date("Y"))) and $gui['fecha_recibido_mercancia']<date("Y-m-d")){
					echo '<img src="img/yellow16.png" alt="amarillo" title="" />';
				}else{
					echo '<img src="img/green16.png" alt="verde" title="" />';
				}
				?>
				</td>
				<td id="guia<?= $gui['id'] ?>">
					<a class="asignar" name="idguia=<?= $gui['id'] ?>&idplanilla=<?= $_GET['idplanilla'] ?>" href="#">
						<img src="css/images/add16.png" alt="Asignar" title="Asignar a la planilla" border="0" />
					</a>
				</td>
			</tr>
	<?php
		}
	}else{
		echo '<tr><td colspan="6" class="expand">No se encontraron guías...</td></tr>';
	}
	?>
	</tbody>
</table>
<?= $paging->fetchNavegacion() ?>
