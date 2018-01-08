<?php
require 'seguridad.php';
require 'class/Logistica.php';
Logistica::register_autoloader();
DBManager::connect();

$query  = "SELECT id, idciudad, CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) nombre_completo FROM clientes LIMIT 500";
$ary1 = Base::build_resources($query);
$ary2 = Base::build_resources($query);

echo '<table border=1 cellspacing=0 cellpadding=2>';
foreach ($ary1 as $cliente1) {
  foreach ($ary2 as $cliente2) {
    if ($cliente1->id != $cliente2->id and $cliente1->idciudad == $cliente2->idciudad) {
      similar_text($cliente1->nombre_completo, $cliente2->nombre_completo, $percent);
      if ($percent >= 90) {
        echo '<tr>';
        echo '<td>'.$cliente1->id.'-'.utf8_decode($cliente1->nombre_completo).'</td>';
        echo '<td>'.$cliente2->id.'-'.utf8_decode($cliente2->nombre_completo).'</td>';
        echo '<td>'.$percent.'%</td>';
        echo '</tr>';
      }
    }
  }
}
echo '</table>';
