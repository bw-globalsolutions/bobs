<?php
class Audit_Addi_QuestionModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getAudit_Addi_Question($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM audit_addi_question 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function insertAudit_Addi_Question($args){
		
		//query y values de argumentos
		$query = "INSERT INTO audit_addi_question SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2);
		
		$res = new Mysql;
		$request = $res -> insert($query, $values);
		
		return $request;
	}

	public function deleteAudit_Addi_Question($condition='id=0'){
		
		$query = "DELETE FROM audit_addi_question WHERE $condition ";
		
		$res = new Mysql;
		$request = $res -> delete($query);
		
		return $request;
	}
}
?>