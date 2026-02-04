<?php
class AuditsModel extends Mysql {
	
	private $intBrandId;
	private $intCountryId;
	private $intRoundId;
	private $intLocationId;
	private $strTipoViaje;
	private $strFechaRevision;
	private $strFechaRevision2;
	private $strType;
	private $strName;
	private $strDateStart;

	public function __construct(){
		parent::__construct();
	}
	    	
	public function getChecklist(int $checklist_id, int $audit_id, string $col, string $audited_areas = null){
		if($audited_areas==NULL){
			$filter_area='';
		}else{
			$areas = explode(",", $audited_areas);
			$strAreas = "";
			foreach($areas as $a){
				$strAreas.="'".($a=='SALu00c3O'?'SALÃO':$a)."',";
			}
			$strAreas = substr($strAreas, 0, -1);
			$filter_area = "AND area IN ($strAreas)";
		}
		//$filter_area = empty($audited_areas)? '' : ' AND area IN("' . str_replace("|", '","', $audited_areas) . '")';
        $sql = "SELECT id, IFNULL($col, eng) AS 'txt', priority, points, question_prefix, section_number, IF(EXISTS(SELECT * FROM audit_na_question WHERE audit_id = $audit_id AND question_prefix = ci.question_prefix), 1, 0) AS 'na' FROM checklist_item ci WHERE type = 'Question' AND checklist_id = $checklist_id $filter_area";

		$request = [];
		foreach($this -> select_all($sql) as $q){
			$sql = "SELECT id, IFNULL($col, eng) AS 'picklist_item', priority, ci.AutoFail, IF(EXISTS(SELECT * FROM audit_opp WHERE audit_id = $audit_id AND checklist_item_id = ci.id), 1, 0) AS 'has_opp' FROM checklist_item ci WHERE type = 'Picklist' AND question_prefix = '{$q['question_prefix']}' AND checklist_id = $checklist_id";
			array_push($request, [
				'question'		=> $q['txt'],
				'prefix'		=> $q['question_prefix'],
				'snumber'		=> $q['section_number'],
				'priority'		=> $q['priority'],
				'points'		=> $q['points'],
				'na'			=> $q['na'],
				'picklist'		=> $this -> select_all($sql)
			]);
		}
		return $request;
	}
	
	public function getTypes(){
		$isAdmin = in_array($_SESSION['userData']['role']['id'], [1,2])? '' : "AND type NOT IN('Calibration Audit')";
        $sql = "SELECT DISTINCT type FROM round WHERE 1 $isAdmin";
        $request = $this -> select_all($sql);
		return $request;
	}

	public function getTimes(int $id){
		if($id>0){
			$sql = "SELECT sos_times FROM audit WHERE id = $id";
			$request = $this -> select($sql);
			if($request['sos_times']!='' && $request['sos_times']!=NULL){
				return $request['sos_times'];
			}else{
				return '{"med_1":"0","med_2":"0","med_3":"0","med_4":"0","med_5":"0","med_6":"0","med_7":"0","med_8":"0","med_9":"0","med_10":"0","win_time":"0","dt_time":"0"}';
			}
		}
	}

	public function saveTimes($audit_id, $med_1, $med_2, $med_3, $med_4, $med_5, $med_6, $med_7, $med_8, $med_9, $med_10, $user_id){
		$arrTimes = array(
			"med_1" => $med_1,
			"med_2" => $med_2,
			"med_3" => $med_3,
			"med_4" => $med_4,
			"med_5" => $med_5,
			"med_6" => $med_6,
			"med_7" => $med_7,
			"med_8" => $med_8,
			"med_9" => $med_9,
			"med_10" => $med_10,
		);
		$strTimes = json_encode($arrTimes);
		$query = "UPDATE audit SET sos_times = ? WHERE id=?";

		$request = $this->update($query, [$strTimes, $audit_id]);
		$query = "INSERT INTO audit_log(audit_id, user_id, category, name, details, date) VALUES (?, ?, 'Web', 'Update times', ?, NOW())";

		$request = $this->update($query, [$audit_id, $user_id, $strTimes]);
		
		return $request;
	}
	
	public function getAuditLanguage(int $id){
        $sql = "SELECT c.language FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN country c ON (l.country_id = c.id) WHERE a.id = $id";
        $request = $this -> select($sql);
		return $request['language'];
	}
    
	public function getAuditList($columns=[], $condition=null, $limit=false){	
		$isGM = $_SESSION['userData']['permission']['Auditorias']['w']? "OR (status IN('Completed', 'In Process', 'Pending') AND type IN('Self-Evaluation','Standar'))" : '';
		$isAdmin = in_array($_SESSION['userData']['role']['id'], [1,2,3])? '' : "AND (status IN('Completed') $isGM) ";
		
		if($limit){
			$limit = "LIMIT 1000";
		} else{
			$limit = "";
		}
		
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ."
					FROM audit_list a WHERE 
					". ($condition ? "$condition" : '1') ." AND country_id IN({$_SESSION['userData']['country_id']}) AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'=0) $isAdmin 
				ORDER BY date_visit DESC, id DESC $limit";
		$res = new Mysql;
		$request = $res->select_all($query);
		return $request;
	}

	Public function getAuditsDashboard($condition=null){
		$query = "SELECT a.*, s.value_1, s.value_2, s.value_3, s.value_4
					FROM audit a LEFT JOIN audit_score s ON (a.id = s.audit_id) WHERE 
					". ($condition ? "$condition" : '1') ." AND a.status IN('Completed') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'=0) 
				ORDER BY date_visit DESC, id DESC";
		$res = new Mysql;
		$request = $res->select_all($query);
		return $request;
	}
	
	public function getAudits($columns=[], $condition=NULL){	
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ." 
				  FROM audit 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$request = $this -> select_all($query);
		return $request;
	}

	public function updateAudit($args, $condition = "id = 0"){
		
		$query = "UPDATE audit SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) ." WHERE $condition";
		$request = $this->update($query, $values);
		
		return $request;
	}

	public function updateAuditLog(int $audit_id, int $user_id, string $status){
		$query = "INSERT INTO audit_log(audit_id, user_id, category, name, details, date) VALUES (?, ?, 'Web', 'Update Audit Status', ?, NOW())";
		$request = $this->update($query, [$audit_id, $user_id, 'Change of status to: '.$status]);
		return $request;
	}

	public function validateRoundAutoEval(int $brand_id, int $country_id)
	{
		$this->intCountryId = $country_id;
		$this->intBrandId = $brand_id;

		$sqlBrand = "SELECT prefix FROM brand WHERE id = $this->intBrandId ";
		$brand = $this->select($sqlBrand);

		$periodo = date("Y-m");
		$roundInfo = knowRoundInfoBy($brand['prefix'], $periodo);

		//$titulo = "Round ".date("n Y");
		$titulo = $roundInfo['name'];

		$sql = "SELECT * FROM round WHERE type='Self-Evaluation' AND brand_id = $this->intBrandId AND country_id = $this->intCountryId AND name = '$titulo' LIMIT 1 ";
		$request = $this->select($sql);

		return $request;
	}

	public function insertRoundAutoEval(int $brand_id, int $country_id)
	{
		$this->intCountryId = $country_id;
		$this->intBrandId = $brand_id;
		$this->strType = 'Self-Evaluation';
		//$this->strName = 'Round '.date("n Y");
		//$this->strDateStart = date("Y-m").'-01 00:00:00';

		$sqlBrand = "SELECT prefix FROM brand WHERE id = $this->intBrandId ";
		$brand = $this->select($sqlBrand);

		$periodo = date("Y-m");
		$roundInfo = knowRoundInfoBy($brand['prefix'], $periodo);

		//$titulo = "Round ".date("n Y");
		$titulo = $roundInfo['name'];
		$date_start = $roundInfo['date_start'];

		$sql = "INSERT INTO round SET type=?, name=?, date_start=?, country_id=?, brand_id=? ";
		$arrData = array($this->strType,$titulo,$date_start,$this->intCountryId,$this->intBrandId);

		$request = $this->insert($sql,$arrData);

		return $request;
	}

	public function listAreas(){
		$sql = "SELECT DISTINCT area FROM checklist_item WHERE area IS NOT NULL";
		$request = array_map(function($item){ return $item['area']; }, $this->select_all($sql));
		return $request;
	}
	
	public function getPrintChecklist(int $checklist_id){
		$sql = "SELECT section_number, section_name, question_prefix, type, {$_SESSION['userData']['default_language']} AS 'text', {$_SESSION['userData']['default_language']}_answer AS 'text_answer', points FROM checklist_item WHERE checklist_id = $checklist_id";
		$request = $this->select_all($sql);
		return $request;
	}

	public function insertAuditAutoEval(int $round_id, int $location_id, string $tipoViaje, int $country_id, int $brand_id)
	{
		$this->intBrandId = $brand_id;
		$this->intRoundId = $round_id;
		$this->intCountryId = $country_id;
		$this->intLocationId = $location_id;
		$this->strTipoViaje = $tipoViaje;

		$fecha = date("Y-m-d H:i:s");
		$fecha2 = date("Y-m-d")." 23:59:59";
		$return = 0;

		$sqlStore = "SELECT shop_type FROM location WHERE id = $this->intLocationId";
		$requestStore = $this->select($sqlStore);
		$requestStore = $requestStore['shop_type'];

		$sqlChk = "SELECT id AS checklist_id FROM checklist WHERE brand_id = $this->intBrandId ORDER BY date_start DESC LIMIT 1 ";
		$requestChk = $this->select($sqlChk);

		$sqlScore = "SELECT id AS scoring_id FROM scoring WHERE brand_id = $this->intBrandId ORDER BY date_start DESC LIMIT 1 ";
		$requestScore = $this->select($sqlScore);

		$sqlAddQ = "SELECT id FROM additional_question WHERE brand_id = $this->intBrandId ORDER BY id DESC LIMIT 1 ";
		$requestAddQ = $this->select($sqlAddQ);

		/* Limita 1 por mes 
		$sql = "SELECT id FROM audit WHERE location_id = $this->intLocationId AND round_id = $this->intRoundId LIMIT 1 ";
		$request = $this->select($sql);

		if(empty($request))
		{*/

			$query_insert = "INSERT INTO audit SET 
											round_id = ?,
											checklist_id = ?,
											scoring_id = ?,
											additional_question_id = ?,
											location_id = ?,
											report_layout_id = ?,
											auditor_email = ?,
											auditor_name = ?,
											local_foranea = ?,
											date_visit = ?,
											date_visit_end = ?,
											status = 'Pending' ";

			$arrData = array($this->intRoundId,
							$requestChk['checklist_id'],
							$requestScore['scoring_id'],
							$requestAddQ['id'],
							$this->intLocationId,
							'1',
							$_SESSION['userData']['email'],
							$_SESSION['userData']['name'],
							$this->strTipoViaje,
							$fecha,
							$fecha2);

			$request_insert = $this->insert($query_insert,$arrData);
			$return = $request_insert;
		/*}else{
			$return = "exist";
		}*/
		return $return;
	}



	public function reAudit(){	

	$query = "SELECT 
				audit.id, 
				(SELECT COUNT(*) FROM audit_opp a INNER JOIN checklist_item b ON a.checklist_item_id = b.id WHERE a.audit_id = audit.id AND main_section IN('SEGURIDAD DE ALIMENTOS') AND esp IN('Critico'))seguridad_alimentos, 
				(SELECT COUNT(*) FROM audit_opp a INNER JOIN checklist_item b ON a.checklist_item_id = b.id WHERE a.audit_id = audit.id AND main_section IN('LIMPIEZA') AND auditor_answer IN('1.- Rojo'))limpieza 
			FROM audit HAVING seguridad_alimentos > 2 || limpieza > 10";
		
		$request = $this -> select_all($query);
		return $request;
		
	}

	public function updateRound($round_id, $id){
		$query = "UPDATE audit SET round_id = ? WHERE id=?";

		$request = $this->update($query, [$round_id, $id]);
		return $request;
	}

	public function getAutoFails($visit_id){
		$sql = "SELECT *, (SELECT COUNT(*) FROM `audit_opp` ao LEFT JOIN checklist_item ci ON (ci.id = ao.checklist_item_id) WHERE `audit_id` = a.id AND (ci.AutoFail IS NOT NULL AND ci.AutoFail!='')) autofails FROM audit a WHERE id = $visit_id";

		$request = $this -> select_all($sql);
		return $request[0]['autofails'];
	}
}
?>