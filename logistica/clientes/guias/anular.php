<?php
$raiz="../../";
require "../seguridad.php";

if (! isset($_GET['id'])) {
  include $raiz."mensajes/id.php";
  exit;
}
?>
<script type="text/javascript">
$(function(){
  $('#anular_guia').button({icons: {primary: 'ui-icon-trash'}});
  $('#AnularGuia').validate({
    rules: {comentario: {required: true, minlength: 20}},
    messages: {comentario: {required: 'Escribe un comentario.', minlength: 'Minimo 20 caracteres.'}},
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form){
      $('#mensaje').slideUp(100);
      var data='anular=101&'+$("#AnularGuia").serialize();
      $('#anular_guia').button('disable').button('option','label','Anulando...');
      $.ajax({
        url: guias_path+'ajax.php',
        type: "POST",
        data: data,
        success: function(msj){
          if(!msj){
            $('#center_content').load(guias_path);
            $('#dialog').dialog('close');
          }else{
            $('#anular_guia').button('enable').button('option','label','Anular');
            $('#mensaje').html(msj).slideDown(600);
          }
        }
      });
    }
  });
  $('#comentario').focus();
});
</script>
<form id="AnularGuia" action="#" method="post">
  <table cellspacing="0" class="text-center">
    <tr>
      <td><b>¿Porqué quieres anular la guía <?= $_GET['id'] ?>?</b></td>
    </tr>
    <tr>
      <td><textarea id="comentario" name="comentario" rows="5" style="width: 94%"></textarea></td>
    </tr>
    <tr>
      <td><small>Ten en cuenta que ésta acción será registrada.</small></td>
    </tr>
  </table>
  <hr class="hr-small">
  <center>
    <button id="anular_guia">Anular</button>
  </center>
  <input type="hidden" name="id" value="<?= $_GET['id'] ?>" />
</form>
<div id="mensaje" class="ui-state-highlight ui-corner-all" style="padding:5px;margin:3px;display: none;"></div>
