<?php
class AuditorSurvey extends Controllers{

	public function __construct()
	{
		parent::__construct();
		$this->audit_id = decryptId($_GET['tk']??-1);
	}

	public function auditorSurvey()
	{
		//echo $this->audit_id;
		$data['page_tag'] = 'Auditor Survey';
		$data['page_title'] = "Auditor Survey";
		$data['page_name'] = "Auditor Survey";
        $data['page-functions_js'] = "functions_auditor_survey.js";
		$data['audit_id'] = $this->audit_id;
		// $tmp = selectAuditList(
		// 	['type', 'round_name', 'date_visit, date_visit_end', 'location_number', 'location_name', 'location_address', 'auditor_name', 'manager_name', 'manager_signature', 'scoring_id'], 
		// 	'id ='. $this->audit_id
		// );
		// $data['audit'] = $tmp[0];

		// $tmp = selectAuditFiles(
		// 	['url'],
		// 	"audit_id = $this->audit_id AND name = 'Picture of the Front Door/Entrance of the Restaurant'"
		// );
		// $data['audit']['picture_front'] = $tmp[0]['url'];
		
		// $data['scoring'] = getScore($this->audit_id, $data['audit']['scoring_id']);
		// $tmp = $this->model->getPreviousAudit($data['audit']['location_number'], $data['audit']['type'], $data['audit']['date_visit']);
		// $data['prev_scoring'] = empty($tmp)? false : getScore($tmp['id'], $tmp['scoring_id']);

		// $data['mains'] = $this->model->getSectionsOpp($this->audit_id);
		$data['ok'] = true;
		$data['questions'] = $this->model->getQuestions();
		$data['auditSurvey'] = $this->model->getAuditSurvey($this->audit_id);
		if (count($data['auditSurvey']) > 0) {
			$data['ok'] = false;
		}
			
		//dep($data['auditSurvey']);
		foreach($data['auditSurvey'] as $r){
			$aAnswers[$r['question_id']] = $r['answer'];
		}
		//$aAnswers[$data['auditSurvey']['question_id']] = $data['auditSurvey']['answer'];
		//dep($aAnswers);
		for($i=0; $i<count($data['questions']); $i++) {
			$data['questions'][$i]['answers'] = explode("|", $data['questions'][$i]['options_esp']);
			$data['questions'][$i]['answer'] = $aAnswers[$data['questions'][$i]['id']];
		}
		//dep($data);
		$this->views->getView($this, "auditorSurvey", $data);
	}

	public function setAnswers()
	{
		//dep($_POST);
		//die();
		//$OK = true;s
		

		foreach($_POST['qID'] as $i => $val){
			$id_question = key($val);
			$insertAuditorSurvey = [
				'audit_id' => $_POST['id_audit'],
				'question_id' => $id_question,
				'answer' => $val[$id_question],
				'user_id' => $_SESSION['userData']['id']
			];
			$request = $this->model->insertAnswer($insertAuditorSurvey);
			if($request > 0)
				{
					$arrResponse = array("status" => true, "msg" => "Data saved successfully");
				}else{
					$arrResponse = array("status" => false, "msg" => "It is not possible to store the data");
				}
		}



		$insertText= ['audit_id' => $_POST['id_audit'],
					  'question_id' => 9,
					  'answer' => $_POST['comentario_auditor'],
		];

		$request = $this->model->insertAnswerText($insertText);

		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
	}
}
?>