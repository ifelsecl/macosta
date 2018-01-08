<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][GUIAS_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

$puede_crear           = isset($_SESSION['permisos'][GUIAS_CREAR]);
$puede_crear_informe   = isset($_SESSION['permisos'][GUIAS_CREAR_INFORME]);
$puede_ver             = isset($_SESSION['permisos'][GUIAS_VER]);
$puede_editar          = isset($_SESSION['permisos'][GUIAS_EDITAR]);
$puede_imprimir        = isset($_SESSION['permisos'][GUIAS_IMPRIMIR]);
$puede_anular          = isset($_SESSION['permisos'][GUIAS_ANULAR]);
$puede_eliminar        = isset($_SESSION['permisos'][GUIAS_ELIMINAR]);
$puede_deshacer        = isset($_SESSION['permisos'][GUIAS_DESHACER]);
$puede_editar_tiempos  = isset($_SESSION['permisos'][GUIAS_EDITAR_TIEMPOS]);
$puede_revalorizar     = isset($_SESSION['permisos'][GUIAS_REVALORIZAR]);
$puede_realizar_cierre = isset($_SESSION['permisos'][GUIAS_REALIZAR_CIERRE]);

if (isset($_GET['buscar'])) {
  $guias = Guia::search($_GET, 'sql');
} else {
  $type = $puede_deshacer ? 'todas' : 'activas';
  $guias = Guia::all($type, 'sql');
}
$paging = new PHPPaging('right_content', $guias);
$paging->ejecutar();
?>
<script>
$(function() {
  $("#fecha").datepicker({
    autoSize:true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    maxDate: 0
  });
  $('#buscar').button({icons: {primary: 'ui-icon-search'}});
  $("#crear, #crear_paquete, #informe, #papelera").click(function(e) {
    e.preventDefault();
    cargarExtra(guias_path+$(this).attr('id')+".php");
  });
  $('table#guias-list')
    .on('click', '.ver', function() {
      LOGISTICA.Dialog.open('Guía '+$(this).data('id'), guias_path+'ver.php?'+this.name);
    })
    .on('click', '.editar', function() {
      cargarExtra(guias_path+'editar.php?'+this.name);
    })
    .on('click', '.anular', function() {
      LOGISTICA.Dialog.open('Anular/Eliminar Guía', guias_path+'anular_eliminar.php?'+this.name);
    })
    .on('click', '.tiempos', function() {
      LOGISTICA.Dialog.open('Editar Tiempos', guias_path+'tiempos.php?'+this.name);
    })
    .on('click', '.imprimir', function(e) {
      $(this).attr("target","_blank").attr("href",guias_path+"imprimir?"+this.name);
    })
    .on('click', '.deshacer', function() {
      var conf = confirm("¿Desea activar esta guia?");
      if (! conf) return false;
      $('#cargando').slideDown();
      $.ajax({
        url: guias_path+'deshacer.php',
        type: "POST",
        data: this.name,
        success: function(msj) {
          if (msj == "ok") {
            cargarPrincipal(guias_path);
          } else {
            $('#cargando').slideUp(200);
            LOGISTICA.Dialog.open('Error', msj, true);
          }
        }
      });
    });
  $('#imprimir').click(function() {
    LOGISTICA.Dialog.open('Imprimir Guías', guias_path+'imprimir_varias.php');
  });
  $('form#BuscarGuias').submit(function(e) {
    e.preventDefault();
    $('#buscar').button('disable').button('option','label','Buscando...');
    cargarPrincipal(guias_path+'index.php?'+$(this).serialize());
  });
  $('#realizar_cierre, #revalorizar, #marcar').click(function(e) {
    e.preventDefault();
    LOGISTICA.Dialog.open($(this).text(), guias_path + this.id + '.php');
  });
});
function fn_paginar(d, r) {$('.'+d).load(r);}
</script>
<div class="pull-right btn-toolbar">
  <?php if ($puede_crear) { ?>
  <div class="btn-group">
    <button class="btn btn-info" id="crear" title="Crear una guía"><i class="icon-plus"></i> Crear Guía</button>
    <button class="btn btn-info dropdown-toggle" data-toggle="dropdown">
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
      <li><a href="#" id="crear_paquete" title="Crear un paquete"><i class="icon-plus"></i> Crear Paquete</a></li>
    </ul>
  </div>
  <?php }
  if ($puede_imprimir) { ?>
    <button id="imprimir" class="btn" title="Imprimir multiples guías"><i class="icon-print"></i> Imprimir</button>
  <?php }
  if ($puede_crear_informe) {
    echo '<button id="informe" class="btn" title="Crear un informe de guías"><i class="icon-file-alt"></i> Informe</button>';
  }
  if ($puede_editar) { ?>
    <button id="marcar" class="btn" title="Marcar multiples guías"><i class="icon-ok-sign"></i> Marcar Entr.</button>
  <?php } ?>
  <div class="btn-group">
    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
      Más
      <span class="caret"></span>
    </a>
    <ul class="dropdown-menu pull-right">
      <?php
      if ($puede_eliminar) {
        echo '<li>';
        echo '<a href="#" id="papelera" title="Ver guías anuladas"><i class="icon-trash"></i> Papelera</a>';
        echo '</li>';
      }
      if ($puede_revalorizar) {
        echo '<li>';
        echo '<a href="#" id="revalorizar" title="Revalorizar guías"><i class="icon-refresh"></i> Revalorizar</a>';
        echo '</li>';
      }
      if ($puede_realizar_cierre) {
        $title = 'Realizar un cierre, las guías creadas hasta la fecha del cierre solo podrán ser editadas individualmente.';
        echo '<li>';
        echo '<a href="#" id="realizar_cierre" title="'.$title.'"><i class="icon-cog"></i> Realizar Cierre</a>';
        echo '</li>';
      }
      ?>
      <!-- dropdown menu links -->
    </ul>
  </div>
</div>
<h2>Guías</h2>
<form action="#" method="post" id="BuscarGuias">
  <?php if ($paging->numEstaPagina() > 1) { ?>
    <input type="hidden" id="pagina" name="pagina" value="<?= $paging->numEstaPagina() ?>" />
  <?php } ?>
  <input type="hidden" name="buscar" value="si" />
  <table cellpadding="0">
    <tr>
      <td>Número:</td>
      <td><input type="text" class="input-small" name="id" id="id" value="<?php if (isset($_REQUEST['id'])) echo $_REQUEST['id'] ?>" /></td>
      <td>Fecha:</td>
      <td><input type="text" class="input-small" name="fecha" id="fecha" value="<?php if (isset($_REQUEST['fecha'])) echo $_REQUEST['fecha'] ?>" /></td>
      <td rowspan="6">
        <button id="buscar" type="submit">Buscar</button>
        <img src="css/ajax-loader.gif" alt="cargando..." id="cargando" style="display:none" />
      </td>
    </tr>
    <tr>
      <td>Manifiesto:</td>
      <td><input type="text" class="input-small" name="manifiesto" id="manifiesto" value="<?php if (isset($_REQUEST['manifiesto'])) echo $_REQUEST['manifiesto'] ?>" /></td>
      <td>No. Anterior:</td>
      <td><input type="text" class="input-small" name="numero_anterior" id="numero_anterior" value="<?php if (isset($_REQUEST['numero_anterior'])) echo $_REQUEST['numero_anterior'] ?>" /></td>
    </tr>
    <tr>
      <td>Origen:</td>
      <td><input type="text" class="input-small" name="ciudad_origen" id="ciudad_origen" value="<?php if (isset($_REQUEST['ciudad_origen'])) echo $_REQUEST['ciudad_origen'] ?>" /></td>
      <td>Destino:</td>
      <td><input type="text" class="input-small" name="ciudad_destino" id="ciudad_destino" value="<?php if (isset($_REQUEST['ciudad_destino'])) echo $_REQUEST['ciudad_destino'] ?>" /></td>
    </tr>
    <tr>
      <td>Cliente:</td>
      <td><input type="text" class="input-small" name="cliente" id="cliente" value="<?php if (isset($_REQUEST['cliente'])) echo $_REQUEST['cliente'] ?>" /></td>
      <td>Destinatario:</td>
      <td><input type="text" class="input-small" name="contacto" id="contacto" value="<?php if (isset($_REQUEST['contacto'])) echo $_REQUEST['contacto'] ?>" /></td>
    </tr>
    <tr>
      <td>No. Dcto:</td>
      <td><input type="text" class="input-small" name="documento" id="documento" value="<?php if (isset($_REQUEST['documento'])) echo $_REQUEST['documento'] ?>" /></td>
      <td>Usuario:</td>
      <td><input type="text" class="input-small" name="usuario" id="usuario" value="<?php if (isset($_REQUEST['usuario'])) echo $_REQUEST['usuario'] ?>" /></td>
    </tr>
    <tr>
      <td>Estado:</td>
      <td><input type="text" class="input-small" name="estado" id="estado" value="<?php if (isset($_REQUEST['estado'])) echo $_REQUEST['estado'] ?>" /></td>
      <td>
        <label title="Busca solo las recogidas" for="solo_recogidas"><input id="solo_recogidas" type="checkbox" name="recogida" value="si" <?= isset($_REQUEST['recogida']) ? 'checked="checked"' : '' ?> />Solo Recogidas</label>
      </td>
      <td>
        <label title="Busca en los últimos 6 meses" for="seis_meses"><input id="seis_meses" type="checkbox" name="seis_meses" value="si" <?= isset($_REQUEST['seis_meses']) ? 'checked="checked"' : '' ?> />Últimos 6 meses</label>
      </td>
    </tr>
  </table>
</form>
<table id="guias-list" class="table table-bordered table-hover table-condensed">
  <thead>
    <tr>
      <th>Número</th>
      <th>Cliente</th>
      <th>Destino</th>
      <th>Destinatario</th>
      <th>Estado</th>
      <th></th>
      <th style="width: 130px">Acción</th>
    </tr>
  </thead>
  <tbody>
  <?php
  if ($paging->numTotalRegistros() == 0) {
    echo '<tr class="warning"><td colspan="7" class="expand">No se encontraron guías...</td></tr>';
  } else {
    $hace_3_dias  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-3, date("Y")));
    $hace_2_dias  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-2, date("Y")));
    $hoy      = date("Y-m-d");
    while ($guia = $paging->fetchResultado('Guia')) {
      $c = $guia->idestado == 6 ? 'error' : '';
      echo '<tr class="'.$c.'">';
      if (empty($guia->numero)) $title = "";
      else $title = "Numero anterior: ".$guia->numero;
      echo '<td title="'.$title.'">'.$guia->id.'</td>';
      if (strlen($guia->cliente_nombre_completo) > 20) {
        echo '<td><abbr title="'.$guia->cliente_nombre_completo.'">'.substr($guia->cliente_nombre_completo, 0, 15).'...</abbr></td>';
      } else {
        echo '<td>'.$guia->cliente_nombre_completo.'</td>';
      }
      echo '<td>'.$guia->contacto_ciudad_nombre.'</td>';
      if (strlen($guia->contacto_nombre_completo) > 18) {
        echo '<td><abbr title="'.$guia->contacto_nombre_completo.'">'.htmlentities(substr($guia->contacto_nombre_completo, 0, 13)).'...</abbr></td>';
      } else {
        echo '<td>'.$guia->contacto_nombre_completo.'</td>';
      }
      if ($guia->idestado == 2) {//Facturada
        $t = 'Factura '.$guia->idfactura;
      }elseif ($guia->idestado == 3) {//Transito
        $t = 'Manifiesto '.$guia->idplanilla.' (Despachado '.$guia->fechadespacho.')';
      }elseif ($guia->idestado == 4) {//Entregada
        $t = 'Entregada: '.strftime('%b %d, %Y',strtotime($guia->fechaentrega));
      } else {
        $t = '';
      }
      echo '<td title="'.$t.'">'.$guia->estado().'</td>';
      $name ="id=".$guia->id."&".nonce_create_query_string($guia->id);
      $style = '';
      if ($guia->idestado == 1 or $guia->idestado == 7) {
        if ($guia->fecha_recibido_mercancia <= $hace_3_dias) {
          $style = 'background-color: red';
        }elseif ($guia->fecha_recibido_mercancia >= $hace_2_dias and $guia->fecha_recibido_mercancia < $hoy) {
          $style = 'background-color: yellow';
        } else {
          $style = 'background-color: green';
        }
      }
      if ($hoy == $guia->fecha_recibido_mercancia) {
        $title = "Hoy";
      } else {
        $time = strtotime($guia->fecha_recibido_mercancia);
        $title = htmlentities(strftime('%b %d, %Y',$time)).' (hace '.time_ago_es($time).')';
      }
      echo '<td align="center" width="12" title="'.$title.'" style="'.$style.'"> </td>';
      echo '<td><div class="btn-group">';
      if ($puede_ver) {
        echo '<button title="Ver" class="btn ver" data-id="'.$guia->id.'" name="'.$name.'"><i class="icon-search"></i></button>';
      }
      if ( ($puede_editar and ($guia->idestado !=6 and $guia->idestado != 2)) or ( isset($_SESSION['permisos'][GUIAS_EDITAR_FACTURADAS]) and $guia->idestado == 2 ) )  {
        echo '<button title="Editar" class="btn editar" name="'.$name.'"><i class="icon-pencil"></i></button>';
      }
      if ($puede_editar_tiempos and ($guia->idestado == 4 or $guia->idestado == 3)) {
        echo '<button title="Editar Tiempos" class="btn tiempos" name="'.$name.'"><i class="icon-time"></i></button>';
      }
      if ($puede_imprimir and $guia->idestado != 6) {
        echo '<a href="#" target="_blank" title="Imprimir" class="btn imprimir" name="'.$name.'"><i class="icon-print"></i></a>';
      }
      if ( ($puede_anular or $puede_eliminar) and ($guia->idestado != 6 and $guia->idestado != 2)) {
        echo '<button title="Anular/Eliminar" class="btn anular btn-danger" name="'.$name.'"><i class="icon-trash"></i></button>';
      }elseif ($puede_deshacer and $guia->idestado == 6) {
        echo '<button title="Activar" class="btn deshacer btn-success" name="'.$name.'"><i class="icon-ok"></i></button>';
      }
      echo '</td></div>';
    }
  }
  ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
