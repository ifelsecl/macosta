<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PLANILLAS_CREAR_INFORME])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

if (isset($_POST['exportar'])) {
	$manifiestos = Manifiesto::export($_POST);
	Logistica::unregister_autoloaders();
	require Logistica::$root.'php/excel/PHPExcel.php';
	if ($_POST['tipo'] == 'fecha') {
		$inicio = strftime('%d/%b/%Y', strtotime($_POST['inicio']));
		$fin = strftime('%d/%b/%Y', strtotime($_POST['fin']));
	} else {
		$inicio = $_POST['inicio'];
		$fin = $_POST['fin'];
	}
	if (empty($manifiestos)) {
		exit('<h2>No se encontraron manifiestos...</h2>');
	}
	$objPHPExcel = new PHPExcel;
	$objPHPExcel->getProperties()
		->setCreator("Logistica")
		->setLastModifiedBy("Logistica")
		->setTitle("Informe de Manifiestos")
		->setSubject("Informe de Manifiestos");
	$sheet = $objPHPExcel->setActiveSheetIndex(0);
	$sheet->setCellValue('A1', 'Informe de Manifiestos '.$inicio.' a '.$fin)
		->setCellValue('A3', 'Número')
		->setCellValue('B3', 'Fecha')
		->setCellValue('C3', 'Placa')
		->setCellValue('D3', 'Conductor')
		->setCellValue('E3', 'Destino');
	$i = 4;
	foreach ($manifiestos as $manifiesto) {
		$sheet->setCellValue('A'.$i, $manifiesto->id);
		$sheet->setCellValue('B'.$i, $manifiesto->fecha);
		$sheet->setCellValue('C'.$i, $manifiesto->placacamion);
		$sheet->setCellValue('D'.$i, $manifiesto->conductor_nombre_completo);
		$sheet->setCellValue('E'.$i, $manifiesto->ciudad_destino_nombre);
		$i++;
	}

	$filename = "Informe_Manifiestos_".$inicio."_".$fin.".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}
?>
<script>
$(function() {
	var dates = $("#fecha_inicio, #fecha_fin").datepicker({
		changeMonth: true,
		changeYear: true,
		numberOfMonths: 3,
		showOn: "both",
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		dateFormat: 'yy-mm-dd',
		buttonText: 'Seleccionar...',
		autoSize: true,
		onSelect: function(selectedDate) {
			var option = this.id == "fecha_inicio" ? "minDate" : "maxDate";
			dates.not(this).datepicker("option", option, selectedDate);
		}
	});
	$('input[name="tipo"]').change(function() {
		if ($(this).val()=='fecha') {
			$('.exp_fecha').slideDown(600);
			$('.exp_fecha input').removeAttr('disabled');
			$('.exp_rango').slideUp(600);
			$('.exp_rango input').attr('disabled','disabled');
		} else {
			$('.exp_fecha').slideUp(600);
			$('.exp_fecha input').attr('disabled','disabled');
			$('.exp_rango').slideDown(600);
			$('.exp_rango input').removeAttr('disabled');
		}
	});
	$('#Exportar').validate({
		rules: {
			fecha_inicio: 'required',
			fecha_fin: 'required',
			rango_inicio: {required: true, digits: true, max: function() {return $('#rango_fin').val()}},
			rango_fin: {required: true, digits: true, min: function() {return $('#rango_inicio').val()}}
		},
		messages: {
			fecha_inicio: 'Selecciona la fecha de inicio',
			fecha_fin: 'Selecciona la fecha de fin',
			rango_inicio: {required: 'Escribe el numero', digits: 'Solo numeros enteros', max: 'El inicio debe ser menor o igual a fin'},
			rango_fin: {required: 'Escribe el numero', digits: 'Solo numeros enteros', min: 'El fin debe ser mayor o igual a inicio'}
		},
		errorPlacement: function(er, el) {er.appendTo( el.parent("td").next("td") );},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function(f) {
			f.submit();
		}
	});
	$('#exportar').button({icons: {primary: 'ui-icon-circle-check'}});
});
</script>
<button title="Regresar a Manifiestos" onclick="regresar();" class="btn btn-success pull-right">Regresar</button>
<h2>Informe de Manifiestos</h2>
<hr class="hr-small">
<form id="Exportar" method="post" target="_blank" action="<?= $_SERVER['PHP_SELF'] ?>">
	Crear un nuevo informe de manifiestos por:
	<ul class="inline">
		<li><label for="t_fecha"><input type="radio" name="tipo" id="t_fecha" value="fecha" checked="checked" />Fecha</label></li>
		<li><label for="t_rango"><input type="radio" name="tipo" id="t_rango" value="rango" />Rango</label></li>
	</ul>
	<div style="width:auto;padding:10px;" class="exp_fecha ui-widget-content ui-corner-all">
		<table>
			<tr>
				<td><b>Inicio:</b></td>
				<td><input type="text" class="input-medium" readonly="readonly" id="fecha_inicio" name="inicio" /></td>
				<td></td>
			</tr>
			<tr>
				<td><b>Fin:</b></td>
				<td><input type="text" class="input-medium" readonly="readonly" id="fecha_fin" name="fin" /></td>
				<td></td>
			</tr>
		</table>
	</div>
	<div style="width:auto;padding:10px;display:none;" class="exp_rango ui-widget-content ui-corner-all">
		<table>
			<tr>
				<td><b>Inicio:</b></td>
				<td><input type="text" class="input-medium" disabled="disabled" id="rango_inicio" name="inicio" /></td>
				<td></td>
			</tr>
			<tr>
				<td><b>Fin:</b></td>
				<td><input type="text" class="input-medium" disabled="disabled" id="rango_fin" name="fin" /></td>
				<td></td>
			</tr>
		</table>
	</div>
	<br>
	<center><button class="" id="exportar">Crear</button></center>
	<input type="hidden" name="exportar" value="si" />
</form>
