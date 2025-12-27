<?php
class Additional_Question_ItemModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	//locacion
	public function getAdditional_Question_Item($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM additional_question_item 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
}
?>