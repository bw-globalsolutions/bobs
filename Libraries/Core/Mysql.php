<?php

ini_set('memory_limit', '10240M');

class SingletonConexion {
	private static $mainConexion = null;

	public static function getConnection() {
		if (self::$mainConexion == null) {
			error_log("singleton\n", 3, "php://stdout");
			self::$mainConexion = new Conexion();
		}
		return self::$mainConexion->connect();
	}

	public static function reconnect() {
		error_log("reconnect\n", 3, "php://stdout");
		self::$mainConexion = null;
		self::$mainConexion = new Conexion();
		return self::$mainConexion->connect();
	}
}

class Mysql extends Conexion
{
	private $conexion;
	private $strquery;
	private $arrValues;
	private $errorRetryMessage = "php_network_getaddresses";

	public function __construct(){
		$this->conexion = SingletonConexion::getConnection();
		$this->init();
	}

	public function reconectSingleton() {
		$this->conexion = SingletonConexion::reconnect();
		$this->init();
	}

	public function init()
	{
		$retryCount = 0;
		do {
			$retry = false;
			$retryCount = $retryCount + 1;
			try {
				$this->conexion->query("SET SESSION group_concat_max_len = 204800;");
			} catch (Exception $e) {
				$retry = $this->evaluate_error($e);
			}
		} while ($retry && $retryCount <= 5);
	}

	//inserta registro
	public function insert(string $query, array $arrValues)
	{
		$retryCount = 0;
		do {
			$retry = false;
			$retryCount = $retryCount + 1;
			try {
				$this->strquery = $query;
				$this->arrValues = $arrValues;

				$insert = $this->conexion->prepare($this->strquery);
				$resInsert = $insert->execute($this->arrValues);
				if($resInsert)
				{
					$lastInsert = $this->conexion->lastInsertId();
				}else{
					$lastInsert = 0;
				}
				
				return $lastInsert;
			} catch (Exception $e) {
				$retry = $this->evaluate_error($e);
			}
		} while ($retry && $retryCount <= 5);
	}

	//Buscar un registro
	public function select(string $query)
	{
		$retryCount = 0;
		do {
			$retry = false;
			$retryCount = $retryCount + 1;
			try {
				$this->strquery = $query;
				$result = $this->conexion->prepare($this->strquery);
				$result->execute();
				$data = $result->fetch(PDO::FETCH_ASSOC);
				return $data;
			} catch (Exception $e) {
				$retry = $this->evaluate_error($e);
			}
		} while ($retry && $retryCount <= 5);
	}

	//Buscar un registro
	public function select_(string $query, array $arrValues)
	{
		$this->strquery = $query;
		$this->arrValues = $arrValues;
		$result = $this->conexion->prepare($this->strquery);
		for($i=1; $i<=count($arrValues); $i++) {
			$result->bindParam(':v'.$i, $arrValues[$i-1]);
		}
		$result->execute();
		$data = $result->fetch(PDO::FETCH_ASSOC);
		return $data;
	}

	//Devuelve todos los registros
	public function select_all(string $query)
	{
		$retryCount = 0;
		do {
			$retry = false;
			$retryCount = $retryCount + 1;
			try {
				$this->strquery = $query;
				$result = $this->conexion->prepare($this->strquery);
				$result->execute();
				$data = $result->fetchall(PDO::FETCH_ASSOC);
				return $data;
				
			} catch (Exception $e) {
				$retry = $this->evaluate_error($e);
			}
		} while ($retry && $retryCount <= 5);
	}

	//Actualizar registros
	public function update(string $query, array $arrValues)
	{
		$retryCount = 0;
		do {
			$retry = false;
			$retryCount = $retryCount + 1;
			try {
				$this->strquery = $query;
				$this->arrValues = $arrValues;
				$update = $this->conexion->prepare($this->strquery);
				$resExecute = $update->execute($this->arrValues);
				return $resExecute;
				
			} catch (Exception $e) {
				$retry = $this->evaluate_error($e);
			}
		} while ($retry && $retryCount <= 5);
	}

	//Eliminar registro
	public function delete(string $query)
	{
		$retryCount = 0;
		do {
			$retry = false;
			$retryCount = $retryCount + 1;
			try {
				$this->strquery = $query;
				$result = $this->conexion->prepare($this->strquery);
				$del = $result->execute();
				return $del;
				
			} catch (Exception $e) {
				$retry = $this->evaluate_error($e);
			}
		} while ($retry && $retryCount <= 5);
	}
	
	public function query(string $query)
	{
		$retryCount = 0;
		do {
			$retry = false;
			$retryCount = $retryCount + 1;
			try {
				$this->strquery = $query;
				$result = $this->conexion->query($this->strquery);
				return $result;
			} catch (Exception $e) {
				$retry = $this->evaluate_error($e);
			}
		} while ($retry && $retryCount <= 5);
	}

	public function evaluate_error($e) {
		if (strpos($e->getMessage(), $this->errorRetryMessage) !== false) {
			$this->reconectSingleton();
			return true;
		}
		return false;
	}

	public function getLastError() {
        // Si usas mysqli:
        if(property_exists($this, 'connection') && $this->conexion instanceof mysqli) {
            return $this->conexion->error;
        }
        // Si usas PDO:
        if(property_exists($this, 'connection') && $this->conexion instanceof PDO) {
            return $this->conexion->errorInfo();
        }
        return 'No se puede obtener el error';
    }
}
?>