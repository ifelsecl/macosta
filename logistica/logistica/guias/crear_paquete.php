<?php require '../../seguridad.php' ?>
<button class="btn btn-success pull-right" onclick="regresar();">Regresar</button>
<h2>Nuevo Paquete</h2>
<hr class="hr-small">
<div id="container">
	<form id="paquete" style="margin: 0;">
		<table>
			<tbody>
				<tr>
					<td>
						<b>Remitente</b><br>
						<input type="text" id="paquete_cliente_nombre">
						<input type="hidden" name="paquete[id_cliente]" id="paquete_cliente_id">
					</td>
					<td>
						<b>Destino</b><br>
						<input type="text" id="paquete_ciudad_nombre">
						<input type="hidden" name="paquete[id_ciudad]" id="paquete_ciudad_id">
					</td>
				</tr>
				<tr>
					<td>
						<b>Tipo Cobro</b>&nbsp;<small id="error" class="text-error" style="display: none">(Debe ser Viaje Convenido)</small><br>
						<select id="paquete_tipo_cobro" name="paquete[id_embalaje]">
							<option value="">Selecciona...</option>
						</select>
					</td>
					<td>
						<b>Total</b><br>
						<input type="text" disabled="disabled" class="input-small" id="paquete_total" name="paquete[total]">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<hr class="hr-small">
	<form id="paquete_add_contacto">
		<table>
			<tbody>
				<tr>
					<td>
						<b>Contacto</b><br>
						<input type="text" id="paquete_contacto_nombre" name="contacto_nombre" placeholder="Nombre">
						<input type="hidden" id="paquete_contacto_id" name="contacto_id">
					</td>
					<td>
						<button type="button" class="btn btn-info" id="paquete_crear_contacto" title="Nuevo Cliente"><i class="icon-plus"></i></button>
					</td>
				</tr>
				<tr>
					<td>
						<b>Valor Declarado</b><br>
						<input type="text" id="paquete_valor_declarado" name="valor_declarado" class="input-medium">
					</td>
					<td>
						<b>No Documento</b><br>
						<input type="text" id="paquete_documento_cliente" name="documento_cliente" placeholder="Opcional" class="input-medium">
					</td>
				</tr>
				<tr>
					<td>
						<b>Observacion</b><br>
						<input type="text" id="paquete_observaciones" name="observacion">
					</td>
					<td>
						<b>Producto</b><br>
						<input type="text" id="paquete_producto_id" name="producto_id">
					</td>
				</tr>
				<tr>
					<td>
						<b>Unidades</b><br>
						<input type="text" id="paquete_contacto_unidades" name="unidades" class="input-mini">
					</td>
					<td>
						<b>Peso</b><br>
						<input type="text" id="paquete_contacto_peso" name="peso" class="input-mini" placeholder="Kgs">
					</td>
				</tr>
				<tr>
					<td colspan="3" class="text-center">
						<button id="paquete_contacto_add" class="btn btn-info"><i class="icon-plus"></i> Agregar</button>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<form>
		<table class="table table-hover table-bordered table-condensed" id="paquete_contactos_list">
			<thead>
				<tr>
					<th>Destinatario</th>
					<th style="width: 30px">Und</th>
					<th style="width: 30px">Peso</th>
					<th style="width: 80px">Seguro</th>
					<th style="width: 80px">Flete</th>
					<th style="width: 30px"></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<div class="input-prepend pull-right">
			<span class="add-on"><b>TOTAL</b></span>
			<input id="paquete_total_calculado" type="text" readonly="readonly" class="input-medium text-right" value="0">
		</div>
	</form>
	<div class="text-center">
		<button id="paquete_save" class="btn btn-primary"> Guardar </button>
	</div>
</div>
<script>
(function() {
	var paquete = {
		$el: $('div#container'),
		id_cliente: null,
		id_ciudad: null,
		ciudad: '',
		total: 0,
		peso: 0,
		contactos: 0,
		precio_kilo: 0,
		total_calculado: 0,
		tipo_cobro: null,
		validate_add_contacto: function() {
			paquete.$el.find('form#paquete_add_contacto').validate({
				rules: {
					contacto_id: 'required',
					valor_declarado: {required: true, number: true},
					observacion: 'required',
					producto_id: {required: true, digits: true},
					unidades: {required: true, digits: true},
					peso: {required: true, number: true}
				},
				errorPlacement: function(er, el) { },
				highlight: function(input) {
					if ('paquete_contacto_id' == input.id) {
						input =	'#paquete_contacto_nombre';
					}
					$(input).addClass("ui-state-highlight");
				},
				unhighlight: function(input) {
					if ('paquete_contacto_id' == input.id) {
						input =	'#paquete_contacto_nombre';
					}
					$(input).removeClass("ui-state-highlight");
				},
				submitHandler: function(form) {
					paquete.add_contacto($(form).serializeObject());
					form.reset();
					paquete.$el.find('#paquete_contacto_nombre').focus();
					return false;
				}
			})
		},
		validate_paquete: function() {
			paquete.$el.find('form#paquete').validate({
				rules: {
					'paquete[id_cliente]': 'required',
					'paquete[id_ciudad]': 'required',
					'paquete[total]': {required: true, number: true},
					'paquete[id_embalaje]': 'required'
				},
				errorPlacement: function(er, el) { },
				highlight: function(input) {
					if ('paquete_cliente_id' == input.id) {
						input =	'#paquete_cliente_nombre';
					} else if ('paquete_ciudad_id' == input.id) {
						input =	'#paquete_ciudad_nombre';
					}
					$(input).addClass("ui-state-highlight");
				},
				unhighlight: function(input) {
					if ('paquete_cliente_id' == input.id) {
						input =	'#paquete_cliente_nombre';
					} else if ('paquete_ciudad_id' == input.id) {
						input =	'#paquete_ciudad_nombre';
					}
					$(input).removeClass("ui-state-highlight");
				}
			});
		},
		add_contacto: function(object) {
			if (! object.contacto_id) {
				alertify.log('Selecciona un contacto.');
				return;
			}
			paquete.peso += parseFloat(object.peso);
			if (paquete.peso == 0) {
				paquete.precio_kilo = 0;
			} else {
				paquete.precio_kilo = paquete.total / paquete.peso;
			}

			var row = '<tr>',
				name = 'guias['+(paquete.contactos++)+']';
			object.seguro = (paquete.tipo_cobro.seguro / 100 * object.valor_declarado).toFixed();
			row += '<td>'+object.contacto_nombre;
			row += '<input type="hidden" name="'+name+'[guia][idcontacto]" value="'+object.contacto_id+'">';
			row += '<input type="hidden" name="'+name+'[guia][observacion]" value="'+object.observacion+'">';
			row += '<input type="hidden" name="'+name+'[guia][valordeclarado]" value="'+object.valor_declarado+'">';
			row += '<input type="hidden" name="'+name+'[guia][documentocliente]" value="'+object.documento_cliente+'">';
			row += '<input type="hidden" name="'+name+'[guia][valorseguro]" value="'+object.seguro+'">';
			row += '<input type="hidden" name="'+name+'[item][idproducto]" value="'+object.producto_id+'">';
			row += '<input type="hidden" name="'+name+'[item][unidades]" value="'+object.unidades+'">';
			row += '</td>';
			row += '<td>'+object.unidades+'</td>';
			row += '<td><input type="text" name="'+name+'[item][peso]" value="'+object.peso+'" class="input-mini peso"></td>';
			row += '<td>'+object.seguro+'</td>';
			row += '<td><input type="text" name="'+name+'[item][valor]" value="" class="input-mini total"></td>';
			row += '<td><button class="btn borrar btn-danger"><i class="icon-trash"></i></button></td>';
			row += '</tr>';
			$('table#paquete_contactos_list').append(row);
			paquete.calculate_all();
		},
		autocomplete_params_for: function(type, select) {
			return {
				autoFocus: true,
				minLength: 3,
				focus: function() {return false;},
				source: helpers_path+'ajax.php?'+type+'=1',
				select: function(e, ui) {
					if ('cliente' == type) {
						$(this).val(ui.item.nombre);
						paquete.$el.find('#paquete_cliente_id').val(ui.item.id);
						paquete.id_cliente = ui.item.id;
						paquete.$el.find('#paquete_ciudad_nombre').focus();
					} else if ('ciudad' == type) {
						$(this).val(ui.item.nombre);
						paquete.$el.find('#paquete_ciudad_id').val(ui.item.id);
						paquete.$el.find('#paquete_tipo_cobro').focus();
						paquete.id_ciudad = ui.item.id;
						paquete.ciudad = ui.item.nombre;
						paquete.load_lista_precios();
					} else if ('producto' == type) {
						$(this).val(ui.item.id);
						paquete.$el.find('#paquete_contacto_unidades').focus();
					} else if ('contacto' == type) {
						if (! paquete.id_ciudad || ! paquete.id_cliente) {
							alertify.log('Selecciona un remitente y la ciudad destino');
							return false;
						}
						$(this).val(ui.item.nombre);
						paquete.$el.find('#paquete_contacto_id').val(ui.item.id);
						paquete.$el.find('#paquete_valor_declarado').focus();
					}
					return false;
				}
			};
		},
		load_lista_precios: function() {
			var elem = this.$el.find('#paquete_tipo_cobro');
			elem.attr('disabled', 'disabled');
			$.ajax({
				url: guias_path+'ajax.php', type: 'POST',
				data: {
					embalaje: 'si',
					id_cliente: this.id_cliente,
					id_ciudad_contacto: this.id_ciudad
				},
				success: function(msj) {
					elem.removeAttr('disabled');
					elem.html(msj);
					if (! msj) {
						alertify.log('El cliente no tiene precios para la ciudad seleccionada.');
					}
				}
			});
		},
		remove_item: function() {
			$(this).parent().parent().remove();
			paquete.calculate_all();
		},
		calculate_total: function() {
			paquete.total_calculado = 0;
			$('table#paquete_contactos_list input.total').each(function(i, el) {
				paquete.total_calculado += parseFloat($(el).val());
			});
			paquete.display_total_calculado();
		},
		display_total_calculado: function() {
			paquete.$el.find('input#paquete_total_calculado').val(paquete.total_calculado);
		},
		calculate_all: function() {
			paquete.total_calculado = 0;
			paquete.peso = 0;
			$('table#paquete_contactos_list input.peso').each(function(i, el) {
				paquete.peso += parseFloat($(el).val());;
			});
			paquete.precio_kilo = paquete.total / paquete.peso;
			$('table#paquete_contactos_list tbody tr').each(function(i, el) {
				var peso = parseFloat($(el).find('input.peso').val());
				var total = parseFloat((peso * paquete.precio_kilo).toFixed());
				$(el).find('input.total').val(total);
				paquete.total_calculado += total;
				paquete.peso += peso;
			});
			paquete.display_total_calculado();
		},
		select_tipo_cobro: function() {
			paquete.tipo_cobro = $(this).find('option:selected').data();
			if (paquete.tipo_cobro.tipoCobro != 'Viaje Convenido') {
				paquete.$el.find('#error').show();
				paquete.$el.find('#paquete_save').attr('disabled', 'disabled');
				paquete.$el.find('#paquete_total').val('');
				return false;
			}
			paquete.$el.find('#error').hide();
			paquete.$el.find('#paquete_save').removeAttr('disabled');
			paquete.total = paquete.tipo_cobro.precio;
			paquete.$el.find('#paquete_total').val(paquete.total);
		},
		save: function() {
			$form = paquete.$el.find('form#paquete');
			if (! $form.valid()) {
				alertify.error('Por favor, completa el formulario.');
				return false;
			}
			var items = paquete.$el.find('#paquete_contactos_list').parent().serialize();
			if (! items) {
				alertify.log('Agrega por lo menos un destinatario.');
				return false;
			}
			if (paquete.total != paquete.total_calculado) {
				alertify.log('El total debe ser '+paquete.total);
				return false;
			}
			var btn = paquete.$el.find('#paquete_save');
			btn.attr('disabled', 'disabled').text('Guardando...');
			$.ajax({
				url: guias_path+'ajax.php?paquete=1',
				type: 'post',
				data: $form.serialize()+'&'+items,
				success: function(response) {
					if (! response) {
						regresar();
					} else {
						btn.removeAttr('disabled').text('Guardar');
						alertify.error(response);
					}
				}
			});
		},
		init: function(){
			this.validate_add_contacto();
			this.validate_paquete();
			this.$el.on('change', 'input.peso', this.calculate_all);
			this.$el.on('change', 'input.total', this.calculate_total);
			this.$el.on('click', 'button.borrar', this.remove_item);
			this.$el.on('click', 'button#paquete_save', this.save);
			this.$el.find('input#paquete_cliente_nombre').focus();
			this.$el.find('input#paquete_cliente_nombre').autocomplete(this.autocomplete_params_for('cliente'));
			this.$el.find('input#paquete_contacto_nombre').autocomplete(this.autocomplete_params_for('contacto'));
			this.$el.find('input#paquete_ciudad_nombre').autocomplete(this.autocomplete_params_for('ciudad'));
			this.$el.find('input#paquete_producto_id').autocomplete(this.autocomplete_params_for('producto'));
			this.$el.find('select#paquete_tipo_cobro').change(this.select_tipo_cobro);
			this.$el.find('button#paquete_crear_contacto').click(function() {
				LOGISTICA.Dialog.open('Nuevo Cliente', clientes_path+'crear.php?dialog=1')
			});
		}
	};
	paquete.init();
})();
</script>
