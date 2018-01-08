<?php
$raiz = "../../";
require '../seguridad.php';
require_once $raiz.'php/Nonce.inc.php';

$puede_ver      = true;
$puede_imprimir = true;
$puede_editar   = true;

$consulta = Guia::all_by_cliente($_SESSION['id'], $_GET, true);
$paging = new PHPPaging('center_content', $consulta);
$paging->ejecutar();
?>
<div class="pull-right">
  <button id="crear" class="btn btn-info" title="Crear una nueva guía"><i class="icon-plus"></i> Crear Guía</button>
  <button id="informe" class="btn btn-success" title="Generar informe de guías"><i class="icon-file-alt"></i> Informe</button>
</div>
<form action="#" method="post" id="BuscarGuias">
  <fieldset>
    <legend>Buscar</legend>
    <input type="hidden" id="pagina" name="pagina" value="<?= $paging->numEstaPagina() ?>" />
    <input type="hidden" name="buscar" value="si" />
    <table cellpadding="0">
      <tr>
        <td>Número:</td>
        <td><input type="text" name="id" value="<?php if (isset($_GET['id'])) echo $_GET['id'] ?>" /></td>
        <td rowspan="5" valign="middle">
          <button type="submit" id="buscar" class="btn btn-info"><i class="icon icon-search"></i> Buscar</button>
          <button type="button" id="actualizar" class="btn btn-default"><i class="icon icon-refresh"></i> Actualizar</button>
        </td>
      </tr>
      <tr>
        <td>Número Anterior:</td>
        <td><input type="text" name="no_anterior" value="<?php if (isset($_GET['no_anterior'])) echo $_GET['no_anterior'] ?>" /></td>
      </tr>
      <tr>
        <tr>
        <td>Fecha:</td>
        <td><input type="text" class="input-small" id="fecha" name="fecha" value="<?php if (isset($_GET['fecha'])) echo $_GET['fecha'] ?>" /></td>
      </tr>
      <tr>
        <td>Destinatario:</td>
        <td><input type="text" name="contacto" value="<?php if (isset($_GET['contacto'])) echo $_GET['contacto'] ?>" /></td>
      </tr>
      <tr>
        <td>No. Documento:</td>
        <td><input type="text" name="documento" value="<?php if (isset($_GET['documento'])) echo $_GET['documento'] ?>" /></td>
      </tr>
    </table>
  </fieldset>
</form>
<form id="form_imprimir" action="guias/imprimir" target="_blank" method="post">
  <button class="btn btn-info pull-right" style="margin:0 5px 3px 0;" type="submit" name="imprimir" id="imprimir">
    <i class="icon-print"></i> Crear relación con selección
  </button>
  <div class="clearfix"></div>
  <table id="guias-list" class="table table-hover table-condensed table-bordered">
    <thead>
      <tr>
        <th><abbr title="Seleccionar para imprimir">Imp</abbr></th>
        <th>Creada</th>
        <th>Número</th>
        <th title="Ciudad destino">Destino</th>
        <th>Destinatario</th>
        <th style="width: 100px">Estado</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
    <?php
    if ($paging->numTotalRegistros() == 0) {
      echo '<tr><td colspan="8" class="expand">No se encontraron guías...</td></tr>';
    } else {
      $hace_3_dias  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-3, date("Y")));
      $hace_2_dias  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-2, date("Y")));
      $hoy      = date("Y-m-d");
      while ($guia = $paging->fetchResultado('Guia')) {
        $c = $guia->idestado == 6 ? 'anulado' : '';
        echo '<tr class="'.$c.'">';
        /*if ( $guia->impresa=='no' )
          echo '<td><input class="imprimir" type="checkbox" name="guias[]" value="'.$guia->id.'"></td>';
        else
          echo '<td></td>';
        */
        if ($guia->fecha_recibido_mercancia == $hoy) {
          echo '<td><input class="imprimir" type="checkbox" name="guias[]" value="'.$guia->id.'"></td>';
        } else {
          echo '<td></td>';
        }

        $s = '';
        if ( $guia->idestado == 1 or $guia->idestado == 7 ) {
          if ($guia->fecha_recibido_mercancia <= $hace_3_dias) {
            $s = 'background-color: red;';
          } elseif ($guia->fecha_recibido_mercancia>=$hace_2_dias and $guia->fecha_recibido_mercancia<$hoy) {
            $s = 'background-color: yellow;';
          } else {
            $s = 'background-color: green;';
          }
        }
        if ($hoy == $guia->fecha_recibido_mercancia) {
          $t = 'Hoy';
        } else {
          $t = strftime('%b %d, %Y', strtotime($guia->fecha_recibido_mercancia));
        }
        echo '<td style="'.$s.'">'.$t.'</td>';
        echo '<td>'.$guia->id.'</td>';

        echo '<td>'.$guia->contacto->ciudad->nombre.'</td>';
        if (strlen($guia->contacto->nombre_completo) > 35) {
          echo '<td title="'.$guia->contacto->nombre_completo.'">'.substr($guia->contacto->nombre_completo,0,32).'...</td>';
        } else {
          echo '<td>'.$guia->contacto->nombre_completo.'</td>';
        }
        if ($guia->idestado == 2) { //Facturada
          $t ='Factura '.$guia->idfactura;
        } elseif ($guia->idestado == 3) { //Transito
          $t ='Manifiesto '.$guia->idplanilla.' (Despachado '.$guia->fechadespacho.')';
        } elseif ($guia->idestado == 4) { //Entregada
          $t ='Entregada: '.strftime('%b %d, %Y', strtotime($guia->fechaentrega));
        } else {
          $t = $guia->estado();
        }
        echo '<td title="'.$t.'"><span class="label label-info">'.$guia->estado().'</span></td>';
        $name="id=".$guia->id."&".nonce_create_query_string($guia->id);
        echo '<td>
        <div class="btn-group">
          <button type="button" class="btn ver" id="Guia '.$guia->id.'" title="Ver" name="'.$name.'"><i class="icon-search"></i></button>';
          if (in_array($guia->idestado, array(1, 7)) and $guia->impresa == 'no' and $guia->edicion == 0) {
            echo '<button type="button" id="Editar Guia '.$guia->id.'" class="btn editar" name="'.$name.'" title="Editar"><i class="icon-pencil"></i></button>';
          }
          if ($puede_imprimir and $guia->idestado != 6) {
            echo '<a target="_blank" href="guias/guia?'.$name.'" class="btn" name="'.$name.'" title="Imprimir"><i class="icon-print"></i></a>';
          }
          if ($guia->idestado == 1 or $guia->idestado == 7) {
            echo '<button type="button" id="Anular Guia '.$guia->id.'" class="btn anular btn-danger" name="'.$name.'" title="Anular"><i class="icon-trash"></i></button>';
          }
        echo '</div></td>';
      }
    }
    ?>
    </tbody>
  </table>
</form>
<?= $paging->fetchNavegacion() ?>
<script>
(function() {
  $('#informe').click(function() {
    LOGISTICA.Dialog.open('Crear Nuevo Informe', guias_path+"informe.php");
  });
  $('#crear').click(function() {
    cargarExtra(guias_path+'crear.php');
  });

  $('#guias-list')
    .on('click', 'button.ver', function() {
      LOGISTICA.Dialog.open(this.id, guias_path+'ver.php?'+$(this).attr('name'));
    })
    .on('click', 'button.editar', function() {
      cargarExtra(guias_path+'editar.php?'+this.name);
    })
    .on('click', 'button.anular', function() {
      LOGISTICA.Dialog.open('Anular Guia', guias_path+'anular.php?'+this.name);
    });

  $("#fecha").datepicker({
    autoSize:true,
    showOn: "both",
    buttonImage: "../css/images/calendar.gif",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    maxDate: 0
  });

  $('#actualizar')
    .click(function() {
      $(this).prop('disabled', true).text('Actualizando...');
      $("#center_content").load(guias_path+'?'+$('#BuscarGuias').serialize());
    });

  $('#BuscarGuias').submit(function(e) {
    e.preventDefault();
    $('#buscar').prop('disabled', true).text('Buscando...');
    $('#pagina').val(1);
    $("#center_content").load(guias_path+'?'+$(this).serialize());
  });
}());
function fn_paginar(d, r) { $('.'+d).load(r); }
</script>
