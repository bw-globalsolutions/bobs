<?php
class Audit_LogModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getAudit_Log($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ." 
				  FROM audit_log 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
	
	public function insertAudit_Log($args){

		//query y values de argumentos
		$query = "INSERT INTO audit_log SET ";
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