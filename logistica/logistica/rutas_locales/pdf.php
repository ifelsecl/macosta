<?php
$raiz = '../../';
require_once $raiz.'php/tcpdf/tcpdf.php';
require_once $raiz."class/Configuracion.php";

/**
* Genera el formato de las rutas locales en PDF.
* @since Enero 6, 2013
*/
class RutaLocalPDF extends TCPDF {

	public $configuracion;

	function Header() {
		$this->configuracion = new Configuracion;
		$this->Image(K_PATH_IMAGES.'logo.jpg', 8, 2);
		$this->Image(K_PATH_IMAGES.'logo_vigilado.png', 175, 9, 30);
		$this->SetTextColor(1, 58, 223);
		$this->SetFont("", "BI", 18);
		$this->Cell(185, 8, $this->configuracion->nombre_empresa, 0, 1, "C", false);//Empresa
		$this->SetFont("", "", 10);
		$this->Cell(185, 4, "NIT ".$this->configuracion->nit_empresa, 0, 1, "C", false);//NIT
		$this->Cell(185, 4, $this->configuracion->direccion_empresa." | Tels.: ".$this->configuracion->telefono_empresa, 0, 1, "C", false);
		$this->Cell(185, 4, "Email: ".$this->configuracion->email_empresa, 0, 1, "C", false);
		$this->Cell(185, 4, $this->configuracion->ciudad_empresa, 0, 1, "C", false);

		$this->SetTextColor(0);
	}

	function Footer() {
		$this->SetY(-15);
		$this->Cell(60, 10, 'Firma Despachador', 'T', false, 'L', 0);
		$this->Cell(65, 10, ' ', 0);
		$this->Cell(60, 10, 'Firma Conductor', 'T', false, 'R', 0);
	}

	function info() {
		$this->SetCreator('Logística');
		$this->SetAuthor('Edgar Ortega Ramírez');
		$this->SetTitle("Ruta Local");
		$this->SetSubject("Ruta Local");
		$this->SetKeywords('Transportes Mario Acosta, Ruta Local, Ruta Domestica');
		$this->setFontSubsetting(false);
		$this->SetMargins(14, 27, 14);
		$this->SetHeaderMargin(1);
		$this->SetFooterMargin(10);
		$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	}
}

?>