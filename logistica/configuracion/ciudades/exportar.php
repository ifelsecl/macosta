<?php
$raiz='../../';
require $raiz."seguridad.php";

if (isset($_GET['token']) and $_GET['token'] == $_SESSION['token']) {
	$ciudad = new Ciudad;
	$result = DBManager::execute( Ciudad::all('sql') );
	if (isset($_GET['f']) and $_GET['f'] == 'XLS') { //XLS Excel 2003
		require_once $raiz."php/Excel.inc.php";
		$nombre = "Ciudades.xls";
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-type: application/vnd.ms-excel; charset=utf-8');
		header("Content-Disposition: attachment; filename=$nombre");

		echo xlsBOF();

		echo xlsWriteLabel(0, 0, "Listado General de Ciudades");
		echo xlsWriteLabel(2, 0, "Codigo");
		echo xlsWriteLabel(2, 1, "Departamento");
		echo xlsWriteLabel(2, 2, "Municipio");
		echo xlsWriteLabel(2, 3, "Poblacion");
		$xlsRow = 3;
		while ($row = mysql_fetch_object($result)) {
			echo xlsWriteNumber($xlsRow, 0, $row->id);
			echo xlsWriteLabel($xlsRow, 1, utf8_decode($row->departamento_nombre));
			echo xlsWriteLabel($xlsRow, 2, utf8_decode($row->municipio));
			echo xlsWriteLabel($xlsRow, 3, utf8_decode($row->nombre));
			$xlsRow++;
		}
		echo xlsEOF();
		exit;
	}
	if (isset($_GET['f']) and $_GET['f']=='CSV') {
		$nombre="Ciudades.csv";
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-type: text/comma-separated-values; charset=UTF-8');
		header("Content-Disposition: attachment; filename=$nombre");

		$sep = ";";
		while ($row = mysql_fetch_assoc($result)) {
			echo implode($sep, $row)."\r\n";
		}
		exit;
	}
}
if ((! isset($_GET['token']) or (isset($_GET['token']) and $_GET['token'] != $_SESSION['token'])) and isset($_GET['f'])) {
	echo '<h3>Algo ha salido mal... recarga la p&aacute;gina e intentalo nuevamente.</h3>';
	exit;
}
?>
<button class="btn btn-success pull-right" id="regresar" onclick="regresar()">Regresar</button>
<h2>Ciudades | Exportar</h2>
<table class="table">
	<tr>
		<td width="32"><img src="img/xls.png" alt="Formato XLS" title="Formato XLS" /></td>
		<td>
			<b>Formato XLS</b><br>Puedes exportar la lista en formato XLS de Microsoft Excel 2003. Este formato no puede ser importado.
		</td>
		<td style="width: 110px">
			<a class="btn btn-info" target="_blank" href="configuracion/ciudades/exportar.php?f=XLS&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a>
		</td>
	</tr>
	<tr>
		<td width="32"><img src="img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
		<td>
			<b>Formato CSV</b><br>Este formato puede ser importado, si quiere realizar una copia de seguridad use este formato.
		</td>
		<td style="width: 110px">
			<a class="btn btn-info" target="_blank" href="configuracion/ciudades/exportar.php?f=CSV&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a>
		</td>
	</tr>
</table>
