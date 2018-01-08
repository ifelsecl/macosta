<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CAMIONES_CREAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
?>
<button class="btn btn-success pull-right" id="regresar">Regresar</button>
<h2>Nuevo Camión</h2>
<hr class="hr-small">
<form id="CrearCamion" name="CrearCamion" method="post" action="#">
  <table border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td><b>Placa</b></td>
      <td><input name="placa" type="text" id="placa" maxlength="6" /></td>
    </tr>
    <tr>
      <td><b>Placa Semirremolque</b></td>
      <td><input name="placa_semiremolque" type="text" id="placa_semiremolque" maxlength="6" /></td>
    </tr>
    <tr>
      <td><b>Marca</b></td>
      <td colspan="2">
        <select name="marca" id="marca" title="Elije la marca">
          <option value="">Selecciona...</option>
          <?php
          foreach (Vehiculo::marcas() as $marca) {
            echo '<option value="'.$marca->codigo_Marcas.'">'.$marca->Descripcion.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>
        <b>Línea</b>
      </td>
      <td>
        <select id="linea" name="linea" title="Elije la linea">
          <option value="">Selecciona...</option>
        </select>
        <img style="display:none;" id="cargando-linea" src="css/ajax-loader.gif" alt="cargando" />
      </td>
    </tr>
    <tr>
      <td><b>Modelo</b></td>
      <td><input name="modelo" type="text" id="modelo" maxlength="4" /></td>
    </tr>
    <tr>
      <td><b>Modelo Repotenciado A</b> <span class="ayuda" title="Puede estar en blanco si el Modelo es mayor o igual a 1990.">[?]</span></td>
      <td><input name="modelo_repotenciado" type="text" id="modelo_repotenciado" maxlength="4" /></td>
    </tr>
    <tr>
      <td><b>Color</b></td>
      <td colspan="3">
        <select name="color" id="color" title="Elije el color">
        <option value="">Selecciona...</option>
        <?php
        foreach (Vehiculo::colores() as $color) {
          echo '<option value="'.$color->codigo_colores.'">'.$color->Descripcion.'</option>';
        }
        ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Carrocería</b></td>
      <td>
        <select name="carroceria" id="carroceria" title="Elije la carrocería">
          <option value="">Selecciona...</option>
          <?php
          foreach (Vehiculo::carrocerias() as $carroceria) {
            echo '<option value="'.$carroceria->codigo_carrocerias.'">'.$carroceria->descripcion.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Configuración</b></td>
      <td>
        <select id="configuracion" name="configuracion" title="Elija la configuración">
          <option value="">Selecciona...</option>
          <?php
          $i = 1;
          $tipo = '';
          foreach (Vehiculo::configuraciones() as $configuracion) {
            if ($i == 1) {
              $tipo = $configuracion->tipo;
              echo '<optgroup label="'.$configuracion->tipo.'">';
            } else {
              if ($configuracion->tipo != $tipo) {
                echo '</optgroup>';
                $tipo = $configuracion->tipo;
                echo '<optgroup label="'.$configuracion->tipo.'">';
              }
            }
            $i += 1;
            if ($configuracion->activo == 'no') {
              echo '<option value="'.$configuracion->id.'">'.$configuracion->configuracion.' (No usar, pronto será eliminado)</option>';
            } else {
              echo '<option value="'.$configuracion->id.'">'.$configuracion->configuracion.'</option>';
            }
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Número de Ejes</b></td>
      <td><input class="input-mini" maxlength="2" name="numero_ejes" type="text" id="numero_ejes" /></td>
    </tr>
    <tr>
      <td><b>Tipo de Combustible</b></td>
      <td>
        <select name="tipo_combustible" id="tipo_combustible">
          <option value="">Selecciona...</option>
          <?php
          foreach (Vehiculo::$tipos_combustible as $id => $nombre) {
            echo '<option value="'.$id.'">'.$nombre.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Peso (kg)</b> <span class="ayuda" title="Peso vacío del camión en kilogramos">[?]</span></td>
      <td><input name="peso" type="text" id="peso" maxlength="5" /></td>
    </tr>
    <tr>
      <td><b>Registro de carga</b> <span class="ayuda" title="Registro nacional de carga del camión, si lo tiene">[?]</span></td>
      <td><input type="text" id="registro" name="registro" maxlength="8" /></td>
    </tr>
    <tr>
      <td><b>Aseguradora</b></td>
      <td colspan="3">
        <select id="aseguradora" name="aseguradora" title="Elija la aseguradora">
          <option value="">Selecciona...</option>
          <?php
          foreach (Vehiculo::aseguradoras() as $aseguradora) {
            echo '<option value="'.$aseguradora->nit.'">'.$aseguradora->nombre.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Capacidad de carga</b></td>
      <td><input type="text" id="capacidad_carga" name="capacidad_carga" maxlength="6" /></td>
    </tr>
    <tr>
      <td><b>Unidad Medida Capacidad de carga</td>
      <td>
        <select name="unidad_medida_capacidad_carga" id="vehiculo_unidad_medida_capacidad_carga">
          <?php
          foreach (Vehiculo::$unidades_medida_capacidad_carga as $key => $value) {
            echo '<option value="'.$key.'">'.$value.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Propietario</b></td>
      <td>
        <input size="40" type="text" id="nombre_propietario" name="nombre_propietario" />
        <input type="hidden" name="id_propietario" id="id_propietario" />
      </td>
    </tr>
    <tr>
      <td><b>Tenedor</b></td>
      <td>
        <input size="40" type="text" id="nombre_tenedor" name="nombre_tenedor" />
        <input type="hidden" name="id_tenedor" id="id_tenedor" />
      </td>
    </tr>
    <tr>
      <td><b>Kilometraje Inicial</b></td>
      <td><input type="text" name="km_inicial" id="km_inicial" value="0" /></td>
    </tr>
    <tr>
      <td><b>Kilometraje Actual</b></td>
      <td><input type="text" name="km_actual" id="km_actual" value="0" /></td>
    </tr>
    <tr>
      <td colspan="5"><hr class="hr-small"></td>
    </tr>
  </table>
  <div class="row-fluid">
    <div class="span6">
      <fieldset>
        <legend>Matricula</legend>
        <table>
          <tr>
            <td><b>Número de la serie:</b></td>
            <td><input name="serie" type="text" id="serie" /></td>
          </tr>
          <tr>
            <td><b>Fecha Matricula:</b></td>
            <td><input name="fecha_matricula" type="text" id="fecha_matricula" /></td>
          </tr>
        </table>
      </fieldset>
    </div>
    <div class="span6">
      <fieldset>
        <legend>Matricula</legend>
        <table>
          <tr>
            <td><b>Número de la serie:</b></td>
            <td><input name="serie" type="text" id="serie" /></td>
          </tr>
        </table>
      </fieldset>
    </div>
  </div>
  <table>
    <tr>
      <td>
        <fieldset class="vencimientos">  <!-- SOAT -->
          <legend>SOAT</legend>
          <table cellspacing="3" cellpadding="3">
            <tr>
              <td>Número:</td>
              <td><input class="input-medium" maxlength="20" name="soat" type="text" id="soat" /></td>
            </tr>
            <tr>
              <td>Fecha vencimiento:</td>
              <td><input readonly="readonly" class="fecha input-small" name="f_venc_soat" type="text" id="f_venc_soat" /></td>
            </tr>
          </table>
        </fieldset>
      </td>
      <td>
        <fieldset class="vencimientos">  <!-- SEGURO -->
          <legend>Seguro</legend>
          <table cellspacing="3" cellpadding="3">
            <tr>
              <td>Número:</td>
              <td><input class="input-medium" name="num_seguro" type="text" id="num_seguro" /></td>
            </tr>
            <tr>
              <td>Fecha vencimiento:</td>
              <td><input class="fecha input-small" readonly="readonly" name="f_venc_seguro" type="text" id="f_venc_seguro" /></td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset class="vencimientos">
          <legend>Tarjeta de operación</legend>
          <table cellspacing="3" cellpadding="3">
            <tr>
              <td>Número:</td>
              <td><input class="input-medium" name="t_operacion" type="text" id="t_operacion" /></td>
            </tr>
            <tr>
              <td>Fecha vencimiento:</td>
              <td><input class="fecha input-small" readonly="readonly" name="f_venc_toperacion" type="text" id="f_venc_toperacion" /></td>
            </tr>
          </table>
        </fieldset>
      </td>
      <td>
        <fieldset class="vencimientos">
          <legend>Revisión Técnico Mecánica</legend>
          <table cellspacing="3" cellpadding="3">
            <tr>
              <td>Número:</td>
              <td><input class="input-medium" name="tecnico_meca" type="text" id="tecnico_meca" /></td>
            </tr>
            <tr>
              <td>Fecha vencimiento:</td>
              <td><input class="fecha input-small" readonly="readonly" name="f_venc_tmec" type="text" id="f_venc_tmec" /></td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td colspan="3">
        <hr class="hr-small">
      </td>
    </tr>
    <tr>
      <td align="center" colspan="3">
        <button type="submit" id="guardar">Guardar</button>
      </td>
    </tr>
  </table>
  <?php nonce_create_form_input("Guardar") ?>
</form>
<script>
(function() {
  var $el = $('#CrearCamion');
  var configuraciones_kilogramos = ["50", "55", "56", "64", "74", "85"];
  $('#guardar').button({icons: { primary: "ui-icon-circle-check"}});
  $('#placa').focus();

  $("#regresar").click(function() {
    regresar();
  });

  $('#marca').change(function() {
    if ($(this).val() == 0) return;
    $('#cargando-linea').show();
    $.ajax({
      url: vehiculos_path+'ajax.php',type: 'POST',
      data: 'search_linea=1&idmarca='+$(this).val(),
      success: function(msj) {
        $('#linea').html(msj);
        $('#cargando-linea').fadeOut(300);
      }
    });
  });

  $el.find(".fecha").datepicker({
    autoSize: true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    buttonText:'Seleccionar...'
  });

  $('#nombre_propietario, #nombre_tenedor').autocomplete({
    autoFocus:true,
    source: helpers_path+'ajax.php?tercero=1',
    select: function(event, ui) {
      if (event.target.id=='nombre_propietario') {
        $('#id_propietario').val(ui.item.id);
        $('#nombre_tenedor').focus();
      } else {
        $('#id_tenedor').val(ui.item.id);
        $('#km_inicial').focus();
      }
    }
  });
  $('#configuracion').change(function() {
    if (configuraciones_kilogramos.indexOf($('#configuracion').val()) != -1 ) {
      $('#unidad_medida_capacidad_carga').val('1');
    }
  });
  $('#CrearCamion').validate({
    rules: {
      placa: {required: true, placa: true},
      placa_semiremolque: {placa_semiremolque: true},
      marca: {required: true},
      linea: 'required',
      modelo: {required: true, digits: true, min: 1900},
      modelo_repotenciado: {digits: true, min: 1900},
      serie: {required: true, maxlength: 50},
      tipo_combustible: 'required',
      numero_ejes: {required: true, digits: true},
      peso: {required: true, digits: true, range: [200, 53000]},
      color: 'required',
      carroceria: 'required',
      configuracion: 'required',
      soat: {required: true, digits: true},
      f_venc_soat: 'required',
      num_seguro: {required: true, digits: true},
      f_venc_seguro: 'required',
      t_operacion: {required: true, digits: true},
      f_venc_toperacion: 'required',
      tecnico_meca: {digits: true},
      aseguradora: 'required',
      vencimientosoat: 'required',
      nombre_propietario: 'required',
      nombre_tenedor: 'required',
      capacidad_carga: {
        required: function(element) {
          return configuraciones_kilogramos.indexOf($('#configuracion').val()) != -1;
        }
      },
      unidad_medida_capacidad_carga: 'required',
      km_inicial: {required: true, number: true},
      km_actual: {required: true, number: true}
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        type: 'POST', url: vehiculos_path+'ajax.php',
        data: 'guardar=1&'+$(form).serialize(),
        success: function(response, textStatus, XMLHttpRequest) {
          if (response == "ok") {
            regresar();
          } else {
            $('#guardar').button('enable').button('option','label','Guardar');
            LOGISTICA.Dialog.open('Error', response, true);
          }
        }
      });
    }
  });
})();
</script>
