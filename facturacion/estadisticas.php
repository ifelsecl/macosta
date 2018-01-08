<?php
require "../seguridad.php";
if (! isset($_SESSION['permisos'][FACTURACION_ENTRAR])) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}
?>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Facturación | Estadísticas</h2>
<div id="facturacion__estadisticas">
  <div class="portlet">
    <div class="portlet-header">Facturación&nbsp;
      <select class="ui-widget-content" id="Facturacion">
        <option value="ACTUAL">Mes Actual</option>
        <option value="ANTERIOR">Mes Anterior</option>
        <option value="3MESES" selected="selected">Hace 3 Meses</option>
        <option value="6MESES">Hace 6 Meses</option>
        <option value="12MESES">Hace 1 Año</option>
        <option value="24MESES">Hace 2 Años</option>
      </select>
    </div>
    <div class="portlet-content">
      <div id="gFacturacion" style="min-width:300px;width:100%;min-height:250px;height:auto;margin:0 auto">
        <p class="expand"><img src="css/ajax-loader.gif" /></p>
      </div>
    </div>
  </div><!-- End Portlets Facturacion -->

  <div class="portlet">
    <div class="portlet-header">Clientes Mas Facturados&nbsp;
      <select class="ui-widget-content" id="ClientesMasFacturados">
        <option value="ACTUAL" selected="selected">Mes Actual</option>
        <option value="ANTERIOR">Mes Anterior</option>
        <option value="3MESES">Hace 3 Meses</option>
        <option value="6MESES">Hace 6 Meses</option>
        <option value="12MESES">Hace 1 Año</option>
        <option value="24MESES">Hace 2 Años</option>
      </select>
    </div>
    <div class="portlet-content">
      <div id="gClientesMasFacturados" style="min-width:300px;width:100%;min-height:350px;height:auto;margin:0 auto">
        <p class="expand"><img src="css/ajax-loader.gif" /></p>
      </div>
    </div>
  </div><!-- End Portlets Clientes -->
</div>
<script>
(function() {
  LOGISTICA.facturacion.estadisticas = function() {
    var $el = $('#facturacion__estadisticas');

    var clientsChartOptions = {
      chart: {type: 'bar', renderTo: 'gClientesMasFacturados'},
      title: {text: 'Clientes Mas Facturados'},
      subtitle: {text: ''},
      plotOptions: {
        column: {
          pointPadding: 0.2,
          borderWidth: 0
        }
      },
      xAxis: {categories: []},
      yAxis: {min: 0, title: {text: ''}},
      credits: {enabled: false},
      legend: {enabled: false},
      tooltip: {
        formatter: function() {
          return '<b>'+ this.x +'</b><br>$ '+ Highcharts.numberFormat(this.y, 0, ',', '.') + ' COP';
        }
      },
      series: []
    };

    var invoicingChartOptions = {
      chart: {type: 'column', renderTo: 'gFacturacion'},
      title: {text: 'Facturación Mensual'},
      yAxis: {
        stackLabels: {
          enabled: true,
          style: {fontWeight: 'bold'}
        }
      },
      plotOptions: {
        column: {stacking: 'normal'}
      }
    };
    var invoicingChartOptions = $.extend(true, {}, clientsChartOptions, invoicingChartOptions);

    var init = function() {
      initPortlet($el.find(".portlet"));
      bindSelects($el.find('select'));
    };

    var initPortlet = function(el) {
      el
        .addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
        .find(".portlet-header").addClass("ui-widget-header ui-corner-all");
    };

    var drawChart = function(g, month) {
      $.ajax({
        url: 'ajax.php?accion='+g+'&mes='+month,
        type: 'POST', dataType: 'json',
        success: function(r) {
          var chartOptions = g == 'Facturacion' ? invoicingChartOptions : clientsChartOptions;
          var serie = {name: r.texto, data: r.cantidades};
          chartOptions.series = [serie];
          chartOptions.xAxis.categories = r.nombres;
          chartOptions.subtitle.text =  r.texto;

          new Highcharts.Chart(chartOptions);
        }
      });
    };

    var bindSelects = function(selects) {
      selects.change(function() {
        drawChart(this.id, $(this).val());
      }).change();
    };

    return {
      init: init
    }
  }();
  LOGISTICA.facturacion.estadisticas.init();
})();
</script>
