<?php

class Files extends Controllers{

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
        $this->permission = $_SESSION['userData']['permission']['Documentos'];
	}

    public function addFile()
	{

        if((!$this->permission['u'] && !empty($_POST['id'])) || (!$this->permission['w'] && empty($_POST['id']))){
			die(http_response_code(401));
		}

        if(
            !empty($_POST['jfiles']) &&
            !empty($_POST['description']) &&
            !empty($_POST['title'])
        ){
            if(!empty($_POST['id'])){
                $request = $this->model->updFile($_POST['title'], $_POST['description'], $_POST['jfiles'], $_POST['id']);
                die(json_encode(['status' => $request? 1 : 0])); 
            } else{
                $request = $this->model->addFile($_POST['title'], $_POST['description'], $_POST['jfiles'], $_SESSION['userData']['id']);
                die(json_encode(['status' => $request]));  
            }

            die(http_response_code(401));
        } else{
            die(json_encode(['status' => 0]));
        }
    }

    public function getFiles(){
        if(!$this->permission['r']){
			die(http_response_code(401));
		}
        
        $request = $this->model->getFiles();
        die(json_encode($request));
    }
    
    public function removeFile($id){
        if(!$this->permission['d']){
			die(http_response_code(401));
		}

        $request = $this->model->removeFile($id);
        die(json_encode(['status' => $request? 1 : 0]));
    }	
}
?>