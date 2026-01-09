<?php

class ModuloComunicacionModel extends Mysql{

	

	public function __construct()
	{
		parent::__construct();
	}

	

public function selectManuales(){
	
	$query = "SELECT id_manual,
				   	 ruta_manual,
				   	 categoria,
				   	 descripcion_manual,
				   	 nombre_manual
			 	FROM manuales";

	$res = new Mysql;
	$request = $res -> select_all($query);
	return $request;

}

public function getPeriods(){
	$query = "SELECT DISTINCT name FROM round";

	$res = new Mysql;
	$request = $res -> select_all($query);
	return $request;
}




public function insertManual( $txtCategoria, $txtDescripcion, $txtNombre, $txtArchivo){
	
	$query = "INSERT INTO manuales(ruta_manual,
								   nombre_manual,
								   descripcion_manual,
								   categoria) 
						  VALUES ('$txtArchivo',
						  		  '$txtNombre',
								  '$txtDescripcion',
								  '$txtCategoria')";



			
   $res = new Mysql;
   $request = $res -> select_all($query);
   return $request;

	

}





}
?>