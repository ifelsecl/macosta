<?php
$raiz='../../';
require_once $raiz."seguridad.php";
if( !isset($_SESSION['permisos'][BACKUP_ENTRAR]) ){
	include $raiz."mensajes/permiso.php";
	exit;
}
$nombre='Backup.'.date('d.m.Y').'.zip';
@unlink($nombre); //Eliminar cualquier archivo anterior.

require_once $raiz."class/MySQLDump.class.php";
$objDump=new MySQLDump();
$bdd=$objDump->Dump(false);

$zip = new ZipArchive();

if ($zip->open($nombre, ZIPARCHIVE::OVERWRITE | ZIPARCHIVE::CREATE)!==TRUE) {
    exit("No se pudo abrir <$nombre>");
}
if(file_exists($bdd) and is_readable($bdd)){
	$zip->addFile($bdd,"base de datos/$bdd");
}
$zip->addEmptyDir('conductores');
$dir='../../logistica/conductores/fotos/';
if(is_dir($dir)){
	if ($d = opendir($dir)) {
		while(($file = readdir($d)) !== false) {
			if ($file!='.' and $file!='..' and $file!='index.php'){
				$zip->addFile($dir.$file, 'conductores/'.$file);
			}
		}
		closedir($d);
	}
}else{
	$zip->addFromString('info.txt', 'El directorio de fotos de los conductores no existe!');
}
//Agregar guias
$zip->addEmptyDir('guias');
$dir='../../logistica/guias/imagenes/';
if(is_dir($dir)){
	if ($d = opendir($dir)) {
		while(($file = readdir($d)) !== false) {
			if ($file!='.' and $file!='..' and $file!='index.php'){
				//$zip->addFromString("$file.txt", "Archivo agregado $dir $file!");
				$zip->addFile($dir.$file,'guias/'.$file);
				
			}
		}
		closedir($d);
	}
}else{
	$zip->addFromString('info.txt', 'El directorio de imagenes de las guias no existe!');
}

$zip->close();
//Borrar base de datos
@unlink($bdd);

//Enviar al navegador
header('Content-Description: File Transfer');
header("Content-Type: application/zip"); 
header('Content-Disposition: attachment; filename='.$nombre);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header("Content-Length: ".filesize($nombre));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
ob_clean();
flush();
readfile($nombre);