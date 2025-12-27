<?php
require_once 'Models/RoundModel.php';
require_once 'Models/AuditoriaModel.php';
class Audits extends Controllers{

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

	public function audits()
	{
		$data['page_tag'] = "Audits";
		$data['page_title'] = "Audit list";
		$data['page_name'] = "audits";
        $data['page-functions_js'] = "functions_audits.js?15022024";
		$data['permision'] = $this->permission;
		
        $data['types'] = $this->model->getTypes();
        $data['type'] =  empty($_GET['type'])? $data['types'][0]['type'] : base64_decode($_GET['type']);
        
		$data['country_name'] = base64_decode($_GET['country']);
		$data['country_where'] = "";

if (!empty($_GET['country'])) {
    $countryDecoded = base64_decode($_GET['country']);
    $data['country_where'] = "AND country_name = '" . $countryDecoded . "'";
}
		
		$data['audit_list'] = $this->model->getAuditList(['id', 'checklist_id', 'location_id', 'round_name', 'period', 'auditor_name', 'auditor_email', 'status', 'date_visit', 'local_foranea', 'location_number', 'location_name', 'location_address','country_id', 'country_name', 'region', 'brand_id', 'brand_name', 'brand_prefix' ,'email_ops_director','email_ops_leader','email_area_manager','email_franchisee','concept','shop_type','area','franchissees_name','audit_result','audit_criticos','audit_rojo'], 
														  "type='{$data['type']}' ".$data['country_where'], 
														  true);
     
    
		// dep ($data['audit_list']);
		// die();
		$data['locations'] = selectLocation(['id', 'number', 'country_id', 'name'], "id IN({$_SESSION['userData']['location_id']}) OR ('{$_SESSION['userData']['location_id']}' = 0 AND country_id IN({$_SESSION['userData']['country_id']}))");
		
		$data['auditor_email'] = [];
		$data['round_name'] = [];
        $data['status'] = [];
        $data['region'] = [];
        
		$data['brand'] = [];
		$data['audit_location'] = [];
		$data['master'] = [];
		$data['result'] = [];

		$data['area'] = [];
		$data['shop_type'] = [];
		$data['country'] = [];
		$data['concept'] = [];
		$data['franchissees_name'] = [];
		$data['email_area_manager'] = [];
		$data['email_ops_leader'] = [];
		$data['email_ops_director'] = [];
		$data['audit_result'] = [];
		$data['audit_criticos'] = [];
		$data['audit_rojo'] = [];

		



		/*$data['area'] = $this->model->getArea();
		$data['shop_type'] = $this->model->getShopType();
		$data['country'] = $this->model->getCountry();
		$data['concept'] = $this->model->getConcept();
		$data['franchissees_name'] = $this->model->getEmailFranchisee();
		$data['email_area_manager'] = $this->model->getEmailAreaManager();
		$data['email_ops_leader'] = $this->model->getEmailOpsLeader();
		$data['email_ops_director'] = $this->model->getEmailOpsDirector();*/


		foreach($data['audit_list'] as $item){
			
			if(!in_array($item['auditor_email'], $data['auditor_email'])){
				array_push($data['auditor_email'], $item['auditor_email']);
			}
			if(!in_array($item['round_name'], $data['round_name'])){
				array_push($data['round_name'], $item['round_name']);
			}
			if(!array_key_exists($item['location_number'], $data['audit_location'])){
				$data['audit_location'][$item['location_number']] = $item['location_name'];
			}

			if(!in_array($item['area'], $data['area'])){
				array_push($data['area'], $item['area']);
			}
			if (!in_array($item['shop_type'], $data['shop_type'])) {
				array_push($data['shop_type'], $item['shop_type']);
			}
			if (!in_array($item['country_name'], $data['country'])) {
				array_push($data['country'], $item['country_name']);
			}
			if (!in_array($item['concept'], $data['concept'])) {
				array_push($data['concept'], $item['concept']);
			}
			if (!in_array($item['franchissees_name'], $data['franchissees_name'])) {
				array_push($data['franchissees_name'], $item['franchissees_name']);
			}
			if (!in_array($item['email_area_manager'], $data['email_area_manager'])) {
				array_push($data['email_area_manager'], $item['email_area_manager']);
			}
			if (!in_array($item['email_ops_leader'], $data['email_ops_leader'])) {
				array_push($data['email_ops_leader'], $item['email_ops_leader']);
			}
			if (!in_array($item['email_ops_director'], $data['email_ops_director'])) {
				array_push($data['email_ops_director'], $item['email_ops_director']);
			}
			if (!in_array($item['audit_result'], $data['audit_result'])) {
				array_push($data['audit_result'], $item['audit_result']);
			}
			if (!in_array($item['audit_criticos'], $data['audit_criticos'])) {
				array_push($data['audit_criticos'], $item['audit_criticos']);
			}
			if (!in_array($item['audit_rojo'], $data['audit_rojo'])) {
				array_push($data['audit_rojo'], $item['audit_rojo']);
			}
			

			// if(!in_array(($item['master']?? 'No'), $data['master'])){
			// 	array_push($data['master'], $item['master']?? 'No');
			// }
			
			if(empty($data['status'][$item['status']])){
				$data['status'][$item['status']] = 1;
			} else{
				$data['status'][$item['status']]++;
			}

			// if(empty($data['region'][$item['region']])){
			// 	$data['region'][$item['region']] = [$item['country_id']];
			// } else{
			// 	array_push($data['region'][$item['region']], $item['country_id']);
			// }

			// if(!in_array($item['country_id'], array_keys($data['country']))){
			// 	$data['country'][$item['country_id']] = $item['country_name'];
			// }

			// if(!in_array($item['brand_id'], array_keys($data['brand']))){
			// 	$data['brand'][$item['brand_id']] = $item['brand_name'];
			// }
			// if(!in_array($item['result'], $data['result']) and !empty($item['result'])){
			// 	array_push($data['result'], $item['result']);
			// }

			$data['country_location'][$item['country_id']][] = $item['location_number'];
		}



		$this->views->getView($this, "audits", $data);
	}

	public function audit()
	{
		$data['page_tag'] = "Audit";
		$data['page_title'] = "Audit";
		$data['page_name'] = "audit";
        $data['page-functions_js'] = "functions_audit.js";
		$data['permision'] = $this->permission;

		$tmp = $this->model->getAuditList(['checklist_id', 'status', 'visit_status', 'audited_areas'], 'id =' . $_GET['id']??-1);
		$checklist_id = $tmp[0]['checklist_id']??-1;

		$data['status'] = $tmp[0]['status'];
		$data['visit_status'] = $tmp[0]['visit_status'];
		$data['section'] = listSeccions($checklist_id, $tmp[0]['audited_areas']);
		$data['question'] = $this->model->getChecklist($checklist_id, $_GET['id'], $_SESSION['userData']['default_language'], $tmp[0]['audited_areas']);
		
		$this->views->getView($this, "audit", $data);
	}

	public function getAnswers()
	{
		$arrOpportunity = selectOpportunity(
			['id','auditor_answer','auditor_comment'], 
			"checklist_item_id={$_POST['picklist_id']} AND audit_id={$_POST['audit_id']}"
		);

		if(!empty($arrOpportunity)){
			$arrOpportunity = $arrOpportunity[0]; 
			
			$arrFiles = selectAuditFiles(
				['id', 'name', 'url'], 
				"reference_id = {$arrOpportunity['id']} AND type = 'Opportunity'"
			);
		}

		$resAnswers = [];
		foreach(listAnswers($_SESSION['userData']['default_language'], $_POST['picklist_id']) as $key => $value){
			array_push($resAnswers, [
				'text'	=> $value,
				'opp' 	=> matchAnswer($key, $arrOpportunity['auditor_answer']??'')
			]);
		}
		
		die(json_encode([
			'answers'	=> $resAnswers,
			'comment'	=> $arrOpportunity['auditor_comment']?? '',
			'files'		=> $arrFiles?? [],
			'opp_id'	=> $arrOpportunity['id']?? ''
		], JSON_UNESCAPED_UNICODE));
	}

	public function auditFiles()
	{
		$data['page_tag'] = "Audit Files";
		$data['page_title'] = "Audit Files";
		$data['page_name'] = "audit files";
        $data['page-functions_js'] = "functions_photography.js";
		$data['files'] = [];

		$auditFiles = selectAuditFiles(['type', 'name', 'url', 'reference_id'], 'audit_id =' . $_GET['id']??-1);
		foreach($auditFiles as $af){
			if(!array_key_exists($af['type'], $data['files'])){
				$data['files'][$af['type']] = [];
			}
			array_push($data['files'][$af['type']], ['name' => $af['name'], 'url' => $af['url'], 'reference_id' => $af['reference_id']]);
		}
		$this->views->getView($this, "audit_files", $data);
	}
	
	public function auditInfo()
	{
		$data['page_tag'] = "General information";
		$data['page_title'] = "General information";
		$data['page_name'] = "general information";
        $data['page-functions_js'] = "functions_gral_info.js?071223";
		$tmp = $this->model->getAudits(
			['date_visit', 'date_visit_end', 'visit_status', 'manager_email', 'manager_name', 'manager_signature', 'visit_comment', 'audited_areas'],
			'id =' . $_GET['id']
		);
		$data['audit'] = $tmp[0];
		$data['areas'] = $this->model->listAreas();
		$data['permision'] = $this->permission;
		$this->views->getView($this, "audit_grl_info", $data);
	}
	
	public function updGrlInfo(){
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));
		}

		$areas = in_array('all', $_POST['areas'] ?? ['all']) ? null : json_encode(array_values($_POST['areas']));


		$arrUpdate = [
			"status"			=> 'In Process',
			"date_visit" 		=> $_POST['date_visit'] . ' ' . $_POST['start_time'],
			"date_visit_end" 	=> $_POST['date_visit'] . ' ' . $_POST['end_time'],
			"visit_status" 		=> $_POST['visit_status'],
			"visit_comment"		=> $_POST['visit_comment']?? NULL,
			"manager_email" 	=> $_POST['manager_email']?? NULL,
			"manager_name" 		=> $_POST['manager_name']?? NULL,
			"audited_areas"		=> $areas
		];

		if($_POST['visit_status'] == 'Closed'){
			$arrUpdate['status'] = 'Closed';
		}else{
			$tmp = selectAudit(['scoring_id'], 'id ='. $_POST['audit_id']);
			setScore($_POST['audit_id'], $tmp[0]['scoring_id']);
		}
		$status = $this->model->updateAudit($arrUpdate, "id = {$_POST['audit_id']}");

		die(json_encode(['status' => $status? 1 : 0], JSON_UNESCAPED_UNICODE));
	}

	public function auditTools()
	{
		if(!in_array($_SESSION['userData']['role']['id'], [1, 2,3])){
			die(http_response_code(401));
		}

		$data['page_tag'] = "Audit Tools";
		$data['page_title'] = "Audit Tools";
		$data['page_name'] = "audit Tools";
        $data['page-functions_js'] = "functions_audit_tools.js";
		
		$data['audit'] = $this->model->getAuditList([], "id=" . $_GET['id'])[0];
		$data['permision'] = $this->permission;

		$data['audit']['front_door_pic'] = selectAuditFiles(
			['url'],
			"audit_id = {$_GET['id']} AND name = 'Foto de fachada de la tienda'"
		);
		$data['audit']['round_id'] = AuditoriaModel::getAudit(['round_id'], "id=" . $_GET['id'])[0]['round_id'];


		if( in_array($_SESSION['userData']['role']['id'], [1, 2]) ){
			
			$data['rounds'] = [];
			$rounds = RoundModel::getRound([], 'country_id='.$data['audit']['country_id']);
			$roundsName = RoundModel::getAllRoundNames();
			$roundsType = RoundModel::getAllRoundTypes();
			$data['name_rounds'] = [];
			$data['types'] = [];
			// print_r($roundsName);
			// print_r($roundsType);
			// dep($roundsName);
			// die();

			foreach($rounds as $r) $data['rounds'][] = $r;
			foreach($roundsName as $rn) $data['name_rounds'][] = $rn['names'];
			foreach($roundsType as $rt) $data['types'][] = $rt['types'];
		}
		

		$this->views->getView($this, "audit_tools", $data);
	}
	
	public function moveAuditStatus()
	{
		if(!in_array($_SESSION['userData']['role']['id'], [1, 2])){
			die(http_response_code(401));
		}

		$arrUpdate = [
			"status"			=> $_POST['audit_status'],
			"date_release"		=> $_POST['audit_status'] == 'Completed'? date('Y-m-d H:i:s') : NULL
		];
		
		$audit = selectAudit(['visit_status'], 'id='.$_POST['audit_id'])[0];
		if($_POST['audit_status'] == 'Pending' && $audit['visit_status'] == 'Closed'){
			$arrUpdate['visit_status'] = null;
		}

		$status = $this->model->updateAudit($arrUpdate, "id = {$_POST['audit_id']}");
		die(json_encode(['status' => $status? 1 : 0], JSON_UNESCAPED_UNICODE));
	}
	
	public function deleteSelfEvaluation($audit_id)
	{
		if(!$this->permission['u'] and !isMySelfEvaluation($audit_id)){
			die(http_response_code(401));
		}

		$arrUpdate = [
			"status"			=> 'Deleted!',
			"date_release"		=> NULL
		];
		
		$status = $this->model->updateAudit($arrUpdate, "id = $audit_id");
		die(json_encode(['status' => $status? 1 : 0], JSON_UNESCAPED_UNICODE));
	}

		public function moveAuditRound()
	{
		if (!in_array($_SESSION['userData']['role']['id'], [1, 2])) {
			die(http_response_code(401));
		}

		$arrUpdate = [
			"round_id"			=> $_POST['audit_round']
		];
		$status = $this->model->updateAudit($arrUpdate, "id = {$_POST['audit_id_round']}");

		if($status){
			$this->model->updateAuditLog($_POST['audit_id_round'], $_SESSION['userData']['id'], "r{$_POST['audit_round']}");
		}

		die(json_encode(['status' => $status ? 1 : 0], JSON_UNESCAPED_UNICODE));
	}


	public function setSignaturePic(){
		if(!in_array($_SESSION['userData']['role']['id'], [1, 2])){
			die(http_response_code(401));
		}

		$arrUpdate = [
			'manager_signature' => $_POST['url_pic']
		];
		$status = $this->model->updateAudit($arrUpdate, "id = {$_POST['audit_id']}");
		die(json_encode(['status' => $status? 1 : 0], JSON_UNESCAPED_UNICODE));
	} 
	
	public function setFrontDoorPic(){
		if(!in_array($_SESSION['userData']['role']['id'], [1, 2])){
			die(http_response_code(401));
		}

		require_once("Models/Audit_FileModel.php");
		$objAuditFile = new Audit_FileModel();

		$objAuditFile->deleteAudit_File("audit_id = {$_POST['audit_id']} AND name = 'Picture of the Front Door/Entrance of the Restaurant'");
		$status = $objAuditFile->insertFrontDoorPic($_POST['audit_id'], $_POST['url_pic']);
		
		die(json_encode(['status' => $status > 0], JSON_UNESCAPED_UNICODE));
	} 

	public function sendPlanReminder(int $id)
	{
		$audit = $this->model->getAuditList([], "id=$id")[0];

		$locationMails = getLocationEmails(['Fanchisee' , 'Ops Director' , 'Ops Leader' , 'Area Manager' , 'Store Manager'], $audit['location_id']);
		$recipientsReminderPlan = emailFilter("$locationMails");
		
		$totalOpps = countTotalOpps($audit['id']);
		if($totalOpps > 0){
			$tmp = getScore($audit['id']);

			$limitDays = $tmp['Criticos'] > 0? 1 : 7;
			$dateLimit = date("d-m-Y",strtotime($audit['date_release']."+ $limitDays days"));
			
			$asunto = "{$audit['brand_prefix']} #{$audit['location_number']} ({$audit['country_name']}) @ Recordatorio del plan de acción";
			$arrMailPlanReminder = ['asunto' 				=> $asunto,
									'email' 				=> $recipientsReminderPlan,
									'audit_id'				=> $audit['id'],
									'score'					=> "Criticos {$tmp['Criticos']} | No criticos {$tmp['NoCriticos']} | Amarillos {$tmp['Amarillos']} | Rojos {$tmp['Rojos']} | Mantenimiento {$tmp['Amarillos']} | Cero tolerancia {$tmp['AutoFail']}",
									'round_name'			=> $audit['round_name'],
									'type'					=> $audit['type'],
									'date_visit'			=> $audit['date_visit'],
									'date_release'			=> $audit['date_release'],
									'date_limit'			=> $dateLimit,
									'limit_days'			=> $limitDays,
									'total_opps'			=> $totalOpps['opps'],
									'location_number'		=> $audit['location_number'],
									'location_name'			=> $audit['location_name'],
									'location_address'		=> $audit['location_address'] ];
			sendEmail($arrMailPlanReminder, 'plan_reminder_gm');
		}
	}

	public function nextStep(){
		if(!$this->permission['u'] and !isMySelfEvaluation($_POST['audit_id'])){
			die(http_response_code(401));
		}

		$tmp = $this->model->getAuditList(['status', 'type', 'location_id', 'country_id', 'country_name', 'location_number', 'region', 'location_address', 'brand_id', 'brand_prefix', 'report_layout_id', 'date_visit', 'manager_email','country_language','region'], 'id='.$_POST['audit_id']);
		$currAudit = $tmp[0];
		
		
		$steps = ['Pending', 'In Process', 'Completed'];
		$index = array_search($currAudit['status'], $steps);
		$status = ['status' => 0];

	


		

		
		if(!empty($index) or $index == 0){
			$nextStatus = $steps[$index + 1];
			if(!empty($nextStatus)){
				$arrUpdate = [
					"status" 		=> $nextStatus,
					"date_release"	=> $nextStatus=='Completed'? date('Y-m-d H:i:s') : NULL
				];
				$status = $this->model->updateAudit($arrUpdate, 'id='. $_POST['audit_id']);
				if($status){
					$this->model->updateAuditLog($_POST['audit_id'], $_SESSION['userData']['id'], $nextStatus);
					if($nextStatus == 'Completed'){

						$tmp = getScore($_POST['audit_id']);

						
		$fnT = translate($currAudit['country_language']);				



		
		    $templateEmail = 'aviso_liberacion';
			$asunto = $fnT('Final Report');
			$calif  = $tmp['Result'] == 'Fail'? $fnT('Fail') : $fnT('Approved');
			$score  = "{$fnT('Critical')} {$tmp['Criticos']} | {$fnT('Basics')} {$tmp['NoCriticos']} | {$fnT('Yellow')} {$tmp['Amarillos']} | {$fnT('Red')}  {$tmp['Rojos']} | {$fnT('Maintenance')} {$tmp['Amarillos']} | {$fnT('Zero tolerance')} {$tmp['AutoFail']}";




			 $mail = "";

		  	if ($currAudit['type'] == "Training-visits") {
				
				$calif = "N/A";

			if($currAudit['country_name'] == 'Panama' ){

				  		$mail = "Jonathan.Edwards@idq.com";

			}elseif($currAudit['country_name'] == 'Indonesia' || $currAudit['country_name'] == 'Qatar'|| $currAudit['country_name'] == 'Bahrain'){

				 		$mail = ",businesssupport-ca@lrqa.com,wildon.lacro@idq.com,Jonathan.Edwards@idq.com";

			}
			elseif($currAudit['country_name'] == 'Philippines'){
 						$mail = ",businesssupport-ca@lrqa.com, 
								  wildon.lacro@idq.com,		   
								  ljuinio@ppiholdingsinc.com.ph,
								  deromarate@ppiholdingsinc.com.ph,
								  cdabu@ppiholdingsinc.com.ph,
								  rherrera@ppiholdingsinc.com.ph,
								  aguillermo@ppiholdingsinc.com.ph,
								  dqtraining@ppiholdingsinc.com.ph,
								  nborillo@ppiholdingsinc.com.ph,
								  cmlim@ppiholdingsinc.com.ph,
								  rcafranca@ppiholdingsinc.com.ph,
								  jcapistrano@ppiholdingsinc.com.ph,
								  murbano@ppiholdingsinc.com.ph";
			}
			else{
				 
						$mail = ",businesssupport-ca@lrqa.com";

            }} 
						
						
						if($currAudit['type'] != 'Calibration Audit'){
							$locationMails = getLocationEmails(['Fanchisee' , 'Ops Director' , 'Ops Leader' , 'Area Manager' , 'Store Manager'], $currAudit['location_id']);

							if($tmp['Result'] == 'Fail' && getScorePrevius($currAudit['location_number'], "Standard','Re-Audit','2nd Re-Audit", $_POST['audit_id'])['result'] == 'Fail'){
								$recidivist = ' (Reincidente)';
							}
						}
						$AdminMails = getLocationEmails(['admin arguilea'], 0);
						if($currAudit['type'] == 'Self-Evaluation' || $currAudit['type'] == 'IDQ Internal Audit'){
							$recipients = emailFilter("{$currAudit['manager_email']},$locationMails");
						}else {
							$recipients = emailFilter("{$currAudit['manager_email']},$locationMails,$AdminMails,$mail");

						}
					

						sendEmail([
							'asunto' 				=> "{$currAudit['brand_prefix']} #{$currAudit['location_number']} ({$currAudit['country_name']}) @ {$asunto}",
							'lang' 			        =>  $currAudit['country_language'],
							'email' 				=> $recipients,
							'audit_id'				=> $_POST['audit_id'],
							'score'					=> $score,
							'result'				=> ($calif) . $recidivist,
							'type'					=> $currAudit['type'],
							'location_number'		=> $currAudit['location_number'],
							'location_address'		=> $currAudit['location_address'],
							'url_report'			=> getURLReport($_POST['audit_id'], $currAudit['report_layout_id'],$currAudit['country_language'])
						], $templateEmail);
						
						if(in_array($currAudit['type'] , ['Standard','Re-Audit','2nd Re-Audit'])) {
							$this->sendPlanReminder($_POST['audit_id']);
						}
					}
				}
				$status = ['status' => $status? 1 : 0, 'currStatus' => $nextStatus, 'nextStatus' => $steps[$index + 2], 'cal' => $cal, 'template' => $templateEmail, 'lang' => $currAudit['country_language'] ];
			}
		}
		die(json_encode($status, JSON_UNESCAPED_UNICODE));
	}

	public function addAutoEval()
	{
		//dep($_POST);
		//die();
		if($_POST)
		{
			$location_id = intVal($_POST['location_id']);
			$country_id = intVal($_POST['country_id']);
			$brand_id = array_key_first($_SESSION['userData']['brand']);

			if($location_id == '' || $country_id == '' || $brand_id == ''){
				$arrResponse = array("status" => false, "msg" => "Datos incorrecto.");
			}else{

				$request_validar = $this->model->validateRoundAutoEval($brand_id, $country_id);

				if(empty($request_validar)){
					$request_insertR = $this->model->insertRoundAutoEval($brand_id, $country_id);

					if($request_insertR > 0){
						$arrResponse = array("status" => false, "msg" => "The Round has been generated correctly");
					}else{
						$arrResponse = array("status" => false, "msg" => "It is not possible to generate the Round");
					}
				}

				$round = $this->model->validateRoundAutoEval($brand_id, $country_id);

				if(!empty($round)){
					//dep($auditoria);
					$round_id = $round['id'];
					$tipoViaje = 'Local';
					$request = "";

					$request = $this->model->insertAuditAutoEval($round_id,
																		$location_id,
																		$tipoViaje,
																		$country_id,
																		$brand_id);



				$idAudit = $this->model->validateId($location_id);// devuelve el id de la auditoria creada

					if($request > 0){
						$arrResponse = array("status" => true, "msg" => "The record has been generated correctly", "id_audit" =>  $idAudit['id_audit']);
					}else if($request == 'exist'){
						$arrResponse = array("status" => false, "msg" => "It is not possible to perform 2 evaluations in the same month");
					}else{
						$arrResponse = array("status" => false, "msg" => "It is not possible to generate the record");
					}
				}
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}


	public function addIDQ()
	{
		//dep($_POST);
		//die();
		if($_POST)
		{
			$location_id = intVal($_POST['location_id']);
			$country_id = intVal($_POST['country_id']);
			$brand_id = array_key_first($_SESSION['userData']['brand']);

			if($location_id == '' || $country_id == '' || $brand_id == ''){
				$arrResponse = array("status" => false, "msg" => "Datos incorrecto.");
			}else{

				$request_validar = $this->model->validateRoundIDQ($brand_id, $country_id);

				if(empty($request_validar)){
					$request_insertR = $this->model->insertRoundIDQ($brand_id, $country_id);

					if($request_insertR > 0){
						$arrResponse = array("status" => false, "msg" => "The Round has been generated correctly");
					}else{
						$arrResponse = array("status" => false, "msg" => "It is not possible to generate the Round");
					}
				}

				$round = $this->model->validateRoundIDQ($brand_id, $country_id);

				if(!empty($round)){
					//dep($auditoria);
					$round_id = $round['id'];
					$tipoViaje = 'Local';
					$request = "";

					$request = $this->model->insertAuditIDQ($round_id,
																		$location_id,
																		$tipoViaje,
																		$country_id,
																		$brand_id);



				$idAudit = $this->model->validateIdIDQ($location_id);// devuelve el id de la auditoria creada

					if($request > 0){
						$arrResponse = array("status" => true, "msg" => "The record has been generated correctly", "id_audit" =>  $idAudit['id_audit']);
					}else if($request == 'exist'){
						$arrResponse = array("status" => false, "msg" => "It is not possible to perform 2 evaluations in the same month");
					}else{
						$arrResponse = array("status" => false, "msg" => "It is not possible to generate the record");
					}
				}
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function testScore(){
		$test = setScore(3, 1);
		die(print_r($test));
	}

	public function auditPrint($checklist_token)
	{
		$data['page_tag'] = "Audit Print";
		$data['page_title'] = "Audit Print";
		$data['page_name'] = "audit print";
        $data['page-functions_js'] = "functions_audit_print.js";
		$tmp = $this->model->getPrintChecklist(decryptId($checklist_token));
		$data['checklist_item'] = array_reduce($tmp, function($acc, $cur){
			if($cur['type'] == 'Question'){
				$aux = [
					'text'		=> $cur['text'],
					'points'	=> $cur['points'],
					'question'	=> true
				];
			} else {
				$aux = [
					'text'		=> $cur['text'],
					'answers'	=> explode('|', $cur['text_answer']),
					'prefix'	=> $cur['picklist_prefix'],
					'picklist'	=> true
				];
			}

			$acc[$cur['section_number'] . '. ' . $cur['section_name']][$cur['question_prefix']][] = $aux;
			return $acc;

		}, []);

		$this->views->getView($this, "audit_print", $data);
	}

	public function selectOpp(){
		
		$id_audit = $_POST['id'];
		$type 	  = $_POST['type'];
    	echo json_encode($this->model->selectOpp($id_audit,$type), true);
	
	}







}
?>