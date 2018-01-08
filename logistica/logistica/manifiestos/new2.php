<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PLANILLAS_CREAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
$vehiculos = Vehiculo::all('activos');
$opciones_carge = Manifiesto::$opciones_cargue;
$tipos = Manifiesto::$tipos;
?>
<button class="btn btn-success pull-right" title="Regresar a Manifiestos" onclick="regresar()">Regresar</button>
<h2>Nuevo Manifiesto</h2>
<div id="manifiesto__new" class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#tab_guias" title="Asignar guías">Guías</a></li>
    <li><a data-toggle="tab" href="#tab_info" title="Informacion del manifiesto">Información</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="tab_guias">
      <div id="AsignarGuias">
        <form id="manifiesto__new__assign_form" class="form-inline">
          <table cellpadding="0">
            <tr>
              <?php
              $str='Escribe el número de la guía y presiona enter o has clic en el botón para asignar la guía directamente al Manifiesto.';
              ?>
              <td style="width:120px"><b>Asignar guía <span class="help" title="<?= $str ?>">[?]</span></b></td>
              <td><input type="text" class="input-medium" name="numero" id="numero" /></td>
              <td><button id="manifiesto__new__assign">Asignar</button></td>
            </tr>
          </table>
        </form>
        <form action="#" method="post" id="formbuscar" class="form-inline">
          <table cellpadding="0">
            <tr>
              <?php
              $str='Utiliza las opciones para buscar guías y asignalas al Manifiesto usando el botón + de color verde.';
              ?>
              <td style="width: 120px"><b>Buscar guía <span class="help" title="<?= $str ?>">[?]</span></b></td>
              <td>
                <select class="input-medium" name="opcion">
                  <option value="numero">Número</option>
                  <option value="numero_anterior">Número anterior</option>
                  <option value="ciudad_destino">Ciudad Destino</option>
                  <option value="cliente">Cliente</option>
                </select>
              </td>
              <td>
                <input type="text" id="termino" class="input-medium" name="termino" value="" />
              </td>
              <td><button id="crear_manifiesto_buscar">Buscar</button></td>
              <td>
                <div id="cargando-buscar" style="display: none;">
                  <img src="css/ajax-loader.gif" alt="Cargando..." />
                </div>
              </td>
            </tr>
          </table>
        </form>
        <div id="GuiasEncontradas">
          <table class="table table-bordered table-hover table-condensed" style="font-size:11px !important;">
            <thead>
              <tr>
                <th>No.</th>
                <th>Remitente</th>
                <th>Destinatario</th>
                <th>Destino</th>
                <th>Dirección</th>
                <th>Total</th>
                <th width="16"></th>
                <th width="16"></th>
                <th width="16"></th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="11" class="expand">Usa el buscador para encontrar guías...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <hr class="hr-small">
      <br>
      <table>
        <tr>
          <td>Guías asignadas: <span id="cantidad">0</span></td>
          <td><img src="css/ajax-loader.gif" alt="cargando" id="cargando3" style="display: none;" /></td>
          <td><span class="muted pull-right">Recuerda que puedes arrastrar las filas para ordenarlas.</span></td>
        </tr>
      </table>
      <table class="table table-bordered table-hover table-condensed" id="GuiasAsignadas" style="font-size: 11px !important">
        <thead>
          <tr>
            <th>No.</th>
            <th>Remitente</th>
            <th>Destinatario</th>
            <th>Destino</th>
            <th>Peso (Kg)</th>
            <th>Vol (m3)</th>
            <th>Total</th>
            <th width="32" colspan="2">Acción</th>
          </tr>
        </thead>
        <tbody class="ordenable" style="cursor: pointer;">
        </tbody>
      </table>
      <hr class="hr-small">
      <table cellpadding="3" cellspacing="0" class="pull-right" style="clear: both">
        <tr>
          <td colspan="2"><b>Peso:</b> (Kg)</td>
          <td colspan="2" align="right" id="peso">0</td>
        </tr>
        <tr>
          <td colspan="2"><b>Volúmen:</b> (m<sup>3</sup>)</td>
          <td colspan="2" align="right" id="volumen">0</td>
        </tr>
        <tr>
          <td colspan="2" style="background-color: silver;"><b>Total:</b></td>
          <td colspan="2" align="right" style="background-color: silver;" id="total">0</td>
        </tr>
      </table>
      <div style="clear:both;"></div>
    </div>
    <div class="tab-pane" id="tab_info">
      <form id="manifiesto__new__form" name="manifiesto__new__form" method="post" action="#">
        <table cellspacing="2" cellpadding="2">
          <tr>
            <td>
              <b>Tipo:</b><br>
              <select name="tipo" id="tipo">
                <?php
                foreach($tipos as $value=>$text) {
                  echo '<option value="'.$value.'">'.$text.'</option>';
                }
                ?>
              </select>
            </td>
            <td>
              <b>Fecha de Expedición:</b><br>
              <input class="fecha input-small" name="fecha_planilla" type="text" id="fecha_planilla" readonly="readonly" value="<?= date("Y-m-d") ?>" />
            </td>
          </tr>
          <tr>
            <td>
              <b>Vehículo:</b><br>
              <select name="placa" id="placa">
                <option value="">Selecciona...</option>
                <?php
                foreach ($vehiculos as $vehiculo) {
                  $titulo = '';
                  if ($vehiculo->f_venc_seguro < date('Y-m-d') or $vehiculo->f_venc_soat < date('Y-m-d') or $vehiculo->f_venc_toperacion < date('Y-m-d') or $vehiculo->f_venc_tmec < date('Y-m-d')) {
                    if ($vehiculo->f_venc_seguro < date('Y-m-d')) {
                      $titulo.='- Seguro';
                    }
                    if ($vehiculo->f_venc_soat < date('Y-m-d')) {
                      $titulo.='- SOAT';
                    }
                    if ($vehiculo->f_venc_toperacion < date('Y-m-d')) {
                      $titulo.='- Tarjeta Operación';
                    }
                    if ($vehiculo->f_venc_tmec < date('Y-m-d')) {
                      $titulo.='- Técnico-Mecánica';
                    }
                    echo '<option title="Documentos vencidos: '.$titulo.'" value="'.$vehiculo->placa.'" style="background-color: red">'.$vehiculo->placa.'</option>';
                  }else{
                    echo '<option value="'.$vehiculo->placa.'">'.$vehiculo->placa.'</option>';
                  }
                }
                ?>
              </select>
            </td>
            <td>
              <b>Conductor:</b><br>
              <input type="text" name="nombre_conductor" id="nombre_conductor" />
              <input type="hidden" name="numero_identificacion_conductor" id="numero_identificacion_conductor" />
            </td>
          </tr>
          <tr>
            <td>
              <b>Origen del Viaje:</b><br>
              <input type="text" id="ciudad_origen" class="autocomplete" />
              <input type="hidden" id="id_ciudad_origen" name="id_ciudad_origen" />
            </td>
            <td>
              <b>Destino Final del Viaje</b>:<br>
              <input type="text" id="ciudad_destino" class="autocomplete" />
              <input type="hidden" id="id_ciudad_destino" name="id_ciudad_destino" />
            </td>
          </tr>
          <tr>
            <td>
              <b>Titular:</b><br>
              <input type="text" id="titular" name="titular" />
              <input type="hidden" name="id_titular" id="id_titular" />
            </td>
            <td>
              <b>Fecha Limite de Entrega</b>:<br>
              <input class="fecha input-small" name="fecha_limite_entrega" type="text" id="fecha_limite_entrega" readonly="readonly" value="<?= date("Y-m-d") ?>" />
            </td>
          </tr>
          <tr>
            <td>
              <b>Valor del Viaje:</b><br>
              <input type="text" id="valor_viaje" name="valor_viaje" value="0" />
            </td>
            <td>
              <b>Anticipo:</b><br>
              <input type="text" id="anticipo" name="anticipo" value="0" />
            </td>
          </tr>
            <td>
              <b>Retención ICA [5.4 x Mil]:</b><br>
              <input type="text" disabled="true" id="reteica" />
            </td>
            <td>
              <b>Retención en la Fuente:</b><br>
              <input type="text" disabled="true" id="retefuente" />
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <fieldset>
                <legend>Pago de Saldo</legend>
                <table cellpadding="0">
                  <tr>
                    <td><label>Lugar</label></td>
                    <td>
                      <input type="text" class="autocomplete" id="ciudad_pago_saldo" name="ciudad_pago_saldo" />
                      <input type="hidden" id="id_ciudad_pago_saldo" name="id_ciudad_pago_saldo" />
                    </td>
                  </tr>
                  <tr>
                    <td><label>Fecha</label></td>
                    <td><input readonly="readonly" type="text" class="fecha input-small" id="fecha_pago_saldo" name="fecha_pago_saldo" value="<?= date('Y-m-d', strtotime('last day of this month')) ?>" /></td>
                  </tr>
                  <tr>
                    <td><label>Cargue pagado por</label></td>
                    <td>
                      <select name="cargue_pagado_por" id="cargue_pagado_por">
                        <?php
                        foreach ($opciones_carge as $value => $text) {
                          echo '<option value="'.$value.'">'.$text.'</option>\n';
                        }
                        ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td><label>Descargue pagado por</label></td>
                    <td>
                      <select name="descargue_pagado_por" id="descargue_pagado_por">
                        <?php
                        foreach ($opciones_carge as $value => $text) {
                          echo '<option value="'.$value.'">'.$text.'</option>';
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
                <textarea style="width: 95%" rows="5" id="observaciones" name="observaciones"></textarea>
              </fieldset>
            </td>
          </tr>
        </table>
        <center class="form-actions"><button type="submit" id="manifiesto__new__save">Guardar</button></center>
        <?php nonce_create_form_input("guardar")?>
      </form>
    </div>
  </div>
</div>
<script>
(function() {
  LOGISTICA.logistica.manifiesto = function() {
    var $el = $('#manifiesto__new');
    var $saveBtn = $el.find('#manifiesto__new__save').button({icons: {primary: "ui-icon-circle-check"}});
    var $assignBtn = $el.find('#manifiesto__new__assign');

    var total = function() {
      var t = 0, p = 0, v = 0;
      $el.find('input.peso').each(function(index, element) {
        p += +$(this).val();
      });
      $el.find('input.volumen').each(function(index, element) {
        v += +$(this).val();
      });
      $el.find('input.total').each(function(index, element) {
        t += +$(this).val();
      });
      $el.find('#peso').text(p.toFixed(2));
      $el.find('#volumen').text(v.toFixed(2));
      $el.find('#total').text(t);
    };

    var handleDialogLink = function() {
      $('#dialog').on('click', 'a.btn.to_bodega', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $btn.attr('disabled', 'disabled').text('Guardando...');
        $.ajax({
          url: guias_path+'ajax.php',
          type: 'POST',
          data: 'change_status=1&idestado=1&id='+this.name,
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              cerrarDialogo();
            } else {
              $btn.removeAttr('disabled').text('Pasar a Bodega');
              alert('Ha ocurrido un error, intentalo nuevamente.');
            }
          }
        });
      });
    };

    var save = function(form) {
      $saveBtn.button('disable').button('option','label','Guardando...');
      $.ajax({
        url: manifiestos_path+'ajax.php',
        type: "POST",
        dataType: 'json',
        data: 'guardar=1&'+$(form).serialize()+'&'+$el.find('.ordenable').sortable("serialize"),
        success: function(m) {
          if (m.error) {
            $saveBtn.button('enable').button('option','label','Guardar');
            LOGISTICA.Dialog.open(m.titulo, m.mensaje, true);
          } else {
            regresar();
          }
        }
      });
    };

    var initFormValidator = function() {
      $el.find('#manifiesto__new__form').validate({
        rules: {
          numero_identificacion_conductor: 'required',
          placa: 'required',
          fecha_planilla: 'required',
          id_ciudad_origen: {required: true},
          id_ciudad_destino: {required: true},
          id_titular: 'required',
          valor_viaje: {required: true,digits: true},
          anticipo: {required: true,digits: true},
          id_ciudad_pago_saldo: {required: true},
          fecha_pago_saldo: {required: true},
          cargue_pagado_por: {required: true},
          descargue_pagado_por: {required: true}
        },
        highlight: function(input) {$(input).addClass("ui-state-highlight");},
        unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
        submitHandler: save
      });
    };

    var initAssignForm = function() {
      $el.find('#manifiesto__new__assign_form').submit(function(e) {
        e.preventDefault();
        var $number = $el.find('#numero');
        if (!$.trim($number.val()) || isNaN($number.val())) {
          $number.focus();
          return;
        }
        var id = $number.val();
        $assignBtn.button('disable');
        var exist = false;
        $el.find('.ids').each(function(index) {
          if(id == $(this).val()) exist=true;
        });
        if(exist) {
          $assignBtn.button('enable');
          return;
        }
        $.ajax({
          url: manifiestos_path+'ajax.php', dataType: 'json',
          type: 'POST', data: {ig: 121, id: id},
          success: function(guia) {
            $assignBtn.button('enable');
            if(guia.error) {
              LOGISTICA.Dialog.open('Error', guia.mensaje, true);
              return;
            }
            var total = parseFloat(guia.total)+parseFloat(guia.valorseguro);
            var fila = '<tr id="ids_'+guia.id+'"><td><input type="hidden" name="guias[]" class="ids" value="'+guia.id+'" />'+guia.id+'</td>'+
              '<td>'+guia.cliente.nombre_completo+'</td><td>'+guia.contacto.nombre_completo+'</td><td>'+guia.contacto.ciudad.nombre+'</td>'+
              '<td align="right"><input type="hidden" class="peso" value="'+guia.peso+'" />'+guia.peso+'</td>'+
              '<td align="right"><input type="hidden" class="volumen" value="'+guia.kilo_vol+'" />'+guia.kilo_vol+'</td>'+
              '<td align="right"><input type="hidden" class="total" value="'+total+'" />'+total+'</td>'+
              '<td><div class="btn-group"><a class="btn ver btn-mini" name="id='+guia.id+'&'+guia.nonce+'" href="#"><i class="icon-search"></i></a>'+
              '<a class="btn quitar btn-danger btn-mini" href="#"><i class="icon-remove"></i></a></div></td></tr>';
            $('#GuiasAsignadas').append(fila);
            total();
            $('.ver').click(function(e) {
              e.preventDefault();
              LOGISTICA.Dialog.open('Guía', guias_path+'ver.php?'+this.name);
            });
            $('.quitar').click(function(e) {
              e.preventDefault();
              $(this).parent().parent().parent().remove();
              total();
            });
            $('#numero').val('').focus();
          }
        });
      });
    };

    var init = function() {
      handleDialogLink();
      initFormValidator();
      initAssignForm();
    };

    return {
      init: init
    }
  }();

  LOGISTICA.logistica.manifiesto.init();
  var $btn_guardar = $("#manifiesto__new__save").button({icons: {primary: "ui-icon-circle-check"}});
  $assignBtn.button({icons: {primary: "ui-icon-circle-check"}, text: false});

  $('#dialog').on('click', 'a.btn.to_bodega', function(e) {
    e.preventDefault();
    var $btn = $(this);
    $btn.attr('disabled', 'disabled').text('Guardando...');
    $.ajax({
      url: guias_path+'ajax.php',
      type: 'POST',
      data: 'change_status=1&idestado=1&id='+this.name,
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          cerrarDialogo();
        } else {
          $btn.removeAttr('disabled').text('Pasar a Bodega');
          alert('Ha ocurrido un error, intentalo nuevamente.');
        }
      }
    });
  });

  /* Asigna una guía directamente a la planilla */
  $('#manifiesto__new__assign_form').submit(function(e) {
    e.preventDefault();
    if (! $.trim($('#numero').val()) || isNaN($('#numero').val())) {
      $('#numero').focus();
      return;
    }
    var id=$('#numero').val();
    $assignBtn.button('disable');
    var existe=false;
    $('.ids').each(function(index) {
      if(id == $(this).val()) existe=true;
    });
    if(existe) {
      $assignBtn.button('enable');
      return;
    }
    $.ajax({
      url: manifiestos_path+'ajax.php', dataType: 'json',
      type: 'POST', data: {ig: 121, id: id},
      success: function(guia) {
        $assignBtn.button('enable');
        if(guia.error) {
          LOGISTICA.Dialog.open('Error', guia.mensaje, true);
          return;
        }
        var total = parseFloat(guia.total)+parseFloat(guia.valorseguro);
        var fila = '<tr id="ids_'+guia.id+'"><td><input type="hidden" name="guias[]" class="ids" value="'+guia.id+'" />'+guia.id+'</td>'+
          '<td>'+guia.cliente.nombre_completo+'</td><td>'+guia.contacto.nombre_completo+'</td><td>'+guia.contacto.ciudad.nombre+'</td>'+
          '<td align="right"><input type="hidden" class="peso" value="'+guia.peso+'" />'+guia.peso+'</td>'+
          '<td align="right"><input type="hidden" class="volumen" value="'+guia.kilo_vol+'" />'+guia.kilo_vol+'</td>'+
          '<td align="right"><input type="hidden" class="total" value="'+total+'" />'+total+'</td>'+
          '<td><div class="btn-group"><a class="btn ver btn-mini" name="id='+guia.id+'&'+guia.nonce+'" href="#"><i class="icon-search"></i></a>'+
          '<a class="btn quitar btn-danger btn-mini" href="#"><i class="icon-remove"></i></a></div></td></tr>';
        $('#GuiasAsignadas').append(fila);
        CalcularTotales();
        $('.ver').click(function(e) {
          e.preventDefault();
          LOGISTICA.Dialog.open('Guía', guias_path+'ver.php?'+this.name);
        });
        $('.quitar').click(function(e) {
          e.preventDefault();
          $(this).parent().parent().parent().remove();
          CalcularTotales();
        });
        $('#numero').val('').focus();
      }
    });
  });

  function CalcularTotales() {
    var t=0;
    var p=0;
    var v=0;
    $('input.peso').each(function(index, element) {
      p+=parseFloat($(this).val());
    });
    $('input.volumen').each(function(index, element) {
      v+=parseFloat($(this).val());
    });
    $('input.total').each(function(index, element) {
      t+=parseFloat($(this).val());
    });
    $('#peso').text(p);
    $('#volumen').text(v);
    $('#total').text(t);
  }
  $('#manifiesto__new__form').validate({
    rules: {
      numero_identificacion_conductor: 'required',
      placa: 'required',
      fecha_planilla: 'required',
      id_ciudad_origen: {required: true},
      id_ciudad_destino: {required: true},
      id_titular: 'required',
      valor_viaje: {required: true,digits: true},
      anticipo: {required: true,digits: true},
      id_ciudad_pago_saldo: {required: true},
      fecha_pago_saldo: {required: true},
      cargue_pagado_por: {required: true},
      descargue_pagado_por: {required: true}
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $btn_guardar.button('disable').button('option','label','Guardando...');
      $.ajax({
        url: manifiestos_path+'ajax.php',
        type: "POST", dataType: 'json',
        data: 'guardar=1&'+$(form).serialize()+'&'+$('.ordenable').sortable("serialize"),
        success: function(m) {
          if (m.error) {
            $btn_guardar.button('enable').button('option','label','Guardar');
            LOGISTICA.Dialog.open(m.titulo, m.mensaje, true);
          } else {
            regresar();
          }
        }
      });
    }
  });
  $('#nombre_conductor').autocomplete({
    autoFocus: true,
    source: helpers_path+'ajax.php?conductor=1',
    select: function(e, ui) {
      $('#numero_identificacion_conductor').val(ui.item.id);
      $('#ciudad_origen').focus();
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
  $('.autocomplete').autocomplete({
    minLength:3,
    autoFocus: true,
    source: helpers_path+'ajax.php?ciudad=1',
    select: function(event,ui) {
      var input=event.target.id;
      if(input=='ciudad_destino') {
        $('#id_ciudad_destino').val(ui.item.id);
        $('#titular').focus();
      }else if(input=='ciudad_origen') {
        $('#id_ciudad_origen').val(ui.item.id);
        $('#ciudad_destino').focus();
      }else{
        $('#id_ciudad_pago_saldo').val(ui.item.id);
        $('#fecha_pago_saldo').focus();
      }
      $('#'+input).val(ui.item.nombre);
      return false;
    }
  });
  $(".fecha" ).datepicker({
    autoSize: true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    minDate: 0
  });
  $('#crear_manifiesto_buscar').button({icons: {primary: "ui-icon-search"}, text: false}).click(function(event) {
    event.preventDefault();
    if( !jQuery.trim($('#termino').val()) ) {
      $('#termino').addClass('ui-state-highlight').focus();
    }else{
      $('#termino').removeClass('ui-state-highlight');
      $('#cargando-buscar').fadeIn("fast");
      $('#GuiasEncontradas').load(manifiestos_path+'guiasEncontradasC.php?'+$('#formbuscar').serialize()+'&'+$('input.ids').serialize(),function() {
        $('#cargando-buscar').fadeOut("fast");
      });
    }
  });
  $('.ordenable').sortable({
    hoverClass: "ui-state-hover",
    placeholder: "ui-state-highlight",
    opacity: 0.8,
    forceHelperSize: true,
    forcePlaceholderSize: true,
    revert: 200,
    update: function(event, ui) {}
  });
  $('#valor_viaje').keyup(function() {
    var v=$(this).val();
    $('#retefuente').val( (v*0.01).toFixed() );
    $('#reteica').val( (v*0.0054).toFixed() );
  });
  $('#numero').focus();
})();
function fn_paginar(d,url) {
  $("#"+d).load(url);
}
</script>
