<?php
$raiz = "../../";
if (empty($_FILES)) {
  exit('<h2>El archivo está en blanco.</h2>');
}
$tempFile = $_FILES['Filedata']['tmp_name'];
if( ! $archivo=file($tempFile) ) {
  exit("<h2>No se ha podido cargar el archivo, intentalo nuevamente.</h2>");
}

require_once $raiz."class/Cliente.php";
$objCliente = new Cliente;
echo '<style>
  .error{color:red;font-weight:bold;font-size:10px;}
  .correcto{color:green;font-weight:bold;}
  .debug{color:blue;font-size:10px;}
  .alerta{color:orange;font-weight:bold;font-size:10px;}
  </style>';
$n = 1;
foreach ($archivo as $num => $line) {
  $linea = explode(',', $line);
  foreach ($linea as $key => $value) {
    $linea[$key] = trim($value);
  }
  if( ! isset($linea[1]) ) exit('Falta Numero identificacion en linea '.$n);
  if( ! isset($linea[2]) ) exit('Falta Nombre en linea '.$n);
  if( ! isset($linea[3]) ) exit('Falta Primer Apellido en linea '.$n);
  if( ! isset($linea[4]) ) exit('Falta Segundo Apellido en linea '.$n);
  if( ! isset($linea[5]) ) exit('Falta Razon Social en linea '.$n);
  $tipo_identificacion  = $linea[0];
  $numero_identificacion  = $linea[1];
  $nombre         = $linea[2];
  $primer_apellido    = $linea[3];
  $segundo_apellido     = $linea[4];
  $direccion        = $linea[6];
  $id_ciudad        = $linea[7];
  $telefono       = $linea[8];
  $email          = $linea[9];
  $sitio_web        = $linea[10];
  $restriccion_peso     = $linea[11];
  $porcentaje_seguro    = $linea[12];
  $id_forma_juridica    = $linea[13];
  $id_regimen       = $linea[14];
  $descuento        = $linea[15];
  $digito_verificacion  = isset($linea[16]) ? $linea[16] : 0;
  if($objCliente->Agregar($tipo_identificacion, $numero_identificacion, $nombre, $primer_apellido, $segundo_apellido, $direccion, $id_ciudad, $telefono, $email, $sitio_web, $restriccion_peso, $porcentaje_seguro, $id_forma_juridica, $id_regimen, $descuento, $digito_verificacion, '', '', '')){
    echo "<p class='correcto'>Linea $n importada con éxito.</p>";
  }else{
    if ($objCliente->con->error_no == 1452) {
      echo "<p class='alerta'>(Linea $n) Los datos no coinciden con los esperados.</p>";
    }elseif ($objCliente->con->error_no == 1064){
      echo "<p class='error'>(Linea $n) Has dejado un campo obligatorio en blanco, revisa los datos.</p>";
    }else{
      echo "<p class='error'>Linea $n no pudo ser importada, revisa los datos.</p>";
    }
  }
  $n+=1;
}
?>
