<?php
class BrandModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getBrand($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM brand 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
	
}
?>