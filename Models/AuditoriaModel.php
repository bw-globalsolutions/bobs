<?php
class AuditoriaModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getAudit($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ." 
				  FROM audit 
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
	
	//Nueva auditoria
	public function insertAudit($args){
		
		//query y values de argumentos
		$query = "INSERT INTO audit SET ";
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

	//Nueva auditoria
	public function updateAudit($args=[], $condition="id=0"){
		
		//query y values de argumentos
		$query = "UPDATE audit SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2) . " WHERE $condition ";
		
		$res = new Mysql;
		$request = $res -> update($query, $values);
		
		return $request;
	}

	public function getAuditsActionPlan(){
		$query = "SELECT *, now() ahora, DATEDIFF(now(), date_release) diferencia FROM audit where date_release is not null  and action_plan_status != 'Finished'";
		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
	}

	public function getAuditsActionPlanReminderSecond(){
		$now = date('Y-m-d H:i:s');
		$query = "SELECT al.*, '$now' ahora, DATEDIFF('$now', al.date_release) diferencia FROM audit_list al where DATEDIFF('$now', al.date_release) = 1 and al.type in ('Standard','Re-Audit') and action_plan_status = 'Pending' and al.id in (SELECT ae.audit_id FROM audit_score ae WHERE ae.value_1 = 0 AND ae.name = 'OVERALL SCORE')";
		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
	}
	
	public function getAuditsActionPlanReminderCritical(){
		$now = date('Y-m-d H:i:s');
		$query = "SELECT al.*, '$now' ahora, DATEDIFF('$now', al.date_release) diferencia FROM audit_list al where TIMESTAMPDIFF(HOUR, al.date_release, '$now') = 12 and al.type in ('Standard','Re-Audit') and action_plan_status = 'Pending' and al.id in (SELECT ae.audit_id FROM audit_score ae WHERE ae.value_1 > 0 AND ae.name = 'OVERALL SCORE')";
		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
	}

	public function getAuditsActionPlanReminderFinal(){
		$query = "SELECT *, now() ahora, DATEDIFF(now(), date_release) diferencia FROM audit_list where DATEDIFF(now(), date_release) = 7 and type in ('Standard','Re-Audit') and action_plan_status = 'Pending'";
		//$query = "SELECT *, now() ahora, DATEDIFF(now(), date_release) diferencia FROM audit_list where DATEDIFF(now(), date_release) BETWEEN 7 AND 12 and type in ('Standard','Re-Audit') and action_plan_status = 'Pending'";
		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
	}

	public function getAuditsActionPlanReminderExpired($country_ids = null, $location_ids = null){
		$now = date('Y-m-d H:i:s');
		$where = is_null($country_ids)? "location_id IN($location_ids)" : "country_id IN($country_ids)";
		$query = "SELECT *, '$now' ahora, DATEDIFF('$now', date_release) diferencia FROM audit_list where type in ('Standard','Re-Audit') and action_plan_status = 'Pending' AND IF(id in(SELECT ae.audit_id FROM audit_score ae WHERE ae.value_1 > 0 AND ae.name = 'OVERALL SCORE'), DATEDIFF('$now', date_release) = 1, DATEDIFF('$now', date_release) = 7) AND $where";

		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
	}

	public function getAuditsActionPlanReminder(){
				
		$query = "SELECT date_release fecha_termino, now() ahora, DATEDIFF(now(), date_release) diferencia FROM audit where date_release is not null  and action_plan_status = 'Pending'";
		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
	}

	public function getInProcessAudits(){
		$query = "SELECT
    				al.id,
					al.STATUS,
    				audited_areas,
    				TYPE,
    				date_visit,
    				round_name,
    				al.country_id,
    				country_name,
    				location_id,
    				location_number,
    				location_name,
    				location_address,
    				al.brand_id,
    				brand_prefix,
    				report_layout_id,
    				manager_email,
    				email_store_manager,
					al.email_franchisee,
					al.email_area_manager,
                    country_language,
					region
				  FROM
				      audit_list al
				  INNER JOIN location a ON al.location_id = a.id
				  WHERE TYPE IN('Standard', 'Re-Audit', '2nd Re-Audit')  AND al.status = 'In Process' AND TIMESTAMPDIFF(HOUR, (SELECT date FROM audit_log WHERE  audit_id = al.id ORDER BY id DESC LIMIT 1), NOW()) > 11 AND date_visit > '2024-01-01 00:00:00'";
		//echo $query;
		$res = new Mysql;
		$request = $res->select_all($query);
		return $request;
	}


	public function reAudit($id_visit){	

		$query = "SELECT 
					a.id id_visit, 
					b.name round,
					a.period,
					b.type,
					c.name nombre_tienda,
					c.number numero_tienda,
					auditor_name,
					auditor_email, 
					a.status,
					(SELECT COUNT(*) FROM audit_opp y INNER JOIN checklist_item z ON y.checklist_item_id = z.id WHERE y.audit_id = a.id AND main_section IN('SEGURIDAD DE ALIMENTOS') AND esp IN('Critico'))seguridad_alimentos, 
					(SELECT COUNT(*) FROM audit_opp y INNER JOIN checklist_item z ON y.checklist_item_id = z.id WHERE y.audit_id = a.id AND main_section IN('LIMPIEZA') AND auditor_answer IN('1.- Rojo'))limpieza, 
					location_id,
					(SELECT id FROM round y WHERE y.name = b.name AND y.type = 'Re-Audit') id_round,
					b.brand_id,
					additional_question_id,
					scoring_id,
					checklist_id,
					report_layout_id,
					local_foranea,
					address_1,
					franchissees_name,
					DATE(date_visit)date_visit,

					c.email email_location,
                    email_main_office,
				    email_store_manager,
				    email_franchisee,
				    email_area_manager,
				    email_ops_leader,
				    email_ops_director
					FROM audit a
					INNER JOIN round b ON a.round_id = b.id
					INNER JOIN location c ON a.location_id = c.id
					HAVING status IN('Completed') AND 
                    (seguridad_alimentos > 2 || limpieza > 10) AND 
                    DATE(date_visit) > '2025-01-31' AND 
					DATE(date_visit) < '2025-02-17' AND
                    type NOT IN('Self-Evaluation') AND id_visit not in(1529,1615,1609,1604,1599,1575,1573,1564) ";
			
		$res = new Mysql;
		$request = $res->select_all($query);
		return $request;
			
	}





	public function insertReAudit(string $round_id, 
								  string $checklist_id ,
								  string $scoring_id,
								  string $additional_question_id,
								  string $location_id, 
								  string $report_layout_id,
								  string $auditor_name,
								  string $auditor_email,
								  string $local_foranea){

 
			$query = "INSERT INTO audit (round_id,
										 checklist_id,
										 scoring_id,
										 additional_question_id,
										 location_id,
										 report_layout_id,
										 auditor_email,
										 auditor_name,
										 local_foranea,
										 status ) 
						VALUES (?,?,?,?,?,?,?,?,?,?) ";



			$values = array($round_id, $checklist_id, $scoring_id, $additional_question_id,$location_id,$report_layout_id,$auditor_name,$auditor_email,$local_foranea,'Pending');
			$res = new Mysql;
			$request = $res -> insert($query, $values);
		
		return $request;
		}
	


	
	
}
?>