<?php
require '../seguridad.php';
$formato_fecha = '%b %d, %Y';
$img = '<p class="expand"><img src="../css/ajax-loader.gif" /></p>';
?>
<style>
.portlet .img{
  min-width: 300px;
  width: 100%;
  min-height: 400px;
  height: auto;
  margin: 0 auto
}
</style>
<script>
$(function() {
  $(".column").sortable({connectWith: ".column", forceHelperSize:true, forcePlaceholderSize:true});
  $(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
    .find(".portlet-header")
      .addClass("ui-widget-header ui-corner-all")
      .prepend("<span title='Abrir/Cerrar' class='ui-icon ui-icon-minusthick'></span>")
      .end()
    .find(".portlet-content");
  $(".portlet-header .ui-icon").click(function() {
    $(this).toggleClass("ui-icon-minusthick").toggleClass("ui-icon-plusthick");
    $(this).parents(".portlet:first").find(".portlet-content").toggle();
  });
  function ActualizarGrafico(g, mes, title){
    var options = {
      chart: {renderTo: ''},
      title: {text: title},
      subtitle: {text: ''},
      xAxis: {categories: []},
      yAxis: {min: 0, title: {text: ''}},
      credits: {enabled: false},
      legend: {enabled: false},
      tooltip: {
        formatter: function() {
          return '<b>'+ this.x +'</b><br>'+ Highcharts.numberFormat(this.y, 0, ',', '.');
        }
      },
      plotOptions: {column: {pointPadding: 0.2, borderWidth: 0}}, series: []
    };
    $.ajax({
      url: 'ajax.php?accion='+g+'&mes='+mes,
      type: 'POST', dataType: 'json',
      success: function(r) {
        var series = {data: [], name: '', type: 'bar'};
        if (g == 'Mercancia') {
          options.tooltip.formatter = function() {
            return '<b>'+this.x +'</b><br>'+ Highcharts.numberFormat(this.y,0,',','.') + (this.series.name == 'Unidades' ? ' und' : ' kgs');
          }

          options.legend.enabled = true;
          options.chart.zoomType = 'xy';
          options.xAxis.categories = r.nombres;
          options.subtitle.text =  r.texto;
          options.yAxis = [{ // Primary yAxis
            min: 0, title: {text: 'Und'}
          }, { // Secondary yAxis
            title: {text: 'Kgs'},
            min: 0,
            opposite: true
          }];
          series.name = 'Unidades';
          series.type = 'spline';
          series.data = r.unidades;
          var serie2  =  {data: []};
          serie2.name = 'Kgs';
          serie2.yAxis = 1;
          serie2.type = 'column';
          serie2.data = r.kilos;
          options.series.push(serie2);
          options.series.push(series);
        } else {
          series.data = r.cantidades;
          series.name = r.texto;
          options.series.push(series);
        }

        options.chart.renderTo = 'g'+g;
        options.xAxis.categories = r.nombres;
        options.subtitle.text = r.texto;
        var chart = new Highcharts.Chart(options);
      }
    });
  }

  $('#sMesDestinos').change(function(){
    ActualizarGrafico('Destinos',$(this).val(), 'Destinos mas frecuentes');
  }).change();
  $('#sMesContactos').change(function(){
    ActualizarGrafico('Contactos',$(this).val(), 'Contactos mas frecuentes');
  }).change();
  $('#sMesMercancia').change(function(){
    ActualizarGrafico('Mercancia',$(this).val(), 'Mercancia Transportada (Und/Kg)');
  }).change();
});
</script>
<div class="alertasDashboard">
  <div class="column">

    <div class="portlet">
      <div class="portlet-header">Contactos Mas Frecuentes&nbsp;
        <select class="input-medium" id="sMesContactos">
          <option value="ACTUAL">Mes Actual</option>
          <option value="ANTERIOR">Mes Anterior</option>
          <option value="3MESES">Hace 3 Meses</option>
          <option value="6MESES">Hace 6 Meses</option>
          <!--<option value="12MESES">Hace 1 Año</option>-->
        </select>
      </div>
      <div class="portlet-content">
        <div id="gContactos" class="img"><?= $img ?></div>
      </div>
    </div><!-- End Portlets Clientes -->

  </div><!-- End Column -->

  <div class="column">

    <div class="portlet">
      <div class="portlet-header">Mercancía Transportada&nbsp;
        <select class="input-medium" id="sMesMercancia">
          <option value="ACTUAL">Mes Actual</option>
          <option value="ANTERIOR">Mes Anterior</option>
          <option value="3MESES">Hace 3 Meses</option>
          <option value="6MESES">Hace 6 Meses</option>
          <option value="12MESES">Hace 1 Año</option>
        </select>
      </div>
      <div class="portlet-content">
        <div id="gMercancia" class="img"><?= $img ?></div>
      </div>
    </div>

    <div class="portlet">
      <div class="portlet-header">Destinos Mas Frecuentes&nbsp;
        <select class="input-medium" id="sMesDestinos">
          <option value="ACTUAL">Mes Actual</option>
          <option value="ANTERIOR">Mes Anterior</option>
          <option value="3MESES">Hace 3 Meses</option>
          <option value="6MESES">Hace 6 Meses</option>
          <!--<option value="12MESES">Hace 1 Año</option>-->
        </select>
      </div>
      <div class="portlet-content">
        <div id="gDestinos" class="img"><?= $img ?></div>
      </div>
    </div>

  </div>
</div>
