<?php
$raiz='../../';
require_once $raiz."seguridad.php";
require_once $raiz.'funciones.php';
require_once $raiz.'php/Excel.inc.php';

if (!isset($_GET['reporte'])) exit;

require_once $raiz."php/chart/class/pDraw.class.php";
require_once $raiz."php/chart/class/pImage.class.php";
require_once $raiz."php/chart/class/pData.class.php";
require_once $raiz."class/guias.class.php";

$objGuia=new Guias();
echo '<script type="text/javascript">
		$(function(){
			$("#descargar").button({icons: {primary: "ui-icon-disk"}});
		});
	</script>';
//variables generales
$forgotte13 = array("FontName"=>$raiz."php/chart/fonts/Forgotte.ttf", "FontSize"=>13);
$forgotte15 = array("FontName"=>$raiz."php/chart/fonts/Forgotte.ttf", "FontSize"=>15);
$forgotte9  = array("FontName"=>$raiz."php/chart/fonts/Forgotte.ttf", "FontSize"=>9);
$w=650;
$h=350;
$settings['background'] = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
$settings['overlay'] = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
$colores['negro']=array("R"=>0,"G"=>0,"B"=>0);
$colores['blanco']=array("R"=>255,"G"=>255,"B"=>255);
//-------
if ($_GET['reporte']=='global') {
	if (empty($_GET['fecha_inicio']) or empty($_GET['fecha_fin'])) {
		$result=$objGuia->ObtenerEstadisticas('global');
	}else{
		$result=$objGuia->ObtenerEstadisticas('global', $_GET['fecha_inicio'], $_GET['fecha_fin']);
	}
	if (mysql_num_rows($result)==0) {
		echo '<h3 style="text-align:center">No se encontr&oacute; informaci&oacute;n, prueba cambiar las fechas</h3>';
	}else{
		include($raiz."php/chart/class/pPie.class.php");
		
		$data = new pData();
		$picture = new pImage($w, $h, $data);
		$picture->setGraphArea(1, 1, $w-1, $h-1);
		$picture->setFontProperties($forgotte13);
		
		$csv='archivos/estadisticas-globales.csv';
		$png='archivos/estadisticas-globales.png';
		
		$f=fopen($csv, 'w+b'); //Escribir en un archivo CSV
		fwrite($f, utf8_decode('Estadísticas Globales').' | Creado: '.date('Y-m-d g:i:s a')."\r\n\r\n");
		$total=0;
		while ($row=mysql_fetch_assoc($result)) {
			$data->addPoints($row['pregunta'], "Labels");
			$data->addPoints($row['cantidad'], "Cantidades");
			$total+=$row['cantidad'];
			fwrite($f, utf8_decode($row['pregunta']).','.$row['cantidad']."\r\n");
		}
		fwrite($f, "Total, $total");
		fclose($f);
		$data->setAbscissa("Labels");
		
		/* Draw the background */
		$picture->drawFilledRectangle(1, 1, $w-1, $h-1, $settings['background']);
		
		/* Overlay with a gradient */
		$picture->drawGradientArea(0, 0, $w, $h, DIRECTION_VERTICAL, $settings['overlay']);
		
		//Top bar
		$picture->drawGradientArea(0, 0, $w, 20, DIRECTION_VERTICAL, array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));
	
		/* Add a border to the picture */
		$picture->drawRectangle(0, 0, $w-1, $h-1, $colores['negro']);
		//Titulo
		$picture->drawText(10, 17, "Estadísticas Globales", $colores['blanco']);
		//Total
		$picture->setFontProperties($forgotte15);
		$picture->drawText(500, 15, "Total: $total", $colores['blanco']);
		
		/* Enable shadow computing */
		$picture->setShadow(TRUE, array("X"=>2,"Y"=>2,"R"=>150,"G"=>150,"B"=>150,"Alpha"=>100));
		
		/* Create the pPie object */
		$PieChart = new pPie($picture, $data);
		
		/* Draw two AA pie chart */
		$PieChart->draw2DPie(130, 160, array('Border'=>TRUE, 'Radius'=>120, 'DrawLabels'=>false, 'RecordImageMap'=>TRUE, 'LabelStacked'=>false, 'WriteValues'=>PIE_VALUE_PERCENTAGE, 'ValuePosition'=>PIE_VALUE_INSIDE));
		
		
		/* Write down the VERTICAL legend*/ 
		$PieChart->drawPieLegend(270, 40);
		
		/* Render the picture */
		$picture->render($png);
		echo '<img src="configuracion/estadisticas/'.$png.'?v='.RandomString(5).'" alt="Estadisticas Globales" />';
		echo '<hr /><form action="configuracion/estadisticas/'.$csv.'"><button id="descargar">Descargar</button></form>';
	}
	exit;
}
if ($_GET['reporte']=='dia') {
	$datos=$objGuia->ObtenerEstadisticas('dia', $_GET['fecha_inicio'], $_GET['fecha_fin']);
	if (empty($datos)) {
		echo '<h3 style="text-align:center">No se encontr&oacute; informaci&oacute;n, prueba cambiar las fechas</h3>';
	}else{
		$nombre_archivo='estadisticas-dia.xls';
		$dias=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31);
		foreach($dias as $dia){
			$entregas[$dia]=0;
			$devoluciones[$dia]=0;
		}
		
		$total_ent=0;
		$total_dev=0;
		
		$xls=xlsBOF();
		$xls.=xlsWriteLabel(0,0,utf8_decode('Estadísticas por día').' ('.$_GET['fecha_inicio'].' hasta '.$_GET['fecha_fin'].')');
		$xls.=xlsWriteLabel(1,0,"Creado: ".date('Y-m-d g:i:s a'));
		$xls.=xlsWriteLabel(3,0,utf8_decode('Día'));
		$xls.=xlsWriteLabel(3,1,'Entregas');
		$xls.=xlsWriteLabel(3,3,utf8_decode('Día'));
		$xls.=xlsWriteLabel(3,4,'Devoluciones');
		$i=4;
		if(isset($datos['entregas'])){
			foreach($datos['entregas'] as $dia=>$cantidad){
				$entregas[$dia]=$cantidad;
				$xls.=xlsWriteLabel($i,0,$dia);
				$xls.=xlsWriteNumber($i,1,$cantidad);
				$total_ent+=$cantidad;
				$i++;
			}
		}
		$j=4;
		if(isset($datos['devoluciones'])){
			foreach($datos['devoluciones'] as $dia=>$cantidad){
				$devoluciones[$dia]=$cantidad;
				$xls.=xlsWriteLabel($j,3,$dia);
				$xls.=xlsWriteNumber($j,4,$cantidad);
				$total_dev+=$cantidad;
				$j++;
			}
		}
		if($i<$j) $i=$j;
		$xls.=xlsWriteLabel($i,0,'Total');
		$xls.=xlsWriteNumber($i,1,$total_ent);
		$xls.=xlsWriteLabel($i,3,'Total');
		$xls.=xlsWriteNumber($i,4,$total_dev);
		$xls.=xlsEOF();
		
		//Escribir en un archivo
		$f=fopen("archivos/$nombre_archivo", 'w+b');
		fwrite($f, $xls);
		fclose($f);
		//----
		
		$data = new pData();
		$data->addPoints($entregas, "Entregas");
		$data->addPoints($devoluciones, "Devoluciones");
		$data->addPoints($dias,"Labels");
		$data->setSerieDescription("Labels","Dias");
		$data->setAbscissa("Labels");
		
		$picture = new pImage($w, $h, $data);
		$picture->setGraphArea(30, 30, $w-30, $h-30);
		$picture->setFontProperties($forgotte13);
		
		/* Draw the background */
		$picture->drawFilledRectangle(1, 1, $w-1, $h-1, $settings['background']);
		
		/* Overlay with a gradient */
		$picture->drawGradientArea(0, 0, $w, $h, DIRECTION_VERTICAL, $settings['overlay']);
		/* Top bar */
		$picture->drawGradientArea(0, 0, $w, 20, DIRECTION_VERTICAL, array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));
	
		/* Add a border to the picture */
		$picture->drawRectangle(0, 0, $w-1, $h-1, $colores['negro']);
		//Titulo
		$picture->drawText(10, 17, "Estadísticas por día", $colores['blanco']);
		//Total
		$picture->setFontProperties($forgotte15);
		$picture->drawText(($w/2)-80, 17, "Entregas: $total_ent | Devoluciones: $total_dev", $colores['blanco']);
		
		$picture->setFontProperties($forgotte9);
		/* Enable shadow computing */
		$picture->setShadow(TRUE,array("X"=>-1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		$scaleSettings = array('MinDivHeight'=>30,"GridR"=>0,"GridG"=>0,"GridB"=>0,'GridAlpha'=>10,"DrawSubTicks"=>TRUE, 'LabelingMethod'=>LABELING_DIFFERENT, 'Mode'=>SCALE_MODE_START0);
		$picture->drawScale($scaleSettings);
		
		$picture->drawBarChart(array("DisplayValues"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE, "DisplayShadow"=>TRUE, 'Gradient'=>TRUE, "DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE));
		
		$Settings = array('Mode'=>LEGEND_HORIZONTAL, 'Style'=>LEGEND_BOX,'Family'=>LEGEND_FAMILY_BOX);
		$picture->drawLegend(($w/2)-80, 40, $Settings);
		$picture->render("archivos/estadisticas-dia.png");
		echo '<img src="configuracion/estadisticas/archivos/estadisticas-dia.png?v='.RandomString(5).'" alt="Reporte por dia" />';
		echo '<hr /><form action="configuracion/estadisticas/archivos/'.$nombre_archivo.'"><button id="descargar">Descargar</button></form>';
	}
	exit;
}
if ($_GET['reporte']=='mes') {
	$datos=$objGuia->ObtenerEstadisticas('mes', $_GET['fecha_inicio'], $_GET['fecha_fin']);
	if (empty($datos)) {
		echo '<h3 style="text-align:center">';
		echo 'No se encontr&oacute; informaci&oacute;n, prueba cambiar las fechas';
		echo '</h3>';
	}else{
		$nombre_archivo='estadisticas-mes.xls';
		
		//Obtener meses entre las 2 fechas
		//para colocarlos en el grafico.
		$meses=ObtenerFechas('mes', $_REQUEST['fecha_inicio'], $_REQUEST['fecha_fin']);
		
		//rellenar las entregas y las devoluciones con 0
		foreach($meses as $mes){
			$entregas[$mes]=0;
			$devoluciones[$mes]=0;
		}
		
		$total_ent=0;
		$total_dev=0;
		$xls=xlsBOF();
		$xls.=xlsWriteLabel(0,0,utf8_decode('Estadísticas por mes').' ('.$_GET['fecha_inicio'].' hasta '.$_GET['fecha_fin'].')');
		$xls.=xlsWriteLabel(1,0,"Creado: ".date('Y-m-d g:i:s a'));
		$xls.=xlsWriteLabel(3,0,'Mes');
		$xls.=xlsWriteLabel(3,1,'Entregas');
		$xls.=xlsWriteLabel(3,3,'Mes');
		$xls.=xlsWriteLabel(3,4,'Devoluciones');
		$i=4;
		if(isset($datos['entregas'])){
			foreach($datos['entregas'] as $mes=>$cantidad){
				$entregas[$mes]=$cantidad;
				$xls.=xlsWriteLabel($i,0,$mes);
				$xls.=xlsWriteNumber($i,1,$cantidad);
				$total_ent+=$cantidad;
				$i++;
			}
		}
		$j=4;
		if(isset($datos['devoluciones'])){
			foreach($datos['devoluciones'] as $mes=>$cantidad){
				$devoluciones[$mes]=$cantidad;
				$xls.=xlsWriteLabel($j,3,$mes);
				$xls.=xlsWriteNumber($j,4,$cantidad);
				$total_dev+=$cantidad;
				$j++;
			}
		}
		if($i<$j) $i=$j;
		$xls.=xlsWriteLabel($i,0,'Total');
		$xls.=xlsWriteNumber($i,1,$total_ent);
		$xls.=xlsWriteLabel($i,3,'Total');
		$xls.=xlsWriteNumber($i,4,$total_dev);
		$xls.=xlsEOF();
		
		//Escribir en un archivo
		$f=fopen("archivos/$nombre_archivo", 'w+b');
		fwrite($f, $xls);
		fclose($f);
		//----
		
		$data = new pData();
		$data->addPoints($entregas, "Entregas");
		$data->addPoints($devoluciones, "Devoluciones");
		$data->addPoints($meses,"Labels");
		$data->setSerieDescription("Labels","Meses");
		$data->setAbscissa("Labels");
		
		$picture = new pImage($w, $h, $data);
		$picture->setGraphArea(30, 30, $w-30, $h-30);
		$picture->setFontProperties(array("FontName"=>$raiz."php/chart/fonts/Forgotte.ttf", "FontSize"=>13));
		
		
		/* Draw the background */
		$Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
		$picture->drawFilledRectangle(1, 1, $w-1, $h-1, $Settings);
		
		/* Overlay with a gradient */
		$Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
		$picture->drawGradientArea(0, 0, $w, $h, DIRECTION_VERTICAL, $Settings);
		$picture->drawGradientArea(0, 0, $w, 20, DIRECTION_VERTICAL, array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));
	
		/* Add a border to the picture */
		$picture->drawRectangle(0, 0, $w-1, $h-1, array("R"=>0,"G"=>0,"B"=>0));
		//Titulo
		$picture->drawText(10, 17, "Estadísticas por mes", array("R"=>255,"G"=>255,"B"=>255));
		//Total
		$picture->setFontProperties(array("FontName"=>$raiz."php/chart/fonts/Forgotte.ttf", "FontSize"=>15));
		$picture->drawText(300, 17, "Entregas: $total_ent | Devoluciones: $total_dev", array("R"=>255,"G"=>255,"B"=>255));
		
		$picture->setFontProperties(array("FontName"=>$raiz."php/chart/fonts/Forgotte.ttf", "FontSize"=>9));
		/* Enable shadow computing */
		$picture->setShadow(TRUE,array("X"=>-1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		$scaleSettings = array('MinDivHeight'=>30,"GridR"=>0,"GridG"=>0,"GridB"=>0,'GridAlpha'=>10,"DrawSubTicks"=>TRUE, 'LabelingMethod'=>LABELING_DIFFERENT, 'Mode'=>SCALE_MODE_START0);
		$picture->drawScale($scaleSettings);
		
		$picture->drawBarChart(array("DisplayValues"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE, "DisplayShadow"=>TRUE, 'Gradient'=>TRUE, "DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE));
		
		$Settings = array('Mode'=>LEGEND_HORIZONTAL, 'Style'=>LEGEND_BOX,'Family'=>LEGEND_FAMILY_BOX);
		$picture->drawLegend(($w/2)-80, 32, $Settings);
		$picture->render("archivos/estadisticas-mes.png");
		echo '<img src="configuracion/estadisticas/archivos/estadisticas-mes.png?v='.RandomString(5).'" alt="Estadisticas por mes" />';
		echo '<hr /><form action="configuracion/estadisticas/archivos/'.$nombre_archivo.'"><button id="descargar">Descargar</button></form>';
	}
	exit;
}
if ($_GET['reporte']=='ano') {
	$datos=$objGuia->ObtenerEstadisticas('ano', $_GET['fecha_inicio'], $_GET['fecha_fin']);
	if (empty($datos)) {
		echo '<h3 style="text-align:center">No se encontr&oacute; informaci&oacute;n, prueba cambiar las fechas</h3>';
	}else{
		$nombre_archivo='estadisticas-ano.xls';
		
		//Obtener meses entre las 2 fechas
		//para colocarlos en el grafico.
		$anos=ObtenerFechas('ano', $_REQUEST['fecha_inicio'], $_REQUEST['fecha_fin']);
		//rellenar las entregas y las devoluciones con 0
		foreach($anos as $ano){
			$entregas[$ano]=0;
			$devoluciones[$ano]=0;
		}
		
		$total_ent=0;
		$total_dev=0;
		$xls=xlsBOF();
		$xls.=xlsWriteLabel(0,0,utf8_decode('Estadísticas por año').' ('.$_GET['fecha_inicio'].' hasta '.$_GET['fecha_fin'].')');
		$xls.=xlsWriteLabel(1,0,"Creado: ".date('Y-m-d g:i:s a'));
		$xls.=xlsWriteLabel(3,0,utf8_decode('Año'));
		$xls.=xlsWriteLabel(3,1,'Entregas');
		$xls.=xlsWriteLabel(3,3,utf8_decode('Año'));
		$xls.=xlsWriteLabel(3,4,'Devoluciones');
		$i=4;
		if(isset($datos['entregas'])){
			foreach($datos['entregas'] as $ano=>$cantidad){
				$entregas[$ano]=$cantidad;
				$xls.=xlsWriteNumber($i,0,$ano);
				$xls.=xlsWriteNumber($i,1,$cantidad);
				$total_ent+=$cantidad;
				$i++;
			}
		}
		$j=4;
		if(isset($datos['devoluciones'])){
			foreach($datos['devoluciones'] as $ano=>$cantidad){
				$devoluciones[$ano]=$cantidad;
				$xls.=xlsWriteNumber($j,3,$ano);
				$xls.=xlsWriteNumber($j,4,$cantidad);
				$total_dev+=$cantidad;
				$j++;
			}
		}
		if($i<$j) $i=$j;
		$xls.=xlsWriteLabel($i,0,'Total');
		$xls.=xlsWriteNumber($i,1,$total_ent);
		$xls.=xlsWriteLabel($i,3,'Total');
		$xls.=xlsWriteNumber($i,4,$total_dev);
		$xls.=xlsEOF();
		
		//Escribir en un archivo
		$f=fopen("archivos/$nombre_archivo", 'w+b');
		fwrite($f, $xls);
		fclose($f);
		//----
		
		$data = new pData();
		$data->addPoints($entregas, "Entregas");
		$data->addPoints($devoluciones, "Devoluciones");
		$data->addPoints($anos,"Labels");
		$data->setSerieDescription("Labels","Años");
		$data->setAbscissa("Labels");
		
		$picture = new pImage($w, $h, $data);
		$picture->setGraphArea(30, 30, $w-30, $h-30);
		$picture->setFontProperties($forgotte13);
		/* Draw the background */
		$Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
		$picture->drawFilledRectangle(1, 1, $w-1, $h-1, $Settings);
		
		/* Overlay with a gradient */
		$Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
		$picture->drawGradientArea(0, 0, $w, $h, DIRECTION_VERTICAL, $Settings);
		$picture->drawGradientArea(0, 0, $w, 20, DIRECTION_VERTICAL, array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));
	
		/* Add a border to the picture */
		$picture->drawRectangle(0, 0, $w-1, $h-1, array("R"=>0,"G"=>0,"B"=>0));
		//Titulo
		$picture->drawText(10, 17, "Estadísticas por Año", array("R"=>255,"G"=>255,"B"=>255));
		//Total
		$picture->setFontProperties(array("FontName"=>$raiz."php/chart/fonts/Forgotte.ttf", "FontSize"=>15));
		$picture->drawText(300, 17, "Entregas: $total_ent | Devoluciones: $total_dev", array("R"=>255,"G"=>255,"B"=>255));
		
		$picture->setFontProperties(array("FontName"=>$raiz."php/chart/fonts/Forgotte.ttf", "FontSize"=>9));
		/* Enable shadow computing */
		$picture->setShadow(TRUE,array("X"=>-1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		$scaleSettings = array('MinDivHeight'=>30,"GridR"=>0,"GridG"=>0,"GridB"=>0,'GridAlpha'=>10,"DrawSubTicks"=>TRUE, 'LabelingMethod'=>LABELING_DIFFERENT, 'Mode'=>SCALE_MODE_START0);
		$picture->drawScale($scaleSettings);
		
		$picture->drawBarChart(array("DisplayValues"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE, "DisplayShadow"=>TRUE, 'Gradient'=>TRUE, "DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE));
		
		$Settings = array('Mode'=>LEGEND_HORIZONTAL, 'Style'=>LEGEND_BOX,'Family'=>LEGEND_FAMILY_BOX);
		$picture->drawLegend(($w/2)-80, 40, $Settings);
		$picture->render("archivos/estadisticas-ano.png");
		echo '<img src="configuracion/estadisticas/archivos/estadisticas-ano.png?v='.RandomString(5).'" alt="Estadisticas por a&ntilde;o" />';
		echo '<hr /><form action="configuracion/estadisticas/archivos/'.$nombre_archivo.'"><button id="descargar">Descargar</button></form>';
	}
	exit;
}