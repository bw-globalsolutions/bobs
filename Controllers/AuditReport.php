<?php

class AuditReport extends Controllers{
	
	private $audit_id;

	public function __construct()
	{
		parent::__construct();
		$this->audit_id = decryptId($_GET['tk']??-1); 
		if(!is_numeric($this->audit_id)){
			die(http_response_code(401));
		}
	}

	public function dq_R1_24()
	{
		$data['audit'] = $this->model->getAuditListById($this->audit_id);

		$tmp = selectAuditFiles(
			['url'],
			"audit_id = $this->audit_id AND name = 'Picture of the Front Door/Entrance of the Restaurant'"
		);
		$data['audit']['picture_front'] = $tmp[0]['url'];
		
		$data['scoring'] = getScore($this->audit_id, $data['audit']['scoring_id']);
		$data['mains'] = $this->model->getSectionsOpp($this->audit_id, $data['audit']['checklist_id']);
		$data['questions'] = $this->model->getQuestionsOpp($this->audit_id, $data['audit']['checklist_id'], $_GET['lan']?? 'esp');

		$this->views->getView($this, "dq_R1_24", $data);
	}
}
?>