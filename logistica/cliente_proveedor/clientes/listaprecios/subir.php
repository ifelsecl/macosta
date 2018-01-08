<?php
$raiz="../../..";
if (! isset($_POST['id'])) {
  echo "<h2>Algo ha salido mal, recarga la página e intentalo nuevamente.</h2>";
  exit;
}
if (! empty($_FILES)) {
  $tempFile = $_FILES['Filedata']['tmp_name'];
  if(! $lineas = file($tempFile)) {
    echo "<h2>No se ha podido cargar el archivo, intentalo nuevamente.</h2>";
    exit;
  }

  echo '<style>
  .error{color:red;font-weight:bold;font-size:10px;}
  .correcto{color:green;font-weight:bold;}
  .debug{color:blue;font-size:10px;}
  .alerta{color:orange;font-weight:bold;font-size:10px;}
</style>';

  require_once $raiz."/class/DBManager.php";
  require_once $raiz."/class/Configuracion.php";
  require_once $raiz."/class/Embalaje.php";

  DBManager::connect();
  $objConf = new Configuracion;
  $objEmbalaje = new Embalaje;
  $calcular = $objConf->CalcularAutomaticamenteKiloyKiloVol();

  //DEBUG, true NO INSERTA REGISTROS
  $debug = FALSE;

  $id_cliente = $_POST['id'];
  $n = 0;
  foreach ($lineas as $num_linea => $linea) {
    $num_linea++;
    $array = explode(";", $linea);
    if(! isset($array[1])) {
      $array = explode(",", $linea);
    }
    $ciudad_origen = trim($array[0]);
    if(isset($array[1])) $ciudad_destino = trim($array[1]);
    else $ciudad_destino = 0;

    if(isset($array[2])) $id_embalaje = trim($array[2]);
    else $id_embalaje = 1;

    if(isset($array[3])) $precio = trim($array[3]);
    else $precio = 0;

    if(isset($array[4])) $seguro = trim($array[4]);
    else $seguro = 1;

    /* Insertar el registro */

    if ($calcular) {
      $precio_kilo = 0;
      if($info_embalaje = Embalaje::find($id_embalaje)) {
        if ($embalaje->tipo_cobro == 'Caja') {
          $precio_kilo = round($precio/30);
        }
      }
      $query="INSERT INTO listaprecios VALUES($id_cliente,$ciudad_origen,$ciudad_destino,$id_embalaje,$precio, $precio_kilo, $precio_kilo,'$seguro')";
    } else {
      $query="INSERT INTO listaprecios VALUES($id_cliente,$ciudad_origen,$ciudad_destino,$id_embalaje,$precio,0,0,'$seguro')";
    }
    if ($debug == true) {
      echo "<p class='debug'>DEBUG <br />$query</p><hr />";
    } elseif($debug == false) {
      if (mysql_query($query)) {
        //echo "<p class='correcto'>Registro importado!</p>";
      } else {
        $n+=1;
        if (mysql_errno() == 1062) {
          echo "<p class='alerta'>
  (Linea $num_linea) Ya existe el registro:<br />
  $ciudad_origen, $ciudad_destino, $id_embalaje, $precio
</p>";
        } else {
          echo "<p class='error'>
  (Linea $num_linea) $ciudad_origen, $ciudad_destino, $id_embalaje, $precio<br />
  (Error) Los datos no coinciden con los esperados...</p>";
        }
        echo "<hr />";
      }

    }
  }
  if ($n == 0 and $debug == false)
    echo "<h2 class='correcto'>&iexcl;Lista de precios importada con exito!</h2>";
  else
    echo "<h2 class='alerta' style='font-size:13px !important;'>No se pudieron insertar $n registros.</h2>";
}
