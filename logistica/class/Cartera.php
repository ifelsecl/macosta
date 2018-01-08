<?php
class Cartera {
  static function unpaid($return_sql = false) {
    $sql = "SELECT f.*, CONCAT_WS(' ', c.nombre, c.primer_apellido, c.segundo_apellido) cliente_nombre
FROM ".Factura::$table." f, ".Cliente::$table." c
WHERE c.id = f.idcliente AND f.total > f.total_pagos
ORDER BY f.idcliente ASC";
    if ($return_sql) return $sql;
    return Base::build_resources($sql, 'Factura');
  }
}
