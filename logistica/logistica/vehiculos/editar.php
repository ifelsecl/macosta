<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CAMIONES_EDITAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
Logistica::check_nonce($_GET['placa'], $_GET[NONCE_KEY]);

if (! $vehiculo = Vehiculo::find($_GET['placa'])) exit('No existe el vehículo');
$marcas = Vehiculo::marcas();
$lineas = $vehiculo->lineas();
$colores = Vehiculo::colores();
$carrocerias = Vehiculo::carrocerias();
$configuraciones = Vehiculo::configuraciones();
$aseguradoras = Vehiculo::aseguradoras();
?>
<div id="vehiculos__editar">
  <button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
  <h2>Editar Vehículo <?= $vehiculo->placa ?></h2>
  <hr class="hr-small">
  <form method="post" action="#">
    <table>
      <tr>
        <td><b>Placa</b></td>
        <td><input name="placa" type="text" id="placa" readonly="readonly" maxlength="6" value="<?= $vehiculo->placa ?>" /></td>
      </tr>
      <tr>
        <td><b>Placa Semirremolque</b></td>
        <td><input name="placa_semiremolque" type="text" id="placa_semiremolque" maxlength="6" value="<?= $vehiculo->placa_semiremolque ?>" /></td>
      </tr>
      <tr>
        <td><b>Marca</b></td>
        <td colspan="2">
          <select name="codigo_Marcas" id="codigo_Marcas" title="Elije la marca">
            <?php
            foreach ($marcas as $marca) {
              $s = $marca->codigo_Marcas == $vehiculo->codigo_Marcas ? 'selected="selected"' : '';
              echo '<option value="'.$marca->codigo_Marcas.'" '.$s.'>'.$marca->Descripcion.'</option>';
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
          <select id="codigo_linea" name="codigo_linea" title="Elije la linea">
          <?php
          foreach ($lineas as $linea) {
            $s = $linea->codigo == $vehiculo->codigo_linea ? 'selected="selected"' : '';
            echo '<option value="'.$linea->codigo.'" '.$s.'>'.$linea->descripcion.'</option>';
          }
          ?>
          </select>
          <div id="cargando-linea" style="display:none"><img src="css/ajax-loader.gif" alt="cargando" /></div>
        </td>
      </tr>
      <tr>
        <td><b>Modelo</b></td>
        <td><input name="modelo" type="text" id="modelo" maxlength="4" value="<?= $vehiculo->modelo ?>" /></td>
      </tr>
      <tr>
        <td><b>Modelo Repotenciado A</b></td>
        <td><input name="modelo_repotenciado" type="text" id="modelo_repotenciado" maxlength="4" value="<?= $vehiculo->modelo_repotenciado ?>" /></td>
      </tr>
      <tr>
        <td><b>Número de la serie</b></td>
        <td><input name="serie" type="text" id="serie" maxlength="50" value="<?= $vehiculo->serie ?>" /></td>
      </tr>
      <tr>
        <td><b>Fecha Matricula</b></td>
        <td><input name="fecha_matricula" class="fecha input-small" type="text" id="vehiculo__fecha_matricula" value="<?= $vehiculo->fecha_matricula ?>" /></td>
      </tr>
      <tr>
        <td><b>Número Chasis</b></td>
        <td><input name="numero_chasis" type="text" id="vehiculo__numero_chasis" maxlength="30" value="<?= $vehiculo->numero_chasis ?>" /></td>
      </tr>
      <tr>
        <td><b>Número Motor</b></td>
        <td><input name="numero_motor" type="text" id="vehiculo__numero_motor" maxlength="55" value="<?= $vehiculo->numero_motor ?>" /></td>
      </tr>
      <tr>
        <td><b>Número Licencia Transito</b></td>
        <td><input name="numero_licencia_transito" type="text" id="vehiculo__numero_licencia_transito" maxlength="15" value="<?= $vehiculo->numero_licencia_transito ?>" /></td>
      </tr>
      <tr>
        <td><b>Número Ficha Homologación</b></td>
        <td><input name="numero_ficha_homologacion" type="text" id="vehiculo__numero_ficha_homologacion" maxlength="30" value="<?= $vehiculo->numero_ficha_homologacion ?>" /></td>
      </tr>
      <tr>
        <td><b>Color</b></td>
        <td colspan="3">
          <select name="codigo_colores" id="codigo_colores" title="Elije el color">
          <?php
          foreach ($colores as $color) {
            $s = $vehiculo->codigo_colores == $color->codigo_colores ? 'selected="selected"' : '';
            echo '<option value="'.$color->codigo_colores.'" '.$s.'>'.$color->Descripcion.'</option>';
          }
          ?>
          </select>
        </td>
      </tr>
      <tr>
        <td><b>Carrocería</b></td>
        <td>
          <select name="codigo_carrocerias" id="codigo_carrocerias" title="Elije la carrocería">
            <?php
            foreach ($carrocerias as $carroceria) {
              $s = $carroceria->codigo_carrocerias == $vehiculo->codigo_carrocerias ? 'selected="selected"' : '';
              echo '<option value="'.$carroceria->codigo_carrocerias.'" '.$s.'>'.$carroceria->descripcion.'</option>';
            }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td><b>Configuración</b></td>
        <td>
          <select id="idconfiguracion" name="idconfiguracion" title="Elija la configuración">
            <option value="">Selecciona...</option>
            <?php
            $i = 1;
            $tipo = '';
            foreach ($configuraciones as $conf) {
              if ($i == 1) {
                $tipo = $conf->tipo;
                echo '<optgroup label="'.$conf->tipo.'">';
              } else {
                if ($conf->tipo != $tipo) {
                  echo '</optgroup>';
                  $tipo = $conf->tipo;
                  echo '<optgroup label="'.$conf->tipo.'">';
                }
              }
              $i++;
              $s = $vehiculo->idconfiguracion == $conf->id ? 'selected="selected"' : '';
              echo '<option value="'.$conf->id.'" '.$s.'>'.$conf->configuracion.'</option>';
            }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td><b>Número de Ejes</b></td>
        <td><input maxlength="2" name="numero_ejes" type="text" id="numero_ejes" value="<?= $vehiculo->numero_ejes ?>" /></td>
      </tr>
      <tr>
        <td><b>Tipo de Combustible</b></td>
        <td>
          <select name="tipo_combustible" id="tipo_combustible">
            <option value="">Selecciona...</option>
            <?php
            foreach (Vehiculo::$tipos_combustible as $id => $nombre) {
              $s = $id == $vehiculo->tipo_combustible ? 'selected="selected"' : '';
              echo '<option '.$s.' value="'.$id.'">'.$nombre.'</option>';
            }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td title="Peso del camión vacio en kilogramos"><b>Peso (kg)</b></td>
        <td><input name="peso" type="text" id="peso" maxlength="5" value="<?= $vehiculo->peso ?>" /></td>
      </tr>
      <tr>
        <td><b>Registro de carga</b> <span class="ayuda" title="Registro nacional de carga del camión, si lo tiene">?</span></td>
        <td><input type="text" id="registro" name="registro" maxlength="8" value="<?= $vehiculo->registro ?>" /></td>
      </tr>
      <tr>
        <td><b>Capacidad de carga</b></td>
        <td>
          <input type="text" id="capacidadcarga" name="capacidadcarga" class="input-small" maxlength="5" value="<?= $vehiculo->capacidadcarga ?>" />
          <select class="input-medium" name="unidad_medida_capacidad_carga" id="unidad_medida_capacidad_carga">
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
          <input type="text" id="nombre_propietario" name="nombre_propietario" value="<?= $vehiculo->propietario()->nombre_completo ?>" />
          <input type="hidden" name="id_propietario" id="id_propietario" value="<?= $vehiculo->idpropietario ?>" />
        </td>
      </tr>
      <tr>
        <td><b>Tenedor</b></td>
        <td>
          <input type="text" id="nombre_tenedor" name="nombre_tenedor" value="<?= $vehiculo->tenedor()->nombre_completo ?>" />
          <input type="hidden" name="id_tenedor" id="id_tenedor" value="<?= $vehiculo->id_tenedor ?>" />
        </td>
      </tr>
      <tr>
        <td><b>Kilometraje Inicial</b></td>
        <td><input type="text" name="km_inicial" id="km_inicial" value="<?= $vehiculo->km_inicial ?>" /></td>
      </tr>
      <tr>
        <td><b>Kilometraje Actual</b></td>
        <td><input type="text" name="km_actual" id="km_actual" value="<?= $vehiculo->km_actual ?>" /></td>
      </tr>
      <tr>
        <td colspan="5"><hr class="hr-small"></td>
      </tr>
    </table>
    <table>
      <tr>
        <td>
          <fieldset><!-- SOAT -->
            <legend>SOAT</legend>
            <table>
              <tr>
                <td>Número Póliza:</td>
                <td><input name="soat" type="text" id="soat" value="<?= $vehiculo->soat ?>" /></td>
              </tr>
              <tr>
                <td>Aseguradora:</td>
                <td colspan="3">
                  <select id="nitaseguradora" name="nitaseguradora" title="Elija la aseguradora">
                    <option></option>
                    <?php
                    foreach ($aseguradoras as $aseguradora) {
                      $s = $aseguradora->nit == $vehiculo->nitaseguradora ? 'selected="selected"' : '';
                      echo '<option value="'.$aseguradora->nit.'" '.$s.'>'.$aseguradora->nombre."</option>";
                    }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Fecha Expedición:</td>
                <td><input readonly="readonly" class="fecha input-small" name="fecha_expedicion_soat" type="text" id="fecha_expedicion_soat" value="<?= $vehiculo->fecha_expedicion_soat ?>" /></td>
              </tr>
              <tr>
                <td>Fecha Vencimiento:</td>
                <td><input readonly="readonly" class="fecha input-small" name="f_venc_soat" type="text" id="f_venc_soat" value="<?= $vehiculo->f_venc_soat ?>" /></td>
              </tr>
            </table>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td>
          <fieldset>
            <legend>Tarjeta de operación</legend>
            <table>
              <tr>
                <td>Número:</td>
                <td><input name="t_operacion" type="text" id="t_operacion" value="<?= $vehiculo->t_operacion ?>" /></td>
              </tr>
              <tr>
                <td>Fecha Afiliación:</td>
                <td><input class="fecha input-small" readonly="readonly" name="fecha_afiliacion" type="text" id="fecha_afiliacion" value="<?= $vehiculo->fecha_afiliacion ?>" /></td>
              </tr>
              <tr>
                <td>Fecha vencimiento:</td>
                <td><input class="fecha input-small" readonly="readonly" name="f_venc_toperacion" type="text" id="f_venc_toperacion" value="<?= $vehiculo->f_venc_toperacion ?>" /></td>
              </tr>
            </table>
          </fieldset>
        </td>
        <td>
          <fieldset>
            <legend>Revisión Técnico Mecánica</legend>
            <table>
              <tr>
                <td>Número:</td>
                <td><input name="tecnico_meca" type="text" id="tecnico_meca" value="<?= $vehiculo->tecnico_meca ?>" /></td>
              </tr>
              <tr>
                <td>Fecha vencimiento:</td>
                <td><input class="fecha input-small" readonly="readonly" name="f_venc_tmec" type="text" id="f_venc_tmec" value="<?= $vehiculo->f_venc_tmec ?>" /></td>
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
          <button class="btn btn-primary" type="submit" id="guardar">Guardar</button>
        </td>
      </tr>
    </table>
    <?php nonce_create_form_input("Guardar") ?>
  </form>
</div>
<script>
(function() {
  LOGISTICA.logistica.vehiculos = function() {
    var configuraciones_kilogramos = ["50", "55", "56", "64", "74", "85"];
    var $el = $('#vehiculos__editar');
    var $form = $el.find('form');
    var $saveBtn = $el.find('#guardar');

    var init = function() {
      $el.find('#placa').focus();
      initDatePicker();
      initBrand();
      initAutocomplete();
      initConfiguration();
      initFormValidator();
    };
    var initDatePicker =  function() {
      $el.find(".fecha").datepicker({
        autoSize: true,
        showOn: "both",
        yearRange: "-50:c+10",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        buttonText:'Seleccionar...'
      });
    };
    var initBrand = function() {
      $el.find('#codigo_Marcas').change(function() {
        if ($(this).val() == 0) return;
        $el.find('#codigo_linea').hide();
        $el.find('#cargando-linea').show();
        $.ajax({
          url: vehiculos_path+'ajax.php',type: 'POST',
          data: 'search_linea=1&idmarca='+$(this).val(),
          success: function(msj) {
            $el.find('#codigo_linea').html(msj).show();
            $el.find('#cargando-linea').fadeOut(200);
          }
        });
      });
    };
    var initAutocomplete = function() {
      $el.find('#nombre_propietario, #nombre_tenedor').autocomplete({
        autoFocus: true,
        source: helpers_path+'ajax.php?tercero=1',
        select: function(event, ui) {
          if (event.target.id == 'nombre_propietario') {
            $el.find('#id_propietario').val(ui.item.id);
            $el.find('#nombre_tenedor').focus();
          } else {
            $el.find('#id_tenedor').val(ui.item.id);
            $el.find('#km_inicial').focus();
          }
        }
      });
    };
    var initConfiguration = function() {
      $el.find('#idconfiguracion').change(function() {
        if (configuraciones_kilogramos.indexOf($(this).val()) != -1 ) {
          $el.find('#unidad_medida_capacidad_carga').val('1');
        }
      });
    };
    var initFormValidator = function() {
      $form.validate({
        rules: {
          placa: {required: true, placa: true},
          placa_semiremolque: {placa_semiremolque: true},
          codigo_Marcas: 'required',
          codigo_linea: 'required',
          modelo: {required: true, digits: true, min: 1900, length: 4},
          modelo_repotenciado: {digits: true, min: 1900, length: 4},
          serie: {required: true, maxlength: 50},
          tipo_combustible: 'required',
          numero_ejes: {required: true, digits: true},
          peso: {required:true, digits: true, range: [200, 53000]},
          codigo_colores: 'required',
          codigo_carrocerias: 'required',
          idconfiguracion: 'required',
          soat: {required: true, digits: true},
          f_venc_soat: 'required',
          num_seguro: {required: true, digits: true},
          f_venc_seguro: 'required',
          t_operacion: {required: true, digits: true},
          f_venc_toperacion: 'required',
          tecnico_meca: {digits: true},
          nitaseguradora: 'required',
          vencimientosoat: 'required',
          nombre_propietario: 'required',
          nombre_tenedor: 'required',
          capacidadcarga: {
            rangelength: [3, 5],
            required: function(element) {
              return configuraciones_kilogramos.indexOf($el.find('#configuracion').val()) != -1;
            }
          },
          unidad_medida_capacidad_carga: 'required',
          km_inicial: {required: true, number: true},
          km_actual: {required: true, number: true},
          fecha_afiliacion: 'required'
        },
        highlight: function(input) {$(input).addClass("ui-state-highlight");},
        unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
        submitHandler: function(form) {
          $saveBtn.prop('disabled', true).text('Guardando...');
          $.ajax({
            type: 'POST',
            url: vehiculos_path+'ajax.php',
            data: 'editar=1&'+$(form).serialize(),
            success:function(response) {
              if (response) {
                $saveBtn.prop('disabled', false).text('Guardar');
                alertify.error(response);
              } else {
                regresar();
              }
            }
          });
        }
      });
    }
    return {
      init: init
    }
  }();
  LOGISTICA.logistica.vehiculos.init();
})();
</script>
