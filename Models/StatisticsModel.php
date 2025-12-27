<?php
class StatisticsModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();
	}

	public function getAudits(string $franchise, string $period, string $type, string $auditor){
		$sql = "SELECT a.id, a.action_plan_status, l.number, l.country, r.type, DATE_FORMAT(a.date_visit, '%d %b %Y') AS 'date_visit', a.auditor_name FROM audit a INNER JOIN location l ON (a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";
		
		$request = $this->select_all($sql);
		return $request;
	}

	public function getAuditMain(string $franchise, string $period, string $type, string $auditor , string $shop_type, string $country, string $area, string $concept, string $area_manager, string $escalation1, string $escalation2 , string $case = 'none'){
		$where = "";		
		switch ($case) {
			case 'completed':
				$where = "AND a.status='Completed'";
				break;
		}
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";
		$query = "SELECT 
			a.auditor_email,
			a.id,
			r.name AS 'round_name', 
			c.name AS 'country_name', 
			l.number AS 'location_number',
			l.name AS 'location_name',
			l.shop_type,
			l.area,
			l.franchissees_name,
			l.core_menu,
			c.name, 
			a.audita_folio,
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
			s.value_1 AS 'criticos', 
			s.value_2 AS 'no_criticos', 
			s.value_4 AS 'amarillo', 
			s.value_5 AS 'rojo', 
			s.value_6 AS 'mantenimiento',
			(SELECT IFNULL(SUM(IF(ci.main_section = 'ZTC', 1, 0)), 0)  
			FROM audit_opp ao 
			INNER JOIN checklist_item ci 
			ON ao.checklist_item_id = ci.id 
			AND ao.audit_id = a.id)zero_tolerancia,
			 CASE 
        WHEN s.value_1 > 1 OR s.value_5 > 10 OR 
             (SELECT IFNULL(SUM(IF(ci.main_section = 'ZTC', 1, 0)), 0)  
             FROM audit_opp ao 
             INNER JOIN checklist_item ci 
             ON ao.checklist_item_id = ci.id 
             AND ao.audit_id = a.id) > 0 
        THEN 'Fail'
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
		WHERE IFNULL(l.franchissees_name, 'NA') IN ('$franchise')
		AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period')
		AND r.type IN ('$type')
		
		AND $new_filter a.auditor_email IN ('$auditor')and
		 l.country  IN ('$country') 
		AND 
		(a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')
		$where
		ORDER BY a.date_visit DESC,
		a.id DESC";



		$request = $this -> select_all($query);
		return $request;
	}

	public function getActionPlan(string $franchise, string $period, string $type, string $auditor , string $shop_type, string $country, string $area, string $concept, string $area_manager, string $escalation1, string $escalation2 , string $lan){
		$date = date('Y-m-d H:i:s');




		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND";
		
		$sql = "SELECT 
		a.id,
		r.name AS 'round_name', 
		r.type AS 'audit_type', 
		a.auditor_name, 
		l.number AS 'location_number', 
		l.name AS 'location_name', 
		a.date_visit, 
		a.date_visit_end,
		-- Action Date con CASE
	CASE 
		WHEN (SELECT MAX(action_date) FROM audit_plan_action WHERE audit_opp_id = ap.id) IS NULL 
		THEN 'N/A'
		ELSE (SELECT MAX(action_date) FROM audit_plan_action WHERE audit_opp_id = ap.id)
	END AS action_date,

	-- Hours Difference con CASE
	CASE 
		WHEN (SELECT MAX(action_date) FROM audit_plan_action WHERE audit_opp_id = ap.id) IS NULL 
		THEN 'N/A'
		ELSE TIMESTAMPDIFF(
			HOUR, 
			a.date_visit, 
			(SELECT MAX(action_date) FROM audit_plan_action WHERE audit_opp_id = ap.id)
		)
	END AS hours_difference,
		ci.main_section,
		ci.section_name,
		ci.question_prefix,
		IFNULL(ci.$lan, ci.eng) 'text',
		ap.actionplan_status,
		(SELECT action_comment FROM audit_plan_action WHERE audit_opp_id = ap.id LIMIT 1) 'action_comm'
	FROM 
		audit a 
		INNER JOIN round r ON(a.round_id = r.id) 
		INNER JOIN location l ON(a.location_id = l.id) 
		INNER JOIN audit_opp ap ON(a.id = ap.audit_id)
		INNER JOIN checklist_item ci ON(ap.checklist_item_id = ci.id)
	WHERE 
		IFNULL(l.franchissees_name, 'NA') IN ('$franchise')  AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND $new_filter  a.auditor_email IN ('$auditor') ORDER BY a.date_visit DESC, a.id DESC";

	//echo $sql;

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
			(SELECT GROUP_CONCAT(email SEPARATOR ', ') FROM user WHERE FIND_IN_SET(l.id, location_id) AND role_id = 10) AS 'emails_gm', 
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
	
	public function frequencyOpp( string $franchise, string $period, string $type, string $auditor,string $shop_type,string $country,string $area,string $concept,string $area_manager,string $escalation1,string $escalation2, string $lan){
		
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";

		
		$query = "SELECT IFNULL(GROUP_CONCAT(a.id SEPARATOR ','), 0) AS 'stack' 
					FROM audit a 
					INNER JOIN location l ON(a.location_id=l.id) 
					INNER JOIN round r ON(a.round_id = r.id) 
					WHERE a.status = 'Completed' AND IFNULL(l.franchissees_name, 'NA') IN ('$franchise') 
					AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type')
					AND
					$new_filter a.auditor_email IN ('$auditor') 
					AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";
		$stack = $this->select($query)['stack'];

		$query = "SELECT ci.section_name, ci.question_prefix, 
						 IFNULL(ci.$lan, eng), 
						 (SELECT COUNT(*) FROM audit_point WHERE question_prefix=ci.question_prefix AND audit_id IN($stack)) opp, 
						 CONCAT(ROUND((SELECT opp * 100 / COUNT(*) 
				FROM audit 
				WHERE FIND_IN_SET(checklist_id, ciq.checklist_ids) 
					  AND id IN($stack)), 2), '%'), (SELECT COUNT(*) FROM audit_point WHERE question_prefix=ci.question_prefix AND audit_id IN($stack) AND id IN(SELECT ap.audit_point_id FROM audit_opp ap INNER JOIN checklist_item ci ON ap.checklist_item_id = ci.id AND ci.esp = 'Crítico')) critics, (SELECT COUNT(*) FROM audit_point WHERE question_prefix=ci.question_prefix AND audit_id IN($stack) AND id IN(SELECT ap.audit_point_id FROM audit_opp ap INNER JOIN checklist_item ci ON ap.checklist_item_id = ci.id AND ci.esp = 'No Crítico')) no_critics, ci.points FROM checklist_item ci INNER JOIN (SELECT MAX(id) id, GROUP_CONCAT(checklist_id) checklist_ids FROM checklist_item ci WHERE type = 'Question' GROUP BY question_prefix) ciq ON ci.id = ciq.id  ORDER BY opp DESC";
	echo $query;
	
	$request = $this -> select_all($query);
		return $request;
	}
	
	public function topOpp(string $lan, string $franchise, string $period, string $type, string $auditor,string $shop_type,string $country,string $area,string $concept,string $area_manager,string $escalation1,string $escalation2){
		
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";

		$request = [];

		$sql = "SELECT IFNULL(GROUP_CONCAT(a.id SEPARATOR ','), 0) AS 'stack', 
					   COUNT(*) AS count 
				FROM audit a 
				INNER JOIN location l ON (a.location_id = l.id) 
				INNER JOIN round r ON(a.round_id = r.id) 
				WHERE a.status = 'Completed' AND 
					  IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
					  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
					  r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND 
					  $new_filter
					  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";

		$audit = $this->select($sql);
	
		foreach(['SEGURIDAD DE ALIMENTOS', 'LIMPIEZA', 'MANTENIMIENTO'] as $mainSection){
			$query = "SELECT ap.question_prefix, (SELECT IFNULL($lan, eng) FROM checklist_item WHERE question_prefix = ap.question_prefix AND type = 'Question' ORDER BY id DESC LIMIT 1) AS 'text', COUNT(*) AS 'frecuency', {$audit['count']} AS 'count' FROM audit_point ap WHERE ap.audit_id IN({$audit['stack']}) AND (SELECT main_section FROM checklist_item WHERE question_prefix = ap.question_prefix LIMIT 1) = '$mainSection' GROUP BY ap.question_prefix ORDER BY frecuency DESC LIMIT 10";

			$request[ucfirst(strtolower($mainSection))] = $this->select_all($query); 
		}
		return $request;
	}

	public function leadership(string $franchise, string $period, string $type, string $auditor,string $shop_type,string $country,string $area,string $concept,string $area_manager,string $escalation1,string $escalation2){
		
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";

		$query = "SELECT 
						l.franchissees_name, 
						COUNT(*) AS 'visits', 
						ROUND(AVG(s.value_1), 2) AS 'criticos', 
						ROUND(AVG(s.value_2), 2) AS 'no_criticos', 
						ROUND(AVG(s.value_4), 2) AS 'amarillo',
						ROUND(AVG(s.value_5), 2) AS 'rojo',
						ROUND(AVG(s.value_6), 2) AS 'mantenimiento'
					FROM audit a 
					INNER JOIN location l ON(a.location_id = l.id) 
					INNER JOIN audit_score s ON (a.id = s.audit_id) 
					INNER JOIN round r ON (a.round_id = r.id)
					WHERE a.status = 'Completed' AND 
						  IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
						  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
						  r.type IN ('$type') AND 
						  a.auditor_email IN ('$auditor') AND 
						  $new_filter
						  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY l.franchissees_name";
						   
		

		$request = $this -> select_all($query);
		return $request;
	}

	public function actionPlanStatus(string $franchise, string $period, string $type, string $auditor,string $shop_type,string $country,string $area,string $concept,string $area_manager,string $escalation1,string $escalation2){
		
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";
		
		$query = "SELECT action_plan_status, 
					     COUNT(*) AS 'count' 
					FROM audit a 
					INNER JOIN location l ON(a.location_id = l.id) 
					INNER JOIN round r ON (a.round_id = r.id) 
					WHERE a.status = 'Completed' AND 
						  IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
						  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
						  r.type IN ('$type') AND 
						  a.auditor_email IN ('$auditor') AND 
						  $new_filter
						  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') 
					GROUP BY action_plan_status";
		//var_dump($query);
		$request = $this -> select_all($query);
		return $request;
	}
	
	public function daypart(string $franchise, string $period, string $type, string $auditor, string $shop_type,string $country,string $area,string $concept,string $area_manager,string $escalation1,string $escalation2){


		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";


		
		$query = "SELECT IFNULL(daypart, 'No registration') AS 'daypart', 
						 COUNT(*) AS 'count' 
					FROM audit a 
					INNER JOIN location l ON(a.location_id = l.id) 
					INNER JOIN round r ON (a.round_id = r.id) 
					WHERE a.status = 'Completed' AND 
						  IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
						  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
						  r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND 
						  $new_filter
						  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') 
					GROUP BY daypart";

		$request = $this -> select_all($query);
		return $request;
	}
	
	public function weekday(string $franchise, string $period, string $type, string $auditor, string $shop_type,string $country,string $area,string $concept,string $area_manager,string $escalation1,string $escalation2){
		
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";


		$query = "SELECT DAYOFWEEK(a.date_visit) AS 'weekday', 
						 COUNT(*) AS 'count' 
					FROM audit a 
					INNER JOIN location l ON(a.location_id = l.id) 
					INNER JOIN round r ON (a.round_id = r.id) 
					WHERE a.status = 'Completed' AND 
						  IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
						  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
						  r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND 
						  $new_filter
						  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') 
					GROUP BY weekday";

		$request = $this -> select_all($query);
		return $request;
	}

	public function duration(string $franchise, string $period, string $type, string $auditor, string $shop_type, string $country, string $area, string $concept, string $area_manager, string $escalation1, string $escalation2){
		
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";
		
		$query = "SELECT
					CASE
						WHEN TIMESTAMPDIFF(MINUTE, a.date_visit, a.date_visit_end) < 60 THEN 'Less than 1 hour'
						WHEN TIMESTAMPDIFF(MINUTE, a.date_visit, a.date_visit_end) < 91 THEN 'Less than 1 hour and 30 minutes'
						WHEN TIMESTAMPDIFF(MINUTE, a.date_visit, a.date_visit_end) < 120 THEN 'Less than 2 hours'
						ELSE 'Greater than 2 hours'
					END AS 'duration',
					COUNT(*) AS 'count'
				FROM audit a 
				INNER JOIN location l ON(a.location_id = l.id) 
				INNER JOIN round r ON (a.round_id = r.id)
				WHERE
					a.status = 'Completed' AND 
					a.date_visit IS NOT NULL AND 
					a.date_visit_end IS NOT NULL AND 
					IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
					IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
					r.type IN ('$type') AND 
					a.auditor_email IN ('$auditor') AND 
					$new_filter
					(a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') 
				GROUP BY duration";

		$request = $this -> select_all($query);
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
						  IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
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
				l.country,
				l.email_area_manager,
				l.franchissees_name,
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
				a.status = 'Completed' AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') ORDER BY a.checklist_id DESC, ao.audit_id DESC";

		

		$request = $this -> select_all($query);
		return $request;
	}


	public function reportCalidadDq(string $franchise, string $period, string $type, string $auditor, string $lan){
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
				checklist_item ci INNER JOIN audit_opp ao ON(ao.checklist_item_id=ci.id) INNER JOIN audit a ON(ao.audit_id=a.id) INNER JOIN audit_score ae ON a.id = ae.audit_id INNER JOIN round r ON(a.round_id = r.id) INNER JOIN location l ON(a.location_id = l.id) INNER JOIN (SELECT question_prefix, priority, checklist_id, $lan 'question' FROM checklist_item WHERE type = 'Question'  AND section_name IN('CALIDAD DQ')) q ON ci.question_prefix = q.question_prefix AND ci.checklist_id = q.checklist_id
			WHERE 
				a.status = 'Completed' AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') ORDER BY a.checklist_id DESC, ao.audit_id DESC";

		

		$request = $this -> select_all($query);
		return $request;
	}
	
	public function categoryTrend(string $franchise, string $period, string $type, string $auditor, string $shop_type, string $country, string $area, string $concept, string $area_manager, string $escalation1, string $escalation2){
		
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";
		
		$whereAudits = "SELECT a.id 
						FROM audit a 
						INNER JOIN location l ON (a.location_id = l.id) 
						INNER JOIN round r ON(a.round_id = r.id) 
						WHERE a.status = 'Completed' AND 
							  IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
							  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
							  r.type IN ('$type') AND 
							  a.auditor_email IN ('$auditor') AND 
							  $new_filter
							  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";

		$sql = "SELECT COUNT(*) opp, ci.section_name, ci.section_number, ci.main_section FROM (SELECT audit_id, audit_point_id, MAX(checklist_item_id) item_id FROM audit_opp WHERE audit_id IN($whereAudits) GROUP BY audit_id, audit_point_id) ao INNER JOIN checklist_item ci ON ao.item_id = ci.id GROUP BY ci.section_name, ci.section_number, ci.main_section ORDER BY opp DESC";

		return $this->select_all($sql);
	}

	public function questionTrend(string $lan, string $franchise, string $period, string $type, string $auditor, string $shop_type, string $country, string $area, string $concept, string $area_manager, string $escalation1, string $escalation2){
		
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";

		$whereAudits = "SELECT a.id 
						FROM audit a 
						INNER JOIN location l ON (a.location_id = l.id) 
						INNER JOIN round r ON(a.round_id = r.id) 
						WHERE a.status = 'Completed' AND 
						      IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
							  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
							  r.type IN ('$type') AND 
							  a.auditor_email IN ('$auditor') AND 
							  $new_filter
							  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";

		$sql = "SELECT ci.txt, ci.question_prefix, ci.section_number, COUNT(*) AS 'opp', REPLACE(TO_BASE64(GROUP_CONCAT(ap.id)), '\n', '') tk FROM audit_point ap INNER JOIN (SELECT question_prefix, IFNULL($lan, eng) AS 'txt', section_number FROM checklist_item WHERE id IN (SELECT MAX(id) FROM checklist_item WHERE type = 'Question' GROUP BY question_prefix)) ci ON ap.question_prefix = ci.question_prefix WHERE ap.audit_id IN($whereAudits) GROUP BY ci.txt, ci.question_prefix, ci.section_number ORDER BY opp";

		return $this->select_all($sql);
	}
	
	public function progressStatus(string $franchise, string $period, string $type, string $auditor, string $shop_type, string $country, string $area, string $concept, string $area_manager, string $escalation1, string $escalation2){
		

		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";


		$query = "SELECT GROUP_CONCAT(a.id SEPARATOR ',') AS 'stack' 
					FROM audit a 
					INNER JOIN location l ON(a.location_id=l.id) 
					INNER JOIN round r ON(a.round_id = r.id) 
					WHERE IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
						  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
						  r.type IN ('$type') AND 
						  a.auditor_email IN ('$auditor') AND 
						  $new_filter
						  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')AND MONTHNAME(a.date_visit) IS NOT NULL";

		$stack = $this->select($query)['stack']??'NULL';

		$query_country = "SELECT c.name AS 'label', 
								 SUM(IF(a.status='Pending', 1, 0)) AS 'pending', 
								 SUM(IF(a.status='In Process', 1, 0)) AS 'in_process', 
								 SUM(IF(a.status='Completed', 1, 0)) AS 'completed' 
						FROM audit a INNER JOIN location l ON(a.location_id=l.id) 
									 INNER JOIN country c ON(l.country_id=c.id) 
						WHERE a.id IN($stack) 
						GROUP BY label";

		$query_quarter = "SELECT r.name AS 'label', 
								 SUM(IF(a.status='Pending', 1, 0)) AS 'pending', 
								 SUM(IF(a.status='In Process', 1, 0)) AS 'in_process', 
								 SUM(IF(a.status='Completed', 1, 0)) AS 'completed' 
						FROM audit a INNER JOIN location l ON(a.location_id=l.id) 
									 INNER JOIN round r ON(a.round_id = r.id) 
						WHERE a.id IN($stack) 
						GROUP BY label";
		
		$query_month_name = "SELECT IFNULL(MONTHNAME(a.date_visit), 'N/A') AS 'label', 
									IFNULL(MONTH(a.date_visit), 0) AS 'month', 
									SUM(IF(a.status='Pending', 1, 0)) AS 'pending', 
									SUM(IF(a.status='In Process', 1, 0)) AS 'in_process', 
									SUM(IF(a.status='Completed', 1, 0)) AS 'completed' 
							FROM audit a INNER JOIN location l ON(a.location_id=l.id) 
							WHERE a.id IN($stack) 
							GROUP BY label, month ORDER BY month ASC";

		$request = [
			'Country'	=> $this->select_all($query_country),
			'Quarter'	=> $this->select_all($query_quarter),
			'Month'		=> $this->select_all($query_month_name)
		];
		return $request;
	}

	public function failureRate(string $franchise, string $period, string $type, string $auditor){
		$sql = "SELECT r.name AS 'period', SUM(IF(s.value_4='Rojo', 1, 0)) AS 'failure', COUNT(*) AS 'count' FROM audit a INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) INNER JOIN audit_score s ON(a.id=s.audit_id AND s.name='OVERALL SCORE') WHERE a.status='Completed' AND IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY period";

		$request = $this->select_all($sql);
		return $request;
	}
	
	public function ratingByDP(string $franchise, string $period, string $type, string $auditor){
		$sql = "SELECT ae.value_4 AS 'score', IFNULL(a.daypart, 'N/A') AS 'daypart', COUNT(*) AS 'count' FROM audit a INNER JOIN audit_score ae ON(ae.audit_id=a.id) INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status='Completed' AND IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY ae.value_4, a.daypart";

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
		$sql = "SELECT ae.value_4 AS 'score', r.name, COUNT(*) AS 'count' FROM audit a INNER JOIN audit_score ae ON(ae.audit_id=a.id) INNER JOIN location l ON(a.location_id = l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status='Completed' AND IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY ae.value_4, r.name";

		$request = [];
		foreach($this->select_all($sql) as $row){
			if(!array_key_exists($row['name'], $request)){
				$request[$row['name']] = ['label' => $row['name']];
			}
			$request[$row['name']][$row['score']] = $row['count'];
		}

		return array_values($request);
	}

	public function actionCompletion(string $franchise, string $period, string $type, string $auditor, string $shop_type, string $country, string $area, string $concept, string $area_manager, string $escalation1, string $escalation2){
		
		
		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";
		
		$sql = "SELECT action_plan_status, 
					   COUNT(*) AS 'count', IFNULL(MONTHNAME(a.date_visit), 'N/A') AS 'label', 
					   IFNULL(MONTH(a.date_visit), 0) AS 'month' 
				FROM audit a 
				INNER JOIN location l ON(a.location_id = l.id) 
				INNER JOIN round r ON (a.round_id = r.id) 
				WHERE a.id IN(SELECT audit_id FROM audit_point) AND 
					  a.status = 'Completed' AND 
					  IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
					  IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND 
					  r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND 
					  $new_filter
					  (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') 
				GROUP BY action_plan_status, label, month 
				ORDER BY month";
		
		$request = [];
		foreach($this->select_all($sql) as $row){
			if(!array_key_exists($row['label'], $request)){
				$request[$row['label']] = ['label' => $row['label']];
			}
			$request[$row['label']][$row['action_plan_status']] = $row['count'];
		}

		return array_values($request);
	}

	public function gallery(string $franchise, string $period,string $country,string $region, string $type, string $auditor,string $checklist ,string $audit_file, string $checklistItem){



		

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
				   WHERE a.status='Completed' AND IFNULL(l.franchissees_name, 'NA') IN ('$franchise') 
				   							  AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') 
											  AND r.type = '$type' 
											  AND a.auditor_email IN ('$auditor') 
											  AND c.name IN ('$country') 
											  AND c.region IN ('$region') 
											
											  AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') 
					ORDER BY a.checklist_id DESC";

		$request = [];


		
		





		foreach($this->select_all($query) as $audit){

			//$auditFiles = selectAuditFiles(['type', 'name', 'url', 'reference_id'], 'audit_id = ' . $audit['id']);
			
			$auditFiles = selectAuditFiles(
    ['type', 'name', 'url', 'reference_id'], 
    '(
        audit_id = '.$audit['id'].' 
        AND (
            (type = "Opportunity" 
                AND reference_id IN (
                    SELECT a.id
                    FROM audit_opp a
                    INNER JOIN checklist_item b ON a.checklist_item_id = b.id
                    WHERE audit_id = '.$audit['id'].' 
                      AND checklist_id IN('.$audit['checklist_id'].') 
                      AND question_prefix IN("'.$checklistItem.'") 
                      AND b.main_section IN("'.$checklist.'")
                )
            )
            OR (type IN("'.$audit_file.'") AND type != "Opportunity")
        )
    )'
);


																										

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

	public function getScoreTopBottom(string $franchise, string $period, string $type, string $auditor, string $shop_type, string $country, string $area, string $concept, string $area_manager, string $escalation1, string $escalation2){
		$params = [
			[
				'label'		=> 'top|SEGURIDAD DE ALIMENTOS',
				'section'	=> 'SEGURIDAD DE ALIMENTOS',
				'orden'		=> 'ASC'
			],
			[
				'label'		=> 'bottom|SEGURIDAD DE ALIMENTOS',
				'section'	=> 'SEGURIDAD DE ALIMENTOS',
				'orden'		=> 'DESC'
			],
			[
				'label'		=> 'top|LIMPIEZA',
				'section'	=> 'LIMPIEZA',
				'orden'		=> 'ASC'
			],
			[
				'label'		=> 'bottom|LIMPIEZA',
				'section'	=> 'LIMPIEZA',
				'orden'		=> 'DESC'
			],
			[
				'label'		=> 'top|MANTENIMIENTO',
				'section'	=> 'MANTENIMIENTO',
				'orden'		=> 'ASC'
			],
			[
				'label'		=> 'bottom|MANTENIMIENTO',
				'section'	=> 'MANTENIMIENTO',
				'orden'		=> 'DESC'
			]
		];

		$new_filter ="CASE WHEN l.shop_type IS NULL OR l.shop_type = '' THEN 'N/A' ELSE l.shop_type END IN ('$shop_type') AND
					  CASE WHEN l.country IS NULL OR l.country = '' THEN 'N/A' ELSE l.country END IN ('$country') AND
					  CASE WHEN l.area IS NULL OR l.area = '' THEN 'N/A' ELSE l.area END IN ('$area') AND
					  CASE WHEN l.concept IS NULL OR l.concept = '' THEN 'N/A' ELSE l.concept END IN ('$concept') AND
					  CASE WHEN l.email_ops_leader IS NULL OR l.email_ops_leader = '' THEN 'N/A' ELSE l.email_ops_leader END IN ('$escalation1') AND
					  CASE WHEN l.email_ops_director IS NULL OR l.email_ops_director = '' THEN 'N/A' ELSE l.email_ops_director END IN ('$escalation2') AND
					  CASE WHEN l.email_area_manager IS NULL OR l.email_area_manager = '' THEN 'N/A' ELSE l.email_area_manager END IN ('$area_manager')AND";

		$request = [];
		foreach($params as $p){
			$sql = "SELECT 
				l.number AS 'location_number', 
				l.name AS 'location_name',
				--  CONCAT('{$p['section']}',' -franchise- ','$franchise',' -period- ','$period',' -type- ','$type',' -auditor- ','$auditor',' -orden- ','{$p['orden']}',' -user- ','{$_SESSION['userData']['location_id']}')  score
				a_opp.cnt AS 'score'

			FROM audit a 
			INNER JOIN location l ON(a.location_id = l.id) 
			INNER JOIN round r ON (a.round_id = r.id) 
			INNER JOIN (SELECT ao.audit_id, COUNT(*) cnt FROM audit_opp ao INNER JOIN checklist_item ci ON ao.checklist_item_id = ci.id WHERE ci.main_section = '{$p['section']}' GROUP BY ao.audit_id) a_opp ON a.id = a_opp.audit_id 
			WHERE IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND 
				 IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND
				 r.type IN ('$type') AND 
				 a.auditor_email IN('$auditor') AND 
				 $new_filter
				 (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') AND a.status = 'Completed'
			ORDER BY 
				a_opp.cnt {$p['orden']} LIMIT 10";

			$request[$p['label']] = $this->select_all($sql);
		}

		return $request ;
	}

	public function programPreview($franchise, $type, $auditor, $country, $region , $months){
		$colPeriods = "";
		foreach($months as $mont){
			$colPeriods .= "CONCAT(SUM(IF(a.period = '$mont' AND a.status = 'Completed', 1, 0)), '/', COUNT(DISTINCT IF(a.period = '$mont', a.audita_id, NULL)), '/', REPLACE(TO_BASE64(GROUP_CONCAT(IF(a.period = '$mont', a.id, NULL))), '\n', '')) AS '$mont', ";
		}
		$filMonths = implode("','", $months);
 
		$query = "SELECT 
			a.auditor_email,
			$colPeriods
			CONCAT(SUM(IF(a.status = 'Completed', 1, 0)), '/',  COUNT(DISTINCT a.audita_id), '/', REPLACE(TO_BASE64(GROUP_CONCAT(a.id)), '\n', '')) AS 'avg'
		FROM 
			audit a INNER JOIN round r ON a.round_id = r.id INNER JOIN location l ON a.location_id = l.id INNER JOIN country c ON l.country_id = c.id 
		WHERE 
			IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND a.period IN('$filMonths') AND r.type IN ('$type') AND a.status IN ('In Process', 'Pending', 'Completed') AND a.auditor_email IN ('$auditor') AND r.type IN('Standard') AND
			IFNULL(c.name, 'NA') IN ('$country') AND IFNULL(c.region, 'NA') IN ('$region') 
		GROUP BY 
			a.auditor_email";

			//echo $query ;
			//die();
		$groupBy = $this -> select_all($query);
		
		$query = "SELECT 
			'total' AS auditor_email,
			$colPeriods
			CONCAT(SUM(IF(a.status = 'Completed', 1, 0)), '/', COUNT(*), '/', REPLACE(TO_BASE64(GROUP_CONCAT(a.id)), '\n', '')) AS 'avg'
		FROM 
			audit a INNER JOIN round r ON a.round_id = r.id INNER JOIN location l ON a.location_id = l.id INNER JOIN country c ON l.country_id = c.id 
		WHERE 
			IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND a.period IN('$filMonths') AND r.type IN ('$type') AND a.status IN ('In Process', 'Pending', 'Completed') AND a.auditor_email IN ('$auditor') AND
			IFNULL(c.name, 'NA') IN ('$country') AND IFNULL(c.region, 'NA') IN ('$region') ";
		//echo $query;
		//die();
		
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

		$query_audits = "SELECT a.id 
							FROM audit a 
						INNER JOIN location l ON (a.location_id = l.id) 
						INNER JOIN round r ON(a.round_id = r.id) 
						WHERE a.status = 'Completed' AND IFNULL(l.franchissees_name, 'NA') IN ('$franchise') 
													 AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') 
													 AND r.type IN ('$type') 
													 AND a.auditor_email IN ('$auditor') 
													 AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";
		
		$query_details = "SELECT ci.question_prefix, 
						 	     COUNT(1) count, 
						 	     IFNULL(ci.$lan, ci.eng) txt ,
								 auditor_answer
						  FROM audit_opp ao 
						  INNER JOIN checklist_item ci ON ao.checklist_item_id = ci.id WHERE ci.question_prefix = '$qprefix' AND audit_id IN($query_audits) GROUP BY txt,auditor_answer";

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
			IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') AND a.status = 'Completed'";
		
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function oppPerSection(string $franchise, string $period, string $type, string $auditor){

		$query = "SELECT IFNULL(GROUP_CONCAT(a.id SEPARATOR ','), 0) AS 'stack', COUNT(*) cnt FROM audit a INNER JOIN location l ON(a.location_id=l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";

		$audits = $this->select($query);

		$sql = "SELECT ci.main_section, ci.section_name, ci.question_prefix, {$audits['cnt']}, IFNULL(ap.cnt, 0), CONCAT(ROUND(IFNULL(ap.cnt / {$audits['cnt']}, 0) * 100, 2), '%') FROM (SELECT DISTINCT main_section, section_name, question_prefix FROM checklist_item WHERE checklist_id = (SELECT MAX(id) FROM checklist)) ci LEFT JOIN (SELECT question_prefix, COUNT(*) cnt FROM audit_point WHERE audit_id IN ({$audits['stack']}) GROUP BY question_prefix) ap ON ci.question_prefix = ap.question_prefix";
		
		$request = $this->select_all($sql);
		return $request;
	}
	
	public function oppPerAuditor(string $franchise, string $period, string $type, string $auditor){

		$query = "SELECT a.auditor_email, IFNULL(GROUP_CONCAT(a.id SEPARATOR ','), 0) AS 'stack', COUNT(*) cnt FROM audit a INNER JOIN location l ON(a.location_id=l.id) INNER JOIN round r ON(a.round_id = r.id) WHERE a.status = 'Completed' AND IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN ('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') GROUP BY a.auditor_email";

		$request = [];
		foreach($this->select_all($query) as $auditor){
			$sql = "SELECT '{$auditor['auditor_email']}' as 'auditor_email', ci.main_section, ci.section_name, ci.question_prefix, {$auditor['cnt']}, IFNULL(ap.cnt, 0), CONCAT(ROUND(IFNULL(ap.cnt / {$auditor['cnt']}, 0) * 100, 2), '%') FROM (SELECT DISTINCT main_section, section_name, question_prefix FROM checklist_item WHERE checklist_id = (SELECT MAX(id) FROM checklist)) ci LEFT JOIN (SELECT question_prefix, COUNT(*) cnt FROM audit_point WHERE audit_id IN ({$auditor['stack']}) GROUP BY question_prefix) ap ON ci.question_prefix = ap.question_prefix";

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
			IFNULL(l.franchissees_name, 'NA') IN ('$franchise') AND IFNULL(a.period, DATE_FORMAT(a.date_visit, '%Y-%m')) IN ('$period') AND r.type IN ('$type') AND a.auditor_email IN('$auditor') AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0') AND a.status = 'Completed'";

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

	public function getFranchissees(){
		$sql = "SELECT DISTINCT IFNULL(l.franchissees_name, 'NA') AS 'name' FROM location l WHERE l.country_id IN ({$_SESSION['userData']['country_id']}) AND (l.id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'='0')";

		$request = $this->select_all($sql);
		return $request;
	}

	public function getChecklist(){
		
		$sql = "SELECT main_section FROM `checklist_item` group by main_section";

		$request = $this->select_all($sql);
		return $request;
	}

	public function getChecklistItem(){
		
		$sql = "SELECT question_prefix,
					   eng,
					   section_name
				FROM checklist_item
				WHERE type = 'Question'
				GROUP BY question_prefix,eng,section_name";

		$request = $this->select_all($sql);
		return $request;
	}


	public function getAuditFile(){
		
		$sql = "SELECT type  FROM audit_file GROUP BY type ORDER BY type DESC";

		$request = $this->select_all($sql);
		return $request;
	}


	public function exportUserPass(){
		$sql = "SELECT 
				a.name user_name,
				email,
				IF(a.password = 'X', 'NO', 'SI')  password,
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
                    (SELECT franchissees_name FROM location y WHERE y.id IN(a.location_id) ) franchissees_name,
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
				//var_dump($query);die();
		$res = new Mysql;
		$request = $res->select_all($query);
		return $request;
	}


	//MOSTRAR DATATABLE ACTION PLAN
	public function actionPlanTable(string $countries, string $period, string $audit_type, string $status, string $franchise, string $area_manager){

		$query = NULL; 
		
		if($_SESSION['userData']['country_id'] == 32 && $_SESSION['userData']['role']['id'] == 13){

		$query = "SELECT
		id,
		report_layout_id,
		brand_prefix,
        country_name,
		location_name,
		location_number,
		type,
		round_name,
		date_visit,
		action_plan_status
		FROM audit_list 
		WHERE status = 'Completed' AND country_id IN ($countries) AND franchissees_name IN('$franchise') AND email_area_manager IN('$area_manager') AND type IN ($audit_type) AND action_plan_status IN ($status) AND round_name IN ('$period') AND franchise_id IN (".$_SESSION['userData']['franchise_id'].")";

		}else{

		$query = "SELECT
		id,
		report_layout_id,
		brand_prefix,
        country_name,
		location_name,
		location_number,
		type,
		round_name,
		date_visit,
		action_plan_status
		FROM audit_list 
		WHERE status = 'Completed' AND country_id IN ($countries) AND franchissees_name IN('$franchise') AND email_area_manager IN('$area_manager')  AND type IN ($audit_type) AND action_plan_status IN ($status) AND round_name IN ('$period')";

		}

	
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}



	//MOSTRAR TIPO DE VISITITAS
	public function selectTypeVisits(){
		$sql = "SELECT DISTINCT type FROM audit_list  
		WHERE type NOT IN ('Self-Evaluation', 'Calibration Audit') 
		ORDER BY type DESC";
		
		$request = $this->select_all($sql);
		return $request;
	}

	//MOSTRAR TIPO DE SECCION
	public function listsectionCheck(){
		$sql = "SELECT DISTINCT main_section FROM checklist_item WHERE checklist_id = 3";
		
		$request = $this->select_all($sql);
		return $request;
	}

	//MOSTRAR SELECT DINAMICO DE PREGUNTAS

	public function getQuestion(string $section, string $lenguage){
		$sectionsArray = explode(',', $section); 
		$sections = "'" . implode("','", $sectionsArray) . "'";
		
		$sql = "SELECT DISTINCT question_prefix as id_question, $lenguage FROM checklist_item WHERE checklist_id = 3 AND main_section IN ($sections) AND type = 'Question' ORDER BY id_question ASC";
		$request = $this->select_all($sql);
		return $request;
	}	


	//MOSTRAR DATATABLE DISTRICT REPORT GLOBAL
	public function districtReportGlobalTable(string $countries, string $years,string $franchise, string $area_manager){

		
		$query = "SELECT 
		audit_result calification, 
		COUNT(CASE WHEN type IN ('Standard') AND round_name LIKE '%Round 1 $years%' THEN 1 END) AS auditoria1,
		COUNT(CASE WHEN type IN ('Re-Audit') AND round_name LIKE '%Round 1 $years%' THEN 1 END) AS re_auditoria1,
		COUNT(CASE WHEN TYPE IN ('2nd Re-Audit') AND round_name LIKE '%Round 1 $years%' THEN 1 END) AS re_auditoria2_Q1, 
		COUNT(CASE WHEN TYPE IN ('3rd Re-Audit') AND round_name LIKE '%Round 1 $years%' THEN 1 END) AS re_auditoria3_Q1,		
		COUNT(CASE WHEN TYPE IN ('4th Re-Audit') AND round_name LIKE '%Round 1 $years%' THEN 1 END) AS re_auditoria4_Q1,		
		COUNT(CASE WHEN type IN ('Standard') AND round_name LIKE '%Round 2 $years%' THEN 1 END) AS auditoria2,
		COUNT(CASE WHEN type IN ('Re-Audit') AND round_name LIKE '%Round 2 $years%' THEN 1 END) AS re_auditoria2,
		COUNT(CASE WHEN TYPE IN ('2nd Re-Audit') AND round_name LIKE '%Round 2 $years%' THEN 1 END) AS re_auditoria2_Q2, 
		COUNT(CASE WHEN TYPE IN ('3rd Re-Audit') AND round_name LIKE '%Round 2 $years%' THEN 1 END) AS re_auditoria3_Q2,
		COUNT(CASE WHEN TYPE IN ('4th Re-Audit') AND round_name LIKE '%Round 2 $years%' THEN 1 END) AS re_auditoria4_Q2,
		COUNT(CASE WHEN type IN ('Standard') AND round_name LIKE '%Round 3 $years%' THEN 1 END) AS auditoria3,
		COUNT(CASE WHEN type IN ('Re-Audit') AND round_name LIKE '%Round 3 $years%' THEN 1 END) AS re_auditoria3,
		COUNT(CASE WHEN TYPE IN ('2nd Re-Audit') AND round_name LIKE '%Round 3 $years%' THEN 1 END) AS re_auditoria2_Q3, 
		COUNT(CASE WHEN TYPE IN ('3rd Re-Audit') AND round_name LIKE '%Round 3 $years%' THEN 1 END) AS re_auditoria3_Q3,
		COUNT(CASE WHEN TYPE IN ('4th Re-Audit') AND round_name LIKE '%Round 3 $years%' THEN 1 END) AS re_auditoria4_Q3,
		COUNT(CASE WHEN type IN ('Standard') AND round_name LIKE '%Round 4 $years%' THEN 1 END) AS auditoria4,
		COUNT(CASE WHEN type IN ('Re-Audit') AND round_name LIKE '%Round 4 $years%' THEN 1 END) AS re_auditoria4,
		COUNT(CASE WHEN TYPE IN ('2nd Re-Audit') AND round_name LIKE '%Round 4 $years%' THEN 1 END) AS re_auditoria2_Q4, 
		COUNT(CASE WHEN TYPE IN ('3nd Re-Audit') AND round_name LIKE '%Round 4 $years%' THEN 1 END) AS re_auditoria3_Q4,
		COUNT(CASE WHEN TYPE IN ('4th Re-Audit') AND round_name LIKE '%Round 4 $years%' THEN 1 END) AS re_auditoria4_Q4,
		COUNT(CASE WHEN type = '2nd Re-Audit' AND round_name LIKE'%$years%' THEN 1 END) AS 2nd_re_audit,
		COUNT(CASE WHEN type = '3rd Re-Audit' AND round_name LIKE'%$years%' THEN 1 END) AS 3rd_re_audit
		FROM audit_list 
		WHERE  country_id IN ($countries) 
		AND calification IS NOT NULL  
		AND audit_list.status = 'Completed' and audit_result not in('N/A','NA')
		 AND franchissees_name IN('$franchise') AND email_area_manager IN('$area_manager')
		GROUP BY calification  
		ORDER BY calification ASC";

		// echo $query;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	//MOSTRAR TOTALES DATATABLE DISTRICT REPORT GLOBAL
	public function districtReportTotal(string $countries, string $years, string $franchise, string $area_manager){

		
		$query = "SELECT 
		COUNT(CASE WHEN type = 'Standard' AND round_name LIKE '%Round 1 $years%' THEN 1 END) AS total_auditoria1,
		COUNT(CASE WHEN type = 'Re-Audit' AND round_name LIKE '%Round 1 $years%' THEN 1 END) AS total_re_auditoria1,
		COUNT(CASE WHEN type = 'Standard' AND round_name LIKE '%Round 2 $years%' THEN 1 END) AS total_auditoria2,
		COUNT(CASE WHEN type = 'Re-Audit' AND round_name LIKE '%Round 2 $years%' THEN 1 END) AS total_re_auditoria2,
		COUNT(CASE WHEN type = 'Standard' AND round_name LIKE '%Round 3 $years%' THEN 1 END) AS total_auditoria3,
		COUNT(CASE WHEN type = 'Re-Audit' AND round_name LIKE '%Round 3 $years%' THEN 1 END) AS total_re_auditoria3,
		COUNT(CASE WHEN type = 'Standard' AND round_name LIKE '%Round 4 $years%' THEN 1 END) AS total_auditoria4,
		COUNT(CASE WHEN type = 'Re-Audit' AND round_name LIKE '%Round 4 $years%' THEN 1 END) AS total_re_auditoria4,
		COUNT(CASE WHEN type = '2nd Re-Audit' AND round_name LIKE'%$years%' THEN 1 END) AS total_re_auditoria2nd,
		COUNT(CASE WHEN type = '3rd Re-Audit' AND round_name LIKE'%$years%' THEN 1 END) AS total_re_auditoria3rd
	    FROM audit_list 
	    WHERE country_id IN ($countries) AND franchissees_name IN('$franchise') AND email_area_manager IN('$area_manager') AND calification IS NOT NULL;";
		

		

		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	//MOSTRAR DATATABLE DISTRICT REPORT TIENDAS
	public function districtReportStoreTable(string $countries, string $years ,string $franchise, string $area_manager){

		
		$query = "SELECT 
        location_number,
        location_name,
		email_franchisee, 
		email_area_manager,
		
		(SELECT GROUP_CONCAT(z.name SEPARATOR ', ') FROM user z WHERE z.role_id  = 11 AND z.country_id IN (1) AND FIND_IN_SET(a.location_id, z.location_id)) AS consultor,
		(SELECT GROUP_CONCAT(z.name SEPARATOR ', ') FROM user z WHERE z.role_id = 14 AND z.country_id IN ($countries) AND FIND_IN_SET(a.location_id, z.location_id)) AS distrital,
        MAX(CASE WHEN type IN ('Standard') AND round_name LIKE '%Round 1 $years%' THEN audit_result END) AS Q1,
        MAX(CASE WHEN type IN ('Re-Audit') AND round_name LIKE '%Round 1 $years%' THEN audit_result END) AS Q1R,
		MAX(CASE WHEN type IN ('2nd Re-Audit') AND round_name LIKE '%Round 1 $years%' THEN audit_result END) AS SegundaReauditQ1,
        MAX(CASE WHEN type IN ('3rd Re-Audit') AND round_name LIKE '%Round 1 $years%' THEN audit_result END) AS tercerReauditQ1,
        MAX(CASE WHEN type IN ('4th Re-Audit') AND round_name LIKE '%Round 1 $years%' THEN audit_result END) AS cuartaReauditQ1,
        MAX(CASE WHEN type IN ('Standard') AND round_name LIKE '%Round 2 $years%' THEN audit_result END) AS Q2,
        MAX(CASE WHEN type IN ('Re-Audit') AND round_name LIKE '%Round 2 $years%' THEN audit_result END) AS Q2R,
		MAX(CASE WHEN type IN ('2nd Re-Audit') AND round_name LIKE '%Round 2 $years%' THEN audit_result END) AS SegundaReauditQ2,
        MAX(CASE WHEN type IN ('3rd Re-Audit') AND round_name LIKE '%Round 2 $years%' THEN audit_result END) AS tercerReauditQ2,
        MAX(CASE WHEN type IN ('4th Re-Audit') AND round_name LIKE '%Round 2 $years%' THEN audit_result END) AS cuartaReauditQ2,
        MAX(CASE WHEN type IN ('Standard') AND round_name LIKE '%Round 3 $years%' THEN audit_result END) AS Q3,
        MAX(CASE WHEN type IN ('Re-Audit') AND round_name LIKE '%Round 3 $years%' THEN audit_result END) AS Q3R,
		MAX(CASE WHEN type IN ('2nd Re-Audit') AND round_name LIKE '%Round 3 $years%' THEN audit_result END) AS SegundaReauditQ3,
		MAX(CASE WHEN type IN ('3rd Re-Audit') AND round_name LIKE '%Round 3 $years%' THEN audit_result END) AS tercerReauditQ3,
        MAX(CASE WHEN type IN ('4th Re-Audit') AND round_name LIKE '%Round 3 $years%' THEN audit_result END) AS cuartaReauditQ3,
        MAX(CASE WHEN type IN ('Standard') AND round_name LIKE '%Round 4 $years%' THEN audit_result END) AS Q4,
        MAX(CASE WHEN type IN ('Re-Audit') AND round_name LIKE '%Round 4 $years%' THEN audit_result END) AS Q4R,
		MAX(CASE WHEN type IN ('2nd Re-Audit') AND round_name LIKE '%Round 4 $years%' THEN audit_result END) AS SegundaReauditQ4,
        MAX(CASE WHEN type IN ('3rd Re-Audit') AND round_name LIKE '%Round 4 $years%' THEN audit_result END) AS tercerReauditQ4,
        MAX(CASE WHEN type IN ('4th Re-Audit') AND round_name LIKE '%Round 4 $years%' THEN audit_result END) AS cuartaReauditQ4,
        MAX(CASE WHEN type IN ('2nd Re-Audit') AND round_name LIKE'%$years%' THEN audit_result END) AS 2ndR,
        MAX(CASE WHEN type IN ('3rd Re-Audit') AND round_name LIKE'%$years%' THEN audit_result END) AS 3rdR
        FROM audit_list a
        WHERE a.country_id IN ($countries) 
        AND calification IS NOT NULL
		AND a.status = 'Completed' and audit_result not in('N/A','NA')
		AND franchissees_name IN('$franchise') AND email_area_manager IN('$area_manager')
        GROUP BY a.location_id,location_name";

		 //echo $query;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}


	
	//VIEW DATATABLE PROGRESS STATUS
	public function revisitsProgressReauditsTable(string $countries, string $period){
		
		$sql = "SELECT 
	        	al.*, 
	        	DATEDIFF(NOW(), al.date_release) AS deadline, 
	        	sc.value_1 AS fs, 
	        	sc.value_2 AS bs, 
	        	sc.value_3 AS cali,
	        	ra1.calification AS reaudit,
	        	ra1.date_visit AS fecha_reaudit,
	        	ra1.id AS id_reaudit,
	        	ra2.calification AS 2ndR,
	        	ra2.date_visit AS fecha_2ndR,
	        	ra2.id AS id_2ndR,
	        	ra3.calification AS 3ndR,
	        	ra3.date_visit AS fecha_3ndR,
	        	ra3.id AS id_3ndR
	        FROM audit_list al
	        JOIN audit_score sc ON al.id = sc.audit_id
	        LEFT JOIN audit_list ra1 
	        	ON ra1.location_id = al.location_id 
	        	AND ra1.round_name = '$period'
	        	AND ra1.type = 'Re-Audit' 
	        	AND ra1.status = 'Completed'
	        LEFT JOIN audit_list ra2 
	        	ON ra2.location_id = al.location_id 
	        	AND ra2.round_name = '$period' 
	        	AND ra2.type = '2nd Re-Audit' 
	        	AND ra2.status = 'Completed'
	        LEFT JOIN audit_list ra3 
	        	ON ra3.location_id = al.location_id 
	        	AND ra3.round_name = '$period' 
	        	AND ra3.type = '3rd Re-Audit' 
	        	AND ra3.status = 'Completed'
	        WHERE al.country_id  in ($countries)
	          AND al.calification IN ('D','F')
	          AND al.status = 'Completed'
	          AND al.type = 'Standard'
	          AND al.round_name = '$period'
	          order by id asc";

		$request = $this->select_all($sql);
		return $request;
	}

	
	public function selectCountriesValidates(){
		$sql = "SELECT id, name FROM country 
				where id in (SELECT country_id
							FROM round
							where type in ('Standard')
							group by country_id
							HAVING COUNT(1) > 1)";
		
		$request = $this->select_all($sql);
		return $request;
	} 

		public function getInfoHistorical(int $location_id, string $date_visit, int $limit_number) {
		$limit = $limit_number ? 'limit '.$limit_number : '';
		$sql = "SELECT al.*, sc.value_1 as fs, sc.value_2 as bs, audit_result as cali
				FROM audit_list al
				JOIN audit_score sc
				ON al.id = sc.audit_id
				where al.location_id = $location_id
				and al.type in ('Standard','Re-Audit')
				and al.date_visit < '$date_visit'
				and al.status = 'Completed'
				order by al.date_visit desc $limit";
		// echo $sql;
		$request = $this->select_all($sql);
		return $request;
	}
	public function getInfoProgrees(int $audit_id, int $location_id, string $type, string $date_visit) {

		$sql = "SELECT * FROM audit_list 
				where location_id = $location_id
				and type in ('Re-Audit')
				and (date_visit > '$date_visit' or id > $audit_id) 
				and status not in ('Deleted!')
				order by date_visit asc 
				limit 1";

		//and type in ('Standard','Re-Audit')
		//echo "<br>".$sql;
		
		$request = $this->select_all($sql);
		return $request;
	}

	public function getRevisitsStatus(string $countries, string $period, string $audit_type, string $franchise, string $area_manager){
		
		$sql = "SELECT al.*, DATEDIFF(now(), al.date_release) deadline, sc.value_1 as fs, sc.value_2 as bs, sc.value_3 as cali 
				FROM audit_list al
				JOIN audit_score sc
				ON al.id = sc.audit_id
				where al.country_id in ($countries)
			
				and al.status = 'Completed'
				and al.type in ($audit_type)
				and al.round_name = '$period'
				AND franchissees_name IN('$franchise') AND email_area_manager IN('$area_manager')
				order by id asc";

		
	
		$request = $this->select_all($sql);
		return $request;
	}

	public function selectAuditTypes(){
		$sql = "SELECT DISTINCT type FROM audit_list WHERE type NOT IN ('Calibration Audit', 'Self-Evaluation')";
		
		$request = $this->select_all($sql);
		return $request;
	}


	

}
?>