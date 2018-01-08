<?php
require '../../seguridad.php';
if (! isset($_POST['guias'])) exit('Debes agregar por lo menos una guia.');

$guias = Guia::find_by_id(implode('-', $_POST['guias']));
if (empty($guias)) exit('No se encontraron guias.');
$count = count($guias);

$template = "nuevoformato.xlsx";
if (! is_readable($template)) exit('El template no se puede leer.');

$cita = (object) $_POST['cita'];
Logistica::unregister_autoloaders();
require Logistica::$root.'php/excel/PHPExcel.php';
$excel = PHPExcel_IOFactory::load($template);
$excel->getSecurity()
  ->setLockWindows(true)
  ->setLockStructure(true)
  ->setWorkbookPassword("TMA!");

$pages = ceil($count / 3);
for ($i = 0; $i < $pages; $i++) {
  $sheet = $excel->setActiveSheetIndex($i);
  $sheet->setCellValue('A6', $cita->fecha_pedido);
  $sheet->setCellValue('I5', $cita->fecha_cita);
  $slice = array_slice($guias, $i * 3, 3);

  $guia = $slice[0];
  $sheet->setCellValue('E9',  $guia->cliente->nombre_completo());
  $sheet->setCellValue('E10', $guia->cliente_numero_identificacion);
  $sheet->setCellValue('E11', $guia->cliente->direccion);
  $sheet->setCellValue('E12', $guia->cliente_ciudad_nombre);
  $sheet->setCellValue('E13', $guia->cliente->telefono_completo());
  $sheet->setCellValue('E14', $guia->cliente->nombre_completo());
  $sheet->setCellValue('I9',  $guia->contacto->nombre_completo());
  $sheet->setCellValue('I10', $guia->contacto->numero_identificacion);
  $sheet->setCellValue('I11', $guia->contacto->direccion);
  $sheet->setCellValue('I12', $guia->contacto_ciudad_nombre);
  $sheet->setCellValue('I13', $guia->contacto->telefono_completo());
  $sheet->setCellValue('I14', $guia->contacto->nombre_completo());
  $sheet->setCellValue('L9',  $guia->unidades);
  $sheet->setCellValue('N9',  $guia->peso);
  $sheet->setCellValue('O9',  $guia->observacion);

  if (isset($slice[1])) {
    $guia = $slice[1];
    $sheet->setCellValue('E16',  $guia->cliente->nombre_completo);
    $sheet->setCellValue('E17', $guia->cliente_numero_identificacion);
    $sheet->setCellValue('E18', $guia->cliente->direccion);
    $sheet->setCellValue('E19', $guia->cliente_ciudad_nombre);
    $sheet->setCellValue('E20', $guia->cliente->telefono_completo());
    $sheet->setCellValue('E21', $guia->cliente->nombre_completo);
    $sheet->setCellValue('I16',  $guia->contacto->nombre_completo);
    $sheet->setCellValue('I17', $guia->contacto->numero_identificacion);
    $sheet->setCellValue('I18', $guia->contacto->direccion);
    $sheet->setCellValue('I19', $guia->contacto_ciudad_nombre);
    $sheet->setCellValue('I20', $guia->contacto->telefono_completo());
    $sheet->setCellValue('I21', $guia->contacto->nombre_completo);
    $sheet->setCellValue('L16',  $guia->unidades);
    $sheet->setCellValue('N16',  $guia->peso);
    $sheet->setCellValue('O16',  $guia->observacion);
  }

  if (isset($slice[2])) {
    $guia = $slice[2];
    $sheet->setCellValue('E23',  $guia->cliente->nombre_completo());
    $sheet->setCellValue('E24', $guia->cliente_numero_identificacion);
    $sheet->setCellValue('E25', $guia->cliente->direccion);
    $sheet->setCellValue('E26', $guia->cliente_ciudad_nombre);
    $sheet->setCellValue('E27', $guia->cliente->telefono_completo());
    $sheet->setCellValue('E28', $guia->cliente->nombre_completo());
    $sheet->setCellValue('I23',  $guia->contacto->nombre_completo());
    $sheet->setCellValue('I24', $guia->contacto->numero_identificacion);
    $sheet->setCellValue('I25', $guia->contacto->direccion);
    $sheet->setCellValue('I26', $guia->contacto_ciudad_nombre);
    $sheet->setCellValue('I27', $guia->contacto->telefono_completo());
    $sheet->setCellValue('I28', $guia->contacto->nombre_completo());
    $sheet->setCellValue('L23',  $guia->unidades);
    $sheet->setCellValue('N23',  $guia->peso);
    $sheet->setCellValue('O23',  $guia->observacion);
  }

}
$excel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Cita Muelle.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$objWriter->save('php://output');
