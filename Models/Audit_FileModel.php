<?php
class Audit_FileModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getAudit_File($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM audit_file  
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function insertAudit_File($args){
		
		//query y values de argumentos
		$query = "INSERT INTO audit_file SET ";
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

	public function insertFrontDoorPic($audit_id, $url_pic){
		$query = "INSERT INTO audit_file(audit_id, reference_id, type, name, url) 
			VALUES (?, (SELECT aqi.id FROM audit a INNER JOIN additional_question_item aqi ON aqi.additional_question_id = a.additional_question_id AND aqi.eng = 'Picture of the Front Door/Entrance of the Restaurant' WHERE a.id = ?), 'Additional Questions', 'Picture of the Front Door/Entrance of the Restaurant', ?)";
		$request = $this -> insert($query, [$audit_id, $audit_id, $url_pic]);
		
		return $request;
	}

	public function deleteAudit_File($condition='id=0'){
		
		$query = "DELETE FROM audit_file WHERE $condition ";
		
		$res = new Mysql;
		$request = $res -> delete($query);
		
		return $request;
	}

	public function insertOpp_File(int $opp_id, string $stack_img){
		$query = "SELECT ao.audit_id, ap.question_prefix FROM audit_opp ao INNER JOIN audit_point ap ON(ao.audit_point_id = ap.id) WHERE ao.id = $opp_id";
		$tmp = $this->select($query);
		$countInsert = 0;

		foreach(explode('|', $stack_img) AS $url){
			$query = "INSERT INTO audit_file(audit_id, reference_id, type, name, url) VALUES(?, ?, 'Opportunity', ?, ?)";
			if($this->update($query, [$tmp['audit_id'], $opp_id, "Opportunity for {$tmp['question_prefix']}", $url])) $countInsert++;
		}

		return $countInsert == count(explode('|', $stack_img));
	}
}
?>