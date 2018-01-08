<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][USUARIOS_VER])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}

if (! isset($_REQUEST['id']) or (isset($_REQUEST['id']) and empty($_REQUEST['id']))) {
  include Logistica::$root."mensajes/id.php";
  exit;
}

if (! $usuario = Usuario::find($_REQUEST['id'])) exit('No existe el usuario');

if(isset($_REQUEST['exportar'])){
  require_once Logistica::$root.'php/Excel.inc.php';
  header("Pragma: no-cache");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header('Content-type: application/vnd.ms-excel; charset=utf-8');
  header("Content-Disposition: attachment; filename=Log_".$usuario->usuario.".xls");
  echo xlsBOF();
  echo xlsWriteLabel(0, 0, "Registro de acciones - ".$usuario->usuario);
  echo xlsWriteLabel(2, 0, "IP");
  echo xlsWriteLabel(2, 1, "Modulo");
  echo xlsWriteLabel(2, 2, "ID");
  echo xlsWriteLabel(2, 3, "Accion");
  echo xlsWriteLabel(2, 4, "Fecha");
  $xlsRow = 3;
  $logs = $usuario->history();
  foreach ($logs as $l) {
    echo xlsWriteLabel($xlsRow, 0, $l->ip);
    echo xlsWriteLabel($xlsRow, 1, $l->modulo);
    $search = array('á','é','í','ó','ú','ñ','Ñ','í','<b>','</b>');
    $replace = array('a','e','i','o','u','n','N','i','"','"');
    $l->accion = str_replace($search, $replace, $l->accion);
    echo xlsWriteLabel($xlsRow, 2, $l->id_modulo);
    echo xlsWriteLabel($xlsRow, 3, $l->accion);
    echo xlsWriteLabel($xlsRow, 4, strftime('%d/%m/%Y %I:%M:%S %p', strtotime($l->fecha)));
    $xlsRow++;
  }
  echo xlsEOF();
  exit;
}
?>
<script>
function fn_paginar(d, u){
  $('#'+d).load(u);
}
</script>
<button class="btn btn-success pull-right" onclick="regresar()">Regresar</button>
<h2>Usuario <?= $usuario->usuario ?></h2>
<hr class="hr-small">
<table>
  <tr>
    <td><b>Nombre:</b></td>
    <td><?= $usuario->nombre ?></td>
  </tr>
  <tr>
    <td><b>Cédula:</b></td>
    <td><?= $usuario->cedula ?></td>
  </tr>
  <tr>
    <td title="Es usado para iniciar sesión en el sitio"><b>Nombre de usuario:</b></td>
    <td><?= $usuario->usuario ?></td>
  </tr>
  <tr>
    <td title="Correo electrónico del usuario"><b>Correo electrónico:</b></td>
    <td><?= $usuario->email ?></td>
  </tr>
  <tr>
    <td title=""><b>Perfil:</b></td>
    <td><?= $usuario->perfil() ?></td>
  </tr>
  <tr>
    <td title="Fecha en la que el usuario fue creado"><b>Fecha de creación:</b></td>
    <td><?= htmlentities(strftime('%A, %d de %B de %Y', strtotime($usuario->fechacreacion))) ?></td>
  </tr>
  <tr>
    <td title="Ultimo acceso del usuario al sistema"><b>Último acceso:</b></td>
    <td><?= htmlentities(strftime('%A, %d de %B de %Y - %I:%M:%S %p', strtotime($usuario->ultimo_acceso))) ?></td>
  </tr>
</table>
<table>
  <tr>
    <td><p style="font-size: 14px;margin-bottom: 0px;"><b>Registro de acciones</b></p></td>
    <td width="200"></td>
    <td>
      <form action="<?= $_SERVER['PHP_SELF'] ?>" target="_blank" method="post">
        <input type="hidden" name="exportar" value="1" />
        <input type="hidden" name="id" value="<?= $usuario->id ?>" />
        <button class="btn btn-info"><i class="icon-file-alt"></i> Exportar Registro</button>
      </form>
    </td>
  </tr>
</table>
<table class="table table-condensed table-hover table-bordered">
  <thead>
    <tr>
      <th>IP</th>
      <th>Modulo</th>
      <th>ID</th>
      <th>Acción realizada</th>
      <th>Fecha/Hora</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = $usuario->history('sql');
    $paging = new PHPPaging('extra_content', $sql, true);
    $paging->ejecutar();
    if ($paging->numTotalRegistros() == 0) {
      echo '<tr class="warning"><td colspan="5" class="expand">No se encontraron registros..</td></tr>';
    }else{
      while( $accion = $paging->fetchResultado() ){
        echo '<tr>';
        echo '<td align="center">'.$accion['ip'].'</td>';
        echo '<td align="center">'.$accion['modulo'].'</td>';
        echo '<td align="center">'.$accion['id_modulo'].'</td>';
        echo '<td>'.$accion['accion'].'</td>';
        echo '<td align="center" title="'.htmlentities(strftime('%A, %d de %B de %Y - %I:%M:%S %p', strtotime($accion['fecha']))).'">'.strftime('%b %d - %I:%M %p', strtotime($accion['fecha'])).'</td>';
        echo '</tr>';
      }
    }
    ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
