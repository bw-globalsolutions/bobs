<?php

class Permisos extends Controllers{

	public function __construct()
	{
		parent::__construct();
		session_start();
		//session_regenerate_id(true);
		if(empty($_SESSION['login']))
		{
			header('location: '.base_url().'/login');
		}
	}

	public function getPermisosRol(int $id)
	{
		$arrPermisosRol = $this->model->getPermissions(['module_id', 'r', 'w', 'u', 'd'], "role_id = $id");
		die(json_encode($arrPermisosRol, JSON_UNESCAPED_UNICODE));
	}

	public function setPermisos()
	{
		if($_POST){
			$role_id = $_POST['role_id'];
			$this->model->deletePermisos($role_id);
			foreach($_POST as $module_id => $perm){
				if(is_int($module_id)){
					$r = strpos($perm, 'r') === false ? 0 : 1;
					$w = strpos($perm, 'w') === false ? 0 : 1;
					$u = strpos($perm, 'u') === false ? 0 : 1;
					$d = strpos($perm, 'd') === false ? 0 : 1;
					$requestPermiso = $this->model->insertPermisos($role_id, $module_id, $r, $w, $u, $d);
				}
			}
			if($requestPermiso > 0)
			{
				$arrResponse = array('status' => true, 'msg' => 'Permissions assigned correctly');
				$this->model->setLog($_SESSION['userData']['id'], "update permission id:$intIdrol");
			}else{
				$arrResponse = array('status' => false, 'msg' => 'Permissions cannot be assigned');
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

}
?>