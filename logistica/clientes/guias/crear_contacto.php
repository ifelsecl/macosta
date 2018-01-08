<?php
require '../seguridad.php';
?>
<form id="CrearContacto" name="CrearContacto" method="post" action="#" class="no-margin">
  <table cellpadding="0">
    <tr>
      <td><b>Tipo de identificación:</b></td>
      <td>
        <select id="tipo_identificacion" name="tipo_identificacion">
          <?php
          foreach (Cliente::$tipos_identificacion as $id => $value) {
            echo '<option value="'.$id.'">'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
        </tr>
        <tr>
          <td><b>Número de identificación:</b></td>
          <td><input type="text" id="numero_identificacion" name="numero_identificacion" maxlength="15" /></td>
          <td></td>
        </tr>
        <tr class="empresa">
      <td><b>Dígito Verificación:</b></td>
      <td><input type="text" class="input-mini" name="digito_verificacion" id="digito_verificacion" maxlength="1" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Nombre:</b></td>
      <td><input type="text" name="nombre" id="nombre" maxlength="100" /></td>
      <td></td>
    </tr>
    <tr class="persona">
      <td><b>Primer Apellido:</b></td>
      <td><input type="text" name="primer_apellido" id="primer_apellido"  maxlength="100" /></td>
      <td></td>
    </tr>
    <tr class="persona">
      <td><b>Segundo Apellido:</b></td>
      <td><input type="text" name="segundo_apellido" placeholder="Opcional" id="segundo_apellido" maxlength="100" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Teléfono Fijo:</b></td>
      <td><input type="text" name="telefono" id="telefono" maxlength="7" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Otros teléfonos:</b></td>
      <td><input type="text" name="telefono2" placeholder="Ext. o teléfonos" id="telefono2" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Celular:</b></td>
      <td><input type="text" name="celular" id="celular" maxlength="10" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Dirección</b></td>
      <td><input type="text" name="direccion" id="direccion" maxlength="60" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ciudad</b></td>
      <td>
        <input type="text" name="nombre_ciudad" id="nombre_ciudad" />
        <input type="hidden" name="idciudad" id="idciudad" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Email</b></td>
      <td><input type="text" name="email" id="email" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Sitio Web</b></td>
      <td><input type="text" name="sitioweb" id="sitioweb" /></td>
      <td></td>
    </tr>
  </table>
  <center class="form-actions no-margin">
    <button type="submit" id="guardar_contacto">Guardar</button>
  </center>
</form>
<script>
(function(){
  var btn = $("#guardar_contacto").button({icons: {primary: 'ui-icon-circle-check'}});
  $('#nombre_ciudad').autocomplete({
    autoFocus: true,
    minLength: 3,
    source: helpers_path+"ajax.php?ciudad=1",
    select: function(event, ui) {
      $('#idciudad').val(ui.item.id);
      $('#email').focus();
    }
  });
  <?php Cliente::validar_tipo_identificacion() ?>

  $('#CrearContacto').validate({
    rules: {
      tipo_identificacion: 'required',
      numero_identificacion: {
        required: true,
        digits: true,
        minlength: 6,
        maxlength: function() { return 'N' == $('#tipo_identificacion').val() ? 9 : 15 ; }
      },
      digito_verificacion: {required: true, digits: true, length: 1},
      nombre: {required: true, maxlength: 100},
      primer_apellido: {required: true, maxlength: 100},
      segundo_apellido: {maxlength: 100},
      telefono: {required: true, digits: true, length: 7},
      celular: {length: 10, digits: true},
      direccion: {required: true, maxlength: 60},
      idciudad: 'required',
      email: {email: true},
      sitioweb: {url: true}
    },
    errorPlacement: function(error, element) {
      error.appendTo( element.parent("td").next("td") );
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      btn.button('disable').button('option','label','Guardando...');
      $.ajax({
        url: guias_path+'ajax.php',
        type: "POST",
        data: 'guardarcontacto=101&'+$(form).serialize(),
        success: function(msj){
          if (! msj) {
            alertify.alert('Nota Importante: Su mercancía en proceso de envío, será detenida sino contempla los datos correctos y/o completos.');
            alertify.success('Contacto guardado correctamente.');
            $('#dialog').dialog('close');
          } else {
            alert('No se pudo guardar el contacto, intentalo nuevamente');
            btn.button('enable').button('option','label','Guardar');
          }
        }
      });
    }
  });
})();
</script>
