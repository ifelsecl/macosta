<?php
require "../seguridad.php";
if (! isset($_SESSION['permisos'][FACTURACION_CREAR_INFORMES])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (isset($_POST['exportar'])) {
  $cliente_id = empty($_POST['cliente_id']) ? null : $_POST['cliente_id'] ;
  if ($_POST['tipo'] == 'fecha') {
    $inicio = strftime('%d/%b/%Y', strtotime($_POST['date_start']));
    $fin = strftime('%d/%b/%Y', strtotime($_POST['date_end']));
    $facturas = Factura::all_by_range('fecha', $cliente_id, $_POST['date_start'], $_POST['date_end']);
  } else {
    $inicio = $_POST['range_start'];
    $fin = $_POST['range_end'];
    $facturas = Factura::all_by_range('rango', $cliente_id, $_POST['range_start'], $_POST['range_end']);
  }
  if (empty($facturas)) exit('<h2>No se encontraron facturas, intenta con opciones diferentes.</h2>');
  Logistica::unregister_autoloaders();
  require Logistica::$root.'php/excel/PHPExcel.php';

  $objPHPExcel = new PHPExcel;
  $objPHPExcel->getProperties()
    ->setCreator("Logistica")
    ->setLastModifiedBy("Logistica")
    ->setTitle("Informe de Facturacion")
    ->setSubject("Informe de Facturacion");
  $sheet = $objPHPExcel->setActiveSheetIndex(0);
  $sheet->setCellValue('A1', 'Informe de Facturacion '.$inicio.' a '.$fin)
    ->setCellValue('A3', 'Número')
    ->setCellValue('B3', 'Cliente')
    ->setCellValue('C3', 'Emisión')
    ->setCellValue('D3', 'Vencimiento')
    ->setCellValue('E3', 'Tipo')
    ->setCellValue('F3', 'Seguro')
    ->setCellValue('G3', 'Flete')
    ->setCellValue('H3', 'Descuento')
    ->setCellValue('I3', 'Total');
  $i = 4;
  $total_flete = 0;
  $total_seguro = 0;
  $total_descuento = 0;
  foreach ($facturas as $factura) {
    $sheet->setCellValue('A'.$i, $factura->id);
    $sheet->setCellValue('B'.$i, $factura->cliente_nombre_completo);
    $sheet->setCellValue('C'.$i, $factura->fecha_emision_corta());
    $sheet->setCellValue('D'.$i, $factura->fecha_vencimiento_corta());
    $sheet->setCellValue('E'.$i, $factura->tipo);
    $sheet->setCellValue('F'.$i, $factura->seguro());
    $sheet->setCellValue('G'.$i, $factura->flete());
    $sheet->setCellValue('H'.$i, $factura->descuento());
    $sheet->setCellValue('I'.$i, $factura->total_neto());
    if ($factura->activa == 'no') {
      $sheet->setCellValue('J'.$i, 'ANULADA');
    }
    $total_flete += $factura->flete();
    $total_seguro += $factura->seguro();
    $total_descuento += $factura->descuento();
    $i++;
  }
  $sheet->setCellValue('C'.$i, 'TOTAL');
  $sheet->setCellValue('D'.$i, '');
  $sheet->setCellValue('E'.$i, '');
  $sheet->setCellValue('F'.$i, $total_seguro);
  $sheet->setCellValue('G'.$i, $total_flete);
  $sheet->setCellValue('H'.$i, $total_descuento);
  $sheet->setCellValue('I'.$i, ($total_seguro + $total_flete - $total_descuento));

  $filename = "Informe_Facturacion_".$inicio."_".$fin.".xlsx";
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename="'.$filename.'"');
  header('Cache-Control: max-age=0');

  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  $objWriter->save('php://output');
  exit;
}
?>
<div id="facturacion__informe">
  <div class="row-fluid">
    <div class="span10">
      <h2>Informe de Facturación</h2>
    </div>
    <div class="span2">
      <button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span12">
      <p class="muted">Cree un informe de facturación por rango de fechas o rango de números en formato Excel</p>
      <form id="Exportar" method="post" target="_blank" action="<?= $_SERVER['PHP_SELF'] ?>" class="form-horizontal">
         <div class="control-group">
          <label class="control-label" for="facturacion__informe__cliente_nombre">Cliente:</label>
          <div class="controls">
            <input type="text" name="cliente_nombre" id="facturacion__informe__cliente_nombre">
            <input type="hidden" name="cliente_id" id="facturacion__informe__cliente_id">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="facturacion__informe__tipo_fecha">Crear informe por:</label>
          <div class="controls">
            <label class="radio inline" for="facturacion__informe__tipo_fecha"><input type="radio" name="tipo" id="facturacion__informe__tipo_fecha" value="fecha" checked="checked" />Fecha</label>
            <label class="radio inline" for="facturacion__informe__tipo_rango"><input type="radio" name="tipo" id="facturacion__informe__tipo_rango" value="rango" />Rango</label>
          </div>
        </div>
        <div id="facturacion__informe__by_date" style="padding:10px;" class="control-group">
          <div class="controls">
            <table style="width:auto;">
              <tr>
                <td><b>Inicio:</b></td>
                <td><input type="text" class="input-small" id="date_start" name="date_start" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Fin:</b></td>
                <td><input type="text" class="input-small" id="date_end" name="date_end" /></td>
                <td></td>
              </tr>
            </table>
          </div>
        </div>
        <div id="facturacion__informe__by_range" style="padding:10px;display:none;" class="control-group">
          <div class="controls">
            <table style="width:auto;">
              <tr>
                <td><b>Inicio:</b></td>
                <td><input type="text" class="input-small" disabled="disabled" id="range_start" name="range_start" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Fin:</b></td>
                <td><input type="text" class="input-small" disabled="disabled" id="range_end" name="range_end" /></td>
                <td></td>
              </tr>
            </table>
          </div>
        </div>
        <center class="form-actions">
          <button class="btn btn-info btn-large" id="crear_informe"><i class="icon-download-alt"></i> Crear Informe</button>
        </center>
        <input type="hidden" name="exportar" value="si" />
      </form>
    </div>
  </div>
</div>
<script>
(function() {
  LOGISTICA.facturacion.informe = function() {
    var $el = $('#facturacion__informe');
    var $form = $el.find('form');

    var initAutocomplete = function() {
      $el.find('#facturacion__informe__cliente_nombre')
        .typeahead({
          name: 'clientes',
          valueKey: 'nombre',
          minLength: 3,
          limit: 10,
          remote: helpers_path+'ajax.php?cliente=1&term=%QUERY',
          template: [
            '<p class="client-name">{{nombre}}</p>',
            '<p class="client-address">{{direccion}} - {{nombre_ciudad}}</p>'
          ].join(''),
          engine: Hogan
        })
        .on('typeahead:selected', function(object, cliente) {
          $('#facturacion__informe__cliente_nombre').val(cliente.nombre);
          $('#facturacion__informe__cliente_id').val(cliente.id);
        });
    };

    var initDatePicker = function() {
      var dates = $el.find("#date_start, #date_end").datepicker({
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
          var option = this.id == "date_start" ? "minDate" : "maxDate";
          dates.not( this ).datepicker("option", option, selectedDate);
        }
      });
    };

    var bindEvents = function() {
      $el.find('input[name="tipo"]').change(function() {
        var $hidden_inputs, $shown_inputs;
        if ($(this).val()=='fecha') {
          $hidden_inputs = $el.find('#facturacion__informe__by_range');
          $shown_inputs = $el.find('#facturacion__informe__by_date');
        } else {
          $hidden_inputs = $el.find('#facturacion__informe__by_date');
          $shown_inputs = $el.find('#facturacion__informe__by_range');
        }
        $shown_inputs.slideDown(600);
        $shown_inputs.find('input').prop('disabled', false);
        $hidden_inputs.slideUp(600);
        $hidden_inputs.find('input').prop('disabled', true);
      });
    };

    var initFormValidator = function() {
      $form.validate({
        rules: {
          date_start: 'required',
          date_end: 'required',
          range_start: {required: true, digits: true, max: function() {return $el.find('#range_end').val()}},
          range_end: {required: true, digits: true, min: function() {return $el.find('#range_start').val()}}
        },
        messages: {
          date_start: 'Selecciona la fecha de inicio',
          date_end: 'Selecciona la fecha de fin',
          range_start: {max: 'El inicio debe ser menor o igual a fin'},
          range_end: {min: 'El fin debe ser mayor o igual a inicio'}
        },
        errorPlacement: function(er, el) {er.appendTo( el.parent("td").next("td") );},
        highlight: function(input) {
          if ($(input).attr('id') == 'facturacion__informe__cliente_id') {
            input = '#facturacion__informe__cliente_nombre';
          }
          $(input).addClass("ui-state-highlight");
        },
        unhighlight: function(input) {
          if ($(input).attr('id') == 'facturacion__informe__cliente_id') {
            input = '#facturacion__informe__cliente_nombre';
          }
          $(input).removeClass("ui-state-highlight");
        },
        submitHandler: function(f) {
          f.submit();
        }
      });
    };

    return {
      init: function() {
        initAutocomplete();
        initDatePicker();
        bindEvents();
        initFormValidator();
        $el.find('#facturacion__informe__cliente_nombre').focus();
      }
    }
  }();

  LOGISTICA.facturacion.informe.init();
})();
</script>
