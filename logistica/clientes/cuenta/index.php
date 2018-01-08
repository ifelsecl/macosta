<?php
$raiz = '../../';
require '../seguridad.php';
$id    = $_SESSION['id'];
$cliente = new Cliente;
if (! $cliente->find($id)) exit('No existe el cliente.');
if (isset($_REQUEST['guardar'])) {
  foreach ($_REQUEST as $k=>$v) {
    $_REQUEST[$k] = trim(addslashes($v));
  }
  $msj = 'NO PERMITIDO';
  $direccion  = isset($_REQUEST['direccion']) ? $_REQUEST['direccion'] : exit($msj);
  $telefono   = isset($_REQUEST['telefono']) ? $_REQUEST['telefono'] : exit($msj);
  $telefono2  = isset($_REQUEST['telefono2']) ? $_REQUEST['telefono2'] : exit($msj);
  $celular  = isset($_REQUEST['celular']) ? $_REQUEST['celular'] : exit($msj);
  $email    = isset($_REQUEST['email']) ? $_REQUEST['email'] : exit($msj);
  $sitioweb   = isset($_REQUEST['sitioweb']) ? $_REQUEST['sitioweb'] : exit($msj);

  $logger = new Logger;
  $accion = 'No realizó cambios';
  if ($cliente->direccion != $direccion) {
    $accion='cambió su dirección, antes '.$cliente->direccion.', ahora '.$direccion.'.';
    $logger->LogCliente($accion, 'Cuenta', $id,'Cliente');
  }else $direccion=false;
  if ($cliente->telefono!=$telefono) {
    $accion='cambió su teléfono, antes '.$cliente->telefono.', ahora '.$telefono.'.';
    $logger->LogCliente($accion, 'Cuenta', $id,'Cliente');
  }else $telefono=false;
  if ($cliente->telefono2!=$telefono2) {
    $accion='cambió su teléfono 2, antes '.$cliente->telefono2.', ahora '.$telefono2.'.';
    $logger->LogCliente($accion, 'Cuenta', $id,'Cliente');
  }else $telefono2=false;
  if ($cliente->celular!=$celular) {
    $accion='cambió su teléfono 3, antes '.$cliente->celular.', ahora '.$celular.'.';
    $logger->LogCliente($accion, 'Cuenta', $id,'Cliente');
  }else $celular=false;
  if ($cliente->email!=$email) {
    if (empty($cliente->email)) $accion='agregó su email '.$email;
    else $accion='cambió su email, antes '.$cliente->email.', ahora '.$email.'.';
    $logger->LogCliente($accion, 'Cuenta', $id,'Cliente');
  }else $email=false;
  if ($cliente->sitioweb!=$sitioweb) {
    if (empty($cliente->sitioweb)) $accion='agregó su sitio web '.$sitioweb;
    else $accion='cambió su sitio web, antes '.$cliente->sitioweb.', ahora '.$sitioweb.'.';
    $logger->LogCliente($accion, 'Cuenta', $id,'Cliente');
  }else $sitioweb=false;
  if ($direccion or $telefono or $telefono2 or $celular or $email or $sitioweb) {
    if ($cliente->EditarBasico($id, $direccion, $telefono, $telefono2, $celular, $email, $sitioweb)) {
      $_SESSION['actualizado']=true;
      $r['ok']=true;
      $r['clase'] = 'alert-success';
      $r['mensaje']='&iexcl;Información actualizada!';
    }else{
      $r['ok']=false;
      $r['clase'] = 'alert-error';
      $r['mensaje']='Error actualizando la informacion';
    }
  }else{
    $r['ok']=false;
    $r['clase'] = 'alert-info';
    $r['mensaje']='No hay nada para actualizar';
  }
  $r['mensaje'] = '<div class="alert '.$r['clase'].'"><a class="close" data-dismiss="alert">x</a>'.$r['mensaje'].'</div>';
  echo json_encode($r);
  exit;
}
?>
<h2>Mi Cuenta</h2>
<form id="fActualizar" class="form-horizontal" method="post" action="#">
  <table class="table table-hover" style="font-size:17px;">
    <tr>
      <td>Nombre:</td>
      <td>
        <label><?= $cliente->nombre_completo ?></label>
      </td>
      <td style="width:220px"></td>
    </tr>
    <tr>
      <td><?= $cliente->tipo_identificacion() ?>:</td>
      <td>
        <label><?= $cliente->numero_identificacion_completo ?></label>
      </td>
      <td style="width:220px"></td>
    </tr>
    <tr>
      <td>Ciudad:</td>
      <td>
        <label><?= $cliente->ciudad_nombre.' ('.$cliente->departamento_nombre.')' ?></label>
      </td>
      <td style="width:220px"></td>
    </tr>
    <tr>
      <td>Dirección:</td>
      <td>
        <input type="text" name="direccion" value="<?= $cliente->direccion ?>" />
      </td>
      <td style="width:220px"></td>
    </tr>
    <tr>
      <td>Teléfono:</td>
      <td>
        <input type="text" maxlength="7" placeholder="Principal" name="telefono" value="<?= $cliente->telefono ?>" />
      </td>
      <td style="width:220px"></td>
    </tr>
    <tr>
      <td>Otros teléfonos:</td>
      <td>
        <input type="text" placeholder="Otros teléfonos" name="telefono2" value="<?= $cliente->telefono2 ?>" />
      </td>
      <td style="width:220px"></td>
    </tr>
    <tr>
      <td>Celular:</td>
      <td>
        <input type="text" maxlength="10" placeholder="Celular" name="celular" value="<?= $cliente->celular ?>" />
      </td>
      <td style="width:220px"></td>
    </tr>
    <tr>
      <td>Email:</td>
      <td>
        <input type="text" placeholder="@" name="email" value="<?= $cliente->email ?>" />
      </td>
      <td style="width:220px"></td>
    </tr>
    <tr>
      <td>Sitio Web:</td>
      <td>
        <input type="text" placeholder="http://" name="sitioweb" value="<?= $cliente->sitioweb ?>" />
      </td>
      <td style="width:220px"></td>
    </tr>
    <tr>
      <td>Ultima Actualización:</td>
      <td>
        <label id="lFecha"><?php if ($cliente->fechamodificacion) echo strftime('%d/%b/%Y %I:%M:%S %p', strtotime($cliente->fechamodificacion)) ?></label>
      </td>
      <td></td>
    </tr>
  </table>
  <div class="form-actions">
    <button id="actualizar" class="btn btn-info"><i class="icon icon-save"></i> Actualizar</button>
  </div>
</form>
<script>
(function() {
  <?php
  if (isset($_SESSION['actualizado']) or date('Y-m-d',strtotime($cliente->fechamodificacion)) == date('Y-m-d')) {
    echo "$('#fActualizar input').attr('readonly','readonly');";
    echo "$('#actualizar').remove();";
  }
  ?>
  var b=$('#actualizar');
  $('#fActualizar').validate({
    rules: {
      direccion: {required: true, maxlength: 60},
      telefono: {required: true, digits: true, length: 7},
      celular: {required: true, digits: true, length: 10},
      email: {required: true, email: true},
      sitioweb: {url: true}
    },
    errorPlacement: function(error, element) {
      error.appendTo( element.parent("td").next("td") );
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $.ajax({
        url: 'cuenta/',type: 'POST', dataType: 'json',
        data: 'guardar=1&'+$(form).serialize(),
        beforeSend: function() {
          $('#fActualizar input').addClass('ui-state-disabled');
          $('#m').hide();
          b.prop('disabled', true);
        },
        success: function(r) {
          if (r.ok) {
            $('#fActualizar input').attr('readonly','readonly');
            b.remove();
            var d=new Date();
            $('#lFecha').text(d.toLocaleString());
          }else{
            $('#fActualizar input').removeClass('ui-state-disabled');
            b.prop('disabled', false);
          }
          $('#fActualizar').prepend(r.mensaje);
        }
      });
    }
  });
})();
</script>
