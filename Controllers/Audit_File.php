<?php

class Audit_File extends Controllers{

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

	public function removeOppFile()
	{
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));	
		}

		$res = $this->model->deleteAudit_File("id =" . $_POST['file_id']);
		$response = ['status' => $res? 1 : 0];
		die(json_encode($response, JSON_UNESCAPED_UNICODE));
	}

	public function insertOppFiles()
	{
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));	
		}

		$res = $this->model->insertOpp_File($_POST['opp_id'], $_POST['stack_img']);
		$response = ['status' => $res? 1 : 0];
		die(json_encode($response, JSON_UNESCAPED_UNICODE));
	}
	
	public function changeResponse()
	{
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));	
		}

		$request = $this->model->deleteAudit_File("reference_id={$_POST['additional_question_item_id']} AND audit_id={$_POST['audit_id']} AND type='Additional Questions'");
        if($request and !empty($_POST['url_pic'])){
            $request = $this->model->insertAudit_File([
                'audit_id'		=> $_POST['audit_id'],
                'reference_id'	=> $_POST['additional_question_item_id'],
                'type'			=> 'Additional Questions',
                'name'			=> $_POST['pic_name'],
				'url'			=> $_POST['url_pic']
            ]);
        }
		die(json_encode(['status' => $request? 1 : 0], JSON_UNESCAPED_UNICODE));
	}
	
	public function insertPicFD()
	{
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));	
		}
		
		$this->model->deleteAudit_File("audit_id = {$_POST['audit_id']} AND name = 'Picture of the Front Door/Entrance of the Restaurant'");
		$request = $this->model->insertFrontDoorPic($_POST['audit_id'], $_POST['url']);
		die(json_encode(['status' => $request? 1 : 0], JSON_UNESCAPED_UNICODE));
	}
}
?>