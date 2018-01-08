<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CAMIONES_CONFIGURAR_MANTENIMIENTOS])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}
$mantenimientos = Mantenimiento::all(true);
?>
<div class="btn-toolbar pull-right">
  <button id="crear_mantenimiento" class="btn btn-info">Crear Mantenimiento</button>
  <button id="regresar" class="btn btn-success" onclick="regresar()">Regresar</button>
</div>
<h2>Mantenimientos</h2>
<table class="table table-hover table-condensed table-bordered">
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Kilometraje</th>
      <th>Editar</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if (empty($mantenimientos)) {
      echo '<tr><td colspan="3" class="expand">No se encontraron mantenimientos...</td></tr>';
    } else {
      foreach ($mantenimientos as $mantenimiento) {
        echo '<tr><td>'.$mantenimiento->nombre.'</td>';
        echo '<td>'.number_format($mantenimiento->kilometraje).'</td>';
        $name = 'id='.$mantenimiento->id.'&'.nonce_create_query_string($mantenimiento->id);
        echo '<td align="center" width="16"><button class="btn editar_mantenimiento" name="'.$name.'"><i class="icon-pencil"></i></button></td>';
        echo '</tr>';
      }
    }
    ?>
  </tbody>
</table>
<script>
(function(){
  $("#crear_mantenimiento").click(function() {
    LOGISTICA.Dialog.open('Agregar Mantenimiento', mantenimientos_path+'crear.php');
  });
  $(".editar_mantenimiento").click(function(){
    LOGISTICA.Dialog.open('Editar Mantenimiento', mantenimientos_path+'editar.php?'+this.name);
  });
})();
</script>
