<?php
class Factura extends Base {

  static $attributes = array('id', 'idcliente', 'id_usuario', 'condicionpago',
    'fechaemision', 'fechavencimiento', 'valorflete', 'valorseguro',
    'descuento', 'total', 'activa', 'fechamodificacion', 'reportada',
    'estado', 'fecha_pago', 'observaciones_pago', 'id_vendedor', 'nc', 'tipo', 'total_pagos');

  static $estados = array('Abierta', 'Cerrada', 'Pagada');
  static $tipos = array('CREDITO', 'CONTADO', 'FLETE AL COBRO');

  static $table = "facturas";

  /**
   * Activa una factura que ha sido anulada.
   *
   * @param   int $id el ID de la factura.
   * @since Julio 1, 2011
   * @version 1.2 - Julio 25, 2011
   */
  function Activar($id) {
    $fecha=date("Y-m-d H:i:s");
    $query="UPDATE ".self::$table." SET activa='si', fechamodificacion='$fecha' WHERE id=$id";
    return DBManager::execute($query);
  }

  /**
   * Anula una factura, la factura no podrá ser usada.
   *
   * @param   int $id el ID de la factura.
   * @since Julio 1, 2011
   * @version 1.2 - Julio 25, 2011
   */
  function Anular($id) {
    $fecha=date("Y-m-d H:i:s");
    $sql = "UPDATE ".self::$table." SET activa = 'no', fechamodificacion = '$fecha' WHERE id= '$id'";
    if (! DBManager::execute($sql)) return false;
    $sql = "UPDATE guias SET idfactura=NULL, idestado=4 WHERE idfactura=$id";
    return DBManager::execute($sql);
  }

  /**
   * Actualiza una factura.
   *
   * @param int $id_factura
   * @param double $valor_flete
   * @param double $valor_seguro
   * @param double $descuento
   * @since Julio 6, 2011
   */
  function Actualizar($id_factura, $valor_flete, $valor_seguro, $descuento) {
    $total = $valor_flete - $descuento + $valor_seguro;
    $sql = "UPDATE ".self::$table." SET activa='si', valorflete='$valor_flete',
valorseguro='$valor_seguro', descuento='$descuento', total='$total' WHERE id=$id_factura";
    return DBManager::execute($sql);
  }

  /**
   * Guarda una factura inicialmente, la fecha de vencimiento de la factura se
   * calcula automaticamente.
   *
   * @param int $id_cliente el ID del cliente.
   * @param int $id_usuario el ID del usuario que factura.
   * @param string $fecha_emision la fecha de emisión de la factura en formato yyyy-mm-dd.
   * @param int $condicion_pago numero de días.
   * @param string $activa 'si' o 'no', indica si la factura está activa, por defecto 'no'.
   * @since Julio 6, 2011
   * @author  Edgar Ortega Ramírez
   */
  function GuardarFacturaInicial($id_cliente, $id_usuario, $fecha_emision, $condicion_pago, $id_vendedor, $activa='no') {
    $fecha = explode("-", $fecha_emision);
    $fecha_vencimiento = date("Y-m-d", mktime(0, 0, 0, $fecha[1], $fecha[2]+$condicion_pago, $fecha[0]));

    $row = DBManager::select("SELECT MIN(id) as id FROM facturas_eliminadas");
    if (is_null($row->id)) {
      $query1 = "LOCK TABLES ".self::$table." WRITE";
      $query2 = "INSERT INTO ".self::$table."(idcliente,id_usuario,
condicionpago,fechaemision,fechavencimiento,id_vendedor,activa) VALUES
('$id_cliente','$id_usuario','$condicion_pago','$fecha_emision',
'$fecha_vencimiento','$id_vendedor','$activa')";
      $query3 = "SELECT LAST_INSERT_ID()";
      $query4 = "UNLOCK TABLES";
      $result1 = DBManager::execute($query1);
      $result2 = DBManager::execute($query2);
      $result3 = DBManager::execute($query3);
      $result4 = DBManager::execute($query4);
      if (! $row = mysql_fetch_array($result3)) return false;
      return $row[0];
    } else {
      $insert_sql = "INSERT INTO ".self::$table."(id, idcliente,
id_usuario,condicionpago,fechaemision,fechavencimiento,id_vendedor,activa)
VALUES ('".$row->id."', '$id_cliente','$id_usuario','$condicion_pago',
'$fecha_emision','$fecha_vencimiento','$id_vendedor','$activa')";
      $delete_sql = "DELETE FROM facturas_eliminadas WHERE id=".$row->id;
      DBManager::execute($insert_sql);
      DBManager::execute($delete_sql);
      return $row->id;
    }
  }

  static function create($params) {
    $resource = DBManager::select("SELECT MIN(id) as id FROM facturas_eliminadas");
    if (! is_null($resource->id)) {
      $params['id'] = $resource->id;
    }
    if ($factura = Factura::_create($params)) {
      if (! is_null($resource->id)) {
        $delete_sql = "DELETE FROM facturas_eliminadas WHERE id=".$resource->id;
        DBManager::execute($delete_sql);
      }
      return $factura;
    } else {
      return false;
    }
  }

  /**
   * Selecciona todas las guias asociadas a una factura.
   * @param int $id_factura
   * @since Julio 6, 2011
   */
  function ObtenerTotales($id_factura) {
    $query="SELECT SUM(total) total, SUM(valorseguro) total_seguro
FROM ".Guia::$table." WHERE idfactura=$id_factura";
    return DBManager::execute($query);
  }

  function total() {
    $sql = 'SELECT SUM(total) total, SUM(valorseguro) seguro
FROM '.Guia::$table.' WHERE idfactura='.$this->id;
    $result = DBManager::select($sql);
    $this->seguro = round($result->seguro);
    return $this->total = round($result->total);
  }

  function fecha_emision_corta() {
    return strftime('%d/%b/%Y', strtotime($this->fechaemision));
  }

  function fecha_vencimiento_corta() {
    return strftime('%d/%b/%Y', strtotime($this->fechavencimiento));
  }

  function seguro() {
    return round($this->valorseguro);
  }

  function flete() {
    return round($this->valorflete);
  }

  function descuento() {
    return round($this->descuento);
  }

  /**
   * Asigna las guias indicadas a una factura.
   *
   * @param int $id_factura el ID de la factura.
   * @param   array $guias un array conteniendo las guias a ser incluidas en la factura.
   * @since Noviembre 16, 2011
   */
  function AsignarGuiasArray($id_factura, $guias) {
    $a = array('error' => 'no', 'mensaje' => '');
    if (empty($guias)) {
      $a = array('error' => 'si', 'mensaje' => 'No se han indicado guias');
      return $a;
    }
    if (! is_array($guias)) $guias = array($guias);
    foreach ($guias as $guia) {
      $sql = "UPDATE ".Guia::$table." SET idfactura=$id_factura, id_estado_anterior=idestado, idestado=2 WHERE id=$guia";
      if (DBManager::execute($sql)) {
        Logger::guia($guia, 'facturó la guía');
      } else {
        $a = array('error' => 'si', 'mensaje' => 'Ocurrio un error al actualizar la guia.');
      }
    }
    return $a;
  }

  function add_guias($guias) {
    $a = array('error' => 'no', 'mensaje' => '');
    if (empty($guias)) {
      $a = array('error' => 'si', 'mensaje' => 'No se han indicado guias');
      return $a;
    }
    if (! is_array($guias)) $guias = array($guias);
    foreach ($guias as $guia) {
      $sql = "UPDATE ".Guia::$table." SET idfactura=$this->id, id_estado_anterior=idestado, idestado=2 WHERE id=$guia";
      if (DBManager::execute($sql)) {
        Logger::guia($guia, 'facturó la guía');
      } else {
        $a = array('error' => 'si', 'mensaje' => 'Ocurrio un error al facturar la guia.');
      }
    }
    return $a;
  }

  /**
   * Cambia el estado de una factura.
   * @param int $id_factura el ID de la factura.
   * @param string $estado 'Abierta' o 'Cerrada'.
   * @since Noviembre 17, 2011
   */
  function CambiarEstado($id_factura, $estado) {
    $q="UPDATE ".self::$table." SET estado='$estado' WHERE id=$id_factura";
    return DBManager::execute($q);
  }

  function destroy() {
    $q = "DELETE FROM pagos WHERE factura_id = '$this->id'";
    if (! DBManager::execute($q)) return false;
    $q = "UPDATE ".Guia::$table." SET idfactura=NULL, idestado=4 WHERE idfactura='$this->id'";
    if (! DBManager::execute($q)) return false;
    $q = "INSERT INTO facturas_eliminadas VALUES('$this->id')";
    if (! DBManager::execute($q)) echo mysql_error();
    $q = "DELETE FROM ".self::$table." WHERE id=$this->id";
    return DBManager::execute($q);
  }

  /**
   * Obtiene la cantidad de facturas abiertas.
   * @since Diciembre 12, 2011
   */
  function FacturasAbiertas() {
    $sql = "SELECT COUNT(*) count FROM ".self::$table." WHERE estado='Abierta'";
    return DBManager::count($sql);
  }

  /**
   * Cierra todas las facturas que están abiertas.
   * @since Diciembre 12, 2011
   */
  function Cerrar() {
    $q="UPDATE ".self::$table." SET estado='Cerrada' WHERE estado='Abierta'";
    return DBManager::execute($q);
  }

  /**
   * Edita los datos de una factura.
   * @since Enero 12, 2012
   */
  function Editar($id, $fecha_emision, $condicion_pago, $estado, $fecha_pago, $observaciones_pago, $tipo) {
    $fecha=explode("-", $fecha_emision);
    $fecha_vencimiento=date("Y-m-d",mktime(0,0,0,$fecha[1],$fecha[2]+$condicion_pago,$fecha[0]));
    $q="UPDATE ".self::$table."
SET condicionpago='$condicion_pago', fechaemision='$fecha_emision',
fechavencimiento='$fecha_vencimiento', estado='$estado', fecha_pago='$fecha_pago',
observaciones_pago='$observaciones_pago', tipo='$tipo'
WHERE id=$id";
    return DBManager::execute($q);
  }

  /**
   * Crea una nota credito para una factura.
   * @since 10 Julio 2012
   */
  static function CrearNotaCredito($factura, $fecha, $concepto, $valor) {
    $factura=addslashes($factura);
    $fecha=addslashes($fecha);
    $concepto=addslashes($concepto);
    $valor=addslashes($valor);
    $q="INSERT INTO nc(fecha, concepto, valor) VALUES('$fecha','$concepto','$valor')";
    if (! DBManager::execute($q)) return false;
    $q="SELECT LAST_INSERT_ID()";
    $result=DBManager::execute($q);
    $row=mysql_fetch_array($result);
    $q="UPDATE ".self::$table." SET nc=".$row[0]." WHERE id=$factura";
    return DBManager::execute($q);
  }

  static function EditarNotaCredito($id, $fecha, $concepto, $valor) {
    $fecha=addslashes($fecha);
    $concepto=addslashes($concepto);
    $valor=addslashes($valor);
    $q="UPDATE nc SET fecha='$fecha', concepto='$concepto', valor='$valor' WHERE id=$id";
    return DBManager::execute($q);
  }

  static function ObtenerNotaCredito($id) {
    $q = "SELECT * FROM nc WHERE id='$id'";
    $result = DBManager::execute($q);
    return mysql_fetch_assoc($result);
  }

  /**
   * Obtiene el total facturado por mes, a todos los clientes o a un cliente.
   *
   * @since Octubre 3, 2012
   * @param mixed $cliente FALSE para todos los clientes o el ID de un cliente.
   * @param int $meses La cantidad de meses a incluir
   * @param string $tipo Grafico por 'DIA' o por 'MES'
   */
  static function FacturacionMensual($cliente, $mes, $tipo) {
    $response = array('nombres' => array(), 'cantidades' => array());
    $t = '';
    if ($mes == 'ACTUAL') {
      $inicio = date('Y-m-d', strtotime('first day of this month'));
      $fin = date('Y-m-d');
      $t = ucfirst(strftime('%B', strtotime('this month')));
    }
    if ($mes == 'ANTERIOR') {
      $inicio = date('Y-m-d', strtotime('first day of last month'));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', strtotime('-1 month')).' - '.strftime('%B %Y');
    }
    if ($mes == '3MESES') {
      $s = strtotime('-3 months');
      $n = date('F Y',$s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
    }
    if ($mes == '6MESES') {
      $s = strtotime('-6 months');
      $n = date('F Y',$s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
      $tipo = 'MES';
    }
    if ($mes == '12MESES') {
      $s = strtotime('-1 year');
      $n = date('F Y',$s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
      $tipo = 'MES';
    }
    if ($mes == '24MESES') {
      $s = strtotime('-2 year');
      $n = date('F Y',$s);
      $inicio = date('Y-m-d', strtotime('first day of '.$n));
      $fin = date('Y-m-d');
      $t = strftime('%B %Y', $s).' - '.strftime('%B %Y');
      $tipo = 'MES';
    }
    $f_c = $cliente ? " AND f.idcliente=$cliente" : '';
    if ($tipo == 'MES') {
      $select = 'YEAR( f.fechaemision ) ano, MONTH( f.fechaemision ) mes, ';
      $groupby = 'GROUP BY ano, mes';
    } else {
      $select = 'f.fechaemision fecha, ';
      $groupby = 'GROUP BY f.fechaemision';
    }
    $q = "SELECT $select SUM( g.total + g.valorseguro ) total
FROM ".self::$table." f, ".Guia::$table." g
WHERE g.idfactura = f.id AND f.fechaemision BETWEEN '$inicio' AND '$fin' $f_c
$groupby";
    $result = DBManager::execute($q);
    $n = DBManager::rows_count($result);
    if ($n != 0) {
      $total = 0;
      while ($f = mysql_fetch_assoc($result)) {
        if ($tipo == 'MES') {
          $response['nombres'][] = strftime('%b %y', strtotime($f['ano'].'-'.$f['mes'].'-01'));
        } else {
          $response['nombres'][] = strftime('%d/%b/%Y', strtotime($f['fecha']));
        }
        $n++;
        $total += $f['total'];
        $response['cantidades'][]=intval($f['total']);
      }
      $response['total'] = intval($total);
      $response['prom'] = intval($total/$n);
    }
    $response['texto'] = $t;
    return $response;
  }

  /**
   * Obtiene los clientes mas facturas en el periodo indicado.
   *
   * @since Octubre 16, 2012
   * @param int $mes La cantidad de meses a incluir
   */
  static function ClientesMasFacturados($mes, $limit) {
    $ciud=array('nombres'=>array(),'cantidades'=>array());
    $t='';
    if ($mes=='ACTUAL') {
      $inicio=date('Y-m-d',strtotime('first day of this month'));
      $fin=date('Y-m-d',strtotime('last day of this month'));
      $t=ucfirst(strftime('%B',strtotime('this month')));
    }
    if ($mes=='ANTERIOR') {
      $inicio=date('Y-m-d',strtotime('first day of last month'));
      $fin=date('Y-m-d',strtotime('last day of last month'));
      $t=ucfirst(strftime('%B',strtotime('last month')));
    }
    if ($mes=='3MESES') {
      $s=strtotime('-3 months');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
    }
    if ($mes=='6MESES') {
      $s=strtotime('-6 months');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
      $tipo='MES';
    }
    if ($mes=='12MESES') {
      $s=strtotime('-1 year');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
      $tipo='MES';
    }
    if ($mes=='24MESES') {
      $s=strtotime('-2 year');
      $n=date('F Y',$s);
      $inicio=date('Y-m-d',strtotime('first day of '.$n));
      $fin=date('Y-m-d');
      $t=strftime('%B %Y',$s).' - '.strftime('%B %Y');
      $tipo='MES';
    }

    $q = "SELECT SUM(g.total+g.valorseguro) total, c.tipo_identificacion AS ti,
c.nombre AS n, c.primer_apellido AS pa
FROM ".self::$table." f, ".Guia::$table." g, ".Cliente::$table." c
WHERE f.id=g.idfactura AND c.id=f.idcliente AND f.fechaemision BETWEEN '$inicio' AND '$fin'
GROUP BY c.id
ORDER BY total DESC
LIMIT 0,$limit";
    $result = DBManager::execute($q);
    if (DBManager::rows_count($result) != 0) {
      while ($f = mysql_fetch_assoc($result)) {
        $ciud['nombres'][] = trim($f['n'].' '.$f['pa']);
        $ciud['cantidades'][] = intval($f['total']);
      }
    }
    $ciud['texto'] = $t;
    return $ciud;
  }

  function guias() {
    $sql = "SELECT g.*, SUM(i.peso) peso, SUM(i.kilo_vol) kilo_vol, SUM(i.unidades) unidades,
co.nombre contacto_nombre, co.primer_apellido contacto_primer_apellido,
co.segundo_apellido contacto_segundo_apellido, cid.nombre contacto_ciudad_nombre
FROM ".Guia::$table." g, ".Cliente::$table." co, ".Ciudad::$table." cid, items i
WHERE co.id=g.idcontacto AND cid.id=co.idciudad AND i.idguia=g.id AND g.idfactura='$this->id' GROUP BY g.id";
    return $this->guias = parent::build_resources($sql, 'Guia');
  }

  function cliente() {
    $cliente = new Cliente;
    return isset($this->cliente) ? $this->cliente : $this->cliente = $cliente->find($this->idcliente);
  }

  function resolucion() {
    $resolucion = Resolucion::find_by_tipo_and_numero('facturacion', $this->id);
    if (! $resolucion) exit('No se encontró una resolución para la factura.');
    return $this->resolucion = $resolucion;
  }

  function history() {
    $sql = "SELECT * FROM ".LOGS_DATABASE_NAME.".log_logistica WHERE ROUND(id_modulo) = '".intval($this->id)."'
AND modulo = 'Facturacion' ORDER BY fecha DESC";
    return $this->history = parent::build_resources($sql);
  }

  function total_neto() {
    return round($this->valorflete + $this->valorseguro - $this->descuento);
  }

  function total_pagos() {
    return round($this->total_pagos);
  }

  function add_payment($payment) {
    $pagos = round($this->total_pagos + $payment->valor);
    $params = array('total_pagos' => $pagos);
    if ($pagos >= $this->total) $params['estado'] = 'Pagada';
    return $this->update_attributes($params);
  }

  function pagos() {
    $params = array('factura_id' => $this->id);
    return Pago::where($params);
  }

  function is_paid() {
    return $this->total_pagos >= $this->total;
  }

  function is_overdue() {
    return $this->fechavencimiento > date('Y-m-d');
  }

  function saldo() {
    return $this->total - $this->total_pagos;
  }

  static function find($id) {
    $id = DBManager::escape($id);
    $sql = "SELECT f.*, CONCAT_WS(' ', c.nombre, c.primer_apellido, c.segundo_apellido) cliente_nombre_completo
FROM ".self::$table." f, ".Cliente::$table." c WHERE c.id=f.idcliente AND f.id='$id'";
    if (! $attributes = DBManager::select($sql)) return false;
    return new self($attributes);
  }

  static function find_by_ids($ids) {
    foreach ($ids as $key => $value) $ids[$key] = DBManager::escape($value);
    $ids = implode(',', $ids);
    $sql = "SELECT f.*, CONCAT_WS(' ', c.nombre, c.primer_apellido, c.segundo_apellido) cliente_nombre_completo
FROM ".self::$table." f, ".Cliente::$table." c WHERE c.id=f.idcliente AND f.id IN ($ids)";
    return parent::build_resources($sql, __CLASS__);
  }


  static function all($return_sql = false) {
    $sql = "SELECT f.*, CONCAT(c.nombre, ' ', c.primer_apellido, ' ', c.segundo_apellido) cliente_nombre_completo
FROM ".self::$table." f, ".Cliente::$table." c WHERE f.idcliente=c.id ORDER BY f.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }

  static function find_by_date($fecha) {
    $fecha = DBManager::escape($fecha);
    $sql = "SELECT f.*, CONCAT_WS(' ', c.nombre, c.primer_apellido, c.segundo_apellido) cliente_nombre_completo,
c.numero_identificacion cliente_numero_identificacion, c.tipo_identificacion cliente_tipo_identificacion,
c.digito_verificacion cliente_digito_verificacion, v.codigo_siigo vendedor_codigo_siigo
FROM ".self::$table." f, ".Cliente::$table." c, vendedores v
WHERE f.idcliente=c.id AND v.id=f.id_vendedor AND f.fechaemision='$fecha'";
    return parent::build_resources($sql, __CLASS__);
  }

  static function all_by_range($type, $id_cliente, $start, $end) {
    if (!is_null($id_cliente)) $id_cliente = DBManager::escape($id_cliente);
    $start      = DBManager::escape($start);
    $end        = DBManager::escape($end);
    $where = '';
    if ($type == 'fecha') {
      $where .= " AND (f.fechaemision BETWEEN '$start' AND '$end')";
    } else {
      $where .= " AND (f.id BETWEEN '$start' AND '$end')";
    }
    if (!is_null($id_cliente)) {
      $where .= " AND c.id='$id_cliente'";
    }
    $sql = "SELECT f.*, c.tipo_identificacion cliente_ti,
CONCAT_WS(' ', c.nombre, c.primer_apellido, c.segundo_apellido) cliente_nombre_completo
FROM ".self::$table." f, ".Cliente::$table." c
WHERE c.id=f.idcliente $where ORDER BY f.id";
    return parent::build_resources($sql, __CLASS__);
  }

  static function unpaid($cliente_id = null) {
    $f_cliente = '';
    if (! is_null($cliente_id)) $f_cliente = "AND idcliente = '$cliente_id'";
    $sql = "SELECT * FROM ".self::$table." WHERE total > total_pagos $f_cliente ORDER BY id LIMIT 50";
    return parent::build_resources($sql, __CLASS__);
  }

  static function search($params, $return_sql = false) {
    foreach ($params as $key => $value) {
      $params[$key] = DBManager::escape($value);
    }
    if (! isset($params['id']) or empty($params['id'])) $f_id = "";
    else $f_id = "AND f.id LIKE '%".$params['id']."%'";

    if (! isset($params['cliente']) or empty($params['cliente'])) $f_cliente = '';
    else $f_cliente = "AND (CONCAT(c.nombre,' ',c.primer_apellido,' ',c.segundo_apellido) LIKE '%".$params['cliente']."%')";

    if (! isset($params['estado']) or empty($params['estado'])) $f_estado = '';
    else $f_estado = "AND f.estado LIKE '%".$params['estado']."%'";

    if (! isset($params['fecha_emision']) or empty($params['fecha_emision'])) $f_fecha = '';
    else $f_fecha = "AND fechaemision LIKE '%".$params['fecha_emision']."%'";

    if (! isset($params['paid'])) $f_paid = "";
    elseif ($params['paid'] == true) $f_paid = "AND f.total_pagos >= f.total";
    else $f_paid = "AND f.total > f.total_pagos";

    $sql = "SELECT f.*, CONCAT(c.nombre, ' ', c.primer_apellido, ' ', c.segundo_apellido) cliente_nombre
FROM ".self::$table." f, ".Cliente::$table." c
WHERE f.idcliente=c.id $f_id $f_cliente $f_estado $f_fecha $f_paid
ORDER BY f.id DESC";
    if ($return_sql) return $sql;
    return parent::build_resources($sql, __CLASS__);
  }
}


