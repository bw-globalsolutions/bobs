<?php

class Roles extends Controllers{

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

	public function roles()
	{
		if(!$this->permission['r']){
			header('Location: '.base_url());
		}
		$data['page_id'] = 2;
		$data['page_tag'] = "Roles";
		$data['page_title'] = "User Roles";
		$data['page_name'] = "roles";
		$data['page-functions_js'] = "functions_roles.js";
		$data['modules'] = selectModules(['id', 'name']);
		$data['notifications'] = selectNotifications(['id', 'name']);
		$data['permission'] = $this->permission;

		$this->views->getView($this, "roles", $data);
	}

	public function getRoles()
	{
		if(!$this->permission['r']){
			echo "Restricted access";
			exit;
		}
		$arrData = $this->model->getRole([], null);

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

			if($this->permission['u']){
				$btnView = '<button class="btn btn-secondary btn-sm btnPermisosRol" onClick="fntPermisos('.$arrData[$i]['id'].')" title="Permissions"> <i class="fa fa-key"></i></button>';
				$btnEdit = '<button class="btn btn-primary btn-sm btnEditRol" onClick="fntEditRol('.$arrData[$i]['id'].')" title="Edit"> <i class="fa fa-pencil"></i></button>';
				$btnMail = '<button class="btn btn-primary btn-sm" style="background-color:#007cff;" onClick="fntEditNotifications('.$arrData[$i]['id'].')" title="Notification"> <i class="fa fa-envelope"></i>';
			}

			if($this->permission['d']){
				$btnDelete = '<button class="btn btn-danger btn-sm btnDelRol" onClick="fntDelRol('.$arrData[$i]['id'].')" title="Delete"> <i class="fa fa-trash"></i></button>';
			}

			$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnMail.' '.$btnDelete.'</div>';
		}

		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die(); //finaliza el proceso
	}

	public function	getRol(int $idRol)
	{
		if(!$this->permission['r']){
			echo "Restricted access";
			exit;
		}
		$intIdrol = intVal(strClear($idRol));
		if($intIdrol > 0)
		{
			$tmp = $this->model->getRole([], 'id='.$intIdrol);
			$arrData = $tmp[0];
			if(empty($arrData))
			{
				$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
			}else{
				$arrResponse = array('status' => true, 'data' => $arrData);
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function setRol()
	{
		$id = intVal($_POST['id']);
		$name = strClear($_POST['name']);
		$description = strClear($_POST['description']);
		$level = strClear($_POST['level']);
		$status = intVal($_POST['status']);
		
		if($id == 0)
		{
			//Crear
			$request_rol = $this->model->insertRol($name, $description, $level, $status);
			$option = 1;
			$request_send = $this->model->crearPermisosEmail();
		}else{
			//Actualizar
			$request_rol = $this->model->updateRol($id, $name, $description, $level, $status);
			$option = 2;
		}

		if($request_rol > 0)
		{
			if($option == 1)
			{
				$arrResponse = array('status' => true, 'msg' => 'Data saved successfully');	
				$this->model->setLog($_SESSION['userData']['id'], 'insert role');
			}else{
				$arrResponse = array('status' => true, 'msg' => 'Data updated successfully');
				$this->model->setLog($_SESSION['userData']['id'], "update role id:$id");
			}
			
		}else if($request_rol == 'exist'){
			$arrResponse = array('status' => false, 'msg' => 'Attention! The role already exists');
		}else{
			$arrResponse = array('status' => false, 'msg' => 'It is not possible to store the data');
		}

		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function delRol()
	{
		if($_POST){
			if($this->permission['d']){
				$intIdrol = intVal($_POST['idrol']);
				$requestDelete = $this->model->deleteRol($intIdrol);
				if($requestDelete == 'ok')
				{
					$arrResponse = array('status' => true, 'msg' => 'The role has been removed');
					$this->model->setLog($_SESSION['userData']['id'], "delete role id:$intIdrol");
				}else if($requestDelete	== 'exist'){
					$arrResponse = array('status' => true, 'msg' => 'It is not possible to delete a role associated with users');
				}else{
					$arrResponse = array('status' => true, 'msg' => 'Error removing role');
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}
}
?>