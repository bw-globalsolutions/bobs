<?php

class ModuloComunicacion extends Controllers{

	private $permission;

	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login']))
		{
			header('location: '.base_url().'/login');
		}
		$this->permission = $_SESSION['userData']['permission']['Usuarios'];
	}

	public function moduloComunicacion()
	{
		if(!$this->permission['r']){
			header('Location: '.base_url());
		}

		$data['page_tag'] = "Manual";
		$data['page_title'] = "Modulo de comunicacion";
		$data['page_name'] = "Modulo de comunicacion";
		$data['page-functions_js'] = "function_modulo_comunicacion.js";
		
		$data['permission'] = $this->permission;
		$data['locations'] = selectLocation(['id', 'name', 'number', 'country_id']);
		$data['periods'] = $this->model->getPeriods();
		
		$where = '';
		switch ($_SESSION['userData']['role']['id']) {
			case 1:  
				break;
			case 2:
				$where = "id NOT IN(1) AND status = 1";
				break;
			case 17:
				$where = "id NOT IN(1,2,17) AND status = 1";
				break;
			default:
				$where = "id NOT IN(1,2) AND status = 1";
		}
		$data['role'] = selectRole(['id', 'name', 'level'], $where);
		//$data['role'] = selectRole(['id', 'name', 'level'], (in_array($_SESSION['userData']['role']['id'], [1,2,17])? '1' : "id IN(10,11,12)") . ' AND status = 1');
		$data['brands'] = selectBrands(['id', 'name']);
		$data['paises'] = [];
		
		$tmp = selectCountries(['id', 'name', 'region'], "id IN({$_SESSION['userData']['country_id']})");
		foreach($tmp as $i){
			if (!array_key_exists($i['region'], $data['paises'])) {
				$data['paises'][$i['region']] = [];
			}
			array_push($data['paises'][$i['region']], ['id' => $i['id'], 'name' => $i['name']]);
		}

		$this->views->getView($this, "moduloComunicacion", $data);
	}



	public function setManual(){
		
		if(empty($_POST['txtCategoria']) || empty($_POST['txtDescripcion']) || empty($_POST['txtNombre'])){

			$arrResponse = array("status" => false, "msg" => "Datos incorrecto.");
		
		}else{
if( $_POST['txtCategoria'] == 1){
	$txtCategoria 	= $_POST['nuevaCategoria'];
}else{
	$txtCategoria 	= $_POST['txtCategoria'];
}
				



				
				$txtDescripcion = $_POST['txtDescripcion'];
				$txtNombre 		= $_POST['txtNombre'];
				$txtArchivo 	= $_POST['evidencias_1'];

				$request_user = $this->model->insertManual($txtCategoria,
													   	   $txtDescripcion,
													   	   $txtNombre,
													   	   $txtArchivo);
			}
			
				$arrResponse = array("status" => true, "msg" => "Datos guardados correctamente.");	
			

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

		die();
	}

	public function getManuales(){
													
		$arrData = $this->model->selectManuales();																						
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);												
		die();												
		
	}


	






	
	
	
	
	
	}







?>