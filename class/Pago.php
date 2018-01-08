<?php
class Pago extends Base {
  static $table = 'pagos';
  static $attributes = array('id', 'factura_id', 'fecha', 'tipo', 'notas', 'valor');
  static $types = array('cheque', 'efectivo', 'consignacion');

  static function create($params) {
    return parent::_create($params);
  }

  static function where($params) {
    $factura_filter = '';
    if (isset($params['factura_id']) and !empty($params['factura_id'])) {
      $factura_filter = "factura_id = '".$params['factura_id']."'";
    }

    $sql = "SELECT * FROM ".self::$table." WHERE $factura_filter ORDER BY fecha DESC";
    return parent::build_resources($sql, __CLASS__);
  }
}
