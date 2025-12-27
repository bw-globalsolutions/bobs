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
	$decodedAreas = json_decode($audited_areas, true);
$areaList = "AND (area IS NULL AND type = 'Question' AND checklist_id = $checklist_id )";

if (!empty($decodedAreas) && is_array($decodedAreas)) {
    $areaList = '"' . implode('","', $decodedAreas) . '"';
}
	$filter_area = is_null($audited_areas)? 'AND (area IS NULL AND type = "Question" AND checklist_id = ' . $checklist_id  . '  )' : 'AND area IN(' . $areaList  . ') OR (area IS NULL AND type = "Question" AND checklist_id = ' . $checklist_id  . ' )';

        $sql = "SELECT id, IFNULL($col, eng) AS 'txt', priority, points, question_prefix, section_number, IF(EXISTS(SELECT * FROM audit_na_question WHERE audit_id = $audit_id AND question_prefix = ci.question_prefix), 1, 0) AS 'na' FROM checklist_item ci WHERE type = 'Question' AND checklist_id = $checklist_id $filter_area";

		$request = [];
		foreach($this -> select_all($sql) as $q){
			$sql = "SELECT id, IFNULL($col, eng) AS 'picklist_item', priority, IF(EXISTS(SELECT * FROM audit_opp WHERE audit_id = $audit_id AND checklist_item_id = ci.id), 1, 0) AS 'has_opp' FROM checklist_item ci WHERE type = 'Picklist' AND question_prefix = '{$q['question_prefix']}' AND checklist_id = $checklist_id";
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
	
	public function getAuditLanguage(int $id){
        $sql = "SELECT c.language FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN country c ON (l.country_id = c.id) WHERE a.id = $id";
        $request = $this -> select($sql);
		return $request['language'];
	}
    
	public function getAuditList($columns=[], $condition=null, $limit=false){	
		$isGM = $_SESSION['userData']['permission']['Auditorias']['w']? "OR (status IN('Completed', 'In Process', 'Pending') AND type IN('Self-Evaluation','Standar','IDQ Internal Audit','Training-visits'))" : '';
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




	public function validateRoundIDQ(int $brand_id, int $country_id)
	{
		$this->intCountryId = $country_id;
		$this->intBrandId = $brand_id;

		$sqlBrand = "SELECT prefix FROM brand WHERE id = $this->intBrandId ";
		$brand = $this->select($sqlBrand);

		$periodo = date("Y-m");
		$roundInfo = knowRoundInfoBy($brand['prefix'], $periodo);

		//$titulo = "Round ".date("n Y");
		$titulo = $roundInfo['name'];

		$sql = "SELECT * FROM round WHERE type='IDQ Internal Audit' AND brand_id = $this->intBrandId AND country_id = $this->intCountryId AND name = '$titulo' LIMIT 1 ";
		$request = $this->select($sql);

		return $request;
	}

	public function validateId(int $country_id)
	{
		$this->intCountryId = $country_id;
		

		$sql = "SELECT MAX(a.id)id_audit FROM audit a INNER JOIN round b ON a.round_id = b.id WHERE location_id = $this->intCountryId AND type = 'Self-Evaluation'";
		$request = $this->select($sql);

		return $request;
	}

	public function validateIdIDQ(int $country_id)
	{
		$this->intCountryId = $country_id;
		

		$sql = "SELECT MAX(a.id)id_audit FROM audit a INNER JOIN round b ON a.round_id = b.id WHERE location_id = $this->intCountryId AND type = 'IDQ Internal Audit'";
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

	public function insertRoundIDQ(int $brand_id, int $country_id)
	{
		$this->intCountryId = $country_id;
		$this->intBrandId = $brand_id;
		$this->strType = 'IDQ Internal Audit';
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

		$sqlChk = "SELECT id AS checklist_id FROM checklist WHERE brand_id = $this->intBrandId AND id = 6 ORDER BY id DESC LIMIT 1 ";
		$requestChk = $this->select($sqlChk);

		$sqlScore = "SELECT id AS scoring_id FROM scoring WHERE brand_id = $this->intBrandId ORDER BY id DESC LIMIT 1 ";
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



	public function insertAuditIDQ(int $round_id, int $location_id, string $tipoViaje, int $country_id, int $brand_id)
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

		$sqlChk = "SELECT id AS checklist_id FROM checklist WHERE brand_id = $this->intBrandId AND id =  6 ORDER BY id DESC LIMIT 1 ";
		$requestChk = $this->select($sqlChk);

		$sqlScore = "SELECT id AS scoring_id FROM scoring WHERE brand_id = $this->intBrandId ORDER BY id DESC LIMIT 1 ";
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

	public function getArea(){
		$sql = "SELECT 
					area
				FROM location
				GROUP BY area";
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function getShopType(){
		$sql = "SELECT shop_type
				FROM location
				GROUP BY shop_type";
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function getCountry(){
		$sql = "SELECT country
				FROM location
				GROUP BY country ";
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function getConcept(){
		$sql = "SELECT concept
				FROM location
				GROUP BY concept";
		$request = $this->select_all($sql);
		return $request;
	}
	
	
	public function getEmailFranchisee(){
		$sql = "SELECT franchissees_name
				FROM location
				GROUP BY franchissees_name";
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function getEmailAreaManager(){
		$sql = "SELECT email_area_manager
				FROM location
				GROUP BY email_area_manager
				ORDER BY email_area_manager";
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function getEmailOpsLeader(){
		$sql = "SELECT email_ops_leader
				FROM location
				GROUP BY email_ops_leader";
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function getEmailOpsDirector(){
		$sql = "SELECT email_ops_director
				FROM location
				GROUP BY email_ops_director";
		$request = $this->select_all($sql);
		return $request;
	}

	public function selectOpp(int $id_audit, string $type){

		$sql = "SELECT
					b.eng type,
				    question_prefix,
   					(SELECT eng FROM checklist_item z WHERE b.question_prefix = z.question_prefix AND type = 'Question' AND  z.checklist_id = b.checklist_id)question
				FROM audit a
				INNER JOIN checklist_item b ON a.checklist_id = b.checklist_id
				INNER JOIN audit_opp c ON b.id = c.checklist_item_id
				WHERE a.id = $id_audit AND eng = '$type'";

		$request = $this->select_all($sql);
		return $request;
	}

	
	



	
}
?>