<?php
if (! isset($factura)) exit('Factura is not defined');
$c = $factura->activa == 'si' ? '' : 'error';
?>
<tr class="<?= $c ?>">
  <td><?= $factura->id ?></td>
  <td><?= $factura->cliente_nombre ?></td>
  <td align="center"><?= $factura->fecha_emision_corta() ?></td>
  <td align="center"><?= $factura->fecha_vencimiento_corta() ?></td>
  <?php
  $c = $factura->tipo == 'CREDITO' ? 'info' : 'inverse';
  ?>
  <td align="center"><span class="label label-<?= $c ?>"><?= $factura->tipo ?></span></td>
  <td align="center" class="factura_<?= $factura->estado ?>"><?= $factura->estado ?></td>
  <?php
  $name = "idfactura=".$factura->id."&".nonce_create_query_string($factura->id);
  ?>
  <td><div class="btn-group">
  <?php
  if (isset($current_user->permisos[FACTURACION_VER])) { ?>
    <a name="<?= $name ?>" class="btn btn-default ver" title="Ver" href="#"><i class="icon-search"></i></a>
  <?php }
  if (isset($current_user->permisos[FACTURACION_EDITAR]) and $factura->activa == 'si') { ?>
    <a name="<?= $name ?>" class="btn btn-default editar" title="Editar" href="#"><i class="icon-pencil"></i></a>
    <?php if (!$factura->is_paid()) { ?>
      <a name="<?= $name ?>" class="btn btn-default pagar" title="Pagar" href="#"><i class="icon-money"></i></a>
    <?php } ?>
  <?php }
  if (isset($current_user->permisos[FACTURACION_IMPRIMIR]) and $factura->activa == 'si') { ?>
    <a href="facturacion/imprimir?<?= $name ?>" target="_blank" class="btn btn-default imprimir" title="Imprimir"><i class="icon-print"></i></a>
  <?php }
  if ( (isset($current_user->permisos[FACTURACION_ELIMINAR]) or isset($current_user->permisos[FACTURACION_ANULAR]))) { ?>
    <a name="<?= $name ?>" class="btn btn-default eliminar btn-danger" title="Eliminar/Anular" href="#"><i class="icon-trash"></i></a>
  <?php } ?>
  </div></td>
</tr>
