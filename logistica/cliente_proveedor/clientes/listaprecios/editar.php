<?php
require "../../../seguridad.php";
if (! isset($_SESSION['permisos'][LISTA_PRECIOS_EDITAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
$precio = Precio::find($_GET['idcliente'], $_GET['idciudadorigen'], $_GET['idciudaddestino'], $_GET['idembalaje']);
?>
<script>
$(function() {
  $('#guardar').button({icons: { primary: 'ui-icon-circle-check'}});
  $('#ciudad_destino').focus();
  $('#ciudad_origen, #ciudad_destino').autocomplete({
    autoFocus: true,
    source: helpers_path+"ajax.php?ciudad=1",
    minLength: 3,
    select: function(event, ui) {
      if ('ciudad_origen' == event.target.id) {
        $('#id_ciudad_origen').val(ui.item.id);
        $('#ciudad_destino').focus();
      } else {
        $('#id_ciudad_destino').val(ui.item.id);
        $('#seguro').focus();
      }
    }
  });
  $('#embalaje').autocomplete({
    autoFocus: true,
    autoFill: true,
    source: clientes_path+"ajax.php?buscarembalaje=si",
    minLength: 0,
    select: function(event,ui) {
      $('#id_embalaje').val(ui.item.id);
      $('#tipo_cobro').val(ui.item.tipo_cobro);
      $('#precio').focus();
      ComprobarTipoCobro();
      $('#precio').keyup();
    }
  });
  ComprobarTipoCobro();
  /**
   * Comprueba el tipo de cobro de un embalaje.
   */
  function ComprobarTipoCobro() {
    var tc = $('#tipo_cobro').val();
    if (tc == 'Caja' || tc == 'Caja2') {
      $('.cobro_caja').fadeIn(600);
      $('.cobro_descuento').fadeOut(600);
    } else if (tc == 'Descuento') {
      $('.cobro_descuento').fadeIn(600);
      $('.cobro_caja').fadeOut(400);
    } else {
      $('.cobro_caja, .cobro_descuento').fadeOut(600);
    }
  }
  $('#precio').keyup(function() {
    var tipo_cobro = $('#tipo_cobro').val();
    if (tipo_cobro == 'Caja' || tipo_cobro == 'Descuento' || tipo_cobro == 'Caja2') {
      var p = ($(this).val()/30).toFixed();
    } else {
      var p = 0;
    }
    $('#precio_kilo, #precio_kilovol').val(p);
  });
  $('#EditarPrecio').validate({
    rules: {
      id_ciudad_origen: 'required',
      id_ciudad_destino: 'required',
      embalaje: 'required',
      seguro: {required: true, number: true},
      precio: {required: true, number: true},
      descuento3: {number: true},
      descuento6: {number: true},
      descuento8: {number: true}
    },
    messages: {
      id_ciudad_origen: 'Selecciona la ciudad',
      id_ciudad_destino: 'Selecciona la ciudad'
    },
    errorPlacement: function(er, el) {er.appendTo(el.parent("td").next("td") );},
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#guardar').button('disable').button('option', 'label', 'Guardando...');
      $.ajax({
        url: lista_precios_path+'ajax.php', type: 'POST', dataType: 'json',
        data: 'editar=192&'+$('#EditarPrecio').serialize(),
        success: function(m) {
          if (m.success) {
            $(".right_content").load(lista_precios_path+"index.php?"+$('#lista_precios__search_form').serialize(), function() {
              $('#dialog').dialog('close');
            });
          } else {
            alertify.error(m.message);
            $('#guardar').button('enable').button('option', 'label', 'Guardar');
          }
        }
      });
    }
  });
});
</script>
<form id="EditarPrecio">
  <table>
    <tr>
      <td><b>Ciudad Origen:</b></td>
      <td title="Minimo 3 caracteres">
        <input type="text" name="ciudad_origen" id="ciudad_origen" value="<?= $precio->ciudad_origen_nombre ?>" />
        <input type="hidden" name="id_ciudad_origen" id="id_ciudad_origen" value="<?= $precio->idciudadorigen ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ciudad Destino:</b></td>
      <td title="Minimo 3 caracteres">
        <input type="text" name="ciudad_destino" id="ciudad_destino" value="<?= $precio->ciudad_destino_nombre ?>" />
        <input type="hidden" name="id_ciudad_destino" id="id_ciudad_destino" value="<?= $precio->idciudaddestino ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Seguro (%):</b></td>
      <td><input type="text" size="5" name="seguro" id="seguro" value="<?= $precio->seguro ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Embalaje:</b></td>
      <td title="Escribe '%' para obtener todos los embalajes.">
        <input type="text" name="embalaje" id="embalaje" value="<?= $precio->embalaje_nombre ?>" />
        <input type="hidden" name="id_embalaje" id="id_embalaje" value="<?= $precio->idembalaje ?>" />
        <input type="hidden" name="tipo_cobro" id="tipo_cobro" value="<?= $precio->tipo_cobro ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Precio:</b></td>
      <td><input type="text" name="precio" id="precio" value="<?= $precio->precio ?>" /></td>
      <td></td>
    </tr>
    <tr class="cobro_caja" style="display: none;">
      <td><b>Precio Kilo:</b></td>
      <td><input type="text" name="precio_kilo" id="precio_kilo" value="<?= $precio->precio_kilo ?>" /></td>
      <td></td>
    </tr>
    <tr class="cobro_caja" style="display: none;">
      <td><b>Precio Kilo/Vol:</b></td>
      <td><input type="text" name="precio_kilovol" id="precio_kilovol" value="<?= $precio->precio_kilovol ?>" /></td>
      <td></td>
    </tr>
    <tr class="cobro_descuento" style="display: none;">
      <td><b>Descuento 3 Und:</b></td>
      <td><input type="text" name="descuento3" id="descuento3" value="<?= $precio->descuento3 ?>" /></td>
      <td></td>
    </tr>
    <tr class="cobro_descuento" style="display: none;">
      <td><b>Descuento 6 Und:</b></td>
      <td><input type="text" name="descuento6" id="descuento6" value="<?= $precio->descuento6 ?>" /></td>
      <td></td>
    </tr>
    <tr class="cobro_descuento" style="display: none;">
      <td><b>Descuento 8 Und:</b></td>
      <td><input type="text" name="descuento8" id="descuento8" value="<?= $precio->descuento8 ?>" /></td>
      <td></td>
    </tr>
  </table>
  <hr class="hr-small">
  <center><button id="guardar">Guardar</button></center>
  <input type="hidden" name="id_cliente" value="<?= $precio->idcliente ?>" />
  <input type="hidden" name="id_ciudad_origen_old" value="<?= $precio->idciudadorigen ?>" />
  <input type="hidden" name="id_ciudad_destino_old" value="<?= $precio->idciudaddestino ?>" />
  <input type="hidden" name="id_embalaje_old" value="<?= $precio->idembalaje ?>" />
</form>
