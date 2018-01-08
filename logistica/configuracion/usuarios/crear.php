<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][USUARIOS_CREAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
$usuario = new Usuario;
?>
<div id="usuarios__form">
  <button class="btn pull-right btn-success" onclick="regresar()">Regresar</button>
  <h2>Nuevo Usuario</h2>
  <hr class="hr-small">
  <form action="#" id="CrearUsuario" name="CrearUsuario" method="post">
    <table>
      <tr>
        <td><b>Nombre</b></td>
        <td><input type="text" id="nombre" name="nombre" /></td>
        <td></td>
      </tr>
      <tr>
        <td><b>Cédula:</b></td>
        <td><input type="text" name="cedula" id="cedula" maxlength="12" /></td>
        <td></td>
      </tr>
      <tr>
        <td><b>Nombre de usuario</b></td>
        <td><input type="text" id="usuario" name="usuario" /></td>
        <td></td>
      </tr>
      <tr>
        <td valign="top"><b>Contraseña (2 veces)</b></td>
        <td>
          <input type="password" id="clave" name="clave" /><br />
          <input type="password" id="clave2" name="clave2" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>Correo electrónico</b></td>
        <td><input type="text" id="email" name="email" /></td>
        <td></td>
      </tr>
      <tr>
        <td><b>Perfil</b></td>
        <td>
          <select name="idperfil" id="idperfil">
            <option></option>
          <?php
          foreach (Usuario::$profiles as $key => $value) {
            echo '<option value="'.$key.'">'.$value.'</option>';
          }
          ?>
          </select>
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>Permisos</b></td>
        <td colspan="2">
          <?php
          $i = 1;
          echo '<table>';
          foreach(Usuario::$modules as $modulo){
            if ($i == 1) {
              echo '<tr>';
            }
            echo '<td><fieldset class="mod_'.$modulo.'">';
            echo '<legend><label><input type="checkbox" class="checkall" id="mod_'.$modulo.'" /><b>'.str_replace('_', ' ', $modulo).'</b></label></legend>';
            echo '<table>';
            $pb = Usuario::ObtenerPermisosPorModulo($modulo);
            while ($p = mysql_fetch_array($pb)) {
              echo '<tr>';
              echo '<td title="'.$p['descripcion'].'">';
              echo '<label><input type="checkbox" id="'.$p['nombre'].'" name="permisos[]" value="'.$p['nombre'].'" />';
              $nombre = str_replace($modulo.'_', '', $p['nombre']);
              $nombre = str_replace('_', ' ', $nombre);
              echo $nombre.'</label></td>';
              echo '</tr>';
            }
            echo '</table>';
            echo '</fieldset>';
            if ($i == 3) {
              echo '</td>';
              $i = 1;
            } else {
              $i++;
            }
          }
          ?>
        </td>
      </tr>
    </table>
    <hr class="hr-small">
    <center><button id="guardar" type="submit" class="btn btn-primary">Guardar</button></center>
    <?php nonce_create_form_input("Guardar") ?>
  </form>
</div>
<script>
(function() {
  LOGISTICA.configuracion.usuarios = function() {
    var $el = $('#usuarios__form');
    var $form = $el.find('form');
    var $saveBtn = $form.find('#guardar');

    var init = function() {
      $el.find('#nombre').focus();
      initCheckbox();
      initFormValidator();
    };

    var initCheckbox = function() {
      $el.find('.checkall').click(function(){
        $el.find("."+this.id+" input[type='checkbox']").prop('checked', $(this).is(':checked'));
      });
    };

    var initFormValidator = function() {
      $form.validate({
        rules: {
          nombre: {required: true, minlength: 5},
          cedula: {required: true, digits: true},
          usuario: {required: true, minlength: 5},
          clave: {required: true, minlength: 5},
          clave2: {equalTo: '#clave'},
          email: {required: true, email: true},
          idperfil: {required: true}
        },
        messages: {
          clave2: {equalTo: "Las contraseñas no coinciden."}
        },
        errorPlacement: function(error, element) {
          error.appendTo( element.parent("td").next("td") );
        },
        highlight: function(input) { $(input).addClass("ui-state-highlight"); },
        unhighlight: function(input) { $(input).removeClass("ui-state-highlight"); },
        submitHandler: function(form) {
          $saveBtn.prop('disabled', true).text('Guardando...');
          $.ajax({
            url: usuarios_path+'ajax.php',
            type: 'POST',
            data: 'guardar=1&'+$(form).serialize(),
            success: function(response){
              if(response=="ok"){
                regresar();
              }else{
                $saveBtn.prop("disabled", false).text('Guardar');
                LOGISTICA.Dialog.open('Error',response,true);
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

  LOGISTICA.configuracion.usuarios.init();
})();
</script>
