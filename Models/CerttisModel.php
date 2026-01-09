<?php
class CerttisModel extends Mysql {
	
	
	    	
    public function __construct(){
		parent::__construct();
	}
	    	
	

//CERTTIS
	public function getOppsPlanCerttis(int $idAudit){
				
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
				order by questionP";
		
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
				
		$query = "SELECT *
				FROM audit_plan_action
				where audit_opp_id = ".$idOpp;
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function insertPlanAction(int $intIdOpp, string $strAccion, string $strAccionDate, string $strStatus)
	{
		$query_insert = "INSERT INTO audit_plan_action (audit_opp_id, action_comment, action_date, action_status) 
						VALUES (?,?,?,?) ";
		$arrData = array($intIdOpp, $strAccion, $strAccionDate, $strStatus);
		$request = $this->insert($query_insert,$arrData);
		return $request;
	}
//CERTTIS
	public function insertCerttis(int $id_audit_opp, int $audit_id, string $comentarioCerttis, string $selectCerttis)
	{

		
		$query_insert = "INSERT INTO certtis (id_audit_opp, 
											  id_user, 
											  id_tipo_certtis, 
											  comentario_certtis, 
											  estatus_certtis) 
						VALUES (?,?,?,?,?) ";
		$arrData = array($id_audit_opp,$_SESSION['userData']['id'], $selectCerttis, $comentarioCerttis, 1);
		$request = $this->insert($query_insert,$arrData);
		return $request;
	}


//CERTTIS

public function selectLineaCertis(int $id_audit_opp )
{
	return $this->select_all("CALL SELECT_CERTTIS_CONCENTRADO($id_audit_opp)");
}

public function selectCertis(int $id_audit_opp){
	$sql = "SELECT t1.comentario_certtis, DATE_FORMAT(t1.fecha_hora_certtis, '%Y-%m-%d') AS fecha_certtis, DATE_FORMAT(t1.fecha_hora_certtis, '%H:%i:%s') AS hora_certtis, t2.name nombre_usuario, t3.nombre_certtis, JSON_EXTRACT(t3.datos_tipo_certtis, '$.color') AS color, JSON_EXTRACT(t3.datos_tipo_certtis, '$.icono') AS icono FROM certtis t1 LEFT JOIN user t2 ON(t2.id = t1.id_user) LEFT JOIN ct_tipo_certtis t3 ON (t3.id_tipo_certtis = t1.id_tipo_certtis) WHERE id_audit_opp = $id_audit_opp";
	$rs = $this->select_all($sql);
	return $rs;
}

public function selectLineaCertisLast(int $audit_id )
{
	return $this->select_all("SELECT 
								nombre_certtis,
								comentario_certtis,
                    			question_prefix,
                    			auditor_answer,
                    			auditor_comment,
								auditor_email
							FROM audit_opp t1
							LEFT JOIN (SELECT t3.*,
											  t4.question,
											  t4.priority as questionP,
							                  t4.priorityV as questionV
										FROM checklist_item t3
										INNER JOIN (SELECT question_prefix, IFNULL({$_SESSION['userData']['default_language']}, eng) AS 'question', priority as priorityV, IF(priority = 'Critical', 0, 1) priority
													FROM checklist_item
													WHERE type = 'Question' and checklist_id = (SELECT checklist_id FROM audit WHERE id = $audit_id  LIMIT 1) ) t4
														ON t3.question_prefix = t4.question_prefix
													WHERE t3.type = 'Picklist') t2 ON t1.checklist_item_id = t2.id
							            INNER JOIN certtis a ON t1.id = a.id_audit_opp
							            INNER JOIN ct_tipo_certtis b ON a.id_tipo_certtis = b.id_tipo_certtis
										INNER JOIN audit c ON t1.audit_id = c.id
							WHERE t1.audit_id = $audit_id 
							ORDER BY  questionP ");
}

	public function updatePlanAction(int $idPlanAction, string $strStatus)
	{
		$sql = "UPDATE audit_plan_action SET action_status = ? WHERE id = $idPlanAction";
		$arrData = array($strStatus);
		$request = $this->update($sql, $arrData);
		return $request;
	}
}

?>