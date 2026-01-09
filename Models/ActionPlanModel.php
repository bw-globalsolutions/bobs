<?php
class ActionPlanModel extends Mysql {
	
	public function __construct(){
		parent::__construct();
	}
	    	
	public function getOppsPlan(int $idAudit){
		$hora_actual = date('Y-m-d H:i:s');
		
		$query = "SELECT 
					t1.id id_audit_opp,
					t1.checklist_item_id,
					t1.audit_point_id,
					t1.audit_id,
					t1.auditor_answer,
					t1.auditor_comment,
					t1.actionplan_status,
					t1.actionplan_comment,
					t1.actionplan_owner,
					t1.actionplan_date,
					(SELECT item_action_plan FROM audit_plan_action WHERE audit_opp_id = t1.id LIMIT 1 )item_action_plan,

					(SELECT COUNT(*) FROM audit_opp b INNER JOIN checklist_item c ON b.checklist_item_id = c.id WHERE b.audit_id = ".$idAudit." AND  (c.eng IN('No critico') OR c.main_section IN('LIMPIEZA')))no_critico,
					(SELECT COUNT(*) FROM audit_opp b INNER JOIN checklist_item c ON b.checklist_item_id = c.id WHERE b.audit_id = ".$idAudit." AND eng IN('Crítico'))critico,
					(SELECT COUNT(*) FROM audit_opp b INNER JOIN checklist_item c ON b.checklist_item_id = c.id WHERE b.audit_id = ".$idAudit." AND   c.main_section IN('MANTENIMIENTO'))mantenimiento,

					TIMESTAMPDIFF(HOUR, date_visit_end, '".$hora_actual."') AS diferencia_en_horas,
					t2.*
				FROM audit_opp t1
				INNER JOIN audit a ON t1.audit_id = a.id
				LEFT JOIN (SELECT t3.*,t4.question,t4.priority as questionP,t4.priorityV as questionV
							FROM checklist_item t3
							INNER JOIN (SELECT question_prefix, IFNULL({$_SESSION['userData']['default_language']}, eng) AS 'question', priority as priorityV, IF(priority = 'Critical', 0, 1) priority
										FROM checklist_item
										WHERE type = 'Question' AND checklist_id = (SELECT checklist_id FROM audit WHERE id = ".$idAudit." limit 1) ) t4 ON t3.question_prefix = t4.question_prefix WHERE t3.type = 'Picklist') t2 ON t1.checklist_item_id = t2.id
				WHERE t1.audit_id = ".$idAudit." 
				ORDER BY questionP";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getFilesOpp(int $idOpp){
				
		$query = "SELECT *
				FROM audit_file 
				where type = 'Opportunity' AND reference_id = ".$idOpp;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getActions(int $idOpp){

		$query = "SELECT t1.*, (SELECT GROUP_CONCAT(op SEPARATOR '<br><br>') FROM action_plan_op WHERE FIND_IN_SET(id, t1.checks_action_plan)) checks 
				FROM audit_plan_action t1
				where audit_opp_id = ".$idOpp;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function insertPlanAction(int $intIdOpp, string $strAccion, string $strAccionDate, string $strStatus,string $evidencia, string $checks)
	{
		$query_insert = "INSERT INTO audit_plan_action (audit_opp_id, action_comment, action_date, action_status, item_action_plan, checks_action_plan) 
						VALUES (?,?,?,?,?,?) ";
		$arrData = array($intIdOpp, $strAccion, $strAccionDate, $strStatus, $evidencia, $checks);
		$request = $this->insert($query_insert,$arrData);
		return $request;
	}

	public function updatePlanAction(int $idPlanAction, string $strStatus)
	{
		$sql = "UPDATE audit_plan_action SET action_status = ? WHERE id = $idPlanAction";
		$arrData = array($strStatus);
		$request = $this->update($sql, $arrData);
		return $request;
	}

	public function getOP($prefix, $lan){
		$query = "SELECT id FROM checklist_item WHERE question_prefix = '$prefix' AND type = 'Question'";
		
		$res = new Mysql;
		$request0 = $res -> select_all($query);
		$query = "SELECT * FROM action_plan_op WHERE id_item = '".$request0[0]['id']."'";
		$request = $res -> select_all($query);
		
		return $request;
	}
}
?>