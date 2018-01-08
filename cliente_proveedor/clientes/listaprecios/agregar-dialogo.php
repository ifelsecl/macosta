<?php
require "../../../seguridad.php";
if (! isset($_SESSION['permisos'][LISTA_PRECIOS_AGREGAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
?>
<script>
$(function(){
  $('#guardar').button({icons: {primary: 'ui-icon-circle-check'}});
  $('#ciudad_origen').autocomplete({
    autoFocus: true,
    source: helpers_path+"ajax.php?ciudad=1",
    minLength: 3,
    select: function(event,ui){
      $('#id_ciudad_origen').val(ui.item.id);
      $('#ciudad_destino').focus();
    }
  });
  $('#ciudad_destino').autocomplete({
    autoFocus: true,
    source: helpers_path+"ajax.php?ciudad=1",
    minLength: 3,
    select: function(event,ui){
      $('#id_ciudad_destino').val(ui.item.id);
      $('#seguro').focus();
    }
  });
  $('#embalaje').autocomplete({
    autoFocus: true,
    source: clientes_path+"ajax.php?buscarembalaje=si",
    minLength: 0,
    select: function(event,ui){
      $('#id_embalaje').val(ui.item.id);
      $('#tipo_cobro').val(ui.item.tipo_cobro);
      $('#precio').focus();
      if(ui.item.tipo_cobro=='Caja'){
        $('.cobro_caja').fadeIn(600);
      }else{
        $('.cobro_caja').fadeOut(600);
      }
    }
  });
  /* Al cambiar de precio calcular el precio por kilo y kilo/vol */
  $('#precio').change(function(){
    if($('#tipo_cobro').val()=='Caja'){
      var p=($(this).val()/30).toFixed(0);
      $('#precio_kilo,#precio_kilovol').val(p);
    }else{
      $('#precio_kilo,#precio_kilovol').val(0);
    }
  });
  $('#AgregarPrecio').validate({
    rules: {
      id_ciudad_origen: 'required',
      id_ciudad_destino: 'required',
      id_embalaje: 'required',
      seguro: {required: true, number: true},
      precio: {required: true, number: true},
      precio_kilo: {required: true, number: true},
      precio_kilovol: {required: true, number: true}
    },
    messages: {
      id_ciudad_origen: 'Selecciona la ciudad origen',
      id_ciudad_destino: 'Selecciona la ciudad destino',
      id_embalaje: 'Selecciona el embalaje',
      seguro: {required: 'Escribe un porcentaje', number: 'Solo numeros'},
      precio: {required: 'Escribe el precio', number: 'Solo números'},
      precio_kilo: {required: 'Escribe el precio', number: 'Solo números'},
      precio_kilovol: {required: 'Escribe el precio', number: 'Solo números'}
    },
    errorPlacement: function(er, el) {er.appendTo( el.parent("td").next("td") );},
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: clientes_path+'listaprecios/ajax.php',
        type: 'POST',
        data: 'guardar=192&'+$('#AgregarPrecio').serialize(),
        success: function(m){
          if(m=='ok'){
            $('#dialog').dialog('close');
          }else{
            $('#guardar').button('enable').button('option','label','Guardar');
            $('div#m').html(m).slideDown(500).delay(6000).fadeOut(500);
          }
        }
      });
    }
  });
  $('#ciudad_destino').focus();
});
</script>
<form id="AgregarPrecio">
  <table>
    <tr>
      <td><b>Ciudad Origen:</b></td>
      <td title="Minimo 3 caracteres">
        <input type="text" name="ciudad_origen" id="ciudad_origen" value="BARRANQUILLA" />
        <input type="hidden" name="id_ciudad_origen" id="id_ciudad_origen" value="08001000" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ciudad Destino:</b></td>
      <td title="Minimo 3 caracteres">
        <input type="text" name="ciudad_destino" id="ciudad_destino" />
        <input type="hidden" name="id_ciudad_destino" id="id_ciudad_destino" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Seguro (%):</b></td>
      <td><input type="text" size="5" name="seguro" id="seguro" value="1" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Embalaje:</b></td>
      <td title="Escribe '%' para obtener todos los embalajes.">
        <input type="text" name="embalaje" id="embalaje" />
        <input type="hidden" name="id_embalaje" id="id_embalaje" />
        <input type="hidden" name="tipo_cobro" id="tipo_cobro" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Precio:</b></td>
      <td><input type="text" name="precio" id="precio" /></td>
      <td></td>
    </tr>
    <tr class="cobro_caja" style="display: none;">
      <td><b>Precio Kilo:</b></td>
      <td><input type="text" name="precio_kilo" id="precio_kilo" value="0" /></td>
      <td></td>
    </tr>
    <tr class="cobro_caja" style="display: none;">
      <td><b>Precio Kilo/Vol:</b></td>
      <td><input type="text" name="precio_kilovol" id="precio_kilovol" value="0" /></td>
      <td></td>
    </tr>
  </table>
  <hr />
  <center><button id="guardar">Guardar</button></center>
  <input type="hidden" name="id_cliente" value="<?= $_GET['id'] ?>" />
</form>
<div id="m" class="ui-state-highlight ui-corner-all" style="padding: 3px; margin:2px;display:none;"></div>
