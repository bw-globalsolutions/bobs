<?php

class Home extends Controllers{

	public function __construct()
	{
		parent::__construct();
		session_start();
		//session_regenerate_id(true);
		if(empty($_SESSION['login']))
		{
			header('location: '.base_url().'/login');
		}
		//getPermisos(0);
	}

	public function actualizarDatos(){
		$strPeriodos = "";
		if($_POST['periodos']!=[]){
			foreach($_POST['periodos'] as $p){
				$strPeriodos.="'".$p."',";
			}
			$strPeriodos = substr($strPeriodos, 0, -1);
		}
		/*$strTipos = "";
		if($_POST['tipos']!=[]){
			foreach($_POST['tipos'] as $p){
				$strTipos.="'".$p."',";
			}
			$strTipos = substr($strTipos, 0, -1);
		}*/
		$strTipos = "'".$_POST['tipos']."'";
		$strPaises = $_POST['paises'];
		$response = $this->model->getAuditStatistics(true, $strPeriodos, $strTipos, $strPaises);
		$res=array(
			"completadas"=>$response['Completed']['count']?? 0,
			"inProcess"=>$response['In Process']['count']?? 0,
			"pendientes"=>$response['Pending']['count']??0,
			"zero"=>$response['Completed']['zero']?? 0
		);
		echo json_encode($res);
	}

	public function home()
	{
		require_once("Models/CountryModel.php");
		$objData = new CountryModel();
		$data['page_id'] = 1;
		$data['page_tag'] = "Home";
		$data['page_title'] = "Home";
		$data['page_name'] = "home";
		$data['page-functions_js'] = "functions_home.js";
		$data['auditTypes'] = listAuditTypes();
		
		$data['alert_se'] = false;
		if($_SESSION['userData']['role']['id']==10){
			$data['alert_se'] = $this->model->getLastSelfEvaluation($_SESSION['userData']['location_id']);
		}
		
		if(!empty($_SESSION['userData']['permission']['Auditorias']['r'])){
			$data['audit_statistics'] = $this->model->getAuditStatistics();
		} else {
			$data['audit_statistics'] = [];
		}

		$data['permissionDoc'] = $_SESSION['userData']['permission']['Documentos'];
		$data['permissionAudit'] = $_SESSION['userData']['permission']['Auditorias'];
		$data['periods'] = array_unique(array_column(selectRound(['name']), 'name'));
		$data['paises'] = $objData->getCountry(['id','name'], "id IN (".$_SESSION['userData']['country_id'].")");
		// dep($data);
		// die(var_dump($data['paises']));
		$this->views->getView($this, "home", $data);
	}

	public function getProgressActionPlan(){
		die(json_encode($this->model->progressActionPlan(), JSON_UNESCAPED_UNICODE));
	}
	
	public function getTopOpp(){
		die(json_encode($this->model->getTopOpp(), JSON_UNESCAPED_UNICODE));
	}

	public function getAVGScore(){
		die(json_encode($this->model->getAVGScore(), JSON_UNESCAPED_UNICODE));
	}
}
?>