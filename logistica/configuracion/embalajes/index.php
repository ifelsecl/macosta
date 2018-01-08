<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][EMBALAJES_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

$sql = Embalaje::all(true);
$paging = new PHPPaging('right_content', $sql);
$paging->ejecutar();
?>
<?php if (isset($_SESSION['permisos'][EMBALAJES_CREAR])) { ?>
<button id="embalaje_crear" title="Crear un nuevo embalaje" class="btn btn-info pull-right">
  <i class="icon-plus"></i> Crear Embalaje
</button>
<?php } ?>
<h2>Embalajes</h2>
<table id="embalajes_list" class="table table-hover table-condensed table-bordered">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Descripción</th>
      <th>Tipo</th>
      <th>Editar</th>
    </tr>
  </thead>
  <tbody>
    <?php
    while ($embalaje = $paging->fetchResultado('Embalaje')) {
      echo '<tr>';
      echo '<td>'.$embalaje->id.'</td>';
      echo '<td>'.$embalaje->nombre.'</td>';
      echo '<td>'.$embalaje->descripcion.'</td>';
      echo '<td>'.$embalaje->tipo_cobro.'</td>';
      $name='id='.$embalaje->id.'&'.nonce_create_query_string($embalaje->id);
      echo '<td width="16">';
      if (isset($_SESSION['permisos'][EMBALAJES_EDITAR])) {
        echo '<button title="Editar" name="'.$name.'" class="btn editar"><i class="icon-pencil"></i></button>';
      }
      echo '</td>';
      echo '</tr>';
    }
    ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
<input type="hidden" id="pag" value="<?= $paging->numEstaPagina() ?>" />
<script>
(function() {
  var Embalaje = {
    crear: function() {
      LOGISTICA.Dialog.open('Crear Embalaje', embalajes_path+'crear.php');
    },
    editar: function() {
      LOGISTICA.Dialog.open('Editar Embalaje', embalajes_path+'editar.php?'+this.name);
    },
    init: function() {
      $('#embalaje_crear').click(this.crear);
      $('table#embalajes_list').on('click', 'button.editar', this.editar);
    }
  }
  Embalaje.init();
})();
function fn_paginar(d, u) { $('.'+d).load(u); }
</script>
