<?php

class SystemLanguages extends Controllers{

	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login']))
		{
			header('location: '.base_url().'/login');
		}

		$this->permission = $_SESSION['userData']['permission']['Traducciones'];

		if(!$this->permission['r']){
			header('Location: '.base_url());
		}
	}

	public function systemLanguages()
	{
		$data['page_tag'] = "Languages";
		$data['page_title'] = "System Languages";
		$data['page_name'] = "languages";
		$data['page-functions_js'] = "system_languages.js";
		$data['permission'] = $this->permission;

		$data['dictionary'] = $this->model->getDictionary();
		$data['languagues'] = $this->model->getLanguages();

		$this->views->getView($this, "system_languages", $data);
	}
	
	public function setTranslate()
	{
		if(!$this->permission['u']){
			die(http_response_code(401));	
		}

		$this->model->removeTranslate($_POST['dictionary_id'], $_POST['language_id']);
		$request = $this->model->insTranslate($_POST['dictionary_id'], $_POST['language_id'], $_POST['word-translate']);
		die(json_encode(['status' => $request]));
	}
	
	public function delTranslate()
	{
		if(!$this->permission['d']){
			die(http_response_code(401));	
		}

		$request = $this->model->removeTranslate($_POST['dictionary_id'], $_POST['language_id']);
		die(json_encode(['status' => $request]));
	}

	public function genJson(){
		if(!$this->permission['u']){
			die(http_response_code(401));	
		}
		
		$file_name = $this->model->getSystemLanguages(['file_name'], "id =" . $_POST['language_id'])[0]['file_name'];
		$jsonLan = json_encode($this->model->genJsonLanguages($_POST['language_id']), JSON_UNESCAPED_UNICODE );

		$request = file_put_contents("Config/Languages/$file_name.json", $jsonLan);

		die(json_encode(['status' => $request===FALSE? 0 : 1]));
	}

}
?>