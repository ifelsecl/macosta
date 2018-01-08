<?php
require "../../seguridad.php";
$usuarios = Usuario::online();
$tiempo = time();
?>
<h2>Usuarios Conectados</h2>
<p class="muted">Ultima actualización: <?= strftime('%b %d, %Y %I:%M:%S %p') ?></p>
<table class="table table-hover table-bordered">
  <thead>
    <tr>
      <th>IP</th>
      <th>Tipo</th>
      <th>Nombre</th>
      <th>Conectado hace</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if (empty($usuarios)) {
      echo '<tr class="warning"><td colspan="4" class="expand">No hay usuarios conectados o estan inactivos</td></tr>';
    } else {
      foreach ($usuarios as $usuario) {
        echo '<tr>';
        echo '<td>'.$usuario->ip.'</td>';
        echo '<td>'.$usuario->tipo.'</td>';
        echo '<td>'.$usuario->nombre.'</td>';
        $t = $tiempo - $usuario->tiempo;
        if ($t == 0) $texto = 'ahora mismo';
        elseif ($t < 60) {
          $texto = $t.' segundo';
          if($t > 1) $texto .= 's';
        } elseif ($t >= 60) {
          $texto = round($t/60).' minuto';
          if ($t > 60) $texto .= 's';
        }
        else $texto = $t.' segundos';
        echo '<td>'.$texto.'</td>';
        echo '</tr>';
      }
    }
    ?>
  </tbody>
</table>
