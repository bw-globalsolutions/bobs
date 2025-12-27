<?php

class AuditCertificate extends Controllers{
	
	private $audit_id;

	public function __construct()
	{
		parent::__construct();
		$this->audit_id = decryptId($_GET['tk']??-1); 
		if(!is_numeric($this->audit_id)){
			die(http_response_code(401));
		}
	}

	public function auditCertificate()
	{
		$data['audit'] = $this->model->getAuditListById($this->audit_id);

		$data['scoring'] = getScore($this->audit_id, $data['audit']['scoring_id']);
		$tmp = $this->model->getPreviousAudit($data['audit']['location_number'], $data['audit']['type'], $data['audit']['date_visit']);
		//$data['prev_scoring'] = empty($tmp)? false : getScore($tmp['id'], $tmp['scoring_id']);

		$this->views->getView($this, "audit_certificate", $data);
	}
}
?>