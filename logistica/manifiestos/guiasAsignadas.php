<?php
/**
 * Permite ver las guias asignadas a una planilla
 * Este archivo es llamado en Editar.php
 */
require "../../seguridad.php";

require_once Logistica::$root."class/guias.class.php";
$objGuia = new Guias;
$result = $objGuia->ObtenerGuiasEnPlanilla($_GET['id']);
$cantidad = DBManager::rows_count($result);
$configuracion = new Configuracion;
?>
<style type="text/css">
.ui-state-highlight{height: 20px;line-height: 20px;}
</style>
<script>
$(function() {
  $('.quitar').click(function(event) {
    event.preventDefault();
    $('#cargando3').fadeIn();
    $.ajax({
      url: manifiestos_path+'quitarGuia.php',
      data: this.name,
      dataType: 'json',
      success: function(response) {
        $('#cargando3').fadeOut(600);
        if (response.success) {
          $('#cargando2').fadeIn(100);
          $('#GuiasAsignadas').load(manifiestos_path+'guiasAsignadas.php?id='+$('#manifiesto__edit__id').val());
          if ($('#termino').val()) {
            $('#GuiasEncontradas').load(manifiestos_path+'guiasEncontradasE.php?'+$('#formbuscar').serialize()+'&idplanilla='+$('#manifiesto__edit__id').val(), function() {
              $('#cargando2').fadeOut(600);
            });
          }
        } else {
          alertify.error(response.message);
        }
      }
    });
  });
  $('.ordenable').sortable({
    hoverClass: "ui-state-hover",
    placeholder: "ui-state-highlight",
    opacity: 0.8,
    forceHelperSize: true,
    forcePlaceholderSize: true,
    revert: 200,
    update: function(event, ui) {
      $.ajax({
        url: manifiestos_path+'ajax.php',
        type: 'POST',
        data: 'ActualizarPosiciones=1&'+$(this).sortable("serialize"),
        success: function() {}
      });
    }
  });
});
</script>
<table>
  <tr>
    <td>Guías asignadas: <?= $cantidad ?><input type="hidden" id="cantidad" value="<?= $cantidad ?>" /></td>
    <td><div id="cargando3" style="display: none;"><img src="css/ajax-loader.gif" alt="cargando" /></div></td>
    <td><span class="muted">Recuerda que puedes arrastrar las filas para ordenarlas.</span></td>
  </tr>
</table>
<table class="table table-hover table-condensed table-bordered">
  <thead>
    <tr>
      <th>No.</th>
      <th>Remitente</th>
      <th>Destinatario</th>
      <th>Destino</th>
      <th>Mercancía Recibida</th>
      <th width="16"></th>
    </tr>
  </thead>
  <tbody <?php if ($cantidad>0) echo 'class="ordenable" style="cursor: pointer;"' ?>>
    <?php
    $total      = 0;
    $total_contado  = 0;
    $total_flete  = 0;
    $total_credito  = 0;
    $peso       = 0;
    $vol      = 0;
    if ($cantidad>0) {
      while ( $guia = mysql_fetch_array($result)) {
        echo '<tr id="idguia_'.$guia['id'].'">';
        echo '<td>'.$guia['id'].'</td>';
        $cliente = trim($guia['cliente_no'].' '.$guia['cliente_pa']);
        $contacto = trim($guia['contacto_nombre'].' '.$guia['contacto_primer_apellido'].' '.$guia['contacto_segundo_apellido']);
        echo '<td title="'.$cliente.'">'.substr($cliente, 0, 22).'</td>';
        if (strlen($contacto)>22) $str = substr($contacto, 0, 20).'...';
        else $str = $contacto;
        echo '<td title="'.$contacto.'">'.$str.'</td>';
        echo '<td>'.$guia['ciudaddestino'].'</td>';
        echo '<td title="'.htmlentities(strftime("%A, %d de %B de %Y", strtotime($guia['fecha_recibido_mercancia']))).'">'.$guia['fecha_recibido_mercancia'].'</td>';
        echo '<td><a class="btn quitar btn-danger btn-mini" title="Quitar" name="idguia='.$guia['id'].'&idplanilla='.$_GET['id'].'" href="#"><i class="icon-remove"></i></a></td>';
        echo '</tr>';
        if ($guia['formapago'] == 'FLETE AL COBRO') {
          $total_flete+=$guia['total']+$guia['valorseguro'];
        } elseif ($guia['formapago']=='CONTADO') {
          $total_contado+=$guia['total']+$guia['valorseguro'];
        } else {
          $total_credito+=$guia['total']+$guia['valorseguro'];
        }
        $total += $guia['total']+$guia['valorseguro'];
        $peso += $guia['peso'];
        $vol += $guia['kilo_vol'] / $configuracion->calKiloVolumen;
      }
    } else {
      echo '<tr><td colspan="6" class="expand">El manifiesto no tiene guías asignadas...</td></tr>';
    }
    ?>
  </tbody>
</table>
<div class="row-fluid">
  <fieldset class="span5 table">
    <legend>Mercancía</legend>
    <table>
      <tr>
        <td><b>Peso:</b></td>
        <td align="right"><?= number_format($peso, 2) ?> Kg</td>
      </tr>
      <tr>
        <td><b>Volúmen:</b></td>
        <td align="right"><?= round($vol, 3) ?> m<sup>3</sup></td>
      </tr>
    </table>
  </fieldset>
  <fieldset class="span5 offset2 table">
    <legend>Flete</legend>
    <table>
      <tr>
        <td><b>Flete al Cobro:</b></td>
        <td align="right"><?= number_format($total_flete) ?></td>
      </tr>
      <tr>
        <td><b>Contado:</b></td>
        <td align="right"><?= number_format($total_contado) ?></td>
      </tr>
      <tr>
        <td><b>Credito:</b></td>
        <td align="right"><?= number_format($total_credito) ?></td>
      </tr>
      <tr class="info">
        <td style="border-top: 1px solid black;"><b>Total:</b></td>
        <td style="border-top: 1px solid black;" align="right"><?= number_format($total) ?></td>
      </tr>
    </table>
  </fieldset>
</div>
