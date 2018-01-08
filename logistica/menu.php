<?php
require 'seguridad.php';
$formato_fecha = '%b %d, %Y';

$preguias           = Guia::count_by_status(7);
$bodegas            = Guia::count_by_status(1);
$transito           = Guia::count_by_status(3);
$ultimasGuias       = Guia::count_from(date('Y-m-d', strtotime('-2 day')));
$conductores        = Conductor::expiring();
$vecimientoSOAT     = Vehiculo::expiring_by_type('f_venc_soat');
$vecimientoTecnoMec = Vehiculo::expiring_by_type('f_venc_tmec');

$hoy = date("Y-m-d");
?>
<script>
$(function() {
  $(".column").sortable({connectWith: ".column", forceHelperSize: true, forcePlaceholderSize: true});
  $('.column .portlet-content').bind('click.sortable mousedown.sortable',function(e){e.stopImmediatePropagation();});
  $(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
    .find(".portlet-header")
      .addClass( "ui-widget-header ui-corner-all" )
      .prepend( "<span title='Abrir/Cerrar' class='ui-icon ui-icon-minusthick'></span>")
      .end()
    .find(".portlet-content");
  $(".portlet-header .ui-icon").click(function() {
    $(this).toggleClass("ui-icon-minusthick").toggleClass("ui-icon-plusthick");
    $(this).parents(".portlet:first").find(".portlet-content").toggle();
  });
  function ActualizarGrafico(g, title, mes){
    $.ajax({
      url: 'ajax.php?accion='+g+'&mes='+mes,
      type: 'POST', dataType: 'json',
      success: function(r){
        var options = {
          chart: {renderTo: ''},
          title: {text: ''},
          subtitle: {text: ''},
          xAxis: {
            categories: []
          },
          yAxis: {min: 0,title: {text: ''},},
          credits: {enabled: false},
          legend: {enabled:false},
          tooltip: {
            formatter: function() {
              return '<b>'+ this.x +'</b><br>'+ Highcharts.numberFormat(this.y,0,',','.');
            }
          },
          plotOptions: {
            column: {
              pointPadding: 0.2,
              borderWidth: 0
            }
          },
          series: []
        };
        options.chart.renderTo = 'g'+g;
        options.title.text = title;
        var series = {data: [], type:'bar'};
        if(g=='Mercancia'){
          options.tooltip.formatter=function() {
            return '<b>'+this.x +'</b><br>'+ Highcharts.numberFormat(this.y,0,',','.') + (this.series.name == 'Unidades' ? ' und' : ' kgs');
          }
          options.legend.enabled=true;
          options.chart.zoomType='xy';
          options.xAxis.categories=r.nombres;
          options.subtitle.text= r.texto;
          options.yAxis=[{ // Primary yAxis
            min: 0, title: {text: 'Und'}
          }, { // Secondary yAxis
            title: {text: 'Kgs'},min: 0,
            opposite: true
          }];
          series.name='Unidades';
          series.type='spline';
          series.data=r.unidades;
          var serie2 = {data: []};
          serie2.name='Kgs';
          serie2.yAxis=1;
          serie2.type='column';
          serie2.data=r.kilos;
          options.series.push(serie2);
          options.series.push(series);
        }else{
          options.xAxis.categories=r.nombres;
          options.subtitle.text= r.texto;
          series.name=r.texto;
          series.data=r.cantidades;
          options.series.push(series);
        }
        var chart = new Highcharts.Chart(options);
      }
    });
  }

  $('#sMesManifiestos').change(function(){
    ActualizarGrafico('Manifiestos', 'Destinos mas frecuentes', $(this).val());
  }).change();
  $('#sMesGuias').change(function(){
    ActualizarGrafico('Guias', 'Destinos mas frecuentes', $(this).val());
  }).change();
  $('#sMesClientes').change(function(){
    ActualizarGrafico('Clientes', 'Clientes mas frecuentes', $(this).val());
  }).change();
  $('#sMesMercancia').change(function(){
    ActualizarGrafico('Mercancia', 'Mercancia Transportada (Und/Kg)', $(this).val());
  }).change();
});
</script>
<div class="alertasDashboard">
  <div class="column">

    <div class="portlet">
      <div class="portlet-header">Guías</div>
      <div class="portlet-content">
        <ul>
          <li>
            <div class="square">
              <div class="number"><?= $ultimasGuias ?></div>
              <div class="text">Últimas (2 días)</div>
            </div>
          </li>
          <li>
            <div class="square">
              <div class="number"><?= number_format($bodegas) ?></div>
              <div class="text">Bodega</div>
            </div>
          </li>
          <li>
            <div class="square">
              <div class="number"><?= number_format($transito) ?></div>
              <div class="text">Transito</div>
            </div>
          </li>
          <li>
            <div class="square">
              <div class="number"><?= number_format($preguias) ?></div>
              <div class="text">Preguías</div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="portlet">
      <div class="portlet-header">Licencias</div>
      <div class="portlet-content">
        <table class="table table-condensed table-hover table-bordered">
        <thead>
          <tr>
            <th>Licencia</th>
            <th>Conductor</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($conductores as $conductor) {
            $venc = $conductor->vencimientopase <= $hoy ? 'class="anulado"' : '';
            echo '<tr '.$venc.'>
            <td>'.$conductor->categorialicencia.'</td>
            <td>'.$conductor->nombre." ".$conductor->primer_apellido.'</td>
            <td>'.strftime($formato_fecha, strtotime($conductor->vencimientopase)).'</td>
            </tr>';
          }
          ?>
        </tbody>
      </table>
      </div>
    </div>

    <div class="portlet">
      <div class="portlet-header">Revisión Técnico Mecánica</div>
      <div class="portlet-content">
        <table class="table table-condensed table-hover table-bordered">
          <thead>
          <tr>
            <th>Numero</th>
            <th>Placa</th>
            <th>Fecha</th>
          </tr>
          </thead>
          <tbody>
          <?php
          foreach ($vecimientoTecnoMec as $vehiculo) {
            $v = $vehiculo->f_venc_tmec <= $hoy ? 'class="anulado"' : '';
            echo '<tr '.$v.'>
            <td>'.$vehiculo->tecnico_meca.'</td>
            <td align="center">'.$vehiculo->placa.'</td>
            <td align="center">'.strftime($formato_fecha,strtotime($vehiculo->f_venc_tmec)).'</td>
          </tr>';
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="portlet">
      <div class="portlet-header">Guías: Destinos Frecuentes&nbsp;
        <select class="input-medium" id="sMesGuias">
          <option value="ACTUAL">Mes Actual</option>
          <option value="ANTERIOR">Mes Anterior</option>
          <option value="3MESES">Hace 3 Meses</option>
          <option value="6MESES">Hace 6 Meses</option>
          <option value="12MESES">Hace 1 Año</option>
          <option value="2014ANO">Año 2014</option>
        </select>
      </div>
      <div class="portlet-content">
        <div id="gGuias" style="min-width:300px;width:100%;min-height:350px;height:auto;margin:0 auto">
          <p class="expand"><img src="css/ajax-loader.gif" /></p>
        </div>
      </div>
    </div>

    <div class="portlet">
      <div class="portlet-header">Mantenimientos</div>
      <div class="portlet-content">
        <table class="table table-condensed table-hover table-bordered">
          <thead>
            <tr>
              <th width="60">Placa</th>
              <th>KM Inicial</th>
              <th>KM Actual</th>
              <th>Mantenimiento</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query="SELECT * FROM mantenimientos";
            $result=DBManager::execute($query);
            if (mysql_num_rows($result)==0) {
              echo '<tr><td colspan="4" style="padding: 6px;">No se han configurado mantenimientos, vaya a Logistica->Camiones->Configurar</td></tr>';
            }else{
              while ($r=mysql_fetch_assoc($result)) {
                $mantenimientos[]=$r;
              }
              $query="SELECT placa, km_inicial, km_actual, km_actual-km_inicial kilometraje
                  FROM camiones
                  WHERE km_inicial!=0 AND km_actual!=0";
              $result=DBManager::execute($query);
              if(mysql_num_rows($result)==0){
                echo '<tr><td colspan="4" class="expand">No se encontraron mantenimientos pendientes...</td></tr>';
              }else{
                while ($r=mysql_fetch_assoc($result)) {
                  $s='';
                  foreach ($mantenimientos as $m) {
                    if ($m['kilometraje']<=$r['kilometraje']) {
                      $s.=$m['nombre'].', ';
                    }
                  }
                  $s=substr($s, 0, -2);
                  echo '<tr><td align="center">'.$r['placa'].'</td>';
                  echo '<td align="center">'.number_format($r['km_inicial']).'</td>';
                  echo '<td align="center">'.number_format($r['km_actual']).'</td>';
                  echo '<td>'.$s.'</td></tr>';
                }
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </div><!-- End Portlet Mantenimientos -->

  </div><!-- End Column -->

  <div class="column">

    <div class="portlet">
      <div class="portlet-header">Vencimientos</div>
      <div class="portlet-content">
        <ul>
          <li>
            <div class="square">
              <div class="number"><?= count($conductores) ?></div>
              <div class="text">Licencia</div>
            </div>
          </li>
          <li>
            <div class="square">
              <div class="number"><?= count($vecimientoSOAT) ?></div>
              <div class="text">SOAT</div>
            </div>
          </li>
          <li>
            <div class="square">
              <div class="number"><?= count($vecimientoTecnoMec) ?></div>
              <div class="text">Técnico Mecánica</div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="portlet">
      <div class="portlet-header">SOAT</div>
      <div class="portlet-content">
        <table class="table table-condensed table-hover table-bordered">
          <thead>
          <tr>
            <th>Numero</th>
            <th>Placa</th>
            <th>Fecha</th>
          </tr>
          </thead>
          <tbody>
          <?php
          foreach ($vecimientoSOAT as $vehiculo) {
            $v = $vehiculo->f_venc_soat <= $hoy ? 'class="anulado"' : '';
            echo '<tr '.$v.'>';
            echo '<td>'.$vehiculo->soat.'</td>';
            echo '<td align="center">'.$vehiculo->placa.'</td>';
            echo '<td align="center">'.strftime($formato_fecha, strtotime($vehiculo->f_venc_soat)).'</td>';
            echo '</tr>';
          }
          ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="portlet">
      <div class="portlet-header">Mercancia Transportada&nbsp;
        <select class="input-medium" id="sMesMercancia">
          <option value="ACTUAL">Mes Actual</option>
          <option value="ANTERIOR">Mes Anterior</option>
          <option value="3MESES" selected="selected">Hace 3 Meses</option>
          <option value="6MESES">Hace 6 Meses</option>
          <option value="12MESES">Hace 1 Año</option>
          <option value="2014ANO">Año 2014</option>          
        </select>
      </div>
      <div class="portlet-content">
        <div id="gMercancia" style="min-width:300px;width:100%;min-height:250px;height:auto;margin:0 auto">
          <p class="expand"><img src="css/ajax-loader.gif" /></p>
        </div>
      </div>
    </div>

    <div class="portlet">
      <div class="portlet-header">Manifiestos: Destinos Frecuentes&nbsp;
        <select class="input-medium" id="sMesManifiestos">
          <option value="ACTUAL">Mes Actual</option>
          <option value="ANTERIOR">Mes Anterior</option>
          <option value="3MESES">Hace 3 Meses</option>
          <option value="6MESES">Hace 6 Meses</option>
          <option value="12MESES">Hace 1 Año</option>
        </select>
      </div>
      <div class="portlet-content">
        <div id="gManifiestos" style="min-width:300px;width:100%;min-height:350px;height:auto;margin:0 auto">
          <p class="expand"><img src="css/ajax-loader.gif" /></p>
        </div>
      </div>
    </div>

    <div class="portlet">
      <div class="portlet-header">Clientes Mas Frecuentes&nbsp;
        <select class="input-medium" id="sMesClientes">
          <option value="ACTUAL" selected="selected">Mes Actual</option>
          <option value="ANTERIOR">Mes Anterior</option>
          <option value="3MESES">Hace 3 Meses</option>
          <option value="6MESES">Hace 6 Meses</option>
          <option value="12MESES">Hace 1 Año</option>
        </select>
      </div>
      <div class="portlet-content">
        <div id="gClientes" style="min-width:300px;width:100%;min-height:350px;height:auto;margin:0 auto">
          <p class="expand"><img src="css/ajax-loader.gif" /></p>
        </div>
      </div>
    </div><!-- End Portlets Clientes -->

  </div><!-- End Column -->
</div>
