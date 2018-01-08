<?php
require "../../seguridad.php";
if(! isset($_SESSION['permisos'][PLANILLAS_IMPRIMIR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

if(! isset($_GET['idplanilla']) and ! isset($_POST['idplanilla'])) {
	include Logistica::$root."mensajes/id.php";
	exit;
}

require_once Logistica::$root."php/tcpdf/PDF.php";
require_once Logistica::$root."class/planillasC.class.php";
require_once Logistica::$root."class/guias.class.php";

$idplanilla = isset($_GET['idplanilla']) ? $_GET['idplanilla'] : $_POST['idplanilla'];

$objGuia = new Guias;
$barcode = 'C128B';
$planilla = new PlanillasC;
$result = $planilla->ObtenerPlanilla($idplanilla);
$row = mysql_fetch_array($result);

$configuracion = new Configuracion;

$pdf = new PDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator('Logística');
$pdf->SetAuthor('Edgar Ortega Ramírez <EdgarOrtegaRamirez@outlook.com>');
$pdf->SetTitle("Manifiesto de carga $idplanilla");
$pdf->SetSubject("Manifiesto de carga - Transportes Mario Acosta");
$pdf->SetKeywords('Transportes Mario Acosta, Manifiesto de carga, Planilla de carga, Anexo de manifiesto de carga');

$pdf->setPrintFooter(false);
$pdf->setPrintHeader(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->SetFillColor(255,255,255);

$pdf->AddPage();
$pdf->Manifiesto($configuracion);
$pdf->SetFont("Helvetica","B",14);
$pdf->Text(231, 12, $configuracion->codigo_regional);//Codigo Regional
$pdf->Text(243, 12, $configuracion->codigo_empresa);//Codigo Empresa
$pdf->Text(262, 12, $row['id']);//Codigo Numero Consecutivo

$pdf->SetFont("","",7);
$pdf->Text(234, 24, $configuracion->numero_resolucion);//Resolucion
$pdf->Text(247, 24, $configuracion->fecha_resolucion);//Fecha Resolucion
//Rangos
$inicio_rango=$configuracion->codigo_regional."-".$configuracion->codigo_empresa."-".$configuracion->inicio_rango;
$fin_rango="AL ".$configuracion->codigo_regional."-".$configuracion->codigo_empresa."-".$configuracion->fin_rango;
$pdf->SetFontSize(6.7);
$pdf->Text(246.5, 27, $inicio_rango,false,false,true,0,0,"L",true);
$pdf->Text(267.5, 27, $fin_rango,false,false,true,0,0,"L",true);

$pdf->SetFontSize(11);
$fecha=explode("-", $row['fecha']);
$fecha_exp=mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]);
$pdf->Text(40, 35, date("d/m/Y",$fecha_exp)); //Fecha expedición
$pdf->MultiCell(85, 5, $row['ciudadorigen'], 0, 'C', false, 0, 115, 35);//Origen del viaje
$pdf->MultiCell(76, 5, $row['ciudaddestino'], 0, 'C', false, 0, 215, 35);//Destino del viaje

$pdf->SetFontSize(9);
$pdf->Text(17, 48, $row['placacamion']);//Placa
$pdf->MultiCell(36, 5, $row['marcacamion'], 0, 'C', false, 0, 43,48);//Marca
$pdf->MultiCell(33, 5, $row['lineacamion'], 0, 'C', false, 0, 79,48);//Linea
$pdf->Text(125, 48, $row['modelocamion']);//Modelo
$pdf->Text(155, 48, $row['modelo_repotenciado']);//Modelo Repotenciado
$pdf->MultiCell(40, 5, $row['seriecamion'], 0, 'C', false, 0, 175, 48);//Serie
$pdf->SetFontSize(6);
$pdf->MultiCell(27, 4, $row['color'],0,'C',false, 0, 215.5, 48);//Color
$pdf->SetFontSize(8);
$pdf->MultiCell(48, 5, $row['carroceria'], 0, 'C',false, 0, 242, 48);//Carroceria

$pdf->Text(17, 56, $row['registrocarga']);//Registro carga
$pdf->Text(75, 56, $row['configuracion']);//Configuracion
$pdf->Text(107, 56, $row['pesocamion']." Kg");//Peso vacio
$pdf->MultiCell(32, 5, $row['soat'], 0, 'C', false, 0, 128, 56);//Numero poliza SOAT
$pdf->SetFontSize(6);
$pdf->MultiCell(54, 4, $row['aseguradora'],0,'C',false,1, 161,55.5);//Compañia de seguros
$pdf->SetFontSize(9);
$fecha=explode("-", $row['fechasoat']);
$fecha_soat=mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]);
$pdf->Text(225, 56, date("d/m/Y",$fecha_soat));//Fecha SOAT
$pdf->Text(265, 56, $row['placa_semiremolque']);//Placa semiremolque SOAT

//Propietario
if ($row['tipo_identificacion_propietario']=='N') $propietario=$row['razon_social_propietario'];
else $propietario=$row['nombre_propietario']." ".$row['primer_apellido_propietario']." ".$row['segundo_apellido_propietario'];
$pdf->MultiCell(106, 5, $propietario, 0, 'C', false, 0, 6, 63.5);//Nombre
if ($row['tipo_identificacion_propietario']=='N') $numero_identificacion=$row['numero_identificacion_propietario']."-".$row['dv_propietario'];
else $numero_identificacion=$row['numero_identificacion_propietario'];
$pdf->MultiCell(48, 5, $numero_identificacion, 0, 'C', false, 0, 112.5, 63.5);//Cedula
$pdf->MultiCell(68, 5, $row['direccion_propietario'], 0, 'C', false, 0, 161, 63.5);//Direccion
$pdf->MultiCell(28, 5, $row['telefono_propietario'], 0, 'C', false, 0, 229, 63.5);//Telefono
$pdf->SetFontSize(8);
$pdf->MultiCell(34, 5, $row['ciudad_propietario'], 0, 'C', false, 0, 257, 63.5);//Ciudad
$pdf->SetFontSize(9);

//Tenedor
if ($row['tipo_identificacion_tenedor']=='N') $tenedor=$row['razon_social_tenedor'];
else $tenedor=$row['nombre_tenedor']." ".$row['primer_apellido_tenedor']." ".$row['segundo_apellido_tenedor'];
$pdf->MultiCell(106, 5, $tenedor, 0, 'C', false, 0, 6, 71.5);//Nombre
if ($row['tipo_identificacion_tenedor']=='N') $numero_identificacion=$row['numero_identificacion_tenedor']."-".$row['dv_tenedor'];
else $numero_identificacion=$row['numero_identificacion_tenedor'];
$pdf->MultiCell(48, 5, $numero_identificacion, 0, 'C', false, 0, 112.5, 71.5);//Cedula
$pdf->MultiCell(68, 5, $row['direccion_tenedor'], 0, 'C', false, 0, 161, 71.5);//Direccion
$pdf->MultiCell(28, 5, $row['telefono_tenedor'], 0, 'C', false, 0, 229, 71.5);//Telefono
$pdf->SetFontSize(8);
$pdf->MultiCell(34, 5, $row['ciudad_tenedor'], 0, 'C', false, 0, 257, 71.5);//Ciudad
$pdf->SetFontSize(9);

//Conductor
$conductor=$row['nombreconductor']." ".$row['apellido1conductor']." ".$row['apellido2conductor'];
$pdf->MultiCell(106, 5, $conductor, 0, 'C', false, 0, 6, 79);//Nombre
$pdf->MultiCell(48, 5, $row['numero_identificacion_conductor'], 0, 'C', false, 0, 112.5, 79);//Cedula
$pdf->MultiCell(68, 5, $row['direccionconductor'], 0, 'C', false, 0, 161, 79);//Direccion
$cat=explode("-", $row['categorialicencia']);
$pdf->MultiCell(28, 5, $cat[0], 0, 'C', false, 0, 229, 79);//Telefono
$pdf->SetFontSize(8);
$pdf->MultiCell(34, 5, $row['ciudadconductor'], 0, 'C', false, 1, 257, 79);//Ciudad

$pdf->SetFontSize(9);
$pdf->MultiCell(30, 5, number_format($row['valor_flete']),0,'R',false, 0, 49, 145.5);//Valor total viaje
$pdf->MultiCell(30, 5, number_format($row['retencion_fuente']),0,'R',false, 0,49,150.5);//Retencion en la fuente
$pdf->MultiCell(30, 5, number_format($row['ica']),0,'R',false, 0, 49,155);//Descuentos
$pdf->MultiCell(30, 5, number_format($row['valor_flete']-$row['retencion_fuente']-$row['ica']), 0,'R', false, 0, 49, 159.7);//Valor Neto
$pdf->MultiCell(30, 5, number_format($row['anticipo']),0,'R',false, 0, 49, 164.3);//Anticipo
$pdf->MultiCell(30, 5, number_format($row['valor_flete']-$row['ica']-$row['retencion_fuente']-$row['anticipo']),0,'R',false, 0, 49, 169);//Anticipo

$pdf->MultiCell(33, 5, $row['ciudad_pago_saldo'], 0, 'C', false, 0, 79, 153);//Lugar pago saldo
$fecha=explode("-", $row['fecha_pago_saldo']);
$fecha_s=date("d/m/Y",mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]));
$pdf->Text(118, 155, $fecha_s);//Fecha pago saldo

$opciones=PlanillasC::ObtenerOpcionesCargueDescargue();
$pdf->MultiCell(64, 5, $opciones[$row['cargue_pagado_por']], 0, 'C', false, 0, 79, 163);//Cargue pagado por
$pdf->MultiCell(64, 5, $opciones[$row['descargue_pagado_por']], 0, 'C', false, 0, 79, 173);//Descargue pagado por

$pdf->SetFontSize(6);
$pdf->MultiCell(44, 5, $configuracion->aseguradora_mercancia,0,'C',false,1,144,147.5);//Compañia de seguros
$pdf->SetFontSize(11);
$pdf->MultiCell(44, 5, $configuracion->numero_poliza_mercancia, 0,'C',false,1,144,158);//Numero Poliza
$fecha=explode("-", $configuracion->vigencia_poliza_mercancia);//Vencimiento poliza
$fecha_s=date("d/m/Y",mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]));
$pdf->Text(155, 168, $fecha_s);//Vigencia Poliza
$pdf->MultiCell(97, 10, $row['observaciones'], 0,'L',false,1,191,147,true,0,false,false,38,'T',true);//Observaciones

$fill = false;
$pdf->SetTextColor(0);//Black
$pdf->SetFontSize(7);
$h=3;

$resultado=$planilla->ObtenerGuias($idplanilla,0,10); //Obtener 10 guías para el manifiesto
if(mysql_num_rows($resultado)>0){
	while ($guia = mysql_fetch_array($resultado)) {
		if ($guia['unidadmedida']==1){
			$unidad = "UNIDADES";
		}else{
			$unidad = $guia['unidadmedida'];
		}
		if ($guia['formapago']=='FLETE AL COBRO') {
			$total = $guia['total'] + $guia['valorseguro'];
		}else{
			$total=0;
		}
		$remitente = trim($guia['nombrecliente'].' '.$guia['cliente_primer_apellido'].' '.$guia['cliente_segundo_apellido']);
		$contacto = trim($guia['nombrecontacto'].' '.$guia['primer_apellido_contacto'].' '.$guia['segundo_apellido_contacto']);
		$data[] = array($guia['id'], $unidad, $guia['cantidad'], $guia['peso'], $guia['naturaleza'], $guia['empaque'], $guia['idproducto'], $guia['producto'], $remitente, $contacto, $guia['ciudaddestino'], $guia['valordeclarado'], $total);
	}
	mysql_free_result($resultado);
	$pdf->SetLeftMargin(6.2);
	$pdf->Ln(-62);
	$pdf->ImprimirPlanilla($data);
}

//-----------------------
//Datos para paginar
$result=$planilla->ObtenerGuias($idplanilla); //obtener todas las guias que tiene la planilla
$rango=32;
$inicio=0;
$fin=$rango;
$num_resultados=mysql_num_rows($result);
$n=intval($num_resultados/$rango);
if ($n!=($num_resultados/$rango)) {
	$n+=1;//Cantidad de paginas
}
//Fin Datos para paginar

$valor_declarado=0; //TOTAL VALOR DECLARADO
$total_fc=0; //TOTAL FLETE AL COBRO
for ($i = 1; $i <= $n; $i++) {
	// agregar un anexo
	$pdf->AddPage();
	$pdf->Anexo($configuracion);

	//echo $i.' de '.$n.'<br>';

	$pdf->SetFont("Helvetica", "B", 10);
	$pdf->Text(254, 9, $i);//Numero anexo
	$pdf->Text(270, 9, $n);//Numero páginas

	$pdf->SetFont("Helvetica", "B", 14);
	$pdf->Text(224, 14, $configuracion->codigo_regional);//Codigo Regional
	$pdf->Text(237, 14, $configuracion->codigo_empresa);//Codigo Empresa
	$pdf->MultiCell(40, 7, $row['id'], 0, 'C', false, 0, 251, 14);//Codigo Numero Consecutivo
	//$pdf->Text(255, 14, $row['id']);

	$pdf->SetFont("Helvetica", "", 7);
	$pdf->Text(242, 26.4, $configuracion->numero_resolucion);//Resolucion
	$pdf->Text(255, 26.4, $configuracion->fecha_resolucion);//Fecha Resolucion
	//Rangos
	$inicio_rango=$configuracion->codigo_regional."-".$configuracion->codigo_empresa."-".$configuracion->inicio_rango;
	$fin_rango="AL ".$configuracion->codigo_regional."-".$configuracion->codigo_empresa."-".$configuracion->fin_rango;
	$pdf->SetFontSize(6.7);
	$pdf->Text(248, 29, $inicio_rango, false, false, true, 0, 0, "L", true);
	$pdf->Text(267.7, 29, $fin_rango, false, false, true, 0, 0, "L", true);

	$pdf->SetFontSize(11);
	$pdf->MultiCell(67, 6, date('d/m/Y', strtotime($row['fecha'])), 0, 'C', false, 0, 6, 35.2);//Fecha expedición
	$pdf->MultiCell(90, 5.5, $row['ciudadorigen'], 0, 'C', false, 0, 84, 35.2);//Origen del viaje
	$pdf->MultiCell(103, 5.5, $row['ciudaddestino'], 0, 'C', false, 0, 188, 35.2);//Destino del viaje

	//Obtener todas las guías para el manifiesto en rangos de 32
	$resultado2=$planilla->ObtenerGuias($idplanilla,$inicio,$rango);
	if(mysql_num_rows($resultado2)>0){
		$data=array();//Limpiar...
		while ($guia=mysql_fetch_array($resultado2)) {
			if ($guia['unidadmedida']==1){
				$unidad="UNIDADES";
			}else{
				$unidad=$guia['unidadmedida'];
			}
			if ($guia['formapago']=='FLETE AL COBRO') {
				$flete=$guia['total']+$guia['valorseguro'];
				$total_fc+=$flete;
			}else{
				$flete=0;
			}
			$valor_declarado += $guia['valordeclarado'];
			$remitente = trim($guia['nombrecliente'].' '.$guia['cliente_primer_apellido'].' '.$guia['cliente_segundo_apellido']);
			$contacto = trim($guia['nombrecontacto'].' '.$guia['primer_apellido_contacto'].' '.$guia['segundo_apellido_contacto']);
			$data[]=array($guia['id'], $unidad, $guia['cantidad'], $guia['peso'], $guia['naturaleza'], $guia['empaque'], $guia['idproducto'], $guia['producto'], $remitente, $contacto, $guia['ciudaddestino'], $guia['valordeclarado'], $flete);
		}
		mysql_free_result($resultado2);

		$pdf->Ln(16);
		$pdf->SetLeftMargin(9);
		$pdf->ImprimirPlanillaAnexo($data);

		//
		$inicio=$fin;
		$fin=$fin+$rango;
	}
}
$pdf->SetAutoPageBreak(true, 1);
if ($num_resultados>0) {
	$pdf->SetFontSize(8);
	$b=0;
	$pdf->MultiCell(40, 4, 'TOTAL F.A.C = '.number_format($total_fc), $b, 'L', false, 0, 6, 184.5);
	$pdf->MultiCell(45, 4, 'TOTAL Vr Aseg = '.number_format($valor_declarado), $b, 'L', false, 0, 45, 184.5);
	$pdf->MultiCell(30, 4, 'Vr Flete = '.number_format($row['valor_flete']), $b, 'L', false, 0, 90, 184.5);
	$pdf->MultiCell(25, 4, 'Dcto = '.number_format($row['descuento']), $b, 'L', false, 0, 120, 184.5);
	$pdf->MultiCell(25, 4, 'Rete Fte = '.number_format($row['retencion_fuente']), $b, 'L', false, 0, 145, 184.5);
	$pdf->MultiCell(25, 4, 'ICA = '.number_format($row['ica']), $b, 'L', false, 0, 170, 184.5);
	$pdf->MultiCell(30, 4, 'Antic = '.number_format($row['anticipo']), $b, 'L', false, 0, 195, 184.5);
	$pdf->MultiCell(35, 4, 'Total = '.number_format($row['valor_flete']-$row['descuento']-$row['retencion_fuente']-$row['ica']-$row['anticipo']), $b, 'L', false, 0, 225, 184.5);
}
$pdf->Output("Manifiesto_$idplanilla.pdf", 'I');
