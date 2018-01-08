<?php
require_once 'DBManager.php';

/**
 * Clase Carta.
 */
class Carta {

	/**
	 *
	 * @var	DBManager
	 */
	var $conexion;

	var $info=NULL;

	/**
	 * @return Carta
	 */
	function __construct($id=NULL) {
		$this->conexion=new DBManager;
		if(isset($id)){
			$this->info($id);
		}
		return $this;
	}

	function info($id){
		$q="SELECT ca.*, cl.nombre cliente, ci.nombre ciudad
FROM cartas ca, clientes cl, ciudades ci
WHERE cl.id=ca.id_cliente AND ci.id=cl.idciudad AND ca.id=$id";
		if(!$result=DBManager::execute($q) or DBManager::rows_count($result)==0){
			$this->info=NULL;
			return FALSE;
		}else{
			$this->info=mysql_fetch_object($result);
			return TRUE;
		}

	}

	/**
	 * Selecciona todas las cartas.
	 */
	function todas($modo=NULL){
		$q="SELECT ca.*, cl.nombre cliente
FROM cartas ca, clientes cl
WHERE ca.id_cliente=cl.id";
		if(!isset($modo)){
			return DBManager::execute($q);
		}
		return $q;
	}

	function crear($fecha, $id_cliente, $contacto, $texto){
		$q="INSERT INTO cartas VALUES(NULL, '$fecha', '$id_cliente', '$contacto', '$texto')";
		$result=DBManager::execute($q);
		if(!$result) return FALSE;
		return mysql_insert_id();
	}
}
?>
