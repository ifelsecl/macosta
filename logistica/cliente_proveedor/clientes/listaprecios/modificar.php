<?php
require "../../../seguridad.php";
if(! isset($_SESSION['permisos'][LISTA_PRECIOS_MODIFICAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
$cliente = new Cliente;
$result = $cliente->HistorialListaPrecios($_REQUEST['id']);
$historial = array();
while ($h = mysql_fetch_object($result)) {
  $historial[] = $h;
}
?>
<script>
$(function(){
  var aceptar_btn = $('#aceptar').button({icons:{primary: 'ui-icon-circle-check'}});
  $('#porcentaje').focus();
  $('#ModificarListaPrecios').validate({
    rules: {
      porcentaje: {required: true, number: true}
    },
    messages: {
      porcentaje: {required: '', number: ''}
    },
    errorPlacement: function(error, element) {},
    highlight: function(i) {$(i).addClass("ui-state-highlight");},
    unhighlight: function(i) {$(i).removeClass("ui-state-highlight");},
    submitHandler: function(f){
      aceptar_btn.button('disable').button('option','label','Guardando...');
      $.ajax({
        url: lista_precios_path+'ajax.php',
        type: 'POST', dataType: 'json',
        data: 'modificar=1&'+$(f).serialize(),
        success: function(m){
          if(m.ok){
            $("#modificar").click();
            $('#actualizar').click();
          }else{
            aceptar_btn.button('enable').button('option','label','Aceptar');
            alert(m.mensaje);
          }
        }
      });
    }
  });
});
</script>
<table cellpadding="5" cellspacing="0">
  <tr>
    <td>
      <h2>Modificar</h2>
    </td>
    <td>
      <h2>Historial</h2>
    </td>
  </tr>
  <tr>
    <td>
      <form id="ModificarListaPrecios" method="post" action="#">
        <input type="hidden" name="id_cliente" value="<?= $_REQUEST['id'] ?>" />
        <table>
          <tr>
            <td>
              <label style="display: block;"><input type="radio" name="modo" checked="checked" value="Aumento" />Aumento</label>
            </td>
            <td>
              <label style="display: block;"><input type="radio" name="modo" value="Disminucion" />Disminución</label>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              Porcentaje (%): <input id="porcentaje" maxlength="4" type="text" class="input-mini" name="porcentaje" />
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <button id="aceptar">Aceptar</button>
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td style="border-left: 1px solid silver;">
      <table class="table table-condensed table-stripe table-bordered" cellpadding="0" >
        <thead>
          <th>Fecha</th>
          <th>Comentario</th>
        </thead>
        <tbody>
          <?php
          if(empty($historial)){
            echo '<tr><td colspan="2" class="expand">No se han realizado modificaciones</td></tr>';
          }else{
            foreach ($historial as $h) {
              $m = $h->modo=='Aumento' ? 'un aumento' : 'una disminución' ;
              echo '<tr><td>'.strftime('%b %d, %Y - %I:%M %p', strtotime($h->fecha)).'</td><td>'.ucfirst($h->usuario).' realizó '.$m.' de '.$h->porcentaje.'%.</td></tr>';
            }
          }
          ?>
        </tbody>
      </table>
    </td>
  </tr>
</table>
