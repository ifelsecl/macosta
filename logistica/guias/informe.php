<?php
require '../../seguridad.php';

if (isset($_REQUEST['crear_informe'])) {
	require_once Logistica::$root."php/Excel.inc.php";
	if (! isset($_REQUEST['estados'])) {
		echo '<h1>Selecciona por lo menos un estado.</h1>';
		exit;
	}
	require_once Logistica::$root."class/guias.class.php";
	$objGuia = new Guias;
	$guias = array();
	foreach($_REQUEST['estados'] as $estado) {
		$fecha_inicio = $_REQUEST['inicio'];
		$fecha_fin = $_REQUEST['fin'];
		if (! $result = $objGuia->ObtenerPorEstado($estado, $fecha_inicio, $fecha_fin) ) {
			echo '<h2>Algo ha salido mal...</h2>';
			exit;
		}
		while ($row = mysql_fetch_assoc($result)) {
			$guias[] = $row;
		}
	}
	if (! empty($guias) ) {
		if (empty($fecha_inicio) and empty($fecha_fin)) {
			$nombre = "Informe_Guias.xls";
			$titulo = 'Informe de Guias';
		} else {
			if (! empty($fecha_fin) and ! empty($fecha_inicio) ) {
				$nombre = "Informe_Guias_".$fecha_inicio."__".$fecha_fin.".xls";
				$titulo = "Informe de Guias Entre $fecha_inicio - $fecha_fin";
			}elseif (empty($fecha_fin) and ! empty($fecha_inicio) ) {
				$nombre = "Informe_Guias_desde_$fecha_inicio.xls";
				$titulo = "Informe de Guias desde $fecha_inicio";
			} else {
				$nombre = "Informe_Guias_hasta_$fecha_fin.xls";
				$titulo = "Informe de Guias hasta $fecha_fin";
			}
		}
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-type: application/vnd.ms-excel; charset=utf-8');
		header("Content-Disposition: attachment; filename=$nombre");

		echo xlsBOF();
		echo xlsWriteLabel(0,1,$titulo);
		echo xlsWriteLabel(2,0,"Numero");
		echo xlsWriteLabel(2,1,"Fecha");
		echo xlsWriteLabel(2,2,"Cliente");
		echo xlsWriteLabel(2,3,"Destino");
		echo xlsWriteLabel(2,4,"Destinatario");
		echo xlsWriteLabel(2,5,"Estado");
		echo xlsWriteLabel(2,6,"Forma de Pago");
		echo xlsWriteLabel(2,7,"Documento Cliente");
		echo xlsWriteLabel(2,8,"Valor Declarado");
		echo xlsWriteLabel(2,9,"Valor Seguro");
		echo xlsWriteLabel(2,10,"Total");
		echo xlsWriteLabel(2,11,"Creada por");
		echo xlsWriteLabel(2,12,"Estado");
		echo xlsWriteLabel(2,13,"Observaciones");
		$i = 3;
		$search 	= array('ñ','Ñ','Á','É','Í','Ó','Ú');
		$replace 	= array('n','N','A','E','I','O','U');
		foreach($guias as $guia) {
			echo xlsWriteNumber($i, 0, $guia['id']);
			echo xlsWriteLabel($i, 1, strftime('%d/%b/%Y', strtotime($guia['fecha_recibido_mercancia'])));
			$cliente = trim($guia['cliente_n'].' '.$guia['cliente_pa'].' '.$guia['cliente_sa']);
			$cliente = str_replace($search, $replace, $cliente);
			echo xlsWriteLabel($i,2,$guia['idcliente'].'-'.$cliente);
			echo xlsWriteLabel($i,3,$guia['nombreciudaddestino']);
			$contacto = trim($guia['contacto_nombre'].' '.$guia['contacto_primer_apellido'].' '.$guia['contacto_segundo_apellido']);
			$contacto=str_replace($search, $replace, $contacto);
			echo xlsWriteLabel($i, 4, $contacto);
			echo xlsWriteLabel($i, 5, $guia['estado']);
			echo xlsWriteLabel($i, 6, $guia['formapago']);
			echo xlsWriteLabel($i, 7, $guia['documentocliente']);
			echo xlsWriteNumber($i, 8, round($guia['valordeclarado']));
			echo xlsWriteNumber($i, 9, round($guia['valorseguro']));
			echo xlsWriteNumber($i, 10, round($guia['total'] + $guia['valorseguro']));
			echo xlsWriteLabel($i, 11, $guia['usuario']);
			echo xlsWriteLabel($i, 12, $guia['estado']);
			echo xlsWriteLabel($i, 13, $guia['observacion']);
			$i++;
		}
		echo xlsEOF();
	} else {
		echo '<h2>No se encontraron guías...</h2>';
	}
	exit;
}
?>
<script>
$(function() {
	$('#regresar').click(function() {
		regresar();
	});
	$( "#inf_fecha_inicio, #inf_fecha_fin" ).datepicker( "destroy" );
	var dates = $("#inf_fecha_inicio, #inf_fecha_fin").datepicker({
		changeMonth: true,
		changeYear: true,
		numberOfMonths: 2,
		showOn: "both",
		maxDate: '0',
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		dateFormat: 'yy-mm-dd',
		buttonText: 'Seleccionar...',
		autoSize: true,
		onSelect: function(selectedDate) {
			var option = this.id == "inf_fecha_inicio" ? "minDate" : "maxDate";
			dates.not(this).datepicker("option", option, selectedDate);
		}
	});
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Nuevo Informe de Guías</h2>
<hr class="hr-small">
<form method="post" id="FormInforme" target="_blank" action="<?= $_SERVER['PHP_SELF'] ?>">
	<input type="hidden" name="crear_informe" value="si" />
	<fieldset>
		<legend>Fecha</legend>
		<table>
			<tr>
				<td>Inicio:</td>
				<td>
					<input type="text" class="input-small" name="inicio" id="inf_fecha_inicio" />
				</td>
			</tr>
			<tr>
				<td>Fin:</td>
				<td>
					<input type="text" class="input-small" name="fin" id="inf_fecha_fin" />
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Estados</legend>
		<table>
		<?php
		foreach (Guia::$estados as $key => $value) {
			echo '<tr>';
			echo '<td><input id="estado_'.$key.'" type="checkbox" name="estados[]" value="'.$key.'" /></td>';
			echo '<td align="left"><label for="estado_'.$key.'">'.$value.'</label></td>';
			echo '</tr>';
		}
		?>
		</table>
	</fieldset>
	<center class="form-actions"><button id="crear_informe" class="btn btn-primary btn-large"><i class="icon-file-alt"></i> Crear Informe</button></center>
</form>
