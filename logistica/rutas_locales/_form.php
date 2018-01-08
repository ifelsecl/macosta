<div id="ruta_local__new_container">
  <button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
  <h2><?= isset($ruta_local->id) ? 'Editar Ruta Local '.$ruta_local->id : 'Nueva Ruta Local' ?></h2>
  <hr class="hr-small">
  <form id="ruta_local__form" name="ruta_local" method="post" action="#" style="margin-bottom:5px">
    <input type="hidden" name="ruta_local[id]" id="ruta_local_id" value="<?= isset($ruta_local->id) ? $ruta_local->id : '' ?>" />
    <table cellpadding="0">
      <tr>
        <td><b>Fecha</b></td>
        <td>
          <input class="fecha input-small" readonly="readonly" type="text" name="ruta_local[fecha]" id="ruta_local_fecha" value="<?= isset($ruta_local->fecha) ? $ruta_local->fecha : date("Y-m-d") ?>" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>Ciudad</b></td>
        <td>
          <input type="text" id="ruta_local__ciudad" value="<?= isset($ruta_local->id_ciudad) ? $ruta_local->ciudad()->nombre : '' ?>" />
          <input type="hidden" name="ruta_local[id_ciudad]" id="ruta_local__id_ciudad" value="<?= isset($ruta_local->id_ciudad) ? $ruta_local->id_ciudad : '' ?>" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>¿Vehiculo Empresa?</b></td>
        <td>
          <div id="label-switch" class="make-switch" data-on-label="SI" data-off-label="NO" style="margin-bottom: 5px">
            <input type="checkbox" name="vehiculo_empresa" <?php echo $ruta_local->vehiculo_empresa() ? '' : 'checked' ?>>
          </div>
        </td>
      </tr>
      <tr>
        <td><b>Vehículo</b></td>
        <td>
          <input type="text" maxlength="6" class="input-small" name="ruta_local[placa_vehiculo_2]" id="ruta_local__placa_vehiculo_2" value="<?= isset($ruta_local->placa_vehiculo_2) ? $ruta_local->placa_vehiculo_2 : '' ?>">
          <select title="Seleccione el vehículo" class="input-small" name="ruta_local[placa_vehiculo]" id="ruta_local__placa_vehiculo">
            <option></option>
            <?php
            foreach ($camiones as $camion) {
              $s = '';
              if (isset($ruta_local->placa_vehiculo) and $camion->placa == $ruta_local->placa_vehiculo) {
                $s = 'selected="selected"';
              }
              echo '<option '.$s.' value="'.$camion->placa.'">'.$camion->placa.'</option>';
            }
            ?>
          </select>
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>Conductor</b></td>
        <td>
          <select title="Seleccione el conductor" id="ruta_local_numero_identificacion_conductor" name="ruta_local[numero_identificacion_conductor]">
            <option selected="selected"></option>
            <?php
            foreach ($conductores as $conductor) {
              $s = '';
              if (isset($ruta_local->numero_identificacion_conductor) and $ruta_local->numero_identificacion_conductor == $conductor->numero_identificacion) {
                $s = 'selected="selected"';
              }
              echo '<option '.$s.' value="'.$conductor->numero_identificacion.'">'.$conductor->nombre_completo.'</option>';
            }
            ?>
          </select>
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>Observaciones</b></td>
        <td>
          <textarea name="ruta_local[observaciones]" id="ruta_local_observaciones" rows="3"><?= isset($ruta_local->observaciones) ? $ruta_local->observaciones : '' ?></textarea>
        </td>
        <td></td>
      </tr>
    </table>
  </form>
  <hr class="hr-small">
  <form id="ruta_local__assign_form" class="form-inline" action="#" method="post" style="margin: 0 0 5px 0">
    <table>
      <tr>
        <td><input class="input-small" placeholder="Guía" type="text" id="ruta_local__guia" name="guia[id]" /></td>
        <td><button id="ruta_local__assign" class="btn btn-info"><i class="icon-plus"></i> Asignar</button></td>
      </tr>
    </table>
  </form>
  <table id="ruta_local__guias_list" class="table table-bordered table-condensed">
    <thead>
      <tr>
        <th>Guía</th>
        <th>Cliente</th>
        <th>Destinatario</th>
        <th>Destino</th>
        <th>Unds</th>
        <th>Vr. Mcia</th>
        <th>Flete al Cobro</th>
        <th>Quitar</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (isset($ruta_local->guias)) {
        foreach ($ruta_local->guias as $guia) {
          if($guia->formapago == 'FLETE AL COBRO') {
            $flete = $guia->total+$guia->valorseguro;
          }else{
            $flete = "-";
          }
      ?>
          <tr>
          <td><?= round($guia->id) ?> <input type="hidden" class="guias_asignadas" name="ruta_local[guias][]" value="<?= round($guia->id) ?>" /></td>
          <td><?= $guia->cliente_nombre_completo ?></td>
          <td><?= $guia->contacto_nombre_completo ?></td>
          <td><?= $guia->contacto_ciudad_nombre ?></td>
          <td><?= $guia->unidades ?></td>
          <td><?= $guia->valordeclarado ?></td>
          <td><?= $flete ?></td>
          <td><button class="btn quitar btn-danger"><i class="icon-remove"></i></button></td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
  <center class="form-actions"><button id="ruta_local__save" class="btn btn-primary">Guardar</button></center>
</div>
<script>
(function() {
  LOGISTICA.logistica.RutaLocal = function() {
    var $el = $('#ruta_local__new_container');
    var $form = $el.find('#ruta_local__form');
    var $saveBtn = $el.find('#ruta_local__save');
    var $assignBtn = $el.find('#ruta_local__assign');
    var $assignForm = $el.find('#ruta_local__assign_form');
    var $guiaInput = $assignForm.find('#ruta_local__guia');
    var $guiasList = $el.find('#ruta_local__guias_list');

    var init = function() {
      initSwitch();
      enableSubmit();
      enableDatePicker();
      enableRemoveLink();
      enableAutocomplete();
      validateForm();
      initAssignForm();
    };

    var initSwitch = function() {
      $el.find('#label-switch')
        .bootstrapSwitch()
        .on('switch-change', toggleVehicleSelect)
        .bootstrapSwitch('toggleState');
    };

    var toggleVehicleSelect = function(e, data) {
      var disabled_input = data.value ? 'ruta_local__placa_vehiculo_2' : 'ruta_local__placa_vehiculo';
      var enabled_input = data.value ? 'ruta_local__placa_vehiculo' : 'ruta_local__placa_vehiculo_2';
      $el.find('#'+disabled_input).attr('disabled', 'disabled').hide();
      $el.find('#'+enabled_input).removeAttr('disabled').show();
    };

    var enableSubmit = function() {
      $saveBtn.on('click', function() {
        $form.submit();
      });
    };

    var enableDatePicker = function() {
      $el.find('.fecha').datepicker({
        autoSize:true,
        showOn: "both",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        changeMonth: true,
        maxDate: 0,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        buttonText: 'Seleccionar...'
      });
    };

    var enableRemoveLink = function() {
      $guiasList.on('click', 'button.quitar', function() {
        $(this).parent().parent().remove();
      });
    };

    var enableAutocomplete = function() {
      $el.find('#ruta_local__ciudad').autocomplete({
        minLength: 3,
        autoFocus: true,
        source: helpers_path + 'ajax.php?ciudad=1',
        select: function(event, ui) {
          $el.find('#ruta_local__id_ciudad').val(ui.item.id);
          $el.find('#ruta_local__placa_vehiculo').focus();
        }
      }).focus();
    };

    var validateForm = function() {
      $form.validate({
        rules: {
          'ruta_local[id_ciudad]': {required: true},
          'ruta_local[fecha]': {required: true},
          'ruta_local[placa_vehiculo]': {required: true},
          'ruta_local[placa_vehiculo_2]': {required: true, placa: true},
          'ruta_local[numero_identificacion_conductor]': {required: true}
        },
        messages: {
          'ruta_local[id_ciudad]': {required: 'Selecciona la ciudad.'},
          'ruta_local[fecha]': {required: 'Selecciona la fecha.'},
          'ruta_local[placa_vehiculo]': {required: 'Selecciona el vehículo.'},
          'ruta_local[numero_identificacion_conductor]': {required: 'Seleciona el conductor.'}
        },
        errorPlacement: function(error, element) {
          error.appendTo(element.parent("td").next("td") );
        },
        highlight: function(input) { $(input).addClass("ui-state-highlight"); },
        unhighlight: function(input) { $(input).removeClass("ui-state-highlight"); },
        submitHandler: function(f) {
          $saveBtn.prop('disabled', true).text('Guardando...');
          $.ajax({
            url: rutas_locales_path+'ajax.php',
            type: "POST",
            data: 'action=save&' + $(f).serialize() + '&' + $guiasList.find('input').serialize(),
            success: function(msj){
              if (msj) {
                alertify.error(msj);
                $saveBtn.prop('disabled', false).text('Guardar');
              } else {
                alertify.success('Ruta Local guardada correctamente');
                regresar();
              }
            }
          });
        }
      });
    };

    var initAssignForm = function() {
      $assignForm.submit(function(e) {
        e.preventDefault();
        if (isNaN($guiaInput.val()) || !$.trim($guiaInput.val())) {
          $guiaInput.val('').focus();
          return;
        }
        var e = false;
        $guiasList.find('input.guias_asignadas').each(function(index) {
          if (Number($guiaInput.val()) == $(this).val()) e = true;
        });
        if (e) {
          alertify.log('La guía ya fue agregada.');
          $guiaInput.focus();
          return;
        }
        $assignBtn.prop('disabled', true);
        $.ajax({
          url: helpers_path + 'ajax.php',
          type: "GET", dataType: 'json',
          data: $(this).serialize(),
          success: function(g) {
            $assignBtn.prop('disabled', false);
            if (g.error) {
              alertify.log(g.message);
              $guiaInput.focus();
              return;
            }
            if (g.idestado != 1) {
              alertify.log('El estado de la guía '+g.id+' es '+g.estado+'. Solo se pueden agregar guías en Bodega');
              return;
            }
            g.id = Number(g.id);
            var flete = "-";
            if(g.formapago == 'FLETE AL COBRO') {
              flete = Number(g.total) + Number(g.valorseguro);
            }
            var attrs = {
              id: g.id,
              clienteNombreCompleto: g.cliente.nombre_completo,
              contactoNombreCompleto: g.contacto.nombre_completo,
              contactoCiudadNombre: g.contacto.ciudad_nombre,
              unidades: g.unidades,
              valorDeclarado: g.valordeclarado,
              flete: flete
            };
            $guiasList.find('tbody').append( template(attrs) );
            $guiaInput.val('').focus();
          }
        });
      });
    };

    var template = function(attrs) {
      return '<tr>'+
  '<td>' + attrs.id + '<input type="hidden" class="guias_asignadas" name="ruta_local[guias][]" value="' + attrs.id + '" /></td>'+
  '<td>' + attrs.clienteNombreCompleto + '</td>'+
  '<td>' + attrs.contactoNombreCompleto + '</td>'+
  '<td>' + attrs.contactoCiudadNombre + '</td>'+
  '<td align="right">' + attrs.unidades + '</td>'+
  '<td align="right">' + attrs.valorDeclarado + '</td>'+
  '<td align="right">' + attrs.flete + '</td>'+
  '<td align="center"><button class="btn quitar btn-danger"><i class="icon-remove"></i></button></td>'+
'</tr>';
    };

    return {init: init}
  }();

  LOGISTICA.logistica.RutaLocal.init();
})();
</script>
