<?php

class Audit_Addi_Question extends Controllers{

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

	public function changeResponse()
	{
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));	
		}
		
        $request = $this->model->deleteAudit_Addi_Question("additional_question_item_id={$_POST['additional_question_item_id']} AND audit_id={$_POST['audit_id']}");
        if($request and !empty($_POST['answer'])){
            $request = $this->model->insertAudit_Addi_Question([
                'audit_id'                      => $_POST['audit_id'],
                'additional_question_item_id'   => $_POST['additional_question_item_id'],
                'answer'                        => $_POST['answer']
            ]);
        }
		die(json_encode(['status' => $request? 1 : 0], JSON_UNESCAPED_UNICODE));
	}
}
?>