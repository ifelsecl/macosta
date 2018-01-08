<?php
class Embalaje extends Base {

  static $attributes = array('nombre', 'id', 'descripcion', 'tipo_cobro');

  static $types = array(
    array('nombre' => 'Caja', 'descripcion' => 'Permite que un embalaje se pueda cobrar por unidad, kilo o kilo volumen.'),
    array('nombre' => 'Caja2', 'descripcion' => 'Permite que un embalaje se pueda cobrar por unidad o kilo, prefiriendo el Kilo.'),
    array('nombre' => 'Unidad', 'descripcion' => 'La mercancia se cobra por unidad'),
    array('nombre' => 'Kilo', 'descripcion' => 'Se cobra por el peso de la mercancia'),
    array('nombre' => 'Kilo Volumen', 'descripcion' => 'Se cobra por kilo volumen'),
    array('nombre' => 'Porcentaje', 'descripcion' => 'Se cobra el porcentaje indicado del valor de la mercancia'),
    array('nombre' => 'Viaje Convenido', 'descripcion' => 'Se cobra directamente el valor indicado'),
    array('nombre' => 'Descuento', 'descripcion' => 'Descuento pactado por cantidad unidades transportadas')
  );

  function __construct($params = array()) {
    $this->set_defaults();
    parent::__construct($params);
  }

  static $table = 'embalajes';

  static function all($return_sql = false) {
    $sql = "SELECT * FROM ".self::$table;
    if ($return_sql) return $sql;
    return parent::build_resources($sql);
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT * FROM ".self::$table." WHERE id='$id'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  static function autocomplete($nombre) {
    $nombre = DBManager::escape($nombre);
    $datos = array();
    $sql = "SELECT * FROM embalajes
WHERE nombre LIKE '%$nombre%' OR id LIKE '%$nombre%'";
    $result = DBManager::execute($sql);
    while ($row = mysql_fetch_assoc($result)) {
      $datos[] = array(
        "id" => $row['id'],
        "value" => $row['nombre'],
        'tipo_cobro' => $row['tipo_cobro']
      );
    }
    return $datos;
  }

  static function where($params) {
    $sql = "SELECT e.*, l.precio, l.seguro FROM embalajes e, listaprecios l
WHERE l.idembalaje=e.id AND l.idcliente='".$params['id_cliente']."' AND
l.idciudaddestino='".$params['id_ciudad_contacto']."'";
    return parent::build_resources($sql);
  }

  /**
   * Obtiene todos los embalajes que coincidan en el nombre o el código con una expresión.
   *
   * @param   string $nombre la expresión.
   * @param   int $idcliente el ID del cliente.
   * @param   int $idciudadorigen el ID de la ciudad origen.
   * @param   int $idciudaddestino el ID de la ciudad destino.
   * @author  Edgar Ortega Ramírez
   * @since Marzo 28, 2011
   */
  static function ObtenerEmbalajesPorDestino($idcliente,$idciudadorigen,$idciudaddestino) {
    $agregado = false;
    $query="SELECT e.*, l.precio, l.seguro FROM embalajes e, listaprecios l
WHERE l.idembalaje=e.id AND l.idcliente='$idcliente' AND
l.idciudadorigen='$idciudadorigen' AND l.idciudaddestino='$idciudaddestino'";
    if (! $result= DBManager::execute($query)) return 'no';
    if (DBManager::rows_count($result) == 0) {
      return 'no';
    }
    $combo = '<option value="">Selecciona...</option>';
    while ($row = mysql_fetch_array($result)) {
      $combo .= '<option name="'.$row['seguro'].'" value="'.$row['id'].'" title="'.$row['descripcion'].'">'.$row['nombre'].'</option>';
    }
    return $combo;
  }

  static function create($params) {
    return parent::_create($params);
  }

  /**
   * Edita la información de un embalaje
   *
   * @param   int $id el ID del embalaje.
   * @param   string $nombre
   * @param   string $descripcion
   * @since Agosto 9, 2011
   */
  function Editar($id, $nombre, $descripcion, $tipo_cobro) {
    $nombre = DBManager::escape($nombre);
    $descripcion = DBManager::escape($descripcion);
    $query = "UPDATE embalajes SET nombre='$nombre',
descripcion='$descripcion', tipo_cobro='$tipo_cobro'
WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Crea un nuevo embalaje.
   * @since Enero 13, 2012
   */
  function Crear($nombre, $descripcion, $tipo_cobro) {
    $q="INSERT INTO embalajes VALUES(NULL,'$nombre','$descripcion','$tipo_cobro')";
    return DBManager::execute($q);
  }
}
