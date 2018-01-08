<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][GUIAS_ANULAR]) and ! isset($_SESSION['permisos'][GUIAS_ELIMINAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
?>
<div id="guias__anular_eliminar">
  <form id="anular_eliminar" action="#" >
    <table cellspacing="0" cellpadding="0">
      <tr>
        <td><b>¿Porqué quieres anular/eliminar esta guía?</b></td>
      </tr>
      <tr>
        <td><textarea class="input-block-level" id="comentario" name="comentario" rows="4" cols="35"></textarea></td>
      </tr>
      <tr>
        <td><small>Ten en cuenta que ésta acción será registrada.</small></td>
      </tr>
    </table>
    <hr class="hr-small">
    <center>
      <?php if (isset($_SESSION['permisos'][GUIAS_ANULAR])): ?>
      <button class="btn btn-primary" id="anular_guia">Anular</button>
      <?php endif; ?>
      <?php if (isset($_SESSION['permisos'][GUIAS_ELIMINAR])): ?>
      <button class="btn btn-danger" id="eliminar_guia">Eliminar</button>
      <?php endif; ?>
    </center>
    <?= nonce_create_form_input($_GET['id']) ?>
    <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
  </form>
</div>
<script>
(function() {
  LOGISTICA.logistica.guias = function() {
    var $el = $('#guias__anular_eliminar');
    var $form = $el.find('form#anular_eliminar');

    var init = function() {
      $el.find('#comentario').focus();
      initFormValidator();
      bindClick();
    };

    var initFormValidator = function() {
      $form.validate({
        rules: { comentario: {required: true, minlength: 20} },
        messages: { comentario: {required: 'Escribe un comentario.'} },
        highlight: function(input) { $(input).addClass("ui-state-highlight"); },
        unhighlight: function(input) { $(input).removeClass("ui-state-highlight"); }
      });
    };

    var formIsValid = function() {
      return $form.valid();
    };

    var bindClick = function() {
      $el.find('button.btn').click(function(e) {
        e.preventDefault();
        if (! formIsValid()) {
          $el.find('#comentario').focus();
          return false;
        }
        var btn = this, label = '';
        if (btn.id == 'anular_guia') {
          label = 'Anular';
          $(btn).text('Anulando...');
        } else {
          label = 'Eliminar';
          $(btn).text('Eliminando...');
        }
        $(btn).prop('disabled', true);
        var data = label.toLowerCase() + '=1&' + $form.serialize();
        $.ajax({
          url: guias_path + 'ajax.php',
          type: "POST", data: data,
          success: function(msj){
            if (msj) {
              $(btn).prop('disabled', false).text(label);
              alertify.error(msj);
            } else {
              $('#buscar').click();
              cerrarDialogo();
            }
          }
        });
      });
    };

    return {
      init: init
    };
  }();
 LOGISTICA.logistica.guias.init();
})();
</script>
