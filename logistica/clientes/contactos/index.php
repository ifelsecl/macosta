<?php
require '../seguridad.php';
$clientes = Cliente::search_contactos($_SESSION['id'], $_GET, true);
$paging = new PHPPaging('center_content', $clientes);
$paging->ejecutar();
?>
<div id="contacts__list">
  <h2>Destintarios Asociados</h2>
  <p class="muted">Edite y administre la información de sus contactos</p>
  <form action="#" class="form-inline" method="GET">
    <table>
      <thead>
        <tr>
          <td>Nombre</td>
          <td>Número Identificación</td>
          <td>Dirección</td>
          <td></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input class="input-medium" type="text" name="nombre" value="<?php if (isset($_GET['nombre'])) echo $_GET['nombre']; ?>"></td>
          <td><input class="input-medium" type="text" name="numero_identificacion" value="<?php if (isset($_GET['numero_identificacion'])) echo $_GET['numero_identificacion']; ?>"></td>
          <td><input class="input-medium" type="text" name="direccion" value="<?php if (isset($_GET['direccion'])) echo $_GET['direccion']; ?>"></td>
          <td><button id="buscar" class="btn btn-info">Buscar</button></td>
          <div class="container">
          <tbody>
          <table class="table table-hover table-bordered table-condensed">
          <td>Nota Importante: "Su mercancía en proceso de envío, será detenida sino contempla los datos correctos y/o completos"</td>
          </tbody>
          </div>       
        </tr>
      </tbody>
    </table>
  </form>
  <table class="table table-hover table-bordered table-condensed">
    <thead>
      <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>Número</th>
        <th>Nombre</th>
        <th>Ciudad</th>
        <th>Dirección</th>
        <th>Editar</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($paging->numTotalRegistros() == 0) {
        echo '<tr class="warning"><td colspan="7" class="expand">No se encontraron contactos...</td></tr>';
      } else {
        while ($contacto = $paging->fetchResultado('Cliente')) {
          $c = $contacto->numero_identificacion == '0' ? 'class="warning"' : '';
          echo '<tr '.$c.'>';
          echo  '<td>'.$contacto->id.'</td>';
          echo  '<td>'.$contacto->tipo_identificacion.'</td>';
          echo  '<td>'.$contacto->numero_identificacion_completo.'</td>';
          echo  '<td>'.$contacto->nombre_completo.'</td>';
          echo  '<td>'.$contacto->ciudad_nombre.'</td>';
          echo  '<td>'.$contacto->direccion.'</td>';
          $name = 'id='.$contacto->id.'&'.nonce_create_query_string($contacto->id);
          echo  '<td width="20" class="text-center"><button class="btn editar-contacto" name="'.$name.'"><i class="icon-pencil"></i></button></td>';
          echo '</tr>';
        }
      }
      ?>
    </tbody>
  </table>
  <?= $paging->fetchNavegacion() ?>
</div>
<script>
(function() {
  var ContactosList = function() {
    var $el = $('#contacts__list');
    var $table = $el.find('table');
    var $searchForm = $el.find('form');

    var init = function() {
      $table.on('click', 'button.editar-contacto', function(){
        LOGISTICA.Dialog.open('Editar Contacto', contactos_path+'editar.php?'+$(this).attr('name'));
      });
      initSearchForm();
    };
    var initSearchForm = function() {
      $searchForm.submit(function(e){
        e.preventDefault();
        $searchForm.find('#buscar').prop('disabled', true).text('Buscando...');
        $('#center_content').load(contactos_path+'?'+$(this).serialize());
      });
    };
    return {
      init: init
    }
  }();

  ContactosList.init();
})();
function fn_paginar(d, u){ $('.'+d).load(u); }
</script>
