<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PLANILLAS_IMPRIMIR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
if (! isset($_GET['idplanilla']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['idplanilla'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}

if (! $manifiesto = Manifiesto::find($_GET['idplanilla'])) exit('No existe el manifiesto.');
$manifiesto->guias();
if (! $manifiesto->resolucion()) exit('No existe una resolucion para este manifiesto.');
$manifiesto->ciudad_origen();
$manifiesto->ciudad_destino();
$manifiesto->ciudad_pago_saldo();
$manifiesto->titular();
$manifiesto->conductor();
$manifiesto->vehiculo();
$manifiesto->vehiculo->propietario();
$manifiesto->vehiculo->tenedor();

$configuracion  = new Configuracion;
Logistica::unregister_autoloaders();
require_once Logistica::$root.'php/excel/PHPExcel.php';
require_once Logistica::$root.'php/NumerosALetras.php';

$objPHPExcel = new PHPExcel;

$formato = "templates/Formato.xlsx";
if (! is_readable($formato)) {
  exit('El archivo de Formato no se puede leer, verifica que no haya sido abierto.');
}
$objPHPExcel = PHPExcel_IOFactory::load($formato);
if (! $objPHPExcel) {
  exit('No se pudo cargar el formato del manifiesto...');
}
$objPHPExcel->getProperties()
  ->setCreator('Edgar Ortega Ramírez')
  ->setLastModifiedBy('Logística')
  ->setTitle('Manifiesto de Carga '.$manifiesto->id)
  ->setSubject('Manifiesto de Carga '.$manifiesto->id)
  ->setDescription('Manifiesto de Carga '.$manifiesto->id)
  ->setKeywords('Manifiesto de Carga '.$manifiesto->id)
  ->setCategory('Manifiesto de Carga ');

$objPHPExcel->getSecurity()->setLockWindows(true);
$objPHPExcel->getSecurity()->setLockStructure(true);
$objPHPExcel->getSecurity()->setWorkbookPassword("TMA!");

$ano_res = explode('-', $manifiesto->resolucion->fecha);

$hoja = $objPHPExcel->setActiveSheetIndex(0);

//$hoja->getHeaderFooter()->setOddHeader('&L&G&C&H RESOLUCIÓN NÚMERO '.$manifiesto->resolucion->numero.' DE '.$ano_res[0]);
$hoja->setTitle('Manifiesto');

$hoja->setCellValue('U4',' '.$configuracion->codigo_empresa.$manifiesto->id);

$hoja->setCellValue('B8', strtoupper($configuracion->nombre_empresa));
$hoja->setCellValue('K8', $configuracion->empresa_sigla);
$hoja->setCellValue('O8', $configuracion->nit_empresa);
$hoja->setCellValue('R8', $manifiesto->tipo());

$hoja->setCellValue('B9', strtoupper($configuracion->direccion_empresa));
$hoja->setCellValue('K9', strtoupper($configuracion->ciudad_empresa));
$hoja->setCellValue('O9', $configuracion->empresa_telefono_sede_principal);

$hoja->setCellValue('A14', date('Y/m/d', strtotime($manifiesto->fecha) ));
$hoja->setCellValue('D14', $manifiesto->ciudad_origen->nombre.' - '.$manifiesto->ciudad_origen->departamento_nombre);
$hoja->setCellValue('L14', $manifiesto->ciudad_destino->nombre.' - '.$manifiesto->ciudad_destino->departamento_nombre);
$hoja->setCellValue('U14', date('Y/m/d', strtotime($manifiesto->fecha_limite_entrega)));

$hoja->setCellValue('A16', $manifiesto->titular->nombre_completo);
$hoja->setCellValue('G16', $manifiesto->titular->numero_identificacion_completo);
$hoja->setCellValue('J16', $manifiesto->titular->direccion);
$hoja->setCellValue('R16', $manifiesto->titular->telefono);
$hoja->setCellValue('U16', $manifiesto->titular->ciudad_nombre.' - '.$manifiesto->titular->departamento_nombre);

$hoja->setCellValue('A21', $manifiesto->placacamion);
$hoja->setCellValue('B21', $manifiesto->vehiculo->marca_nombre);
$hoja->setCellValue('E21', $manifiesto->vehiculo->configuracion_nombre);
$hoja->setCellValue('J21', number_format($manifiesto->vehiculo->peso));
$hoja->setCellValue('O21', $manifiesto->vehiculo->aseguradora_nombre);
$hoja->setCellValue('U21', date('Y/m/d', strtotime($manifiesto->vehiculo->f_venc_soat)));
$hoja->setCellValue('W21', ' '.$manifiesto->vehiculo->soat);

$hoja->setCellValue('A24', $manifiesto->vehiculo->propietario->nombre_completo);
$hoja->setCellValue('G24', $manifiesto->vehiculo->propietario->numero_identificacion_completo);
$hoja->setCellValue('J24', $manifiesto->vehiculo->propietario->direccion);
$hoja->setCellValue('R24', $manifiesto->vehiculo->propietario->telefono);
$hoja->setCellValue('U24', $manifiesto->vehiculo->propietario->ciudad_nombre.' - '.$manifiesto->vehiculo->propietario->departamento_nombre);

$hoja->setCellValue('A26', $manifiesto->vehiculo->tenedor->nombre_completo);
$hoja->setCellValue('G26', $manifiesto->vehiculo->tenedor->numero_identificacion_completo);
$hoja->setCellValue('J26', $manifiesto->vehiculo->tenedor->direccion);
$hoja->setCellValue('R26', $manifiesto->vehiculo->tenedor->telefono);
$hoja->setCellValue('U26', $manifiesto->vehiculo->tenedor->ciudad_nombre.' - '.$manifiesto->vehiculo->tenedor->departamento_nombre);

$hoja->setCellValue('A28', $manifiesto->conductor->nombre_completo);
$hoja->setCellValue('G28', $manifiesto->conductor->numero_identificacion_completo);
$categoria = explode('-', $manifiesto->conductor->categorialicencia);
$hoja->setCellValue('J28', $manifiesto->conductor->categorialicencia);
$hoja->setCellValue('L28', $manifiesto->conductor->direccion);
$hoja->setCellValue('U28', $manifiesto->conductor->ciudad_nombre.' - '.$manifiesto->conductor->departamento_nombre);

if (count($manifiesto->guias) > 5) {
  $hoja->setCellValue('A33', 'REMESAS VARIAS');
  $hoja->setCellValue('B33', 'KILOGRAMO');
  $hoja->setCellValue('C33', $manifiesto->vehiculo->capacidadcarga);
  $hoja->setCellValue('D33', 1);
  $hoja->setCellValue('F33', 0);
  $hoja->setCellValue('G33', ' 009980');
  $hoja->setCellValue('H33', 'PRODUCTOS VARIOS');
  $hoja->setCellValue('M33', 'CIUDADES VARIAS');
  $hoja->setCellValue('S33', 'VARIOS');

  $hoja->setCellValue('S34', 'VARIOS');

  $hoja->setCellValue('M35', 'CIUDADES VARIAS');
  $hoja->setCellValue('S35', 'VARIOS');
} else {
  $i = 0;
  foreach ($manifiesto->guias as $g) {
    if ($i == 0) {
      $c = 33;
    } elseif ($i == 1) {
      $c = 38;
    } elseif ($i == 2) {
      $c = 47;
    } elseif ($i == 3) {
      $c = 52;
    } else {
      $c = 57;
    }
    $hoja->setCellValue('A'.$c, $g->id);
    $hoja->setCellValue('B'.$c, $g->unidadmedida);
    $hoja->setCellValue('C'.$c, $g->peso);
    $hoja->setCellValue('D'.$c, $g->naturaleza);
    $hoja->setCellValue('F'.$c, $g->empaque);
    $hoja->setCellValue('G'.$c, $g->producto_id);
    $hoja->setCellValue('H'.$c, $g->producto_nombre);
    $hoja->setCellValue('M'.$c, $g->cliente_ciudad_nombre);
    if ($g->propietario == 'Remitente') {
      $remitente = $g->cliente_nombre_completo;
      $propietario_nombre = $remitente;
      $propietario_numero_identificacion = $g->cliente_numero_identificacion;
    } else {
      $remitente = $g->contacto_nombre_completo;
      $propietario_nombre = $g->contacto_nombre_completo;
      $propietario_numero_identificacion = $g->contacto_numero_identificacion;
    }
    $hoja->setCellValue('S'.$c, $propietario_nombre);
    $hoja->setCellValue('W'.$c, $propietario_numero_identificacion);

    $hoja->setCellValue('M'.($c+1), $g->cliente_direccion);
    $hoja->setCellValue('S'.($c+1), $remitente);
    $hoja->setCellValue('W'.($c+1), $g->cliente_numero_identificacion);

    $hoja->setCellValue('M'.($c+2), $g->contacto_ciudad_nombre);
    $hoja->setCellValue('S'.($c+2), $g->contacto_nombre_completo);
    $hoja->setCellValue('W'.($c+2), $g->contacto_numero_identificacion);

    $hoja->setCellValue('M'.($c+3), $g->contacto_direccion);
    $i++;
  }
}

$hoja->setCellValue('C64', $manifiesto->valor_flete);
$hoja->setCellValue('K64', $manifiesto->observaciones);

$hoja->setCellValue('E65', strtoupper(num2letras($manifiesto->valor_flete, FALSE)));

$hoja->setCellValue('C66', $manifiesto->retencion_fuente);
$hoja->setCellValue('C67', $manifiesto->ica);
$valor_neto=$manifiesto->valor_flete-$manifiesto->retencion_fuente-$manifiesto->ica;
$hoja->setCellValue('C68', $valor_neto);
$hoja->setCellValue('C69', $manifiesto->anticipo);
$hoja->setCellValue('C70', $valor_neto-$manifiesto->anticipo);
$hoja->setCellValue('C71', date('Y/m/d', strtotime($manifiesto->fecha_pago_saldo)));
$hoja->setCellValue('A73', $manifiesto->ciudad_pago_saldo->nombre);

$hoja->setCellValue('C74', $manifiesto->cargue_pagado_por());
$hoja->setCellValue('F74', strtoupper($configuracion->nombre_empresa));
$hoja->setCellValue('L74', $manifiesto->conductor->nombre_completo);
$hoja->setCellValue('S74', $manifiesto->titular->nombre_completo);

$hoja->setCellValue('C75', $manifiesto->descargue_pagado_por());
$hoja->setCellValue('H75', $configuracion->nit_empresa);
$hoja->setCellValue('M75', $manifiesto->cedulaconductor);
$hoja->setCellValue('U75', $manifiesto->titular->numero_identificacion_completo);

$hoja->getProtection()->setPassword('TMA!');
$hoja->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
$hoja->getProtection()->setSort(true);
$hoja->getProtection()->setInsertRows(true);
$hoja->getProtection()->setFormatCells(true);

//Hoja de tiempos
$hoja = $objPHPExcel->setActiveSheetIndex(1);
$hoja->setTitle('Tiempos');
//$hoja->getHeaderFooter()->setOddHeader('&L&G&C&H RESOLUCIÓN NÚMERO '.$num_res.' DE '.$ano_res[0]);
$hoja->setCellValue('J3', ' '.$configuracion->codigo_empresa.$manifiesto->id);

$hoja->setCellValue('B7', strtoupper($configuracion->nombre_empresa));
$hoja->setCellValue('E7', $configuracion->empresa_sigla);
$hoja->setCellValue('H7', $configuracion->nit_empresa);
$hoja->setCellValue('I7', $manifiesto->tipo());

$hoja->setCellValue('B8', strtoupper($configuracion->direccion_empresa));
$hoja->setCellValue('E8', strtoupper($configuracion->ciudad_empresa));
$hoja->setCellValue('H8', $configuracion->empresa_telefono_sede_principal);

foreach (range(0, 2) as $i) {
  if (! isset($manifiesto->guias[$i])) break;
  $g = $manifiesto->guias[$i]->id;
  $f = date('Y/m/d', strtotime($manifiesto->guias[$i]->fecha_recibido_mercancia));
  if ($i == 0) {
    $hoja->setCellValue('A13', ' '.$g);
    $hoja->setCellValue('C13', 8);
    $hoja->setCellValue('D13', $f);
    $hoja->setCellValue('E13', '8:00am');
    $hoja->setCellValue('F13', $f);
    $hoja->setCellValue('G13', '6:00pm');
  } elseif ($i == 1) {
    $hoja->setCellValue('A21',' '.$g);
    $hoja->setCellValue('C21', 8);
    $hoja->setCellValue('D21', $f);
    $hoja->setCellValue('E21', '8:00am');
    $hoja->setCellValue('F21', $f);
    $hoja->setCellValue('G21', '6:00pm');
  } elseif ($i == 2) {
    $hoja->setCellValue('A29', ' '.$g);
    $hoja->setCellValue('C29', 8);
    $hoja->setCellValue('D29', $f);
    $hoja->setCellValue('E29', '8:00am');
    $hoja->setCellValue('F29', $f);
    $hoja->setCellValue('G29', '6:00pm');
  }
}
$hoja->getProtection()->setPassword('TMA!');
$hoja->getProtection()->setSheet(true);
$hoja->getProtection()->setSort(true);
$hoja->getProtection()->setInsertRows(true);
$hoja->getProtection()->setFormatCells(true);

/* HOJA DE ANEXO */
$hoja=$objPHPExcel->setActiveSheetIndex(2);

$flete_al_cobro = 0;
$valor_declarado = 0;
$unidades = 0;
$peso = 0;
$f = 3; //Inicia en la primera fila
foreach ($manifiesto->guias as $g) {
  if ($g->formapago == 'FLETE AL COBRO') {
    $valor = $g->total + $g->valorseguro;
    $flete_al_cobro += $valor;
  } else {
    $valor = 0;
    $unidades += $g->unidades;
    $peso += $g->peso;
  }
  $valor_declarado += $g->valordeclarado;
  $hoja->setCellValueByColumnAndRow(0, $f, $g->id);
  $hoja->setCellValueByColumnAndRow(1, $f, $g->unidades);
  $hoja->setCellValueByColumnAndRow(2, $f, $g->peso);
  $hoja->setCellValueByColumnAndRow(3, $f, substr($g->producto_nombre, 0, 25));
  $hoja->setCellValueByColumnAndRow(4, $f, $g->cliente_nombre_completo);
  $hoja->setCellValueByColumnAndRow(5, $f, $g->contacto_nombre_completo);
  $hoja->setCellValueByColumnAndRow(6, $f, $g->contacto_ciudad_nombre);
  $hoja->setCellValueByColumnAndRow(7, $f, $g->valordeclarado);
  $hoja->setCellValueByColumnAndRow(8, $f, round($valor));
  $f++;
}
$hoja->setCellValueByColumnAndRow(0, $f, 'TOTAL');
$hoja->setCellValueByColumnAndRow(1, $f, $unidades);
$hoja->setCellValueByColumnAndRow(2, $f, $peso);
$hoja->setCellValueByColumnAndRow(7, $f, round($valor_declarado));
$hoja->setCellValueByColumnAndRow(8, $f, round($flete_al_cobro));
$f += 2;

$hoja->setCellValueByColumnAndRow(6, $f, 'VALOR VIAJE');
$hoja->setCellValueByColumnAndRow(7, $f, $manifiesto->valor_flete);
$f++;
$hoja->setCellValueByColumnAndRow(6, $f, '(-) DESCUENTO');
$hoja->setCellValueByColumnAndRow(7, $f, $manifiesto->descuento);
$f++;
$hoja->setCellValueByColumnAndRow(6, $f, '(-) RETE FTE');
$hoja->setCellValueByColumnAndRow(7, $f, $manifiesto->retencion_fuente);
$f++;
$hoja->setCellValueByColumnAndRow(6, $f, '(-) ICA');
$hoja->setCellValueByColumnAndRow(7, $f, $manifiesto->ica);
$f++;
$hoja->setCellValueByColumnAndRow(6, $f, '(-) ANTICIPO');
$hoja->setCellValueByColumnAndRow(7, $f, $manifiesto->anticipo);
$f++;
$hoja->setCellValueByColumnAndRow(6, $f, 'SALDO');
$saldo = $manifiesto->valor_flete - $manifiesto->descuento - $manifiesto->retencion_fuente - $manifiesto->ica - $manifiesto->anticipo;
$hoja->setCellValueByColumnAndRow(7, $f, $saldo);

$hoja->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
$hoja->getPageSetup()->setPrintAreaByColumnAndRow(0, 1, 8, $f);

$objPHPExcel->setActiveSheetIndex(0);
$nombre='Manifiesto '.$manifiesto->id;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$nombre.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
