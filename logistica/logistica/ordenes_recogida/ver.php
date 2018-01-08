<?php
$raiz = "../../";
require $raiz."seguridad.php";
if (! isset($_GET['id']) or ! nonce_is_valid($_GET[NONCE_KEY],$_GET['id'])) {
	include Logistica::$root.'mensajes/id.php';
	exit;
}
if (! isset($_SESSION['permisos'][ORDENES_RECOGIDA_VER]) ) {
	include Logistica::$root."mensajes/permiso.php";
	exit;
}

$configuracion = new Configuracion;
if (! $orden_recogida = OrdenRecogida::find($_GET['id']) ) exit('No existe la orden de recogida');
//La carpeta para las ordebes escaneadas debe estar dentro de la carpeta 'htdocs'.
// el '../' se usa para regresar a la carpeta 'htdocs'.
if (! file_exists('../'.$raiz.$configuracion->app_ruta_ordenes)) {
	mkdir('../'.$raiz.$configuracion->app_ruta_ordenes);
}
$orden_escaneada = '../'.$raiz.$configuracion->app_ruta_ordenes.$orden_recogida->id.'_1.pdf';
?>
<script>
$(function() {
	<?php if (file_exists($orden_escaneada)) { ?>
	var pdf = new PDFObject({
		url: '<?= $orden_escaneada ?>',
		pdfOpenParams: { view: 'FitH', statusbar: '0', messages: '0', navpanes: '0' }
	}).embed("pdf");
	if (pdf) {
		$('#pdf').css('width','100%').css('height','500px');
	}
	<?php } ?>
});
</script>
<div class="btn-toolbar pull-right">
  <?php
  if (isset($_SESSION['permisos'][ORDENES_RECOGIDA_IMPRIMIR])) {
    echo '<a class="btn" href="logistica/ordenes_recogida/imprimir?id='.$orden_recogida->id.'&'.nonce_create_query_string($orden_recogida->id).'" target="_blank"><i class="icon-print"></i></a>';
  }
  ?>
	<button class="btn btn-success" id="regresar" onclick="regresar()">Regresar</button>
</div>
<h2>Orden de Recogida <?= $orden_recogida->id ?></h2>
<div class="row-fluid">
  <div class="span4">
    <b>Ciudad</b>
    <p class="lead"><?= $orden_recogida->ciudad_nombre ?></p>
  </div>
  <div class="span4">
    <b>Fecha</b>
    <p class="lead"><?= $orden_recogida->fecha_larga ?></p>
  </div>
  <div class="span4">
    <b>Ruta</b><br>
    <p class="lead"><?= $orden_recogida->ruta ?></p>
  </div>
</div>
<div class="row-fluid">
  <div class="span6">
    <fieldset>
      <legend>Conductor</legend>
      <table>
        <tr>
          <td><b>Nombre</b></td>
          <td><?= $orden_recogida->conductor()->nombre_completo() ?></td>
        </tr>
        <tr>
          <td><b>Cédula</b></td>
          <td><?= $orden_recogida->conductor()->numero_identificacion ?></td>
        </tr>
        <tr>
          <td><b>Ciudad</b></td>
          <td><?= $orden_recogida->conductor()->ciudad_nombre ?></td>
        </tr>
        <tr>
          <td><b>Licencia</b></td>
          <td><?= $orden_recogida->conductor()->categorialicencia ?></td>
        </tr>
      </table>
    </fieldset>
  </div>
  <div class="span6">
    <fieldset>
      <legend>Vehículo</legend>
      <table>
        <tr>
          <td><b>Placa</b></td>
          <td><?= $orden_recogida->vehiculo()->placa ?></td>
        </tr>
        <tr>
          <td><b>Marca</b></td>
          <td><?= $orden_recogida->vehiculo()->marca()->nombre ?></td>
        </tr>
      </table>
    </fieldset>
  </div>
</div>
<div class="row-fluid">
  <fieldset class="span12">
    <legend>Recoger a...</legend>
    <table id="lista_clientes" class="table table-condensed table-hover">
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Observaciones</th>
          <th>Unidades</th>
          <th>Hora</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (empty($orden_recogida->clientes)) {
          echo '<tr><td colspan="3">No se encontraron clientes...</td></tr>';
        } else {
          $lineas = explode(';;', $orden_recogida->clientes);
          foreach ($lineas as $linea) {
            $campos = explode('--', $linea);
            if (! isset($campos[3]) ) {
              $campos[3] = '';
              $campos[4] = '';
            }
            echo '<tr>';
            echo '<td>'.$campos[0].'<br><span class="muted">'.$campos[1].'</span></td>';
            echo '<td>'.$campos[2].'</td>';
            echo '<td>'.$campos[3].'</td>';
            echo '<td>'.$campos[4].'</td>';
            echo '</tr>';
          }
        }
        ?>
      </tbody>
    </table>
  </fieldset>
</div>
<div class="row-fluid">
  <fieldset class="span12">
    <legend>Ayudantes</legend>
    <table>
      <?php
      $orden_recogida->ayudantes();
      if (empty($orden_recogida->ayudantes)) {
        echo '<tr><td class="expand">No se indicaron ayudantes</td></tr>';
      } else {
        foreach ($orden_recogida->ayudantes as $ayudante) {
          echo '<tr>';
          echo '<td>'.$ayudante->numero_identificacion.' - '.$ayudante->nombre.'</td>';
          echo '</tr>';
        }
      }
      ?>
    </table>
  </fieldset>
</div>
<?php
if (file_exists($orden_escaneada)) {
	echo '<div class="row-fluid"><fieldset class="span12">';
	echo '<legend>Orden Escaneada</legend>';
	echo '<div id="pdf">Parece que tu navegador no tiene soporte para PDF, puedes instalar <a target="_blank" title="Descargar Adobe Reader" href="http://get.adobe.com/reader/">Adobe Reader</a></div>';
	echo '<br /><a target="_blank" href="'.$orden_escaneada.'">Has clic aquí para descargar el archivo</a>';
	echo '</fieldset></div>';
}
?>
