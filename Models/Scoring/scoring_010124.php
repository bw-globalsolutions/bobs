<?php
	$result = array();
	$criticos = 0;
	$nocriticos = 0;
	$verdes = 0;
	$amarillos = 0;
	$rojos = 0;
	$mayores = 0;
	$menores = 0;
	$mantenimiento = 0;
	$af = 0;

	$visit_result = "Pass";
	
	$res = new Mysql;
	
	$sqlScore1 = "SELECT 
			IFNULL(SUM(IF(ao.auditor_answer LIKE '% Verde%', 1, 0)), 0) verdes, 
			IFNULL(SUM(IF(ao.auditor_answer LIKE '% Amarillo%', 1, 0)), 0) amarillos,
			IFNULL(SUM(IF(ao.auditor_answer LIKE '% Rojo%', 1, 0)), 0) rojos
			FROM audit_opp ao 
			WHERE ao.audit_id = $audit_id";
	$score1 = $res->select($sqlScore1);

	$sqlScore2 = "SELECT 
			IFNULL(SUM(IF(ci.esp = 'Crítico', 1, 0)), 0) criticos, 
			IFNULL(SUM(IF(ci.esp = 'No Crítico' OR ci.esp = 'Básicos', 1, 0)), 0) AS nocriticos, 
			IFNULL(SUM(IF(ci.main_section = 'MANTENIMIENTO', 1, 0)), 0) main, 
			IFNULL(SUM(IF(ci.main_section = 'ZTC', 1, 0)), 0) af  
			FROM audit_opp ao 
			INNER JOIN checklist_item ci 
			ON ao.checklist_item_id = ci.id 
			AND ao.audit_id = $audit_id";
	$score2 = $res->select($sqlScore2);

	// die();

	$criticos = $score2['criticos'];
	$nocriticos = $score2['nocriticos'];
	$verdes = $score1['verdes'];
	$amarillos = $score1['amarillos'];
	$rojos = $score1['rojos'];
	$mantenimiento = $score2['main'];
	$af = $score2['af'];
	// $mayores = $score2['mayores'];
	// $menores = $score2['menores'];

	if($criticos > 1 || $rojos > 10 || $af > 0){
		$visit_result = "Fail";
	}
	 
	$sqltype = "SELECT type FROM audit a INNER JOIN round b ON a.round_id = b.id WHERE a.id = $audit_id";
	$type = $res->select($sqltype);
	$tipo = $type['type'];

	if($tipo =='Training-visits'){
		$visit_result = "NA";
	}
	
	
	$sqlScoreAll = "SELECT id FROM audit_score WHERE audit_id = $audit_id AND type='General' AND name='OVERALL SCORE' LIMIT 1";
	$scoreAll = $res->select($sqlScoreAll);

	if($scoreAll['id'] > 0){
		$sql_update = "UPDATE audit_score SET value_1=?, value_2=?, value_3=?, value_4=?, value_5=?, value_6=?, value_fs=?, result=? WHERE audit_id=? AND type='General' AND name='OVERALL SCORE' ";
		$request = $res->update($sql_update, [$criticos, $nocriticos, $verdes, $amarillos, $rojos, $mantenimiento, $af, $visit_result, $audit_id]);
	} else { 
		$sql_insert = "INSERT INTO audit_score SET audit_id=?, type=?, name=?, value_1=?, value_2=?, value_3=?, value_4=?, value_5=?, value_6=?, value_fs=?, result=?";
		$request = $res->insert($sql_insert, [$audit_id, 'General', 'OVERALL SCORE', $criticos, $nocriticos, $verdes, $amarillos, $rojos, $mantenimiento, $af, $visit_result]);
	}

	$result = [];
	if($request){
		$result['Criticos'] = $criticos;
		$result['NoCriticos'] = $nocriticos;
		$result['Verdes'] = $verdes;
		$result['Amarillos'] = $amarillos;
		$result['Rojos'] = $rojos;
		$result['Mantenimiento'] = $mantenimiento;
		$result['AutoFail'] = $af;
		$result['Result'] = $visit_result;
	}else{
		$result['Criticos'] = 'NA';
		$result['NoCriticos'] = 'NA';
		$result['Verdes'] = 'NA';
		$result['Amarillos'] = 'NA';
		$result['Rojos'] = 'NA';
		$result['Mantenimiento'] = 'NA';
		$result['AutoFail'] = 'NA';
		$result['Result'] = 'NA';
	}
?>