<?php
class AppealsModel extends Mysql {

	public function __construct(){
		parent::__construct();
	}

	public function selectAppeals($condition=NULL){
		$result = array();

		$query = "SELECT 
					t1.*, 
					(select email FROM user where id = t1.author_user_id) as author,
					(select email FROM user where id = t1.owner_user_id) as owner
					FROM appeal t1
					where 1 $fil $condition";
		//echo $query;
		//$res = new Mysql;
		$request = $this->select_all($query);

		if($request){
			foreach($request as $r)
			{
				$result['appeals'][$r['id']] = $r;

				$sqllocation = "SELECT t2.number, t2.name, t2.address_1 
								from audit t1
								left join location t2
								on t1.location_id = t2.id 
								where t1.id = $r[audit_id]";
				//echo $sqllocation;
				$rlocation = $this->select($sqllocation);
				$result['appeals'][$r['id']]['location'] = $rlocation;

				$sqlGralInfo = "SELECT round_name, date_release, date_visit, type, region from audit_list where id = $r[audit_id]";
				//echo $sqllocation;
				$rGralInfo = $this->select($sqlGralInfo);
				$result['appeals'][$r['id']]['gralInfo'] = $rGralInfo;

				$sql2 = "SELECT 
							t1.id id_appeal_item, 
							t1.appeal_id, 
							t1.audit_opp_id, 
							t1.decision_result, 
							t1.author_comment, 
							t1.owner_comment,
							t1.decision_comment,
							t3.*
						FROM appeal_item t1
						left join 
							(select 
								t2.*, 
								(select question_prefix FROM checklist_item where id = t2.checklist_item_id) as question_prefix, 
								(select {$_SESSION['userData']['default_language']} FROM checklist_item where id = t2.checklist_item_id) as eng 
								from audit_opp t2 where audit_id in ( $r[audit_id], ($r[audit_id]*-1) ) ) t3
						on t1.audit_opp_id = t3.id WHERE t1.appeal_id = $r[id]";

				//echo $sql2;
				$request2 = $this->select_all($sql2);
				$result['appeals'][$r['id']]['items'] = $request2;
			}
		}
		
		return $result;
	}

	public function selectAppealUpd(int $idAppeal){
		$query = "SELECT 
					t1.id id_appeal_item, 
					t1.appeal_id, 
					t1.audit_opp_id, 
					t1.decision_result, 
					t1.author_comment, 
					t1.owner_comment, 
					t1.decision_comment, 
					t3.*
				FROM appeal_item t1
				left join 
					(select 
						t2.*, 
						(select question_prefix FROM checklist_item where id = t2.checklist_item_id) as question_prefix, 
						(select {$_SESSION['userData']['default_language']} FROM checklist_item where id = t2.checklist_item_id) as eng
						from audit_opp t2) t3
				on t1.audit_opp_id = t3.id WHERE t1.appeal_id = $idAppeal";
				//echo $query;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
	}
    
	public function getAuditList($columns=[], $condition=null){	
		// $location_set = $location_set? "AND FIND_IN_SET(a.location_id, '$location_set')" : '';
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ."
					FROM audit_list a WHERE 
					". ($condition ? "$condition" : '1') ."
				  ORDER BY date_visit DESC, id DESC";
		$res = new Mysql;

		//echo $query;
		$request = $res->select_all($query);
		return $request;
	}

	public function getAuditAppeal(int $idAudit) {
		$query = "SELECT 
					t1.id id_audit_opp,
					t1.checklist_item_id,
					t1.audit_point_id,
					t1.audit_id,
					t1.auditor_answer,
					t1.auditor_comment,
					t2.*
				FROM audit_opp t1
				left join (SELECT 
								t3.*,
								t4.question,
								t4.priority as questionP,
                           		t4.priorityV as questionV
							FROM checklist_item t3
							inner join (select question_prefix, IFNULL({$_SESSION['userData']['default_language']}, eng) AS 'question', priority as priorityV, IF(priority = 'Critical', 0, 1) priority
										FROM checklist_item
										where type = 'Question' and checklist_id = (select checklist_id from audit where id = ".$idAudit." limit 1) ) t4
							on t3.question_prefix = t4.question_prefix
							where t3.type = 'Picklist') t2
				on t1.checklist_item_id = t2.id
				where t1.audit_id = ".$idAudit." 
				order by checklist_item_id";

		echo $query;
		die();
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
	}

	public function getAuditById($columns=[], $condition=NULL){	
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ." 
				  FROM audit_list
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		$request = $this -> select_all($query);
		return $request;
	}

	public function getOpps(int $idAudit){
				
		$query = "SELECT 
					t1.id id_audit_opp,
					t1.checklist_item_id,
					t1.audit_point_id,
					t1.audit_id,
					t1.auditor_answer,
					t1.auditor_comment,
					t2.*
				FROM audit_opp t1
				left join (SELECT 
								t3.*,
								t4.question,
								t4.priority as questionP,
                           		t4.priorityV as questionV
							FROM checklist_item t3
							inner join (select question_prefix, IFNULL({$_SESSION['userData']['default_language']}, eng) AS 'question', priority as priorityV, IF(priority = 'Critical', 0, 1) priority
										FROM checklist_item
										where type = 'Question'  and checklist_id = (select checklist_id from audit where id = ".$idAudit." limit 1) ) t4
							on t3.question_prefix = t4.question_prefix
							where t3.type = 'Picklist') t2
				on t1.checklist_item_id = t2.id
				where t1.audit_id = ".$idAudit."  and t2.section_name NOT IN('CALIDAD DQ')
				order by checklist_item_id";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
	}

	public function insertAppeal($args){
		
		//query y values de argumentos
		$query = "INSERT INTO appeal SET ";
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

	public function updateAppeal($args, $condition = "id = 0"){
		
		$query = "UPDATE appeal SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) ." WHERE $condition";
		$request = $this->update($query, $values);
		
		return $request;
	}

	public function insertAppealItem($args){
		
		//query y values de argumentos
		$query = "INSERT INTO appeal_item SET ";
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

	public function updateAppealItem($args, $condition = "id = 0"){
		
		$query = "UPDATE appeal_item SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) ." WHERE $condition";
		$request = $this->update($query, $values);
		
		return $request;
	}

	public function selectAppeal($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM appeal 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		//echo $query;
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function selectAppealItem($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM appeal_item
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		//echo $query;
		$res = new Mysql;
		$request = $res -> select($query);
		
		return $request;
	}

	public function selectOppItem($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM audit_opp
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		//echo $query;
		$res = new Mysql;
		$request = $res -> select($query);
		
		return $request;
	}

	public function selectAppealDetail($appeal){
		$result = array();

		$query = "SELECT * FROM appeal where id=".$appeal;
		//$res = new Mysql;
		$request = $this->select($query);

		if($request){
			$sqlDet ="SELECT * FROM audit_list WHERE id=$request[audit_id]";
			$requestDet = $this->select_all($sqlDet)[0];
			$request['detail'] = $requestDet;
			$sql2 = "SELECT t1.id id_appeal_item, t1.appeal_id, t1.audit_opp_id, t1.decision_result, t1.author_comment, t1.owner_comment, t2.*
						FROM appeal_item t1
						left join audit_opp t2
						on t1.audit_opp_id = t2.id WHERE t1.appeal_id = $request[id]";
			$request2 = $this->select_all($sql2);
			$request['items'] = $request2;
			for($i=0; $i<count($request['items']); $i++){
				$query = "SELECT 
					t1.id id_audit_opp,
					t1.checklist_item_id,
					t1.audit_point_id,
					t1.audit_id,
					t1.auditor_answer,
					t1.auditor_comment,
					t2.*
				FROM audit_opp t1
				left join (SELECT 
								t3.*,
								t4.esp as question
							FROM checklist_item t3
							inner join (select question_prefix, eng, esp
										FROM checklist_item
										where type = 'Question') t4
							on t3.question_prefix = t4.question_prefix
							where t3.type = 'Picklist') t2
				on t1.checklist_item_id = t2.id
				where t1.id = ".$request['items'][$i]['audit_opp_id'];
				//echo $query;
				$requestDetItem = $this->select($query);
				$request['items'][$i]['detaiel_item'] = $requestDetItem;
			}
		}
		
		return $request;
	}

	public function updateOpportunityAppeal($opp){
		$query = "UPDATE audit_opp SET audit_id = ?, appeal_status = ? where id = $opp";
		$arrData = array( '(audit_id*-1)' ,0);
		//echo $query;
		// die();
		$request = $this->update($query, $arrData);
		return $request;
	}

	public function updateOpportunityAppealProc($args, $condition = "id = 0"){
		
		$query = "UPDATE audit_opp SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) ." WHERE $condition";
		//echo $query;
		$request = $this->update($query, $values);
		
		return $request;
	}

	public function updatePointAppealProc($args, $condition = "id = 0"){
		
		$query = "UPDATE audit_point SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) ." WHERE $condition";
		//echo $query;
		$request = $this->update($query, $values);
		
		return $request;
	}

	public function listRounds(){
				
		$query = "SELECT DISTINCT(name) FROM round";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function listStatus(){
				
		$query = "SELECT DISTINCT(status) FROM appeal";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function listStores(){
				
		$query = "SELECT 
					DISTINCT(ap.location_id), 
					CONCAT('#', lo.number, ' - ', lo.name) AS 'name'
				FROM appeal ap 
				join location lo
				on ap.location_id = lo.id
				where lo.id IN({$_SESSION['userData']['location_id']}) OR ('{$_SESSION['userData']['location_id']}' = 0 AND lo.country_id IN({$_SESSION['userData']['country_id']}))";

		
		//echo $query; die();
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getFilesApp(int $idOpp){
				
		$query = "SELECT af.* FROM audit_file af WHERE reference_id = $idOpp AND type = 'Appeal'";
		
		$request = $this -> select_all($query);
		
		return $request;
	}
	
}
?>