<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
if (! isset($_SESSION['permisos'][CLIENTES_VER]))  {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
$cliente = new Cliente;
if (! $cliente->find($_REQUEST['id'])) exit('No existe el cliente');
$_SESSION['id_cliente'] = $cliente->id;
$unpaid_invoices = $cliente->unpaid_invoices();
?>
<div id="clientes__show">
  <button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
  <h2><?= $cliente->nombre_completo ?></h2>
  <div class="tabbable">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#tab_informacion">Información</a></li>
      <li><a data-toggle="tab" href="#tab_cartera">Cartera</a></li>
      <li><a data-toggle="tab" href="#tab_contactos">Contactos</a></li>
      <li><a data-toggle="tab" href="#tab_historial">Historial</a></li>
    </ul>
    <div class="tab-content">
      <div id="tab_informacion" class="tab-pane fade in active">
        <div id="map" class="pull-right">
          <img src="http://maps.googleapis.com/maps/api/staticmap?zoom=16&size=200x180&sensor=false&markers=size:small|<?= urlencode($cliente->direccion.', '.$cliente->ciudad_nombre) ?>" />
        </div>
        <table cellpadding="1">
          <tr>
            <td><b>Tipo de identificación:</b></td>
            <td><?= $cliente->tipo_identificacion ?></td>
            <td><b>Número de identificación:</b></td>
            <td><?= $cliente->numero_identificacion_completo ?></td>
          </tr>
          <tr>
            <td><b>N°Sede:</b></td>
            <td colspan="3">
              <?= $cliente->numero_sede ?>
            </td>
          </tr>
		  <tr>
            <td><b>Sede:</b></td>
            <td colspan="3">
              <?= $cliente->sede ?>
            </td>
          </tr>
		  <tr>
            <td><b>Ciudad:</b></td>
            <td colspan="3">
              <?= $cliente->ciudad_nombre.' ('.$cliente->departamento_nombre.')' ?>
            </td>
          </tr>
          <tr>
            <td><b>Dirección:</b></td>
            <td colspan="3">
              <?= $cliente->direccion ?>
            </td>
          </tr>
          <tr>
            <td><b>Teléfono:</b></td>
            <td colspan="3"><?= $cliente->telefono.' - '.$cliente->telefono2 ?></td>
          </tr>
          <tr>
            <td><b>Celular:</b></td>
            <td><?= $cliente->celular ?></td>
          </tr>
          <tr>
            <td><b>E-mail:</b></td>
            <td>
            <a href="mailto:<?= $cliente->email ?>"><?= $cliente->email ?></a></td>
            <td><b>Sitio Web:</b></td>
            <td><a href="<?= $cliente->sitioweb ?>" target="_blank"><?= $cliente->sitioweb ?></a></td>
          </tr>
          <?php if ($cliente->tipo_identificacion == 'N') { ?>
          <tr>
            <td><b>Forma Jurídica:</b></td>
            <td><?= $cliente->forma_juridica() ?></td>
            <td><b>Régimen:</b></td>
            <td><?= $cliente->regimen() ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><b>Restricción peso:</b></td>
            <td><?= $cliente->restriccionpeso ?> Kg</td>
            <td><b>Seguro:</b></td>
            <td><?= $cliente->porcentajeseguro ?>%</td>
          </tr>
          <tr>
            <td><b>Descuento:</b></td>
            <td><?= $cliente->descuento ?>%</td>
            <td><b>Condición de Pago:</b></td>
            <td><?= $cliente->condicion_pago ?> días</td>
          </tr>
        </table>
        <br>
        <?php $img = '<p class="expand"><img src="css/ajax-loader.gif" /></p>' ?>
        <div class="portlet">
          <div class="portlet-header">Facturación
            <select id="sFacturacionMes">
              <option value="ACTUAL">Mes Actual</option>
              <option value="ANTERIOR">Mes Anterior</option>
              <option value="3MESES" selected="selected">Hace 3 Meses</option>
              <option value="6MESES">Hace 6 Meses</option>
              <option value="12MESES">Hace 1 Año</option>
            </select>
          </div>
          <div class="portlet-content">
            <div id="gFacturacionCliente" style="min-width:300px;width:100%;min-height:250px;height:auto;margin:0 auto">
              <?= $img ?>
            </div>
          </div>
        </div>
        <div class="portlet">
          <div class="portlet-header">Mercancía Transportada&nbsp;
            <select id="sMesMercancia">
              <option value="ACTUAL">Mes Actual</option>
              <option value="ANTERIOR">Mes Anterior</option>
              <option value="3MESES" selected="selected">Hace 3 Meses</option>
              <option value="6MESES">Hace 6 Meses</option>
              <option value="12MESES">Hace 1 Año</option>
            </select>
          </div>
          <div class="portlet-content">
            <div id="gMercanciaCliente" style="min-width:300px;width:100%;min-height:300px;height:auto;margin:0 auto">
              <?= $img ?>
            </div>
          </div>
        </div><!-- End Portlet Mercancia -->
        <div class="portlet">
          <div class="portlet-header">Contactos Mas Frecuentes&nbsp;
            <select id="sMesContactos">
              <option value="ACTUAL">Mes Actual</option>
              <option value="ANTERIOR">Mes Anterior</option>
              <option value="3MESES">Hace 3 Meses</option>
              <option value="6MESES">Hace 6 Meses</option>
              <option value="12MESES">Hace 1 Año</option>
            </select>
          </div>
          <div class="portlet-content">
            <div id="gContactosCliente" style="min-width:300px;width:100%;min-height:300px;height:auto;margin:0 auto">
              <?= $img ?>
            </div>
          </div>
        </div><!-- End Portlet Clientes -->
        <div class="portlet">
          <div class="portlet-header">Destinos Mas Frecuentes&nbsp;
            <select id="sMesDestinos">
              <option value="ACTUAL">Mes Actual</option>
              <option value="ANTERIOR">Mes Anterior</option>
              <option value="3MESES">Hace 3 Meses</option>
              <option value="6MESES">Hace 6 Meses</option>
              <option value="12MESES">Hace 1 Año</option>
            </select>
          </div>
          <div class="portlet-content">
            <div id="gDestinosCliente" style="min-width:300px;width:100%;min-height:300px;height:auto;margin:0 auto">
              <?= $img ?>
            </div>
          </div>
        </div><!-- End Portlet Destinos -->
      </div>
      <div id="tab_cartera" class="tab-pane fade in">
        <table class="table table-hover table-condensed table-bordered">
          <thead>
            <tr>
              <?php
              $headers = array('ID', 'Fecha', 'Total', 'Pagos', 'Saldo', 'Acción');
              foreach ($headers as $h) {
                echo '<th>'.$h.'</th>';
              }
              ?>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($unpaid_invoices)) { ?>
            <tr><td colspan="<?= count($headers) ?>">No se encontraron facturas</td></tr>
            <?php } else {
              $totales = array('total' => 0, 'total_pagos' => 0, 'saldo' => 0);
              foreach ($unpaid_invoices as $factura) {
                $totales['total'] += $factura->total;
                $totales['total_pagos'] += $factura->total_pagos;
                $totales['saldo'] += $factura->saldo();
                $name = "idfactura=".$factura->id."&".nonce_create_query_string($factura->id);
            ?>
              <tr>
                <td><?= $factura->id ?></td>
                <td><?= $factura->fecha_emision_corta().' - '.$factura->fecha_vencimiento_corta() ?></td>
                <td class="text-right"><?= number_format($factura->total()) ?></td>
                <td class="text-right"><?= number_format($factura->total_pagos()) ?></td>
                <td class="text-right"><?= number_format($factura->saldo()) ?></td>
                <?php
                if (isset($current_user->permisos[FACTURACION_EDITAR])) { ?>
                <td><button name="<?= $name ?>" class="btn btn-primary pay"><i class="icon-money"></i> Pagar</button></td>
                <?php } ?>
              </tr>
              <?php } ?>
              <tr>
                <td colspan="2"><b>Total</b></td>
                <td class="text-right"><?= number_format($totales['total']) ?></td>
                <td class="text-right"><?= number_format($totales['total_pagos']) ?></td>
                <td class="text-right"><?= number_format($totales['saldo']) ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div id="tab_contactos" class="tab-pane fade in">
        <table class="table table-hover table-condensed table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tipo ID</th>
              <th>Num ID</th>
              <th>Nombre</th>
              <th>Ciudad</th>
              <th>Dirección</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $cliente->contactos(50);
            if( empty($cliente->contactos) ){
              echo '<tr class="warning"><td class="expand" colspan="6">No se encontraron contactos...</td></tr>';
            } else {
              foreach ($cliente->contactos as $c) {
                echo '<tr>';
                echo '<td>'.$c->id.'</td>';
                echo '<td>'.$c->tipo_identificacion.'</td>';
                echo '<td>'.$c->numero_identificacion_completo.'</td>';
                echo '<td>'.$c->nombre_completo.'</td>';
                echo '<td>'.$c->ciudad_nombre.'</td>';
                if( strlen($c->direccion)>30 )
                  echo '<td title="'.$c->direccion.'">'.substr($c->direccion,0,30).'...</td>';
                else
                  echo '<td>'.$c->direccion.'</td>';
                echo '</tr>';
              }
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
            $cliente->history();
            if (empty($cliente->history)) {
              echo '<tr class="warning"><td colspan="3" class="expand">No se han realizado modificaciones</td></tr>';
            } else {
              foreach ($cliente->history as $h) {
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
</div>
<script>
(function() {
  LOGISTICA.terceros.clientes = function() {
    var $el = $('#clientes__show');
    var init = function() {
      initPortlet();
      initSelects();
      $el.find('#tab_cartera').on('click', '.pay', payInvoice);
    };
    var initPortlet = function() {
      $el.find(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
        .find(".portlet-header")
        .addClass("ui-widget-header ui-corner-all").end()
        .find(".portlet-content");
    };
    var payInvoice = function(e) {
      LOGISTICA.Content.loadExtra(facturacion_path+"pagar.php?"+this.name);
    };
    var updateChart = function(g, title, mes) {
      var options = {
        chart: {renderTo: ''},
        title: {text: ''},
        subtitle: {text: ''},
        xAxis: {categories: []},
        yAxis: {min: 0,title: {text: ''}},
        credits: {enabled: false},
        legend: {enabled:false},
        tooltip: {
          formatter: function() {
            return '<b>'+ this.x +'</b><br>'+ Highcharts.numberFormat(this.y,0,',','.');
          }
        },
        plotOptions: {column: {pointPadding: 0.2,borderWidth: 0}},
        series: []
      };
      $.ajax({
        url: 'ajax.php?accion='+g+'&mes='+mes,
        type: 'POST', dataType: 'json',
        success: function(r){
          var t='bar';
          if(g=='FacturacionCliente'){
            options.tooltip.formatter=function() {
              return '<b>'+ this.x +'</b><br>$'+Highcharts.numberFormat(this.y,0,',','.')+' COP';
            }
            t='column';
          }
          if(g=='MercanciaCliente'){
            options.legend.enabled = true;
            options.tooltip.formatter = function() {
              return '<b>'+this.x +'</b><br>'+ Highcharts.numberFormat(this.y,0,',','.') + (this.series.name == 'Unidades' ? ' und' : ' kgs');
            }
            options.chart.zoomType = 'xy';
            options.xAxis.categories = r.nombres;
            options.subtitle.text =  r.texto;
            options.yAxis = [
              { // Primary yAxis
                min: 0, title: {text: 'Und'}
              }, { // Secondary yAxis
                title: {text: 'Kgs'},
                min: 0,
                opposite: true
              }
            ];
            var serie1 = {name: 'Unidades', data: r.unidades, type: 'spline'};
            var serie2 = {name: 'Kgs', data: r.kilos, type: 'column', yAxis: 1};
            options.series.push(serie2);
            options.series.push(serie1);
          } else {
            var serie1 = {
              data: r.cantidades,
              name: r.texto,
              type: t
            };
            options.series.push(serie1);
          }
          options.chart.renderTo = 'g'+g;
          options.title.text = title;
          options.xAxis.categories = r.nombres;
          options.subtitle.text =  r.texto;
          var chart = new Highcharts.Chart(options);
        }
      });
    };
    var initSelects = function() {
      $('#sFacturacionMes').change(function(){
        updateChart('FacturacionCliente', 'Total Facturado', $(this).val());
      }).change();
      $('#sMesDestinos').change(function(){
        updateChart('DestinosCliente', 'Destinos mas frecuentes', $(this).val());
      }).change();
      $('#sMesContactos').change(function(){
        updateChart('ContactosCliente', 'Contactos mas frecuentes', $(this).val());
      }).change();
      $('#sMesMercancia').change(function(){
        updateChart('MercanciaCliente', 'Mercancia Transportada (Und/Kg)', $(this).val());
      }).change();
    };
    return {
      init: init
    }
  }();

  LOGISTICA.terceros.clientes.init();
})();
</script>
