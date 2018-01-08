<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][USUARIOS_EDITAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
if (! isset($_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
if (! $usuario = Usuario::find($_GET['id'])) exit('No existe el usuario.');
$permisos = $usuario->format_permissions();
?>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Editar Usuario</h2>
<hr class="hr-small">
<form action="#" id="EditarUsuario" name="EditarUsuario">
  <table>
    <tr>
      <td><b>Nombre</b></td>
      <td><input type="text" id="nombre" name="nombre" value="<?= $usuario->nombre ?>" /></td>
      <td></td>
    </tr>
    <tr>
          <td><b>Cédula:</b></td>
          <td><input type="text" name="cedula" id="cedula" value="<?= $usuario->cedula ?>" /></td>
          <td></td>
        </tr>
    <tr>
      <td><b>Nombre de usuario</b></td>
      <td title="No puede ser cambiado"><input disabled="disabled" type="text" id="usuario" name="usuario" value="<?= $usuario->usuario ?>" /></td>
    </tr>
    <tr>
      <td valign="top"><b>Contraseña (2 veces)</b><br /><small>Dejar en blanco para no cambiar</small></td>
      <td><input type="password" id="clave" name="clave" /><br />
        <input type="password" id="clave2" name="clave2" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Correo electrónico</b></td>
      <td><input type="text" id="email" name="email" value="<?= $usuario->email ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Perfil</b></td>
      <td>
        <select name="idperfil" id="idperfil">
          <?php
          foreach (Usuario::$profiles as $key => $value) {
            $s = $usuario->idperfil == $key ? 'selected="selected"' : '' ;
            echo '<option value="'.$key.'" '.$s.'>'.$value.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><b>Permisos</b></td>
      <td colspan="2">
        <?php
        $i = 1;
        echo '<table>';
        foreach (Usuario::$modules as $modulo) {
          if ($i == 1) {
            echo '<tr>';
          }
          echo '<td><fieldset class="mod_'.$modulo.'">';
          echo '<legend><label><input type="checkbox" class="checkall" id="mod_'.$modulo.'" /><b>'.str_replace('_', ' ', $modulo).'</b></label></legend>';
          echo '<table>';
          $pb = Usuario::ObtenerPermisosPorModulo($modulo);
          while ($p = mysql_fetch_array($pb)) {
            echo '<tr>';
            echo  '<td title="'.$p['descripcion'].'">';
            $check = '';
            if (isset($permisos[$p['nombre']])) {
              $check = 'checked="checked"';
            }
            echo  '<label><input type="checkbox" id="'.$p['nombre'].'" '.$check.' name="permisos[]" value="'.$p['nombre'].'" />';
            $nombre = str_replace($modulo.'_', '', $p['nombre']);
            $nombre = str_replace('_', ' ', $nombre);
            echo  $nombre.'</label></td>';
            echo '</tr>';
          }
          echo '</table>';
          echo '</fieldset>';
          if ($i==3) {
            echo '</td>';
            $i=1;
          } else {
            $i++;
          }
        }
        ?>
      </td>
    </tr>
    <tr>
      <td align="center" colspan="2">
        <?php nonce_create_form_input("Guardar") ?>
        <input type="hidden" id="idusuario" name="idusuario" value="<?= $usuario->id ?>">
      </td>
    </tr>
  </table>
  <hr class="hr-small">
  <center><button type="submit" id="guardar">Guardar</button></center>
</form>
<script>
(function() {
  $('.checkall').click(function() {
    $("."+this.id+" input[type='checkbox']").attr('checked', $(this).is(':checked'));
  });
  $('#guardar').button({icons: {primary: "ui-icon-circle-check"}});
  $('#EditarUsuario').validate({
    rules: {
      nombre: {required: true, minlength: 5},
      cedula: {required: true, digits: true},
      clave: {minlength: 5},
      clave2: {equalTo: '#clave'},
      email: {required: true, email: true}
    },
    messages: {
      clave2: {equalTo: "Las contraseñas no coinciden."},
    },
    errorPlacement: function(error, element) {
      error.appendTo( element.parent("td").next("td") );
    },
    highlight: function(input) { $(input).addClass("ui-state-highlight"); },
    unhighlight: function(input) { $(input).removeClass("ui-state-highlight"); },
    submitHandler: function(form) {
      $('#guardar').button("disable").button('option','label','Guardando...');
      $.ajax({
        type: 'POST',
        url: usuarios_path+'ajax.php',
        data: 'editar=1&'+$(form).serialize(),
        success: function(response) {
          if (response == "ok") {
            regresar();
          } else {
            $('#guardar').button("enable").button('option','label','Guardar');
            LOGISTICA.Dialog.open('Error',response,true);
          }
        }
      });
    }
  });
  $('#nombre').focus();
})();
</script>
