<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][TERCEROS_EXPORTAR])) {
	include Logistica::$root.'mensajes/permiso.php';
	exit;
}

if (isset($_GET['token']) and $_GET['token']==$_SESSION['token']) {
	$tercero = new Tercero;

	if(isset($_GET['f']) and $_GET['f']=='XLS'){
		require_once Logistica::$root."php/Excel.inc.php";
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-type: application/vnd.ms-excel; charset=utf-8');
		header("Content-Disposition: attachment; filename=Terceros.xls");

		$terceros = $tercero->all();
		echo xlsBOF();
		echo xlsWriteLabel(0, 0, "Terceros ".date('Y'));
		echo xlsWriteLabel(2, 0, "ID");
		echo xlsWriteLabel(2, 1, "Tipo de identificacion");
		echo xlsWriteLabel(2, 2, "Numero de identificacion");
		echo xlsWriteLabel(2, 3, "Nombre");
		echo xlsWriteLabel(2, 4, "Ciudad");
		echo xlsWriteLabel(2, 5, "Direccion");
		echo xlsWriteLabel(2, 6, "Telefono");
		echo xlsWriteLabel(2, 7, "Email");
		echo xlsWriteLabel(2, 8, "Activo");
		$xlsRow = 3;
		foreach ($terceros as $t) {
			echo xlsWriteNumber($xlsRow, 0, $t->id);
			echo xlsWriteLabel($xlsRow, 1, $t->tipo_identificacion);
			echo xlsWriteLabel($xlsRow, 2, $t->numero_identificacion_completo);
			echo xlsWriteLabel($xlsRow, 3, utf8_decode($t->nombre_completo));
			echo xlsWriteLabel($xlsRow, 4, $t->ciudad_nombre);
			echo xlsWriteLabel($xlsRow, 5, $t->direccion);
			echo xlsWriteNumber($xlsRow, 6, $t->telefono);
			echo xlsWriteLabel($xlsRow, 7, $t->email);
			echo xlsWriteLabel($xlsRow, 8, $t->activo);
			$xlsRow++;
		}
		echo xlsEOF();
		exit;
	}
	if (isset($_GET['f']) and $_GET['f']=='CSV') {
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-type: text/comma-separated-values; charset=utf-8');
		header("Content-Disposition: attachment; filename=Terceros.csv");
		$result = $tercero->Exportar();
		$sep = ";";
		while ($row = mysql_fetch_assoc($result)) {
			$l = "";
			foreach ($row as $key => $value) {
				$l .= '"'.$value.'"'.$sep;
			}
			$l.="\r\n";
			echo $l;
		}
		exit;
	}
}
if ((!isset($_GET['token']) or (isset($_GET['token']) and $_GET['token']!=$_SESSION['token'])) and isset($_GET['f'])){
	exit('<h2>Algo ha salido mal...</h2>');
}
?>
<script>
$('#regresar').click(function(event){
	regresar();
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Terceros | Exportar</h2>
<table class="table table-hover">
	<tbody>
		<tr>
			<td width="32"><img src="img/xls.png" alt="Formato XLS" title="Formato XLS" /></td>
			<td><b>Formato XLS</b><br />Puedes exportar la lista de terceros en
				formato XLS de Microsoft Excel. Este formato no puede ser importado.
			</td>
			<td style="width: 120px"><a class="btn btn-info" target="_blank" href="logistica/terceros/exportar.php?f=XLS&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
		</tr>
		<tr>
			<td width="32"><img src="img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
			<td><b>Formato CSV</b><br />Este formato puede ser importado, si
				quiere realizar una copia de seguridad use este formato.</td>
			<td style="width: 120px"><a class="btn btn-info" target="_blank" href="logistica/terceros/exportar.php?f=CSV&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
		</tr>
	</tbody>
</table>
