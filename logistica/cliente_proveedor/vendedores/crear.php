<?php
$raiz = '../../';
require_once $raiz.'seguridad.php';
if( ! isset($_SESSION['permisos'][VENDEDORES_CREAR]) ){
	include $raiz."mensajes/permiso.php";
	exit;
}
?>
<script>
$(function(){
	$("#guardar").button({icons: {primary: 'ui-icon-circle-check'}});
	$('#nombre').focus();
	$('#nombre_ciudad').autocomplete({
		autoFocus:true,
		minLength:4,
		selectFirst: true,
		source: helpers_path+"ajax.php?ciudad=1",
		select: function(event,ui){
			$('#id_ciudad').val(ui.item.id);
		}
	});

	$( "#regresar" ).click(function() {
		cargarPrincipal(vendedores_path);
	});
	$('#Crear').validate({
		rules: {
			nombre: 'required',
			cedula: {required: true, digits: true},
			id_ciudad: 'required',
			direccion: 'required',
			telefono: 'required',
			email: {email: true},
			codigo_siigo: {required: true, digits: true, rangelength: [4, 4]}
		},
		messages: {
			codigo_siigo: {
				required: 'Escribe el código del vendedor en SIIGO',
				digits: 'Solo numeros',
				rangelength: '4 números, rellena con 0 a la <b>izquierda</b>.'
			}
		},
		errorPlacement: function(er, el) {er.appendTo(el.parent("td").next("td") );},
		highlight: function(i) {$(i).addClass("ui-state-highlight");},
		unhighlight: function(i) {$(i).removeClass("ui-state-highlight");},
		submitHandler: function(form) {
			$('#m').hide();
			$('#guardar').button('disable').button('option','label','Guardando...');
			$.ajax({
        		url: vendedores_path+'ajax.php',
        	    type: "POST",
    	        data: 'g=101&'+$("#Crear").serialize(),
    	      	success: function(msj){
	            	if(msj=='ok'){
	            		cargarPrincipal(vendedores_path);
	            	}else{
	            		$('#m').html(msj).slideDown(600).delay(6000).slideUp(600);
	            		$('#guardar').button('enable').button('option','label','Guardar');
	            	}
	         	}
     		});
		}
	});
});
</script>
<button id="regresar" class="btn btn-success pull-right">Regresar</button>
<h2>Nuevo Vendedor</h2>
<hr class="hr-small">
<form id="Crear" method="post" action="#">
    <table border="0" cellpadding="3" cellspacing="3">
        <tr>
        	<td><b>Nombre:</b></td>
        	<td><input type="text" name="nombre" id="nombre" /></td>
        	<td></td>
        </tr>
        <tr>
        	<td><b>Cédula:</b></td>
        	<td><input type="text" name="cedula" id="cedula" /></td>
        	<td></td>
        </tr>
        <tr>
        	<td><b>Ciudad:</b></td>
        	<td>
          		<input title="Escribe para empezar a buscar..." type="text" id="nombre_ciudad" name="nombre_ciudad" />
          		<input type="hidden" id="id_ciudad" name="id_ciudad" />
        	</td>
        	<td></td>
        </tr>
        <tr>
        	<td><b>Direccion:</b></td>
        	<td><input type="text" name="direccion" id="direccion" /></td>
        	<td></td>
        </tr>
        <tr>
        	<td><b>Teléfono:</b></td>
        	<td><input type="text" name="telefono" id="telefono" /></td>
        	<td></td>
        </tr>
        <tr>
        	<td><b>Email:</b></td>
        	<td><input type="text" name="email" id="email" /></td>
        	<td></td>
        </tr>
        <tr>
        	<td><b>Código SIIGO:</b></td>
        	<td><input type="text" name="codigo_siigo" maxlength="4" size="4" id="codigo_siigo" value="" /></td>
        	<td></td>
        </tr>
    </table>
    <hr class="hr-small">
    <center><button id="guardar" type="submit">Agregar</button></center>
</form>
<div class="ui-state-highliht ui-corner-all" style="display: none;" id="m"></div>
