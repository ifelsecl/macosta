<?php
$raiz="../../";
require_once $raiz."seguridad.php";

if (!isset($_GET['id']) or !nonce_is_valid($_GET[NONCE_KEY],$_GET['id'])) {
  include_once $raiz."mensajes/id.php";
  exit;
}
if( !isset($_SESSION['permisos'][CLIENTES_CAMBIAR_CLAVE])){
  include_once $raiz."mensajes/permiso.php";
  exit;
}

$cliente = new Cliente;
if( ! $cliente->find($_GET['id']) ) exit('No existe el cliente');
?>
<script>
$(function() {
  $( "#cambiar" ).button({icons:{primary: 'ui-icon-key'}});
  $('#regresar').click(function(){
    regresar();
  });
  $('#CambiarClaveCliente').validate({
    rules: {
      clave: {required: true, minlength: 3},
      clave2: {equalTo: '#clave'}
    },
    messages: {
      clave: {required: 'Escribe la contraseña'},
      clave2: {equalTo: "Las contraseñas no coinciden."}
    },
    errorPlacement: function(error, element) {
      error.appendTo( element.parent("td").next("td") );
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#cambiar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: clientes_path+'ajax.php',
        type: "POST",
        data: 'cambiar_clave=101&'+$('#CambiarClaveCliente').serialize(),
        success: function(msj){
          if(msj == "ok"){
            regresar();
          }else{
            $('#cambiar').button('enable').button('option','label','Guardar');
            LOGISTICA.Dialog.open('Error cambiando contraseña', msj, true);
          }
        }
      });
    }
  });
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2><?= $cliente->nombre_completo ?> | Cambiar Contraseña</h2>
<hr class="hr-small">
<p class="muted">Para cambiar contraseña del cliente, escriba 2 veces la contraseña y presione Guardar.</p>
<form id="CambiarClaveCliente">
  <table style="border-spacing:5px">
    <tr>
      <td><b>Contraseña:</b></td>
      <td><input type="password" name="clave" id="clave" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Confirmar contraseña:</b></td>
      <td><input type="password" name="clave2" id="clave2" /></td>
      <td></td>
    </tr>
  </table>
  <center class="form-actions"><button type="submit" id="cambiar">Guardar</button></center>
  <input type="hidden" id="id" name="id" value="<?= $_GET['id'] ?>" />
  <?php nonce_create_form_input($_GET['id']) ?>
</form>
