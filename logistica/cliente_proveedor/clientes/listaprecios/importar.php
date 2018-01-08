<?php
require "../../../seguridad.php";
if (! isset($_GET['id'])) {
  include Logistica::$root."mensajes/id.php";
  exit;
}
if (! isset($_SESSION['permisos'][LISTA_PRECIOS_IMPORTAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

$cliente = new Cliente;
if (! $cliente->find($_GET['id'])) exit('No existe el cliente.');
?>
<script>
$(function(){
  var ruta_mensajes = 'mensajes/';

  $('#ayuda').click(function(event){
    event.preventDefault();
    $('#respuesta, #respuesta2').slideUp();
    var o={'title':'Ayuda | Importar listas de precios','height':250, 'width':650,'position':'center'};
    $( "#dialog" ).dialog('option',o);
    $( "#dialog" ).html('Cargando...').dialog('open').load(ruta_mensajes+'ayuda_importar_lista_precios.php');
  });
  $('#ayuda2').click(function(event){
    event.preventDefault();
    $('#respuesta, #respuesta2').slideUp();
    var o={'title':'Ayuda | Importar listas de precios','height':250, 'width':650,'position':'center'};
    $( "#dialog" ).dialog('option',o);
    $( "#dialog" ).html('Cargando...').dialog('open').load(ruta_mensajes+'ayuda_importar_lista_precios2.php');
  });
  $('#archivo').uploadify({
    'uploader'  : 'js/uploadify.swf',
    'script'    : clientes_path+'listaprecios/subir.php',
    'cancelImg' : 'css/images/cancel.png',
    'folder'    : clientes_path+'listaprecios/archivos',
    'auto'      : false,
    'buttonText': 'Seleccionar...',
    'fileExt'   : '*.csv; *.txt',
    'fileDesc'  : 'Archivos planos (.CSV, .TXT)',
    'width'   : 200,
    'scriptData': {'id':$('#id_cliente').val()},
    'onComplete': function(event, ID, fileObj, response, data) {
      $('#dialog').dialog('close');
      $('#respuesta').html(response).slideDown(1000);
    },
    'onError' : function (event,ID,fileObj,errorObj) {
      $('#dialog').dialog('close');
      alert(errorObj.type + ' Error: ' + errorObj.info);
    },
    'onCancel'  : function(event,ID,fileObj,data) {
      $('#dialog').dialog('close');
    }
  });
  $('#subir').button({
    icons: {primary: 'ui-icon-circle-arrow-n'}
  }).click(function(e){
    e.preventDefault();
    if ($('#archivoQueue .uploadifyQueueItem').attr("id")){
      var o={'title':'Importando lista de precios','height':150, 'width': 350,'position':'center'};
      $( "#dialog" ).load(ruta_mensajes+'importando.php', function(){
        $( "#dialog" ).dialog('open').dialog('option',o);
        $('#archivo').uploadifyUpload();
      });
    }
  });

  $('#subir_2').button({
    icons: {primary: 'ui-icon-circle-arrow-n'}
  }).click(function(e){
    e.preventDefault();
    if ($('#archivo_2Queue .uploadifyQueueItem').attr("id")){
      var o={'title':'Importando lista de precios','height':150, 'width': 350,'position':'center'};
      $( "#dialog" ).load(ruta_mensajes+'importando.php', function(){
        $( "#dialog" ).dialog('open').dialog('option',o);
        $('#archivo_2').uploadifyUpload();
      });
    }
  });
  $('#archivo_2').uploadify({
    'uploader'  : 'js/uploadify.swf',
    'script'    : clientes_path+'listaprecios/subir2.php',
    'cancelImg' : 'css/images/cancel.png',
    'folder'    : clientes_path+'listaprecios/archivos',
    'auto'      : false,
    'buttonText': 'Seleccionar...',
    'fileExt'   : '*.csv; *.txt',
    'fileDesc'  : 'Archivos planos (.CSV, .TXT)',
    'width'   : 200,
    'scriptData': {'id':$('#id_cliente').val(),'opc_modo_prueba':$('#opc_modo_prueba').val(),'opc_ciudad_destino':$('#opc_ciudad_destino').val()},
    'onComplete': function(event, ID, fileObj, response, data) {
      $('#dialog').dialog('close');
      $('#respuesta_2').html(response).slideDown(1000);
    },
    'onError' : function (event,ID,fileObj,errorObj) {
      $('#dialog').dialog('close');
      alert(errorObj.type + ' Error: ' + errorObj.info);
    },
    'onCancel'  : function(event,ID,fileObj,data) {
      $('#dialog').dialog('close');
    }
  });
});
</script>
<div class="row-fluid">
  <div class="span10"><h3><?= $cliente->nombre_completo ?> | Importar Lista de Precios</h3></div>
  <div class="span2"><button id="regresar" class="btn btn-success pull-right" onclick="regresar()">Regresar</button></div>
</div>
<hr class="hr-small">
<div class="row-fluid">
  <div class="span12">
    <div class="row-fluid">
      <div class="span8 offset2 text-center">
        <h3>Primera opción</h3>
        <p>Permite importar las listas de precios usando los códigos de las ciudades.</p>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span2 text-center">
        <button id="subir">Subir</button>
      </div>
      <div class="span9">
        <input type="file" id="archivo" name="archivo" />
      </div>
      <div class="span1">
        <a href="#" id="ayuda">Ayuda</a>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span12 ui-widget-content" id="respuesta" style="display:none; padding: 10px; margin-top: 7px;"></div>
    </div>
  </div>
</div>
<hr>
<div class="row-fluid">
  <div class="span12">
    <div class="row-fluid">
      <div class="span8 offset2 text-center">
        <h3>Segunda opción</h3>
        <p>Permite importar las listas de precios usando los nombres de las ciudades.</p>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span2 text-center">
        <button id="subir_2">Subir</button>
      </div>
      <div class="span9">
        <input type="file" id="archivo_2" name="archivo_2" />
      </div>
      <div class="span1">
        <a href="#" id="ayuda2">Ayuda</a>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span12 ui-widget-content" id="respuesta_2" style="display:none; padding: 10px; margin-top: 7px;"></div>
    </div>
  </div>
</div>
<input type="hidden" id="" name="id_cliente" value="<?= $_GET['id'] ?>" />
