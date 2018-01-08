<?php
$raiz = "../../";
require $raiz."seguridad.php";
if( ! isset($_SESSION['permisos'][CLIENTES_IMPORTAR]) ) {
  include $raiz.'mensajes/permiso.php';
  exit;
}
?>
<script>
$(function(){
  $('#archivo').uploadify({
    'uploader'  : 'js/uploadify.swf',
    'script'    : clientes_path+'subir.php',
    'cancelImg' : 'css/images/cancel.png',
    'folder'    : clientes_path+'archivos',
    'auto'      : false,
    'buttonText': 'Seleccionar...',
    'fileExt'   : '*.csv; *.txt',
    'fileDesc'  : 'Archivos planos (.CSV, .TXT)',
    'width'   : 200,
    'scriptData': {},
    'onComplete': function(event, ID, fileObj, response, data) {
      $('#dialog').dialog('close');
      $('#respuesta').html(response).slideDown(1000);
    },
    'onError' : function (event,ID,fileObj,errorObj) {
      $('#dialog').dialog('close');
      alertify.error(errorObj.type + ' Error: ' + errorObj.info);
    },
    'onCancel'  : function(event,ID,fileObj,data) {
      $('#dialog').dialog('close');
    }
  });
  $('#subir')
    .button({icons: {primary: 'ui-icon-circle-arrow-n'}})
    .click(function(event){
      event.preventDefault();
      if($('#archivoQueue .uploadifyQueueItem').attr("id")){
        var o={'title':'Importando clientes','width':330,'height':170, 'position':'center'};
        $('#dialog').dialog('option',o).load('mensajes/importando.php',function(){
          $('#dialog').dialog('open');
          $('#archivo').uploadifyUpload();
        });
      }
    });
  $('#ayuda').click(function(){
    var o={'title':'Ayuda para importar clientes','width':600,'height':200, 'position':'center'};
    $('#dialog').dialog('option',o).html('<p>Cargando...</p>').dialog('open').load('mensajes/ayuda_importar_clientes.php');
  });
});
</script>
<button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Clientes | Importar</h2>
<hr class="hr-small">
<table style="width:100%">
  <tr>
    <td style="width:15%" rowspan="2" valign="top"><button id="subir">Subir</button></td>
  </tr>
  <tr>
    <td style="width:75%"><input type="file" id="archivo" name="archivo" /></td>
    <td style="width:10%" valign="top"><button class="btn" id="ayuda"><i class="icon-question-sign"></i> Ayuda</button></td>
  </tr>
</table>
<div id="respuesta" style="display:none; padding: 10px; margin-top: 7px;" class="ui-widget-content"></div>
