<?php
require_once 'DBManager.php';

class Base {

  private $con;

  public function __set($name, $value) {
    if (gettype($value) == 'string') $value = str_replace(array('"', "'"), '', trim($value));
    $this->$name = $value;
  }

  public function __get($name) {
    return $this->$name;
  }

  function __construct($params = array()) {
    $this->set_defaults();
    if (! empty($params)) $this->set_attributes($params);
    if (! isset($this->con)) $this->con = new DBManager; //Compatibility with old code :(
  }

  private function set_attributes($params) {
    foreach ($params as $name => $value) $this->$name = $value;
    return $this;
  }

  static function find_record($sql) {
    if (! $attributes = DBManager::select($sql)) return false;
    return new static($attributes);
  }

  static function _create($params) {
    self::check_mass_assignment($params, static::$attributes);
    $columns = implode(',', array_keys($params));
    $values = '';
    foreach ($params as $key => $value) {
      $values .= (is_null($value) ? 'NULL' : "'$value'" ).',';
    }
    $values = substr($values, 0, -1);
    $sql = "INSERT INTO ".static::$table."($columns) VALUES($values)";
    DBManager::execute($sql);
    if (! isset($params['id']) or empty($params['id'])) {
      $sql = "SELECT LAST_INSERT_ID() id";
      $resource = DBManager::select($sql);
      $params['id'] = $resource->id;
    }
    return new static($params);
  }

  function set_defaults() {
    foreach (static::$attributes as $key) {
      if (! isset($this->$key)) $this->$key = null;
    }
  }

  function update_attributes($params) {
    self::check_mass_assignment($params, static::$attributes);
    self::__construct($params);
    $sql = '';
    foreach ($params as $key => $value) {
      if (is_null($value)) {
        $value = 'NULL';
      } elseif (! in_array($value, static::$attributes)) {
        $value = "'$value'";
      }
      $sql .= $key.'='.$value.',';
    }
    $sql = substr($sql, 0, -1);
    $sql = 'UPDATE '.static::$table.' SET '.$sql.' WHERE id="'.$this->id.'"';
    return DBManager::execute($sql);
  }

  function updated_attributes($array, $return_html = true) {
    $changed = false;
    $changes = '<ul>';
    $modified_attributes = array();
    foreach ($array as $key => $value) {
      if (property_exists($this, $key) and $this->$key != $value) {
        $modified_attributes[$key] = $value;
        $changed = true;
        $changes .= '<li>'.ucwords(str_replace('_', ' ', $key)).': ';
        if ($this->$key == '') $this->$key = 'vacio';
        if ($value == '') $value = 'vacio';
        $changes .= "era <i>".$this->$key."</i> ahora <i>".filter_var($value, FILTER_SANITIZE_STRING)."</i></li>";
      }
    }
    $changes .= '</ul>';
    return $changed ? ($return_html ? $changes : $modified_attributes) : false;
  }

  static function build_resources($sql, $class = 'stdClass') {
    $result = DBManager::execute($sql);
    $resources = array();
    while ($resource = mysql_fetch_object($result, $class)) {
      $resources[] = $resource;
    }
    return $resources;
  }

  static function check_mass_assignment($params, $valid_attributes) {
    if (empty($params)) return true;
    foreach ($params as $key => $value) {
      if (! in_array($key, $valid_attributes)) throw new UnexpectedValueException("Unknow column '$key'");
    }
  }
}
