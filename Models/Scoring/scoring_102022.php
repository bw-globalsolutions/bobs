<?php
	$result = array();
	$criticos = 0;
	$nocriticos = 0;
	$verdes = 0;
	$amarillos = 0;
	$rojos = 0;
	$mayores = 0;
	$menores = 0;
	// $nas = 0;
	
	$res = new Mysql;
	
	$sqlScore1 = "SELECT 
			IFNULL(SUM(IF(ao.auditor_answer LIKE '%Verde%', 1, 0)), 0) verdes, 
			IFNULL(SUM(IF(ao.auditor_answer LIKE '%Amarillo%', 1, 0)), 0) amarillos,
			IFNULL(SUM(IF(ao.auditor_answer LIKE '%Rojo%', 1, 0)), 0) rojos
			FROM audit_opp ao 
			WHERE ao.audit_id = $audit_id";
	$score1 = $res->select($sqlScore1);
	// dep ($score1);

	$sqlScore2 = "SELECT 
			IFNULL(SUM(IF(ci.esp = 'Crítico', 1, 0)), 0) criticos, 
			IFNULL(SUM(IF(ci.esp = 'No Crítico', 1, 0)), 0) nocriticos, 
			IFNULL(SUM(IF(ci.esp = 'Mayor', 1, 0)), 0) mayores, 
			IFNULL(SUM(IF(ci.esp = 'Menor', 1, 0)), 0) menores 
			FROM audit_opp ao 
			INNER JOIN checklist_item ci 
			ON ao.checklist_item_id = ci.id 
			AND ao.audit_id = $audit_id";
	$score2 = $res->select($sqlScore2);
	// dep ($score2);

	// die();

	$criticos = $score2['criticos'];
	$nocriticos = $score2['nocriticos'];
	$verdes = $score1['verdes'];
	$amarillos = $score1['amarillos'];
	$rojos = $score1['rojos'];
	$mayores = $score2['mayores'];
	$menores = $score2['menores'];
	
	$sqlScoreAll = "SELECT id FROM audit_score WHERE audit_id = $audit_id AND type='General' AND name='OVERALL SCORE' LIMIT 1";
	$scoreAll = $res->select($sqlScoreAll);
	
	if($scoreAll['id'] > 0){
		$sql_update = "UPDATE audit_score SET value_1=?, value_2=?, value_3=?, value_4=?, value_5=?, value_6=?, value_7=? WHERE audit_id=? AND type='General' AND name='OVERALL SCORE' ";
		$request = $res->update($sql_update, [$criticos, $nocriticos, $verdes, $amarillos, $rojos, $mayores, $menores, $audit_id]);
	} else { 
		$sql_insert = "INSERT INTO audit_score SET audit_id=?, type=?, name=?, value_1=?, value_2=?, value_3=?, value_4=?, value_5=?, value_6=?, value_7=?";
		$request = $res->insert($sql_insert, [$audit_id, 'General', 'OVERALL SCORE', $criticos, $nocriticos, $verdes, $amarillos, $rojos, $mayores, $menores]);
	}

	$result = [];
	if($request){
		$result['Criticos'] = $criticos;
		$result['NoCriticos'] = $nocriticos;
		$result['Verdes'] = $verdes;
		$result['Amarillos'] = $amarillos;
		$result['Rojos'] = $rojos;
		$result['Mayores'] = $mayores;
		$result['Menores'] = $menores;
	}else{
		$result['Criticos'] = 'NA';
		$result['NoCriticos'] = 'NA';
		$result['Verdes'] = 'NA';
		$result['Amarillos'] = 'NA';
		$result['Rojos'] = 'NA';
		$result['Mayores'] = 'NA';
		$result['Menores'] = 'NA';
	}
?>