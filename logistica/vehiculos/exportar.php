<?php
$raiz = "../..";
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CAMIONES_EXPORTAR])) {
  include $raiz."/mensajes/permiso.php";
  exit;
}

if (isset($_GET['token'])) {
  $vehiculos = Vehiculo::all();

  if (isset($_GET['f']) and $_GET['f']=='XLS') {
    require_once $raiz."/php/Excel.inc.php";
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=Vehiculos.xls");
    echo xlsBOF();
    echo xlsWriteLabel(2,  0, 'ID');
    echo xlsWriteLabel(2,  1, "Grupo");
    echo xlsWriteLabel(2,  2, "ID");
    echo xlsWriteLabel(2,  3, "Clase");
    echo xlsWriteLabel(2,  4, "ID");
    echo xlsWriteLabel(2,  5, "Marca");
    echo xlsWriteLabel(2,  6, "ID");
    echo xlsWriteLabel(2,  7, "Linea");
    echo xlsWriteLabel(2,  8, "ID");
    echo xlsWriteLabel(2,  9, "Designacion");
    echo xlsWriteLabel(2, 10, "ID");
    echo xlsWriteLabel(2, 11, "Color");
    echo xlsWriteLabel(2, 12, "Modelo");
    echo xlsWriteLabel(2, 13, "Placa");
    echo xlsWriteLabel(2, 14, "Fecha Matricula");
    echo xlsWriteLabel(2, 15, "VIN o Serial");
    echo xlsWriteLabel(2, 16, "Serie");
    echo xlsWriteLabel(2, 17, "Nro. Chasis");
    echo xlsWriteLabel(2, 18, "Nro. Motor");
    echo xlsWriteLabel(2, 19, "ID");
    echo xlsWriteLabel(2, 20, "Combustible");
    echo xlsWriteLabel(2, 21, "Capacidad de carga (pasajeros)");
    echo xlsWriteLabel(2, 22, "Capacidad de carga (toneladas)");
    echo xlsWriteLabel(2, 23, "Nro Licencia Transito");
    echo xlsWriteLabel(2, 24, "Nro Tarjeta Operacion");
    echo xlsWriteLabel(2, 25, "Fecha Expedicion");
    echo xlsWriteLabel(2, 26, "Fecha de vencimiento");
    echo xlsWriteLabel(2, 27, "ID");
    echo xlsWriteLabel(2, 28, "Estado");
    echo xlsWriteLabel(2, 29, "ID");
    echo xlsWriteLabel(2, 30, "Tipo de propiedad");
    echo xlsWriteLabel(2, 31, "ID");
    echo xlsWriteLabel(2, 32, "Tipo de documento");
    echo xlsWriteLabel(2, 33, "Numero identificacion");
    echo xlsWriteLabel(2, 34, "Nombre del propietario");
    echo xlsWriteLabel(2, 35, "Apellido del propietario");
    echo xlsWriteLabel(2, 36, "ID");
    echo xlsWriteLabel(2, 37, "Sexo");
    echo xlsWriteLabel(2, 38, "Numero identificacion");
    echo xlsWriteLabel(2, 39, "Razon social");
    echo xlsWriteLabel(2, 40, "Objeto social");
    echo xlsWriteLabel(2, 41, "Nro. Contrato");
    echo xlsWriteLabel(2, 42, "Fecha inicio");
    echo xlsWriteLabel(2, 43, "Fecha vencimiento");
    echo xlsWriteLabel(2, 44, "Numero de poliza");
    echo xlsWriteLabel(2, 45, "Fecha de expedicion");
    echo xlsWriteLabel(2, 46, "Vigencia desde");
    echo xlsWriteLabel(2, 47, "Vigencia hasta");
    echo xlsWriteLabel(2, 48, "ID");
    echo xlsWriteLabel(2, 49, "Entidad expide");
    echo xlsWriteLabel(2, 50, "Nro Ficha homologacion");
    echo xlsWriteLabel(2, 51, "ID");
    echo xlsWriteLabel(2, 52, "Activo");
    $xlsRow = 3;
    foreach ($vehiculos as $v) {
      echo xlsWriteLabel($xlsRow,  0, '4');
      echo xlsWriteLabel($xlsRow,  1, "Otros");
      echo xlsWriteLabel($xlsRow,  2, "8");
      echo xlsWriteLabel($xlsRow,  3, "CAMION");
      echo xlsWriteLabel($xlsRow,  4, $v->codigo_Marcas);
      echo xlsWriteLabel($xlsRow,  5, strtoupper($v->marca_nombre));
      echo xlsWriteLabel($xlsRow,  6, $v->codigo_linea);
      echo xlsWriteLabel($xlsRow,  7, strtoupper($v->linea_nombre));
      echo xlsWriteLabel($xlsRow,  8, $v->idconfiguracion);
      echo xlsWriteLabel($xlsRow,  9, $v->configuracion_nombre);
      echo xlsWriteLabel($xlsRow, 10 , $v->codigo_colores);
      echo xlsWriteLabel($xlsRow, 11, strtoupper($v->color_nombre));
      echo xlsWriteLabel($xlsRow, 12, $v->modelo);
      echo xlsWriteLabel($xlsRow, 13, $v->placa);
      echo xlsWriteLabel($xlsRow, 14, $v->fecha_matricula);
      echo xlsWriteLabel($xlsRow, 15, $v->serie);
      echo xlsWriteLabel($xlsRow, 16, $v->serie);
      echo xlsWriteLabel($xlsRow, 17, $v->numero_chasis);
      echo xlsWriteLabel($xlsRow, 18, $v->numero_motor);
      echo xlsWriteLabel($xlsRow, 19, '3');
      echo xlsWriteLabel($xlsRow, 20, "Diesel");
      echo xlsWriteLabel($xlsRow, 21, '3');
      echo xlsWriteLabel($xlsRow, 22, ($v->capacidadcarga / 1000));
      echo xlsWriteLabel($xlsRow, 23, $v->numero_licencia_transito);
      echo xlsWriteLabel($xlsRow, 24, $v->numero_tarjeta_operacion());
      echo xlsWriteLabel($xlsRow, 25, $v->fecha_afiliacion);
      echo xlsWriteLabel($xlsRow, 26, $v->f_venc_toperacion);
      echo xlsWriteLabel($xlsRow, 27, 'V');
      echo xlsWriteLabel($xlsRow, 28, 'Vinculado');
      echo xlsWriteLabel($xlsRow, 29, '2');
      echo xlsWriteLabel($xlsRow, 30, 'Propio');
      $types = array('C' => '1', 'N' => '2');
      echo xlsWriteLabel($xlsRow, 31, $types[$v->propietario_tipo_identificacion]);
      $names = array('C' => 'Cedula', 'N' => 'Nit');
      echo xlsWriteLabel($xlsRow, 32, $names[$v->propietario_tipo_identificacion]);
      if ($v->propietario_tipo_identificacion == 'C') {
        echo xlsWriteLabel($xlsRow, 33, $v->propietario_numero_identificacion);
        echo xlsWriteLabel($xlsRow, 34, $v->propietario_nombre);
        echo xlsWriteLabel($xlsRow, 35, $v->propietario_primer_apellido);
        echo xlsWriteLabel($xlsRow, 36, '');
        echo xlsWriteLabel($xlsRow, 37, '');
        echo xlsWriteLabel($xlsRow, 38, '');
        echo xlsWriteLabel($xlsRow, 39, '');
        echo xlsWriteLabel($xlsRow, 40, '');
      } else {
        echo xlsWriteLabel($xlsRow, 33, '');
        echo xlsWriteLabel($xlsRow, 34, '');
        echo xlsWriteLabel($xlsRow, 35, '');
        echo xlsWriteLabel($xlsRow, 36, '');
        echo xlsWriteLabel($xlsRow, 37, '');
        echo xlsWriteLabel($xlsRow, 38, $v->propietario_numero_identificacion);
        echo xlsWriteLabel($xlsRow, 39, $v->propietario_razon_social);
        echo xlsWriteLabel($xlsRow, 40, 'TRANSPORTE DE CARGA POR CARRETERA');
      }
      echo xlsWriteLabel($xlsRow, 41, '');
      echo xlsWriteLabel($xlsRow, 42, '');
      echo xlsWriteLabel($xlsRow, 43, '');
      echo xlsWriteLabel($xlsRow, 44, $v->soat);
      echo xlsWriteLabel($xlsRow, 45, $v->fecha_expedicion_soat);
      echo xlsWriteLabel($xlsRow, 46, $v->fecha_expedicion_soat);
      echo xlsWriteLabel($xlsRow, 47, $v->f_venc_soat);
      echo xlsWriteLabel($xlsRow, 48, '');
      echo xlsWriteLabel($xlsRow, 49, utf8_decode($v->aseguradora_nombre));
      echo xlsWriteLabel($xlsRow, 50, $v->numero_ficha_homologacion);
      echo xlsWriteLabel($xlsRow, 51, 'S');
      echo xlsWriteLabel($xlsRow, 52, "Si");
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
    header("Content-Disposition: attachment; filename=Camiones.csv");
    require_once $raiz."/class/camiones.class.php";
    $camion = new Camiones;
    $result=$camion->Exportar("CSV");
    $sep = ",";
    while ($row = mysql_fetch_assoc($result)) {
      $l = "";
      foreach ($row as $key => $value) {
        $l .= '"'.$value.'"'.$sep;
      }
      $l .= "\r\n";
      echo $l;
    }
    exit;
  }
}
?>
<button onclick="regresar()" class="btn btn-success pull-right">Regresar</button>
<h2>Vehículos | Exportar</h2>
<table class="table">
  <tbody>
    <tr>
      <td width="32"><img src="img/xls.png" alt="Formato XLS" title="Formato XLS" /></td>
      <td><b>Formato XLS</b><br>Puedes exportar la lista de camiones en
        formato XLS de Microsoft Excel. Este formato no puede ser importado.
      </td>
      <td><a class="btn btn-info" href="logistica/vehiculos/exportar.php?f=XLS&token=<?= $_SESSION['token'] ?>"
        id="exportarXLS"><i class="icon-download-alt"></i> Exportar</a></td>
    </tr>
    <tr>
      <td width="32"><img src="img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
      <td><b>Formato CSV</b><br>Este formato puede ser importado, si
        quiere realizar una copia de seguridad use este formato.</td>
      <td><a class="btn btn-info" id="exportarCSV" href="logistica/vehiculos/exportar.php?f=CSV&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
    </tr>
  </tbody>
</table>
