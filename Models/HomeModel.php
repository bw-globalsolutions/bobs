<?php

class HomeModel extends Mysql{

	public function __construct()
	{
		parent::__construct();
	}

	public function getAuditStatistics(){
		
		$sql = "SELECT a.status, COUNT(*) AS 'count' FROM audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status IN('Pending', 'Completed' ,'In process') AND r.type NOT IN('Self-Evaluation') AND l.country_id IN({$_SESSION['userData']['country_id']}) AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY a.status";
		//echo $sql;
		$request = [];
		foreach($this->select_all($sql) as $row){
			$request[$row['status']] = [
				'count'		=> $row['count']
			];
		}
		return $request;
	}
	
	public function progressActionPlan(){
		$sql = "SELECT MONTHNAME(a.date_visit) AS 'label', MONTH(a.date_visit) AS 'month', SUM(IF(a.action_plan_status='Pending', 1, 0)) AS 'pending', SUM(IF(a.action_plan_status='In Process', 1, 0)) AS 'in_process', SUM(IF(a.action_plan_status='Finished', 1, 0)) AS 'finished' FROM audit a INNER JOIN location l ON(a.location_id=l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND r.type = 'Standard' AND l.country_id IN({$_SESSION['userData']['country_id']}) AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') AND YEAR(a.date_visit) = YEAR(CURDATE()) GROUP BY label, month ORDER BY month ASC";

		$request = $this->select_all($sql);
		return $request;
	}

	public function getTopOpp(){
		$request = [];
		$lan = $_SESSION['userData']['default_language'];

		$sql = "SELECT IFNULL(GROUP_CONCAT(a.id SEPARATOR ','), 0) AS 'stack', COUNT(*) AS count FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE r.type = 'Standard' AND l.country_id IN({$_SESSION['userData']['country_id']}) AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') AND a.status = 'Completed' AND YEAR(a.date_visit) = YEAR(CURDATE())";
		$audit = $this->select($sql);

		foreach(['ESTANDAR DE LA MARCA', 'ASEGURAMIENTO DE CALIDAD'] as $mainSection){
			$query = "SELECT ap.question_prefix, (SELECT IFNULL($lan, eng) FROM checklist_item WHERE question_prefix = ap.question_prefix AND type = 'Question' LIMIT 1) AS 'text', COUNT(*) AS 'frecuency', {$audit['count']} AS 'count' FROM audit_point ap WHERE ap.audit_id IN({$audit['stack']}) AND (SELECT main_section FROM checklist_item WHERE question_prefix = ap.question_prefix AND type = 'Question' LIMIT 1) = '$mainSection' GROUP BY ap.question_prefix ORDER BY frecuency DESC LIMIT 5";

			$request[ucfirst(strtolower($mainSection))] = $this->select_all($query); 
		}
		return $request;
	}

	public function getAVGScore(){
		$sql = "SELECT r.name AS 'quarter', SUM(IF(s.value_4 = 'Platino', 1, 0)) AS 'sum_platino', SUM(IF(s.value_4 = 'Verde', 1, 0)) AS 'sum_verde',  SUM(IF(s.value_4 = 'Amarillo', 1, 0)) AS 'sum_amarillo', SUM(IF(s.value_4 = 'Rojo', 1, 0)) AS 'sum_rojo', COUNT(*) AS 'sum_total' FROM audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) INNER JOIN audit_score s ON(a.id=s.audit_id AND s.name='OVERALL SCORE') WHERE a.status='Completed' AND r.type = 'Standard' AND YEAR(a.date_visit) = YEAR(CURDATE()) AND l.country_id IN({$_SESSION['userData']['country_id']}) AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY quarter, r.date_start ORDER BY r.date_start ASC";

		$request = $this->select_all($sql);
		return $request;
	}
	
	public function getLastSelfEvaluation(string $location_id){
		$sql = "SELECT l.number, IFNULL(TIMESTAMPDIFF(MONTH, MAX(ar.date_visit), CURDATE()), 1) AS 'month' FROM location l LEFT JOIN (SELECT a.location_id, a.date_visit, r.type FROM audit a INNER JOIN round r ON(a.round_id=r.id) WHERE a.status='Completed')ar ON(ar.location_id=l.id) WHERE l.id IN($location_id) GROUP BY l.number";

		$request = $this->select_all($sql);
		return $request;
	}

	public function getAuditList($columns=[], $condition=null, $limit=false){	
		$isGM = $_SESSION['userData']['permission']['Auditorias']['w']? "OR (status IN('Completed', 'In Process', 'Pending') AND type IN('Self-Evaluation','Standar'))" : '';
		$isAdmin = in_array($_SESSION['userData']['role']['id'], [1,2])? '' : "AND (status IN('Completed') $isGM) ";
		
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
}
?>