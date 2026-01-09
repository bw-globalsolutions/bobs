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
        $this->permission = $_SESSION['userData']['permission']['Archivos'];
	}

    public function files()
	{
        require_once("Models/CountryModel.php");
        require_once("Models/RolesModel.php");
		$objData = new CountryModel();
        $obj2 = new RolesModel();
        $this->model->inactivarFechasCad();
		$data['page_tag'] = 'Files';
		$data['page_title'] = "Files";
		$data['page_name'] = "Files";
        $data['page-functions_js'] = "functions_files.js";
        $data['permissionDoc'] = $_SESSION['userData']['permission']['Archivos'];
        $data['paises'] = $objData->getCountry(['id','name'], "id IN (".$_SESSION['userData']['country_id'].")");
        $data['roles'] = $obj2->getRole([]);
        $data['rol'] = $_SESSION['userData']['role']['id'];
		
		$this->views->getView($this, "files", $data);
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
                $request = $this->model->updFile($_POST['title'], $_POST['description'], $_POST['jfiles'], $_POST['id'], $_POST['countrys'], $_POST['roles'], $_POST['statusF'], $_POST['expirationDate']);
                die(json_encode(['status' => $request? 1 : 0])); 
            } else{
                $request = $this->model->addFile($_POST['title'], $_POST['description'], $_POST['jfiles'], $_SESSION['userData']['id'], $_POST['countrys'], $_POST['roles'], $_POST['statusF'], $_POST['expirationDate']);
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