<?php
class Personal extends Base {

  static $table = 'personal';
  static $attributes = array('tipo_identificacion', 'numero_identificacion',
    'nombre', 'primer_apellido', 'segundo_apellido', 'direccion', 'id_ciudad',
    'telefono', 'telefono2', 'celular', 'conductor', 'ayudante');

  function __construct($params) {
    parent::__construct($params);
  }

  static function find($id) {

  }

  static function all($return_sql = true) {

  }
}
