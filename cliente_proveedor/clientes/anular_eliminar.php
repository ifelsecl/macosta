<?php
require "../../seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY],$_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}

$cliente = new Cliente;
if (! $cliente->find($_GET['id'])) exit('No existe el cliente.');
?>
<script>
$(function(){
  $('#comentario_anular').focus();
  $('#anular').button({icons: {primary: 'ui-icon-locked'}});
  $('#eliminar').button({icons: {primary: 'ui-icon-trash'}});

  $('#nombre_nuevo_cliente').autocomplete({
    autoFocus: true,
    minLength: 4,
    source: helpers_path+'ajax.php?cliente=1',
    select: function(event, ui) {
      if(ui.item.id==$('#id_cliente_').val()){
        alertify.error('No puedes asignar el mismo cliente.');
        return false;
      }else{
        $('#id_nuevo_cliente').val(ui.item.id);
        $('#comentario_eliminar').focus();
      }
    }
  });

  $('#FAnular').validate({
    rules: {comentario: {required: true, minlength: 20}},
    messages: {comentario: {required: 'Escribe un comentario', minlength: 'Minimo 20 caracteres'}},
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(f){
      $('#anular').button('disable').button('option','label','Anulando...');
      $.ajax({
        url: clientes_path+'anular.php', type: 'POST',
        data: $(f).serialize(),
        success: function(msj){
          if(msj=='ok'){
            cargarPrincipal(clientes_path+"index.php?"+$('#BuscarClientes').serialize(), cerrarDialogo());
          }else{
            $('#anular').button('enable').button('option','label','Anular');
            $('#mensaje').html(msj).slideDown(600).delay(6000).fadeOut(600);
          }
        }
      })
    }
  });
  $('#FEliminar').validate({
    rules: {
      comentario: {required: true, minlength: 20},
      id_nuevo_cliente: 'required'
    },
    messages: {
      comentario: {required: 'Escribe un comentario', minlength: 'Minimo 20 caracteres'},
      id_nuevo_cliente: 'Selecciona un cliente'
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(f){
      var conf=confirm('¿Estas seguro? esta acción no se puede deshacer');
      if(!conf) return false;
      $('#eliminar').button('disable').button('option','label','Eliminando...');
      $.ajax({
        url: clientes_path+'ajax.php', type: 'POST',
        data: 'eliminar=12&'+$(f).serialize(),
        success: function(msj){
          if(msj=='ok'){
            cargarPrincipal(clientes_path+"index.php?"+$('#BuscarClientes').serialize(), cerrarDialogo());
          }else{
            $('#eliminar').button('enable').button('option','label','Eliminar');
            $('#mensaje').html(msj).slideDown(600).delay(10000).fadeOut(600);
          }
        }
      })
    }
  });
});
</script>
<input type="hidden" id="id_cliente_" value="<?= $cliente->id ?>" />
<h3><?= $cliente->nombre_completo ?></h3>
<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#tab1" title="Anular Cliente">Anular</a></li>
    <li><a data-toggle="tab" href="#tab2" title="Eliminar Cliente">Eliminar</a></li>
  </ul>
  <div class="tab-content text-center">
    <div id="tab1" class="tab-pane active">
      <?php if (! isset($_SESSION['permisos'][CLIENTES_ANULAR])) {
        echo 'No tienes permisos para anular clientes...';
      } else { ?>
      <form id="FAnular">
        <table>
          <tr>
            <td align="center"><b>¿Por qué quieres anular este cliente?</b></td>
          </tr>
          <tr>
            <td><textarea id="comentario_anular" name="comentario" rows="3" cols="40"></textarea></td>
          </tr>
        </table>
        <center><button id="anular">Anular</button></center>
        <input type="hidden" name="id" value="<?= $cliente->id ?>" />
        <?= nonce_create_form_input($cliente->id) ?>
      </form>
      <?php } ?>
    </div>
    <div id="tab2" class="tab-pane text-center">
      <?php if (! isset($_SESSION['permisos'][CLIENTES_ELIMINAR])) {
        echo 'No tienes permisos para eliminar clientes...';
      } else { ?>
      <form id="FEliminar">
        <table>
          <tr>
            <td align="center"><b>Asignar las guías al cliente:</b></td>
          </tr>
          <tr>
            <td>
              <input size="40" type="text" name="nombre_nuevo_cliente" id="nombre_nuevo_cliente" />
              <input type="hidden" name="id_nuevo_cliente" id="id_nuevo_cliente" />
            </td>
          </tr>
          <tr>
            <td align="center"><b>¿Por qué quieres eliminar este cliente?</b></td>
          </tr>
          <tr>
            <td>
              <textarea id="comentario_eliminar" name="comentario" rows="3" cols="40"></textarea>
              <br>
              <small>Ten en cuenta que esta acción será registrada</small>
            </td>
          </tr>
        </table>
        <center><button id="eliminar">Eliminar</button></center>
        <input type="hidden" name="id" value="<?= $cliente->id ?>" />
        <?php nonce_create_form_input($cliente->id) ?>
      </form>
      <?php } ?>
    </div>
  </div>
</div>
<div id="mensaje" class="ui-state-highlight ui-corner-all" style="display: none;margin:3px;padding:5px;"></div>
