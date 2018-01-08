<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  include Logistica::$root.'mensajes/id.php';
  exit;
}
if (! isset($_SESSION['permisos'][RUTAS_LOCALES_VER])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

if (! $ruta_local = RutaLocal::find($_GET['id'])) exit('No existe la ruta local');
?>
<div class="pull-right">
  <?php if(isset($_SESSION['permisos'][RUTAS_LOCALES_IMPRIMIR])) { ?>
  <a class="btn btn-info" target="_blank" href="logistica/rutas_locales/imprimir?id=<?= $ruta_local->id . '&' . nonce_create_query_string($ruta_local->id) ?>">
    <i class="icon-print"></i> Imprimir
  </a>
  <?php } ?>
  <button id="regresar" class="btn btn-success" onclick="regresar()">Regresar</button>
</div>
<h2>Ruta Local <?= $ruta_local->id ?></h2>
<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#tab_info">Información</a></li>
    <li><a data-toggle="tab" href="#tab_history">Historial</a></li>
  </ul>
  <div class="tab-content">
    <div id="tab_info" class="tab-pane active">
      <table>
        <tr>
          <td><b>Ciudad:</b></td>
          <td><?= $ruta_local->ciudad_nombre ?></td>
        </tr>
        <tr>
          <td><b>Fecha:</b></td>
          <td><?= $ruta_local->fecha_corta ?></td>
        </tr>
      </table>
      <div class="row-fluid">
        <div class="span4 thumbnail">
          <div class="caption">
            <h4>Conductor</h4>
            <table>
              <tr>
                <td><b>Nombre:</b></td>
                <td><?= $ruta_local->conductor()->nombre_completo ?></td>
              </tr>
              <tr>
                <td><b>Cédula:</b></td>
                <td><?= $ruta_local->conductor->numero_identificacion ?></td>
              </tr>
              <tr>
                <td><b>Ciudad:</b></td>
                <td><?= $ruta_local->conductor->ciudad_nombre ?></td>
              </tr>
              <tr>
                <td><b>Licencia:</b></td>
                <td><?= $ruta_local->conductor->categorialicencia ?></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="span4 thumbnail">
          <div class="caption">
            <h4>Vehículo</h4>
            <?php if ($ruta_local->vehiculo_empresa()) { ?>
            <table>
              <tr>
                <td><b>Placa:</b></td>
                <td><?= $ruta_local->vehiculo()->placa ?></td>
              </tr>
              <tr>
                <td><b>Marca:</b></td>
                <td><?= $ruta_local->vehiculo->marca()->nombre ?></td>
              </tr>
              <tr>
                <td><b>Modelo:</b></td>
                <td><?= $ruta_local->vehiculo->modelo ?></td>
              </tr>
              <tr>
                <td><b>No Serie:</b></td>
                <td><?= $ruta_local->vehiculo->serie ?></td>
              </tr>
            </table>
            <?php } else { ?>
            <table>
              <tr>
                <td><b>Placa:</b></td>
                <td><?= $ruta_local->placa_vehiculo_2 ?></td>
              </tr>
              <tr>
                <td colspan="2" class="text-center">
                  <span class="label label-warning">Vehículo no asociado con la empresa</span>
                </td>
              </tr>
            </table>
            <?php } ?>
          </div>
        </div>
      </div>
      <table style="margin-top: 10px;" class="table table-bordered table-hover table-condensed">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Contacto</th>
            <th>Destino</th>
            <th>Unds</th>
            <th>Vlr Merc.</th>
            <th>Flete al Cobro</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $ruta_local->guias();
          if ( empty($ruta_local->guias) ) {
            echo '<tr><td class="expand" colspan="7">No tiene guias...</td></tr>';
          }else{
            $total = 0;
            $unidades = 0;
            foreach ($ruta_local->guias as $guia) {
              echo '<tr>';
              echo '<td>'.$guia->id.'</td>';
              echo '<td>'.$guia->cliente_nombre_completo.'</td>';
              echo '<td>'.$guia->contacto_nombre_completo.'</td>';
              echo '<td>'.$guia->contacto_ciudad_nombre.'</td>';
              echo '<td>'.$guia->unidades.'</td>';
              echo '<td>'.number_format($guia->valordeclarado).'</td>';
              $flete = 0;
              $unidades += $guia->unidades;
              if ($guia->formapago == 'FLETE AL COBRO') {
                $flete = $guia->valorseguro+$guia->total;
                $total += $flete;
              }
              echo '<td>'.number_format($flete).'</td>';
              echo '</tr>';
            }
            echo '<tr>
            <td colspan="4" class="text-right">Total</td>
            <td>'.$unidades.'</td>
            <td></td>
            <td><b>'.number_format($total).'</b></td>
            </tr>';
          }
          ?>
        </tbody>
      </table>
      <div><b>Observaciones</b>: <?= $ruta_local->observaciones ?></div>
    </div>
    <div id="tab_history" class="tab-pane">
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
          $ruta_local->history();
          if( empty($ruta_local->history) ) {
            echo '<tr class="warning"><td colspan="3" class="expand">No se han realizado modificaciones</td></tr>';
          }else{
            foreach ($ruta_local->history as $h) {
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
