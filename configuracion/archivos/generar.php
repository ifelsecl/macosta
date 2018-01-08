<?php
require "../../seguridad.php";
if (! isset($_SESSION['permisos'][ARCHIVOS_ENTRAR])) {
  include Logistica::$root."mensajes/permiso.php";
  exit;
}
if (! isset($_REQUEST['fecha'])) {
  echo '<title>Logistica</title><h2>No se ha indicado la fecha, actualiza la pagina e intentalo nuevamente.</h2>';
  exit;
}
$f = explode('-', $_REQUEST['fecha']);
if (! isset($f[1]) or ! isset($f[2])) exit('<h2>Algo esta mal con la fecha que seleccionaste '.$_REQUEST['fecha'].'</h2>');

$configuracion = new Configuracion;

if (isset($_REQUEST['siigo'])) {
  $carpeta = "C:/backup_logistica/facturacion/".date('Md_ga')."/";
  if (! file_exists($carpeta)) {
    if (! mkdir($carpeta, 0777, true)) exit('No se pudo crear el directorio para el archivo...');
  }
  $facturas = Factura::find_by_date($_REQUEST['fecha']);
  if (empty($facturas)) exit('<h1>No se encontraron facturas :(</h1>');
  $nombre_archivo = "tma.txt";
  $ruta = $carpeta.$nombre_archivo;
  $f = fopen($ruta, "w+");
  fflush($f);
  header("Pragma: no-cache");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header('Content-type: text/plain; charset=UTF-8');
  header("Content-Disposition: attachment; filename=$nombre_archivo");

  foreach ($facturas as $factura) {
    $factura->total();

    $tipo = 'F';
    $consecutivo = 1;
    $codigo_comprobante = '001';
    $nit = str_pad($factura->cliente_numero_identificacion, 13, '0', STR_PAD_LEFT);
    $sucursal = $configuracion->siigo_sucursal;
    $codigo_producto = '0000000000000';
    $fecha = str_replace('-', '', $factura->fechaemision);;
    $centro_costo = $configuracion->siigo_centro_costo;
    $subcentro_costo = $configuracion->siigo_subcentro_costo;
    $base_retencion = '000000000000000';
    $codigo_vendedor = $factura->vendedor_codigo_siigo;
    $codigo_ciudad = $configuracion->siigo_codigo_ciudad;
    $codigo_zona = $configuracion->siigo_codigo_zona;
    $codigo_bodega = $configuracion->siigo_codigo_bodega;
    $codigo_ubicacion = $configuracion->siigo_codigo_ubicacion;
    $cantidad = '000000000000000';
    $tipo_documento_cruce = 'F';
    $codigo_comprobante_cruce = '001';
    $numero_documento_cruce = $factura->id;
    $fecha_vencimiento_documento_cruce = $fecha;
    $codigo_forma_pago = '0001';
    $codigo_banco = $configuracion->siigo_codigo_banco;
    // archivo 2
    $archivo2 = array(
      'tipo_documento_pedido' => ' ',
      'codigo_comprobante_pedido' => '000',
      'numero_comprobante_pedido' => '00000000000',
      'secuencia_pedido' => '000',
      'codigo_moneda' => '00',
      'tasa_cambio' => '000000000000000',
      'valor_movimiento_extranjera' => '000000000000000',
      'concepto_nomina' => '000',
      'cantidad_pago' => '000000000000',
      'porcentaje_descuento_movimiento' => '0000',
      'valor_descuento_movimiento' => '0000000000000',
      'porcentaje_cargo_movimiento' => '0000',
      'valor_cargo_movimiento' => '00000000000',
      'porcentaje_iva_movimiento' => '0000',
      'valor_iva_movimiento' => '0000000000000',
      'indicador_nomina' => 'N',
      'numero_pago' => ' ',
      'numero_cheque' => '00000000000',
      'indicador_tipo_movimiento' => 'N',
      'nombre_computador' => 'CONT',
      'estado_comprobante' => '   00',
      'peru_numero_comprobante' => '    ',
      'numero_documento_proveedor' => '00000000000',
      'prefijo_documento_proveedor' => '          ',
      'fecha_documento_proveedor' => '00000000',
      'precio_unitario_moneda_local' => '000000000000000000',
      'precio_unitario_moneda_extranjera' => '000000000000000000',
      'indicar_tipo_movimiento' => ' ',
      'veces_depreciar_activo' => '000',
      'secuencia_transaccion' => '00',
      'autorizacion_imprenta' => '0000000000',
      'secuencia_marcada_iva_coa' => 'A',
      'numero_caja' => '000',
      'numero_puntos_obtenidos' => '000000000000',
      'cantidad_dos' => '000000000000000',
      'cantidad_alterna_dos' => '000000000000000',
      'metodo_depreciacion' => ' ',
      'cantidad_factor_conversion' => '000000000000000000',
      'operador_factor_conversion' => '0',
      'factor_conversion' => '0000000000',
      'fecha_caducidad' => '00000000',
      'codigo_ice' => '00',
      'codigo_retencion' => '     ',
      'clase_retencion' => '0000',
      'codigo_motivo_devolucion' => '0000',
      'datos_mercancia_consignacion' => '                                            ',
      'numero_comprobante_fiscal_propio' => '                   ',
      'numero_comprobante_fiscal_proveedor' => '                   ',
      'indicador_tipo_letra' => ' ',
      'estado_letra' => ' ',
      'valor_movimiento_estado' => '                  ',
      'valor_movimiento_estado_extranjera' => '                  ',
      'codigo_medio_pago' => '000',
      'base_transaccion' => '000000000000000'
    );

    $archivo2 = implode('', $archivo2);

    $nombre_cliente = trim($factura->cliente_nombre_completo);

    //Total
    $descripcion_movimiento = str_pad(substr($nombre_cliente, 0, 50), 50);
    $debito_credito = 'D';
    $secuencia_documento_cruce = '001';
    $total = round($factura->total + $factura->seguro - $factura->descuento);
    $valor_movimiento = str_pad($total, 13, '0', STR_PAD_LEFT).'00';
    $cuenta = $factura->tipo == 'CREDITO' ? $configuracion->siigo_cuenta_contable_total_credito : $configuracion->siigo_cuenta_contable_total_contado;
    echo $linea = $tipo.$codigo_comprobante.'00000'.$factura->id.'0000'.$consecutivo.$nit.$sucursal.$cuenta.$codigo_producto.$fecha.$centro_costo.$subcentro_costo.$descripcion_movimiento.$debito_credito.$valor_movimiento.$base_retencion.$codigo_vendedor.$codigo_ciudad.$codigo_zona.$codigo_bodega.$codigo_ubicacion.$cantidad.$tipo_documento_cruce.$codigo_comprobante_cruce.'00000'.$numero_documento_cruce.$secuencia_documento_cruce.$fecha_vencimiento_documento_cruce.$codigo_forma_pago.$codigo_banco.$archivo2."\r\n";
    fwrite($f, $linea); //Copia de seguridad

    //Flete
    $consecutivo++;
    $debito_credito = 'C';
    $secuencia_documento_cruce = '00'.$consecutivo;
    $total = round($factura->total);

    $valor_movimiento = str_pad($total, 13, '0', STR_PAD_LEFT).'00';
    echo $linea = $tipo.$codigo_comprobante.'00000'.$factura->id.'0000'.$consecutivo.$nit.$sucursal.$configuracion->siigo_cuenta_contable_total_flete.$codigo_producto.$fecha.$centro_costo.$subcentro_costo.$descripcion_movimiento.$debito_credito.$valor_movimiento.$base_retencion.$codigo_vendedor.$codigo_ciudad.$codigo_zona.$codigo_bodega.$codigo_ubicacion.$cantidad.$tipo_documento_cruce.$codigo_comprobante_cruce.'00000'.$numero_documento_cruce.$secuencia_documento_cruce.$fecha_vencimiento_documento_cruce.$codigo_forma_pago.$codigo_banco.$archivo2."\r\n";
    fwrite($f, $linea); //Copia de seguridad

    //Descuento
    if ($factura->descuento > 0) {
      $consecutivo++;
      $debito_credito = 'D';
      $secuencia_documento_cruce = '00'.$consecutivo;
      $total = round($factura->descuento);

      $valor_movimiento = str_pad($total, 13, '0', STR_PAD_LEFT).'00';
      echo $linea = $tipo.$codigo_comprobante.'00000'.$factura->id.'0000'.$consecutivo.$nit.$sucursal.$configuracion->siigo_cuenta_contable_descuento.$codigo_producto.$fecha.$centro_costo.$subcentro_costo.$descripcion_movimiento.$debito_credito.$valor_movimiento.$base_retencion.$codigo_vendedor.$codigo_ciudad.$codigo_zona.$codigo_bodega.$codigo_ubicacion.$cantidad.$tipo_documento_cruce.$codigo_comprobante_cruce.'00000'.$numero_documento_cruce.$secuencia_documento_cruce.$fecha_vencimiento_documento_cruce.$codigo_forma_pago.$codigo_banco.$archivo2."\r\n";
      fwrite($f, $linea); //Copia de seguridad
    }

    //Seguro
    $consecutivo++;
    $debito_credito = 'C';
    $secuencia_documento_cruce = '00'.$consecutivo;
    $total = round($factura->seguro);

    $valor_movimiento = str_pad($total, 13, '0', STR_PAD_LEFT).'00';
    echo $linea = $tipo.$codigo_comprobante.'00000'.$factura->id.'0000'.$consecutivo.$nit.$sucursal.$configuracion->siigo_cuenta_contable_total_seguro.$codigo_producto.$fecha.$centro_costo.$subcentro_costo.$descripcion_movimiento.$debito_credito.$valor_movimiento.$base_retencion.$codigo_vendedor.$codigo_ciudad.$codigo_zona.$codigo_bodega.$codigo_ubicacion.$cantidad.$tipo_documento_cruce.$codigo_comprobante_cruce.'00000'.$numero_documento_cruce.$secuencia_documento_cruce.$fecha_vencimiento_documento_cruce.$codigo_forma_pago.$codigo_banco.$archivo2."\r\n";
    fwrite($f, $linea); //Copia de seguridad

   //CREE
   $total = round($factura->total * $configuracion->siigo_cree_porcentaje);
    $valor_movimiento = str_pad($total, 13, '0', STR_PAD_LEFT).'00';

    $consecutivo++;
    $debito_credito = 'C';
    $secuencia_documento_cruce = '00'.$consecutivo;
    echo $linea = $tipo.$codigo_comprobante.'00000'.$factura->id.'0000'.$consecutivo.$nit.$sucursal.$configuracion->siigo_cuenta_contable_cree_credito.$codigo_producto.$fecha.$centro_costo.$subcentro_costo.$descripcion_movimiento.$debito_credito.$valor_movimiento.$base_retencion.$codigo_vendedor.$codigo_ciudad.$codigo_zona.$codigo_bodega.$codigo_ubicacion.$cantidad.$tipo_documento_cruce.$codigo_comprobante_cruce.'00000'.$numero_documento_cruce.$secuencia_documento_cruce.$fecha_vencimiento_documento_cruce.$codigo_forma_pago.$codigo_banco.$archivo2."\r\n";
    fwrite($f, $linea); //Copia de seguridad

    $consecutivo++;
    $debito_credito = 'D';
    $secuencia_documento_cruce = '00'.$consecutivo;
    echo $linea = $tipo.$codigo_comprobante.'00000'.$factura->id.'0000'.$consecutivo.$nit.$sucursal.$configuracion->siigo_cuenta_contable_cree_debito.$codigo_producto.$fecha.$centro_costo.$subcentro_costo.$descripcion_movimiento.$debito_credito.$valor_movimiento.$base_retencion.$codigo_vendedor.$codigo_ciudad.$codigo_zona.$codigo_bodega.$codigo_ubicacion.$cantidad.$tipo_documento_cruce.$codigo_comprobante_cruce.'00000'.$numero_documento_cruce.$secuencia_documento_cruce.$fecha_vencimiento_documento_cruce.$codigo_forma_pago.$codigo_banco.$archivo2."\r\n";
    fwrite($f, $linea); //Copia de seguridad
  }
  if ($f) fclose($f);
  if (! empty($facturas)) {
    Logger::archivos('descargó el archivo para SIIGO.');
  }
  exit;
} else {
  echo '<html>
<head>
<title>Logistica | FTP Ministerio de Transporte</title>
<link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon" />
</head><body>';
  require_once Logistica::$root."class/planillasC.class.php";
  $objPlanilla = new PlanillasC;
  $result = $objPlanilla->ExportarMT($_REQUEST['fecha']);
  if (DBManager::rows_count($result) == 0) {
    echo '<h1>No se emitieron manifiestos el dia '.$_REQUEST['fecha'].'</h1></body></html>';
    exit;
  }
  $ftp_servidor = $configuracion->ftp_servidor;
  $ftp_usuario  = $configuracion->ftp_usuario;
  $ftp_clave    = $configuracion->ftp_clave;
  if (! $ftp_stream = @ftp_connect($ftp_servidor)) {
    echo '<h1>Error de Conexi&oacute;n FTP</h1>';
    echo '<h3>No se pudo establecer una conexi&oacute;n con el servidor FTP del Ministerio de Transporte.</h3>';
    echo '<h4>Verifica si tu conexi&oacute;n a Internet est&aacute; activa o si puedes ingresar a <a href="ftp://'.$ftp_servidor.'" target="_blank">'.$ftp_servidor.'</a></h4>';
    exit;
  }
  if (! $ftp_login = ftp_login($ftp_stream, $ftp_usuario, $ftp_clave)) {
    echo '<h1>Error FTP</h1>';
    echo '<p>Se ha establecido una conexi&oacute;n con el servidor FTP del ministerio de transporte.<br>
Pero no se ha podido iniciar sesi&oacute;n con el usuario '.$ftp_usuario.'</p>';
    exit;
  } else {
    echo '['.date('g:i:s a').'] Generando y subiendo los archivos para el dia '.$_REQUEST['fecha'].'<br>';
    echo '['.date('g:i:s a').'] Se ha establecido una conexi&oacute;n con el servidor FTP del ministerio de transporte<br>';
  }
  ftp_pasv($ftp_stream, TRUE);
  $fa = date('Ymd',strtotime($_REQUEST['fecha']));

  // Datos generales
  $search     = array('¡', '$', '#', '%');
  $replace    = array('', '', 'No', '');
  $sep      = chr(9); //Separador de campos (TAB).
  $manifiestos  = array();
  $vehiculos    = array();
  $terceros     = array();
  $conductores  = array();
  $aseguradoras   = array();
  $guias      = array();
  $clientes     = array();
  $contactos    = array();
  $tiempos    = array();

  //-Quitar puntos y '-' del NIT, pero dejar el DV
  $configuracion->nit = str_replace(array('.', '-'), '', $configuracion->nit_empresa);
  $nit = $configuracion->nit;

  $carpeta='C:/backup_logistica/ministerio/';
  if (! file_exists($carpeta)) {
    if (! mkdir($carpeta, 0777, true)) exit('No se pudo crear el directorio para los archivos...');
  }
  echo '['.date('g:i:s a').'] Preparando archivo de Manifiestos...<br>';
  //Archivos de Manifiestos
  $archivo_manifiestos=$configuracion->codigo_empresa.$fa."man.txt";
  $ruta=$carpeta.$archivo_manifiestos;
  $f=fopen($ruta, "w+");
  fflush($f);
  while($row = mysql_fetch_array($result)) {
    $manifiestos[]=$row['id'];
    if (!in_array($row['placacamion'], $vehiculos)) {
      $vehiculos[]=$row['placacamion'];
    }
    if (!in_array($row['numero_identificacion_conductor'], $conductores)) {
      $conductores[]=$row['numero_identificacion_conductor'];
    }
    if (!in_array($row['titular_id'], $terceros)) {
      $terceros[]=$row['titular_id'];
    }

    $fecha_poliza=date("Ymd", strtotime($configuracion->vigencia_poliza_mercancia));
    $row['fecha']=date('Ymd',strtotime($row['fecha']));
    $row['fecha_pago_saldo']=date('Ymd',strtotime($row['fecha_pago_saldo']));
    $row['observaciones']=substr($row['observaciones'],0,200);
    $row['fecha_limite_entrega']=date('Ymd',strtotime($row['fecha_limite_entrega']));

    $linea=$nit.$sep.$configuracion->codigo_empresa.$row['id'].$sep.$row['fecha'].$sep.$row['idciudadorigen'].$sep.$row['idciudaddestino'].$sep.$row['placacamion'].$sep.$row['tipo_identificacion_conductor'].$sep.$row['numero_identificacion_conductor'];
    $linea.=$sep.$row['placa_semirremolque'].$sep.$row['valor_flete'].$sep.$row['retencion_fuente'].$sep.$row['ica'].$sep.$row['anticipo'].$sep.$row['id_ciudad_pago_saldo'].$sep.$row['fecha_pago_saldo'];
    $linea.=$sep.$row['cargue_pagado_por'].$sep.$row['descargue_pagado_por'].$sep.$row['observaciones'].$sep.$row['titular_tipo_identificacion'].$sep.$row['titular_numero_identificacion'].$sep.$row['fecha_limite_entrega'];
    $linea.=$sep.$row['tipo'].$sep.$row['estado'].$sep.$row['motivo_anulacion']."\r\n";
    fwrite($f, $linea);
  }
  if ($f) fclose($f);
  mysql_free_result($result);
  echo '['.date('g:i:s a').'] Terminado.<br>';
  if (ftp_put($ftp_stream, $archivo_manifiestos, $ruta, FTP_ASCII)) {
    unlink($ruta);
    echo '['.date('g:i:s a').'] Se ha subido el archivo de Manifiestos.<br>';
  }else{
    echo '['.date('g:i:s a').'] [Error] No se pudo subir el archivo de Manifiestos.<br>';
  }

  echo '['.date('g:i:s a').'] Preparando archivo de Vehiculos...<br>';
  //Archivo de Vehiculos
  $archivo_vehiculos=$configuracion->codigo_empresa.$fa."veh.txt";
  $ruta=$carpeta.$archivo_vehiculos;
  $f=fopen($ruta, "w+");
  fflush($f);
  foreach ($vehiculos as $placa) {
    $vehiculo = Vehiculo::find($placa);
    $vehiculo->f_venc_soat = date('d/m/Y', strtotime($vehiculo->f_venc_soat));
    if (! in_array($vehiculo->nitaseguradora, $aseguradoras)) {
      $aseguradoras[] = $vehiculo->nitaseguradora;
    }
    if (! in_array($vehiculo->idpropietario, $terceros)) {
      $terceros[] = $vehiculo->idpropietario;
    }
    if (! in_array($vehiculo->id_tenedor, $terceros)) {
      $terceros[] = $vehiculo->id_tenedor;
    }
    $vehiculo->f_venc_soat = date('Ymd',strtotime($vehiculo->f_venc_soat));
    $linea=$vehiculo->placa.$sep.$vehiculo->codigo_Marcas.$sep.$vehiculo->codigo_linea.$sep.$vehiculo->modelo.$sep.$vehiculo->modelo_repotenciado.$sep.$vehiculo->codigo_colores.$sep.$vehiculo->codigo_carrocerias;
    $linea.=$sep.$vehiculo->idconfiguracion.$sep.$vehiculo->peso.$sep.$vehiculo->soat.$sep.'N'.$sep.$vehiculo->nitaseguradora.$sep.$vehiculo->f_venc_soat.$sep.$vehiculo->capacidadcarga.$sep.$vehiculo->numero_ejes;
    $linea.=$sep.$vehiculo->tipo_combustible.$sep.$vehiculo->propietario_tipo_identificacion.$sep.$vehiculo->propietario_numero_identificacion.$sep.$vehiculo->tenedor_tipo_identificacion;
    $linea.=$sep.$vehiculo->tenedor_numero_identificacion."\r\n";
    fwrite($f, $linea);
  }
  if ($f) fclose($f);
  echo '['.date('g:i:s a').'] Terminado<br>';
  if (ftp_put($ftp_stream, $archivo_vehiculos, $ruta, FTP_ASCII)) {
    unlink($ruta);
    echo '['.date('g:i:s a').'] Se ha subido el archivo de Vehiculos.<br>';
  }else{
    echo '['.date('g:i:s a').'] [Error] No se pudo subir el archivo de Vehiculos.<br>';
  }

  echo '['.date('g:i:s a').'] Preparando archivo de Remesas.<br>';
  /**
   * Archivo de Remesas
   */
  $archivo_remesas=$configuracion->codigo_empresa.$fa."rem.txt";
  $ruta=$carpeta.$archivo_remesas;
  $f=fopen($ruta, "w+");
  fflush($f);
  require_once Logistica::$root."class/guias.class.php";
  $objGuia=new Guias;
  $producto=new stdClass;
  $propietario=new stdClass;
  foreach ($manifiestos as $m) {
    $re=$objPlanilla->ObtenerGuias($m);
    while ($g=mysql_fetch_object($re)) {
      if ($g->items==1) {
        $producto->codigo = $g->idproducto;
        $producto->nombre = substr($g->producto, 0, 60);
      }else{
        $producto->codigo = '009980';
        $producto->nombre = 'PRODUCTOS VARIOS';
      }
      if ($g->cliente_tipo_identificacion=='N') {
        $g->cliente_numero_identificacion = $g->cliente_numero_identificacion.$g->cliente_dv;
      }
      if ($g->tipo_identificacion_contacto=='N') {
        $g->numero_identificacion_contacto = $g->numero_identificacion_contacto.$g->contacto_dv;
      }
      if ($g->propietario=='Remitente') {
        $propietario->tipo_identificacion = $g->cliente_tipo_identificacion;
        $propietario->numero_identificacion = $g->cliente_numero_identificacion;
      }else{
        $propietario->tipo_identificacion = $g->tipo_identificacion_contacto;
        $propietario->numero_identificacion = $g->numero_identificacion_contacto;
      }
      if (!in_array($g->idcliente, $clientes)) {
        $clientes[] = $g->idcliente;
      }
      if (!in_array($g->idcontacto, $contactos)) {
        $contactos[] = $g->idcontacto;
      }
      $g->cliente_direccion = str_replace($search, $replace, $g->cliente_direccion);
      $g->direccion_contacto = str_replace($search, $replace, $g->direccion_contacto);
      if ($g->peso<3) $g->peso = 10;
      $l = $nit.$sep.$configuracion->codigo_empresa.$g->idplanilla.$sep.$g->id.$sep.$g->unidadmedida.$sep.$g->peso.$sep.$g->naturaleza.$sep.$g->empaque.$sep.$g->peso_contenedor.$sep.$producto->codigo.$sep.$producto->nombre.$sep.$g->cliente_tipo_identificacion;
      $l .= $sep.$g->cliente_numero_identificacion.$sep.$g->cliente_id_ciudad.$sep.$g->cliente_direccion.$sep.$g->tipo_identificacion_contacto.$sep.$g->numero_identificacion_contacto.$sep.$g->id_ciudad_contacto;
      $l .= $sep.$g->direccion_contacto.$sep.$propietario->tipo_identificacion.$sep.$propietario->numero_identificacion.$sep.'N'.$sep.''.$sep.''."\r\n";
      fwrite($f, $l);
    }
    mysql_free_result($re);
  }
  if ($f) fclose($f);
  echo '['.date('g:i:s a').'] Terminado.<br>';
  if (ftp_put($ftp_stream, $archivo_remesas, $ruta, FTP_ASCII)) {
    unlink($ruta);
    echo '['.date('g:i:s a').'] Se ha subido el archivo de Remesas.<br>';
  }else{
    echo '['.date('g:i:s a').'][Error] No se pudo subir el archivo de Remesas.<br>';
  }
  echo '['.date('g:i:s a').'] Preparando archivo de Personas.<br>';
  /**
   * Archivo de Personas
   */
  $archivo_personas = $configuracion->codigo_empresa.$fa."per.txt";
  $ruta = $carpeta.$archivo_personas;
  $f = fopen($ruta, "w+");
  fflush($f);

  //conductores
  require_once Logistica::$root."class/Conductor.php";
  $obj_conductor = new Conductor;
  foreach ($conductores as $c) {
    $conductor = $obj_conductor->find($c);
    $licencia = explode("-", $conductor->categorialicencia);
    $conductor->telefono = substr($conductor->telefono, 0, 10);
    $conductor->direccion = str_replace($search, $replace, $conductor->direccion);
    $l = $conductor->tipo_identificacion.$sep.$conductor->numero_identificacion.$sep.$conductor->primer_apellido.$sep.$conductor->segundo_apellido.$sep.$conductor->nombre.$sep.$conductor->telefono;
    $l .= $sep.$conductor->direccion.$sep.$conductor->idciudad.$sep.$licencia[0].$sep.$licencia[1]."\r\n";
    fwrite($f,$l);
  }

  //Terceros
  require_once Logistica::$root."class/Tercero.php";
  foreach ($terceros as $t) {
    $tercero = Tercero::find($t);
    if ($tercero->tipo_identificacion !== 'N') {
      $tercero->direccion = str_replace($search, $replace, $tercero->direccion);
      $l = $tercero->tipo_identificacion.$sep.$tercero->numero_identificacion.$sep.$tercero->primer_apellido.$sep.$tercero->segundo_apellido.$sep.$tercero->nombre;
      $l .= $sep.$tercero->telefono.$sep.$tercero->direccion.$sep.$tercero->id_ciudad."\r\n";
      fwrite($f,$l);
    }
  }

  //Clientes
  $cliente = new Cliente;
  foreach ($clientes as $c) {
    $cliente->find($c);
    if ($cliente->tipo_identificacion != 'N') {
      $cliente->direccion = str_replace($search, $replace, $cliente->direccion);
      $l = $cliente->tipo_identificacion.$sep.$cliente->numero_identificacion.$sep.$cliente->primer_apellido.$sep.$cliente->segundo_apellido.$sep.$cliente->nombre.$sep.$cliente->telefono.$sep.$cliente->direccion.$sep.$cliente->idciudad."\r\n";
      fwrite($f, $l);
    }
  }
  if ($f) fclose($f);
  echo '['.date('g:i:s a').'] Terminado.<br>';
  if (ftp_put($ftp_stream, $archivo_personas, $ruta, FTP_ASCII)) {
    unlink($ruta);
    echo '['.date('g:i:s a').'] Se ha subido el archivo de Personas.<br>';
  }else{
    echo '['.date('g:i:s a').'][Error] No se pudo subir el archivo de Personas.<br>';
  }
  echo '['.date('g:i:s a').'] Preparando archivo de Empresas.<br>';
  /**
   * Archivo de Empresas
   */
  $archivo_empresas = $configuracion->codigo_empresa.$fa."emp.txt";
  $ruta = $carpeta.$archivo_empresas;
  $f = fopen($ruta, "w+");
  fflush($f);

  //Aseguradoras
  $l = 'N'.$sep.$configuracion->nit_aseguradora_mercancia.$sep.$configuracion->aseguradora_mercancia;
  $l .= $sep.$configuracion->telefono_aseguradora_mercancia.$sep.$configuracion->direccion_aseguradora_mercancia.$sep.$configuracion->id_ciudad_aseguradora_mercancia."\r\n";
  fwrite($f,$l);

  //Terceros
  foreach ($terceros as $t) {
    $tercero = Tercero::find($t);
    if ($tercero->tipo_identificacion == 'N') {
      $tercero->direccion = str_replace($search, $replace, $tercero->direccion);
      $l = $tercero->tipo_identificacion.$sep.$tercero->numero_identificacion.$tercero->digito_verificacion.$sep.$tercero->nombre;
      $l .= $sep.$tercero->telefono.$sep.$tercero->direccion.$sep.$tercero->id_ciudad."\r\n";
      fwrite($f,$l);
    }
  }

  //Clientes
  foreach ($clientes as $c) {
    $cliente->find($c);
    if ($cliente->tipo_identificacion == 'N') {
      $cliente->direccion = str_replace($search, $replace, $cliente->direccion);
      $l = $cliente->tipo_identificacion.$sep.$cliente->numero_identificacion.$sep.$cliente->nombre.$sep.$cliente->telefono.$sep.$cliente->direccion.$sep.$cliente->idciudad."\r\n";
      fwrite($f, $l);
    }
  }

  if ($f) fclose($f);
  echo '['.date('g:i:s a').'] Terminado.<br>';
  if (ftp_put($ftp_stream, $archivo_empresas, $ruta, FTP_ASCII)) {
    unlink($ruta);
    echo '['.date('g:i:s a').'] Se ha subido el archivo de Empresas.<br>';
  }else{
    echo '['.date('g:i:s a').'][Error] No se pudo subir el archivo de Empresas.<br>';
  }

  echo '['.date('g:i:s a').'] Preparando archivo de Tiempos.<br>';
  /**
   * Archivo de Tiempos
   */
  $archivo_tiempos=$configuracion->codigo_empresa.$fa."tie.txt";
  $ruta=$carpeta.$archivo_tiempos;
  $f=fopen($ruta, "w+");
  fflush($f);
  foreach ($manifiestos as $m) {
    $re=$objPlanilla->ObtenerGuias($m);
    $primera=TRUE;
    while ($g=mysql_fetch_object($re)) {
      $cargue_fecha_l=date('Ymd',strtotime($g->fecha_recibido_mercancia));
      $cargue_hora_l=date('08:00');
      $cargue_fecha_s=date('Ymd',strtotime($g->fecha_recibido_mercancia));
      $cargue_hora_s=date('16:00');
      if ($g->id_ciudad_contacto=='13001000' or $g->id_ciudad_contacto=='47001000') {
        //Cartagena o Santa Marta => 1 dia
        $s="+1 day";
      }else{
        //Otra ciudad => 2 dias
        $s="+2 days";
      }
      $t=$g->cantidad*2.5*60;
      if ($primera) {
        $llegada=strtotime(date('Y-m-d',strtotime($s,strtotime($g->fechadespacho))).' 07:00:00');
        $primera=FALSE;
      }
      $salida=strtotime(date('Y-m-d H:i:s',strtotime('+'.$t.' seconds',$llegada)));
      $descargue_fecha_l=date('Ymd',$llegada);
      $descargue_hora_l=date('H:i',$llegada);
      $descargue_fecha_s=date('Ymd',$salida);
      $descargue_hora_s=date('H:i',$salida);
      $llegada=$salida+300; //5 minutos entre cada entrega

      $g->cargue_horas_pactadas = 8;
      $g->descargue_horas_pactadas = 8;

      $l=$configuracion->codigo_empresa.$g->idplanilla.$sep.$g->id.$sep.$g->cargue_horas_pactadas.$sep.$g->descargue_horas_pactadas.$sep.$cargue_fecha_l.$sep.$cargue_hora_l.$sep.$cargue_fecha_s.$sep.$cargue_hora_s.$sep.$descargue_fecha_l;
      $l.=$sep.$descargue_hora_l.$sep.$descargue_fecha_s.$sep.$descargue_hora_s."\r\n";
      fwrite($f, $l);
    }
    mysql_free_result($re);
  }
  if ($f) fclose($f);
  echo '['.date('g:i:s a').'] Terminado.<br>';
  if (ftp_put($ftp_stream, $archivo_tiempos, $ruta, FTP_ASCII)) {
    unlink($ruta);
    echo '['.date('g:i:s a').'] Se ha subido el archivo de Tiempos.<br>';
  }else{
    echo '['.date('g:i:s a').'][Error] No se pudo subir el archivo de Tiempos.<br>';
  }

  if ($ftp_stream) ftp_close($ftp_stream);
  echo '['.date('g:i:s a').'] FIN<br>';

  echo '<fieldset style="width:auto;" class="ui-widget-content"><legend class="ui-widget-content"><b>Recuerda</b></legend>
Puedes verificar tus archivos entrando al <a href="ftp://'.$ftp_servidor.'" target="_blank">Servidor FTP del Ministerio de Transporte</a><br>
Usuario: '.$ftp_usuario.'<br>
Contrase&ntilde;a: '.$ftp_clave.'<br>
Servidor FTP: '.$ftp_servidor.'</fieldset>';
  $logger=new Logger;
  $logger->log($_SERVER['REMOTE_ADDR'], 'subió los archivos de mintransporte del día '.$_REQUEST['fecha'].'.', "Archivos", date("Y-m-d H:i:s"), $_SESSION['userid']);
  if (file_exists('log')) $lines=file('log');
  else $lines=array();
  $log=fopen('log', "w+");
  $l='['.strftime('%b %d, %I:%M %p').'] '.$_SESSION['username']." generó los archivos del día ".$_REQUEST['fecha']."<br>\r\n";
  fwrite($log, $l);
  if (!empty($lines)) {
    $i=1;
    foreach ($lines as $l) {
      if ($i!=11) {
        fwrite($log, $l);
        $i++;
      }
    }
  }
  fclose($log);
}
?>
