<?php
require_once 'database_config.php';
class DBManager{

  /**
   * MySQL link identifier.
   * @var resource
   */
  private $link;

  /**
   * MySQL Connection ID.
   * @var int
   */
  private $id;

  public $error;
  public $error_no;

  /**
   * Intenta establecer una conexión con la base de datos.
   *
   * @param string $db el server de base de datos: 'mysql'.
   * @return  <tt>true</tt> si la conexión es exitosa y la base de datos fue
   *      seleccionada; <tt>false</tt> en caso contrario.
   * @version 1.2 - Julio 14, 2011
   */
  public function conectar($db='mysql') {
    if ($db=='mysql') {
      if (! $link = mysql_connect(DATABASE_SERVER, DATABASE_USER, DATABASE_PASSWORD)) {
        $this->error="<h2>No se pudo estableecer una conexion con la base de datos</h2>";
        return false;
      }
      if (! mysql_select_db(APP_DATABASE_NAME, $link)) {
        $this->error="<h2>No existe la base de datos</h2>";
        return false;
      }
      $this->link=$link;
      $this->id=mysql_thread_id($this->link);
      mysql_query("SET NAMES 'utf8'");
      return true;
    }
  }

  static function connect() {
    $link = mysql_connect(DATABASE_SERVER, DATABASE_USER, DATABASE_PASSWORD);
    if (! $link) {
      throw new Exception("<h2>No se pudo establecer una conexion con la base de datos</h2>");
    }
    if (! mysql_select_db(APP_DATABASE_NAME, $link)) {
      throw new Exception("<h2>No existe la base de datos</h2>");
    }
    mysql_query("SET NAMES 'utf8'");
  }

  static function execute($sql) {
    if ($result = mysql_query($sql)) return $result;
    else throw new Exception( mysql_error() );
  }

  static function rows_count($result) {
    return mysql_num_rows($result);
  }

  static function count($sql) {
    return mysql_result(self::execute($sql), 0, 'count');
  }

  static function escape($string) {
    return mysql_real_escape_string(strip_tags($string));
  }

  static function select($sql, $return_object = true) {
    $r = self::execute($sql);
    if (self::rows_count($r) == 0) return false;
    return $return_object ? mysql_fetch_object($r) : mysql_fetch_assoc($r);
  }

  /**
   * Iniciar una transacción.
   *
   * @param   string $db base de datos: mysql u oracle.
   * @since Julio 13, 2011
   */
  function IniciarTransaccion($db='mysql') {
    if ($db=='mysql') {
      if ($this->conectar()) {
        @mysql_query('SET AUTOCOMMIT=0', $this->link);
        return mysql_query('BEGIN',$this->link);
      }else{
        return false;
      }
    }
  }

  /**
   * Cancela una transacción.
   *
   * @param   string $db base de datos: mysql u oracle.
   * @since Julio 13, 2011
   */
  function CancelarTransaccion($db='mysql') {
    if ($db=='mysql') {
      if ($this->conectar()) {
        return mysql_query('ROLLBACK',$this->link);
      }else{
        $this->error=mysql_error($this->link);
        $this->error_no=mysql_errno($this->link);
        return false;
      }
    }
  }

  /**
   * Confirma una transacción.
   *
   * @param   string $db base de datos: mysql u oracle.
   * @since Julio 13, 2011
   */
  function ConfirmarTransaccion($db='mysql') {
    if ($db=='mysql') {
      if ($this->conectar()) {
        return mysql_query('COMMIT',$this->link);
      }else{
        $this->error=mysql_error($this->link);
        $this->error_no=mysql_errno($this->link);
        return false;
      }
    }
  }
}
