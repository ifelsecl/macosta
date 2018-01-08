<?php
require '../../../class/Logistica.php';
Logistica::initialize();
if (!isset($_POST['id']) or !$_POST['id']) {
  echo "<h2>Algo ha salido mal, recarga la página e intentalo nuevamente.</h2>";
  exit;
}
if (!empty($_FILES)) {
  $tempFile = $_FILES['Filedata']['tmp_name'];
  if (!$lineas=file($tempFile)) {
    echo "<h2>No se ha podido cargar el archivo, intentalo nuevamente.</h2>";
    exit;
  }
  $id_cliente=$_POST['id'];
  ?>
  <style type="text/css">
  .error{margin:0;color:red;font-weight:bold;font-size:11px;}
  .correcto{margin:0;color:green;font-weight:bold;}
  .debug{margin:0;color:blue;font-size:11px;}
  .alerta{margin:0;color:orange;font-weight:bold;font-size:11px;}
  </style>
  <script type="text/javascript">
  $(".agregar").button({icons: {primary: "ui-icon-circle-plus"}, text: false});
  $(".agregar").click(function(event) {
    event.preventDefault();
    var o={'title':'Agregar Precio','position':'center','width':'auto','height':'auto'};
    $("#dialog").html('<p class="expand">Cargando...</p>').dialog("open").dialog("option", o).load("cliente_proveedor/clientes/listaprecios/agregar-dialogo.php?id=<?php echo $id_cliente;?>");
  });
  </script>
  <?php
  $objConf = new Configuracion;
  $objEmbalaje = new Embalaje;
  $calcular = $objConf->CalcularAutomaticamenteKiloyKiloVol();

  //DEBUG, true NO INSERTA REGISTROS
  $debug=FALSE;

  $n=0;
  $i=1;
  foreach ($lineas as $num_linea => $linea) {
    //if ($i>300) exit(); //Maximo 300 registros
    $array=explode(";", $linea);
    if (!isset($array[1])) {
      $array=explode(",", $linea);
    }

    if (empty($array[0])) {
      $ciudad_origen='-------';
    } else {
      $ciudad_origen=trim($array[0]);
    }

    if (isset($array[1]) and !empty($array[1])) {
      $ciudad_destino=trim($array[1]);
    } else {
      $ciudad_destino='-------';
    }
    if (isset($array[2]) and !empty($array[2])) {
      $id_embalaje=trim($array[2]);
    } else {
      $id_embalaje=1;
    }
    if (isset($array[3]) and !empty($array[3])) {
      $precio=trim($array[3]);
    } else {
      $precio=1;
    }
    if (isset($array[4]) and !empty($array[4])) {
      $seguro=trim($array[4]);
    } else {
      $seguro=0;
    }

    $id_ciudad_origen_defecto=08001000;//Barranquilla
    $id_ciudad_origen=$id_ciudad_origen_defecto;

    /* Buscar id ciudad origen */
    $query="SELECT  c.*, d.nombre departamento FROM ciudades c, departamentos d
WHERE c.iddepartamento=d.id AND d.id IN (8, 5, 13, 20, 23, 44, 47, 70) AND c.nombre LIKE '$ciudad_origen'";
    if (! $result = DBManager::execute($query)) {
      echo "<table><tr><td><b>(Linea $i)</b></td><td><b>Origen: </b>$ciudad_origen</td><td><b>Destino: </b>$ciudad_destino</td><td><b>ID Embalaje: </b>$id_embalaje</td><td><b>Precio: </b>$precio</td></tr></table>";
      echo "<p class'error'>Ha ocurrido un error con la consulta para buscar el codigo de la ciudad origen $ciudad_origen.</p>";
    } else {
      if (mysql_num_rows($result)>1) {
        echo "<table><tr><td><b>(Linea $i)</b></td><td><b>Origen: </b>$ciudad_origen</td><td><b>Destino: </b>$ciudad_destino</td><td><b>ID Embalaje: </b>$id_embalaje</td><td><b>Precio: </b>$precio</td></tr></table>";
        echo "<p class='alerta'>Coincidencias para la ciudad origen '$ciudad_origen'</p>";
        echo '<table border="1" cellspacing="0" cellpadding="0"><tr><th>Codigo</th><th>Poblacion</th><th>Municipio</th><th>Departamento</th></tr>';
        while ($row = mysql_fetch_assoc($result)) {
          echo '<tr>';
          echo '<td>'.$row['id'].'</td>';
          echo '<td>'.$row['nombre'].'</td>';
          echo '<td>'.$row['municipio'].'</td>';
          echo '<td>'.$row['departamento'].'</td>';
          echo '<td><button class="agregar">Agregar</button></td>';
          echo '</tr>';
        }
        echo '</table>';
      } elseif (mysql_num_rows($result) == 0) {
        echo "<table><tr><td><b>(Linea $i)</b></td><td><b>Origen: </b>$ciudad_origen</td><td><b>Destino: </b>$ciudad_destino</td><td><b>ID Embalaje: </b>$id_embalaje</td><td><b>Precio: </b>$precio</td></tr></table>";
        echo "<p class='alerta'>No hay coincidencias para la ciudad origen '$ciudad_origen'</p>";
        echo '<button class="agregar">Agregar</button>';
      } else {
        $row = mysql_fetch_assoc($result);
        $id_ciudad_origen=$row['id'];
      }
    }
    mysql_free_result($result);

    /* Buscar id ciudad destino */

    $id_ciudad_destino=0;
    $query = "SELECT  c.*,d.nombre departamento FROM ciudades c, departamentos d
WHERE c.iddepartamento=d.id AND d.id IN(8, 5, 13, 20, 23, 44, 47, 70) AND c.nombre LIKE '$ciudad_destino'";
    $result = DBManager::execute($query);
    $cantidad_resultados = mysql_num_rows($result);
    if ($cantidad_resultados > 1) {
      echo "<table><tr><td><b>(Linea $i)</b></td><td><b>Origen: </b>$ciudad_origen</td><td><b>Destino: </b>$ciudad_destino</td><td><b>ID Embalaje: </b>$id_embalaje</td><td><b>Precio: </b>$precio</td></tr></table>";
      echo "<p class='alerta'>$cantidad_resultados Coincidencias para la ciudad destino '$ciudad_destino'</p>";
      echo '<table border="1" cellspacing="0" cellpadding="0"><tr><th>Codigo</th><th>Poblacion</th><th>Municipio</th><th>Departamento</th><th>Agregar</th></tr>';
      while ($row = mysql_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['nombre'].'</td>';
        echo '<td>'.$row['municipio'].'</td>';
        echo '<td>'.$row['departamento'].'</td>';
        echo '<td align="center"><button class="agregar">Agregar</button></td>';
        echo '</tr>';
      }
      echo '</table>';
      if ($ciudad_destino=='MONTERIA') $id_ciudad_destino=23001000;
      if ($ciudad_destino=='SANTA MARTA') $id_ciudad_destino=47001000;
      if ($ciudad_destino=='ARJONA' and $precio<=15000) $id_ciudad_destino=13052000;
      if ($ciudad_destino=='OVEJAS') $id_ciudad_destino=70508000;
      if ($ciudad_destino=='SABANALARGA') $id_ciudad_destino=8638000;
      if ($ciudad_destino=='SAN JACINTO') $id_ciudad_destino=13654000;
      if ($ciudad_destino=='SAN PABLO') $id_ciudad_destino=13670000;
      if ($ciudad_destino=='SAN PEDRO') $id_ciudad_destino=70717000;
      if ($ciudad_destino=='ZAMBRANO') $id_ciudad_destino=13894000;
      if ($ciudad_destino=='TIERRALTA') $id_ciudad_destino=23807000;
      if ($ciudad_destino=='GRANADA') $id_ciudad_destino=47460000;
      if ($ciudad_destino=='ALBANIA') $id_ciudad_destino=44035000;
      if ($ciudad_destino=='EL BANCO') $id_ciudad_destino=47245000;
      if ($ciudad_destino=='HATO NUEVO') $id_ciudad_destino=0;
      if ($ciudad_destino=='SAN SEBASTIAN') $id_ciudad_destino=0;
      if ($ciudad_destino=='SEVILLA') $id_ciudad_destino=47980008;
      if ($ciudad_destino=='VILLANUEVA') $id_ciudad_destino=0;
      if ($ciudad_destino=='PUEBLO NUEVO') $id_ciudad_destino=0;
      if ($ciudad_destino=='EL CARMEN') $id_ciudad_destino=0;
      if ($ciudad_destino=='PUERTO COLOMBIA') $id_ciudad_destino=08573000;
      if ($ciudad_destino=='COVEÑAS') $id_ciudad_destino=70221000;
    } elseif ($cantidad_resultados == 0) {
      echo "<table><tr><td><b>(Linea $i)</b></td><td><b>Origen: </b>$ciudad_origen</td><td><b>Destino: </b>$ciudad_destino</td><td><b>ID Embalaje: </b>$id_embalaje</td><td><b>Precio: </b>$precio</td></tr></table>";
      echo "<p class='error'>No hay coincidencias para la ciudad destino '$ciudad_destino', deberas agregar este precio manualmente.</p>";
      echo '<button class="agregar">Agregar</button>';
      if ($ciudad_destino=='LORICA') $id_ciudad_destino=23417000;
      if ($ciudad_destino=='SAN JUAN NEPO') $id_ciudad_destino=13657000;
      if ($ciudad_destino=='COVEÑAS') $id_ciudad_destino=70221000;
      if ($ciudad_destino=='BANCO') $id_ciudad_destino=47245000;
      if ($ciudad_destino=='CIENEGA') $id_ciudad_destino=47189000;
      if ($ciudad_destino=='CODAZZI') $id_ciudad_destino=20013000;
      if ($ciudad_destino=='COPEY') $id_ciudad_destino=20238000;
      if ($ciudad_destino=='DIFICIL') $id_ciudad_destino=47058000;
      if ($ciudad_destino=='LA JAGUA DE IBI') $id_ciudad_destino=20400000;
      if ($ciudad_destino=='SAN JUAN DEL CE' or $ciudad_destino=='SAN JUAN DEL C') $id_ciudad_destino=44650000;
      if ($ciudad_destino=='CIENEGA DE ORO') $id_ciudad_destino=23189000;
      if ($ciudad_destino=='SANTAIGO DE TOLU') $id_ciudad_destino=70820000;
      if ($ciudad_destino=='PUEBLO NUEVO MAGDALENA') $id_ciudad_destino=70820000;
      if ($ciudad_destino=='ARJONA CESAR') $id_ciudad_destino=20032001;
      if ($ciudad_destino=='VILLANUEVA BOLIVAR') $id_ciudad_destino=13873000;
      if ($ciudad_destino=='STAMARTA') $id_ciudad_destino=47001000;
      if ($ciudad_destino=='GRAN VIA') $id_ciudad_destino=47980003;
      if ($ciudad_destino=='RETEN') $id_ciudad_destino=47268000;
      if ($ciudad_destino=='S/LARGA') $id_ciudad_destino=8638000;
      if ($ciudad_destino=='PTO C/BIA') $id_ciudad_destino=8638000;
    } else {
      //Si solo hay 1 ciudad que coincida con el nombre.
      $row = mysql_fetch_assoc($result);
      $id_ciudad_destino = $row['id'];
    }
    if ($id_ciudad_destino != 0 or ! empty($id_ciudad_destino)) {
      /* Insertar el registro */

      if ($calcular) {
        $precio_kilo = 0;
        if ($embalaje = Embalaje::find($id_embalaje)) {
          if ($embalaje->tipo_cobro == 'Caja') {
            $precio_kilo = round($precio/30);
          }
        }
      } else {
        $precio_kilo = 0;
      }
      $query="INSERT INTO listaprecios VALUES($id_cliente,$id_ciudad_origen,$id_ciudad_destino,$id_embalaje,$precio, $precio_kilo, $precio_kilo,'$seguro')";
      if ($debug) {
        echo "<p class='debug'>DEBUG (Ok)<br />$query</p>";
      } else {
        if (! DBManager::execute($query)) {
          $n += 1;
          if (mysql_errno() == 1062) {
            echo "<p class='alerta'>Se ha intentado ingresar, pero ya existe el registro.</p>";
          } else {
            echo "<p class='error'>Este precio no pudo ser insertado, agregalo manualmente...</p>";
          }
          echo "<hr />";
        }
      }
    }
    $i += 1;
  }
  if ($n == 0 and !$debug)
    echo "<h2 class='correcto'>&iexcl;Lista de precios importada con exito!</h2>";
  elseif (!$debug)
    echo "<h2>No se pudieron insertar <b>$n</b> registros.</h2>";
  else echo '<h2>Modo <b>DEBUG</b> activado, ningun registro fue insertado.</h2>';
}
