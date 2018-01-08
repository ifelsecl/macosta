<?php
require 'SplClassLoader.php';

class Logistica {

  static $root;

  static function initialize() {
    self::register_autoloader();
    DBManager::connect();
    self::load_tasks();
    self::set_locale();
  }

  static function register_autoloader() {
    $loader = new SplClassLoader;
    $loader->register();
  }

  static function unregister_autoloaders() {
    $functions = spl_autoload_functions();
    foreach ($functions as $function) {
      spl_autoload_unregister($function);
    }
  }

  static function load_tasks() {
    $result = DBManager::execute("SELECT * FROM tareas");
    while ($row = mysql_fetch_array($result)) {
      $name = strtoupper($row['nombre']);
      if (! defined($name)) define($name, $row['nombre']);
    }
  }

  static function set_locale() {
    setlocale(LC_ALL, array('Spanish_Colombia.1252','Spanish', 'es_ES'));
    date_default_timezone_set("America/Bogota");
  }

  static function respond_as_json() {
    if (! headers_sent()) header('content-type: application/json');
  }

  static function not_found() {
    if (! headers_sent()) header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");;
  }

  static function check_nonce($attr, $nonce) {
    if (! isset($attr) or ! nonce_is_valid($nonce, $attr)) {
      include Logistica::$root."mensajes/id.php";
      exit;
    }
  }
}
