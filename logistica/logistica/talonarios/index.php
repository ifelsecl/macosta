<?php
require '../../seguridad.php';

if (! isset($_SESSION['permisos'][TALONARIOS_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}
$consulta = Talonario::search($_GET, 'sql');
$paging = new PHPPaging('center_content', $consulta);
$paging->ejecutar();
?>
<div class="btn-toolbar pull-right">
  <button class="btn btn-info" id="talonarios-crear">Asignar Talonario</button>
</div>
<h2>Control de Guías</h2>
<form class="form-inline" action="#" method="get" id="search-talonarios">
  <div class="input-append">
    <input class="input-small" name="numero" id="search-term" type="text" value="<?= isset($_GET['numero']) ? $_GET['numero'] : '' ?>">
    <button class="btn btn-info" id="search-button">Buscar</button>
    <button class="btn btn-info" type="button" id="refresh-button">Actualizar</button>
  </div>
</form>
<table id="talonarios-list" class="table table-hover table-bordered table-condensed">
  <thead>
    <tr>
      <th>ID</th>
      <th>Conductor</th>
      <th>Rango</th>
      <th>Fecha Entrega</th>
      <th style="width: 100px">Acción</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if ($paging->numTotalRegistros() == 0) {
      echo '<tr class="warning"><td colspan="5" class="expand">No se encontraron registros...</td></tr>';
    } else {
      while ($talonario = $paging->fetchResultado('Talonario')) {
        echo '<tr id="talonario_'.$talonario->id.'">';
        echo '<td>'.$talonario->id.'</td>';
        echo '<td>'.$talonario->conductor->nombre_completo.'</td>';
        echo '<td>'.$talonario->inicio.' - '.$talonario->fin.'</td>';
        echo '<td>'.$talonario->fecha_entrega.'</td>';
        $name = "id=".$talonario->id."&".nonce_create_query_string($talonario->id);
        echo '<td><div class="btn-group">
          <button class="btn ver" name="'.$name.'" title="Ver"><i class="icon-search"></i></button>
          <button class="btn editar" name="'.$name.'" title="Editar"><i class="icon-pencil"></i></button>
          <button class="btn btn-danger eliminar" name="'.$name.'" title="Eliminar"><i class="icon-trash"></i></button>
        </div></td>';
        echo '</tr>';
      }
    }
    ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
<script>
(function() {
  $('button#talonarios-crear').click(function() {
    cargarExtra(talonarios_path+'new.php');
  });
  $('button#refresh-button').click(function() {
    $(this).text('Actualizando...').prop('disabled', true);
    cargarPrincipal(talonarios_path+'?'+$('form#search-talonarios').serialize());
  });
  $('#talonarios-list')
    .on('click', 'button.ver', function() {
      cargarExtra(talonarios_path+'show.php?'+this.name);
    })
    .on('click', 'button.editar', function() {
      cargarExtra(talonarios_path+'edit.php?'+this.name);
    })
    .on('click', 'button.eliminar', function() {
      var t = this;
      var c = confirm('¿Eliminar?');
      if (! c) return;
      $.ajax({
        url: talonarios_path+'ajax.php?'+t.name,
        type: 'DELETE',
        success: function(response) {
          if (! response) {
            alertify.success('Talonario eliminado correctamente, actualizando...');
            cargarPrincipal(talonarios_path+'?'+$(this).serialize());
          } else {
            alertify.error(response);
          }
        }
      });
    });
  $('#search-talonarios').submit(function(e) {
    e.preventDefault();
    $('#search-button').prop('disabled', true).text('Buscando...');
    cargarPrincipal(talonarios_path+'?'+$(this).serialize());
  });
})();
</script>
