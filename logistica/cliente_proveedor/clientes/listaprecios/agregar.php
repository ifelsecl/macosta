<?php
require "../../../seguridad.php";
if (! isset($_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}

if (! isset($_SESSION['permisos'][LISTA_PRECIOS_AGREGAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$cliente = new Cliente;
if (! $cliente->find($_GET['id'])) exit('No existe el cliente.');
?>
<script>
$(function() {
  $('#ciudad_destino').focus();
  $('#guardar').button({icons: { primary: 'ui-icon-circle-check'}});
  $('#ciudad_origen, #ciudad_destino').autocomplete({
    autoFocus: true,
    source: helpers_path+"ajax.php?ciudad=1",
    minLength: 3,
    select: function(event, ui) {
      if ($(this).attr('id')=='ciudad_destino') {
        $('#id_ciudad_destino').val(ui.item.id);
        $('#seguro').focus();
      } else {
        $('#id_ciudad_origen').val(ui.item.id);
        $('#ciudad_destino').focus();
      }
    }
  });
  $('#embalaje').autocomplete({
    autoFocus: true,
    autoFill: true,
    source: clientes_path+"ajax.php?buscarembalaje=1",
    minLength: 0,
    select: function(event,ui) {
      $('#id_embalaje').val(ui.item.id);
      $('#tipo_cobro').val(ui.item.tipo_cobro);
      $('#precio').focus();
      if (ui.item.tipo_cobro == 'Caja' || ui.item.tipo_cobro == 'Caja2') {
        $('.cobro_caja').fadeIn(600);
        $('.cobro_descuento').fadeOut(600);
      } else if (ui.item.tipo_cobro == 'Descuento') {
        $('.cobro_descuento').fadeIn(600);
        $('.cobro_caja').fadeOut(400);
      } else {
        $('.cobro_caja, .cobro_descuento').fadeOut(600);
      }
    }
  });
  var restriccion_peso = <?= $cliente->restriccionpeso ?>;
  $('#precio').keyup(function() {
    if (isNaN($(this).val())) return;
    var precio = 0,
        tipo_cobro = $('#tipo_cobro').val();
    if (tipo_cobro == 'Caja' || tipo_cobro == 'Descuento' || tipo_cobro == 'Caja2') {
      precio = restriccion_peso == 0 ? 0 : ($(this).val() / restriccion_peso).toFixed() ;
    } else {
      precio = 0;
    }
    $('#precio_kilo, #precio_kilovol').val(precio);
  });
  $('#AgregarPrecio').validate({
    rules: {
      id_ciudad_origen: 'required',
      id_ciudad_destino: 'required',
      id_embalaje: 'required',
      seguro: {required: true, number: true},
      precio: {required: true, number: true},
      precio_kilo: {required: true, number: true},
      precio_kilovol: {required: true, number: true},
      descuento3: {number: true},
      descuento6: {number: true},
      descuento8: {number: true}
    },
    messages: {
      id_ciudad_origen: 'Selecciona la ciudad origen',
      id_ciudad_destino: 'Selecciona la ciudad destino',
    },
    errorPlacement: function(er, el) {er.appendTo( el.parent("td").next("td") );},
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: lista_precios_path+'ajax.php',
        type: 'POST',
        data: 'guardar=1&'+$('#AgregarPrecio').serialize(),
        success: function(m) {
          if (m=='ok') {
            $(".right_content").load(lista_precios_path+"index.php?"+$('#lista_precios__search_form').serialize(), function() {
              $('#dialog').dialog('close');
            });
          } else {
            $('#guardar').button('enable').button('option','label','Guardar');
            $('div#m').html(m).slideDown(500).delay(6000).fadeOut(500);
          }
        }
      });
    }
  });
});
</script>
<form id="AgregarPrecio" action="#">
  <table>
    <tr>
      <td><b>Ciudad Origen:</b></td>
      <td>
        <input type="text" name="ciudad_origen" id="ciudad_origen" value="<?= $cliente->ciudad_nombre ?>" />
        <input type="hidden" name="id_ciudad_origen" id="id_ciudad_origen" value="<?= $cliente->idciudad ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ciudad Destino:</b></td>
      <td>
        <input type="text" name="ciudad_destino" id="ciudad_destino" />
        <input type="hidden" name="id_ciudad_destino" id="id_ciudad_destino" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Seguro (%):</b></td>
      <td><input type="text" size="5" name="seguro" id="seguro" value="<?= $cliente->porcentajeseguro ?>" /></td>
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
    <tr class="cobro_descuento" style="display: none;">
      <td><b>Descuento 3 Unid.:</b></td>
      <td><input type="text" name="descuento3" id="descuento3" value="0" /></td>
      <td></td>
    </tr>
    <tr class="cobro_descuento" style="display: none;">
      <td><b>Descuento 6 Unid.:</b></td>
      <td><input type="text" name="descuento6" id="descuento6" value="0" /></td>
      <td></td>
    </tr>
    <tr class="cobro_descuento" style="display: none;">
      <td><b>Descuento 8 Unid.:</b></td>
      <td><input type="text" name="descuento8" id="descuento8" value="0" /></td>
      <td></td>
    </tr>
  </table>
  <hr class="hr-small">
  <center><button id="guardar">Guardar</button></center>
  <input type="hidden" name="id_cliente" value="<?= $cliente->id ?>" />
</form>
<div id="m" class="ui-state-highlight ui-corner-all" style="padding: 3px; margin:2px;display:none;"></div>
