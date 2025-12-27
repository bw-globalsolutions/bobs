<?php

class Home extends Controllers{

	public function __construct()
	{
		parent::__construct();
		session_start();
		//session_regenerate_id(true);
		if(empty($_SESSION['login']))
		{
			header('location: '.base_url().'/login');
		}
		//getPermisos(0);
	}

	public function home()
	{
		$data['page_id'] = 1;
		$data['page_tag'] = "Home";
		$data['page_title'] = "Home";
		$data['page_name'] = "home";
		$data['page-functions_js'] = "functions_home.js";
		$data['auditTypes'] = listAuditTypes();
		$data['alert_se'] = false;
		$data['audit_list'] = $this->model->getAuditList(['id', 'checklist_id', 'location_id', 'round_name', 'period', 'auditor_name', 'auditor_email', 'status', 'date_visit', 'local_foranea', 'location_number', 'location_name', 'location_address','country_id', 'country_name', 'region', 'brand_id', 'brand_name', 'brand_prefix' ,'email_ops_director','email_ops_leader','email_area_manager','email_franchisee','concept','shop_type','area','franchissees_name'], "", true);
	
		
		$data['country'] = [];
		

 


		
	
		foreach($data['audit_list'] as $item){
			
			if (!in_array($item['country_name'], $data['country'])) {
				array_push($data['country'], $item['country_name']);
			}
		}

		if($_SESSION['userData']['role']['id']==10){
			$data['alert_se'] = $this->model->getLastSelfEvaluation($_SESSION['userData']['location_id']);
		}
		
		if(!empty($_SESSION['userData']['permission']['Auditorias']['r'])){
			$data['audit_statistics'] = $this->model->getAuditStatistics();
		} else {
			$data['audit_statistics'] = [];
		}

		$data['permissionDoc'] = $_SESSION['userData']['permission']['Documentos'];
		$data['permissionAudit'] = $_SESSION['userData']['permission']['Auditorias'];
		// dep($data);
		// die();
		$this->views->getView($this, "home", $data);
	}

	public function getProgressActionPlan(){
		die(json_encode($this->model->progressActionPlan(), JSON_UNESCAPED_UNICODE));
	}
	
	public function getTopOpp(){
		die(json_encode($this->model->getTopOpp(), JSON_UNESCAPED_UNICODE));
	}

	public function getAVGScore(){
		die(json_encode($this->model->getAVGScore(), JSON_UNESCAPED_UNICODE));
	}
}
?>