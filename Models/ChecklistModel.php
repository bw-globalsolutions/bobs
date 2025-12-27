<?php
class ChecklistModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	//Checklist
	
	public function getChecklist($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM checklist   
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id DESC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
}
?>