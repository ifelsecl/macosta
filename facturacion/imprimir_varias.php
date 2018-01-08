<script>
$(function(){
	$('#inicio').focus();
	$('#IR').validate({
		rules: {
			inicio: {required: true, digits: true},
			fin: {required: true, digits: true, min: function(){return $('#inicio').val()}}
		},
		errorPlacement: function(er, el){},
		highlight: function(input) {$(input).addClass("ui-state-highlight");},
		unhighlight: function(input) {$(input).removeClass("ui-state-highlight");},
		submitHandler: function(f) {f.submit();}
	});
});
</script>
<fieldset>
	<legend>Por rango</legend>
	<form id="IR" target="_blank" action="facturacion/imprimir" method="post">
		<table>
			<tr>
				<td><b>Imprimir</b> desde:</td>
				<td><input type="text" id="inicio" name="inicio" class="input-mini" /></td>
				<td>hasta:</td>
				<td><input type="text" id="fin" name="fin" class="input-mini" /></td>
			</tr>
		</table>
		<center><button class="btn imprimir_varias btn-info" type="submit"><i class="icon-print"></i> Imprimir</button></center>
		<input type="hidden" name="multiple" value="IR" />
	</form>
</fieldset>
