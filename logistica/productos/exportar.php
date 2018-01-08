<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PRODUCTOS_EXPORTAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
if (isset($_GET['token']) and $_GET['token'] == $_SESSION['token']) {
	$productos = Producto::all();
	if (isset($_GET['f']) and $_GET['f'] == 'XLS') {
		require_once Logistica::$root."php/Excel.inc.php";
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-type: application/vnd.ms-excel; charset=utf-8');
		header("Content-Disposition: attachment; filename=Productos.xls");

		echo xlsBOF();
		$ano = date("Y");
		echo xlsWriteLabel(0, 0, "Productos $ano");
		echo xlsWriteLabel(2, 0, "ID");
		echo xlsWriteLabel(2, 1, "Nombre");
		echo xlsWriteLabel(2, 2, "Tipo");
		echo xlsWriteLabel(2, 3, "Fecha Modificacion");
		echo xlsWriteLabel(2, 4, "Activo");
		$xlsRow = 3;
		foreach ($productos as $p) {
			echo xlsWriteNumber($xlsRow, 0, $p->id);
			echo xlsWriteLabel($xlsRow, 1, $p->nombre);
			echo xlsWriteLabel($xlsRow, 2, $p->tipo);
			echo xlsWriteLabel($xlsRow, 3, $p->fechamodificacion);
			echo xlsWriteLabel($xlsRow, 4, $p->activo);
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
		header("Content-Disposition: attachment; filename=Productos.csv");
		$sep = ",";
		foreach ($productos as $p) {
			echo $p->id.$sep.'"'.$p->nombre.'"'.$sep.$p->tipo.$sep.$p->activo.$sep.$p->fechamodificacion."\r\n";
		}
		exit;
	}
}
if ((! isset($_GET['token']) or (isset($_GET['token']) and $_GET['token'] != $_SESSION['token'])) and isset($_GET['f'])) {
	echo '<h2>Algo ha salido mal, recarga la página e intentalo nuevamente.</h2>';
	exit;
}
?>
<script>
$('#regresar').click(function(){
	regresar();
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Productos | Exportar</h2>
<table class="table">
	<tbody>
		<tr>
			<td width="32"><img src="img/xls.png" alt="Formato XLS" title="Formato XLS" /></td>
			<td><b>Formato XLS</b><br />Puedes exportar la lista de productos en
				formato XLS de Microsoft Excel. Este formato no puede ser importado.
			</td>
			<td style="width: 130px"><a class="btn btn-info btn-large" href="logistica/productos/exportar?f=XLS&token=<?php echo $_SESSION['token'];?>"><i class="icon-download-alt"></i> Exportar</a></td>
		</tr>
		<tr>
			<td width="32"><img src="img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
			<td><b>Formato CSV</b><br />Este formato puede ser importado, si
				quiere realizar una copia de seguridad use este formato.</td>
			<td style="width: 130px"><a class="btn btn-info btn-large" href="logistica/productos/exportar?f=CSV&token=<?php echo $_SESSION['token'];?>"><i class="icon-download-alt"></i> Exportar</a></td>
		</tr>
	</tbody>
</table>
