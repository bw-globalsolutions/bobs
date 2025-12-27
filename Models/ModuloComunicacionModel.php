<?php

class ModuloComunicacionModel extends Mysql{

	

	public function __construct()
	{
		parent::__construct();
	}

	

public function selectManuales($where){
	
	$query = "SELECT id_manual,
				   	 ruta_manual,
				   	 categoria,
				   	 descripcion_manual,
				   	 nombre_manual,
					 lang
			 	FROM manuales 
                $where";
				

	$res = new Mysql;
	$request = $res -> select_all($query);
	return $request;

}




public function insertManual( $txtCategoria, $txtDescripcion, $txtNombre, $txtArchivo,$txtLang){
	
	$query = "INSERT INTO manuales(ruta_manual,
								   nombre_manual,
								   descripcion_manual,
								   categoria,
								   lang) 
						  VALUES ('$txtArchivo',
						  		  '$txtNombre',
								  '$txtDescripcion',
								  '$txtCategoria',
								  '$txtLang')";



			
   $res = new Mysql;
   $request = $res -> select_all($query);
   return $request;

	

}

public function eliminarManual($id_manual) {
    $query = "DELETE FROM manuales WHERE id_manual = '$id_manual'";

    $res = new Mysql;
    $request = $res->select_all($query);

    return $request; // true o false
}

public function editarManual($id_manual, $nombre, $categoria, $txtLang) {
    $query = "UPDATE manuales 
              SET nombre_manual = '$nombre', categoria = '$categoria' , lang = '$txtLang'
              WHERE id_manual = '$id_manual'";

    $res = new Mysql;
    $request = $res->select_all($query);
    return $request;
}




}
?>