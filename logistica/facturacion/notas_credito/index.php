<?php
require '../../seguridad.php';
$sql = "SELECT nc.*, clientes.*, facturas.*\n"
    . "FROM nc, clientes\n"
    . "LEFT JOIN facturas \n"
    . "ON facturas.idcliente = clientes.id\n"
    . "WHERE nc.id = facturas.nc\n"
    . "ORDER BY `nc`.`fecha` DESC";
$paging = new PHPPaging('right_content', $sql);
$paging->ejecutar();
?>
<h2>Notas Credito</h2>
<table class="table table-hover table-bordered table-condensed">
  <thead>
    <tr>
      <?php
      $menus = array('NC','Empresa','Factura','Fecha','Concepto', 'Valor');
      foreach ($menus as $menu) {
        echo '<th>'.$menu.'</th>';
      }
      ?>
    </tr>
  </thead>

<?php

	while ($factura = $paging->fetchResultado('stdClass')) {
		echo '<tr>';
		echo '<td>'.$factura->nc.'</td>';
		echo '<td>'.$factura->nombre.'</td>';
		echo '<td>'.$factura->id.'</td>';
		echo '<td>'.$factura->fecha.'</td>';
		echo '<td>'.$factura->concepto.'</td>';
		echo '<td>'.$factura->valor.'</td>';
	}
	?>
</table>
<?= $paging->fetchNavegacion() ?>
