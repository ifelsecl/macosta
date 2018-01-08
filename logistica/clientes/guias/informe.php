<?php
$raiz='../../';
require_once '../seguridad.php';

if(isset($_REQUEST['crear_informe'])){
  require_once $raiz."php/Excel.inc.php";
  if(!isset($_REQUEST['estados'])){
    exit('<h1>Selecciona por lo menos un estado.</h1>');
  }
  $fecha_inicio = $_REQUEST['inicio'];
  $fecha_fin = $_REQUEST['fin'];
  $guias = Guia::informe_cliente($_SESSION['id'], $_REQUEST['estados'], $fecha_inicio, $fecha_fin);
  if(!empty($guias)){
    $f = date('Y.m.d');
    if(empty($fecha_inicio) and empty($fecha_fin)){
      $nombre = "Informe_Guias_$f.xls";
      $titulo = 'Informe de Guias';
    }else{
      if(!empty($fecha_fin) and !empty($fecha_inicio)){
        $nombre = "Informe_Guias_".$fecha_inicio."__".$fecha_fin.".xls";
        $titulo = "Informe de Guias Entre $fecha_inicio - $fecha_fin";
      }elseif(empty($fecha_fin) and !empty($fecha_inicio)){
        $nombre = "Informe_Guias_desde_$fecha_inicio.xls";
        $titulo = "Informe de Guias desde $fecha_inicio";
      }else{
        $nombre = "Informe_Guias_hasta_$fecha_fin.xls";
        $titulo = "Informe de Guias hasta $fecha_fin";
      }
    }
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-type: application/vnd.ms-excel; charset=utf-8');
    header("Content-Disposition: attachment; filename=$nombre");

    echo xlsBOF();//inicio de archivo

    echo xlsWriteLabel(0, 1, 'TRANSPORTES MARIO ACOSTA & CIA LTDA');
    echo xlsWriteLabel(1, 1, $titulo);
    echo xlsWriteLabel(2, 1, 'Generado '.date('d/m/Y g:i:s A'));

    echo xlsWriteLabel(4, 0, "Numero");
    echo xlsWriteLabel(4, 1, "Fecha");
    echo xlsWriteLabel(4, 2, "Destino");
    echo xlsWriteLabel(4, 3, "Destinatario");
    echo xlsWriteLabel(4, 4, "Estado");
    echo xlsWriteLabel(4, 5, "Forma de Pago");
    echo xlsWriteLabel(4, 6, "Documento Cliente");
    echo xlsWriteLabel(4, 7, "Valor Declarado");
    echo xlsWriteLabel(4, 8, "Valor Seguro");
    echo xlsWriteLabel(4, 9, "Total");
    echo xlsWriteLabel(4, 10, "Observaciones");
    $i = 5;
    foreach($guias as $guia){
      echo xlsWriteNumber($i, 0, $guia->id);
      echo xlsWriteLabel($i, 1, strftime('%d/%b/%Y', strtotime($guia->fecha_recibido_mercancia)));
      echo xlsWriteLabel($i, 2, utf8_decode($guia->contacto()->ciudad_nombre));
      echo xlsWriteLabel($i, 3, utf8_decode($guia->contacto->nombre_completo));
      echo xlsWriteLabel($i, 4, $guia->estado());
      echo xlsWriteLabel($i, 5, $guia->formapago);
      echo xlsWriteLabel($i, 6, $guia->documentocliente);
      echo xlsWriteNumber($i, 7, round($guia->valordeclarado));
      echo xlsWriteNumber($i, 8, round($guia->valorseguro));
      echo xlsWriteNumber($i, 9, round($guia->total+$guia->valorseguro));
      echo xlsWriteLabel($i, 10, utf8_decode($guia->observacion));
      $i++;
    }
    echo xlsEOF();//fin de archivo
    $logger = new Logger;
    $ip = $_SESSION['ip'];
    $id_cliente = $_SESSION['id'];
    $logger->LogCliente('Creó un informe: '.$titulo, 'Guias', $id_cliente);
  }else{
    echo '<title>Informe de guias</title><h2>No se encontraron guías...</h2>';
  }
  exit;
}
?>
<script>
$(function(){
  var dates = $("#inf_fecha_inicio, #inf_fecha_fin").datepicker({
    changeMonth: true,
    changeYear: true,
    numberOfMonths: 2,
    showOn: "both",
    maxDate: '0',
        buttonImage: "../css/images/calendar.gif",
        buttonImageOnly: true,
    dateFormat: 'yy-mm-dd',
        buttonText: 'Seleccionar...',
        autoSize: true,
    onSelect: function(selectedDate) {
      var option = this.id == "inf_fecha_inicio" ? "minDate" : "maxDate";
      dates.not(this).datepicker("option", option, selectedDate);
    }
  });
});
</script>
<p class="muted">Genere un informe detallado de guías por rango de fechas y estados.</p>
<form method="post" id="FormInforme" target="_blank" action="guias/informe">
  <input type="hidden" name="crear_informe" value="si" />
  <table>
    <tr>
      <td>
        <fieldset>
          <legend>Fecha</legend>
          <table style="font-size: 14px;">
            <tr>
              <td>Inicio:</td>
              <td>
                <input type="text" class="input-small" name="inicio" id="inf_fecha_inicio" />
              </td>
            </tr>
            <tr>
              <td>Fin:</td>
              <td>
                <input type="text" class="input-small" name="fin" id="inf_fecha_fin" />
              </td>
            </tr>
          </table>
          <p style="text-align:left;font-size: 12px;">Puede seleccionar varias opciones:
            <ul style="margin-left:7px;text-align:left;font-size: 12px;">
              <li>Rango completo: se buscaran las guias entre el rango indicado.</li>
              <li>Fecha inicial: se buscaran las guias a partir de la fecha indicada.</li>
              <li>Fecha final: se buscaran las guias hasta la fecha indicada.</li>
              <li>Sin fecha: se buscaran todas las guias.</li>
            </ul>
          </p>
        </fieldset>
      </td>
      <td style="vertical-align: top">
        <fieldset>
          <legend>Estados</legend>
          <table>
          <?php
          foreach (Guia::$estados as $key => $value) {
            if($key != 3 and $key != 6){
              echo '<tr>';
              echo '<td><label for="estado_'.$key.'"><input id="estado_'.$key.'" type="checkbox" name="estados[]" value="'.$key.'" />';
              echo ' '.$value.'</label></td>';
              echo '</tr>';
            }
          }
          ?>
          </table>
        </fieldset>
      </td>
    </tr>
  </table>
  <center>
    <button class="btn btn-info"><i class="icon-file"></i> Crear Informe</button>
  </center>
</form>
