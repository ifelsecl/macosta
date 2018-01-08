<?php
function RandomString($length=15,$uc=true,$n=true,$sc=false){
	$source = 'abcdefghijklmnopqrstuvwxyz';
	if($uc==1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if($n==1) $source .= '1234567890';
	if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
	if($length>0){
		$rstr = "";
		$source = str_split($source,1);
		for($i=1; $i<=$length; $i++){
			mt_srand((double)microtime() * 1000000);
			$num = mt_rand(1,count($source));
			$rstr .= $source[$num-1];
		}

	}
	return $rstr;
}
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$ext=strrchr($_FILES['Filedata']['name'],".");
	$nombre=RandomString().$ext;
	$targetFile =  str_replace('//','/',$targetPath).$nombre;
	if(move_uploaded_file($tempFile,$targetFile)){
		echo $nombre;
	}else{
		echo "error";
	}
}
?>