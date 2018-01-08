<?php
require '../../seguridad.php';
if (! isset($_GET['accion'])) exit('Algo ha salido mal.');
$accion = $_GET['accion'];

if ($accion == 'comprobar_guia') {
  $r['error'] = true;
  if (!isset($_GET['id_guia']) or !isset($_GET['id_cliente'])) {
    $r['mensaje']='Algo no está bien...';
  } else {
    $guia = new Guia;
    if (! $guia->find($_GET['id_guia'])) {
      $r['mensaje'] = 'No existe la guia';
    } else {
      if ($guia->idestado == 6) {
        $r['mensaje']='La guía <b>'.$guia->id.'</b> está ANULADA.';
      } elseif ($guia->idcliente == $_GET['id_cliente']) {
        $r['error'] = false;
        $r['mensaje'] = '<tr><td><input class="guias_asignadas" type="hidden" name="relacion[guias][]" value="'.$_GET['id_guia'].'" />'.$_GET['id_guia'].'</td><td><button class="btn btn-danger btn-small quitar"><i class="icon-remove"></i></button></td></tr>';
      } else {
        $r['mensaje'] = 'La guía <b>'.$_GET['id_guia'].'</b> pertenece a <b>'.$guia->cliente()->nombre_completo.'</b>';
      }
    }
  }
  echo json_encode($r);
  exit;
}
if ($accion == 'numero_documento') {
  $r = array();
  if (! isset($_GET['term']) or ! isset($_GET['id_cliente'])) {
    $r[] = 'Algo no está bien...';
  } else {
    $guias = Guia::all_by_id_cliente_and_numero_documento($_GET['id_cliente'], $_GET['term']);
    foreach ($guias as $g) {
      $r[] = array(
        'id' => $g->id,
        'value' => $g->id.' '.$g->contacto()->nombre_completo,
        'html' => '<tr><td><input class="guias_asignadas" type="hidden" name="relacion[guias][]" value="'.$g->id.'" />'.$g->id.'</td><td><button class="btn btn-danger btn-small quitar"><i class="icon-remove"></i></button></td></tr>'
      );
    }
  }
  echo json_encode($r);
  exit;
}
