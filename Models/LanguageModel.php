<?php
class LanguageModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getLanguage($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM language 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id DESC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}	
}
?>