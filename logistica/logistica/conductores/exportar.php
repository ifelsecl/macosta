<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CONDUCTORES_EXPORTAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (isset($_GET['token']) and $_GET['token'] == $_SESSION['token']) {
  $conductores = Conductor::all();
  if (isset($_GET['f']) and $_GET['f'] == 'XLS') {
    require_once Logistica::$root.'php/Excel.inc.php';
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=Conductores.xls");

    echo xlsBOF();
    echo xlsWriteLabel(0, 0, "Conductores ".date('Y'));
    echo xlsWriteLabel(2, 0, "Tipo Identificacion");
    echo xlsWriteLabel(2, 1, "Numero Identificacion");
    echo xlsWriteLabel(2, 2, "Nombre");
    echo xlsWriteLabel(2, 3, "Primer Apellido");
    echo xlsWriteLabel(2, 4, "Segundo Apellido");
    echo xlsWriteLabel(2, 5, "Ciudad");
    echo xlsWriteLabel(2, 6, "Direccion");
    echo xlsWriteLabel(2, 7, "Telefono");
    echo xlsWriteLabel(2, 8, "Categoria Licencia");
    echo xlsWriteLabel(2, 9, "Vencimiento Pase");
    echo xlsWriteLabel(2, 10, "Fecha Modificacion");
    echo xlsWriteLabel(2, 11, "Activo");
    $xlsRow = 3;
    foreach ($conductores as $c) {
      echo xlsWriteLabel($xlsRow, 0, $c->tipo_identificacion);
      echo xlsWriteNumber($xlsRow, 1, $c->numero_identificacion);
      echo xlsWriteLabel($xlsRow, 2, $c->nombre);
      echo xlsWriteLabel($xlsRow, 3, $c->primer_apellido);
      echo xlsWriteLabel($xlsRow, 4, $c->segundo_apellido);
      echo xlsWriteLabel($xlsRow, 5, $c->ciudad()->nombre);
      echo xlsWriteLabel($xlsRow, 6, $c->direccion);
      echo xlsWriteNumber($xlsRow, 7, $c->telefono);
      echo xlsWriteLabel($xlsRow, 8, $c->categorialicencia);
      echo xlsWriteLabel($xlsRow, 9, $c->vencimientopase);
      echo xlsWriteLabel($xlsRow, 10, $c->fechamodificacion);
      echo xlsWriteLabel($xlsRow, 11, $c->activo);
      $xlsRow++;
    }
    echo xlsEOF();
    exit;
  }
  if (isset($_GET['f']) and $_GET['f'] == 'CSV') {
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-type: text/comma-separated-values; charset=utf-8');
    header("Content-Disposition: attachment; filename=Conductores.csv");
    $sep = ";";
    foreach ($conductores as $c) {
      echo $l = '"'.$c->tipo_identificacion.'"'.$sep.$c->numero_identificacion.$sep.'"'.$c->nombre.'"'.$sep.'"'.$c->primer_apellido.'"'.$sep.'"'.$c->segundo_apellido.'"'.$sep.'"'.$c->idciudad.'"'.$sep.'"'.$c->categorialicencia.'"'.$sep.'"'.$c->direccion.'"'.$sep.'"'.$c->telefono.'"'.$sep.'"'.$c->vencimientopase.'"'.$sep.'"'.$c->activo.'"'."\r\n";
    }
    exit;
  }
}
if ((!isset($_GET['token']) or (isset($_GET['token']) and $_GET['token']!=$_SESSION['token'])) and isset($_GET['f'])){
  exit('<h3>Algo ha salido mal...</h3>');
}
?>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Conductores | Exportar</h2>
<table class="table">
  <tbody>
    <tr>
      <td width="32"><img src="img/xls.png" alt="Formato XLS" title="Formato XLS" /></td>
      <td><b>Formato XLS</b><br>Puedes exportar la lista de conductores en
        formato XLS de Microsoft Excel. Este formato no puede ser importado.
      </td>
      <td style="width:120px"><a class="btn btn-info" href="logistica/conductores/exportar.php?f=XLS&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
    </tr>
    <tr>
      <td width="32"><img src="img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
      <td><b>Formato CSV</b><br>Este formato puede ser importado, si
        quiere realizar una copia de seguridad use este formato.</td>
      <td style="width:120px"><a class="btn btn-info" href="logistica/conductores/exportar.php?f=CSV&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
    </tr>
  </tbody>
</table>
