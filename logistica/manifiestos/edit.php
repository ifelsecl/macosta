<?php
require "../../seguridad.php";

if (! isset($_SESSION['permisos'][PLANILLAS_EDITAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (! isset($_GET['idplanilla']) or ! isset($_GET[NONCE_KEY]) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['idplanilla'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
$idplanilla = $_GET['idplanilla'];
require_once Logistica::$root."class/planillasC.class.php";

$objPlanilla = new PlanillasC;

$mosCon = $objPlanilla->mostrar_conductor();
$vehiculos = Vehiculo::all('activos');

if (! $result = $objPlanilla->ObtenerPlanilla($idplanilla)) {
  exit('<h3>No se pudo cargar la información...</h3>');
}
$planilla = mysql_fetch_array($result);
?>
<div id="manifiesto__edit">
  <div class="row-fluid">
    <div class="span8">
      <h2>Editar Manifiesto <?= $idplanilla ?></h2>
    </div>
    <div class="span1">
      <img id="manifiesto__edit__loading" style="display: none;" src="css/ajax-loader.gif" alt="Cargando..." />
    </div>
    <div class="span3 pull-right">
      <a id="manifiesto__edit__print" title="Imprimir Manifiesto" target="_blank" style="display:none;" href="logistica/manifiestos/imprimir2?idplanilla=<?= $idplanilla.'&'.nonce_create_query_string($idplanilla) ?>" class="btn btn-primary">Imprimir</a>
      <button id="manifiesto__edit__back" title="Regresar a Manifiestos" class="btn btn-success">Regresar</button>
    </div>
    <hr class="hr-small">
  </div>
  <div class="row-fluid">
    <div class="span12">
      <form id="EditarPlanilla" name="EditarPlanilla" method="post" action="">
        <table cellspacing="0" cellpadding="0">
          <tr>
            <td><b>Tipo:</b></td>
            <td>
              <select name="tipo" id="tipo" class="">
                <?php
                foreach(Manifiesto::$tipos as $value=>$text) {
                  $c = $value == $planilla['tipo'] ? 'selected="selected"' : '';
                  echo '<option value="'.$value.'" '.$c.'>'.$text.'</option>';
                }
                ?>
              </select>
            </td>
          </tr>
        <tr>
          <td><b>Conductor:</b></td>
          <td>
            <select title="Elija el conductor" class="" name="numero_identificacion_conductor" id="numero_identificacion_conductor">
              <option></option>
              <?php
              while($dep = mysql_fetch_array($mosCon) ) {
                $check='';
                if ($dep['numero_identificacion']==$planilla['numero_identificacion_conductor']) {
                  $check='selected="selected"';
                }
                echo '<option '.$check.' value="'.$dep['numero_identificacion'].'">' .$dep['nombre']." ".$dep['primer_apellido']." ".$dep['segundo_apellido'].'</option>';
              }
              mysql_free_result($mosCon);
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><b>Vehículo:</b></td>
          <td>
          <select title="Elija el vehículo" class="" name="placa" id="placa">
            <option value="">Selecciona...</option>
            <?php
            foreach ($vehiculos as $vehiculo) {
              $style = $title = '';
              if ($vehiculo->tiene_papeles_vencidos()) {
                $title = 'Documentos vencidos: ';
                $style = "background-color: red";
                if ($vehiculo->seguro_vencido()) $title .= '- Seguro';
                if ($vehiculo->soat_vencido()) $title .= '- SOAT';
                if ($vehiculo->tarjeta_operacion_vencida()) $title .= '- Tarjeta Operación';
                if ($vehiculo->tecnico_mecanica_vencida()) $title .= '- Técnico Mecánica';
              }
              $check = $vehiculo->placa == $planilla['placacamion'] ? 'selected="selected"' : '';
              echo '<option title="'.$title.'" '.$check.' value="'.$vehiculo->placa.'" style="'.$style.'">'.$vehiculo->placa.'</option>';
            }
            ?>
          </select>
          </td>
        </tr>
        <tr>
          <td><b>Fecha de Expedición:</b></td>
          <td><input class=" fecha" name="fecha_planilla" type="text" id="fecha_planilla" readonly="readonly" value="<?= $planilla['fecha']; ?>" /></td>
        </tr>
        <tr>
          <td><b>Origen del viaje:</b></td>
          <td>
            <input type="text" id="ciudad_origen" class="autocomplete " value="<?= $planilla['ciudadorigen']; ?>" />
            <input type="hidden" id="id_ciudad_origen" name="id_ciudad_origen" value="<?= $planilla['idciudadorigen']; ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Destino final del viaje:</b></td>
          <td>
            <input type="text" id="ciudad_destino" class="autocomplete " value="<?= $planilla['ciudaddestino']; ?>" />
            <input type="hidden" id="id_ciudad_destino" name="id_ciudad_destino" value="<?= $planilla['idciudaddestino']; ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Titular:</b></td>
          <td>
            <?php
            if ($planilla['titular_tipo_identificacion']=='N') {
              $titular=$planilla['titular_razon_social'];
            } else {
              $titular=$planilla['titular_nombre'].' '.$planilla['titular_primer_apellido'].' '.$planilla['titular_segundo_apellido'];
            }
            ?>
            <input type="text" id="titular" name="titular" class="" value="<?= $titular ?>" />
            <input type="hidden" name="id_titular" id="id_titular" value="<?= $planilla['id_titular'] ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Fecha Limite de Entrega: </b></td>
          <td>
            <input class=" fecha" name="fecha_limite_entrega" type="text" id="fecha_limite_entrega" readonly="readonly" value="<?= $planilla['fecha_limite_entrega'] ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Valor Flete:</b></td>
          <td><input type="text" id="valor_flete" name="valor_flete" class="" value="<?= $planilla['valor_flete']; ?>" /></td>
        </tr>
        <tr>
          <td><b>Anticipo:<b></td>
          <td><input type="text" id="anticipo" name="anticipo" class="" value="<?= $planilla['anticipo']; ?>" /></td>
        </tr>
        <tr>
          <td colspan="2">
            <fieldset>
              <legend>Pago de Saldo</legend>
              <table>
                <tr>
                  <td>Lugar:</td>
                  <td>
                    <input type="text" class="autocomplete " id="ciudad_pago_saldo" name="ciudad_pago_saldo" value="<?= $planilla['ciudad_pago_saldo']; ?>" />
                    <input type="hidden" id="id_ciudad_pago_saldo" name="id_ciudad_pago_saldo" value="<?= $planilla['id_ciudad_pago_saldo']; ?>" />
                  </td>
                </tr>
                <tr>
                  <td>Fecha:</td>
                  <td><input type="text" readonly="readonly" class=" fecha" id="fecha_pago_saldo" name="fecha_pago_saldo" value="<?= $planilla['fecha_pago_saldo']; ?>" /></td>
                </tr>
                <tr>
                  <td>Cargue pagado por:</td>
                <td>
                    <select class="" name="cargue_pagado_por" id="cargue_pagado_por">
                      <?php
                      foreach (Manifiesto::$opciones_cargue as $value => $text) {
                        $c = $planilla['cargue_pagado_por'] == $value ? 'selected="selected"' : '';
                        echo '<option value="'.$value.'" '.$c.'>'.$text.'</option>';
                      }
                      ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>Descargue pagado por:</td>
                  <td>
                    <select class="" name="descargue_pagado_por" id="descargue_pagado_por">
                      <?php
                      foreach (Manifiesto::$opciones_cargue as $value => $text) {
                        $c = $planilla['descargue_pagado_por'] == $value ? 'selected="selected"' : '';
                        echo '<option value="'.$value.'" '.$c.'>'.$text.'</option>';
                      }
                      ?>
                    </select>
                  </td>
                  </tr>
                </table>
              </fieldset>
            </td>
        </tr>
        <tr>
          <td colspan="2">
            <fieldset>
              <legend>Observaciones</legend>
              <textarea class="" style="width: 100%" rows="5" id="observaciones" name="observaciones"><?= $planilla['observaciones']; ?></textarea>
            </fieldset>
          </td>
        </tr>
        </table>
        <center><button type="submit" id="guardarplanilla">Guardar</button></center>
        <?php nonce_create_form_input($planilla['id']) ?>
        <input type="hidden" id="manifiesto__edit__id" name="idplanilla" value="<?= $planilla['id'] ?>" />
      </form>
    </div>
  </div>
</div>
<div id="asignarguias" style="display: none;">
  <form action="#" method="post" id="form_asignar" class="form-inline">
    <div class="alert alert-info">
      <button type="button" class="close fade in" data-dismiss="alert">&times;</button>
      <strong>¡Recuerda!</strong> Los cambios que realices aquí serán guardados automaticamente.
    </div>
    <table>
      <tr>
        <td>Asignar:</td>
        <td>
          <input type="text" name="guia_id" id="guia_id" class="input-small" />
        </td>
        <td>
          <button id="guia_agregar">Asignar</button>
        </td>
      </tr>
    </table>
  </form>
  <form action="#" method="post" id="formbuscar" class="form-inline">
    <table>
      <tr>
        <td>Buscar por:</td>
        <td>
          <select id="opcion" name="opcion" class="input-medium">
            <option value="numero">Número</option>
            <option value="numero_anterior">Número anterior</option>
            <option value="ciudad_destino">Ciudad Destino</option>
            <option value="ciudad_origen">Ciudad Origen</option>
            <option value="cliente">Cliente</option>
          </select>
        </td>
        <td>
          <input type="text" id="termino" name="termino" class="input-small" />
        </td>
          <td><button id="buscar-guias">Buscar</button></td>
          <td>
            <div id="cargando-buscar" style="display: none;">
              <img src="css/ajax-loader.gif" alt="Cargando..." />
            </div>
          </td>
        </tr>
      </table>
    </form>
    <hr class="hr-small">
  <div id="GuiasEncontradas">
    <table class="table table-condensed table-bordered table-hover">
      <thead>
        <tr>
          <th>No.</th>
          <th>Remitente</th>
          <th>Destinatario</th>
          <th>Entrega</th>
          <th width="16"></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="6" class="expand">Usa el buscador para buscar guías.</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div id="GuiasAsignadas" style="clear: both;">
    <div id="cargando3" style="display: none;height: 150px"><img src="css/ajax-loader.gif" alt="cargando" /></div>
  </div>
</div>
<script>
$(function() {
  $("#guardarplanilla").button({icons: {primary: "ui-icon-circle-check"}});
  var manifiesto='<?= $idplanilla ?>';
  var btn_asignar = $('#guia_agregar').button({icons: {primary: 'ui-icon-circle-plus'}, text: false});
  $('#form_asignar').submit(function(e) {
    e.preventDefault();
    var guia = Number($('#guia_id').val());
    if (isNaN(guia) || guia == 0) {
      $('#guia_id').val('').focus();
      return;
    }
    btn_asignar.button('disable');
    $('#cargando2').slideDown();
    var posicion = parseInt($('#cantidad').val())+1;
    $.ajax({
      url: manifiestos_path+'asignarGuia.php',
      data: 'idguia='+guia+'&idplanilla='+manifiesto+'&posicion='+posicion,
      success: function(msj) {
        if (msj!=0) {
          $('#cargando3').slideDown();
          $('#GuiasAsignadas').load(manifiestos_path+'guiasAsignadas.php?id='+manifiesto, function() {
            $('#cargando3').fadeOut(600);
          });
          $('#guia_id').val('').focus();
        } else {
          alert("Ha ocurrido un error al asignar la guia... intentalo nuevamente.");
        }
        btn_asignar.button('enable');
      }
    });
  });
  $('#EditarPlanilla').validate({
    rules: {
      numero_identificacion_conductor: {required: true},
      placa: {required: true},
      fecha_planilla: {required: true},
      id_ciudad_origen: {required: true},
      id_ciudad_destino: {required: true},
      valor_flete: {required: true,digits: true},
      descuento: {required: true,digits: true},
      anticipo: {required: true,digits: true},
      id_ciudad_pago_saldo: {required: true},
      fecha_pago_saldo: {required: true},
      cargue_pagado_por: {required: true},
      descargue_pagado_por: {required: true}
    },
    messages: {
      numero_identificacion_conductor: {required: 'Selecciona el conductor.'},
      placa: {required: 'Selecciona el vehículo.'},
      fecha_planilla: {required: 'Selecciona la fecha de expedición.'},
      id_ciudad_origen: {required: 'Selecciona la ciudad origen.'},
      id_ciudad_destino: {required: 'Selecciona la ciudad destino.'},
      valor_flete: {required: 'Escribe el valor del flete.', digits: 'Sólo números.'},
      descuento: {required: 'Escribe el valor del descuento.', digits: 'Sólo números.'},
      anticipo: {required: 'Escribe el valor del anticipo.', digits: 'Sólo números.'},
      id_ciudad_pago_saldo: {required: 'Selecciona la ciudad donde se pagará el saldo.'},
      fecha_pago_saldo: {required: 'Selecciona la fecha cuando se pagará el saldo.'},
      cargue_pagado_por: {required: 'Escribe quien paga el cargue.'},
      descargue_pagado_por: {required: 'Escribe quien paga el descargue.'}
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function() {
      $('#guardarplanilla').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: manifiestos_path+'ajax.php',
        type: "POST", dataType: 'json',
        data: 'editar=1&'+$('#EditarPlanilla').serialize(),
        success: function(msj) {
          if (!msj.error) {
            $('#manifiesto__edit__id, #idplanilla2').val(manifiesto); //ID para Asignar guias
            //Esconder Form
            $('#EditarPlanilla').slideUp(300, function() {
              $('#asignarguias').slideDown("fast");
            });
            $('#termino').focus();
            $('#manifiesto__edit__print').fadeIn();//Boton Imprimir
            $('#cargando3').fadeIn();
            $('#GuiasAsignadas').load(manifiestos_path+'guiasAsignadas.php?id='+manifiesto,function() {
              $('#cargando3').fadeOut(600);
            }).fadeIn(200);
          } else {
            $('#guardarplanilla').button('enable').button('option','label','Guardar');
            alert(msj.mensaje);
          }
        }
      });
    }
  });
  $('#titular').autocomplete({
    autoFocus: true,
    source: helpers_path+'ajax.php?tercero=1',
    select: function(e, ui) {
      $('#id_titular').val(ui.item.id);
      $('#fecha_limite_entrega').focus();
    }
  });
  $("#manifiesto__edit__back").click(function() {
    $('#manifiesto__edit__loading').slideDown();
    $(".right_content").load(manifiestos_path);
  });
  $('.autocomplete').autocomplete({
    minLength:3,
    source: helpers_path+'ajax.php?ciudad=1',
    select: function(event,ui) {
      var input=event.target.id;
      if (input=='ciudad_destino') {
        $('#id_ciudad_destino').val(ui.item.id);
        $('#valor_flete').focus();
      } else if (input=='ciudad_origen') {
        $('#id_ciudad_origen').val(ui.item.id);
        $('#ciudad_destino').focus();
      } else {
        $('#id_ciudad_pago_saldo').val(ui.item.id);
        $('#fecha_pago_saldo').focus();
      }
    }
  });
  $(".fecha").datepicker({
    autoSize:true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    buttonText:'Seleccionar...'
  });
  $('#buscar-guias').button({icons: {primary: "ui-icon-search"}, text: false}).click(function(event) {
    event.preventDefault();
    if (!$.trim($('#termino').val())) {
      $('#termino').addClass('ui-state-highlight').focus();
    } else {
      $('#termino').removeClass('ui-state-highlight');
      $('#cargando-buscar').fadeIn("fast");
      $('#GuiasEncontradas').load(manifiestos_path+'guiasEncontradasE.php?'+$('#formbuscar').serialize()+'&idplanilla='+manifiesto,function() {
        $('#cargando-buscar').fadeOut("fast");
      });
    }
  });
});
function fn_paginar(d,url) { $("#"+d).load(url); }
</script>
