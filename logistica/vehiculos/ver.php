<?php
require "../../seguridad.php";
if (! isset($_GET['placa']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['placa'])) {
  include Logistica::$root.'mensajes/id.php';
  exit;
}
if (! isset($_SESSION['permisos'][CAMIONES_VER])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (! $vehiculo = Vehiculo::find($_GET['placa'])) exit('No existe el vehículo');
$mantenimientos = $vehiculo->mantenimientos();
$history = $vehiculo->history();
?>
<div id="vehiculo__show">
  <button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
  <h2>Vehículo <?= $vehiculo->placa ?></h2>
  <div class="tabbable">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#tab_info">Información</a></li>
      <li><a data-toggle="tab" href="#tab_mantenimientos">Mantenimientos</a></li>
      <li><a data-toggle="tab" href="#tab_history">Historial</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="tab_info">
        <table cellspacing="2" cellpadding="4">
          <tr>
            <td><b>Placa Semiremolque</b></td>
            <td><?= $vehiculo->placa_semiremolque ?></td>
            <td><b>Marca</b></td>
            <td colspan="2"><?= $vehiculo->marca_nombre ?></td>
          </tr>
          <tr>
            <td><b>Línea</b></td>
            <td><?= $vehiculo->linea_nombre ?></td>
            <td><b>Color</b></td>
            <td colspan="3"><?= $vehiculo->color_nombre ?></td>
          </tr>
          <tr>
            <td><b>Modelo</b></td>
            <td><?= $vehiculo->modelo ?></td>
            <td><b>Modelo Repotenciado A</b></td>
            <td><?= $vehiculo->modelo_repotenciado ?></td>
          </tr>
          <tr>
            <td><b>Número de la serie</b></td>
            <td><?= $vehiculo->serie ?></td>
            <td><b>Carrocería</b></td>
            <td><?= $vehiculo->carroceria_nombre ?></td>
          </tr>
          <tr>
            <td><b>Configuración</b></td>
            <td><?= $vehiculo->configuracion_nombre ?></td>
            <td><b>Peso</b></td>
            <td><?= number_format($vehiculo->peso)?> kg</td>
          </tr>
          <tr>
            <td><b>Registro de carga</b></td>
            <td><?= $vehiculo->registro ?></td>
            <td><b>Capacidad de carga</b></td>
            <td><?= number_format($vehiculo->capacidadcarga)?> kg</td>
          </tr>
          <tr>
            <td><b>Aseguradora</b></td>
            <td colspan="3">
              <?= $vehiculo->nitaseguradora." - ".$vehiculo->aseguradora_nombre ?>
            </td>
          </tr>
          <tr>
            <td><b>Propietario</b></td>
            <td colspan="3">
              <?= $vehiculo->propietario()->nombre_completo ?>
            </td>
          </tr>
          <tr>
            <td><b>Tenedor</b></td>
            <td colspan="3"><?= $vehiculo->tenedor()->nombre_completo ?></td>
          </tr>
          <tr>
            <td><b>Kilometraje Inicial</b></td>
            <td><?= number_format($vehiculo->km_inicial)?></td>
            <td><b>Kilometraje Actual</b></td>
            <td><?= number_format($vehiculo->km_actual)?></td>
          </tr>
          <tr>
            <td><b>Fecha Matricula</b></td>
            <td><?= $vehiculo->fecha_matricula ?></td>
          </tr>
        </table>
        <div class="row-fluid">
          <fieldset class="span6 table">
            <legend>SOAT</legend>
            <table class="table table-condensed">
              <tr>
                <td>Número:</td>
                <td><?= $vehiculo->soat ?></td>
              </tr>
              <tr>
                <td>Fecha vencimiento:</td>
                <td>
                <?= strftime('%B %d, %Y',strtotime($vehiculo->f_venc_soat)) ?>
                </td>
              </tr>
            </table>
          </fieldset><!-- SOAT -->
          <fieldset class="span6 table">
            <legend>Seguro</legend>
            <table class="table">
              <tr>
                <td>Número:</td>
                <td><?= $vehiculo->num_seguro ?></td>
              </tr>
              <tr>
                <td>Fecha vencimiento:</td>
                <td><?= strftime('%B %d, %Y',strtotime($vehiculo->f_venc_seguro)) ?></td>
              </tr>
            </table>
          </fieldset>
        </div>
        <div class="row-fluid">
          <fieldset class="span6 table">
            <legend>Tarjeta de Operación</legend>
            <table class="table">
              <tr>
                <td>Número:</td>
                <td><?= $vehiculo->t_operacion ?></td>
              </tr>
              <tr>
                <td>Fecha afiliacón:</td>
                <td><?= strftime('%B %d, %Y', strtotime($vehiculo->fecha_afiliacion)) ?></td>
              </tr>
              <tr>
                <td>Fecha vencimiento:</td>
                <td><?= strftime('%B %d, %Y', strtotime($vehiculo->f_venc_toperacion)) ?></td>
              </tr>
            </table>
          </fieldset>
          <fieldset class="span6 table">
            <legend>Revisión Técnico Mecánica</legend>
            <table class="table">
              <tr>
                <td>Número:</td>
                <td><?= $vehiculo->tecnico_meca ?></td>
              </tr>
              <tr>
                <td>Fecha vencimiento:</td>
                <td>
                <?= strftime('%B %d, %Y',strtotime($vehiculo->f_venc_tmec)) ?>
                </td>
              </tr>
            </table>
          </fieldset>
        </div>
      </div>
      <div class="tab-pane" id="tab_mantenimientos">
        <div class="btn-toolbar">
          <button class="btn btn-info" id="registrar-mantenimiento" name="<?= $vehiculo->to_param() ?>">Registrar mantenimiento</button>
        </div>
        <table id="vehiculo-mantenimientos" class="table table-hover table-condensed table-bordered">
          <thead>
            <tr>
              <th>Mantenimiento</th>
              <th>Fecha</th>
              <th>Precio</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($mantenimientos as $m) {
              echo $m;
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="tab-pane" id="tab_history">
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
            if (empty($vehiculo->history)) {
              echo '<tr class="warning"><td colspan="3" class="expand">No se han realizado modificaciones</td></tr>';
            } else {
              foreach ($history as $h) {
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
  LOGISTICA.logistica.mantenimientos = {
    $el: $('#vehiculo__show'),
    init: function() {
      this.$el.find('#registrar-mantenimiento').click(function() {
        LOGISTICA.Dialog.open('Agregar Mantenimiento', vehiculos_path+'mantenimientos/crear.php?'+this.name);
      });
      this.$el.find('table#vehiculo-mantenimientos').on('click', '.editar', function() {
        LOGISTICA.Dialog.open('Editar Mantenimiento', vehiculos_path+'mantenimientos/editar.php?'+this.name);
      });
    }
  };
  LOGISTICA.logistica.mantenimientos.init();
})();
</script>
