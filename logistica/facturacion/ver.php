<?php
require "../seguridad.php";
if (! isset($_GET['idfactura'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
if (! isset($_SESSION['permisos'][FACTURACION_VER])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (! $factura = Factura::find($_GET['idfactura'])) exit('No existe la factura.');
$guias = $factura->guias();
$pagos = $factura->pagos()
?>
<div class="pull-right">
  <div id="cargando" style="display:none; height: 16px; width: 16px;"><img src="css/ajax-loader.gif" alt="cargando" /></div>
  <?php if ($factura->estado == 'Abierta') { ?>
    <button id="cerrar" class="btn"><i class="icon-lock"></i> Cerrar</button>
  <?php } ?>
  <a class="btn" title="Imprimir" target="_blank" href="facturacion/imprimir?idfactura=<?= $factura->id.'&'.nonce_create_query_string($factura->id) ?>"><i class="icon-print"></i></a>
  <button id="regresar" class="btn btn-success">Regresar</button>
</div>
<h2>Factura <?= $factura->id ?></h2>
<?php if ($factura->activa == 'no') { ?>
<div class="alert alert-error">
  <strong>ANULADA</strong>
</div>
<?php }
if ($factura->is_paid()) { ?>
<div class="alert alert-success alert-block">
  <h4><i class="icon-ok"></i> ¡Factura Pagada!</h4>
  <!-- <b>La factura fue pagada el día <?= htmlentities(strftime('%d de %B de %Y', strtotime($factura->fecha_pago )) ) ?>.</b>
  <br>Observaciones: <?= $factura->observaciones_pago ?> -->
</div>
<?php } else { ?>
<div class="alert alert-warning alert-block">
  <h4><i class="icon-warning"></i> ¡Factura Pendiente!</h4>
  La factura aún no ha sido pagada.
</div>
<?php } ?>
<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab_informacion" data-toggle="tab">Información</a></li>
    <li><a href="#tab_pagos" data-toggle="tab">Pagos</a></li>
    <li><a href="#tab_historial" data-toggle="tab">Historial</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active fade in" id="tab_informacion">
      <table class="table table-condensed">
        <tr>
          <td><b>Cliente</b></td>
          <td colspan="3"><?= $factura->cliente()->nombre_completo ?></td>
        </tr>
        <tr>
          <td>
            <b><?= $factura->cliente->tipo_identificacion() ?></b>
          </td>
          <td><?= $factura->cliente->numero_identificacion_completo ?>
          </td>
          <td><b>Dirección</b></td>
          <td><?= $factura->cliente->direccion ?></td>
        </tr>
        <tr>
          <td><b>Teléfono</b></td>
          <td><?= $factura->cliente->primer_telefono() ?></td>
          <td><b>Ciudad</b></td>
          <td><?= $factura->cliente->ciudad_nombre ?></td>
        </tr>
        <tr>
          <td><b>Condición de pago</b></td>
          <td><?= $factura->condicionpago ?> días</td>
          <td><b>Fecha emisión</b></td>
          <td><?= $factura->fecha_emision_corta() ?></td>
        </tr>
        <tr>
          <td><b>Fecha vencimiento</b></td>
          <td colspan="3"><?= $factura->fecha_vencimiento_corta() ?></td>
        </tr>
      </table>
      <table class="table table-hover table-condensed table-bordered" cellpadding="0">
        <thead>
          <tr>
            <th>#</th>
            <th>Cliente Destino</th>
            <th>Unid.</th>
            <th>Guía</th>
            <th>Doc. Cliente</th>
            <th>Destino</th>
            <th>Seguro</th>
            <th>Flete</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $valor_flete  = 0;
        $valor_seguro = 0;
        $peso         = 0;
        if (empty($guias)) {
          $factura->valorseguro = 0;
          echo '<tr class="warning"><td class="expand" colspan="8">La factura no tiene guías.</td></tr>';
        } else {
          foreach ($guias as $i => $guia) {
            echo '<tr>';
            echo '<td>'.($i + 1).'</td>';
            echo '<td>'.$guia->contacto->nombre_completo.'</td>';
            echo '<td>'.$guia->unidades.'</td>';
            echo '<td>'.$guia->id.'</td>';
            if (strlen($guia->documentocliente) > 15) {
              echo '<td title="'.$guia->documentocliente.'">'.substr($guia->documentocliente, 0, 13).'...</td>';
            } else {
              echo '<td>'.$guia->documentocliente.'</td>';
            }
            echo '<td>'.$guia->contacto->ciudad->nombre.'</td>';
            echo '<td style="text-align: right">'.number_format($guia->valorseguro).'</td>';
            echo '<td style="text-align: right">'.number_format($guia->total).'</td>';
            echo '</tr>';
            $valor_flete  += $guia->total;
            $valor_seguro += $guia->valorseguro;
            $peso         += $guia->peso;
          }
        }
        ?>
        </tbody>
      </table>
      <table class="table table-condensed pull-right" style="width: 300px">
        <tbody>
          <tr>
            <td align="right"><b>Total Peso</b></td>
            <td align="right"><?= number_format($peso) ?> Kg</td>
          </tr>
          <tr>
            <td align="right"><b>Total flete</b></td>
            <td align="right"><?= number_format($valor_flete) ?></td>
          </tr>
          <tr>
            <td align="right"><b>Total seguro</b>(+)</td>
            <td align="right"><?= number_format($valor_seguro) ?></td>
          </tr>
          <tr>
            <?php
            if ($factura->descuento == 0) $descuento = 0;
            else $descuento = round($factura->descuento/$valor_flete*100, 1);
            ?>
            <td align="right"><b>Descuento (<?= $descuento ?>%)</b>(-)</td>
            <td align="right"><?= number_format($factura->descuento) ?></td>
          </tr>
          <tr class="info">
            <td align="right"><b>TOTAL A PAGAR</b></td>
            <td align="right"><b><?= number_format($valor_flete+$valor_seguro-$descuento) ?></b></td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" id="idfactura" value="<?= $factura->id ?>" />
    </div>
    <div id="tab_pagos" class="tab-pane fade in">
      <table class="table table-hover table-condensed table-bordered">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Valor</th>
            <th>Tipo</th>
            <th>Notas</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($pagos as $pago) {
            echo '<tr>';
            echo '<td>'.$pago->fecha.'</td>';
            echo '<td class="text-right">$ '.number_format($pago->valor).'</td>';
            echo '<td>'.$pago->tipo.'</td>';
            echo '<td>'.$pago->notas.'</td>';
            echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
    <div id="tab_historial" class="tab-pane fade in">
      <table class="table table-hover table-condensed table-bordered">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $factura->history();
          if ( empty($factura->history) ) {
            echo '<tr class="warning"><td colspan="3" class="expand">No se han realizado modificaciones</td></tr>';
          } else {
            foreach ($factura->history as $h) {
              echo '<tr>';
              echo '<td>'.$h->fecha.'</td>';
              echo '<td>'.$h->usuario.'</td>';
              echo '<td>'.$h->accion.'</td>';
              echo '</tr>';
            }
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
(function() {
  $('#regresar').click(function() {
    $('#actualizar').click();
    regresar();
  });
  $('#cerrar').click(function() {
    $('#cargando').show();
    $.ajax({
      url: facturacion_path+'ajax.php', data: 'cerrar=a0b13a&'+$('#cerrar-ver').attr('name'), type: 'POST',
      success: function(r) {
        if (r=='ok') {
          var html='<p class="expand"><i class="icon-ok icon-2x"></i>¡La factura ha sido cerrada!</p>';
          LOGISTICA.Dialog.open('Facturacion',html,true);
          $("#extra_content").load(facturacion_path+"ver.php?idfactura="+$('#idfactura').val());
        } else {
          LOGISTICA.Dialog.open('Error',r,true);
        }
        $('#cargando').hide();
      }
    });
  });
})();
</script>
