<?php
require '../../seguridad.php';
?>
<script>
(function() {
  LOGISTICA.logistica.guias = function() {
    var $el = $('#guias__imprimir');

    var init = function() {
      $el.find('#inicio').focus();
      initUserForm();
      initClientForm();
      initRangeForm();
      initNumberForm();
    };

    var initUserForm = function() {
      $el.find("#fecha_imp, #imp_fecha").datepicker({
        autoSize: true,
        showOn: "both",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        maxDate: 0,
        buttonText: 'Seleccionar...'
      });
    };

    var initClientForm = function() {
      $el.find('#imp_cliente').autocomplete({
        autoFocus: true,
        minLength: 3,
        focus: function() { return false; },
        source: helpers_path+'ajax.php?cliente=1',
        select: function(event, ui) {
          $el.find('#imp_id_cliente').val(ui.item.id);
        }
      });
    };

    var initRangeForm = function() {
      $el.find('form#IR').validate({
        rules: {
          inicio: {required: true, digits: true},
          fin: {required: true, digits: true},
          cantidad: {required: true, digits: true}
        },
        errorPlacement: function(er, el) {},
        highlight: function(input) { $(input).addClass("ui-state-highlight"); },
        unhighlight: function(input) { $(input).removeClass("ui-state-highlight"); },
        submitHandler: function(f) { f.submit(); }
      });
    };

    var initNumberForm = function() {
      $el.find('form#IN').validate({
        rules: { numeros: 'required' },
        errorPlacement: function(er, el) {},
        highlight: function(input) { $(input).addClass("ui-state-highlight"); },
        unhighlight: function(input) { $(input).removeClass("ui-state-highlight"); },
      });
    }

    return {
      init: init
    }
  }();
  LOGISTICA.logistica.guias.init();
})();
</script>
<div id="guias__imprimir">
  <div class="tabbable">
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#tab_range">Rango</a></li>
      <li><a data-toggle="tab" href="#tab_user">Usuario</a></li>
      <li><a data-toggle="tab" href="#tab_client">Cliente</a></li>
      <li><a data-toggle="tab" href="#tab_number">Números</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="tab_range">
        <form id="IR" target="_blank" action="logistica/guias/imprimir" method="post">
          <table>
            <tr>
              <td>Imprimir desde:</td>
              <td><input type="text" class="input-mini" id="inicio" name="inicio" size="6" /></td>
              <td>hasta:</td>
              <td><input type="text" class="input-mini" id="fin" name="fin" size="6" /></td>
            </tr>
            <tr>
              <td>Cantidad:</td>
              <td>
                <select class="input-mini" name="cantidad">
                  <option>1</option>
                  <option selected="selected">2</option>
                  <option>3</option>
                  <option>4</option>
                </select>
              </td>
            </tr>
          </table>
          <center><button class="btn btn-primary" type="submit"><i class="icon-print"></i> Imprimir</button></center>
          <input type="hidden" name="imprimir" value="IR" />
          <input type="hidden" name="varias" value="1" />
        </form>
      </div>
      <div class="tab-pane" id="tab_user">
        <form id="IU" target="_blank" action="logistica/guias/imprimir" method="post">
          <table>
            <tr>
              <td><b>Usuario:</b></td>
              <td>
                <select name="id_usuario">
                  <option value="<?= $_SESSION['userid'] ?>"><?= $_SESSION['username'] ?> (Tú)</option>
                  <option value="16">Cliente</option>
                </select>
              </td>
            </tr>
            <tr>
              <td><b>Fecha:</b></td>
              <td>
                <input type="text" readonly="readonly" class="input-small" name="fecha" id="fecha_imp" value="<?= date('Y-m-d') ?>" />
              </td>
            </tr>
            <tr>
              <td><b>Cantidad</b>:</td>
              <td>
                <select class="input-mini" name="cantidad">
                  <option>1</option>
                  <option selected="selected">2</option>
                  <option>3</option>
                  <option>4</option>
                </select>
              </td>
            </tr>
            <tr>
              <td align="center" colspan="2">
                <label for="incluir_impresas_usuario">
                  <input type="checkbox" name="incluir_impresas" id="incluir_impresas_usuario" value="si" />Incluir guías impresas
                </label>
              </td>
            </tr>
          </table>
          <center><button class="btn btn-primary" type="submit"><i class="icon-print"></i> Imprimir</button></center>
          <input type="hidden" name="imprimir" value="IU" />
          <input type="hidden" name="varias" value="1" />
        </form>
      </div>
      <div class="tab-pane" id="tab_client">
        <form id="IC" target="_blank" action="logistica/guias/imprimir" method="post">
          <table>
            <tr>
              <td><b>Cliente:</b></td>
              <td>
                <input type="text" name="imp_cliente" id="imp_cliente" />
                <input type="hidden" id="imp_id_cliente" name="id_cliente" />
              </td>
            </tr>
            <tr>
              <td><b>Fecha:</b></td>
              <td>
                <input type="text" readonly="readonly" class="input-small" name="fecha" id="imp_fecha" value="<?= date('Y-m-d') ?>" />
              </td>
            </tr>
            <tr>
              <td><b>Cantidad:</b></td>
              <td>
                <select class="input-mini" name="cantidad">
                  <option>1</option>
                  <option selected="selected">2</option>
                  <option>3</option>
                  <option>4</option>
                </select>
              </td>
            </tr>
            <tr>
              <td align="center" colspan="2">
                <label for="incluir_impresas_cliente">
                  <input type="checkbox" name="incluir_impresas" id="incluir_impresas_cliente" value="si" />Incluir guías impresas
                </label>
              </td>
            </tr>
          </table>
          <center><button class="btn btn-primary" type="submit"><i class="icon-print"></i> Imprimir</button></center>
          <input type="hidden" name="imprimir" value="IC" />
          <input type="hidden" name="varias" value="1" />
        </form>
      </div>
      <div class="tab-pane" id="tab_number">
        <form id="IN" target="_blank" action="logistica/guias/imprimir" method="post">
          <table>
            <tr>
              <td><b>Números:</b></td>
              <td>
                <input type="text" name="numeros" id="numeros" placeholder="Números separados por guión" />
              </td>
            </tr>
            <tr>
              <td><b>Cantidad:</b></td>
              <td>
                <select class="input-mini" name="cantidad">
                  <option>1</option>
                  <option selected="selected">2</option>
                  <option>3</option>
                  <option>4</option>
                </select>
              </td>
            </tr>
          </table>
          <center><button class="btn btn-primary" type="submit"><i class="icon-print"></i> Imprimir</button></center>
          <input type="hidden" name="imprimir" value="IN" />
          <input type="hidden" name="varias" value="1" />
        </form>
      </div>
    </div>
  </div>
</div>
