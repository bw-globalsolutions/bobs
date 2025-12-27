<?php
require_once 'Models/UsuariosModel.php';
class UsuariosTienda extends Controllers{

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

	public function usuariosTienda()
	{
		if(!$this->permission['r']){
			header('Location: '.base_url());
		}

		$data['page_tag']   = "Users";
		$data['page_title'] = "Users";
		$data['page_name']  = "users";
		$data['page-functions_js'] = "functions_usuarios.js";
		
		$data['permission'] = $this->permission;
		$data['locations'] = selectLocation(['id', 'name', 'number', 'country_id']);
		
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
		$data['brands'] = selectBrands(['id', 'name']);
		$data['paises'] = [];
		
		$tmp = selectCountries(['id', 'name', 'region'], "id IN({$_SESSION['userData']['country_id']})");
		foreach($tmp as $i){
			if (!array_key_exists($i['region'], $data['paises'])) {
				$data['paises'][$i['region']] = [];
			}
			array_push($data['paises'][$i['region']], ['id' => $i['id'], 'name' => $i['name']]);
		}

		$this->views->getView($this, "usuariosTienda", $data);
	}

	

	public function getUsuarios()
	{
		if(!$this->permission['r']){
			echo "Restricted access";
			exit;
		}
		$arrData = $this->model->selectUsuarios($_SESSION['userData']['role']['id']);

		for($i=0; $i<count($arrData); $i++){
			$btnView = '';
			$btnEdit = '';
			$btnDelete = '';

			if($arrData[$i]['status'] == 1)
			{
				$arrData[$i]['status'] = '<span class="badge badge-success">Active</span>';
			}else{
				$arrData[$i]['status'] = '<span class="badge badge-danger">Inactive</span>';
			}
			
			if($arrData[$i]['notification'] == 1)
			{
				$arrData[$i]['notification'] = '<span class="badge badge-success">Active</span>';
			}else{
				$arrData[$i]['notification'] = '<span class="badge badge-danger">Inactive</span>';
			}

			$btnView = '<button class="btn mr-1 mb-1 btn-secondary btn-sm btnViewUsuario" onClick="fntViewUsuario('.$arrData[$i]['id'].')" title="View user"> <i class="fa fa-eye"></i></button>';
			
			if($this->permission['u']){
				$btnEdit = '<button class="btn mr-1 mb-1 btn-primary btn-sm btnEditUsuario" onClick="fntEditUsuario(this,'.$arrData[$i]['id'].')" title="Edit"> <i class="fa fa-pencil"></i></button>';
			}

			if($this->permission['d']){
				$btnDelete = '<button class="btn mr-1 mb-1 btn-danger btn-sm btnDelUsuario" onClick="fntDelUsuario('.$arrData[$i]['id'].')" title="Delete"> <i class="fa fa-trash"></i></button>';
			}

			$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
		}

		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}

	


	


	
	}







?>