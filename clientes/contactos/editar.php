<?php
$raiz = '../../';
require "../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  include LOGISTICA_ROOT.'mensajes/id.php';
  exit;
}
$cliente = new Cliente;
if (! $cliente->find($_GET['id'])) exit('No existe el contacto.');
?>
<form id="EditarContacto" name="EditarContacto" method="post" action="#" class="no-margin">
  <table cellpadding="0">
    <tr>
      <td><b>Tipo de identificación</b></td>
      <td>
        <select id="tipo_identificacion" name="tipo_identificacion">
          <?php
          foreach (Cliente::$tipos_identificacion as $key => $value) {
            $s = $key == $cliente->tipo_identificacion ? 'selected="selected"' : '';
            echo '<option value="'.$key.'" '.$s.'>'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Número de identificación</b></td>
      <td><input type="text" id="numero_identificacion" maxlength="15" name="numero_identificacion" value="<?= $cliente->numero_identificacion ?>" /></td>
      <td></td>
    </tr>
    <tr class="empresa">
      <td><b>Dígito Verificación</b></td>
      <td><input type="text" class="input-mini" name="digito_verificacion" id="digito_verificacion" maxlength="1" value="<?= $cliente->digito_verificacion ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Nombre:</b></td>
      <td><input type="text" name="nombre" id="nombre" maxlength="100" value="<?= $cliente->nombre ?>" /></td>
      <td></td>
    </tr>
    <tr class="persona">
      <td><b>Primer Apellido:</b></td>
      <td><input type="text" name="primer_apellido" maxlength="100" id="primer_apellido" value="<?= $cliente->primer_apellido ?>" /></td>
      <td></td>
    </tr>
    <tr class="persona">
      <td><b>Segundo Apellido:</b> <small>(Opcional)</small></td>
      <td><input type="text" name="segundo_apellido" maxlength="100" id="segundo_apellido" value="<?= $cliente->segundo_apellido ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Teléfono Fijo</b></td>
      <td><input type="text" name="telefono" placeholder="Principal" id="telefono" maxlength="7" value="<?= $cliente->telefono ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Otros teléfonos</b></td>
      <td><input type="text" name="telefono2" placeholder="Otros teléfonos" id="telefono2" value="<?= $cliente->telefono2 ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Celular</b></td>
      <td><input type="text" name="celular" placeholder="Celular" id="celular" maxlength="10" value="<?= $cliente->celular ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Dirección</b></td>
      <td><input type="text" name="direccion" id="direccion" maxlength="60" value="<?= $cliente->direccion ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ciudad</b></td>
      <td>
        <input type="text" name="nombre_ciudad" id="nombre_ciudad" value="<?= $cliente->ciudad_nombre ?>" />
        <input type="hidden" name="idciudad" id="idciudad" value="<?= $cliente->idciudad ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Email</b></td>
      <td><input type="text" name="email" id="email" maxlength="50" value="<?= $cliente->email ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Sitio Web</b></td>
            <td><input type="text" name="sitioweb" id="sitioweb" value="<?= $cliente->sitioweb ?>" /></td>
            <td></td>
        </tr>
  </table>
  <center class="form-actions no-margin">
    <button class="btn btn-info" type="submit" id="editar_contacto"><i class="icon icon-save"></i> Guardar</button>
  </center>
  <input type="hidden" id="id" name="id" value="<?= $cliente->id ?>" />
  <?= nonce_create_form_input($cliente->id) ?>
</form>
<script>
(function(){
  $('#nombre_ciudad').autocomplete({
    autoFocus: true,
    source: helpers_path+"ajax.php?ciudad=1",
    minLength: 3,
    select: function(event, ui) {
      $('#idciudad').val(ui.item.id);
      $('#email').focus();
    }
  });

  var $tipo_identificacion = $('#tipo_identificacion'),
    $celular = $('#celular');

  <?php Cliente::validar_tipo_identificacion() ?>

  $('#EditarContacto').validate({
    rules: {
      tipo_identificacion: {required: true, length: 1},
      numero_identificacion: {
        required: true,
        digits: true,
        minlength: 5,
        maxlength: function() { return 'N' == $tipo_identificacion.val() ? 9 : 15; }
      },
      digito_verificacion: {required: true, digits: true, length: 1},
      nombre: {required: true, maxlength: 100},
      primer_apellido: {required: true, maxlength: 100},
      telefono: {
        required: function() { return ('N' == $tipo_identificacion.val() || !$celular.val()) },
        digits: true,
        length: 7
      },
      celular: {
        required: function() { return !$("#telefono").val() },
        digits: true,
        length: 10
      },
      direccion: {required: true, maxlength: 60},
      idciudad: {required: true, digits: true},
      email: {email: true},
      sitio_web: {url: true}
    },
    messages: {
      telefono: {
        required: function() {
          if ('N' == $tipo_identificacion.val()) {
            return 'Este campo es obligatorio.';
          } else {
            return 'Escriba el teléfono fijo, celular o ambos.';
          }
        }
      },
      celular: {required: 'Escribe el teléfono fijo, celular o ambos.'},
    },
    errorPlacement: function(error, element) {
      error.appendTo( element.parent("td").next("td") );
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#editar_contacto').prop('disabled', true);
      $.ajax({
        url: contactos_path+'ajax.php',
        type: "POST",
        data: 'editar=101&'+$("#EditarContacto").serialize(),
        success: function(msj){
          if (! msj) {
            LOGISTICA.Dialog.close();
          } else {
            $('#editar_contacto').prop('disabled', false);
            alertify.error(msj);
          }
        }
      });
    }
  });
}());
</script>
