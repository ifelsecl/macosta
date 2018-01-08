<?php
$raiz = "../../";
$formato_fecha = '%b %d, %Y';
require "../seguridad.php";
if (! isset($_GET['id'])) {
  include $raiz."mensajes/id.php";
  exit;
}
$guia = new Guia;
if (! $guia->find($_GET['id'])) exit('No existe la guía.');
$pdfs = RemoteFile::process(array($guia->id, IP()));
$pdf = $pdfs[0];
?>
<style>
#tabs{border:none;}
#tabs .ui-tabs-nav {
  padding-left: 0px;
  background: transparent;
  border-width: 0px 0px 1px 0px;
  border-radius: 0px;
  -moz-border-radius: 0px;
  -webkit-border-radius: 0px;
}
#tabs .ui-tabs-panel {
  background: transparent repeat-x scroll 50% top #f5f3e5;
  padding: 3px !important;
  border-width: 0px 1px 1px 1px;
}
</style>
<script>
$(function(){
  <?php if ($pdf->found) { ?>
  var pdf = new PDFObject({
    url: '<?= $pdf->url ?>',
    pdfOpenParams: { view: 'FitH', statusbar: '0', messages: '0', navpanes: '0' }
  }).embed("PDF");
  $('#PDF').css('width','600px').css('height','500px');
  <?php } ?>
  $("#tabs").tabs();
});
</script>
<div id="tabs">
  <ul>
    <li><a href="#InformacionGuia" title="Información de la guía">Información</a></li>
    <li <?php if($guia->idestado==1 or $guia->idestado==6 or $guia->idestado==7) echo 'style="display:none;"';?>><a href="#GuiaEscaneada" title="Guía Escaneada en PDF">Guía Escaneada</a></li>
  </ul>
  <div id="InformacionGuia">
    <table align="center">
      <tr>
        <?php
        $rec = strftime($formato_fecha, strtotime($guia->fecha_recibido_mercancia));
        $des = 'Sin despachar';
        if($guia->fechadespacho){
          $des = strftime($formato_fecha, strtotime($guia->fechadespacho));
        }
        $ent = 'Sin entregar';
        if($guia->fechaentrega){
          $ent = strftime($formato_fecha, strtotime($guia->fechaentrega));
        }
        ?>
        <td align="left"><b>Recibido:</b> <?= $rec ?></td>
        <td align="center"><b>Despachado:</b> <?= $des ?></td>
        <td align="right"><b>Entregado:</b> <?= $ent ?></td>
      </tr>
      <tr style="background-color: silver">
        <td><b>Manifiesto:</b>
          <?php
          if($guia->idplanilla){
            echo $guia->idplanilla;
          }else{
            echo '<span class="">sin asignar</span>';
          }
          ?>
        </td>
        <td align="center"><b>Factura:</b>
          <?php
          if($guia->idfactura){
            echo $guia->idfactura;
          }else{
            echo '<span class="">sin facturar</span>';
          }
          ?>
        </td>
        <?php
        $c = $guia->idestado == 6 ? 'anulada' : 'activa';
        echo '<td class="guia-'.$c.'">'.$guia->estado().'</td>';
        ?>
      </tr>
    </table>
    <table style="width: 100%">
      <tr>
        <td style="border-right: 1px solid #eee; vertical-align: top">
          <b>REMITENTE</b>
          <hr class="hr-small">
          <table cellpadding="0">
            <tr>
              <td><b>Nombre: </b></td>
              <td><?= wordwrap($guia->cliente()->nombre_completo, 30, '<br>') ?></td>
            </tr>
            <tr>
              <td><b><?= $guia->cliente->tipo_identificacion() ?>:</b></td>
              <td><?= $guia->cliente->numero_identificacion_completo ?></td>
            </tr>
            <tr>
              <td><b>Direccion: </b></td>
              <td><?= wordwrap($guia->cliente->direccion, 30, '<br>') ?></td>
            </tr>
            <tr>
              <td><b>Ciudad: </b></td>
              <td><?= $guia->cliente->ciudad_nombre ?></td>
            </tr>
            <tr>
              <td><b>Telefono:</b></td>
              <td><?= $guia->cliente->telefono.'-'.$guia->cliente->telefono2 ?></td>
            </tr>
            <tr>
              <td><b>Celular:</b></td>
              <td><?= $guia->cliente->celular ?></td>
            </tr>
          </table>
        </td>
        <td style="vertical-align: top">
          <b>DESTINATARIO</b>
          <hr class="hr-small">
          <table cellpadding="0">
            <tr>
              <td><b>Nombre:</b></td>
              <td><?= $guia->contacto()->nombre_completo ?></td>
            </tr>
            <tr>
              <td><b><?= $guia->contacto->tipo_identificacion() ?>:</b></td>
              <td> <?= $guia->contacto->numero_identificacion_completo ?></td>
            </tr>
            <tr>
              <td><b>Dirección:</b></td>
              <td><?= wordwrap($guia->contacto->direccion, 30, '<br>')?></td>
            </tr>
            <tr>
              <td><b>Ciudad:</b></td>
              <td><?= $guia->contacto->ciudad_nombre ?></td>
            </tr>
            <tr>
              <td><b>Telefono:</b></td>
              <td><?= $guia->contacto->telefono ?></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <table class="text-center">
      <tr>
        <td><b>No. anterior:</b> <?= $guia->numero ?></td>
        <td><b>No. Documento:</b> <?= $guia->documentocliente ?></td>
        <td><b>Recogida:</b> <?= $guia->recogida ?></td>
      </tr>
      <tr>
        <td colspan="4">
          <table class="table table-bordered table-hover table-condensed" cellspacing="0" cellpadding="0">
            <thead>
              <tr>
                <th>Codigo</th>
                <th>Producto</th>
                <th title="Unidades">Unid</th>
                <th>Kg</th>
                <th>Kg/Vol</th>
                <th title="Valor Declarado">Vr Decl.</th>
                <th style="width:2px;"><!-- --></th>
                <th>Seguro</th>
                <th>Flete</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $guia->items();
              $uni  = 0;
              $peso = 0;
              $kv   = 0;
              $seguro = 0;
              $decl = 0;
              $total  = 0;
              foreach ($guia->items as $item) {
                echo '<tr>';
                echo '<td>'.$item->idproducto.'</td>';
                $p=wordwrap(substr($item->producto, 0, 30),20,"<br>");
                echo '<td style="font-size:85%" title="'.$item->producto.'">'.$p.'</td>';
                echo '<td class="text-right">'.$item->unidades.'</td>';
                echo '<td class="text-right">'.number_format($item->peso).'</td>';
                echo '<td class="text-right">'.$item->kilo_vol.'</td>';
                echo '<td class="text-right">'.number_format($item->valor_declarado).'</td>';
                echo '<td></td>';
                echo '<td class="text-right">'.number_format($item->seguro).'</td>';
                echo '<td class="text-right">'.number_format($item->valor).'</td>';
                echo '</tr>';
                $uni  += $item->unidades;
                $peso += $item->peso;
                $kv   += $item->kilo_vol;
                $decl += $item->valor_declarado;
                $seguro += $item->seguro;
                $total  += $item->valor;
              }
              ?>
              <tr style="font-weight:bold;">
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="text-right"><?= number_format($uni) ?></td>
                <td class="text-right"><?= number_format($peso) ?></td>
                <td class="text-right"><?= number_format($kv) ?></td>
                <td class="text-right"><?= number_format($decl) ?></td>
                <td></td>
                <td class="text-right"><?= number_format($seguro) ?></td>
                <td class="text-right"><?= number_format($total) ?></td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      <tr style="background-color: silver;font-size: 18px;">
        <td colspan="2"><b>Forma de pago:</b> <?= $guia->formapago ?></td>
        <td class="text-right"><b>TOTAL:</b> <?= number_format($guia->total+$guia->valorseguro) ?></td>
      </tr>
      <tr>
        <td height="17" colspan="4"><hr class="hr-small"></td>
      </tr>
      <tr>
        <td><b>Observaciones:</b></td>
        <td colspan="3" class="text-left">
          <?= wordwrap($guia->observacion, 50, '<br />')?>
        </td>
      </tr>
    </table>
  </div>
  <div id="GuiaEscaneada">
    <?php
    if ($pdf->found) {
      echo '<div style="text-align:center;" id="PDF">Parece que tu navegador no tiene soporte para PDF, puedes instalar <a target="_blank" title="Descargar Adobe Reader" href="http://get.adobe.com/reader/">Adobe Reader</a></div>';
      echo '<br /><a target="_blank" href="'.$pdf->url.'">Descargar el archivo</a>';
    }else{
      echo '<div style="width:350px;height:200px;background:transparent url(../img/pdf.png) no-repeat center center" class="expand">Aún no se ha cargado el archivo...</div>';
    }
    ?>
  </div>
</div>
