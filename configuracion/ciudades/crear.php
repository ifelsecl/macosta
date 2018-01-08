<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][CIUDADES_AGREGAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
$departamentos = Departamento::all();
?>
<script>
$(function(){
  $('#guardar').button({icons: {primary: "ui-icon-circle-check"}});
  $('#departamento').change(function(){
    $('#codigo').val($('#departamento').val()).focus();
  });
  $('#municipio').autocomplete({source: ciudades_path+'ajax.php?bm=18', minLength: 2});
  $('#Crear').validate({
    rules: {
      departamento: 'required',
      codigo: {required: true, digits: true, minlength: 7, maxlength: 8},
      nombre: 'required',
      municipio: 'required'
    },
    messages: {
      departamento: 'Selecciona el departamento',
      codigo: {
        required: 'Escribe el codigo',
        digits: 'Solo numeros',
        minlength: 'Minimo 7, completa con 0 a la derecha',
        maxlength: 'Maximo 8, completa con 0 a la derecha'
      },
      nombre: 'Escribe el nombre',
      municipio: 'Escribe el municipio'
    },
    errorPlacement: function(error, element) {error.appendTo( element.parent("td").next("td") );},
    highlight: function(input) { $(input).addClass("ui-state-highlight"); },
    unhighlight: function(input) { $(input).removeClass("ui-state-highlight"); },
    submitHandler: function() {
      $('#guardar').button('disable').button('option','label','Guardando...');
      $.ajax({
        url: ciudades_path+'ajax.php',
        type: 'POST',
        data: 'g=1&'+$("#Crear").serialize(),
        success: function(response){
          if (! response) {
            regresar();
          } else {
            $('#guardar').button('enable').button('option','label','Guardar');
            LOGISTICA.Dialog.open('Error',response,true);
          }
        }
      });
    }
  });
});
</script>
<button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Nueva Ciudad</h2>
<hr class="hr-small">
<form id="Crear">
  <table>
    <tr>
      <td><b>Departamento:</b></td>
      <td>
        <select name="departamento" id="departamento">
          <option value="">Selecciona...</option>
          <?php
          foreach ($departamentos as $d) {
            echo '<option value="'.$d->id.'">'.$d->id.'-'.substr($d->nombre, 0, 30).'</option>';
          }
          ?>
        </select>
      </td>
      <td></td>
    </tr>
    <tr>
      <td><b>Código:</b></td>
      <td><input maxlength="8" type="text" name="codigo" id="codigo"></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Nombre/Población:</b></td>
      <td><input type="text" name="nombre" id="nombre"></td>
      <td></td>
    </tr>
    <tr>
      <td><b>Municipio:</b></td>
      <td>
        <input type="text" id="municipio" name="municipio">
        <br><small class="muted">Puedes omitir las sugerencias</small>
      </td>
      <td></td>
    </tr>
  </table>
  <hr class="hr-small">
  <center><button id="guardar">Guardar</button></center>
</form>
