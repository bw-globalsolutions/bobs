<?php

class Additional_Question extends Controllers{

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

    public function additional_Question(){

        $data['page_tag'] = "Additional questions";
		$data['page_title'] = "Additional Questions";
		$data['page_name'] = "additional questions";
        $data['page-functions_js'] = "functions_addl_questions.js";
        $data['permission'] = $this->permission;
        
		$data['question'] = $this->model->listAdditional_Question($_GET['id'], $_SESSION['userData']['default_language']);

        foreach($data['question'] as $q){
            $tmp[] = $q['type'];
        }
        $data['type'] = array_unique($tmp);
        
		$tmp = selectAudit(['status', 'visit_status'], 'id =' . $_GET['id']);
		$data['status'] = $tmp[0]['status'];
		$data['visit_status'] = $tmp[0]['visit_status'];

		$this->views->getView($this, "Additional_Question", $data);
    }
}

?>