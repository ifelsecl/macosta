<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CLIENTES_EXPORTAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$cliente = new Cliente;

if (isset($_GET['token']) and $_GET['token'] == $_SESSION['token']) {
  if (isset($_GET['f']) and $_GET['f'] == 'XLS') {
    require_once Logistica::$root."php/Excel.inc.php";
    $nombre = 'Clientes_'.strftime("%B_%Y").'.xls';
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filname=$nombre");
    echo xlsBOF();
    echo xlsWriteLabel(0, 0, "Clientes ".date("Y"));
    echo xlsWriteLabel(2, 0, "ID");
    echo xlsWriteLabel(2, 1, "Tipo Identificacion");
    echo xlsWriteLabel(2, 2, "Numero Identificacion");
    echo xlsWriteLabel(2, 3, "Nombre");
    echo xlsWriteLabel(2, 4, "Ciudad");
    echo xlsWriteLabel(2, 5, "Departamento");
    echo xlsWriteLabel(2, 6, "Direccion");
    echo xlsWriteLabel(2, 7, "Telefono");
    echo xlsWriteLabel(2, 8, "Correo electronico");
    echo xlsWriteLabel(2, 9, "Sitio web");
    echo xlsWriteLabel(2, 10, "Forma Juridica");
    echo xlsWriteLabel(2, 11, "Regimen");
    echo xlsWriteLabel(2, 12, "Restr. Peso");
    echo xlsWriteLabel(2, 13, "Seguro (%)");
    echo xlsWriteLabel(2, 14, "Descuento (%)");
    echo xlsWriteLabel(2, 15, "Fecha modificacion");
    echo xlsWriteLabel(2, 16, "activo");
    $xlsRow = 3;
    $result = $cliente->Exportar("XLS");
    while($cliente = mysql_fetch_array($result)) {
      echo xlsWriteNumber($xlsRow, 0, $cliente['id']);
      $ni = $cliente['numero_identificacion'];
      if ($cliente['tipo_identificacion'] == 'N') {
        $ni .= '-'.$cliente['digito_verificacion'];
      }
      echo xlsWriteLabel($xlsRow, 1, $cliente['tipo_identificacion']);
      echo xlsWriteNumber($xlsRow, 2, $ni);
      echo xlsWriteLabel($xlsRow, 3, trim($cliente['nombre'].' '.$cliente['primer_apellido'].' '.$cliente['segundo_apellido']));
      echo xlsWriteLabel($xlsRow, 4, $cliente['nombreciudad']);
      echo xlsWriteLabel($xlsRow, 5, $cliente['nombredepartamento']);
      echo xlsWriteLabel($xlsRow, 6, $cliente['direccion']);
      echo xlsWriteLabel($xlsRow, 7, $cliente['telefono']);
      echo xlsWriteLabel($xlsRow, 8, $cliente['email']);
      echo xlsWriteLabel($xlsRow, 9, $cliente['sitioweb']);
      echo xlsWriteLabel($xlsRow, 10, $cliente['formajuridica']);
      echo xlsWriteLabel($xlsRow, 11, $cliente['regimen']);
      echo xlsWriteNumber($xlsRow, 12, $cliente['restriccionpeso']);
      echo xlsWriteNumber($xlsRow, 13, $cliente['porcentajeseguro']);
      echo xlsWriteNumber($xlsRow, 14, $cliente['descuento']);
      echo xlsWriteLabel($xlsRow, 15, $cliente['fechamodificacion']);
      echo xlsWriteLabel($xlsRow, 16, $cliente['activo']);
      $xlsRow++;
    }
    echo xlsEOF();
    exit;
  }
  if (isset($_GET['f']) and $_GET['f'] == 'CSV') {
    $nombre='Clientes_'.strftime('%B_%Y').'.csv';
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-type: text/comma-separated-values; charset=UTF-8');
    header("Content-Disposition: attachment; filename=$nombre");
    $result = $cliente->Exportar("CSV");
    $sep = ",";
    while($row = mysql_fetch_assoc($result)) {
      $linea = '';
      foreach ($row as $key => $value) {
        if ($key != 'id')
          $linea .= $value.$sep;
      }
      $linea = substr($linea,0,-1);
      $linea .= "\r\n";
      echo $linea;
    }
    exit;
  }
  if (isset($_GET['f']) and $_GET['f'] == 'CSV2') {
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-type: text/comma-separated-values; charset=UTF-8');
    header("Content-Disposition: attachment; filename=ListaPrecios.csv");
    $result = $cliente->ExportarListaPrecios(null,"CSV");
    $sep = ",";
    while($row = mysql_fetch_assoc($result)) {
      $l = "";
      foreach ($row as $key => $value) {
        $l .= $value.$sep;
      }
      $l = substr($l,0,-1);
      $l .= "\r\n";
      echo $l;
    }
    exit;
  }
}
if ((! isset($_GET['token']) or (isset($_GET['token']) and $_GET['token'] != $_SESSION['token'])) and isset($_GET['f'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
?>
<script>
$('#regresar').click(function() {
  regresar();
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Clientes | Exportar</h2>
<table class="table">
  <tbody>
    <tr>
      <td width="32"><img src="img/xls.png" alt="Formato XLS" title="Formato XLS" /></td>
      <td><b>Formato XLS</b><br> Puedes exportar la lista de clientes en
        formato XLS de Microsoft Excel. Este formato no puede ser importado.
      </td>
      <td style="width: 110px"><a class="btn btn-info" href="cliente_proveedor/clientes/exportar.php?f=XLS&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
    </tr>
    <tr>
      <td width="32"><img src="img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
      <td><b>Formato CSV</b><br> Este formato puede ser importado, si
        quiere realizar una copia de seguridad use este formato.</td>
      <td><a class="btn btn-info" href="cliente_proveedor/clientes/exportar.php?f=CSV&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
    </tr>
  </tbody>
</table>
<br>
<h3>Exportar lista de precios de todos los clientes</h3>
<table class="table">
  <tr>
    <td width="32"><img src="img/csv.png" alt="Formato CSV" title="Formato CSV" /></td>
      <td><b>Formato CSV</b><br> Este formato puede ser importado, si
        quiere realizar una copia de seguridad use este formato.</td>
      <td style="width: 110px"><a class="btn btn-info" href="cliente_proveedor/clientes/exportar.php?f=CSV2&token=<?= $_SESSION['token'] ?>"><i class="icon-download-alt"></i> Exportar</a></td>
  </tr>
</table>
