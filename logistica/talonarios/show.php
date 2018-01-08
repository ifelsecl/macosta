<?php
require '../../seguridad.php';
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
if (! isset($_SESSION['permisos'][TALONARIOS_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}
if (! $talonario = Talonario::find($_GET['id'])) exit('No existe el talonario');
$talonario->guias();
?>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Talonario <?= $talonario->id ?></h2>
<hr class="hr-small">
<div class="row-fluid">
  <div class="span3">
    <div class="square">
      <div class="number"><?= $talonario->inicio.'-'.$talonario->fin ?></div>
      <div class="text">Rango</div>
    </div>
  </div>
  <div class="span2">
    <div class="square">
      <div class="number"><?= $talonario->fecha_corta ?></div>
      <div class="text">Fecha Asignación</div>
    </div>
  </div>
  <div class="span6">
    <div class="square">
      <div class="number"><?= $talonario->conductor->nombre_completo ?></div>
      <div class="text">Conductor</div>
    </div>
  </div>
</div>
<div class="row-fluid">
  <div class="span5">
    <h4>En Sistema</h4>
    <table class="table table-condensed">
      <thead>
        <tr>
          <th>ID</th>
          <th>Número</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($talonario->guias as $guia) {
          echo '<tr>';
          echo '<td>'.$guia->id.'</td>';
          echo '<td>'.$guia->numero.'</td>';
          echo '</tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
  <div class="span5">
    <h4>Pendientes</h4>
    <table class="table table-condensed">
      <thead>
        <tr>
          <th>Número</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (empty($talonario->numeros_pendientes)) {
          echo '<tr class="warning"><td class="expand">No hay pendientes!</td></tr>';
        } else {
          foreach ($talonario->numeros_pendientes as $numero) {
            echo '<tr>';
            echo '<td>'.$numero.'</td>';
            echo '</tr>';
          }
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
