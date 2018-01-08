<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][GUIAS_VER])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
$formato_fecha = '%b %d, %Y';
if (! isset($_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
$guia = new Guia;
if (! $guia->find($_GET['id'])) exit('No existe la guía.');
$tipos = Cliente::$tipos_identificacion;
$pdfs = RemoteFile::process(array($guia->id), IP());
$pdf = $pdfs[0];
?>
<?php if (isset($_SESSION['permisos'][GUIAS_IMPRIMIR])) {
  echo '<a class="btn pull-right" href="logistica/guias/imprimir?id='.$guia->id.'&'.NONCE_KEY.'='.nonce_create($guia->id).'" title="Imprimir" target="_blank"><i class="icon-print"></i></a>';
} ?>
<div id="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#tab_informacion" title="Información de la guía">Información</a></li>
    <li <?php if($guia->idestado==1 or $guia->idestado==6 or $guia->idestado==7) echo 'style="display:none;"' ?>><a data-toggle="tab" href="#tab_pdf" title="Guía Escaneada en PDF">Guía Escaneada</a></li>
    <li><a data-toggle="tab" href="#tab_historial" title="Historial de Cambios">Historial</a></li>
  </ul>
  <div class="tab-content">
    <div id="tab_informacion" class="tab-pane active">
      <table style="width:100%" align="center">
        <tr>
          <?php
          $rec = strftime($formato_fecha, strtotime($guia->fecha_recibido_mercancia));
          $des = '<span>Sin despachar</span>';
          if ($guia->fechadespacho) {
            $des = strftime($formato_fecha, strtotime($guia->fechadespacho));
          }
          $ent = '<span>Sin entregar</span>';
          if ($guia->fechaentrega) {
            $ent = strftime($formato_fecha, strtotime($guia->fechaentrega));
          }
          ?>
          <td class="text-left"><b>Recibido:</b> <?= $rec ?></td>
          <td class="text-center"><b>Despachado:</b> <?= $des ?></td>
          <td class="text-right"><b>Entregado:</b> <?= $ent ?></td>
        </tr>
        <tr>
          <?php
          $c = $guia->idestado == 6 ? 'anulada' : 'activa';
          ?>
          <td class="guia-<?= $c ?>"><?= $guia->estado() ?></td>
          <td class="text-center"><b>Manifiesto:</b>
            <?php
            if ($guia->idplanilla) {
              echo $guia->idplanilla;
            } else {
              echo '<span class="label">Sin asignar</span>';
            }
            ?>
          </td>
          <td class="text-right"><b>Factura:</b>
            <?php
            if ($guia->idfactura) {
              echo $guia->idfactura;
            } else {
              echo '<span class="label">Sin facturar</span>';
            }
            ?>
          </td>
        </tr>
      </table>
      <table style="width:100%">
        <tr>
          <td>
            <fieldset>
              <legend>REMITENTE</legend>
              <table cellpadding="0">
                <tr>
                  <td><b>Nombre: </b></td>
                  <td><?= wordwrap($guia->cliente()->nombre_completo, 30, '<br>') ?></td>
                </tr>
                <tr>
                  <td><b><?= $tipos[$guia->cliente->tipo_identificacion] ?>:</b></td>
                  <td><?= $guia->cliente->numero_identificacion_completo ?></td>
                </tr>
                <tr>
                  <td><b>Direccion: </b></td>
                  <td><?= wordwrap($guia->cliente->direccion, 30, '<br>')?></td>
                </tr>
                <tr>
                  <td><b>Ciudad: </b></td>
                  <td><?= $guia->cliente->ciudad_nombre ?></td>
                </tr>
                <tr>
                  <td><b>Telefono:</b></td>
                  <td><?= $guia->cliente->telefono_completo() ?></td>
                </tr>
              </table>
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend>DESTINATARIO</legend>
              <table cellpadding="0">
                <tr>
                  <td><b>Nombre:</b></td>
                  <td><?= wordwrap($guia->contacto()->nombre_completo, 30, '<br>') ?></td>
                </tr>
                <tr>
                  <td><b><?= $guia->contacto->tipo_identificacion() ?>:</b></td>
                  <td><?= $guia->contacto->numero_identificacion_completo ?></td>
                </tr>
                <tr>
                  <td><b>Dirección:</b></td>
                  <td><?= wordwrap($guia->contacto->direccion, 30, '<br>') ?></td>
                </tr>
                <tr>
                  <td><b>Ciudad:</b></td>
                  <td><?= $guia->contacto->ciudad_nombre ?></td>
                </tr>
                <tr>
                  <td><b>Teléfono:</b></td>
                  <td><?= $guia->contacto->telefono_completo() ?></td>
                </tr>
              </table>
            </fieldset>
          </td>
        </tr>
      </table>
      <table style="width: 100%" align="center" cellpadding="0">
        <tr>
          <td><b>No. anterior:</b> <?= $guia->numero ?></td>
          <td><b>No. Documento:</b> <?= $guia->documentocliente ?></td>
          <td>
            <?php
            if($guia->recogida == 'si') echo '<b>Recogida:</b> SI</td>';
            ?>
          </td>
        </tr>
        <tr>
          <td colspan="4">
            <table style="margin-botom: 5px" class="table table-bordered table-condensed table-hover" cellpadding="0">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Producto</th>
                  <th>Embalaje</th>
                  <th title="Unidades">Unid</th>
                  <th>Kg</th>
                  <th>Kg/Vol</th>
                  <th title="Valor Declarado">Vr Decla</th>
                  <th style="width:3px;"><!-- --></th>
                  <th>Seguro</th>
                  <th>Flete</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($guia->items() as $item) {
                  echo '<tr>';
                  echo '<td>'.$item->idproducto.'</td>';
                  $p=wordwrap(substr($item->producto, 0, 30),20,"<br>");
                  echo '<td style="font-size:85%" title="'.$item->producto.'">'.$p.'</td>';
                  echo '<td align="center">'.$item->embalaje.'</td>';
                  echo '<td align="right">'.$item->unidades.'</td>';
                  echo '<td align="right">'.number_format($item->peso, 2).'</td>';
                  echo '<td align="right">'.$item->kilo_vol.'</td>';
                  echo '<td align="right">'.number_format($item->valor_declarado).'</td>';
                  echo '<td></td>';
                  echo '<td align="right">'.number_format($item->seguro).'</td>';
                  echo '<td align="right">'.number_format($item->valor).'</td>';
                  echo '</tr>';
                }
                ?>
              </tbody>
            </table>
          </td>
        </tr>
        <tr style="background-color: silver;font-size: 16px;">
          <td><b>Valor Declarado:</b> <?= number_format($guia->valordeclarado) ?></td>
          <td><b>Seguro:</b> <?= number_format($guia->valorseguro) ?></td>
          <td class="text-right"><b>TOTAL:</b> <?= number_format($guia->total+$guia->valorseguro) ?></td>
        </tr>
        <tr>
          <td><b>Forma de pago:</b> <span class="label label-success"><?= $guia->formapago ?></span></td>
          <td><b>Observaciones:</b></td>
          <td colspan="2" class="text-left">
            <?= wordwrap($guia->observacion, 50, '<br>') ?>
          </td>
        </tr>
        <tr class="devuelta" <?php if ($guia->idestado != 5) echo 'style="display:none;"' ?>>
          <td><b>Razón de la devolución:</b></td>
          <td>
            <?php
            $guia->razon_devolucion();
            if ($guia->razon_devolucion) {
              echo $guia->razon_devolucion->nombre;
            }
            ?>
          </td>
        </tr>
      </table>
    </div>
    <div id="tab_pdf" class="tab-pane">
      <?php
      if ($pdf->found) {
        echo '<div style="text-align:center;" id="PDF">Parece que tu navegador no tiene soporte para PDF, puedes instalar <a target="_blank" title="Descargar Adobe Reader" href="http://get.adobe.com/reader/">Adobe Reader</a></div>';
        echo '<br /><a target="_blank" href="'.$pdf->url.'">Has clic aquí para descargar el archivo</a>';
      } else {
        echo '<div style="width:400px;height:200px;" class="expand">NO EXISTE EL ARCHIVO PDF</div>';
      }
      ?>
    </div>
    <div id="tab_historial" class="tab-pane">
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
          $guia->history();
          if (empty($guia->history)) {
            echo '<tr class="warning"><td colspan="3" class="expand">No se han realizado modificaciones</td></tr>';
          } else {
            foreach ($guia->history as $h) {
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
<?php if ($pdf->found) { ?>
<script>
(function(){
  var pdf = new PDFObject({
    url: '<?= $pdf->url ?>',
    pdfOpenParams: { view: 'FitH', statusbar: '0', messages: '0', navpanes: '0' }
  }).embed("PDF");
  $('#PDF').css('width','600px').css('height','500px');
})();
</script>
<?php } ?>
