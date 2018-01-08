<?php
require_once "DBManager.php";

class Camiones {

  /**
   * Mantiene la conexión con la base de datos.
   * @var DBManager
   */
  public $con;

  static $table = 'camiones';

  public function __set($name, $value) {
    if (gettype($value) == 'string') $value = addslashes($value);
    $this->$name = $value;
  }

  public function __get($name) {
    return $this->$name;
  }

  function __construct($params = array()) {
    foreach ($params as $name => $value) {
      $this->$name = $value;
    }
    $this->con = new DBManager;
  }

  function all($cuales = 'todos'){
    if ($cuales == 'todos') {
      $where = '';
    } else {
      $where = 'WHERE activo = ';
      if($cuales == 'activos') $where .= '"si"';
      elseif($cuales == 'anulados') $where .= '"no"';
    }

    $sql = "SELECT * FROM ".self::$table." $where ORDER BY placa";
    $vehiculos = array();
    $result = DBManager::execute($sql);
    while ($vehiculo = mysql_fetch_object($result, __CLASS__)) {
      $vehiculos[] = $vehiculo;
    }
    return $vehiculos;
  }

  /**
   * Selecciona todos los datos de todos los camiones, ésta función es usada para exportar.
   * @param string $formato "XLS" o "CSV", el formato indica que campos serán retornados.
   * @since Abril 21, 2011
   * @author  Edgar Ortega Ramirez
   */
  function Exportar($formato) {
    if (strtoupper($formato) == "XLS") {
      $query = "SELECT c.*, m.Descripcion marca, p.tipo_identificacion tipo_identificacion_p,
p.nombre nombre_p, p.primer_apellido primer_apellido_p,
p.segundo_apellido segundo_apellido_p, p.razon_social razon_social_p,
ten.tipo_identificacion tipo_identificacion_t, ten.nombre nombre_t,
ten.primer_apellido primer_apellido_t,
ten.segundo_apellido segundo_apellido_t, ten.razon_social razon_social_t,
l.descripcion linea, col.Descripcion color,
car.descripcion carroceria, con.configuracion configuracion, a.nombre aseguradora
FROM camiones c, lineas l, aseguradoras a, marcas m, terceros p,
colores col, carrocerias car, configuraciones con, terceros ten
WHERE ten.id=c.id_tenedor AND c.idpropietario=p.id
AND c.nitaseguradora=a.nit AND c.codigo_linea=l.codigo
AND c.codigo_Marcas=m.codigo_Marcas AND c.codigo_colores=col.codigo_colores
AND c.codigo_carrocerias=car.codigo_carrocerias
AND l.codigomarca=m.codigo_Marcas AND c.idconfiguracion=con.id";
    } elseif (strtoupper($formato)=="CSV") {
      $query = "SELECT * FROM camiones";
    }
    return DBManager::execute($query);
  }

  /**
   * Obtiene todos los camiones.
   *
   * @param string $cuales 'Todos', 'Anulados' o 'Activos'
   * @param string $modo 'SQL' para retornar la consulta y cualquier otro valor para retornar el resultado de la consulta.
   * @since Junio 17, 2011
   */
  function Obtener($cuales,$modo){
    $sql = "SELECT c.*, t.nombre t_nombre, t.primer_apellido t_primer_apellido,
t.segundo_apellido t_segundo_apellido, t.razon_social t_razon_social, t.tipo_identificacion t_tipo_identificacion
FROM camiones c, terceros t WHERE t.id=c.idpropietario";
    if($cuales=='Anulados'){
      $sql.=" AND c.activo='no'";
    }elseif($cuales=='Activos'){
      $sql.=" AND c.activo='si'";
    }
    if (strtoupper($modo)=="SQL") {
      return $sql;
    } else {
      return DBManager::execute($sql);
    }
  }

  /**
   * Agrega un nuevo camión a la base de datos.
   * @param string $placa
   * @param string $marca
   * @param string $linea
   * @param string $modelo
   * @param string $serie
   * @param string $color
   * @param string $carroceria
   * @param string $configuracion
   * @param double $peso
   * @param string $registro
   * @param int $aseguradora
   * @param string $capacidadcarga
   * @param int $idpropietario
   * @param string $soat
   * @param string $fechasoat
   * @param string $seguro
   * @param string $fechaseguro
   * @param string $tarjetaoperacion
   * @param string $fechatarjeta
   * @param string $tecnicomecanica
   * @param string $fechatecnicomecanica
   * @param string $modelo_repotenciado
   * @param string $placa_semiremolque
   * @param int $id_tenedor el ID del tenedor del camión.
   * @param string $activo 'si' o 'no'.
   * @since Mayo 10, 2011
   * @author  Edgar Ortega Ramírez
   */
  function Agregar($placa,$marca,$linea,$modelo,$serie,$color,$carroceria,$configuracion,$peso,$registro,
          $aseguradora,$capacidadcarga,$id_propietario,$soat,$fechasoat,$seguro,$fechaseguro,
          $tarjetaoperacion,$fechatarjeta,$tecnicomecanica,$fechatecnicomecanica,
          $modelo_repotenciado,$placa_semiremolque,$id_tenedor,$km_inicial, $km_actual,
          $numero_ejes, $tipo_combustible, $fecha_afiliacion, $activo='si') {
    $placa = strtoupper($placa);
    $placa_semiremolque = strtoupper($placa_semiremolque);
    $activo = strtolower($activo);
    $query="INSERT INTO camiones(placa, codigo_Marcas, codigo_linea, codigo_colores,
codigo_carrocerias, idpropietario, modelo, serie, num_seguro, f_venc_seguro, soat,
f_venc_soat, t_operacion, f_venc_toperacion, tecnico_meca, f_venc_tmec, peso, activo,
fechamodificacion, idconfiguracion, registro, nitaseguradora, capacidadcarga,
modelo_repotenciado, placa_semiremolque, id_tenedor, km_inicial, km_actual, numero_ejes,
tipo_combustible, unidad_medida_capacidad_carga, fecha_afiliacion)
VALUES('$placa','$marca','$linea',
'$color','$carroceria','$id_propietario','$modelo','$serie',
'$seguro','$fechaseguro','$soat','$fechasoat','$tarjetaoperacion',
'$fechatarjeta','$tecnicomecanica','$fechatecnicomecanica',
'$peso','$activo','','$configuracion','$registro','$aseguradora',
'$capacidadcarga','$modelo_repotenciado','$placa_semiremolque',
'$id_tenedor','$km_inicial', '$km_actual','$numero_ejes','$tipo_combustible', 1, '$fecha_afiliacion')";
    return DBManager::execute($query);
  }

  /**
   * Comprueba si existe un camión con la placa indicada.
   * @param string $placa la placa del camión.
   * @since Junio 9, 2011
   */
  function Existe($placa) {
    $query="SELECT placa FROM camiones WHERE placa='$placa'";
    $result=DBManager::execute($query);
    if (DBManager::rows_count($result) == 1) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Verifica los documentos de un camión.
   *
   * @param string $placa
   * @since Julio 6, 2011
   */
  function VerificarDocumentos($placa) {
    $query="SELECT * FROM camiones WHERE placa='$placa'";
    $result=DBManager::execute($query);
    $row=mysql_fetch_assoc($result);
    $vencidos['vencido']='no';
    $hoy=date('Y-m-d');
    if ($row['f_venc_soat'] < $hoy) {
      $vencidos['vencido']='si';
      $vencidos['SOAT']=$row['f_venc_soat'];
    }
    if ($row['f_venc_toperacion'] < $hoy) {
      $vencidos['vencido']='si';
      $vencidos['Tarjeta de operacion']=$row['f_venc_toperacion'];
    }
    if ($row['f_venc_tmec'] < $hoy) {
      $vencidos['vencido']='si';
      $vencidos['Tecnico mecanica']=$row['f_venc_tmec'];
    }
    if($row['f_venc_seguro'] < $hoy) {
      $vencidos['vencido']='si';
      $vencidos['Seguro']=$row['f_venc_seguro'];
    }
    return $vencidos;
  }
}
