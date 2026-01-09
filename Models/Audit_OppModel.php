<?php
class Audit_OppModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getAudit_Opp($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM audit_opp 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function insertAudit_Opp($args){
		
		//query y values de argumentos
		$query = "INSERT INTO audit_opp SET ";
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

	public function deleteAudit_Opp($condition='id=0'){
		
		$query = "DELETE FROM audit_opp WHERE $condition ";
		
		$res = new Mysql;
		$request = $res -> delete($query);
		
		return $request;
	}

	public function gettOppsPlan(int $idAudit){
				
		$query = "SELECT 
					t1.id,
					t1.audit_id,
					t1.checklist_item_id,
					t1.audit_point_id,
					t1.audit_id
				FROM audits.audit_opp t1
				left join audits.checklist_item t2
				on t1.checklist_item_id = t2.id
				where t1.audit_id = ".$idAudit;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function updateOpportunity($args, $condition = "id = 0"){
		
		$query = "UPDATE audit_opp SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) ." WHERE $condition";
		$request = $this->update($query, $values);
		
		return $request;
	}

	public function getQuestionOpp(int $snumber, int $audit_id){
		$query = "SELECT ci.question_prefix FROM audit_opp ao INNER JOIN checklist_item ci ON(ao.checklist_item_id = ci.id) WHERE ci.section_number = $snumber AND ao.audit_id = $audit_id";
		$arrQPrefix = [];
		foreach($this->select_all($query) as $p){
			$arrQPrefix[] = $p['question_prefix'];
		}
		return $arrQPrefix;
	}

	public function getAuditId($id){
		$query = "SELECT audit_id FROM audit_opp WHERE id = $id";
		$rs = $this->select_all($query);
		return $rs[0]['audit_id'];
	}

	public function newOpportunity(int $audit_id, int $checklist_item_id, string $auditor_answer, string $auditor_comment){
		$request = null;
		$sql = "SELECT * FROM audit_na_question WHERE audit_id = $audit_id AND question_prefix = (SELECT question_prefix FROM checklist_item WHERE id = $checklist_item_id)";
		if(empty($this->select($sql))){
			$query = "CALL newOpportunity($audit_id, $checklist_item_id, '$auditor_answer', '$auditor_comment');";
			$request = $this->select($query);
		}
		return $request;
	}

	public function setAutoFail(){
		$query = "UPDATE audit_score SET value_3 = 'F', value_4 = 0";
		$request = $this->update($query);
		
		return $request;
	}
	
	public function removeOpportunity(int $opp_id){
		$query = "CALL removeOpportunity($opp_id);";
		$tmp = $this->select($query);
		$request = $tmp['done'];
		return $request;
	}

	public function getOppInfo(int $opp_id, string $lan = 'eng'){
		$query = "SELECT ci.id, ao.audit_id, ci.section_name, ci.question_prefix, IFNULL(ci.$lan, eng) as 'text', ao.auditor_answer, ao.auditor_comment FROM checklist_item ci INNER JOIN audit_opp ao ON (ci.id = ao.checklist_item_id) WHERE ao.id = $opp_id";
		$request = $this->select($query);
		return $request;
	}

	public function insertNA(int $audit_id, int $snumber, string $qprefx, string $points){
		$status = 1;
		$sql = "SELECT ao.id FROM audit_point ap INNER JOIN audit_opp ao ON(ap.id=ao.audit_point_id) WHERE ap.audit_id = $audit_id AND ap.question_prefix = '$qprefx'";
		foreach($this->select_all($sql) as $p){
			$sql = "CALL removeOpportunity({$p['id']})";
			$status = $status && $this->select($sql)['done'];
		}
		$request = false;
		if($status){
			$sql = "INSERT INTO audit_na_question(audit_id, section_number, question_prefix, target_point) SELECT ?, ?, ?, ? FROM dual WHERE NOT EXISTS (SELECT * FROM audit_na_question WHERE audit_id = ? AND question_prefix = ?)";
			$request = $this->insert($sql, [$audit_id, $snumber, $qprefx, $points, $audit_id, $qprefx]);
		}
		return $request > 0? 1 : 0;
	}
	
	public function removeNA(int $audit_id, string $qprefx){
		$sql = "DELETE FROM audit_na_question WHERE audit_id = $audit_id AND question_prefix = '$qprefx'";
		$request = $this->delete($sql);
		return $request? 1 : 0;
	}
	
}
?>