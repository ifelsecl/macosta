<?php
require "../seguridad.php";
if (! isset($_SESSION['permisos'][FACTURACION_FACTURAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
?>
<script>
$(function() {
  var factura = {
    descuento: 0,
    porcentaje_descuento: 0,
    guias: [],
    total: 0
  };
  $('#nombre_cliente').focus();
  $('#guardar').button({icons: {primary: 'ui-icon-circle-check'}}).click(function(e) {
    e.preventDefault();
    if(!$('#NuevaFactura').valid()) return false;
    $('#guardar').button('disable').button('option','label','Facturando...');
    $.ajax({
      url: facturacion_path+'ajax.php',
      type: "POST", dataType: 'json',
      data: 'facturar=101&'+$('#NuevaFactura').serialize(),
      success: function(msj) {
        if(msj.error == false) {
          $('#actualizar').click();
          LOGISTICA.Dialog.open('Facturacion', msj.mensaje, true);
          regresar();
        }else{
          $('#guardar').button('enable').button('option','label','Facturar');
          var html='<table align="center"><tr><td><i class="icon-warning-sign"></i></td><td>'+msj.mensaje+'</td></tr></table>';
          LOGISTICA.Dialog.open('Facturacion', html, true);
        }
      }
    });
  });
  $('#nombre_cliente').autocomplete({
    autoFocus: true,
    minLength: 3,
    source: helpers_path+'ajax.php?cliente=1',
    select: function(event, ui) {
      $('#nombre_cliente').val(ui.item.nombre);
      $('#condicion_pago').val(ui.item.condicion_pago);
      $('#id_cliente').val(ui.item.id);
      factura.porcentaje_descuento=ui.item.descuento;
      $('#descuento').text(ui.item.descuento+'%');
      $('#fecha_emision').focus();
      return false;
    }
  });
  $('#fecha_emision').datepicker({
    changeMonth: true,
    changeYear: true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    autoSize: true
  });
  var dates = $("#from, #to").datepicker({
    changeMonth: true,
    changeYear: true,
    numberOfMonths: 3,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    autoSize: true,
    onSelect: function(selectedDate) {
      var option = this.id == "from" ? "minDate" : "maxDate";
      dates.not( this ).datepicker("option", option, selectedDate);
    }
  });
  $('#NuevaFactura').validate({
    rules: {
      id_cliente: 'required',
      condicion_pago: {required: true, digits: true},
      from: {required: true},
      to: {required: true}
    },
    messages: {
      id_cliente: 'Selecciona el cliente',
      from: {required: 'Selecciona la fecha.'},
      to: {required: 'Selecciona la fecha.'}
    },
    errorPlacement: function(error, element) {
      error.appendTo( element.parent("td").next("td") );
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");}
  });
  $('#ft').change(function() {
    if($(this).attr('checked')) {
      $('.cliente').fadeOut(600);
      $('.cliente input').attr('disabled', 'disabled');
      $('#tipo_fecha').click();
    }else{
      $('.cliente input').removeAttr('disabled');
      $('.cliente').fadeIn(600, function() {
        $('#nombre_cliente').focus();
      });
    }
  });
  $('#tipo_fecha').click(function() {
    $('.numeros').fadeOut(600);
    $('.numeros input').attr('disabled','disabled');
    $('.fecha').fadeIn(600);
    $('.fecha input').removeAttr('disabled');
  }).click();
  $('#tipo_numeros').click(function() {
    $('#ft').removeAttr('checked');
    $('.cliente input,.numeros input').removeAttr('disabled');
    $('.cliente,.numeros').fadeIn(600);
    $('.fecha').fadeOut(600);
    $('.fecha input').attr('disabled','disabled');
    $('#id_guia').focus();
  });
  $btn_agregar_guia = $('#agregar');
  $btn_agregar_guia.button({icons: {primary: 'ui-icon-circle-plus'}}).click(function(e) {
    e.preventDefault();
    if(!$.trim($('#id_cliente').val())) {
      alert('Selecciona un cliente');
      $('#nombre_cliente').focus();
      return;
    }

    var id=Math.round($('#id_guia').val());
    if( !$.trim(id) || isNaN(id) ) return false;
    if(factura.guias.indexOf(id)!=-1) {
      alert('La guia '+id+' ya fue agregada');
      return;
    }
    if(factura.guias.length >= 35) {
      alert('Solo se permiten 35 guias por factura.');
      return;
    }
    var data='cg=1b12n&id_cliente='+$('#id_cliente').val()+'&id_guia='+id;
    $btn_agregar_guia.button('disable');
    $.getJSON(facturacion_path+'ajax.php', data, function(r) {
      $btn_agregar_guia.button('enable');
      if(!r) return false;
      if(r.error=='no') {
        factura.total+=r.valor;
        factura.descuento=(factura.total*(factura.porcentaje_descuento/100)).toFixed();
        $('#lbl_descuento').text(factura.descuento);
        $('#lbl_subtotal').text(factura.total);
        $('#lbl_total').text(factura.total-factura.descuento);
        $('#factura_guias tbody').append(r.mensaje);
        factura.guias.push(id);
        $('#id_guia').val('').focus();
      }else{
        LOGISTICA.Dialog.open('Facturacion',r.mensaje,true);
      }
    });
  });
  $("table#factura_guias").on("click", 'button.quitar', function() {
    var valor = $(this).parent().prev().text();
    var index = factura.guias.indexOf(valor);
    factura.guias.splice(index, 1);

    factura.total -= valor;
    factura.descuento = (factura.total*(factura.porcentaje_descuento/100)).toFixed();
    $('#lbl_subtotal').text(factura.total);
    $('#lbl_descuento').text(factura.descuento);
    $('#lbl_total').text(factura.total-factura.descuento);
    $(this).parent().parent().remove();
  });
});
</script>
<button id="regresar" class="btn btn-success pull-right" onClick="regresar();">Regresar</button>
<h2>Nueva Factura</h2>
<hr class="hr-small">
<form id="NuevaFactura" action="#" method="post" style="padding:0 0 0 20px">
  <table>
    <tr class="cliente">
      <td><b>Cliente:</b></td>
      <td>
        <input type="text" name="cliente" id="nombre_cliente" />
        <input type="hidden" name="id_cliente" id="id_cliente" />
      </td>
      <td></td>
    </tr>
    <tr class="cliente">
      <td><b>Descuento</b></td>
      <td id="descuento"></td>
    </tr>
    <tr>
      <td><b>Fecha emisión</b></td>
      <td><input readonly="readonly" class="input-small" type="text" id="fecha_emision" name="fecha_emision" value="<?= date("Y-m-d") ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Condición de pago <small>(días)</small></b></td>
      <td><input type="text" class="input-mini" maxlength="3" id="condicion_pago" name="condicion_pago" /></td>
      <td></td>
    </tr>
    <tr>
      <td align="center">
        <label for="tipo_fecha">
          <input title="Fecha" id="tipo_fecha" type="radio" name="tipo" value="fecha" checked="checked" />
          Rango de Fecha
        </label>
      </td>
      <td align="center">
        <label for="tipo_numeros">
          <input title="Guias" id="tipo_numeros" type="radio" name="tipo" value="numeros" />
          Número de Guías
        </label>
      </td>
    </tr>
    <tr class="fecha">
      <td><b>Facturar desde:</b></td>
      <td><input type="text" readonly="readonly" class="fecha input-small" id="from" name="from" /></td>
      <td></td>
    </tr>
    <tr class="fecha">
      <td><b>Facturar hasta:</b></td>
      <td><input type="text" readonly="readonly" class="fecha input-small" id="to" name="to" /></td>
      <td></td>
    </tr>
    <tr class="form-inline numeros" style="display:none;">
      <td valign="top"><b>Número de Guía</b></td>
      <td>
        <input type="text" class="input-medium" disabled="disabled" name="id_guia" id="id_guia" />
        <button id="agregar">Agregar</button>
      </td>
    </tr>
  </table>
  <table class="table table-bordered table-condensed table-hover" id="factura_guias">
    <thead>
      <tr>
        <th>Número</th>
        <th>Destinatario</th>
        <th>Destino</th>
        <th>Seguro</th>
        <th>Flete</th>
        <th>Total</th>
        <th>Quitar</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
  <table class="numeros" style="float:right;font-weight:bold;font-size:18px;">
    <tr>
      <td>SubTotal</td>
      <td align="right" id="lbl_subtotal">0</td>
    </tr>
    <tr>
      <td>Descuento</td>
      <td align="right" id="lbl_descuento">0</td>
    </tr>
    <tr>
      <td style="border-top:1px solid gray;">Total</td>
      <td align="right" style="border-top:1px solid gray;" id="lbl_total">0</td>
    </tr>
  </table>
  <br>
  <center><button type="button" id="guardar">Facturar</button></center>
  <?php nonce_create_form_input("Guardar") ?>
</form>
