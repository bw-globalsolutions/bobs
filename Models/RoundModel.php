<?php
class RoundModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getRound($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ." 
				  FROM round  
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		//echo $query;
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
	

	public function getAllRoundNames(){
				
		$query = "SELECT distinct(name) as names FROM round";
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getAllRoundTypes(){
				
		$query = "SELECT distinct(type) as types FROM round";
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
	
	public function insertRound($args){

		//query y values de argumentos
		$query = "INSERT INTO round SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2);
		
		//var_dump($args);
		$res = new Mysql;
		$request = $res -> insert($query, $values);
		
		return $request;
	}	
}
?>