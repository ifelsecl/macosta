<?php
$raiz = '../../';
require_once $raiz.'seguridad.php';
if( ! isset($_SESSION['permisos'][USUARIOS_EXPORTAR]) ){
	include_once $raiz.'mensajes/permiso.php';
	exit;
}
$usuario = new Usuario;

if ((isset($_GET['token']) and $_GET['token']==$_SESSION['token'])) {
	if (isset($_GET['f']) and $_GET['f']=='XLS') {
		require_once $raiz.'php/Excel.inc.php';
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-type: application/vnd.ms-excel; charset=utf-8');
		header("Content-Disposition: attachment; filename=Usuarios.xls");
		$result=$usuario->Exportar("XLS");
		echo xlsBOF();
		$año=date("Y");
		echo xlsWriteLabel(0,0,"Usuarios $año");
		echo xlsWriteLabel(2,0,"ID");
		echo xlsWriteLabel(2,1,"Nombre");
		echo xlsWriteLabel(2,2,"Usuario");
		echo xlsWriteLabel(2,3,"Clave");
		echo xlsWriteLabel(2,4,"Correo electronico");
		echo xlsWriteLabel(2,5,"Fecha creacion");
		echo xlsWriteLabel(2,6,"Ultimo acceso");
		echo xlsWriteLabel(2,7,"Perfil");
		echo xlsWriteLabel(2,8,"Activo");
		$xlsRow = 3;
		while($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo xlsWriteNumber($xlsRow, 0, $row['id']);
	 	   	echo xlsWriteLabel($xlsRow, 1, $row['nombre']);
    		echo xlsWriteLabel($xlsRow, 2, $row['usuario']);
    		echo xlsWriteLabel($xlsRow, 3, $row['clave']);
    		echo xlsWriteLabel($xlsRow, 4, $row['email']);
    		echo xlsWriteLabel($xlsRow, 5, $row['fechacreacion']);
    		echo xlsWriteLabel($xlsRow, 6, $row['ultimo_acceso']);
    		echo xlsWriteLabel($xlsRow, 7, $row['perfil']);
    		echo xlsWriteLabel($xlsRow, 8, $row['activo']);
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
		header("Content-Disposition: attachment; filename=Usuarios.csv");
		$result=$usuario->Exportar("CSV");
		$sep = ","; //caracter separador
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$l="";
			foreach ($row as $key => $value) {
				$l.='"'.$value.'"'.$sep;
			}
			$l.="\r\n";
			echo $l;
		}
		exit();
	}
}
if ((!isset($_GET['token']) or (isset($_GET['token']) and $_GET['token']!=$_SESSION['token'])) and isset($_GET['f'])){
	echo '<h3>Parece que algo no est&aacute; bien...</h3>';
	exit;
}
?>
<script>
$('#regresar').click(function(){
	regresar();
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Usuarios | Exportar</h2>
<table class="table">
	<thead>
	</thead>
	<tbody>
		<tr>
			<td width="32"><img src="img/xls.png" alt="Formato XLS" title="Formato XLS" /></td>
			<td>
				<b>Formato XLS</b><br />
				Puedes exportar la lista de usuarios en formato XLS de Microsoft Excel.
				Este formato no puede ser importado.
			</td>
			<td><a class="btn btn-info btn-large" href="configuracion/usuarios/exportar.php?f=XLS&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
		</tr>
		<tr>
			<td><img src="img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
			<td>
				<b>Formato CSV</b><br />
				Este formato puede ser importado.
			</td>
			<td><a class="btn btn-info btn-large" href="configuracion/usuarios/exportar.php?f=CSV&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
		</tr>
	</tbody>
</table>