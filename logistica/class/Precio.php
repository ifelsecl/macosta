<?php
class Precio extends Base {
  static $table = 'listaprecios';
  static $attributes = array('idcliente', 'idciudadorigen', 'idciudaddestino', 'idembalaje',
    'precio', 'precio_kilo', 'precio_kilovol', 'seguro', 'descuento3', 'descuento6', 'descuento8');

  static function sql_base() {
    return "SELECT lp.*, co.nombre ciudad_origen_nombre, cd.nombre ciudad_destino_nombre,
em.nombre embalaje_nombre, em.tipo_cobro tipo_cobro
FROM ".self::$table." lp, ".Ciudad::$table." co, ".Ciudad::$table." cd, ".Embalaje::$table." em
WHERE co.id=lp.idciudadorigen AND cd.id=lp.idciudaddestino AND em.id=lp.idembalaje ";
  }

  static function find($id_cliente, $id_ciudad_origen, $id_ciudad_destino, $id_embalaje) {
    $sql = self::sql_base()." AND lp.idcliente='$id_cliente' AND lp.idciudadorigen='$id_ciudad_origen'
AND lp.idciudaddestino='$id_ciudad_destino' AND lp.idembalaje='$id_embalaje'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  static function available($id_cliente, $id_ciudad_origen, $id_ciudad_destino) {
    $sql = self::sql_base()." AND lp.idcliente='$id_cliente'
AND lp.idciudadorigen='$id_ciudad_origen'
AND lp.idciudaddestino='$id_ciudad_destino'";
    return parent::build_resources($sql, __CLASS__);
  }

  function update_attributes($params) {
    self::check_mass_assignment($params, self::$attributes);
    $sql = '';
    foreach ($params as $key => $value) {
      $sql .= $key."='$value',";
    }
    $sql = substr($sql, 0, -1);
    $sql = 'UPDATE '.self::$table.' SET '.$sql." WHERE idcliente='$this->idcliente' AND idciudadorigen='$this->idciudadorigen'
AND idciudaddestino='$this->idciudaddestino' AND idembalaje='$this->idembalaje'";
    return DBManager::execute($sql);
  }

  function liquidate($restriccion_peso, $unidades, $peso, $valor_declarado) {
    $this->descuento = 0;
    switch ($this->tipo_cobro) {
      case 'Caja':
        $precio_unidad = $this->precio * $unidades;
        $precio_kilo = $this->precio_kilo * $peso;
        $flete = $precio_unidad >= $precio_kilo ? $precio_unidad : $precio_kilo;
        break;

      case 'Caja2':
        if ($restriccion_peso == $peso) {
          $flete = $this->precio;
        } else {
          $flete = $this->precio_kilo * $peso;
        }
        break;

      case 'Descuento':
        $total = $this->precio * $unidades;
        $porcentaje_descuento = 0;
        if ($unidades >= 8) {
          $porcentaje_descuento = $this->descuento8;
        } elseif ($unidades >= 6) {
          $porcentaje_descuento = $this->descuento6;
        } elseif ($unidades >= 3) {
          $porcentaje_descuento = $this->descuento3;
        }
        $this->descuento = $total * $porcentaje_descuento / 100;
        $flete = $total -= $this->descuento;
        break;

      case 'Kilo':
        $flete = $this->precio * $peso;
        break;

      case 'Porcentaje':
        $flete = $valor_declarado * ($this->precio / 100);
        break;

      case 'Viaje Convenido':
        $flete = $this->precio;
        break;

      default:
        $flete = $this->precio * $unidades;
        break;
    }
    return round($flete);
  }
}
