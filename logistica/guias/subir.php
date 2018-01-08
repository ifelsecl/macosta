<?php
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = '../../../guias_escaneadas/';
	$ext=strrchr($_FILES['Filedata']['name'],".");
	$nombre=str_pad($_REQUEST['id'], 10, '0', STR_PAD_LEFT).'_1'.$ext;
	$targetFile = $targetPath.$nombre;
	if(move_uploaded_file($tempFile,$targetFile)){
		echo 'ok';
	}else{
		echo "error";
	}
}
?>