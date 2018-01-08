<?php
require '../../seguridad.php';

if (isset($_POST['search_linea'])) {
  $html = "<option></option>";
  foreach (Vehiculo::all_lineas_by_marca($_POST['idmarca']) as $linea) {
    $html .= '<option value="'.$linea->codigo.'">'.$linea->descripcion.'</option>';
  }
  echo $html;
  exit;
}
if (isset($_POST['guardar'])) {
  $placa                = strtoupper($_POST['placa']);
  $marca                = $_POST['marca'];
  $linea                = $_POST['linea'];
  $modelo               = $_POST['modelo'];
  $serie                = $_POST['serie'];
  $color                = $_POST['color'];
  $carroceria           = $_POST['carroceria'];
  $configuracion        = $_POST['configuracion'];
  $peso                 = $_POST['peso'];
  $registro             = $_POST['registro'];
  $aseguradora          = $_POST['aseguradora'];
  $capacidadcarga       = $_POST['capacidad_carga'];
  $id_propietario       = $_POST['id_propietario'];
  $soat                 = $_POST['soat'];
  $fechasoat            = $_POST['f_venc_soat'];
  $seguro               = $_POST['num_seguro'];
  $fechaseguro          = $_POST['f_venc_seguro'];
  $tarjetaoperacion     = $_POST['t_operacion'];
  $fechatarjeta         = $_POST['f_venc_toperacion'];
  $tecnicomecanica      = $_POST['tecnico_meca'];
  $fechatecnicomecanica = $_POST['f_venc_tmec'];
  $modelo_reptenciado   = $_POST['modelo_repotenciado'];
  $placa_semiremolque   = $_POST['placa_semiremolque'];
  $id_tenedor           = $_POST['id_tenedor'];
  $km_inicial           = $_POST['km_inicial'];
  $km_actual            = $_POST['km_actual'];
  $numero_ejes          = $_POST['numero_ejes'];
  $tipo_combustible     = $_POST['tipo_combustible'];
  $fecha_afiliacion     = $_POST['fecha_afiliacion'];
  require_once Logistica::$root.'class/camiones.class.php';
  $camion = new Camiones;
  if ($camion->Existe($placa)) {
    echo "<table><tr>";
    echo "<td><img src='css/images/alert.png' alt='alert'/></td>";
    echo "<td>Existe un camión con la placa <b>$placa</b>.<br />Por favor, verifica y escribela nuevamente.</td>";
    echo "</tr></table>";
  } else {
    if ($camion->Agregar($placa, $marca, $linea, $modelo, $serie, $color, $carroceria, $configuracion, $peso, $registro, $aseguradora, $capacidadcarga, $id_propietario, $soat, $fechasoat, $seguro, $fechaseguro, $tarjetaoperacion, $fechatarjeta, $tecnicomecanica, $fechatecnicomecanica,$modelo_reptenciado,$placa_semiremolque,$id_tenedor, $km_inicial, $km_actual, $numero_ejes, $tipo_combustible, $fecha_afiliacion)) {
      Logger::vehiculo($placa, 'creó el vehiculo');
      echo "ok";
    } else {
      include Logistica::$root.'mensajes/guardando_error.php';
    }
  }
  exit;
}
if (isset($_POST['editar'])) {
  if (! isset($_POST['placa'])) {
    include Logistica::$root."mensajes/id.php";
    exit;
  }
  if (! $vehiculo = Vehiculo::find($_POST['placa']) ) exit('No existe el vehiculo');
  $changes = $vehiculo->updated_attributes($_POST);
  if ($changes) {
    if ($vehiculo->update($_POST)) {
      Logger::vehiculo($vehiculo->placa, 'editó el vehículo'.$changes);
    } else {
      include Logistica::$root.'mensajes/guardando_error.php';
    }
  } else {
    echo 'No hay cambios para actualizar';
  }
  exit;
}
