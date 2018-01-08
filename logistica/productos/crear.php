<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][PRODUCTOS_CREAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
?>
<div id="productos__form">
  <button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
  <h2>Nuevo Producto</h2>
  <hr class="hr-small">
  <form method="post" action="#">
    <table>
      <tr>
        <td><b>Código:</b></td>
        <td><input type="text" maxlength="6" name="producto[id]" id="id" /></td>
        <td></td>
      </tr>
      <tr>
        <td><b>Nombre del Producto:</b></td>
        <td>
          <textarea rows="4" cols="30" id="nombre" name="producto[nombre]"></textarea>
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>Tipo</b></td>
        <td>
          <select id="tipo" name="producto[tipo]">
            <option value="CARGA NORMAL" selected="selected">CARGA NORMAL</option>
            <option value="CARGA PELIGROSA">CARGA PELIGROSA</option>
            <option value="DESECHOS PELIGROSOS">DESECHOS PELIGROSOS</option>
          </select>
        </td>
        <td></td>
      </tr>
    </table>
    <center class="form-actions">
      <button class="btn btn-primary" id="guardar">Guardar</button>
    </center>
  </form>
</div>
<script>
(function() {
  LOGISTICA.logistica.productos = function() {
    var $el = $('#productos__form');
    var $form = $el.find('form');
    var $saveBtn = $el.find('#guardar');

    var init = function() {
      $el.find('#id').focus();
      initFormValidator();
    };
    var initFormValidator = function() {
      $form.validate({
        rules: {
          'producto[id]': {required: true, digits: true},
          'producto[nombre]': 'required',
          'producto[tipo]': 'required'
        },
        errorPlacement: function(error, element) {
          error.appendTo( element.parent("td").next("td") );
        },
        highlight: function(input) {$(input).addClass("ui-state-highlight");},
        unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
        submitHandler: function(form) {
          $saveBtn.prop('disabled', true).text('Guardando...');
          $.ajax({
            url: productos_path+'ajax.php',
            type: "POST",
            data: 'guardar=101&'+$(form).serialize(),
            success: function(msj){
              if(! msj){
                alertify.success('Producto creado correctamente');
                regresar();
              }else{
                $saveBtn.prop('disabled', false).text('Guardar');
                alertify.error(msj);
              }
            }
          });
        }
      });
    };
    return {
      init: init
    }
  }();
  LOGISTICA.logistica.productos.init();
})();
</script>
