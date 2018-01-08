<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PLANILLAS_VER])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (! isset($_GET['idplanilla']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['idplanilla'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
$configuracion = new Configuracion;
if (! $manifiesto = Manifiesto::find($_GET['idplanilla'])) exit('No existe el Manifiesto');
?>
<div class="pull-right btn-toolbar">
  <div class="btn-group">
    <a id="imprimir" title="Imprimir" href="logistica/manifiestos/imprimir2?idplanilla=<?= $manifiesto->id .'&'. nonce_create_query_string($manifiesto->id) ?>" target="_blank" class="btn"><i class="icon-print"></i></a>
    <button id="entregadas" title="Marcar las guias de este manifiesto como entregadas" class="btn btn-error"><i class="icon-ok-sign"></i> Marcar como Entregadas</button>
    <button title"Regresar" class="btn btn-success" onclick="regresar()">Regresar</button>
  </div>
</div>
<h2>Manifiesto <?= $manifiesto->id ?></h2>
<?php if ($manifiesto->activa == 'no') { ?>
<div class="alert alert-error"><strong>MANIFIESTO ANULADO</strong></div>
<?php } ?>
<table class="table">
  <tr>
    <td>MANIFIESTO DE CARGA</td>
    <td><b>CONDUCTOR</b>: <?= $manifiesto->conductor()->nombre_completo ?></td>
    <td><b>VEHICULO</b>: <?= $manifiesto->placacamion ?></td>
  </tr>
  <tr>
    <td><b>FECHA</b>: <?= strftime('%B %d, %Y',strtotime($manifiesto->fecha)) ?></td>
    <td><b>CIUDAD ORIGEN</b>: <?= $manifiesto->ciudad_origen()->nombre ?></td>
    <td><b>CIUDAD DESTINO</b>: <?= $manifiesto->ciudad_destino()->nombre ?></td>
  </tr>
  <tr>
    <td colspan="3"><b>Observaciones:</b> <?= $manifiesto->observaciones ?></td>
  </tr>
</table>
<table class="table table-bordered table-condensed table-hover">
  <tr>
    <th>Remesa</th>
    <th>Cant.</th>
    <th>Peso</th>
    <th>Remitente</th>
    <th>Destinatario</th>
    <th>Ciudad Destino</th>
    <th>Estado</th>
    <th>Forma Pago</th>
    <th>Total</th>
  </tr>
  <?php
  $total          = 0;
  $total_contado  = 0;
  $total_flete    = 0;
  $total_credito  = 0;
  $total_seguro   = 0;
  $unidades       = 0;
  $peso           = 0;
  $vol            = 0;
  $manifiesto->guias();
  if (empty($manifiesto->guias)) {
    echo "<tr class='warning'><td colspan='9' class='expand'>El manifiesto no tiene guías...</td></tr>";
  } else {
    foreach ($manifiesto->guias as $guia) {
      $c = ($guia->idestado == 3 or $guia->idestado == 1) ? 'class="warning"' : '';
      echo "<tr $c>";
      echo '<td title="'.$guia->fecha_recibido_mercancia.'">'.$guia->id."</td>";
      echo '<td class="text-right">'.$guia->unidades."</td>";
      echo '<td class="text-right">'.$guia->peso."</td>";
      echo "<td>".$guia->cliente_nombre_completo."</td>";
      echo "<td>".$guia->contacto_nombre_completo."</td>";
      echo "<td>".$guia->contacto_ciudad_nombre."</td>";
      echo "<td>".$guia->estado()."</td>";
      echo "<td>".$guia->formapago."</td>";
      echo "<td class='text-right'>".number_format($guia->total + $guia->valorseguro)."</td>";
      echo "</tr>";
      $total_seguro += $guia->valorseguro;
      $t = $guia->total + $guia->valorseguro;
      if ($guia->formapago == 'FLETE AL COBRO') {
        $total_flete += $t;
      } elseif ($guia->formapago == 'CONTADO') {
        $total_contado += $t;
      } else {
        $total_credito += $t;
      }
      $total += $t;
      $unidades += $guia->unidades;
      $peso += $guia->peso;
      $vol += $guia->kilo_vol/$configuracion->calKiloVolumen;
    }
  }
  ?>
</table>
<div class="row-fluid">
  <fieldset class="span4 table">
    <legend>Viaje</legend>
    <table class="table">
      <tr>
        <td><b>Valor del Viaje</b></td>
        <td class="text-right">
          <?= number_format($manifiesto->valor_flete) ?>
        </td>
      </tr>
      <tr>
        <td><b>Retención Fuente</b> (-)</td>
        <td class="text-right">
          <?= number_format($manifiesto->retencion_fuente) ?>
        </td>
      </tr>
      <tr>
        <td><b>ICA</b> (-)</td><td class="text-right"><?= number_format($manifiesto->ica) ?></td>
      </tr>
      <tr class="info">
        <td style="border-top: 1px solid black"><b>Flete Neto</b></td>
        <td class="text-right" style="border-top: 1px solid black">
          <?= number_format($manifiesto->valor_flete - $manifiesto->retencion_fuente - $manifiesto->ica)?>
        </td>
      </tr>
      <tr>
        <td><b>Anticipo</b> (-)</td><td class="text-right"><?= number_format($manifiesto->anticipo)?></td>
      </tr>
      <tr>
        <td><b>Descuento</b> (-)</td><td class="text-right"><?= number_format($manifiesto->descuento)?></td>
      </tr>
      <tr class="info">
        <td style="border-top: 1px solid black"><b>Total Neto</b></td>
        <td class="text-right" style="border-top: 1px solid black">
          <?= number_format($manifiesto->valor_flete - $manifiesto->descuento - $manifiesto->retencion_fuente-$manifiesto->ica - $manifiesto->anticipo)?>
        </td>
      </tr>
    </table>
  </fieldset>
  <fieldset class="span4 table">
    <legend>Mercancía</legend>
    <table class="table">
      <tr>
        <td colspan="2"><b>Unidades:</b></td>
        <td colspan="2" class="text-right"><?= number_format($unidades)?></td>
      </tr>
      <tr>
        <td colspan="2"><b>Peso:</b> (Kg)</td>
        <td colspan="2" class="text-right"><?= number_format($peso, 2)?></td>
      </tr>
      <tr>
        <td colspan="2"><b>Volúmen:</b> (m<sup>3</sup>)</td>
        <td colspan="2" class="text-right"><?= round($vol, 3)?></td>
      </tr>
    </table>
  </fieldset>
  <fieldset class="span4 table">
    <legend>Flete</legend>
    <table class="table">
      <tr>
        <td colspan="2"><b>Total Flete al Cobro:</b></td>
        <td colspan="2" class="text-right"><?= number_format($total_flete)?></td>
      </tr>
      <tr>
        <td colspan="2"><b>Total Contado:</b></td>
        <td colspan="2" class="text-right"><?= number_format($total_contado)?></td>
      </tr>
      <tr>
        <td colspan="2"><b>Total Credito:</b></td>
        <td colspan="2" class="text-right"><?= number_format($total_credito)?></td>
      </tr>
      <tr class="info">
        <td colspan="2" style="border-top: 1px solid black;"><b>Total Viaje:</b></td>
        <td colspan="2" style="border-top: 1px solid black;" class="text-right"><?= number_format($total)?></td>
      </tr>
      <tr>
        <td colspan="2"><b>Total Seguro (-):</b></td>
        <td colspan="2" class="text-right"><?= number_format($total_seguro)?></td>
      </tr>
      <tr class="info">
        <td colspan="2" style="border-top: 1px solid black;"><b>Flete Neto:</b></td>
        <td colspan="2" style="border-top: 1px solid black;" class="text-right"><?= number_format($total-$total_seguro)?></td>
      </tr>
    </table>
  </fieldset>
</div>
?>
<script>
(function(){
  $('#entregadas').click(function(){
    LOGISTICA.Dialog.open('Marcar Como Entregadas', manifiestos_path+'marcar.php?id=<?= $manifiesto->id ?>');
  });
})();
</script>
