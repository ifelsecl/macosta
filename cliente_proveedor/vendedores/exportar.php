<?php
require_once '../../seguridad.php';
if (isset($_GET['token']) and $_GET['token']==$_SESSION['token']) {
	if (isset($_GET['f']) and $_GET['f']=='XLS'){
	require_once '../../php/Excel.inc.php';
	header("Pragma: no-cache");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header('Content-type: application/vnd.ms-excel; charset=utf-8');
	header("Content-Disposition: attachment; filename=Proveedores.xls");
	require_once '../../class/proveedor.class.php';
	$prov=new Proveedor();
	$result=$prov->Exportar("XLS");
	echo xlsBOF();
	$año=date("Y");
	echo xlsWriteLabel(0,0,"Proveedores $año");
	echo xlsWriteLabel(2,0,"ID");
	echo xlsWriteLabel(2,1,"Nombre");
	echo xlsWriteLabel(2,2,"Cedula/NIT");
	echo xlsWriteLabel(2,3,"Ciudad");
	echo xlsWriteLabel(2,4,"Departamento");
	echo xlsWriteLabel(2,5,"Direccion");
	echo xlsWriteLabel(2,6,"Telefono");
	echo xlsWriteLabel(2,7,"Correo electronico");
	echo xlsWriteLabel(2,8,"Sitio web");
	echo xlsWriteLabel(2,9,"Forma Juridica");
	echo xlsWriteLabel(2,10,"Regimen");
	echo xlsWriteLabel(2,11,"Fecha Modificacion");
	echo xlsWriteLabel(2,12,"Activo");
	$xlsRow = 3;
	while($proveedor=mysql_fetch_array($result)) {
		echo xlsWriteNumber($xlsRow,0,$proveedor['id']);
	    echo xlsWriteLabel($xlsRow,1,$proveedor['nombreproveedor']);
    	echo xlsWriteNumber($xlsRow,2,$proveedor['nit']);
    	echo xlsWriteLabel($xlsRow,3,$proveedor['nombreciudad']);
    	echo xlsWriteLabel($xlsRow,4,$proveedor['nombredepartamento']);
    	echo xlsWriteLabel($xlsRow,5,$proveedor['direccion']);
    	echo xlsWriteNumber($xlsRow,6,$proveedor['telefono']);
    	echo xlsWriteLabel($xlsRow,7,$proveedor['email']);
    	echo xlsWriteLabel($xlsRow,8,$proveedor['sitioweb']);
    	echo xlsWriteLabel($xlsRow,9,$proveedor['nombreformajuridica']);
    	echo xlsWriteLabel($xlsRow,10,$proveedor['nombreregimen']);
    	echo xlsWriteLabel($xlsRow,11,$proveedor['fechamodificacion']);
    	echo xlsWriteLabel($xlsRow,12,$proveedor['activo']);
    	$xlsRow++;
	}
	echo xlsEOF();
	exit();
	}
	if (isset($_GET['f']) and $_GET['f']=='CSV') {
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-type: text/comma-separated-values; charset=UTF-8');
		header("Content-Disposition: attachment; filename=Proveedores.csv");
		require_once '../../class/proveedor.class.php';
		$prov=new Proveedor();
		$result=$prov->Exportar("CSV");
		$sep = ",";
		while($row = mysql_fetch_array($result) ) {
			echo /*$linea=*/ $row['id'].$sep.'"'.$row['nombre'].'"'.$sep.$row['nit'].$sep.'"'.$row['direccion'].'"'.$sep.$row['idciudad'].$sep.'"'.$row['telefono'].'"'.$sep.'"'.$row['email'].'"'.$sep.'"'.$row['sitioweb'].'"'.$sep.$row['idformajuridica'].$sep.$row['idregimen'].$sep.'"'.$row['fechamodificacion'].'"'.$sep.$row['activo']."\r\n"; //cada campo separado con $sep.
			//fwrite($f,$linea);
		}
		//fclose($f);
		exit();
	}
}
if ((!isset($_GET['token']) or (isset($_GET['token']) and $_GET['token']!=$_SESSION['token'])) and isset($_GET['f'])){
	echo '<h3>Algo ha salido mal...</h3>';
	exit();
}
?>
<script type="text/javascript">
$('button, #exportarXLS,#exportarCSV').button();
$('#regresar').click(function(){
	var ruta="cliente_proveedor/proveedores/";
	$(".right_content").load(ruta);
});
</script>
<button id="regresar" class="btn btn-success">Regresar</button>
<h2>Exportar proveedores...</h2>
<table class="table table-bordered" style="width:500px">
	<thead>
	</thead>
	<tbody>
		<tr>
			<td width="32"><img src="./img/xls.png" alt="Formato XLS" title="Formato XLS" /></td>
			<td>
				<b>Formato XLS</b><br />
				Puedes exportar la lista de proveedores en formato XLS de Microsoft Excel.
				Este formato no puede ser importado.
			</td>
			<td><a href="./cliente_proveedor/proveedores/exportar.php?f=XLS&token=<?php echo $_SESSION['token'];?>" id="exportarXLS">Exportar</a></td>
		</tr>
		<tr>
			<td width="32"><img src="./img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
			<td>
				<b>Formato CSV</b><br />
				Este formato puede ser importado.
			</td>
			<td><a id="exportarCSV" href="./cliente_proveedor/proveedores/exportar.php?f=CSV&token=<?php echo $_SESSION['token'];?>">Exportar</a></td>
		</tr>
	</tbody>
</table>
