<?php
require_once 'Models/AuditoriaModel.php';
require_once 'Models/RoundModel.php';
require_once 'Models/BrandModel.php';
require_once 'Models/LocationModel.php';
require_once 'Models/CountryModel.php';
require_once 'Models/ScoringModel.php';
require_once 'Models/ChecklistModel.php';
require_once 'Models/Checklist_ItemModel.php';
require_once 'Models/Report_LayoutModel.php';
require_once 'Models/Additional_QuestionModel.php';
require_once 'Models/Additional_Question_ItemModel.php';
require_once 'Models/Audit_LogModel.php';
require_once 'Models/Announced_VisitsModel.php';

class ActionPlan extends Controllers{

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

	public function auditPlan()
	{
		require_once("Models/Audit_OppModel.php");
		$objData = new Audit_OppModel();
		$data['page_tag'] = "Action Plan";
		$data['page_title'] = "Action Plan";
		$data['page_name'] = "action plan";
        $data['page-functions_js'] = "functions_audit_plan_action.js";
		//echo $_GET['id'];
		$data['opps'] = $objData->getAudit_Opp([], 'id =' . $_GET['id']??-1);
		$data['section'] = listSeccions(1);
		
		$this->views->getView($this, "action_plan_t", $data);
	}

	public function getOpps()
	{
		$fnT = translate($_SESSION['userData']['default_language']);

		$idi = $_SESSION['userData']['default_language'];
		//echo $_GET['idAudit'];
		$audit = $_GET['idAudit'];
		//echo $audit;
		$data = $this->model->getOppsPlan($audit);
		//dep($data);
		for($i=0; $i<count($data); $i++){
			$btnAction = '';
			$badgeStatus = '';
			$auxStatus = '';
			$auxIdOpp = $data[$i]['id_audit_opp'];
			$dataFiles = $this->model->getFilesOpp($data[$i]['id_audit_opp']);
			$dataActions = $this->model->getActions($data[$i]['id_audit_opp']);
			//dep($dataActions);

			$files = '';
			$actions = '';
			foreach ($dataFiles as $key) {
				$files .= ' <div class="mr-3 mb-3">
								<a href="'.$key['url'].'" target="_blank">
									<img style="height:100px; width:100px" class="rounded shadow-sm of-cover cr-pointer" src="'.$key['url'].'">
								</a><br>
							</div>';
			}

			if (count($dataActions)) {
				$actions .= '<span class="badge badge-info"> '.$fnT("Previous actions").'</span><br>';
				foreach ($dataActions as $key) {
					if ( $key['action_status'] == 'In Review' ) {
						$auxStatus = $key['action_status'];
						$auxId = $key['id'];
						$badgeStatus = '<span class="badge badge-primary">'.$fnT($key['action_status']).'</span>';
						$actions .= ' <span class="text-primary"><i class="fa fa-question"></i> '.$fnT($key['action_comment']).'</span><br>';
					} else if ( $key['action_status'] == 'Approved' ) {
						$auxId = $key['id'];
						$auxStatus = $key['action_status'];
						$badgeStatus = '<span class="badge badge-success">'.$fnT($key['action_status']).'</span>';
						$actions .= ' <span class="text-success"><i class="fa fa-check-square"></i> '.$fnT($key['action_comment']).'</span> <br>';
					} else if ( $key['action_status'] == 'Rejected' ) {
						$auxStatus = $key['action_status'];
						$badgeStatus = '<span class="badge badge-danger">'.$fnT($key['action_status']).'</span>';
						$actions .= ' <span class="text-danger"><i class="fa fa-minus-square"></i> '.$fnT($key['action_comment']).'</span> <br>';
					} else if ( $key['action_status'] == 'Finished' ) {
						$auxStatus = $key['action_status'];
						$badgeStatus = '<span class="badge badge-success">'.$fnT($key['action_status']).'</span>';
						$actions .= ' <span class="text-success"><i class="fa fa-check-square"></i> '.$fnT($key['action_comment']).'</span> <br>';
					}
					
				}
			} else {
				$auxStatus = 'Pending';
				$badgeStatus = '<span class="badge badge-warning">'.$fnT("Pending").'</span>';
			}
			
			//echo $actions;

			$data[$i]['num'] = $i+1;
			$answers = '';
			$answersArr = explode("|", $data[$i]['auditor_answer']);
			foreach ($answersArr as $keyA) {
				if(!empty($keyA)){
					$answers.= '<span class="text-primary"><i class="fa fa-comments"></i> '.$keyA.'</span> <br>';
				}
			}

			$data[$i]['opportunity'] = '<span class="h6"><span class="badge badge-info"> '.$fnT($data[$i]['section_name']).'</span> <br>
										<span class="badge badge-secondary">'.$data[$i]['question_prefix'].'</span> '.$data[$i]['question'].' <span class="badge badge-danger">'.$fnT($data[$i]['questionV']) .'</span> <br>
										<span class="text-danger"><i class="fa fa-thumbs-o-down"></i> '.$data[$i][$idi].'</span> <br>'.$answers;

			if(!empty($data[$i]['auditor_comment'])){
				$data[$i]['opportunity'] .= '<span class="text-info"><i class="fa fa-comments"></i> '.$data[$i]['auditor_comment'].'</span> <br>
				<div class="d-flex flex-wrap">'.$files.'</div>';
			}
			
			$data[$i]['status'] = $actions.''.$badgeStatus;
			
			//echo $auxStatus;
			//$data[$i]['status'] = '<span class="badge badge-info">In Review</span>';
			//$data[$i]['status'] = '<span class="badge badge-success">Finished</span>';

			
			
			//$temporalidad  = 'disabled';
			$temporalidad  = '';
			/*	
				Variable temporalidad condiciones
				2160 horas(90 dias) Mantenimiento
				72   horas No Críticas y de Limpieza la temporalidad
				24   horas Penalizaciones Críticas 
				$horas.' '.$data[$i]['diferencia_en_horas'].
			*/
			//if($data[$i]['mantenimiento'] > 0){
			//	$horas = 2160;
			//}
			//if($data[$i]['no_critico'] > 0){
			//	$horas = 72;
			//}
			//if($data[$i]['critico'] > 0){
			//	$horas = 24;
			//}
//
//
			//if( $data[$i]['diferencia_en_horas'] > $horas ){
			//	$temporalidad  = 'disabled';
			//}
			
			

			if ($auxStatus == 'Pending' || $auxStatus == 'Rejected') {
				if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 10, 14,20] ) ) {
					$btnAction = '<button '.$temporalidad.' class="btn btn-success btnAddAction" onClick="fntAddAction('.$data[$i]['id_audit_opp'].','.$audit.')" title="'.$fnT("Add action").'"><i class="fa fa-plus-circle"></i> '.$fnT("Add action").'</button>';
				}
			} else if ($auxStatus == 'In Review') {
				if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 14,20] ) ) {
					$btnAction = '<button class="btn btn-info btnChangeStatus" onClick="fntChangeStatusAction('.$auxId.','.$auxIdOpp.')" title="'.$fnT("Approve / Decline").'"> '.$fnT("Approve / Decline").'</button>';
				}
			} else if ($auxStatus == 'Approved') {
				if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 10, 14,20] ) ) {
					$btnAction = '<button class="btn btn-primary btnCloseAction" onClick="fntCloseAction('.$auxId.','.$auxIdOpp.')" title="'.$fnT("Finish action").'"> '.$fnT("Finish action").'</button>';
				}
			}

			
			$data[$i]['options'] = '<div class="text-center">'.$btnAction.'</div>';

			/*$btnView = '<button class="btn btn-secondary btn-sm btnViewUsuario" onClick="fntViewUsuario('.$arrData[$i]['id'].')" title="View user"> <i class="fa fa-eye"></i></button>';
			
			if($this->permission['u']){
				$btnEdit = '<button class="btn btn-primary btn-sm btnEditUsuario" onClick="fntEditUsuario(this,'.$arrData[$i]['id'].')" title="Edit"> <i class="fa fa-pencil"></i></button>';
			}

			if($this->permission['d']){
				$btnDelete = '<button class="btn btn-danger btn-sm btnDelUsuario" onClick="fntDelUsuario('.$arrData[$i]['id'].')" title="Delete"> <i class="fa fa-trash"></i></button>';
			}

			$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';*/
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function setAction()
	{
		if($_POST){
			if(empty($_POST['opp_id']) || empty($_POST['action'])){
				$arrResponse = array("status" => false, "msg" => "Wrong data");
			}else{
				$auditType = getAuditType($_POST['opp_audit_id']);
				// if( $_SESSION['userData']['role']['id'] = 1 ) dep($_POST); dep($auditType);  die('TIPO');

				if( in_array( $auditType['type'], ["Self-Evaluation"] ) ) {
					$status = 'Finished';
				} else {
					$status = 'In Review';
				}
				
				$idOpp = intVal($_POST['opp_id']);
				$evidencia = $_POST['evidencia'];
				$action = ucwords(strClear($_POST['action']));
				$actionDate = date('Y-m-d H:i:s');
				//$status = 'In Review';
				$request_action = "";
				require_once("Models/Audit_OppModel.php");
				$objData = new Audit_OppModel();
				//$request_opp = $objData->updateOpportunity(['actionplan_status'],"id = $_POST['opp_id']");
				$request_opp = $objData->updateOpportunity([
					"actionplan_status"	=> $status
				], "id = {$_POST['opp_id']}");

				$request_action = $this->model->insertPlanAction($idOpp, $action, $actionDate, $status,$evidencia);

				if($request_action > 0)
				{
					$arrResponse = array("status" => true, "msg" => "Data saved successfully");
				}else{
					$arrResponse = array("status" => false, "msg" => "It is not possible to store the data");
				}
			}

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function updateStatus()
	{
		$idAction = intVal($_GET['id']);
		$statusAction = $_GET['status'];
		$request_action = "";

		$request_action = $this->model->updatePlanAction($idAction, $statusAction);

		require_once("Models/Audit_OppModel.php");
		$objData = new Audit_OppModel();
		$request_opp = $objData->updateOpportunity([
			"actionplan_status"	=> $statusAction
		], "id = {$_GET['idOpp']}");
		

		if($request_action > 0){
			$arrResponse = array("status" => true, "msg" => "Data saved successfully");
		}else{
			$arrResponse = array("status" => false, "msg" => "It is not possible to store the data");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function refreshStatistics()
	{
		progressTemplateActionPlan($_GET['id']);
		//die();
	}

}
?>