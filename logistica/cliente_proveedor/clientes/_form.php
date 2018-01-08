<?php
$vendedores = Vendedor::all();
?>
<?php if (! isset($_GET['dialog'])) { ?>
<button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2><?= (isset($cliente->id)) ? 'Editar Cliente '.$cliente->id : 'Nuevo Cliente' ?></h2>
<hr class="hr-small">
<?php } ?>
<form id="form_save_cliente" name="form_save_cliente" method="post" action="#" class="no-margin">
  <table cellpadding="0">
    <tr>
      <td><b>Tipo de identificación</b></td>
      <td>
        <select id="tipo_identificacion" name="tipo_identificacion">
          <?php
          foreach (Cliente::$tipos_identificacion as $value => $text) {
            $s = (isset($cliente->tipo_identificacion) and $value == $cliente->tipo_identificacion) ? 'selected="selected"' : '';
            echo '<option value="'.$value.'" '.$s.'>'.$text.'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Número de identificación</b></td>
      <td><input type="text" id="numero_identificacion" name="numero_identificacion" autofocus value="<?= isset($cliente->numero_identificacion) ? $cliente->numero_identificacion : '' ?>" /></td>
      <td></td>
    </tr>
    <tr class="empresa">
      <td><b>Dígito Verificación</b></td>
      <td><input type="text" class="input-mini" name="digito_verificacion" id="digito_verificacion" maxlength="1" value="<?= isset($cliente->digito_verificacion) ? $cliente->digito_verificacion : '' ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Nombre:</b></td>
      <td><input type="text" name="nombre" id="nombre" maxlength="100" value="<?= isset($cliente->nombre) ? $cliente->nombre : '' ?>" /></td>
      <td></td>
    </tr>
    <tr class="persona">
      <td><b>Primer Apellido:</b></td>
      <td><input type="text" name="primer_apellido" id="primer_apellido" maxlength="100" value="<?= isset($cliente->primer_apellido) ? $cliente->primer_apellido : '' ?>" /></td>
      <td></td>
    </tr>
    <tr class="persona">
      <td><b>Segundo Apellido:</b> (Opcional)</td>
      <td><input type="text" name="segundo_apellido" id="segundo_apellido" maxlength="100" value="<?= isset($cliente->segundo_apellido) ? $cliente->segundo_apellido : '' ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Teléfonos</b></td>
      <td>
        <input type="text" class="input-small" placeholder="Principal" maxlength="7" name="telefono" id="telefono" value="<?= isset($cliente->telefono) ? $cliente->telefono : '' ?>" />
        <input type="text" class="input-small" placeholder="Ext o Teléfono 2" name="telefono2" id="telefono2" value="<?= isset($cliente->telefono2) ? $cliente->telefono2 : '' ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Celular:</b></td>
      <td>
        <input type="text" class="input-small" placeholder="Celular" maxlength="10" name="celular" id="celular" value="<?= isset($cliente->celular) ? $cliente->celular : '' ?>" />
      </td>
      <td></td>
    </tr>
	<tr class="empresa">
      <td><b>Número Sede:</b></td>
      <td><input type="text" placeholder="Número sede" name="numero_sede" id="numero_sede" maxlength="100" value="<?= isset($cliente->numero_sede) ? $cliente->numero_sede : '' ?>" /></td>
      <td></td>
    </tr>
	<tr class="empresa">
      <td><b>Sede:</b></td>
      <td><input type="text" placeholder="Sede" name="sede" id="sede" maxlength="100" value="<?= isset($cliente->sede) ? $cliente->sede : '' ?>" /></td>
      <td></td>
    </tr>
    <tr>
    <tr>
      <td><b>Dirección</b></td>
      <td><input type="text" name="direccion" id="direccion" maxlength="100" value="<?= isset($cliente->direccion) ? $cliente->direccion : '' ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Ciudad</b></td>
      <td>
        <input type="text" id="nombre_ciudad" value="<?= isset($cliente->idciudad) ? $cliente->ciudad_nombre : '' ?>" />
        <input type="hidden" name="idciudad" id="idciudad" value="<?= isset($cliente->idciudad) ? $cliente->idciudad : '' ?>" />
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Email</b></td>
      <td><input type="text" name="email" id="email" value="<?= isset($cliente->email) ? $cliente->email : '' ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Sitio Web</b></td>
      <td><input type="text" name="sitioweb" id="sitioweb" value="<?= isset($cliente->sitioweb) ? $cliente->sitioweb : '' ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>¿Cliente Credito?:</b></td>
      <td>
        <select name="credito" id="credito">
          <option <?php if (isset($cliente->credito) and $cliente->credito =='SI') echo 'selected="selected"' ?>>SI</option>
          <option <?php if (isset($cliente->credito) and $cliente->credito =='NO') echo 'selected="selected"' ?>>NO</option>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Nueva Liquidación</b></td>
      <td>
        <select name="nl" id="nl">
          <option value="si" <?php if (isset($cliente->nl) and $cliente->nl == 'si') echo 'selected="selected"' ?>>SI</option>
          <option value="no" <?php if (isset($cliente->nl) and $cliente->nl == 'no') echo 'selected="selected"' ?>>NO</option>
        </select>
      </td>
    </tr>
    <tr class="empresa">
      <td><b>Forma Jurídica</b></td>
      <td>
        <select name="idformajuridica" id="idformajuridica">
          <option value="">Selecciona...</option>
          <?php
          foreach (Cliente::$formas_juridicas as $id => $value) {
            $s = (isset($cliente->idformajuridica) and $id == $cliente->idformajuridica) ? 'selected="selected"' : '';
            echo '<option value="'.$id.'" '.$s.'>'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr class="empresa">
      <td><b>Régimen:</b></td>
      <td>
        <select name="idregimen" id="idregimen">
          <option value="">Selecciona...</option>
          <?php
          foreach (Cliente::$regimenes as $id => $value) {
            $s = (isset($cliente->idregimen) and $id == $cliente->idregimen) ? 'selected="selected"' : '';
            echo '<option value="'.$id.'" '.$s.'>'.$value.'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Restricción de peso (Kg):</b></td>
      <td><input type="text" class="input-mini" maxlength="5" id="restriccionpeso" name="restriccionpeso" value="<?= isset($cliente->restriccionpeso) ? $cliente->restriccionpeso : '30' ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Seguro (%):</b></td>
      <td><input type="text" class="input-mini" maxlength="5" id="porcentajeseguro" name="porcentajeseguro" value="<?= isset($cliente->porcentajeseguro) ? $cliente->porcentajeseguro : '1' ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Condición de Pago:</b></td>
      <td>
        <div class="input-append">
          <input type="text" class="input-mini" maxlength="5" id="condicion_pago" name="condicion_pago" value="<?= isset($cliente->condicion_pago) ? $cliente->condicion_pago : '15' ?>" />
          <span class="add-on">días</span>
        </div>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Descuento (%):</b></td>
      <td><input type="text" class="input-mini" maxlength="5" id="descuento" name="descuento" value="<?= isset($cliente->descuento) ? $cliente->descuento : '0' ?>" /></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Vendedor:</b></td>
      <td>
        <?php
        if (empty($vendedores)) {
          echo 'No hay vendedores';
        } else {
          echo '<select name="id_vendedor">';
          echo '<option value="">Selecciona...</option>';
          foreach ($vendedores as $v) {
            $s = (isset($cliente->id_vendedor) and $v->id == $cliente->id_vendedor) ? 'selected="selected"' : '';
            echo '<option value="'.$v->id.'" '.$s.'>'.$v->codigo_siigo.'-'.$v->nombre.' - '.$v->ciudad.'</option>';
          }
          echo '</select>';
        }
        ?>
      </td>
      <td></td>
    </tr>
  </table>
  <div class="form-actions text-center"><button type="submit" id="cliente_guardar">Guardar</button></div>
  <input type="hidden" id="id" name="id" value="<?= isset($cliente->id) ? $cliente->id : '' ?>" />
  <input type="hidden" id="seguro_anterior" name="seguro_anterior" value="<?= isset($cliente->porcentajeseguro) ? $cliente->porcentajeseguro : '' ?>" />
  <?php nonce_create_form_input(isset($cliente->id) ? $cliente->id : '') ?>
</form>
<script>
(function() {
  var $tipo_identificacion = $('#tipo_identificacion'),
      $celular             = $('#celular');
  $("#cliente_guardar").button({icons:{primary: 'ui-icon-circle-check'}});
  $('#nombre_ciudad').autocomplete({
    autoFocus: true,
    minLength: 3,
    source: helpers_path+"ajax.php?ciudad=si",
    select: function(event, ui) {
      $('#idciudad').val(ui.item.id);
      $('#email').focus();
    }
  });

  <?php Cliente::validar_tipo_identificacion() ?>

  $('#numero_identificacion').keyup(function() {
    $('#digito_verificacion').val( calcular_dv($(this).val()) );
  });

  $('#form_save_cliente').validate({
    rules: {
      tipo_identificacion: {required: true, length: 1},
      numero_identificacion: {required: true, digits: true,
        maxlength: function() { return 'N' == $tipo_identificacion.val() ? 9 : 15 ; }
      },
      digito_verificacion: {required: true, digits: true, length: 1},
      nombre: {required: true, maxlength: 100},
      primer_apellido: {required: true, maxlength: 100},
      telefono: {
        required: function() { return ('N' == $tipo_identificacion.val() || !$celular.val()) },
        digits: true,
        length: 7
      },
      celular: {
        required: function() { return !$("#telefono").val() },
        digits: true,
        length: 10
      },
      direccion: {required: true, maxlength: 60},
	  numero_sede: {required: true, digits: true},
	  sede: {required: true, maxlength: 10},
      idciudad: {required: true, digits: true},
      email: {email: true},
      sitioweb: {url: true},
      idformajuridica: 'required',
      idregimen: 'required',
      restriccionpeso: {required: true,  number: true},
      porcentajeseguro: {required: true, number: true},
      condicion_pago: {required: true, digits: true},
      descuento: {required: true, number: true},
      id_vendedor: 'required'
    },
    messages: {
      telefono: {
        required: function() {
          if ('N' == $tipo_identificacion.val()) {
            return 'Este campo es obligatorio.';
          } else {
            return 'Escriba el teléfono fijo, celular o ambos.';
          }
        }
      },
      celular: {required: 'Escribe el teléfono fijo, celular o ambos.'},
      email: {email: 'Escribe un email válido, ej: info@example.com.'},
      sitioweb: {url: 'Escribe una URL válida, ej: http://www.example.com.'}
    },
    errorPlacement: function(error, element) {
      error.appendTo( element.parent("td").next("td") );
    },
    highlight: function(input) {$(input).addClass("ui-state-highlight");},
    unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
    submitHandler: function(form) {
      $('#cliente_guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: clientes_path+'ajax.php',
        type: "POST",
        data: 'save=1&'+$(form).serialize(),
        success: function(msj) {
          if (! msj) {
            alertify.success('Cliente guardado correctamente');
            <?php if (isset($_GET['dialog'])) { ?>
            cerrarDialogo();
            <?php } else { ?>
            regresar();
            <?php } ?>
          } else {
            $('#cliente_guardar').button('enable').button('option','label','Guardar');
            alertify.error(msj);
          }
        }
      });
    }
  });
}());
</script>
