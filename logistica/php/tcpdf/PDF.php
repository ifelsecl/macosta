<?php
/**
 * ESTE ARCHIVO INCLUYE LAS SIGUIENTES CLASES:
 *
 * Clase PDF    -> genera las guias, ordenes de recogida, planillas
 * Clase FacturaPDF -> genera facturas
 * Clase CartaPDF -> genera cartas
 */

require_once('config/lang/spa.php');
require_once('tcpdf.php');

/**
 * PDF tiene funciones usadas para la generación de los archivos de la aplicación.
 * Extiende de la clase TCPDF.
 *
 * @author  Edgar Ortega Ramírez
 * @author  Junio 3, 2011
 * @version 2.0
 * @see   TCPDF
 *
 */
class PDF extends TCPDF {

  /**
   * Crea una tabla para una Relación.
   *
   * @param array $header los campos del header de la tabla.
   * @param array $data los datos que seran agregados a la tabla.
   * @param boolean $relacion indica si la factura es una relación o no.
   */
  public function Relacion($data) {
    // Colors, line width and bold font
    $this->SetFillColor(100,100,100);
    $this->SetTextColor(255);
    $this->SetDrawColor(0, 0, 0);

    $this->SetFont('', 'B',8);
    $header = array('Destinatario', 'Unid', 'Guía', 'Documento', 'Destino', 'Factura', 'Seguro', 'Flete', 'Total');
    $w = array(45, 10, 20, 20, 30, 20, 15, 15, 15); //total debe ser igual a 190
    $num_headers = count($header);
    for($i = 0; $i < $num_headers; ++$i) {
      $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }
    $this->Ln();

    // Color and font restoration
    $this->SetFillColor(224, 235, 255);
    $this->SetTextColor(0);
    $this->SetFont('','',7.5);

    // Data
    $fill = false;
    $h=5;
    foreach($data as $row) {
      if ($fill) {
        $this->SetFillColor(224, 235, 255);
      } else {
        $this->SetFillColor(255);
      }
      $this->Cell($w[0], $h, substr($row[0], 0, 26), 'LR', 0, 'L', $fill);
      $this->Cell($w[1], $h, $row[1], 'LR', 0, 'R', $fill);
      $this->Cell($w[2], $h, $row[2], 'LR', 0, 'R', $fill);
      $this->Cell($w[3], $h, substr($row[3], 0, 12), 'LR', 0, 'R', $fill);//doc cliente
      $this->Cell($w[4], $h, substr($row[4], 0, 20), 'LR', 0, 'L', $fill);//destino
      $this->Cell($w[5], $h, $row[5], 'LR', 0, 'L', $fill);//forma de pago
      $this->Cell($w[6], $h, $row[6], 'LR', 0, 'R', $fill);//seguro
      $this->Cell($w[7], $h, $row[7], 'LR', 0, 'R', $fill);//flete
      $this->Cell($w[8], $h, $row[8], 'LR', 0, 'R', $fill);//total
      $this->Ln();
      $fill = !$fill;//estilo cebra en la factura
    }
    $this->Cell(array_sum($w), 0, '', 'T',1);//Linea de cierre
  }

  /**
   * Crea una tabla para un Manifiesto de carga.
   *
   * @param   array $data los datos que seran agregados a la tabla.
   * @since Junio 6, 2011
   * @author  Edgar Ortega Ramirez
   */
  public function ImprimirPlanilla($data) {
    $this->SetTextColor(0);
    $this->SetFontSize(7);
    $fill = false;
    $h=4;
    $y=96;//Altura inicial
    foreach($data as $row) {
      $this->MultiCell(22, 6, $row[0], 0, 'C', false, 0, 6, $y, true);
      $this->Cell(14.8, $h, $row[1], 0, 0, 'L', $fill);
      $this->Cell(15.4, $h, $row[2], 0, 0, 'R', $fill);
      $this->Cell(20.58, $h, $row[3], 0, 0, 'R', $fill);
      $this->Cell(18.3, $h, $row[4], 0, 0, 'R', $fill);
      $this->Cell(15, $h, $row[5], 0, 0, 'R', $fill);
      $this->Cell(16, $h, $row[6], 0, 0, 'R', $fill);
      $this->Cell(46, $h, substr($row[7], 0, 28), 0, 0, 'L', $fill); //nombre producto
      $this->Cell(41, $h, substr($row[8], 0, 26), 0, 0, 'L', $fill); //nombre cliente
      $this->Cell(42, $h, substr($row[9], 0, 26), 0, 0, 'L', $fill);
      $this->Cell(33, $h, substr($row[10], 0, 18), 0, 0, 'L', $fill); //ciudad destino
      $y=$y+$h;//Posicion Y de la siguiente linea
    }
  }

  /**
   * Crea una tabla para un Anexo de carga.
   *
   * @param   array $data los datos que seran agregados a la tabla.
   * @since Junio 6, 2011
   * @author  Edgar Ortega Ramirez
   */
  public function ImprimirPlanillaAnexo($data) {
    $this->SetTextColor(0);//Black
    $this->SetFontSize(7);
    // Data
    $fill = false;
    $h=6;
    foreach($data as $row) {
      $this->Cell(16, $h, $row[0], 0, 0, 'R', $fill); //No Remesa
      $this->Cell(13, $h, $row[1], 0, 0, 'L', $fill, '', 1); //Unidad Medida
      $this->Cell(12.7, $h, $row[2], 0, 0, 'R', $fill); //Cantidad
      $this->Cell(11, $h, $row[3], 0, 0, 'R', $fill); //Peso
      $this->Cell(11, $h, $row[4], 0, 0, 'C', $fill); //Codigo Naturaleza
      $this->Cell(10.8, $h, $row[5], 0, 0, 'C', $fill); //Codigo Empaque
      $this->Cell(13, $h, $row[6], 0, 0, 'R', $fill); //Codigo Producto
      $this->Cell(42, $h, substr($row[7], 0, 28), 0, 0, 'L', $fill, '', 1); //Producto
      $this->Cell(36, $h, substr($row[8], 0, 25), 0, 0, 'L', $fill, '', 1); //Remitnete
      $this->Cell(39, $h, substr($row[9], 0, 25), 0, 0, 'L', $fill, '', 1); //Destinatario
      $this->Cell(36.8, $h, substr($row[10], 0, 18), 0, 0, 'L', $fill, '', 1); //Destino
      $this->Cell(20.5, $h, number_format($row[11]), 0, 0, 'R', $fill); //Valor Asegurado
      $this->Cell(20, $h, number_format($row[12]), 0, 0, 'R', $fill); //Flete al cobro
      $this->Ln(4);
    }
  }

  /**
   * Indica que el PDF será para crear un manifiesto de carga, se establece
   * la plantilla del manifiesto.
   *
   * @author  Edgar Ortega
   * @since Junio 3, 2011
   */
  public function Manifiesto($configuracion) {
    // get the current page break margin
    $bMargin = $this->getBreakMargin();
    // get current auto-page-break mode
    $auto_page_break = $this->AutoPageBreak;
    // disable auto-page-break
    $this->SetAutoPageBreak(false, 0);
    // set background image
    $img_file = 'templates/Manifiesto.jpg';
    $this->Image($img_file, 5, 5, 287, 202, 'JPEG', '', '', false, 300, '', false, false, 0,false,false,false);
    //set header
    $this->Image(K_PATH_IMAGES.'logo.gif', 8, 5);
    $this->Image(K_PATH_IMAGES.'logo_vigilado.png', 180, 5);
    $this->Ln(-17); //distancia desde el borde sup.
    $this->SetTextColor(1, 58, 223);
    $this->SetFont("", "BI", 18);
    $this->Cell(185, 8, $configuracion->nombre_empresa, 0, 1, "C", false);
    $this->SetFont("", "", 10);
    $this->Cell(185, 4, "NIT ".$configuracion->nit_empresa, 0, 1, "C", false);
    $this->Cell(185, 4, $configuracion->direccion_empresa." | Tels.: ".$configuracion->telefono_empresa, 0, 1, "C", false);
    $this->Cell(185, 4, "Email: ".$configuracion->email_empresa, 0, 1, "C", false);
    $this->Cell(185, 4, $configuracion->ciudad_empresa, 0, 1, "C", false);

    $this->SetTextColor(0);
    // restore auto-page-break status
    $this->SetAutoPageBreak($auto_page_break, $bMargin);
    // set the starting point for the page content
    $this->setPageMark();
  }

  /**
   * Indica que el PDF será para crear un anexo de manifiesto de carga.
   * @author  Edgar Ortega
   * @since Junio 3, 2011
   */
  public function Anexo($configuracion) {
    $this->setPrintHeader(false);
    // get the current page break margin
    $bMargin = $this->getBreakMargin();
    // get current auto-page-break mode
    $auto_page_break = $this->AutoPageBreak;
    // disable auto-page-break
    $this->SetAutoPageBreak(false, 0);
    // set background image
    $img_file = 'templates/Anexo.jpg';
    $this->Image($img_file, 5, 5, 287, 202, 'JPEG', '', '', false, 300, '', false, false, 0,false,false,false);

    //set header
    $this->Image(K_PATH_IMAGES.'logo.gif',8,5);
    $this->Ln(-17);
    $this->SetTextColor(1, 58, 223);
    $this->SetFont("", "BI", 18);
    $this->Cell(190, 8, $configuracion->nombre_empresa, 0, 1, "C", false);//Empresa
    $this->SetFont("", "", 10);
    $this->Cell(190, 4, "NIT ".$configuracion->nit_empresa,0,1,"C",false);//NIT
    $this->Cell(190, 4, $configuracion->direccion_empresa." | Tels.: ".$configuracion->telefono_empresa,0,1,"C",false);
    $this->Cell(190, 4, "Email: ".$configuracion->email_empresa,0,1,"C",false);
    $this->Cell(190, 4, $configuracion->ciudad_empresa,0,1,"C",false);

    $this->SetTextColor(0);
    // restore auto-page-break status
    $this->SetAutoPageBreak($auto_page_break, $bMargin);
    // set the starting point for the page content
    $this->setPageMark();
  }

  public function OrdenRecogida() {
    require_once "../../class/Configuracion.php";
    $conf=new Configuracion;
    $configuracion=$conf->ObtenerConfiguracion();
    $this->setPrintHeader(false);
    // get the current page break margin
    $bMargin = $this->getBreakMargin();
    // get current auto-page-break mode
    $auto_page_break = $this->AutoPageBreak;
    // disable auto-page-break
    $this->SetAutoPageBreak(false, 0);

    //set header
    $this->Image(K_PATH_IMAGES.'logo.gif',8,5);
    $this->Image(K_PATH_IMAGES.'logo_vigilado.png',175,5);
    $this->Ln(-15);
    $this->SetTextColor(1,58,223);
    $this->SetFont("","BI",18);
    $this->Cell(185, 8,$configuracion['nombre_empresa'],0,1,"C",false);//Empresa
    $this->SetFont("","",10);
    $this->Cell(185, 4,"NIT ".$configuracion['nit_empresa'],0,1,"C",false);//NIT
    $this->Cell(185, 4,$configuracion['direccion_empresa']." | Tels.: ".$configuracion['telefono_empresa'],0,1,"C",false);
    $this->Cell(185, 4,"Email: ".$configuracion['email_empresa'],0,1,"C",false);
    $this->Cell(185, 4,$configuracion['ciudad_empresa'],0,1,"C",false);

    $this->SetTextColor(0);
    // restore auto-page-break status
    $this->SetAutoPageBreak($auto_page_break, 5);
    // set the starting point for the page content
    $this->setPageMark();
    $this->setPrintFooter(false);
    
  }

  /**
   * Indica que el PDF será para una guía, no se imprimirá el header ni el footer.
   */
 function Guia() {
    $this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", PDF_HEADER_STRING);
    $this->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $this->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $this->SetHeaderMargin(5);
    $this->SetFooterMargin(5);
    $this->SetAutoPageBreak(true, 10);
    $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $this->setFontSubsetting(false);
    $this->setPrintHeader(false);
    $this->setPrintFooter(false);
    $this->SetMargins(7.5, 3);
  }
  
}


/**
 * Clase para generar las facturas en formato PDF.
 *
 * @author  Edgar Ortega Ramírez
 * @author  Diciembre 22, 2011
 * @version 2.0
 * @see   TCPDF
 *
 */
class FacturaPDF extends TCPDF {

  /**
   * Imprime el Header de las facturas.
   * @since Diciembre 23, 2011
   */
  public function Header() {
    global $factura;
    global $configuracion;
    $formato_fecha='%d/%b/%Y';

    $this->Image(K_PATH_IMAGES.'logo.gif',8,3, 0, 20);
    $this->Image(K_PATH_IMAGES.'logo_vigilado.png',180,1,0,6); //:( Heavy... :(
    $this->Ln(-3);
    $color = $this->convertHTMLColorToDec('#335799');
    $this->SetTextColor($color['R'], $color['G'], $color['B']);
    $this->SetFont("","BI",18);
    $this->Cell(200, 6,$configuracion->nombre_empresa,0,1,"C",false, '', 0);
    $this->SetFont("","B",9);
    $this->Cell(185, 2,"NIT ".$configuracion->nit_empresa,0,1,"C",false);
    $this->SetFont("","",9);
    $this->MultiCell(185, 2, $configuracion->direccion_empresa, 0, 'C', false, 1, $this->GetX(), $this->GetY()-1);
    $this->MultiCell(185, 2, "Tels.: ".$configuracion->telefono_empresa, 0, 'C', false, 1, $this->GetX(), $this->GetY()-1);
    $this->MultiCell(185, 2, "Email: ".$configuracion->email_empresa, 0, 'C', false, 1, $this->GetX(), $this->GetY()-1);
    $this->MultiCell(185, 2, $configuracion->ciudad_empresa.'.', 0, 'C', false, 1, $this->GetX(), $this->GetY()-1);

    $this->SetTextColor(0);
    $this->SetFont('helvetica', 'B', 12);
    $this->MultiCell(50, 5, 'FACTURA DE VENTA', 0, 'C', false, 1, 150, 12);

    $this->RoundedRect(150, 17, 50, 8, 2, '1111', '', array(), array());

    $this->SetFont('helvetica', 'B', 14);
    $this->SetTextColor(255,0,0);
    $this->MultiCell(50, 8, $configuracion->facturacion_prefijo.'-'.$factura->id, 0, 'C', false, 1, 150, 17, true, 0, false, true, 8, 'M');

    $this->SetTextColor(0);
    $this->SetFont('helvetica', '', 9);
    $this->Ln(2);
    //Obtener posicion actual
    $x=$this->GetX()+120;
    $y=$this->GetY();
    //--
    $this->SetFont('helvetica', 'B', 9);
    $this->Cell(18, 7, ' CLIENTE: ', 'BTL', 0);
    $this->SetFont('helvetica', '', 9);
    $this->Cell(102, 7, $factura->idcliente.' - '.$factura->cliente->nombre_completo.' - '.$factura->cliente->numero_identificacion_completo, 'RTB', 1);

    $this->SetFont('helvetica', 'B', 9);
    $this->Cell(20, 7, ' DIRECCIÓN: ', 'BTL', 0);
    $this->SetFont('helvetica', '', 9);
    $this->Cell(100, 7, $factura->cliente->direccion, 'RTB', 1);

    $this->SetFont('helvetica', 'B', 9);
    $this->Cell(20, 6, ' TELÉFONO: ', 'LTB', 0);
    $this->SetFont('helvetica', '', 9);
    $this->Cell(48, 6, $factura->cliente->primer_telefono(), 'RTB', 0);
    $this->SetFont('helvetica', 'B', 9);
    $this->Cell(37, 6, ' CONDICIÓN DE PAGO: ', 'LTB', 0);
    $this->SetFont('helvetica', '', 9);
    $this->Cell(15, 6, $factura->condicionpago.' DIAS', 'RTB', 1);

    $this->SetX($x);
    $this->SetY($y);

    $fecha_factura = "<b>FECHA EMISION</b><br>".strftime($formato_fecha, strtotime($factura->fechaemision));
    $x1 = $x2 = $this->GetX() + 155.5;
    $y1 = $y2 = $this->GetY() + 12;
    $this->Line($x1, $y1, $x2, $y2);
    $fecha_vencimiento = "<b>FECHA VENCIMIENTO</b><br>".strftime($formato_fecha, strtotime($factura->fechavencimiento));
    $this->MultiCell(40, 10, $fecha_factura, 1, 'C', false, 1, $x, $this->GetY(), true, 0, true);
    $this->MultiCell(40, 10, $fecha_vencimiento, 1, 'C', false, 1, $x, $this->GetY(), true, 0, true);

    $this->SetX($x);
    $this->SetY($y);

    $x = $x+40;

    $l1 = "Régimen Común";
    $l2 = "Res. DIAN ".$factura->resolucion->numero;
    $factura->resolucion->fecha = strftime('%Y/%m/%d', strtotime($factura->resolucion->fecha));
    $l3 = "Del ".$factura->resolucion->fecha;
    $l4 = "Rango: BQA-".$factura->resolucion->inicio." - BQA-".$factura->resolucion->fin;
    $l5 = "Actividad Servicio Cod. 302";
    $l6 = "Tarifa Aplicar ".$configuracion->facturacion_tarifa_aplicar;
    $this->SetFont('helvetica', '', 7);
    $this->MultiCell(40, 2, $l1, 0, 'C', false, 1, $x, $this->GetY());
    $this->MultiCell(40, 2, $l2, 0, 'C', false, 1, $x, $this->GetY());
    $this->MultiCell(40, 2, $l3, 0, 'C', false, 1, $x, $this->GetY());
    $this->MultiCell(40, 2, $l4, 0, 'C', false, 1, $x, $this->GetY());
    $this->MultiCell(40, 2, $l5, 0, 'C', false, 1, $x, $this->GetY());
    $this->MultiCell(40, 2, $l6, 0, 'C', false, 1, $x, $this->GetY());

    $this->Ln(3);
  }

  /**
   * Crea la tabla con la información de las guías de una factura.
   *
   * @param array $data los datos que seran agregados a la tabla.
   * @since Diciembre 23, 2011
   */
  public function FacturaBody($data) {
    $this->FacturaHead(); //Imprimir la cabecera de la tabla

    // Color and font restoration
    $this->SetFillColor(224, 235, 255);
    $this->SetFont('','',7.5);

    $w = array(65, 10, 20, 25, 50, 30); //total debe ser igual a 200
    $fill = false;
    $this->SetFillColor(255);
    $h=5;
    $i=1;
    foreach($data as $row) {

      $this->Cell($w[0], $h, substr($row[0], 0, 30), 'LR', 0, 'L', $fill);
      $this->Cell($w[1], $h, $row[1], 'LR', 0, 'R', $fill);
      $this->Cell($w[2], $h, $row[2], 'LR', 0, 'R', $fill);
      $this->Cell($w[3], $h, $row[3], 'LR', 0, 'R', $fill);
      $this->Cell($w[4], $h, substr($row[4], 0, 20), 'LR', 0, 'L', $fill);
      $this->Cell($w[5], $h, $row[5], 'LR', 0, 'R', $fill);//total
      $this->Ln();
      $fill=!$fill;//estilo cebra en la factura

      /* IMPRIME 36 POR PAGINA para las facturas que fueron creadas con mas de 35 guias */
      if($i>=36){
        $this->Cell(array_sum($w), 0, '', 'T',1);//Linea de cierre superior
        $this->AddPage();
        $this->FacturaHead();
        $i=1;
      }else{
        $i+=1;
      }
    }
    $this->Cell(array_sum($w), 0, '', 'T',1);//Linea de cierre
  }

  /**
   * Imprime la cabecera de la tabla que contiene las guías.
   * @since Diciembre 23, 2011
   */
  function FacturaHead(){
    $this->SetFillColor(255); //Blanco
    $this->SetTextColor(0); //Negro
    $this->SetDrawColor(0, 0, 0); //Negro
    $this->SetFont('', 'B',9);
    $header = array('Destinatario', 'Unid', 'Guia', 'Doc. Cliente', 'Destino', 'Valor');
    $w = array(65, 10, 20, 25, 50, 30); //total debe ser igual a 200
    $num_headers = count($header);
    for($i = 0; $i < $num_headers; ++$i) {
      $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }

    /* Restaurar la fuente */
    $this->SetTextColor(0);
    $this->SetFont('', '',8);
    $this->Ln();
  }
}

/**
 * Clase CartaPDF
 */
class CartaPDF extends TCPDF {

  function setInfo() {
    $this->SetCreator('Logística');
    $this->SetAuthor('Edgar Ortega Ramírez');
    $this->SetTitle("Carta");
    $this->SetSubject("Carta");
    $this->SetKeywords('Transportes Mario Acosta, Carta');
  }

  function Header(){
    global $configuracion;
    $this->RoundedRect(5, 5, 200, 287, 5);
    $this->Image(K_PATH_IMAGES.'logo.gif', 12, 10, 0, 22);
    $this->SetFont('', 'B',18);
    $this->SetTextColor(39, 97, 156);
    $this->MultiCell(0, 10, strtoupper($configuracion['nombre_empresa']), 0, 'C', false, true, 40, 17);
  }

  function Footer(){
    global $configuracion;
    $this->SetFont('', '',7);
    $this->SetTextColor(100);
    $texto='NIT: '.$configuracion['nit_empresa']."\r\n".$configuracion['direccion_empresa']."\r\n".'http://transmarioacosta.com/'."\r\n".$configuracion['email_empresa']."\r\nTels: ".$configuracion['telefono_empresa']."\r\n".$configuracion['ciudad_empresa'];
    $this->MultiCell(0, 4, $texto, 0, 'C', false, false);
  }
}

class TarjetaVehiculo extends TCPDF{

  function setInfo() {
    $this->SetCreator('Logística');
    $this->SetAuthor('Edgar Ortega Ramírez');
    $this->SetTitle("Tarjeta Vehiculo");
    $this->SetSubject("Tarjeta Vehiculo");
    $this->SetKeywords('Transportes Mario Acosta, Carta');
  }

  function imprimir($info, $configuracion){

  }
}
