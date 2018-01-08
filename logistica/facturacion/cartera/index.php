<?php
require '../../seguridad.php';
$sql = Cartera::unpaid(true);
$paging = new PHPPaging('right_content', $sql);
$paging->ejecutar();
?>
<h2>Cartera</h2>
<table class="table table-hover table-bordered table-condensed">
  <thead>
    <tr>
      <?php
      $menus = array('Fecha', 'Total', 'Pagos', 'Saldo');
      foreach ($menus as $menu) {
        echo '<th>'.$menu.'</th>';
      }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php
    if ($paging->numTotalRegistros() == 0) {
      echo '<tr class="warning"><td colspan="'.count($menu).'>" class="expand">No se encontraron clientes...</td></tr>';
    } else {
      $previous_client = null;
      $saldo_total = 0;
      while ($factura = $paging->fetchResultado('Factura')) {
        if ($previous_client != $factura->idcliente) {
          if (! is_null($previous_client)) {
            echo '<tr class="warning"><td><b>Saldo</b></td><td colspan="3" class="text-right">'.number_format($saldo_total).'</td></tr>';
            echo '<tr><td colspan="'.count($menus).'"></td></tr>';
            $saldo_total = 0;
          }
          echo '<tr><td colspan="'.count($menus).'"><b>'.$factura->cliente_nombre.'</b></td></tr>';
        }
        if (is_null($previous_client)) {
          $previous_client = $factura->idcliente;
        }
        echo '<tr>';
        echo '<td>'.$factura->fecha_emision_corta().'</td>';
        echo '<td class="text-right">'.number_format($factura->total()).'</td>';
        echo '<td class="text-right">'.number_format($factura->total_pagos()).'</td>';
        echo '<td class="text-right">'.number_format($factura->saldo()).'</td>';
        echo '</tr>';
        $saldo_total += $factura->saldo();
      }
    }
    ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
