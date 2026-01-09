<?php
$result = array();
$criticos = 0;
$autoFail = 0;
$letra = '';

$res = new Mysql;

$sqlAudit = "SELECT checklist_id, audited_areas FROM audit WHERE id = $audit_id ";
$audit = $res->select($sqlAudit);

$sqlFS = "SELECT SUM(lost_point) AS total FROM audit_point WHERE audit_id = $audit_id AND section_number>1 AND section_number<11";
$foodSafety = $res->select($sqlFS);
$sqlFS = "SELECT SUM(points) AS total FROM checklist_item WHERE type='Question' AND section_number>1 AND section_number<11 AND checklist_id = ".$audit['checklist_id'];
$foodSafetyTotal = $res->select($sqlFS);
//$foodSafety = $this->select($sqlFS);

$sqlBS = "SELECT SUM(lost_point) AS total FROM audit_point WHERE audit_id = $audit_id AND section_number>10 AND section_number<20 ";
$brandStandards = $res->select($sqlBS);
$sqlFS = "SELECT SUM(points) AS total FROM checklist_item WHERE type='Question' AND section_number>10 AND section_number<21 AND checklist_id = ".$audit['checklist_id'];
$brandStandardsTotal = $res->select($sqlFS);
//$operations = $this->select($sqlBS);

$sqlFS = "SELECT SUM(points) AS total FROM checklist_item WHERE type='Question' AND checklist_id = ".$audit['checklist_id'];
$totalTotal = $res->select($sqlFS);

$sqlBS = "SELECT SUM(lost_point) AS total FROM audit_point WHERE audit_id = $audit_id AND section_number>20 ";
$diamondRule = $res->select($sqlBS);
//$operations = $this->select($sqlBS);

#### Obtener los puntos NA para ajustar los targets--------------------------------------------
$sqlPtsNA = "SELECT (SELECT SUM(target_point) FROM audit_na_question WHERE audit_id=$audit_id AND section_number>1 AND section_number<11) as totalFS, (SELECT SUM(target_point) FROM audit_na_question WHERE audit_id=$audit_id AND section_number>10 AND section_number<21) as totalBS, (SELECT SUM(target_point) FROM audit_na_question WHERE audit_id=$audit_id AND section_number>20) as totalDR, (SELECT SUM(target_point) FROM audit_na_question WHERE audit_id=$audit_id) as total FROM audit_na_question LIMIT 1 ";
$ptsNA = $res->select($sqlPtsNA);

#### Obtener los targets de puntos del checklist considerando las areas auditadas--------------------------------------------
$areas = '';
if($audit['audited_areas']!='' && $audit['audited_areas']!='["-Sin-Areas-"]'){
	$audited_areas = "'".str_replace("|","','",$audit['audited_areas'])."'";

	//$sqlAreas = "SELECT CONCAT('\'', GROUP_CONCAT(question_prefix SEPARATOR '\',\''), '\'') AS prefixA FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND area IN ($audited_areas) ";
	//$pAreas = $res->select($sqlAreas);

	$areas = " AND area IN ($audited_areas) ";
} 

$sqlTarget = "SELECT (SELECT SUM(points) FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND section_number>1 AND section_number<11 $areas) as totalFS, (SELECT SUM(points) FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND section_number>10 AND section_number<21 $areas) as totalBS, (SELECT SUM(points) FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND section_number>20) as totalDR, (SELECT SUM(points) FROM checklist_item WHERE checklist_id=$audit[checklist_id] $areas) as total FROM checklist_item LIMIT 1 ";
$target = $res->select($sqlTarget);

#### Identificar puntos criticos en el checklist--------------------------------------------
$sqlPCrit = "SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND main_section='Regra Diamante' ";
$pCrit = $res->select($sqlPCrit);

#### Identificar si hay puntos penalizados que sean criticos--------------------------------
if($pCrit['ids']){
	$sqlCriticos = "SELECT COUNT(id) AS total FROM audit_opp WHERE audit_id = $audit_id AND checklist_item_id IN ($pCrit[ids]) ";
	$criticos = $res->select($sqlCriticos)['total'];
}

#### Identificar autofail en el checklist--------------------------------------------
$sqlPAF = "SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids2 FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND main_section='Regra Diamante' ";
$pAF = $res->select($sqlPAF);

#### Identificar si hay puntos penalizados que sean autofail--------------------------------
if($pAF['ids2']){
	$sqlAutoFail = "SELECT COUNT(id) AS totalAF FROM audit_opp WHERE audit_id = $audit_id AND checklist_item_id IN ($pAF[ids2]) ";
	$autoFail = $res->select($sqlAutoFail)['totalAF'];
}

if(empty($foodSafety['total'])) $foodSafety['total']=0;
if(empty($brandStandards['total'])) $brandStandards['total']=0;
$totalPerdidos = $foodSafety['total'] + $brandStandards['total'];
$totalNA = $ptsNA['totalFS'] + $ptsNA['totalBS'];

$overallFS = ((($foodSafetyTotal['total']-$ptsNA['totalFS'])-$foodSafety['total'])/($foodSafetyTotal['total']-$ptsNA['totalFS']))*100;
$overallFS = number_format($overallFS, 2);
$brandStandardsE = ((($brandStandardsTotal['total']-$ptsNA['totalBS'])-$brandStandards['total'])/($brandStandardsTotal['total']-$ptsNA['totalBS']))*100;
$brandStandardsE = number_format($brandStandardsE, 2);
/*$overallScore = ((($totalTotal['total']-$totalNA)-$totalPerdidos)/($totalTotal['total']-$totalNA))*100;
$overallScore = number_format($overallScore, 2);*/
$overallScore = ($overallFS+$brandStandardsE)/2;
$overallScore = number_format($overallScore, 2);

if($autoFail>0) $overallScore = 0;

if($overallScore >= 90){ $letra = 'ZONA DE EXCELÊNCIA'; $color='#3f6320'; }
if($overallScore >= 80 && $overallScore <90){ $letra = 'ZONA DE CUALIDADE'; $color='#5e8d35'; }
if($overallScore >= 70 && $overallScore <80){ $letra = 'ZONA DE ATENÇÃO'; $color='#d0a113'; }
if($overallScore <70){ $letra = 'ZONA CRÍTICA'; $color='#a51111'; }
//if($criticos > 0) $letra = 'F';

$sqlScore = "SELECT id FROM audit_score WHERE audit_id = $audit_id AND type='General' AND name='OVERALL SCORE' LIMIT 1";
$score = $res->select($sqlScore);
//$score = $this->select($sqlScore);

if($score['id']>0){
	$sql_update = "UPDATE audit_score SET value_1=?, value_2=?, value_3=?, value_4=? WHERE audit_id=? AND type='General' AND name='OVERALL SCORE' ";
	$request = $res->update($sql_update, [$overallFS, $brandStandardsE, $letra, $overallScore, $audit_id]);
	//$request = $this->update($sql_update, [$foodSafety['total'], $operations['total'], $letra, $audit_id]);
}else{
	$sql_insert = "INSERT INTO audit_score SET audit_id=?, type=?, name=?, value_1=?, value_2=?, value_3=?, value_4=? ";
	$request = $res->insert($sql_insert, [$audit_id, 'General', 'OVERALL SCORE', $overallFS, $brandStandardsE, $letra, $overallScore]);
	//$request = $this->insert($sql_insert, [$audit_id, 'General', 'OVERALL SCORE', $foodSafety['total'], $operations['total'], $letra]);
}

if($request){
	$result['FootSafety'] = $overallFS;
	$result['OperationsE'] = $brandStandardsE;
	$result['Letra'] = $letra;
	$result['color'] = $color;
	$result['OverallScore'] = $overallScore;
	$result['AutoFail'] = $autoFail;
	//$result['otro'] = intval($ptsNA['totalFS']);
	//$result['otro'] = $sqlTarget;
}else{
	$result['FootSafety'] = 'NA';
	$result['OperationsE'] = 'NA';
	$result['Letra'] = 'NA';
	$result['color'] = '#a51111';
	$result['OverallScore'] = 'NA';
	$result['AutoFail'] = 0;
	$result['otro'] = 'NA';
}
?>