<?php
class Audit_PointModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getAudit_Point($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM audit_point  
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function insertAudit_Point($args){
		
		//query y values de argumentos
		$query = "INSERT INTO audit_point SET ";
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

	public function deleteAudit_Point($condition='id=0'){
		
		$query = "DELETE FROM audit_point WHERE $condition ";
		
		$res = new Mysql;
		$request = $res -> delete($query);
		
		return $request;
	}

	public function getLostPoint($items){
		$items = implode(',', $items);
		$sql = "SELECT SUM(points_partial) pp FROM checklist_item WHERE id IN($items)";
		$res = new Mysql;
		$request = $res -> select($sql);
		return $request['pp'];
	}
	
}
?>