<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][OPCIONES_ENTRAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (isset($_POST['guardar'])) {
  foreach ($_POST as $key => $value) {
    if ($key != "guardar" and $key != "_nonce") {
      $opciones[$key] = $value;
    }
  }
  if (Configuracion::save($opciones)) {
    Logger::opciones('modificó las opciones.');
  } else {
    echo "error";
  }
  exit;
}
$configuracion = new Configuracion;
?>
<div id="opciones__index">
  <div class="row-fluid">
    <div class="span12"><h2>Opciones</h2></div>
  </div>
  <div class="row-fluid">
    <div class="span12">
      <form id="opciones_save" name="opciones_save" method="post" action="#">
        <div id="tabs-opciones">
          <ul>
            <li><a href="#app" title="Opciones de la aplicación">Aplicación</a></li>
            <li><a href="#empresa" title="Datos de la empresa">Empresa</a></li>
            <li><a href="#seguro" title="Seguro de la mercancía">Seguro</a></li>
            <li><a href="#guias_opciones" title="Opciones de Guías">Guías</a></li>
            <li><a href="#siigo" title="Opciones de SIIGO">SIIGO</a></li>
            <li><a href="#opciones_facturacion" title="Opciones de Facturación">Facturación</a></li>
            <li><a href="#ftp" title="Servidor FTP del Ministerio de Transporte">FTP</a></li>
          </ul>
          <div id="app">
            <table>
              <tr>
                <td><b>Número de intentos fallidos: <span class="ayuda" title="Número de intentos de inicio de sesión fallidos antes de desactivar la cuenta">[?]</span></b></td>
                <td align="left"><input type="text" name="app_numero_intentos" id="app_numero_intentos" value="<?= $configuracion->app_numero_intentos ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Calcular precio Kilo y Kilo/Vol: <span class="ayuda" title="Indica si se debe calcular automáticamente el precio para Kilo y Kilo/Vol para una caja cuando se importa una lista de precios.">[?]</span></b></td>
                <td>
                  <select name="lp_cal_kilo_kilovol">
                    <option value="si" <?php if ($configuracion->lp_cal_kilo_kilovol=='si') echo 'selected="selected"' ?>>Si</option>
                    <option value="no" <?php if ($configuracion->lp_cal_kilo_kilovol=='no') echo 'selected="selected"' ?>>No</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td><b>Ruta para las guías escaneadas:</b> (termina con '/')</td>
                <td>
                  <input type="text" name="app_ruta_guias" id="app_ruta_guias" value="<?= $configuracion->app_ruta_guias ?>" />
                </td>
              </tr>
              <tr>
                <td><b>Ruta para las recogidas escaneadas:</b> (termina con '/')</td>
                <td>
                  <input type="text" name="app_ruta_ordenes" id="app_ruta_ordenes" value="<?= $configuracion->app_ruta_ordenes ?>" />
                </td>
              </tr>
            </table>
          </div>
          <div id="empresa">
            <table>
              <tr>
                <td><b>Razón Social</b></td>
                <td><input type="text" name="nombre_empresa" id="nombre_empresa" value="<?= $configuracion->nombre_empresa ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Sigla</b></td>
                <td><input type="text" name="empresa_sigla" id="empresa_sigla" value="<?= $configuracion->empresa_sigla ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>NIT</b></td>
                <td><input type="text" name="nit_empresa" id="nit_empresa" value="<?= $configuracion->nit_empresa ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Dirección Sede Principal</b></td>
                <td><input type="text" name="direccion_empresa" id="direccion_empresa" value="<?= $configuracion->direccion_empresa ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Teléfono Fijo Sede Principal</b></td>
                <td><input type="text" name="empresa_telefono_sede_principal" id="empresa_telefono_sede_principal" value="<?= $configuracion->empresa_telefono_sede_principal ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Teléfonos</b></td>
                <td><input type="text" name="telefono_empresa" id="telefono_empresa" value="<?= $configuracion->telefono_empresa ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Ciudad</b></td>
                <td><input type="text" name="ciudad_empresa" id="ciudad_empresa" value="<?= $configuracion->ciudad_empresa ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Email</b></td>
                <td><input type="text" name="email_empresa" id="email_empresa" value="<?= $configuracion->email_empresa ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Código Regional</b></td>
                <td><input type="text" id="codigo_regional" name="codigo_regional" value="<?= $configuracion->codigo_regional ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Código de la empresa</b></td>
                <td><input type="text" id="codigo_empresa" name="codigo_empresa" value="<?= $configuracion->codigo_empresa ?>" /></td>
                <td></td>
              </tr>
            </table>
          </div>
          <div id="seguro">
            <table>
              <tr>
                <td><b>Compañia de seguros</b></td>
                <td>
                  <input type="text" name="aseguradora_mercancia" id="aseguradora_mercancia" value="<?= $configuracion->aseguradora_mercancia ?>" />
                  <input type="hidden" name="nit_aseguradora_mercancia" id="nit_aseguradora_mercancia" value="<?= $configuracion->nit_aseguradora_mercancia ?>" />
                </td>
                <td></td>
              </tr>
              <tr>
                <td><b>Número poliza</b></td>
                <td><input type="text" name="numero_poliza_mercancia" id="numero_poliza_mercancia" value="<?= $configuracion->numero_poliza_mercancia ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Vigencia poliza</b></td>
                <td><input type="text" class="fecha" name="vigencia_poliza_mercancia" id="vigencia_poliza_mercancia" value="<?= $configuracion->vigencia_poliza_mercancia ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Teléfono</b></td>
                <td><input type="text" name="telefono_aseguradora_mercancia" id="telefono_aseguradora_mercancia" value="<?= $configuracion->telefono_aseguradora_mercancia ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Dirección</b></td>
                <td><input type="text" name="direccion_aseguradora_mercancia" id="direccion_aseguradora_mercancia" value="<?= $configuracion->direccion_aseguradora_mercancia ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Ciudad</b></td>
                <td>
                  <input type="text" name="ciudad_aseguradora_mercancia" id="ciudad_aseguradora_mercancia" value="<?= $configuracion->ciudad_aseguradora_mercancia ?>" />
                  <input type="hidden" name="id_ciudad_aseguradora_mercancia" id="id_ciudad_aseguradora_mercancia" value="<?= $configuracion->id_ciudad_aseguradora_mercancia ?>" />
                </td>
                <td></td>
              </tr>
            </table>
          </div>
          <div id="guias_opciones">
            <table>
              <tr>
                <td><b>Constante para el cálculo de Kilo/Vol:</b></td>
                <td>
                  <input type="text" name="calKiloVolumen" id="calKiloVolumen" value="<?= $configuracion->calKiloVolumen ?>" />
                </td>
                <td></td>
              </tr>
              <tr>
                <td><b>Pie de página (impresión):</b></td>
                <td>
                  <input type="text" name="guias_pie_pagina" id="guias_pie_pagina" value="<?= $configuracion->guias_pie_pagina ?>" />
                </td>
                <td></td>
              </tr>
              <tr>
                <td><b>Aviso de contrato de Guia (impresión):</b></td>
                <td>
                  <input type="text" name="contrato" id="contrato"  value="<?= $configuracion->contrato ?>" />
                </td>
                <td></td>
              </tr>
            </table>
          </div>
          <div id="siigo">
            <table cellpadding="0">
              <tr>
                <td><b>Sucursal</b></td>
                <td><input type="text" name="siigo_sucursal" id="siigo_sucursal" value="<?= $configuracion->siigo_sucursal ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Centro de costo</b></td>
                <td><input type="text" name="siigo_centro_costo" id="siigo_centro_costo" value="<?= $configuracion->siigo_centro_costo ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Subcentro de costo</b></td>
                <td><input type="text" name="siigo_subcentro_costo" id="siigo_subcentro_costo" value="<?= $configuracion->siigo_subcentro_costo ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Código del vendedor</b></td>
                <td><input type="text" name="siigo_codigo_vendedor" id="siigo_codigo_vendedor" value="<?= $configuracion->siigo_codigo_vendedor ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Código de la ciudad</b></td>
                <td><input type="text" name="siigo_codigo_ciudad" id="siigo_codigo_ciudad" value="<?= $configuracion->siigo_codigo_ciudad ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Código de la zona</b></td>
                <td><input type="text" name="siigo_codigo_zona" id="siigo_codigo_zona" value="<?= $configuracion->siigo_codigo_zona ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Código de la bodega</b></td>
                <td><input type="text" name="siigo_codigo_bodega" id="siigo_codigo_bodega" value="<?= $configuracion->siigo_codigo_bodega ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Código de la ubicación</b></td>
                <td><input type="text" name="siigo_codigo_ubicacion" id="siigo_codigo_ubicacion" value="<?= $configuracion->siigo_codigo_ubicacion ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Código del banco</b></td>
                <td><input type="text" name="siigo_codigo_banco" id="siigo_codigo_banco" value="<?= $configuracion->siigo_codigo_banco ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Cuenta Contable Total (Contado)</b></td>
                <td><input type="text" maxlength="10" name="siigo_cuenta_contable_total_contado" id="siigo_cuenta_contable_total_contado" value="<?= $configuracion->siigo_cuenta_contable_total_contado ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Cuenta Contable Total (Credito)</b></td>
                <td><input type="text" maxlength="10" name="siigo_cuenta_contable_total_credito" id="siigo_cuenta_contable_total_credito" value="<?= $configuracion->siigo_cuenta_contable_total_credito ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Cuenta Contable Flete</b></td>
                <td><input type="text" maxlength="10" name="siigo_cuenta_contable_total_flete" id="siigo_cuenta_contable_total_flete" value="<?= $configuracion->siigo_cuenta_contable_total_flete ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Cuenta Contable Seguro</b></td>
                <td><input type="text" maxlength="10" name="siigo_cuenta_contable_total_seguro" id="siigo_cuenta_contable_total_seguro" value="<?= $configuracion->siigo_cuenta_contable_total_seguro ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Cuenta Contable Descuento</b></td>
                <td><input type="text" maxlength="10" name="siigo_cuenta_contable_descuento" id="siigo_cuenta_contable_descuento" value="<?= $configuracion->siigo_cuenta_contable_descuento ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Cuenta Contable CREE Credito</b></td>
                <td><input type="text" maxlength="10" name="siigo_cuenta_contable_cree_credito" id="siigo_cuenta_contable_cree_credito" value="<?= $configuracion->siigo_cuenta_contable_cree_credito ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Cuenta Contable CREE Debito</b></td>
                <td><input type="text" maxlength="10" name="siigo_cuenta_contable_cree_debito" id="siigo_cuenta_contable_cree_debito" value="<?= $configuracion->siigo_cuenta_contable_cree_debito ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Porcentaje CREE</b></td>
                <td><input type="text" name="siigo_cree_porcentaje" id="siigo_cree_porcentaje" value="<?= $configuracion->siigo_cree_porcentaje ?>" /></td>
                <td></td>
              </tr>
            </table>
          </div>
          <div id="opciones_facturacion">
            <table>
              <tr>
                <td><b>Prefijo:</b></td>
                <td><input type="text" name="facturacion_prefijo" id="facturacion_prefijo" value="<?= $configuracion->facturacion_prefijo ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Tarifa Aplicar:</b></td>
                <td><input type="text" name="facturacion_tarifa_aplicar" id="facturacion_tarifa_aplicar" value="<?= $configuracion->facturacion_tarifa_aplicar ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Cód. Actividad Principal:</b></td>
                <td><input type="text" name="facturacion_codigo_actividad_principal" id="facturacion_codigo_actividad_principal" value="<?= $configuracion->facturacion_codigo_actividad_principal ?>" /></td>
                <td></td>
              </tr>
            </table>
          </div>
          <div id="ftp">
            <p class="ayuda">Define los datos para la conexión con el servidor FTP del Ministerio de Transporte.</p>
            <table style="font-size:110%">
              <tr>
                <td><b>Servidor FTP:</b></td>
                <td><input type="text" name="ftp_servidor" id="ftp_servidor" value="<?= $configuracion->ftp_servidor ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Usuario:</b></td>
                <td><input type="text" name="ftp_usuario" id="ftp_usuario" value="<?= $configuracion->ftp_usuario ?>" /></td>
                <td></td>
              </tr>
              <tr>
                <td><b>Contraseña:</b></td>
                <td><input type="text" name="ftp_clave" id="ftp_clave" value="<?= $configuracion->ftp_clave ?>" /></td>
                <td></td>
              </tr>
            </table>
          </div>
        </div>
        <?php nonce_create_form_input("opciones_save") ?>
        <center id="errores" style="display:none;" class="ui-corner-all"></center>
        <br>
        <center><button type="submit" id="guardar">Guardar</button></center>
      </form>
    </div>
  </div>
</div>
<script>
(function() {
  LOGISTICA.configuracion.opciones = function() {
    var $el = $('#opciones__index');

    var init = function() {
      $el.find('#guardar').button({icons: {primary: "ui-icon-circle-check"}});
      initTabs();
      initDatePicker();
      initAutocomplete();
      initForm();
    };

    var initTabs = function() {
      $el.find("#tabs-opciones").tabs();
    };

    var initDatePicker = function() {
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
    };

    var initAutocomplete = function() {
      $el.find('#aseguradora_mercancia').autocomplete({
        minLength:3,
        source: opciones_path+'ajax.php?buscar_aseguradora=1011',
        select: function(event,ui) {
          $('#nit_aseguradora_mercancia').val(ui.item.id);
          $('#numero_poliza_mercancia').focus();
        }
      });
      $el.find('#ciudad_aseguradora_mercancia').autocomplete({
        minLength: 3,
        source: helpers_path+'ajax.php?ciudad=1',
        select: function(event,ui) {
          $('#id_ciudad_aseguradora_mercancia').val(ui.item.id);
        }
      });
    };

    var initForm = function() {
      $el.find('#opciones_save').validate({
        rules: {
          app_numero_intentos: {required: true, digits: true, min: 1},
          nombre_empresa: 'required',
          nit_empresa: 'required',
          direccion_empresa: 'required',
          telefono_empresa: 'required',
          ciudad_empresa: 'required',
          email_empresa: {required: true, email: true},
          aseguradora_mercancia: 'required',
          numero_poliza_mercancia: {required: true, digits: true},
          vigencia_poliza_mercancia: 'required',
          codigo_regional: {required: true, digits: true},
          codigo_empresa: {required: true, digits: true},
          calKiloVolumen: {required: true, digits: true},
          app_ruta_guias: 'required',
          app_ruta_ordenes: 'required',
          siigo_centro_costo: {required: true, digits: true, length: 4},
          siigo_subcentro_costo: {required: true, digits: true, length: 3},
          siigo_codigo_vendedor: {required: true, digits: true, length: 4},
          siigo_codigo_ciudad: {required: true, digits: true, length: 4},
          siigo_codigo_zona: {required: true, digits: true, length: 3},
          siigo_codigo_bodega: {required: true, digits: true, length: 4},
          siigo_codigo_ubicacion: {required: true, digits: true, length: 3},
          siigo_codigo_banco: {required: true, digits: true, length: 2},
          siigo_cuenta_contable_total_contado: {required: true, digits: true, length: 10},
          siigo_cuenta_contable_total_credito: {required: true, digits: true, length: 10},
          siigo_cuenta_contable_total_flete: {required: true, digits: true, length: 10},
          siigo_cuenta_contable_total_seguro: {required: true, digits: true, length: 10},
          siigo_cuenta_contable_descuento: {required: true, digits: true, length: 10},
          siigo_cuenta_contable_cree_debito: {required: true, digits: true, length: 10},
          siigo_cuenta_contable_cree_credito: {required: true, digits: true, length: 10},
          siigo_cree_porcentaje: {required: true, number: true},
          siigo_sucursal: {required: true, digits: true, length: 3},
          facturacion_tarifa_aplicar: 'required',
          facturacion_prefijo: 'required',
          facturacion_codigo_actividad_principal: {required: true, digits: true}
        },
        messages:{
          vigencia_poliza_mercancia: {required: 'Selecciona la fecha.'},
          app_ruta_guias: 'Escribe la ruta de la carpeta de las guias escaneadas.',
          app_ruta_ordenes: 'Escribe la ruta de la carpeta de las ordenes de recogida escaneadas.',
          siigo_centro_costo: {length: '4 números, rellena con 0 a la <b>izquierda</b>.'},
          siigo_subcentro_costo: {length: '3 números, rellena con 0 a la <b>izquierda</b>.'},
          siigo_codigo_vendedor: {length: '4 números, rellena con 0 a la <b>izquierda</b>.'},
          siigo_codigo_ciudad: {length: '4 números, rellena con 0 a la <b>izquierda</b>.'},
          siigo_codigo_zona: {length: '3 números, rellena con 0 a la <b>izquierda</b>.'},
          siigo_codigo_bodega: {length: '4 números, rellena con 0 a la <b>izquierda</b>.'},
          siigo_codigo_ubicacion: {length: '3 números, rellena con 0 a la <b>izquierda</b>.'},
          siigo_codigo_banco: {length: '2 números, rellena con 0 a la <b>izquierda</b>.'},
          siigo_cuenta_contable_total_contado: {length: '10 números, rellena con 0 a la <b>derecha</b>.'},
          siigo_cuenta_contable_total_credito: {length: '10 números, rellena con 0 a la <b>derecha</b>.'},
          siigo_cuenta_contable_total_flete: {length: '10 números, rellena con 0 a la <b>derecha</b>.'},
          siigo_cuenta_contable_total_seguro: {length: '10 números, rellena con 0 a la <b>derecha</b>.'},
          siigo_cuenta_contable_descuento: {length: '10 números, rellena con 0 a la <b>derecha</b>.'},
          siigo_cuenta_contable_cree_debito: {length: '10 números, rellena con 0 a la <b>derecha</b>.'},
          siigo_cuenta_contable_cree_credito: {length: '10 números, rellena con 0 a la <b>derecha</b>.'},
          siigo_sucursal: {length: '3 números, rellena con 0 a la <b>izquierda</b>.'},
        },
        invalidHandler: function(form, validator) {
          $("#errores").empty().slideUp(200);
          var errors = validator.numberOfInvalids();
          if (errors) {
            var message = 'Revisa las opciones, ';
            message += errors == 1 ? 'hay un campo invalido.' : 'hay ' + errors + ' campos invalidos.';
            $("#errores").html(message).addClass('ui-state-highlight').slideDown(600);
          }
        },
        errorPlacement: function(error, element) {error.appendTo( element.parent("td").next("td") );},
        highlight: function(input) {$(input).addClass("ui-state-highlight");},
        unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
        submitHandler: function(form) {
          $("#errores").empty().slideUp(200);
          $.ajax({
            url: opciones_path+'index.php', type: 'POST',
            beforeSend:function() {
              var html='<p class="expand"><i class="icon-spinner icon-2x icon-spin"></i> Guardando opciones...</p>';
              LOGISTICA.Dialog.open('Opciones', html, true);
            },
            data:'guardar=si&'+$(form).serialize(),
            success: function(msj) {
              var html='<p class="expand"><i class="icon-ok-sign icon-2x"></i> Opciones guardadas.</p>';
              LOGISTICA.Dialog.open('Opciones', html, true);
            }
          });
        }
      });
    };

    return {
      init: init
    }
  }();
  LOGISTICA.configuracion.opciones.init();
})();
</script>
