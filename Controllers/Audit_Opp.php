<?php

class Audit_Opp extends Controllers{

	private $permission;

	public function __construct()
	{
		parent::__construct();
		session_start();
		//session_regenerate_id(true);
		if(empty($_SESSION['login']))
		{
			header('location: '.base_url().'/login');
		}
		$this->permission = $_SESSION['userData']['permission']['Auditorias'];

		if(!$this->permission['r']){
			header('Location: '.base_url());
		}
	}

	public function changeOpp()
	{
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));	
		}

		if(empty($_POST['opp_id'])){
			// dep($_POST);
			// die();
			$tmp = selectAudit(['scoring_id'], 'id ='. $_POST['audit_id']);
			$scoring_id = $tmp[0]['scoring_id'];
			$opp = $this->model->newOpportunity($_POST['audit_id'], $_POST['checklist_item_id'], $_POST['opp_answers'], $_POST['opp_comment']);
			if(!empty($opp)){
				$score = setScore($_POST['audit_id'], $scoring_id);
				//$score['color'] = getScoreDefinition($score['Calificacion'])[0]; 
				$response = [
					'status'	=> 1,
					'score'		=> $score,
					'opp_id'	=> $opp['@audit_opp_id']
				];
			} else $response = ['status' => 0];
		}else{
			$res = $this->model->updateOpportunity([
				"auditor_answer"	=> $_POST['opp_answers'],
				"auditor_comment"	=> $_POST['opp_comment']
			], "id = {$_POST['opp_id']}");
	
			$response = [
				'status'	=> $res? 1 : 0,
				'opp_id'	=> $_POST['opp_id']
			];			
		}

		die(json_encode($response, JSON_UNESCAPED_UNICODE));
	}
	
	public function removeOpp()
	{
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));	
		}

		$tmp = selectAudit(['scoring_id'], 'id ='. $_POST['audit_id']);
		$scoring_id = $tmp[0]['scoring_id'];

		$res = $this->model->removeOpportunity($_POST['opp_id']);
		$score = setScore($_POST['audit_id'], $scoring_id);
		$score['color'] = getScoreDefinition($score['Calificacion'])[0]; 
		
		$response = [
			'status'	=> empty($res)? 0 : 1,
			'score'		=> $score
		];

		if(!empty($_POST['section_number']) and !empty($_POST['audit_id'])){
			$response['questions_opp'] = $this->model->getQuestionOpp($_POST['section_number'], $_POST['audit_id']);
		}
		die(json_encode($response, JSON_UNESCAPED_UNICODE));
	}

	public function getOpp($opp_id){
		$oppData = $this->model->getOppInfo($opp_id, $_SESSION['userData']['default_language']);
		$audit_lan = getAuditLan($oppData['audit_id']);
		
		$answers = [];
		foreach(listAnswers($_SESSION['userData']['default_language'], $oppData['id']) as $key => $value){
			if(matchAnswer($key, $oppData['auditor_answer'])) array_push($answers, $value);
		}

		$response = [
			'text' 		=> $oppData['text'],
			'answers' 	=> $answers,
			'question_prefix'	=> $oppData['question_prefix']
		];

		die(json_encode($response, JSON_UNESCAPED_UNICODE));
	}

	public function insertNA(){
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));	
		}

		$status = $this->model->insertNA($_POST['audit_id'], $_POST['section_number'], $_POST['question_prefix'], $_POST['points']);

		$tmp = selectAudit(['scoring_id'], 'id ='. $_POST['audit_id']);
		$scoring_id = $tmp[0]['scoring_id'];
		
		$score = setScore($_POST['audit_id'], $scoring_id);
		$score['color'] = getScoreDefinition($score['Calificacion'])[0]; 
		
		$response = [
			'status'		=> $status,
			'score'			=> $score,
			'questions_opp'	=> $this->model->getQuestionOpp($_POST['section_number'], $_POST['audit_id'])
		];

		die(json_encode($response));
	}
	
	public function removeNA(){
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));	
		}

		$status = $this->model->removeNA($_POST['audit_id'], $_POST['question_prefix']);

		$tmp = selectAudit(['scoring_id'], 'id ='. $_POST['audit_id']);
		$scoring_id = $tmp[0]['scoring_id'];
		
		$score = setScore($_POST['audit_id'], $scoring_id);
		$score['color'] = getScoreDefinition($score['Calificacion'])[0]; 
		
		$response = [
			'status'		=> $status,
			'score'			=> $score
		];

		die(json_encode($response));
	}
}
?>