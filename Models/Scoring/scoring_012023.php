<?php
$result = array();
$criticos = 0;
$autoFail = 0;
$letra = '';

$res = new Mysql;

$sqlAudit = "SELECT checklist_id, audited_areas FROM audit WHERE id = $audit_id ";
$audit = $res->select($sqlAudit);

$sqlFS = "SELECT SUM(lost_point) AS total FROM audit_point WHERE audit_id = $audit_id AND section_number<=8 ";
$food = $res->select($sqlFS);
//$food = $this->select($sqlFS);

$sqlBS = "SELECT SUM(lost_point) AS total FROM audit_point WHERE audit_id = $audit_id AND section_number>8 ";
$brand = $res->select($sqlBS);
//$brand = $this->select($sqlBS);

#### Obtener los puntos NA para ajustar los targets--------------------------------------------
$sqlPtsNA = "SELECT (SELECT SUM(target_point) FROM audit_na_question WHERE audit_id=$audit_id AND section_number<=8) as totalFS, (SELECT SUM(target_point) FROM audit_na_question WHERE audit_id=$audit_id AND section_number>8) as totalBS, (SELECT SUM(target_point) FROM audit_na_question WHERE audit_id=$audit_id) as total FROM audit_na_question LIMIT 1 ";
$ptsNA = $res->select($sqlPtsNA);

#### Obtener los targets de puntos del checklist considerando las areas auditadas--------------------------------------------
$areas = '';
if($audit['audited_areas']!='' && $audit['audited_areas']!='["-Sin-Areas-"]'){
	$audited_areas = "'".str_replace("|","','",$audit['audited_areas'])."'";

	//$sqlAreas = "SELECT CONCAT('\'', GROUP_CONCAT(question_prefix SEPARATOR '\',\''), '\'') AS prefixA FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND area IN ($audited_areas) ";
	//$pAreas = $res->select($sqlAreas);

	$areas = " AND area IN ($audited_areas) ";
} 

$sqlTarget = "SELECT (SELECT SUM(points) FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND section_number<=8 $areas) as totalFS, (SELECT SUM(points) FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND section_number>8 $areas) as totalBS, (SELECT SUM(points) FROM checklist_item WHERE checklist_id=$audit[checklist_id] $areas) as total FROM checklist_item LIMIT 1 ";
$target = $res->select($sqlTarget);

#### Identificar puntos criticos en el checklist--------------------------------------------
$sqlPCrit = "SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND priority='Critical'  ";
$pCrit = $res->select($sqlPCrit);

#### Identificar si hay puntos penalizados que sean criticos--------------------------------
if($pCrit['ids']){
	$sqlCriticos = "SELECT COUNT(id) AS total FROM audit_opp WHERE audit_id = $audit_id AND checklist_item_id IN ($pCrit[ids]) ";
	$criticos = $res->select($sqlCriticos)['total'];
}

#### Identificar autofail en el checklist--------------------------------------------
$sqlPAF = "SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids2 FROM checklist_item WHERE checklist_id=$audit[checklist_id] AND auto_fail=1 ";
$pAF = $res->select($sqlPAF);

#### Identificar si hay puntos penalizados que sean autofail--------------------------------
if($pAF['ids2']){
	$sqlAutoFail = "SELECT COUNT(id) AS totalAF FROM audit_opp WHERE audit_id = $audit_id AND checklist_item_id IN ($pAF[ids2]) ";
	$autoFail = $res->select($sqlAutoFail)['totalAF'];
}

if(empty($food['total'])) $food['total']=0;
if(empty($brand['total'])) $brand['total']=0;
$totalPerdidos = $food['total'] + $brand['total'];

$overallFS = ((($target['totalFS']-intval($ptsNA['totalFS']))-$food['total'])/($target['totalFS']-intval($ptsNA['totalFS']))) * 100;
$overallFS = number_format($overallFS, 2);
$overallBS = ((($target['totalBS']-intval($ptsNA['totalBS']))-$brand['total'])/($target['totalBS']-intval($ptsNA['totalBS']))) * 100;
$overallBS = number_format($overallBS, 2);
$overallScore = ((($target['total']-intval($ptsNA['total']))-$totalPerdidos)/($target['total']-intval($ptsNA['total']))) * 100;
$overallScore = number_format($overallScore, 2);

if($autoFail>0) $overallScore = 0;

if($overallScore >= 90 && $criticos<=4) $letra = 'Passing';
if($overallScore < 90 || $criticos==5) $letra = 'Marginal';
if($overallScore < 80 || $criticos>5) $letra = 'Failing';
//if($criticos > 0) $letra = 'F';

$sqlScore = "SELECT id FROM audit_score WHERE audit_id = $audit_id AND type='General' AND name='OVERALL SCORE' LIMIT 1";
$score = $res->select($sqlScore);
//$score = $this->select($sqlScore);

if($score['id']>0){
	$sql_update = "UPDATE audit_score SET value_1=?, value_2=?, value_3=?, value_4=? WHERE audit_id=? AND type='General' AND name='OVERALL SCORE' ";
	$request = $res->update($sql_update, [$overallFS, $overallBS, $letra, $overallScore, $audit_id]);
	//$request = $this->update($sql_update, [$food['total'], $brand['total'], $letra, $audit_id]);
}else{
	$sql_insert = "INSERT INTO audit_score SET audit_id=?, type=?, name=?, value_1=?, value_2=?, value_3=?, value_4=? ";
	$request = $res->insert($sql_insert, [$audit_id, 'General', 'OVERALL SCORE', $overallFS, $overallBS, $letra, $overallScore]);
	//$request = $this->insert($sql_insert, [$audit_id, 'General', 'OVERALL SCORE', $food['total'], $brand['total'], $letra]);
}

if($request){
	$result['FootSafety'] = $overallFS;
	$result['BrandStandars'] = $overallBS;
	$result['Calificacion'] = $letra;
	$result['OverallScore'] = $overallScore;
	//$result['otro'] = intval($ptsNA['totalFS']);
	//$result['otro'] = $sqlTarget;
}else{
	$result['FootSafety'] = 'NA';
	$result['BrandStandars'] = 'NA';
	$result['Calificacion'] = 'NA';
	$result['OverallScore'] = 'NA';
	$result['otro'] = 'NA';
}
?>