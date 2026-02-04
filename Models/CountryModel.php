<?php
class CountryModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getRegion(){
		$sql = "SELECT region, GROUP_CONCAT(id SEPARATOR ',') AS 'listCountry' FROM country GROUP BY region";
        $request = $this -> select_all($sql);
		return $request;
	}
	
	public function getCountry($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM country 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getEstados(){
		$query = "SELECT DISTINCT state_name FROM location ";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getRegionales(){
		$query = "SELECT DISTINCT regional FROM location WHERE regional!='' AND regional IS NOT NULL";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
}
?>