<?php
class StatisticsModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();
	}

	public function getAudits(string $franchise, string $period, string $type, string $auditor){
		$sql = "SELECT a.id, a.action_plan_status, l.number, l.country, r.type, DATE_FORMAT(a.date_visit, '%d %b %Y') AS 'date_visit', a.auditor_name FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";
		
		$request = $this->select_all($sql);
		return $request;
	}

	public function getAuditMain(string $franchise, string $period, string $type, string $auditor, string $case = 'none'){
		$where = "";		
		switch ($case) {
			case 'completed':
				$where = "AND a.status='Completed'";
				break;
		}

		$query = "SELECT 
			a.auditor_email,
			a.id,
			r.name AS 'round_name', 
			l.number AS 'location_number',
			l.name AS 'location_name',
			c.name, 
			r.type AS 'audit_type', 
			a.local_foranea, 
			a.status AS 'audit_status', 
			a.visit_status, 
			a.daypart, 
			DAYNAME(a.date_visit) AS 'week_day', 
			a.date_visit, 
			a.date_visit_end, 
			TIMESTAMPDIFF(SECOND, a.date_visit, a.date_visit_end) AS'visit_duration', 
			TIMESTAMPDIFF(SECOND, a.date_visit_end, a.date_release) AS 'release_duration', 
 
			s.value_1 AS 'food_safety', 
			s.value_2 AS 'operations_excellence', 
			s.value_4 AS 'overall_score',  
			

			 CASE 
        WHEN s.value_4 < 70 
        THEN 'Failed'
        ELSE 'Approved'
    END AS visit_result,






			IF(ap.ap_total IS NULL, '', a.action_plan_status),
			ap.ap_total,
			ap.ap_inprocess 'acti_inpro',
			ap.ap_completed 'acti_comp'
		FROM audit a
		INNER JOIN round r ON(a.round_id = r.id)
		INNER JOIN location l ON(a.location_id = l.id)
		INNER JOIN country c ON(l.country_id = c.id)
		INNER JOIN brand b ON(l.brand_id = b.id)
		LEFT JOIN (SELECT audit_id, COUNT(*) 'ap_total', SUM(IF(op.actionplan_status != 'Pending', 1, 0)) 'ap_inprocess', SUM(IF(op.actionplan_status = 'Finished', 1, 0)) 'ap_completed' FROM audit_opp op GROUP BY audit_id) ap ON(a.id = ap.audit_id)
		LEFT JOIN audit_score s ON(a.id=s.audit_id)
		WHERE IFNULL(l.name, 'NA') IN ('$franchise')
		AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period')
		AND r.type IN ('$type')
		AND a.auditor_email IN ('$auditor')
		AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')
		$where
		ORDER BY a.date_visit DESC,
		a.id DESC";
		$request = $this -> select_all($query);
		return $request;
	}

	public function getActionPlan(string $franchise, string $period, string $type, string $auditor, string $lan){
		$date = date('Y-m-d H:i:s');
		
		$sql = "SELECT 
		a.id,
		r.name AS 'round_name', 
		r.type AS 'audit_type', 
		a.auditor_name, 
		l.number AS 'location_number', 
		l.name AS 'location_name', 
		a.date_visit, 
		a.date_visit_end,
		ci.main_section,
		ci.section_name,
		ci.question_prefix,
		IFNULL(ci.$lan, ci.eng) 'text',
		ap.actionplan_status,
		(SELECT DATE_FORMAT(MAX(action_date), '%Y-%m-%d') FROM audit_plan_action WHERE audit_opp_id = ap.id) 'action_date',
		(SELECT action_comment FROM audit_plan_action WHERE audit_opp_id = ap.id LIMIT 1) 'action_comm'
	FROM 
		audit a 
		INNER JOIN round r ON(a.round_id = r.id) 
		INNER JOIN location l ON(a.location_id = l.id) 
		INNER JOIN audit_opp ap ON(a.id = ap.audit_id)
		INNER JOIN checklist_item ci ON(ap.checklist_item_id = ci.id)
	WHERE 
		IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') ORDER BY a.date_visit DESC, a.id DESC";

		$request = $this -> select_all($sql);
		return $request;
	}

	public function getAuditPPDetails($stack){
		$query = "SELECT 
				a.id,
				r.name AS 'round_name', 
				r.type AS 'audit_type', 
				a.auditor_name, 
				c.name AS 'country_name', 
				l.number AS 'location_number', 
				l.address_1 AS 'location_addr', 
				a.status AS 'audit_status', 
				a.visit_status, 
				a.daypart, 
				DAYNAME(a.date_visit) AS 'week_day', 
				a.date_visit, 
				a.date_visit_end, 
				TIMESTAMPDIFF(SECOND, a.date_visit, a.date_visit_end) AS'visit_duration', 
				TIMESTAMPDIFF(SECOND, a.date_visit_end, a.date_release) AS 'release_duration'
			FROM audit a
			INNER JOIN round r ON(a.round_id = r.id)
			INNER JOIN location l ON(a.location_id = l.id)
			LEFT JOIN audit_score s ON(a.id=s.audit_id)
			INNER JOIN country c ON(l.country_id = c.id)
			
			WHERE a.id IN($stack)
			ORDER BY a.date_visit DESC,
			a.id DESC";

		$request = $this -> select_all($query);
		return $request;
	}
	
	public function getPtsDetails($stack){
		$query = "SELECT
			ap.audit_id, 
			l.number,
			ap.question_prefix, 
			ci.picklist_prefix, 
			ci.esp, 
			ap.lost_point, 
			ao.auditor_answer, 
			ao.auditor_comment, 
			ao.actionplan_status
		FROM 
			audit_point ap INNER JOIN audit_opp ao ON ap.id = ao.audit_point_id INNER JOIN checklist_item ci ON ci.id = ao.checklist_item_id INNER JOIN audit a ON ap.audit_id = a.id INNER JOIN location l ON l.id = a.location_id
		WHERE 
			ap.id IN ($stack)";

		$request = $this -> select_all($query);
		return $request;
	}

	public function getLocations($idLocation = false){
		$idLocation = $idLocation? "AND id = $idLocation" : "";
		$sql = "SELECT 
			CONCAT(number, ' ') AS 'number', 
			name, 
			address_1, 
			city, 
			state_code, 
			state_name, 
			zip, 
			country, 
			phone, 
			(SELECT GROUP_CONCAT(email SEPARATOR ', ') FROM user WHERE FIND_IN_SET(l.id, location_id) AND role_id = 10) AS 'email', 
			shop_type,
			status 
		FROM 
			location l
		WHERE 
			1 $idLocation";
		$request = $this->select_all($sql);
		return $request;
	}

	public function fullUsers(){
		$sql = "SELECT u.id, (SELECT GROUP_CONCAT(name SEPARATOR ', ') FROM country WHERE FIND_IN_SET(id, u.country_id)), (SELECT GROUP_CONCAT(number SEPARATOR ', ') FROM location WHERE FIND_IN_SET(id, u.location_id)), r.name role, u.name, u.email, IF(u.status=1, 'Active', 'Inactive'), u.created FROM user u INNER JOIN role r ON u.role_id = r.id";
		$request = $this->select_all($sql);
		return $request;
	}

	public function getAuditDay(int $days){
		$date = date('Y-m-d H:i:s');
		
		$sql = "SELECT a.id AS 'audit_id', r.name AS 'round_name', r.type AS 'audit_type', a.auditor_name, a.local_foranea, c.name AS 'country_name', l.number AS 'location_number', a.daypart, DAYNAME(a.date_visit) AS 'week_day', a.date_visit, a.date_visit_end, TIMESTAMPDIFF(HOUR, a.date_visit, a.date_visit_end) AS'visit_duration', TIMESTAMPDIFF(HOUR, a.date_visit_end, a.date_release) AS 'release_duration', s.value_2 AS 'bs_score', s.value_1 AS 'fs_score', s.value_1 + s.value_2 AS 'bs_fs', s.value_3 AS 'overall_score', cs.critics FROM audit a INNER JOIN round r ON(a.round_id = r.id) INNER JOIN location l ON(a.location_id = l.id) INNER JOIN country c ON(l.country_id = c.id) INNER JOIN brand b ON(l.brand_id = b.id) INNER JOIN audit_score s ON(a.id=s.audit_id) LEFT JOIN (SELECT audit_id, SUM(IF(lost_point > 24, 1, 0)) AS 'critics' FROM `audit_point` GROUP BY audit_id) cs ON(a.id = cs.audit_id) WHERE r.type IN('Self-Evaluation', 'Standard') AND a.status = 'Completed' AND a.date_release BETWEEN DATE_ADD('$date', INTERVAL - $days DAY) AND '$date' ORDER BY a.date_visit DESC, a.id DESC";

		$request = $this -> select_all($sql);
		return $request;
	}
	
	public function frequencyOpp(string $franchise, string $period, string $type, string $auditor, string $lan){
		$query = "SELECT IFNULL(GROUP_CONCAT(a.id SEPARATOR ','), 0) AS 'stack' FROM audit a INNER JOIN location l ON(a.location_id=l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";
		$stack = $this->select($query)['stack'];

		$query = "SELECT ci.section_name, ci.question_prefix, IFNULL(ci.$lan, eng), (SELECT COUNT(*) FROM audit_point WHERE question_prefix=ci.question_prefix AND audit_id IN($stack)) opp, CONCAT(ROUND((SELECT opp * 100 / COUNT(*) FROM audit WHERE FIND_IN_SET(checklist_id, ciq.checklist_ids) AND id IN($stack)), 2), '%') FROM checklist_item ci INNER JOIN (SELECT MAX(id) id, GROUP_CONCAT(checklist_id) checklist_ids FROM checklist_item ci WHERE type = 'Question' AND ci.section_name != 'Information' GROUP BY question_prefix) ciq ON ci.id = ciq.id  ORDER BY opp DESC";
		$request = $this -> select_all($query);
		return $request;
	}
	
	public function topOpp(string $lan, string $franchise, string $period, string $type, string $auditor){
		$request = [];

		$sql = "SELECT IFNULL(GROUP_CONCAT(a.id SEPARATOR ','), 0) AS 'stack', COUNT(*) AS count FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ($franchise) AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";
		$audit = $this->select($sql);

		foreach(['Food Safety', 'Operations Excellence'] as $mainSection){
			if($audit['count']>0){
				$query = "SELECT ap.question_prefix, (SELECT IFNULL($lan, eng) FROM checklist_item WHERE question_prefix = ap.question_prefix AND type = 'Question' ORDER BY id DESC LIMIT 1) AS 'text', COUNT(*) AS 'frecuency', {$audit['count']} AS 'count' FROM audit_point ap WHERE ap.audit_id IN({$audit['stack']}) AND (SELECT main_section FROM checklist_item WHERE question_prefix = ap.question_prefix LIMIT 1) = '$mainSection' GROUP BY ap.question_prefix ORDER BY frecuency DESC LIMIT 10";

				$request[ucfirst(strtolower($mainSection))] = $this->select_all($query); 
			}else{
				$request[ucfirst(strtolower($mainSection))] = [];
			}
		}
		$test = array("sql"=>$sql);
		return $request;
	}

	public function leadership(string $franchise, string $period, string $type, string $auditor){
		
		$query = "SELECT 
			l.name, 
			COUNT(*) AS 'visits', 
			ROUND(AVG(s.value_1), 2) AS 'Food safety', 
			ROUND(AVG(s.value_2), 2) AS 'Operations excellence', 
			ROUND(AVG(s.value_4), 2) AS 'Overall score'
		FROM 
			audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN audit_score s ON (a.id = s.audit_id) INNER JOIN round r ON (a.round_id = r.id)
		WHERE 
			a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY l.name";

		$request = $this -> select_all($query);
		if(count($request)==0){
			$request = [["name"=>"", "visits"=>0, 'Food safety'=>0, 'Operations excellence'=>0, 'Overall score'=>0]];
		}
		$test = array("sql"=>$query);
		return $request;
	}

	public function getAutofails($name, string $period, string $type){
		$sql = "SELECT COUNT(*) af FROM `location` l LEFT JOIN audit a ON (l.id = a.location_id) LEFT JOIN audit_opp ao ON (ao.audit_id = a.id) LEFT JOIN checklist_item ci ON (ci.id = ao.checklist_item_id) LEFT JOIN round r ON (a.round_id = r.id) WHERE l.name = '$name' AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND (ci.AutoFail is NOT NULL AND ci.AutoFail!='') AND a.status = 'Completed'";
		$request = $this -> select_all($sql);
		return $request[0]['af'];
	}

	public function actionPlanStatus(string $franchise, string $period, string $type, string $auditor){
		if (strlen($period) >= 2 && $period[0] === "'" && $period[strlen($period)-1] === "'") {
			$period = substr($period, 1, -1);
		}
		$query = "SELECT action_plan_status, COUNT(*) AS 'count' FROM audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON (a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY action_plan_status";
		$request = $this -> select_all($query);
		$test = array("sql"=>$query);
		return $request;
	}
	
	public function daypart(string $franchise, string $period, string $type, string $auditor){
		if (strlen($period) >= 2 && $period[0] === "'" && $period[strlen($period)-1] === "'") {
			$period = substr($period, 1, -1);
		}
		$query = "SELECT IFNULL(daypart, 'No registration') AS 'daypart', COUNT(*) AS 'count' FROM audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON (a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY daypart";
		$request = $this -> select_all($query);
		$test = array("sql"=>$query);
		return $request;
	}
	
	public function weekday(string $franchise, string $period, string $type, string $auditor){
		if (strlen($period) >= 2 && $period[0] === "'" && $period[strlen($period)-1] === "'") {
			$period = substr($period, 1, -1);
		}
		$query = "SELECT DAYOFWEEK(a.date_visit) AS 'weekday', COUNT(*) AS 'count' FROM audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON (a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY weekday";
		$request = $this -> select_all($query);
		$test = array("sql"=>$query);
		return $request;
	}

	public function duration(string $franchise, string $period, string $type, string $auditor){
		if (strlen($period) >= 2 && $period[0] === "'" && $period[strlen($period)-1] === "'") {
			$period = substr($period, 1, -1);
		}
		$query = "SELECT
			CASE
				WHEN TIMESTAMPDIFF(MINUTE, a.date_visit, a.date_visit_end) < 60 THEN 'Less than 1 hour'
				WHEN TIMESTAMPDIFF(MINUTE, a.date_visit, a.date_visit_end) < 91 THEN 'Less than 1 hour and 30 minutes'
				WHEN TIMESTAMPDIFF(MINUTE, a.date_visit, a.date_visit_end) < 120 THEN 'Less than 2 hours'
				ELSE 'Greater than 2 hours'
			END AS 'duration',
			COUNT(*) AS 'count'
		FROM 
			audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON (a.round_id = r.id)
		WHERE
			a.status = 'Completed' AND a.date_visit IS NOT NULL AND a.date_visit_end IS NOT NULL AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') 
		GROUP BY 
			duration";

		$request = $this -> select_all($query);
		$test = array("sql"=>$query);
		return $request;
	}

	public function addQuestions(string $franchise, string $period, string $type, string $auditor, string $lan){
		$query = "SELECT aq.audit_id, 
							  r.name, 
							  r.type, 
							  a.status, 
							  l.number numero_tienda,
							  l.name nombre_tienda,
							  a.auditor_name, 
							  IFNULL(ai.$lan, ai.eng), 
							  aq.answer 
					FROM audit_addi_question aq 
					INNER JOIN additional_question_item ai ON(aq.additional_question_item_id=ai.id) 
					INNER JOIN audit a ON(aq.audit_id=a.id) 
					INNER JOIN location l ON(a.location_id = l.id) 
					INNER JOIN round r ON (a.round_id = r.id) 
					INNER JOIN brand b ON(l.brand_id = b.id) 
					WHERE ai.input_type IN('SELECT_OPTIONS', 'FREE_TEXT') AND 
						  a.status = 'Completed' AND 
						  IFNULL(l.name, 'NA') IN ('$franchise') AND 
						  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
						  r.type IN ('$type') AND 
						  a.auditor_email IN ('$auditor') AND 
						  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') 
						  ORDER BY aq.audit_id";
		$request = $this -> select_all($query);
		return $request;
	}
	
	public function reportOpp(string $franchise, string $period, string $type, string $auditor, string $lan){
		$query = "SELECT 
				a.id, 
				a.checklist_id, 
				l.number, 
				l.name, 
				ci.main_section,
				ci.section_name, 
				ci.question_prefix, 
				q.question, 
				ci.$lan, 
				ao.auditor_answer, 
				ao.auditor_comment,
				a.auditor_name, 
				a.auditor_email,
				a.date_visit,
				a.date_visit_end,
				TIMESTAMPDIFF(SECOND, a.date_visit, a.date_visit_end) AS'visit_duration' 
			FROM 
				checklist_item ci INNER JOIN audit_opp ao ON(ao.checklist_item_id=ci.id) INNER JOIN audit a ON(ao.audit_id=a.id) INNER JOIN audit_score ae ON a.id = ae.audit_id INNER JOIN round r ON(a.round_id = r.id) INNER JOIN location l ON(a.location_id = l.id) INNER JOIN (SELECT question_prefix, priority, checklist_id, $lan 'question' FROM checklist_item WHERE type = 'Question') q ON ci.question_prefix = q.question_prefix AND ci.checklist_id = q.checklist_id
			WHERE 
				a.status = 'Completed' AND ci.section_name != 'Information' AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') ORDER BY a.checklist_id DESC, ao.audit_id DESC";

		$request = $this -> select_all($query);
		return $request;
	}
	
	public function categoryTrend(string $franchise, string $period, string $type, string $auditor){
		$whereAudits = "SELECT a.id FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";

		$sql = "SELECT COUNT(*) opp, ci.section_name, ci.section_number, ci.main_section FROM (SELECT audit_id, audit_point_id, MAX(checklist_item_id) item_id FROM audit_opp WHERE audit_id IN($whereAudits) GROUP BY audit_id, audit_point_id) ao INNER JOIN checklist_item ci ON ao.item_id = ci.id AND ci.section_name != 'Information' GROUP BY ci.section_name, ci.section_number, ci.main_section ORDER BY opp DESC";

		$request = $this->select_all($sql);
		$test = array("sql"=>$sql);
		return $request;
	}

	public function questionTrend(string $lan, string $franchise, string $period, string $type, string $auditor){
		$whereAudits = "SELECT a.id FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";

		$sql = "SELECT ci.txt, ci.question_prefix, ci.section_number, COUNT(*) AS 'opp', REPLACE(TO_BASE64(GROUP_CONCAT(ap.id)), '\n', '') tk FROM audit_point ap INNER JOIN (SELECT question_prefix, IFNULL($lan, eng) AS 'txt', section_number FROM checklist_item WHERE id IN (SELECT MAX(id) FROM checklist_item WHERE type = 'Question' GROUP BY question_prefix) AND section_name != 'Information') ci ON ap.question_prefix = ci.question_prefix WHERE ap.audit_id IN($whereAudits) GROUP BY ci.txt, ci.question_prefix, ci.section_number ORDER BY opp";

		$request = $this->select_all($sql);
		$test = array("sql"=>$sql);
		return $request;
	}
	
	public function progressStatus(string $franchise, string $period, string $type, string $auditor){
		$query = "SELECT GROUP_CONCAT(a.id SEPARATOR ',') AS 'stack' FROM audit a INNER JOIN location l ON(a.location_id=l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";
		$stack = $this->select($query)['stack']??'NULL';

		$query_country = "SELECT c.name AS 'label', SUM(IF(a.status='Pending', 1, 0)) AS 'pending', SUM(IF(a.status='In Process', 1, 0)) AS 'in_process', SUM(IF(a.status='Completed', 1, 0)) AS 'completed' FROM audit a INNER JOIN location l ON(a.location_id=l.id) INNER JOIN country c ON(l.country_id=c.id) WHERE a.id IN($stack) GROUP BY label";

		$query_quarter = "SELECT r.name AS 'label', SUM(IF(a.status='Pending', 1, 0)) AS 'pending', SUM(IF(a.status='In Process', 1, 0)) AS 'in_process', SUM(IF(a.status='Completed', 1, 0)) AS 'completed' FROM audit a INNER JOIN location l ON(a.location_id=l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.id IN($stack) GROUP BY label";
		
		$query_month_name = "SELECT IFNULL(MONTHNAME(a.date_visit), 'N/A') AS 'label', IFNULL(MONTH(a.date_visit), 0) AS 'month', SUM(IF(a.status='Pending', 1, 0)) AS 'pending', SUM(IF(a.status='In Process', 1, 0)) AS 'in_process', SUM(IF(a.status='Completed', 1, 0)) AS 'completed' FROM audit a INNER JOIN location l ON(a.location_id=l.id) WHERE a.id IN($stack) GROUP BY label, month ORDER BY month ASC";

		$request = [
			'Country'	=> $this->select_all($query_country),
			'Quarter'	=> $this->select_all($query_quarter),
			'Month'		=> $this->select_all($query_month_name)
		];
		$test = array("sql"=>$query);
		return $request;
	}

	public function failureRate(string $franchise, string $period, string $type, string $auditor){
		$sql = "SELECT r.name AS 'period', SUM(IF(s.value_4='Rojo', 1, 0)) AS 'failure', COUNT(*) AS 'count' FROM audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) INNER JOIN audit_score s ON(a.id=s.audit_id AND s.name='OVERALL SCORE') WHERE a.status='Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY period";

		$request = $this->select_all($sql);
		return $request;
	}
	
	public function ratingByDP(string $franchise, string $period, string $type, string $auditor){
		$sql = "SELECT ae.value_4 AS 'score', IFNULL(a.daypart, 'N/A') AS 'daypart', COUNT(*) AS 'count' FROM audit a INNER JOIN audit_score ae ON(ae.audit_id=a.id) INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status='Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY ae.value_4, a.daypart";

		$request = [];
		foreach($this->select_all($sql) as $row){
			if(!array_key_exists($row['daypart'], $request)){
				$request[$row['daypart']] = ['label' => $row['daypart']];
			}
			$request[$row['daypart']][$row['score']] = $row['count'];
		}

		return array_values($request);
	}
	
	public function ratingByPeriod(string $franchise, string $period, string $type, string $auditor){
		$sql = "SELECT ae.value_4 AS 'score', r.name, COUNT(*) AS 'count' FROM audit a INNER JOIN audit_score ae ON(ae.audit_id=a.id) INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status='Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY ae.value_4, r.name";

		$request = [];
		foreach($this->select_all($sql) as $row){
			if(!array_key_exists($row['name'], $request)){
				$request[$row['name']] = ['label' => $row['name']];
			}
			$request[$row['name']][$row['score']] = $row['count'];
		}

		return array_values($request);
	}

	public function actionCompletion(string $franchise, string $period, string $type, string $auditor){
		$sql = "SELECT action_plan_status, COUNT(*) AS 'count', IFNULL(MONTHNAME(a.date_visit), 'N/A') AS 'label', IFNULL(MONTH(a.date_visit), 0) AS 'month' FROM audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON (a.round_id = r.id) WHERE a.id IN(SELECT audit_id FROM audit_point) AND a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY action_plan_status, label, month ORDER BY month";
		
		$request = [];
		foreach($this->select_all($sql) as $row){
			if(!array_key_exists($row['label'], $request)){
				$request[$row['label']] = ['label' => $row['label']];
			}
			$request[$row['label']][$row['action_plan_status']] = $row['count'];
		}

		$test = array("sql"=>$sql);
		return array_values($request);
	}

	public function gallery(string $franchise, string $period, string $type, string $auditor,string $checklist, string $checklistItem){



		$ejmlo= "2,1";

		$query = "SELECT
		                 a.checklist_id,
						 a.id, 
						 l.number, 
						 a.status, 
						 r.type, 
						 c.name, 
						 a.auditor_name, 
						 DATE_FORMAT(a.date_visit, '%d %b %Y') AS 'date_visit', 
						 b.prefix 
				   FROM audit a 
				  		INNER JOIN round r ON(a.round_id = r.id) 
				  		INNER JOIN location l ON(a.location_id = l.id) 
				  		INNER JOIN country c ON (l.country_id=c.id) 
				  		INNER JOIN brand b ON(l.brand_id=b.id) 
				   WHERE a.status='Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') 
				   							  AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') 
											  AND r.type = '$type' 
											  AND a.auditor_email IN ('$auditor') 
											  AND a.checklist_id IN ('$checklist') 
											  AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') 
					ORDER BY a.checklist_id DESC";

		$request = [];


		
		





		foreach($this->select_all($query) as $audit){



			
								 			 

			//$auditFiles = selectAuditFiles(['type', 'name', 'url', 'reference_id'], 'audit_id = ' . $audit['id']);
			
			$auditFiles = selectAuditFiles(['type', 'name', 'url', 'reference_id'], '(reference_id IN (SELECT a.id
																										FROM audit_opp a
																										INNER JOIN checklist_item b ON a.checklist_item_id = b.id
																									WHERE audit_id = '.$audit['id'].' 
																										AND checklist_id   IN('.$audit['checklist_id'].') 
																										AND question_prefix IN("'.$checklistItem.'")) 
																										AND audit_id = '.$audit['id'].') 
																										OR (type NOT IN("Opportunity")AND audit_id = '.$audit['id'].')');
			

			


			$tmp = [];
			foreach($auditFiles as $af){
				if(!array_key_exists($af['type'], $tmp)){
					$tmp[$af['type']] = [];
				}
				array_push($tmp[$af['type']], ['name' => $af['name'], 'url' => $af['url'], 'reference_id' => $af['reference_id']]);
			}

			if(!empty($tmp)){
				array_push($request, [
					'id'			=> $audit['id'],
					'number'		=> $audit['number'],
					'type'			=> $audit['type'],
					'status'		=> $audit['status'],
					'brand_prefix'	=> $audit['prefix'],
					'country_name'	=> $audit['name'],
					'auditor_name'	=> $audit['auditor_name'],
					'date_visit' 	=> $audit['date_visit'],
					'files' 		=> $tmp,
					'checklistArray'=> $checklistItem
				
				]);
			}
		}

		return $request;
	}

	public function getScoreTopBottom(string $franchise, string $period, string $type, string $auditor){
		$params = [
			[
				'label'		=> 'top|Food Safety',
				'section'	=> 'Food Safety',
				'orden'		=> 'ASC'
			],
			[
				'label'		=> 'bottom|Food Safety',
				'section'	=> 'Food Safety',
				'orden'		=> 'DESC'
			],
			[
				'label'		=> 'top|Operations Excellence',
				'section'	=> 'Operations Excellence',
				'orden'		=> 'ASC'
			],
			[
				'label'		=> 'bottom|Operations Excellence',
				'section'	=> 'Operations Excellence',
				'orden'		=> 'DESC'
			]
		];

		$request = [];
		foreach($params as $p){
			$sql = "SELECT 
				l.number AS 'location_number', 
				l.name AS 'location_name',
				--  CONCAT('{$p['section']}',' -franchise- ','$franchise',' -period- ','$period',' -type- ','$type',' -auditor- ','$auditor',' -orden- ','{$p['orden']}',' -user- ','{$_SESSION['userData']['location_id']}')  score
				a_opp.cnt AS 'score'

			FROM 
				audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON (a.round_id = r.id) INNER JOIN (SELECT ao.audit_id, COUNT(*) cnt FROM audit_opp ao INNER JOIN checklist_item ci ON ao.checklist_item_id = ci.id WHERE ci.main_section = '{$p['section']}' GROUP BY ao.audit_id) a_opp ON a.id = a_opp.audit_id 
			WHERE 
				IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') AND a.status = 'Completed'
			ORDER BY 
				a_opp.cnt {$p['orden']} LIMIT 10";

			$request[$p['label']] = $this->select_all($sql);
			$request['sql'] = $sql;
		}

		return $request ;
	}

	public function programPreview($franchise, $type, $auditor, $months){
		$colPeriods = "";
		foreach($months as $mont){
			$colPeriods .= "CONCAT(SUM(IF(a.period = '$mont' AND a.status = 'Completed', 1, 0)), '/', SUM(IF(a.period = '$mont', 1, 0)), '/', REPLACE(TO_BASE64(GROUP_CONCAT(IF(a.period = '$mont', a.id, NULL))), '\n', '')) AS '$mont', ";
		}
		$filMonths = implode("','", $months);

		$query = "SELECT 
			a.auditor_email,
			$colPeriods
			CONCAT(SUM(IF(a.status = 'Completed', 1, 0)), '/', COUNT(*), '/', REPLACE(TO_BASE64(GROUP_CONCAT(a.id)), '\n', '')) AS 'avg'
		FROM 
			audit a INNER JOIN round r ON a.round_id = r.id INNER JOIN location l ON a.location_id = l.id INNER JOIN country c ON l.country_id = c.id 
		WHERE 
			IFNULL(l.name, 'NA') IN ('$franchise') AND a.period IN('$filMonths') AND r.type IN ('$type') AND a.status IN ('In Process', 'Pending', 'Completed') AND a.auditor_email IN ('$auditor') AND r.type IN('Standard')
		GROUP BY 
			a.auditor_email";
		$groupBy = $this -> select_all($query);
		
		$query = "SELECT 
			'total' AS auditor_email,
			$colPeriods
			CONCAT(SUM(IF(a.status = 'Completed', 1, 0)), '/', COUNT(*), '/', REPLACE(TO_BASE64(GROUP_CONCAT(a.id)), '\n', '')) AS 'avg'
		FROM 
			audit a INNER JOIN round r ON a.round_id = r.id INNER JOIN location l ON a.location_id = l.id INNER JOIN country c ON l.country_id = c.id 
		WHERE 
			IFNULL(l.name, 'NA') IN ('$franchise') AND a.period IN('$filMonths') AND r.type IN ('$type') AND a.status IN ('In Process', 'Pending', 'Completed') AND a.auditor_email IN ('$auditor') AND r.type IN('Standard')";
		$total = $this -> select_all($query);

		return array_merge($groupBy, $total);
	}
	
	public function getTarget($country, $period){
		$query = "SELECT target FROM period_target WHERE country_id = $country AND round = '$period'";
		$request = $this -> select($query);

		return $request;
	}
	
	public function setTarget($country, $period, $target){
		$query = "DELETE FROM period_target WHERE country_id = $country AND round = '$period'";
		$this->query($query);

		$query = "INSERT INTO period_target (country_id, round, target) VALUES (?,?,?)";
		$request = $this -> insert($query, [$country, $period, $target]);
		return $request > 1;
	}

	public function topOppDetails(string $franchise, string $period, string $type, string $auditor, string $lan, string $qprefix){
		$request = [];

		$query_audits = "SELECT a.id FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";
		
		$query_details = "SELECT ci.question_prefix, COUNT(1) count, IFNULL(ci.$lan, ci.eng) txt FROM audit_opp ao INNER JOIN checklist_item ci ON ao.checklist_item_id = ci.id AND ci.section_name != 'Information' WHERE ci.question_prefix = '$qprefix' AND audit_id IN($query_audits) GROUP BY txt";

		$request = $this->select_all($query_details); 
		return $request;
	}

	public function getAuditors(){
		$sql = "SELECT DISTINCT a.auditor_email FROM audit a INNER JOIN location l ON a.location_id = l.id WHERE a.status = 'Completed' AND l.country_id IN({$_SESSION['userData']['country_id']})";
		$request = $this->select_all($sql);
		return $request;
	}

	public function appealItems(string $franchise, string $period, string $type, string $auditor, string $lan){
		$sql = "SELECT 
			a.id, 
			l.number, 
			l.name, 
			ci.main_section, 
			ci.section_name, 
			ci.question_prefix, 
			cq.txt_q, 
			IFNULL(ci.$lan, ci.eng) txt_p, 
			ai.author_comment, 
			a.auditor_name, 
			a.auditor_email, 
			al.`status`, 
			ai.decision_result, 
			ai.decision_comment 
		FROM 
			audit a INNER JOIN location l ON a.location_id = l.id INNER JOIN appeal al ON a.id = al.audit_id INNER JOIN appeal_item ai ON ai.appeal_id = al.id INNER JOIN audit_opp ao ON ai.audit_opp_id = ao.id INNER JOIN checklist_item ci ON ci.id = ao.checklist_item_id INNER JOIN (SELECT IFNULL($lan, eng) txt_q, question_prefix, checklist_id FROM checklist_item WHERE `type` = 'Question') cq ON cq.question_prefix = ci.question_prefix AND cq.checklist_id = ci.checklist_id INNER JOIN round r ON r.id = a.round_id
		WHERE 
			IFNULL(l.name, 'NA') IN ('$franchise') AND ci.section_name != 'Information' AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') AND a.status = 'Completed'";
		
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function oppPerSection(string $franchise, string $period, string $type, string $auditor){

		$query = "SELECT IFNULL(GROUP_CONCAT(a.id SEPARATOR ','), 0) AS 'stack', COUNT(*) cnt FROM audit a INNER JOIN location l ON(a.location_id=l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";

		$audits = $this->select($query);

		$sql = "SELECT ci.main_section, ci.section_name, ci.question_prefix, {$audits['cnt']}, IFNULL(ap.cnt, 0), CONCAT(ROUND(IFNULL(ap.cnt / {$audits['cnt']}, 0) * 100, 2), '%') FROM (SELECT DISTINCT main_section, section_name, question_prefix FROM checklist_item WHERE checklist_id = (SELECT MAX(id) FROM checklist) AND section_name != 'Information') ci LEFT JOIN (SELECT question_prefix, COUNT(*) cnt FROM audit_point WHERE audit_id IN ({$audits['stack']}) GROUP BY question_prefix) ap ON ci.question_prefix = ap.question_prefix";
		
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function oppPerAuditor(string $franchise, string $period, string $type, string $auditor){

		$query = "SELECT a.auditor_email, IFNULL(GROUP_CONCAT(a.id SEPARATOR ','), 0) AS 'stack', COUNT(*) cnt FROM audit a INNER JOIN location l ON(a.location_id=l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY a.auditor_email";

		$request = [];
		foreach($this->select_all($query) as $auditor){
			$sql = "SELECT '{$auditor['auditor_email']}' as 'auditor_email', ci.main_section, ci.section_name, ci.question_prefix, {$auditor['cnt']}, IFNULL(ap.cnt, 0), CONCAT(ROUND(IFNULL(ap.cnt / {$auditor['cnt']}, 0) * 100, 2), '%') FROM (SELECT DISTINCT main_section, section_name, question_prefix FROM checklist_item WHERE checklist_id = (SELECT MAX(id) FROM checklist) AND section_name != 'Information') ci LEFT JOIN (SELECT question_prefix, COUNT(*) cnt FROM audit_point WHERE audit_id IN ({$auditor['stack']}) GROUP BY question_prefix) ap ON ci.question_prefix = ap.question_prefix";

			array_push($request, ...$this->select_all($sql));
		}

		return $request;
	}
	
	public function auditorSurvey(string $franchise, string $period, string $type, string $auditor){
		$sql = "SELECT 
			l.number,
			l.name AS 'location_name', 
			u.name AS 'user_name', 
			u.email, 
			a.date_visit, 
			ay.answer 
		FROM 
			audit a INNER JOIN (SELECT audit_id, answer, user_id FROM audit_survey WHERE question_id = 8) ay ON a.id = ay.audit_id INNER JOIN location l ON l.id = a.location_id LEFT JOIN user u ON u.id = ay.user_id INNER JOIN round r ON(a.round_id = r.id)
		WHERE 
			IFNULL(l.name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') AND a.status = 'Completed'";

		$request = $this->select_all($sql);
		return $request;
	}

	public function exportCerttis(){
		


$sql = "SELECT 					a.id_audit_opp,
								
								auditor_email,
								nombre_certtis,
					            comentario_certtis,
							
								
                    			question_prefix,
                    			auditor_answer,
                    			auditor_comment
							FROM audit_opp t1
							LEFT JOIN (SELECT t3.*,
											  t4.question,
											  t4.priority as questionP,
							                  t4.priorityV as questionV
										FROM checklist_item t3
										INNER JOIN (SELECT question_prefix, IFNULL(null, eng) AS 'question', priority as priorityV, IF(priority = 'Critical', 0, 1) priority
													FROM checklist_item
													WHERE type = 'Question'  ) t4
														ON t3.question_prefix = t4.question_prefix
													WHERE t3.type = 'Picklist') t2 ON t1.checklist_item_id = t2.id
							            INNER JOIN certtis a ON t1.id = a.id_audit_opp
							            INNER JOIN ct_tipo_certtis b ON a.id_tipo_certtis = b.id_tipo_certtis
										INNER JOIN audit c ON t1.audit_id = c.id
							WHERE  estatus_certtis = 1 
							ORDER BY  questionP";




		$request = $this->select_all($sql);
		return $request;
	}
	public function exportPending(){
		


		$sql = "SELECT
		a.id,
		c.name round,
		b.number,
		b.name,
		a.auditor_name,
		a.auditor_email,
		c.date_start,
		c.type
		FROM audit a
		INNER JOIN location b ON a.location_id = b.id
		INNER JOIN round c ON a.round_id = c.id
		WHERE a.status = 'pending' ";
		
		
		
		
				$request = $this->select_all($sql);
				return $request;
			}

	public function getFranchissees($countrys='', $ml='', $subF=''){
		if($countrys=='no country')return [];
		if($ml=='no ml')return[];
		if($subF=='no subF')return[];
		$sql = "SELECT DISTINCT IFNULL(l.name, 'NA') AS 'name' FROM location l WHERE l.country_id IN (".($countrys!=''?$countrys:$_SESSION['userData']['country_id']).") AND ".($subF!=''?"l.sub_franchise_entity_name IN ($subF) AND ":"").($ml!=''?"l.id IN(".$ml.")":"(l.id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')");

		$request = $this->select_all($sql);
		
		return $request;
	}

	public function getChecklist(){
		
		$sql = "SELECT * FROM checklist";

		$request = $this->select_all($sql);
		return $request;
	}

	public function getChecklistItem(){
		
		$sql = "SELECT question_prefix,
					   eng,
					   section_name
				FROM checklist_item
				WHERE type = 'Question' AND ci.section_name != 'Information' 
				GROUP BY question_prefix,eng,section_name";

		$request = $this->select_all($sql);
		return $request;
	}


	public function exportUserPass(){
		$sql = "SELECT 
				a.name user_name,
				email,
				IF(a.password = 'X', 'NO', 'YES')  password,
				b.name role
				FROM user a
INNER JOIN role b ON a.role_id = b.id
WHERE a.role_id NOT IN(1,2)";

		$request = $this->select_all($sql);
		return $request;
	}


	public function exportUserLogin(){


		$sql = "SELECT
					a.id,
                    (SELECT number FROM location y WHERE y.id IN(a.location_id) ) number_location,
                    (SELECT name FROM location y WHERE y.id IN(a.location_id) ) name_location,
                    (SELECT name FROM location y WHERE y.id IN(a.location_id) ) name,
    				a.email,
                    b.name role,
                    (SELECT COUNT(*) FROM system_logs z WHERE z.user_id = a.id ) log_count
				FROM user a 
                INNER JOIN role b ON a.role_id = b.id 
				WHERE   a.role_id NOT IN(1,2) AND b.id IN(10)
				GROUP BY a.id 
				ORDER BY role ASC";

		$request = $this->select_all($sql);
		return $request;
	}


	public function exportLayoutReport(){
		
		$sql = "CALL SELECT_DATA_LOG()";

		$request = $this->select_all($sql);
		return $request;
	}

	

}
?>