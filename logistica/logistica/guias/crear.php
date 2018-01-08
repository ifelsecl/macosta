<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][GUIAS_CREAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$configuracion = new Configuracion;
?>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar();">Regresar</button>
<h2>Nueva Guía</h2>
<hr class="hr-small">
<form id="CrearGuia" name="CrearGuia" method="post">
  <table>
    <tr>
      <td>
        <b>Unidad de medida:</b><br>
        <select id="unidadmedida" name="guia[unidadmedida]" class="input-medium">
          <?php
          foreach (Guia::$unidades_medida as $key => $value) {
            echo '<option value="'.$key.'">'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td>
        <b>Naturaleza de la carga:</b><br>
        <select id="naturaleza" name="guia[naturaleza]" class="input-medium">
          <?php
          foreach (Guia::$naturalezas_carga as $key => $value) {
            echo '<option value="'.$key.'">'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td>
        <b>Propietario de la carga</b><br>
        <select class="input-medium" name="guia[propietario]" id="propietario">
          <?php
          foreach (Guia::$propietarios_carga as $p) {
            echo '<option>'.$p.'</option>';
          }
          ?>
        </select>
      </td>
      <td>
        <b>Tipo Operación</b><br>
        <select name="guia[id_tipo_operacion]" class="input-medium">
          <?php
          foreach (Guia::$tipos_operacion as $key => $value) {
            echo '<option value="'.$key.'">'.$value.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>
        <b>Tipo Empaque:</b><br>
        <select id="empaque" name="guia[empaque]">
          <?php
          foreach (Guia::$unidades_empaque as $key => $value) {
            echo '<option value="'.$key.'">'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td>
        <b>Peso del contenedor (Kg):</b> <span title="Peso vacío del contenedor" class="ayuda">[?]</span><br>
        <input disabled="disabled" class="input-mini" type="text" name="guia[peso_contenedor]" id="peso_contenedor" value="0" />
      </td>
      <td>
        <b>Recogida</b><br>
        <select class="input-small" name="guia[recogida]" id="recogida">
          <option value="no" selected="selected">NO</option>
          <option value="si">SI</option>
        </select>
      </td>
    </tr>
  </table>
  <table style="margin-bottom: 4px" cellpadding="0">
    <tr>
      <td class="well" style="padding: 5px 10px;">
        <b>Remitente</b>
        <div class="btn-group pull-right">
          <?php
          if (isset($_SESSION['permisos'][CLIENTES_CREAR])) {
            echo '<button title="Crear" class="btn btn-info" type="button" id="crear_cliente"><i class="icon-plus"></i></button>';
          }
          if (isset($_SESSION['permisos'][CLIENTES_EDITAR])) {
            echo '<button title="Editar" class="btn btn-info" type="button" id="editar_cliente"><i class="icon-pencil"></i></button>';
          }
          ?>
        </div>
        <table cellpadding="0">
          <tr>
            <td>Código:</td>
            <td><input class="input-small" type="text" id="codigo_cliente" /></td>
          </tr>
          <tr>
            <td>Nombre:</td>
            <td><input class="input-medium" type="text" id="nombre_cliente" /></td>
          </tr>
          <tr>
            <td>No. Ident:</td>
            <td><input class="input-medium" type="text" id="ni_cliente" /></td>
          </tr>
		  <tr>
            <td>N°Sede:</td>
            <td><input class="input-medium" readonly="readonly" type="text" id="numero_sede" /></td>
          </tr>
		  <tr>
            <td>Sede:</td>
            <td><input class="input-medium" readonly="readonly" type="text" id="sede" /></td>
          </tr>
          <tr>
            <td>Dirección:</td>
            <td><input class="input-medium" readonly="readonly" type="text" id="direccion_cliente" /></td>
          </tr>
		  <tr>
            <td>Ciudad:</td>
            <td><input class="input-medium" readonly="readonly" type="text" id="ciudad_cliente" /></td>
          </tr>
          <tr>
            <td>Telefono:</td>
            <td><input class="input-medium" readonly="readonly" type="text" id="telefono_cliente" /></td>
          </tr>
        </table>
        <input type="hidden" name="guia[idcliente]" id="idcliente" />
        <input type="hidden" id="id_ciudad_cliente" />
      </td>
      <td width="10"></td>
      <td class="well" style="padding: 5px 10px;">
        <b>Destinatario</b>
        <div class="btn-group pull-right">
          <?php
          if (isset($_SESSION['permisos'][CLIENTES_CREAR])) {
            echo '<button title="Crear" class="btn btn-info" type="button" id="crear_contacto"><i class="icon-plus"></i></button>';
          }
          if (isset($_SESSION['permisos'][CLIENTES_EDITAR])) {
            echo '<button title="Editar" class="btn btn-info" type="button" id="editar_contacto"><i class="icon-pencil"></i></button>';
          }
          ?>
        </div>
        <table cellpadding="0">
          <tr>
            <td>Codigo:</td>
            <td><input type="text" class="input-small" readonly="readonly" id="codigo_contacto" /></td>
          </tr>
          <tr>
            <td>Nombre:</td>
            <td><input class="input-medium" type="text" id="nombre_contacto" /></td>
          </tr>
          <tr>
            <td>No. Ident:</td>
            <td><input class="input-medium" readonly="readonly" type="text" id="ni_contacto" /></td>
          </tr>
          <tr>
            <td>Dirección:</td>
            <td><input class="input-medium" readonly="readonly" type="text" id="direccion_contacto" /></td>
          </tr>
          <tr>
            <td>Ciudad:</td>
            <td><input class="input-medium" readonly="readonly" type="text" id="ciudad_contacto" /></td>
          </tr>
          <tr>
            <td>Telefono:</td>
            <td><input class="input-medium" readonly="readonly" type="text" id="telefono_contacto" /></td>
          </tr>
        </table>
        <input type="hidden" readonly="readonly" name="guia[idcontacto]" id="idcontacto" />
        <input type="hidden" id="id_ciudad_contacto" />
      </td>
    </tr>
  </table>
  <table cellpadding="0">
    <tr>
      <td><b>Forma de pago:</b></td>
      <td>
        <select id="formapago" name="guia[formapago]">
          <option selected="selected" value="">Selecciona...</option>
          <?php
          foreach (Guia::$formas_pago as $fp) {
            echo '<option value="'.$fp.'">'.$fp.'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Valor Declarado:</b></td>
      <td>
        <input type="text" name="guia[valordeclarado]" id="valordeclarado" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Seguro:</b></td>
      <td>
        <div class="input-prepend">
          <span class="add-on"><span class="lbl_seguro">-</span>%</span>
          <input type="text" class="input-small" name="guia[valorseguro]" id="valorseguro" />
        </div>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>No. documento:</b></td>
      <td><input type="text" name="guia[documentocliente]" id="documentocliente" value="" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Número Guía Ant. <span class="ayuda" title="Numero de la guia anterior">[?]</span>:</b></td>
      <td><input type="text" name="guia[numero]" id="numero" /></td>
    </tr>
    <tr>
      <td valign="top"><b>Observaciones:</b></td>
      <td>
        <textarea id="observacion" name="guia[observacion]" cols="32" rows="3"></textarea>
      </td>
      <td></td>
    </tr>
  </table>
  <hr class="hr-small">
  <input type="hidden" id="porcentaje_seguro" readonly="readonly" value="0" />
</form>
<table style="width: 100%" cellpadding="0">
  <tr>
    <td>
      <!-- Agregar Producto con lista de precios -->
      <form id="AgregarProducto" name="AgregarProducto" action="#" method="post" style="display:none">
        <table cellpadding="0">
          <tr>
            <td><b>Producto:</b></td>
            <td>
              <input type="text" name="nombre_producto" id="nombre_producto" />
              <input type="hidden" name="id_producto" id="id_producto" />
            </td>
            <td></td>
          </tr>
          <tr>
            <td><b>Cobrar por:</b></td>
            <td>
              <select id="tipo_cobro" name="tipo_cobro">
                <option value="">...</option>
              </select>
            </td>
            <td></td>
          </tr>
        </table>
        <table cellpadding="1">
          <tr>
            <td><b>Unidades:</b></td>
            <td><b>Peso (Kg):</b></td>
            <td><b>Ancho (cm)</b></td>
            <td><b>Largo (cm)</b></td>
            <td><b>Alto (cm)</b></td>
          </tr>
          <tr>
            <td><input class="input-mini" maxlength="10" id="unidades" name="unidades" value="" type="text" /></td>
            <td><input class="input-mini" maxlength="10" id="peso" value="" type="text" name="peso" /></td>
            <td><input class="input-mini" maxlength="4" type="text" value="1" id="ancho" name="ancho" /></td>
            <td><input class="input-mini" maxlength="4" type="text" value="1" id="largo" name="largo" /></td>
            <td><input class="input-mini" maxlength="4" type="text" value="1" id="alto" name="alto" /></td>
          </tr>
        </table>
        <hr class="hr-small">
        <center><button type="submit" id="agregar">Agregar</button></center>
      </form>
    </td>
    <td>
      <!-- Agregar Producto escribiendo el precio directamente -->
      <form id="AgregarProducto2" name="AgregarProducto2" action="#" method="post" style="display:none;">
        <table cellpadding="0">
          <tr>
            <td><b>Producto:</b></td>
            <td>
              <input type="text" name="nombre_producto2" id="nombre_producto2" />
              <input type="hidden" name="id_producto2" id="id_producto2" />
            </td>
            <td></td>
          </tr>
          <tr>
            <td><b>Valor a cobrar:</b></td>
            <td><input type="text" name="valor" id="valor" /></td>
            <td></td>
          </tr>
        </table>
        <table cellpadding="1">
          <tr>
            <td><b>Unidades:</b></td>
            <td><b>Peso (Kg):</b></td>
            <td><b>Ancho (cm)</b></td>
            <td><b>Largo (cm)</b></td>
            <td><b>Alto (cm)</b></td>
          </tr>
          <tr>
            <td><input class="input-mini" maxlength="10" id="unidades2" name="unidades2" value="" type="text" /></td>
            <td><input class="input-mini" maxlength="10" id="peso2" value="" type="text" name="peso2" /></td>
            <td><input class="input-mini" maxlength="4" type="text" value="1" id="ancho2" name="ancho2" /></td>
            <td><input class="input-mini" maxlength="4" type="text" value="1" id="largo2" name="largo2" /></td>
            <td><input class="input-mini" maxlength="4" type="text" value="1" id="alto2" name="alto2" /></td>
          </tr>
        </table>
        <hr class="hr-small">
        <center><button type="submit" id="agregar2">Agregar</button></center>
      </form>
    </td>
  </tr>
</table>
<form id="items" name="items" action="#" method="post">
  <table class="table table-condensed table-bordered table-striped" cellpadding="0">
    <thead>
      <tr>
        <th>Producto</th>
        <th>Unid.</th>
        <th>Peso (kg)</th>
        <th>Kilo/Vol</th>
        <th>Valor</th>
        <th>Borrar</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</form>
<input type="hidden" id="restriccion_peso" value="" />
<input type="hidden" id="constante" value="<?= $configuracion->calKiloVolumen ?>" />
<center><button id="guardar">Guardar</button></center>
<!--Desde aqui se escribe el código que aparece en GUIAS-NUEVA GUIA, donde se autocompleta el llenado del formulario-->
<script>
(function() {
  var Guia = {
    items: [],
    set_values: function(e, ui) {
      $('#nombre_cliente').val(ui.item.nombre);
      $('#ni_cliente').val(ui.item.numero_identificacion);
	  $('#numero_sede').val(ui.item.numero_sede);
	  $('#sede').val(ui.item.sede);
      $('#direccion_cliente').val(ui.item.direccion);
      $('#ciudad_cliente').val(ui.item.nombre_ciudad);
      $('#telefono_cliente').val(ui.item.telefono);
      $('#porcentaje_seguro').val(ui.item.porcentajeseguro);
      $('.lbl_seguro').text(ui.item.porcentajeseguro);
      $('#id_ciudad_cliente').val(ui.item.idciudad);
      $('#restriccion_peso').val(ui.item.restriccionpeso);
      $('#idcontacto').val('');
      $('#id_ciudad_contacto').val('');
      $('#idcliente').val(ui.item.id);
      $('#codigo_cliente').val(ui.item.id);
      $('#nombre_contacto').val('').focus();
	Guia.cargar_lista_precios(false);
      return false;
    },
    cargar_lista_precios: function(alert) {
      $.ajax({
        url: guias_path+'ajax.php', type: 'POST',
        data: {
          buscarembalaje: 'si',
          id_cliente: $('#idcliente').val(),
          id_ciudad_cliente: $('#id_ciudad_cliente').val(),
          id_ciudad_contacto: $('#id_ciudad_contacto').val()
        },
        success: function(msj) {
          if (msj == 'no') {
            if (alert) {
              alertify.log('El cliente no tiene precios para la ciudad de este contacto.');
              $('tipo_cobro').html('');
              $('#formapago').focus();
            } else {
              $('#nombre_contacto').focus();
            }
          } else {
            $('#tipo_cobro').html(msj);
          }
        }
      });
    },
    i: 1,
    add_item: function (producto, codigo_producto, unidades, peso, kilo_vol, tipo_cobro, precio) {
      var fila = '<tr>';
      fila+='<td title="'+producto+'">'+producto.substr(0,35)+'<input type="hidden" name="items['+this.i+'][idproducto]" value="'+codigo_producto+'" /></td>';
      fila+='<td>'+unidades+'<input type="hidden" name="items['+this.i+'][unidades]" value="'+unidades+'" /></td>';
      fila+='<td>'+peso+'<input type="hidden" name="items['+this.i+'][peso]" value="'+peso+'" /></td>';
      fila+='<td>'+kilo_vol+'<input type="hidden" name="items['+this.i+'][kilo_vol]" value="'+kilo_vol+'" /><input type="hidden" name="items['+this.i+'][idembalaje]" value="'+tipo_cobro+'" /></td>';
      <?php if (isset($_SESSION['permisos'][GUIAS_EDITAR_PRECIO_ITEMS])) { ?>
        fila += '<td><input type="text" class="input-mini" name="items['+this.i+'][valor]" value="'+precio.toString()+'" /></td>';
      <?php } else { ?>
        fila += '<td><input type="hidden" name="items['+this.i+'][valor]" value="'+precio.toString()+'" />'+precio.toString()+'</td>';
      <?php } ?>
      fila+='<td><button type="button" tabindex="-1" class="btn borrar btn-danger btn-mini"><i class="icon-remove"></i></button></td>';
      fila+='</tr>';
      $("#items table tbody").append(fila);
      this.i++;
    },
    greatest: function (u, k, v) {
      if (u==k && u==v) return parseFloat(u);
      if (u>=k && u>=v) return parseFloat(u);
      if (k>=u && k>=v) return parseFloat(k);
      if (v>=u && v>=k) return parseFloat(v);
    }
  };
  $('#codigo_cliente').focus();
  $('#guardar').button({icons: {primary:'ui-icon-circle-check'}}).click(function(event) {
    event.preventDefault();
    if (! $('#CrearGuia').valid()) {
      $('#nombre_cliente').focus();
      alertify.error('Completa toda la información de la guía.');
    } else {
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: guias_path+'ajax.php',
        type: 'POST',
        data: 'guardar=110&'+$('#CrearGuia').serialize()+'&'+$('#items').serialize(),
        success: function(resp) {
          if (! resp) {
            $("#extra_content").load(guias_path+'crear.php');
          } else {
            $('#guardar').button('enable').button('option','label','Guardar');
            LOGISTICA.Dialog.open('Error',resp,true);
          }
        }
      });
    }
  });

  $('#peso_contenedor').change(function() {
    if (isNaN($(this).val()) || $.trim($(this).val())=='' ) $(this).val(0);
  });
  $('#codigo_cliente').autocomplete({
    autoFocus:true,
    focus: function() {return false;},
    source:guias_path+'ajax.php?buscarcliente=si&opcion=id',
    select: Guia.set_values
  });
  $('#direccion_cliente').autocomplete({
    autoFocus:true,
    minLength: 3,
    focus: function() {return false;},
    source: guias_path+'ajax.php?buscarcliente=si&opcion=direccion',
    select: Guia.set_values
  });
  $('#nombre_cliente').autocomplete({
    autoFocus:true,
    minLength: 3,
    focus: function() {return false;},
    source: helpers_path+'ajax.php?cliente=1',
    select: Guia.set_values
  });

  $('#ni_cliente').autocomplete({
    autoFocus:true,
    minLength: 2,
    focus: function() {return false;},
    source: guias_path+'ajax.php?buscarcliente=si&opcion=numero_identificacion',
    select: Guia.set_values
  });
  
  $('#numero_sede').autocomplete({
    autoFocus:true,
    minLength: 4,
    focus: function() {return false;},
    source: guias_path+'ajax.php?buscarcliente=si&opcion=numero_sede',
    select: Guia.set_values
  });
  
  $('#sede').autocomplete({
    autoFocus:true,
    minLength: 10,
    focus: function() {return false;},
    source: guias_path+'ajax.php?buscarcliente=si&opcion=sede',
    select: Guia.set_values
  });

  $('#tipo_cobro').change(function() {
    var s=$('#tipo_cobro option:selected').attr('name');
    if (s) {
      $('span.lbl_seguro').text(s);
      $('#porcentaje_seguro').val(s);
      $('#valordeclarado').keyup();
    } else {
      $('span.lbl_seguro').text(0);
    }
  });

  $('#nombre_contacto').autocomplete({
    autoFocus: true,
    minLength: 3,
    source: helpers_path+'ajax.php?contacto=1',
    select: function(event, ui) {
      $('#idcontacto').val(ui.item.id);
      $('#ni_contacto').val(ui.item.numero_identificacion);
      $('#codigo_contacto').val(ui.item.id);
      $('#nombre_contacto').val(ui.item.nombre);
      $('#direccion_contacto').val(ui.item.direccion);
	  $('#telefono_contacto').val(ui.item.telefono);
      $('#ciudad_contacto').val(ui.item.ciudad);
      $('#id_ciudad_contacto').val(ui.item.id_ciudad);
      $('#formapago').focus();
      Guia.cargar_lista_precios(true);
      return false;
    }
  });

  $('#agregar, #agregar2').button({icons:{primary:'ui-icon-circle-plus'}});

  $('#empaque').change(function() {
    var ue = $(this).val();
    if (ue==7 || ue==8 || ue==9) {
      $('#peso_contenedor').removeAttr('disabled').focus();
    } else {
      $('#peso_contenedor').attr('disabled','disabled');
    }
  });

  $('#CrearGuia').validate({
    rules:{
      'guia[peso_contenedor]': {required: true, number: true},
      'guia[idcliente]': 'required',
      'guia[idcontacto]': 'required',
      'guia[valordeclarado]': {required: true, number: true},
      'guia[valorseguro]': {required: true, number: true},
      'guia[formapago]': 'required',
      'guia[observacion]': {required: true, rangelength: [5, 170]}
    },
    messages: {
      'guia[idcontacto]': 'Selecciona el destinatario',
      'guia[idcliente]': 'Selecciona el cliente'
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");}
  });

  //Cobrar usando lista de precios
  $('#AgregarProducto').validate({
    rules: {
      id_producto: 'required',
      tipo_cobro: 'required',
      unidades: {required: true, digits: true, min: 1},
      peso: {required: true, number: true, min: 1},
      ancho: {required: true, number: true, min: 1},
      largo: {required: true, number: true, min: 1},
      alto: {required: true, number: true, min: 1}
    },
    messages: {
      id_producto: 'Selecciona un producto'
    },
    errorPlacement: function(er, el) {
      if (el.attr('id') == 'id_producto' || el.attr('id') == 'tipo_cobro')
        er.appendTo(el.parent("td").next("td"));
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#agregar').button('disable').button('option','label','Agregando...');
      var idembalaje  = $('#tipo_cobro').val(),
      precio          = 0,
      unidades        = $('#unidades').val(),
      peso            = $('#peso').val(),
      alto            = $('#alto').val()/100,
      ancho           = $('#ancho').val()/100,
      largo           = $('#largo').val()/100,
      kilo_vol        = ((alto * ancho * largo) * $('#constante').val()).toFixed();

      var data = {
        liquidar: 1,
        id_cliente: $('#idcliente').val(),
        id_ciudad_destino: $('#id_ciudad_contacto').val(),
        id_ciudad_origen: $('#id_ciudad_cliente').val(),
        id_embalaje: idembalaje,
        unidades: unidades,
        peso: peso,
        valor_declarado: $('#valordeclarado').val(),
        restriccion_peso: $('#restriccion_peso').val()
      };

      $.post(guias_path + 'ajax.php', data)
        .done(function(precio) {
          Guia.add_item($('#nombre_producto').val(), $('#id_producto').val(), unidades, peso, kilo_vol, idembalaje, precio.flete);
        })
        .always(function(response) {
          $('#agregar').button('enable').button('option','label','Agregar');
        });
    }
  });

  /*
   * Agrega un producto a la lista de items
   * permitiendo escribir el valor a cobrar directamente.
   * FLETE AL COBRO
   */
  $('#AgregarProducto2').validate({
    rules: {
      id_producto2: 'required',
      valor: {required: true, number: true},
      unidades2: {required: true, digits: true, min: 1},
      peso2: {required: true, number: true, min: 1},
      ancho2: {required: true, number: true, min: 1},
      largo2: {required: true, number: true, min: 1},
      alto2: {required: true, number: true, min: 1}
    },
    messages: {
      id_producto2: 'Selecciona un producto'
    },
    errorPlacement: function(er, el) {
      if (el.attr('id') == 'id_producto2' || el.attr('id') == 'valor')
        er.appendTo(el.parent("td").next("td"));
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#agregar2').button('disable');
      var alto  = $('#alto2').val()/100,
      ancho     = $('#ancho2').val()/100,
      largo     = $('#largo2').val()/100;
      kilo_vol  = ((alto*ancho*largo)*$('#constante').val()).toFixed(),
      precio    = $('#valor').val();
      Guia.add_item($('#nombre_producto2').val(), $('#id_producto2').val(),$('#unidades2').val(), $('#peso2').val(), kilo_vol, 1, precio);
      $('#agregar2').button('enable');
    }
  });

  $('#valordeclarado').keyup(function() {
    var vd = $(this).val();
    if (isNaN(vd)) { $('#valorseguro').val(0); }
    else{
      var ps = $('#porcentaje_seguro').val();
      $('#valorseguro').val((vd*(ps/100)).toFixed());
    }
  });

  $('#items table').on('click', 'button.borrar', function() {
    $(this).parent().parent().remove();
  });

  $('#formapago').change(function() {
    if ($(this).val() == 'CREDITO') {
      $('#AgregarProducto').show(100, function() {
        $('#AgregarProducto2').hide(200);
      });
      $('#valorseguro').attr({readonly: 'readonly', tabindex: 10});
    } else {
      $('#AgregarProducto2').show(100, function() {
        $('#AgregarProducto').hide(200);
      });
      $('#valorseguro').removeAttr('readonly tabindex');
    }
  });

  $('#nombre_producto, #nombre_producto2').autocomplete({
    autoFocus: true,
    autoFill: true,
    minLength: 3,
    source: helpers_path+'ajax.php?producto=1',
    select: function(event, ui) {
      if ($(this).attr('id')=='nombre_producto') {
        $('#id_producto').val(ui.item.id);
        $('#tipo_cobro').focus();
      } else {
        $('#id_producto2').val(ui.item.id);
        $('#valor').focus();
      }
    }
  });

  $('#crear_contacto, #crear_cliente').click(function() {
    var t = 'Crear nuevo ', archivo = clientes_path+'crear.php';
    t += this.id == 'crear_contacto' ? 'contacto' : 'cliente';
    LOGISTICA.Dialog.open(t, archivo+'?dialog=1');
  });
  $('#editar_cliente, #editar_contacto').click(function() {
    var t, archivo = clientes_path+'editar.php?id=';
    if (this.id == 'editar_contacto') {
      if ($('#idcontacto').val() == '') {
        $('#idcontacto').focus();
        return;
      }
      t = 'Editar Contacto';
      archivo += $('#idcontacto').val();
    } else {
      if ($('#idcliente').val() == '') {
        $('#idcliente').focus();
        return;
      }
      t = 'Editar Cliente';
      archivo += $('#idcliente').val();
    }
    LOGISTICA.Dialog.open(t, archivo+'&dialog=1');
  });
}());
</script>
