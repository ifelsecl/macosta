<?php
require "../seguridad.php";
if (! isset($_SESSION['permisos'][FACTURACION_GENERAR_NOTA_CREDITO])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

if (isset($_REQUEST['d'])) {
	$id_nc = $_REQUEST['id'];
	$nc = (object)Factura::ObtenerNotaCredito($id_nc);

	if (! $factura = Factura::find($_REQUEST['factura'])) exit('No se encontro la factura');
	$factura->cliente();

	Logistica::unregister_autoloaders();
	require_once Logistica::$root.'php/excel/PHPExcel.php';
	$objPHPExcel = new PHPExcel;
	$objPHPExcel = PHPExcel_IOFactory::load("nc.xlsx");
	$objPHPExcel->getProperties()
		->setCreator('Edgar Ortega Ramírez')
		->setLastModifiedBy('Logística')
		->setTitle('Nota Credito '.$id_nc)
		->setSubject('Nota Credito '.$id_nc)
		->setDescription('Nota Credito '.$id_nc)
		->setKeywords('Nota Credito '.$id_nc)
		->setCategory('Nota Credito');

	$hoja=$objPHPExcel->setActiveSheetIndex(0);
	$hoja->setTitle('Nota Credito');
	$hoja->setCellValue('F6',' '.$id_nc);
	$hoja->setCellValue('A8', $factura->cliente->nombre_completo);
	$hoja->setCellValue('E8', $factura->cliente->numero_identificacion_completo);
	$hoja->setCellValue('G8', $nc->fecha);
	$hoja->setCellValue('A10', $factura->cliente->direccion);
	$hoja->setCellValue('A14', $nc->concepto);
	$hoja->setCellValue('G14', $nc->valor);

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="NotaCredito.xlsx"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}
?>
<script>
$(function() {
	var btn_v=$('#nc_v').button({icons: {primary:'ui-icon-circle-check'}});
	var btn_g=$('#nc_g').button({icons: {primary:'ui-icon-circle-check'}});
	var btn_i=$('#nc_i').button({icons: {primary:'ui-icon-print'}});
	$('#factura').focus();
	$('#nc_fv').validate({
		rules: {
			factura: {required: true, digits: true}
		},
		errorPlacement: function(er, el) {return false;},
		highlight: function(inp) {$(inp).addClass("ui-state-highlight");},
		unhighlight: function(inp) {$(inp).removeClass("ui-state-highlight");},
		submitHandler: function (f) {
			var f=$('#factura').val();
			btn_v.button('disable').button('option','label','Validando...');
			$('#nc_fc, #nc_fe').hide();
			$.ajax({
				url: facturacion_path+'ajax.php?if=1&id='+f,
				dataType: 'json',
				success: function(r) {
					btn_v.button('enable').button('option','label','Validar');
					if (! r) {
						$('#nc_fg, #nc_fi').hide(300);
						alert('La factura '+f+' no existe.');
						return;
					}
					if (r.nc) {
						$('#nc_numero').removeAttr('disabled');
						$('#nc_numero, #nc_id').val(r.nc);
						$('#nc_fecha').val(r.fecha);
						$('#nc_accion').val('Editar');
						$('#nc_titulo').text('Editar Nota Credito '+r.nc);
						$('#nc_concepto').val(r.concepto);
						$('#nc_valor').val(r.valor);
						$('#nc_fi').show(200);
					} else {
						$('#nc_fi').hide(200);
						$('#nc_numero').val('').attr('disabled','disabled');
						$('#nc_accion').val('Crear');
						$('#nc_titulo').text('Crear Nota Credito');
					}
					$('#nc_factura, .nc_factura').val(r.id);
					$('#nc_fg').show(300);
				}
			});
		}
	});
	$('#nc_fecha').datepicker({
		changeMonth: true,
		changeYear: true,
		showOn: "both",
		buttonImage: "css/images/calendar.gif",
		buttonImageOnly: true,
		dateFormat: 'yy-mm-dd',
		buttonText: 'Seleccionar...',
		autoSize: true,
		maxDate: 0
	});
	$('#nc_fg').validate({
		rules: {
			fecha: 'required',
			concepto: 'required',
			valor: {required: true, number: true}
		},
		errorPlacement: function(er, el) {return false;},
		highlight: function(inp) {$(inp).addClass("ui-state-highlight");},
		unhighlight: function(inp) {$(inp).removeClass("ui-state-highlight");},
		submitHandler: function (f) {
			btn_g.button('option','label','Guardando...').button('disable');
			$.ajax({
				url: facturacion_path+'ajax.php',
				type: 'POST',
				data: 'gnc=1&'+$(f).serialize(),
				success: function(r) {
					btn_g.button('option','label','Guardar').button('enable');
					if (r=='ok') {
						$('#nc_fv').submit();
						alert('Se ha guardado, ahora puedes imprimirla');
					} else {
						$('#nc_fi').hide(200);
					}
				}
			});
		}
	});
});
</script>
<button id="regresar_nc" class="btn btn-success pull-right" onclick="regresar();">Regresar</button>
<h2>Generar Nota Credito</h2>
<p class="muted">Para generar o editar una nota credito, escriba el número de la factura y presione validar.</p>
<form id="nc_fv" class="form-inline" action="#" method="post">
	<table>
		<tr>
			<td><b>Factura:</b></td>
			<td>
				<input type="text" id="factura" name="factura" />
			</td>
			<td>
				<button id="nc_v">Validar</button>
			</td>
		</tr>
	</table>
</form>
<hr>
<form style="display:none;" action="#" method="post" id="nc_fg">
	<input type="hidden" name="accion" id="nc_accion" value="" />
	<input type="hidden" name="numero" id="nc_numero" />
	<table>
		<tr>
			<td colspan="2"><h3 id="nc_titulo"></h3></td>
		</tr>
		<tr>
			<td><b>Fecha:</b></td>
			<td><input type="text" name="fecha" id="nc_fecha" /></td>
		</tr>
		<tr>
			<td><b>Factura:</b></td>
			<td><input type="text" readonly="readonly" id="nc_factura" name="factura" /></td>
		</tr>
		<tr>
			<td><b>Concepto:</b></td>
			<td><textarea style="width:250px;height:50px" id="nc_concepto" name="concepto"></textarea></td>
		</tr>
		<tr>
			<td><b>Valor:</b></td>
			<td><input type="text" name="valor" id="nc_valor" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<button id="nc_g">Guardar</button>
			</td>
		</tr>
	</table>
</form>
<form style="display:none;" id="nc_fi" target="_blank" method="post" action="facturacion/nc">
	<p>
		<label class="muted">Ya se ha generado una nota credito para esta factura, ahora puedes imprimirla.</label>
		<button id="nc_i">Imprimir</button>
	</p>
	<input type="hidden" name="id" id="nc_id" value="" />
	<input type="hidden" name="d" value="1" />
	<input type="hidden" name="factura" class="nc_factura" value="1" />
</form>
