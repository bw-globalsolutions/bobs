<?php
class ScoringModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	//Scoring Versión
	public function getScoring($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(', ', $columns) : "*") ." 
				  FROM scoring  
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id DESC";
		
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	//Setear Scores de la visita
	public function setScore($audit_id, $scoring_id){

		//....aqui invocar el script que gestionara algoritmo para ese scoring_id...
		//ese script debe consultar los puntos perdidos y etc, luego hacer la incersion de
		//las califiaciones en tabla audit_score, etc...
		$result='';

		$fileScore = self::getScoring(['script_location'], "id='$scoring_id'")[0];
		require_once("Models/Scoring/".$fileScore['script_location'].".php");

		return $result;
	}

	public function getScore($audit_id){

		$sqlScore = "SELECT * FROM audit_score WHERE audit_id = $audit_id AND type='General' AND name='OVERALL SCORE' ORDER BY id DESC LIMIT 1";
		$tmp = $this->select($sqlScore);
	
		$request = [
			'value_1'			=> $tmp['value_1'],
			'value_2'			=> $tmp['value_2'],
			'value_3'			=> $tmp['value_3'],
			'value_4'			=> $tmp['value_4'],
			'value_5'			=> $tmp['value_5'],
			'value_6'			=> $tmp['value_6'],
			'value_7'			=> $tmp['value_7'],
			'value_fs'			=> $tmp['value_fs'],
			'Result'			=> $tmp['result']
			
			// 'Mayores'			=> $tmp['value_6'],
			// 'Menores'			=> $tmp['value_7']
		];
		return $request;
	}

	public function closedScore($audit_id = 0){
		$res = new Mysql;

		$sqlScore = "SELECT id FROM audit_score WHERE audit_id = $audit_id AND type='General' AND name='OVERALL SCORE' LIMIT 1";
		$score = $res->select($sqlScore);

		if($score['id']>0){
			$sql_update = "UPDATE audit_score SET value_1=?, value_2=?, value_3=? WHERE audit_id=? AND type='General' AND name='OVERALL SCORE' ";
			$request = $res->update($sql_update, [0, 0, 'F', $audit_id]);
		}else{
			$sql_insert = "INSERT INTO audit_score SET audit_id=?, type=?, name=?, value_1=?, value_2=?, value_3=? ";
			$request = $res->insert($sql_insert, [$audit_id, 'General', 'OVERALL SCORE', 0, 0, 'F']);
		}
		
		return $request;
	}
}
?>