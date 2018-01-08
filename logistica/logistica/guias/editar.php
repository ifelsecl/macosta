<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][GUIAS_EDITAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
$formato_fecha = '%b %d, %Y';
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY], $_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}

$guia = new Guia;
if (! $guia->find($_GET['id'])) exit('No existe la guía.');
$configuracion = new Configuracion;

$carpeta_guias = '../../../'.$configuracion->app_ruta_guias;
if (! file_exists($carpeta_guias)) mkdir($carpeta_guias);
$guia_escaneada = $carpeta_guias.$guia->id.'_1.pdf';
?>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Editar Guía <?= $guia->id ?></h2>
<hr class="hr-small">
<form id="EditarGuia" name="EditarGuia" method="post">
  <?php
  if ($guia->idfactura) {
    echo '<div class="text-center ui-widget-header ui-corner-all">¡Guía Facturada!</div>';
    echo '<div class="text-center ui-widget-content ui-corner-all">Esta guía ya ha sido facturada.<br />';
    echo 'El número de la factura es <b>'.$guia->idfactura.'</b></div>';
    echo '<div class="text-center"><label><input type="checkbox" name="idfactura" id="idfactura" value="vacio" />Quitar de la factura</label></div>';
  }
  ?>
  <table>
    <tr>
      <td><b>Recibido:</b></td>
      <td>
        <?= strftime('%b %d, %Y',strtotime($guia->fecha_recibido_mercancia)) ?>
      </td>
      <td width="20"></td>
      <td>
        <b>Despachado:</b>
      </td>
      <td>
        <?php if ($guia->fechadespacho) echo strftime('%b %d, %Y',strtotime($guia->fechadespacho)) ?>
      </td>
    </tr>
  </table>
  <table cellpadding="0">
    <tr>
      <td>
        <b>Unidad de medida:</b><br>
        <select id="unidadmedida" name="unidadmedida">
          <?php
          foreach (Guia::$unidades_medida as $key => $value) {
            $sel = $guia->unidadmedida == $key ? 'selected="selected"' :'';
            echo '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td>
        <b>Naturaleza de la carga:</b><br>
        <select id="naturaleza" name="naturaleza">
          <?php
          foreach (Guia::$naturalezas_carga as $key => $value) {
            $s = $key == $guia->naturaleza ? 'selected="selected"' : '';
            echo '<option value="'.$key.'" '.$s.'>'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td>
        <b>Propietario de la Carga:</b><br>
        <select class="input-medium" name="propietario" id="propietario">
          <?php
          foreach (Guia::$propietarios_carga as $p) {
            $s = $p == $guia->propietario ? 'selected="selected"' : '' ;
            echo '<option '.$s.'>'.$p.'</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td>
        <b>Unidad de empaque:</b><br>
        <select id="empaque" name="empaque">
          <?php
          foreach (Guia::$unidades_empaque as $key => $value) {
            $s = $key == $guia->empaque ? 'selected="selected"' : '';
            echo '<option value="'.$key.'" '.$s.'>'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td>
        <b>Recogida</b><br>
        <select class="input-small" name="recogida" id="recogida">
          <option value="no" <?php if ($guia->recogida=='no') echo 'selected="selected"' ?>>NO</option>
          <option value="si" <?php if ($guia->recogida=='si') echo 'selected="selected"' ?>>SI</option>
        </select>
      </td>
    </tr>
  </table>
  <table cellpadding="0">
    <tr>
      <td><b>Remitente</b></td>
      <td>
        <?php $guia->cliente() ?>
        <input type="text" name="nombre_cliente" id="nombre_cliente" value="<?= $guia->cliente->nombre_completo ?>" />
        <input type="hidden" name="idcliente" id="idcliente" value="<?= $guia->idcliente ?>" />
        <input type="hidden" name="id_ciudad_cliente" id="id_ciudad_cliente" value="<?= $guia->cliente->idciudad ?>" />
		
      </td>
    </tr>
    <tr>
      <td><b>Destinatario</b></td>
      <td>
        <?php $guia->contacto() ?>
        <input type="text" name="nombre_contacto" id="nombre_contacto" value="<?= $guia->contacto->nombre_completo ?>" />
        <input type="hidden" name="idcontacto" id="idcontacto" value="<?= $guia->idcontacto ?>" />
        <input type="hidden" name="id_ciudad_contacto" id="id_ciudad_contacto" value="<?= $guia->contacto->idciudad ?>" />
      </td>
    </tr>
    <tr>
      <td><b>No. documento:</b></td>
      <td><input type="text" class="input-medium" name="documentocliente" id="documentocliente" value="<?= $guia->documentocliente ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Fecha Recibido:</b></td>
      <td><input type="text" class="fecha input-small" name="fecha_recibido_mercancia" id="fecha_recibido_mercancia" value="<?php if ($guia->fecha_recibido_mercancia) echo $guia->fecha_recibido_mercancia ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Fecha Entrega:</b></td>
      <td><input type="text" class="fecha input-small" name="fechaentrega" id="fechaentrega" value="<?php if ($guia->fechaentrega) echo $guia->fechaentrega ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Número anterior:</b></td>
      <td><input type="text" class="input-medium" name="numero" id="numero" value="<?= $guia->numero ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Forma de pago:</b></td>
      <td>
        <select class="input-medium" id="formapago" name="formapago">
          <option value="">Selecciona...</option>
          <?php
          foreach (Guia::$formas_pago as $fp) {
            $s = $fp == $guia->formapago ? 'selected="selected"' : '';
            echo '<option value="'.$fp.'" '.$s.'>'.$fp.'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Valor declarado:</b></td>
      <td><input type="text" name="valordeclarado" id="valordeclarado" value="<?= $guia->valordeclarado ?>" /></td>
      <td></td>
    </tr>
      <td><b>Seguro:</b></td>
      <td>
        <div class="input-prepend">
          <span class="add-on"><span class="lbl_seguro">-</span>%</span>
          <input type="text" class="input-small" name="valorseguro" id="valorseguro" value="<?= $guia->valorseguro ?>" />
        </div>
      </td>
      <td></td>
    </tr>
    <tr>
      <td valign="top"><b>Observación:</b></td>
      <td>
        <textarea id="observacion" name="observacion" cols="32" rows="3"><?= $guia->observacion ?></textarea>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Estado:</b></td>
      <td>
        <select id="idestado" name="idestado">
          <option value="">Selecciona...</option>
          <?php
          foreach(Guia::$estados as $key => $value) {
            if ($key != 6 ) {
              $s = $guia->idestado == $key ? 'selected="selected"' : '';
              echo '<option value="'.$key.'" '.$s.'>'.$value.'</option>';
            }
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr class="devuelta" <?php if ($guia->idestado != 5 and $guia->idestado != 4) echo 'style="display:none;"' ?>>
      <td><b>Selecciona una razón:</b></td>
      <td>
        <select name="id_razon_devolucion" id="id_razon_devolucion" <?php if ($guia->idestado != 5) echo 'disabled="disabled"' ?>>
          <option value="">Selecciona...</option>
          <?php
          $guia->razones_devolucion();
          foreach ($guia->razones_devolucion as $razon) {
            $s = $guia->id_razon_devolucion == $razon->id ? 'selected="selected"' : '';
            echo '<option value="'.$razon->id.'" '.$s.'>'.$razon->nombre.'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <?php
    if (file_exists($guia_escaneada)) { ?>
    <tr style="height: 30px;" class="entregada" >
      <td><b>Guia Escaneada PDF:</b></td>
      <td>
        <a target="_blank" title="Descargar guia escaneada (PDF)" href="<?= $guia_escaneada ?>">Descargar archivo</a>
        <br>
      </td>
    </tr>
    <?php
    }
    ?>
    <tr class="entregada" <?php if ($guia->idestado != 5 and $guia->idestado != 4) echo 'style="display:none;"' ?>>
      <td><b>Subir nuevo archivo PDF:</b></td>
      <td>
        <input type="file" id="imagen" name="imagen" class="ui-corner-all" <?php if ($guia->idestado!=4) echo 'disabled="disabled"' ?> />
        <small>El archivo será renombrado automaticamente a <br>'<?= $_REQUEST['id'] ?>_1.pdf'</small> <?php if (file_exists($guia_escaneada)) echo '<small>y reemplazará el archivo existente.</small>' ?>
      </td>
    </tr>
  </table>
  <?= nonce_create_form_input($guia->id) ?>
  <input type="hidden" name="id" id="id" value="<?= $guia->id ?>" />
</form>
<hr class="hr-small">
<table class="agregar" <?php if ($guia->idestado != 1) echo 'style="display: none;"' ?>>
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
          <tr class="precio_producto" style="display: none">
            <td><b>Precio:</b></td>
            <td><input disabled="disabled" type="text" id="precio" name="precio" /></td>
            <td></td>
          </tr>
        </table>
        <table cellpadding="1">
          <tr>
            <td>
              <b>Unidades:</b><br>
              <input class="input-mini" maxlength="6" id="unidades" name="unidades" value="" type="text" />
            </td>
            <td>
              <b>Peso (Kg):</b><br>
              <input class="input-mini" maxlength="10" id="peso" value="" type="text" name="peso" />
            </td>
            <td>
              <b>Ancho (cm)</b><br>
              <input class="input-mini" maxlength="4" type="text" value="1" id="ancho" name="ancho" />
            </td>
            <td>
              <b>Largo (cm)</b><br>
              <input class="input-mini" maxlength="4" type="text" value="1" id="largo" name="largo" />
            </td>
            <td>
              <b>Alto (cm)</b><br>
              <input class="input-mini" maxlength="4" type="text" value="1" id="alto" name="alto" />
            </td>
          </tr>
        </table>
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
              <input type="text" name="nombre_producto2" id="nombre_producto2"  />
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
            <td>
              <b>Unidades:</b><br>
              <input class="input-mini" maxlength="10" id="unidades2" name="unidades2" value="" type="text" />
            </td>
            <td>
              <b>Peso (Kg):</b><br>
              <input class="input-mini" maxlength="10" id="peso2" value="" type="text" name="peso2" />
            </td>
            <td>
              <b>Ancho (cm)</b><br>
              <input class="input-mini" maxlength="4" type="text" value="1" id="ancho2" name="ancho2" />
            </td>
            <td>
              <b>Largo (cm)</b><br>
              <input class="input-mini" maxlength="4" type="text" value="1" id="largo2" name="largo2" />
            </td>
            <td>
              <b>Alto (cm)</b><br>
              <input class="input-mini" maxlength="4" type="text" value="1" id="alto2" name="alto2" />
            </td>
          </tr>
        </table>
        <center><button type="submit" id="agregar2">Agregar</button></center>
      </form>
    </td>
  </tr>
</table>
<form id="items" name="items" action="#" method="post">
  <table class="table table-bordered table-condensed table-striped">
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
      <?php
      $i = 1;
      $guia->items();
      foreach ($guia->items as $item) {
        echo '<tr>';
        echo '<td title="'.$item->producto.'">';
        echo substr($item->producto, 0, 30);
        echo '<input type="hidden" name="items['.$i.'][idproducto]" value="'.$item->idproducto.'" />';
        echo '<input type="hidden" name="items['.$i.'][idembalaje]" value="'.$item->idembalaje.'" /></td>';
        if (isset($_SESSION['permisos'][GUIAS_EDITAR_PRECIO_ITEMS])) {
          echo '<td><input class="input-mini" type="text" name="items['.$i.'][unidades]" value="'.$item->unidades.'" /></td>';
          echo '<td><input class="input-mini" type="text" name="items['.$i.'][peso]" value="'.$item->peso.'" /></td>';
          echo '<td><input class="input-mini" type="text" name="items['.$i.'][kilo_vol]" value="'.$item->kilo_vol.'" />';
          echo '<td><input class="input-mini" type="text" name="items['.$i.'][valor]" value="'.$item->valor.'" /></td>';
        } else {
          echo '<td>'.$item->unidades.'<input type="hidden" name="items['.$i.'][unidades]" value="'.$item->unidades.'" /></td>';
          echo '<td>'.$item->peso.'<input type="hidden" name="items['.$i.'][peso]" value="'.$item->peso.'" /></td>';
          echo '<td>'.$item->kilo_vol.'<input type="hidden" name="items['.$i.'][kilo_vol]" value="'.$item->kilo_vol.'" />';
          echo '<td>'.$item->valor.'<input type="hidden" name="items['.$i.'][valor]" value="'.$item->valor.'" /></td>';
        }
        echo  '<td><button type="button" class="btn borrar btn-danger btn-mini"><i class="icon-remove"></i></button></td>';
        echo '</tr>';
        $i += 1;
      }
      ?>
    </tbody>
  </table>
</form>
<input type="hidden" id="restriccion_peso" value="<?= $guia->cliente->restriccionpeso ?>" />
<input type="hidden" id="constante" value="<?= $configuracion->calKiloVolumen ?>" />
<input type="hidden" name="porcentaje_seguro" id="porcentaje_seguro" readonly="readonly" value="<?= $guia->cliente->porcentajeseguro ?>" />
<center class="form-actions"><button id="guardar">Guardar</button></center>
<script>
(function() {
  $('#cliente').focus();
  $btn_guardar = $('#guardar');

  var i = <?= count($guia->items())+1 ?>;

  function Guardar() {
    var data='editar=110&'+$('#EditarGuia').serialize()+'&'+$('#items').serialize();
    $btn_guardar.button('disable').button('option','label','Guardando...');
    $.ajax({
      url: guias_path+'ajax.php', type: 'POST', data: data,
      success: function(resp) {
        if (! resp) {
          regresar();
        } else {
          $btn_guardar.button('enable').button('option','label','Guardar');
          LOGISTICA.Dialog.open('Error', resp, true);
        }
      }
    });
  }

  $('#nombre_cliente').autocomplete({
    autoFocus: true, minLength: 3,
    focus: function() {return false;},
    source: helpers_path+'ajax.php?cliente=1',
    select: function(event, ui) {
      $('#idcliente').val(ui.item.id);
      $('#codigo_cliente').val(ui.item.id);
      $('#direccion_cliente').val(ui.item.direccion);
	  $('#numero_sede').val(ui.item.numerosede);
	  $('#sede').val(ui.item.sede);
      $('#telefono_cliente').val(ui.item.telefono);
      $('#porcentaje_seguro').val(ui.item.porcentajeseguro);
      $('#id_ciudad_cliente').val(ui.item.idciudad);
      $('#restriccion_peso').val(ui.item.restriccionpeso);
      $('.lbl_seguro').text(ui.item.porcentajeseguro);
      $('#nombre_contacto').focus();
      cargar_lista_precios(false);
    }
  });

  $('.lbl_seguro').text($('#porcentaje_seguro').val());

  $('#valordeclarado').keyup(function() {
    var vd = $(this).val();
    if (isNaN(vd)) {
      $('#valorseguro').val(0);
    } else {
      var ps = $('#porcentaje_seguro').val();
      $('#valorseguro').val((vd*(ps/100)).toFixed());
    }
  });

  /**
   * Obtiene los embalajes asignados en la lista de precios del
   * cliente origen al destino.
   * @param alert true indica que se debe mostrar un mensaje si no
   *      se encuentran embalajes
   */
  function cargar_lista_precios(alerta) {
    $.ajax({
      url: guias_path+'ajax.php', type: 'POST',
      data:'buscarembalaje=si&id_cliente='+$('#idcliente').val()+'&id_ciudad_cliente='+$('#id_ciudad_cliente').val()+'&id_ciudad_contacto='+$('#id_ciudad_contacto').val(),
      success: function(msj) {
        if (msj == 'no') {
          $('#nombre_contacto').focus();
          if (alerta)
            alertify.log('El cliente no tiene precios para la ciudad de este contacto.\r\nPuedes usar como forma de pago FLETE AL COBRO o CONTADO para cobrar directamente sin una lista de precios.', "", 10);
        } else {
          $('#tipo_cobro').html(msj);
        }
      }
    });
  }
  cargar_lista_precios(false);

  $('#nombre_contacto').autocomplete({
    autoFocus:true,
    minLength: 3,
    source: helpers_path+'ajax.php?contacto=1',
    select: function(event, ui) {
      $('#idcontacto').val(ui.item.id);
      $('#direccion_contacto').val(ui.item.direccion);
	  $('#telefono_contacto').val(ui.item.telefono);
      $('#ciudad_contacto').val(ui.item.ciudad);
      $('#id_ciudad_contacto').val(ui.item.id_ciudad);
      $('#documentocliente').focus();
      cargar_lista_precios(false);
    }
  });

  $('#agregar, #agregar2').button({icons: {primary: 'ui-icon-circle-plus'}});

  $btn_guardar
    .button({icons: {primary: 'ui-icon-circle-check'}})
    .click(function(event) {
      event.preventDefault();
      if (! $('#EditarGuia').validate().form()) {
        alert('Debes completar todos los campos de la guía.');
      } else {
        $idestado = $('#idestado');
        if ($idestado.val() == 4 || $idestado.val() == 5) {
          if ($('.uploadifyQueueItem').attr("id")) {
            $btn_guardar.button('disable').button('option','label','Subiendo archivo...');
            $('#imagen').uploadifyUpload();
          } else {
            Guardar();
          }
        } else {
          Guardar();
        }
      }
    });
  $(".fecha").datepicker({
    autoSize:true,
    showOn: "both",
    buttonImage: "css/images/calendar.gif",
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    buttonText: 'Seleccionar...',
    maxDate: 0
  });
  $('#formapago').change(function() {
    if ($(this).val() == 'CREDITO') {
      $('#AgregarProducto').show(100, function() {
        $('#AgregarProducto2').hide(200);
      });
    } else {
      $('#AgregarProducto2').show(100, function() {
        $('#AgregarProducto').hide(200);
      });
    }
  }).change();

  $('#idestado').change(function() {
    var e = $(this).val();
    if (e == 4) {//Entregada
      $('.agregar').fadeOut(500);
      $('#imagen,#id_razon_devolucion').removeAttr('disabled');
      $('.devuelta,.entregada').fadeIn(800);
    }else if (e == 5) {//Devuelta
      $('.agregar').fadeOut(500);
      $('.entregada,.devuelta').fadeIn(800);
      $('#imagen,#id_razon_devolucion').removeAttr('disabled');
    }else if (e == 1) {//bodega
      $('.agregar').fadeIn(500);
      $('.entregada,.devuelta').fadeOut(800);
      $('#imagen,#id_razon_devolucion').attr('disabled','disabled');
    } else {
      $('.entregada,.devuelta').fadeOut(800);
      $('#id_razon_devolucion,#imagen').attr('disabled','disabled');
    }
  }).change();

  $('#EditarGuia').validate({
    rules: {
      formapago: 'required',
      valordeclarado: {required: true, number: true},
      valorseguro: {required: true, number: true},
      fechaentrega: {required: function(element) {
        $idestado = $('#idestado');
        return $idestado.val()==4 || $idestado.val()==5;
      }},
      observacion: {required: true, rangelength: [5, 120]},
      id_razon_devolucion: {required: true},
      idestado: 'required'
    },
    errorPlacement: function(error, element) {error.appendTo( element.parent("td").next("td") );},
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");}
  });

  /* Usando lista de precios */
  $('#AgregarProducto').validate({
    rules: {
      id_producto: {required: true},
      tipo_cobro: {required: true},
      unidades: {required: true, digits: true, min: 1},
      peso: {required: true, number: true, min: 1},
      precio: {required: true, number: true, min: 100},
      ancho: {required: true, number: true, min: 1},
      largo: {required: true, number: true, min: 1},
      alto: {required: true, number: true, min: 1}
    },
    messages: {
      id_producto: {required: 'Selecciona el producto.'},
    },
    errorPlacement: function(error, element) {
      if (element.attr('id') == 'id_producto' || element.attr('id') == 'tipo_cobro')
        error.appendTo(element.parent("td").next("td"));
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
          NuevaFila($('#nombre_producto').val(), $('#id_producto').val(), unidades, peso, kilo_vol, idembalaje, precio.flete);
        })
        .always(function(response) {
          $('#agregar').button('enable').button('option','label','Agregar');
        });
    }
  });

  function NuevaFila(producto, codigo_producto, unidades, peso, kilo_vol, tipo_cobro, precio) {
    var fila='<tr>';
    fila+='<td title="'+producto+'">'+producto.substr(0,35)+'<input type="hidden" name="items['+i+'][idproducto]" value="'+codigo_producto+'" /><input type="hidden" name="items['+i+'][idembalaje]" value="'+tipo_cobro+'" /></td>';
    <?php if (isset($_SESSION['permisos'][GUIAS_EDITAR_PRECIO_ITEMS])) { ?>
      fila+='<td><input class="input-mini" type="text" name="items['+i+'][unidades]" value="'+unidades+'" /></td>';
      fila+='<td><input class="input-mini" type="text" name="items['+i+'][peso]" value="'+peso+'" /></td>';
      fila+='<td><input class="input-mini" type="text" name="items['+i+'][kilo_vol]" value="'+kilo_vol+'" /></td>';
      fila+='<td><input class="input-mini" type="text" name="items['+i+'][valor]" value="'+precio.toString()+'" /></td>';
    <?php } else { ?>
      fila+='<td>'+unidades+'<input type="hidden" name="items['+i+'][unidades]" value="'+unidades+'" /></td>';
      fila+='<td>'+peso+'<input type="hidden" name="items['+i+'][peso]" value="'+peso+'" /></td>';
      fila+='<td>'+kilo_vol+'<input type="hidden" name="items['+i+'][kilo_vol]" value="'+kilo_vol+'" /></td>';
      fila+='<td><input class="input-mini" type="hidden" name="items['+i+'][valor]" value="'+precio.toString()+'" />'+precio.toString()+'</td>';
    <?php } ?>
    fila+='<td><button type="button" class="btn borrar btn-danger btn-mini"><i class="icon-remove"></i></button></td>';
    fila+='</tr>';
    $("#items table tbody").append(fila);
    i+=1;
  }

  $('#items table').on('click', 'button.borrar', function() {
    $(this).parent().parent().remove();
  });

  /*
   * Agrega un producto a la lista de items
   * permitiendo escribir el valor a cobrar directamente.
   */
  $('#AgregarProducto2').validate({
    rules: {
      id_producto2: {required: true},
      valor: {required: true, number: true},
      unidades2: {required: true, digits: true, min: 1},
      peso2: {required: true, number: true, min: 1},
      ancho2: {required: true, number: true, min: 1},
      largo2: {required: true, number: true, min: 1},
      alto2: {required: true, number: true, min: 1}
    },
    messages: {
      id_producto2: {required: 'Selecciona el producto.'}
    },
    errorPlacement: function(error, element) {
      var id = element.attr('id');
      if (id == 'id_producto2' || id == 'valor')
        error.appendTo(element.parent("td").next("td"));
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#agregar2').button('disable');
      var alto=$('#alto2').val()/100;
      var ancho=$('#ancho2').val()/100;
      var largo=$('#largo2').val()/100;
      var kilo_vol=((alto*ancho*largo)*$('#constante').val()).toFixed();
      var precio=$('#valor').val();
      var producto=$('#nombre_producto2').val();
      var codigo_producto=$('#id_producto2').val();
      var unidades=$('#unidades2').val();
      var peso=$('#peso2').val();
      var tipo_cobro=1;
      NuevaFila(producto, codigo_producto, unidades, peso, kilo_vol, tipo_cobro, precio);
      $('#agregar2').button('enable');
    }
  });

  $('#tipo_cobro').change(function() {
    var s=$('#tipo_cobro option:selected').attr('name');
    if (s) {
      $('.lbl_seguro').text(s);
      $('#porcentaje_seguro').val(s);
    } else {
      $('.lbl_seguro').text(0);
    }
  });

  function Mayor(u, k, v) {
    if (u==k && u==v) return parseFloat(u);
    if (u>=k && u>=v) return parseFloat(u);
    if (k>=u && k>=v) return parseFloat(k);
    if (v>=u && v>=k) return parseFloat(v);
  }

  $('#nombre_producto, #nombre_producto2').autocomplete({
    autoFocus: true,
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

  $('#imagen').uploadify({
    'uploader'  : 'js/uploadify.swf',
    'script'  : guias_path+'subir.php',
    'cancelImg' : 'css/images/cancel.png',
    'folder'  : 'guias_escaneadas',
    'auto'    : false,
    'buttonText': 'Seleccionar...',
    'fileExt' : '*.pdf',
    'fileDesc'  : 'Archivos PDF (.PDF)',
    'width'   : 200,
    'scriptData': {'id': '<?= $guia->id ?>'},
    'onComplete': function(event, ID, fileObj, response, data) {
      if (response!="ok") {
        alert(response+"=>El archivo no se ha podido guardar, la guia se guardara sin el archivo adjunto, solo tienes que copiar el archivo en la carpeta de guias escaneadas.");
      }
      Guardar();
    },
    'onError' : function (event,ID,fileObj,errorObj) {
      alert(errorObj.type + ' Error: ' + errorObj.info);
    }
  });
}());
</script>
