<?php

require_once 'Models/AuditsModel.php';
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
require_once 'Models/ActionPlanModel.php';
require_once 'Models/Audit_FileModel.php';

class Appeals extends Controllers{

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
		$this->permission = $_SESSION['userData']['permission']['Aclaraciones'];
		if(!$this->permission['r']){
			header('Location: '.base_url());
		}
	}

	public function appeals(){
		$data['page_id'] = 2;
		$data['page_tag'] = "Appeals";
		$data['page_title'] = "Appeals";
		$data['page_name'] = "appeals";
		$data['page-functions_js'] = "functions_appeals.js";

		$data['rounds'] = $this->model->listRounds();	
		$data['status'] = ['Pending', 'Completed'];
		$data['stores'] = $this->model->listStores();
		
		$this->views->getView($this, "appeals", $data);
	}

	public function getAudits()
	{
		
		$fil = "(status='Completed' AND type IN('Standard') AND DATEDIFF(NOW(), date_visit_end) <= 7 AND id NOT IN (SELECT audit_id FROM appeal))  AND (a.location_id IN({$_SESSION['userData']['location_id']}))";

		if(!in_array($_SESSION['userData']['role']['id'], [1, 2] ) ) {
			$fil .= " AND country_id IN({$_SESSION['userData']['country_id']}) AND (a.location_id IN({$_SESSION['userData']['location_id']}) OR '{$_SESSION['userData']['location_id']}'=0)";
		}

		$selectColumns = ['id', 'checklist_id', 'round_name', 'auditor_name', 'auditor_email', 'type', 'status', 'date_visit', 'local_foranea', 'location_number', 'location_id', 'location_name', 'location_address', 'country_id', 'country_name', 'region', 'brand_id', 'brand_name', 'brand_prefix' , '(7-DATEDIFF(NOW(), date_visit_end))restante'];
		$data['audit_list'] = $this->model->getAuditList($selectColumns, $fil);	

		$htmlOptions = '';
		if(count($data['audit_list']) > 0){
			for($i=0; $i<count($data['audit_list']); $i++){
			   $htmlOptions .= '<option data-lnumber="#'. $data['audit_list'][$i]['location_number'] .' - '. $data['audit_list'][$i]['location_name'] .'" 
										data-lid="'. $data['audit_list'][$i]['location_id'] .'" value="'.$data['audit_list'][$i]['id'].'">
										'.$data['audit_list'][$i]['id'].'- ['.$data['audit_list'][$i]['type'].'] #'.$data['audit_list'][$i]['location_number'].' - '.$data['audit_list'][$i]['location_name'].' ('.$data['audit_list'][$i]['country_name'].') '.$data['audit_list'][$i]['round_name'].' ('.$data['audit_list'][$i]['restante'].')
								</option>';
			}
		}
		die($htmlOptions);
	}

	public function getOpps() {
		$this->views->getView($this, "appealOpps");
	}

	public function getAppeals() {
		global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']);
		

		$filter = "";

		if (isset($_POST['f_status']) ) {
			$f_status = "'" . implode("','", $_POST['f_status']) . "'";
			$filter .= " and t1.status IN ($f_status)";
		} else{
			$filter .= " and 0";
		}

		if (isset($_POST['f_round']) ) {
			$f_round = "'" . implode("','", $_POST['f_round']) . "'";
			$filter .= " and (select round_name FROM audit_list where id = t1.audit_id) in ($f_round)";
		} else{
			$filter .= " and 0";
		}

		if (isset($_POST['f_store']) ) {
			$f_store = "'" . implode("','", $_POST['f_store']) . "'";
			$filter .= " and t1.location_id IN ($f_store)";
		} else{
			$filter .= " and 0";
		}

		$data = $this->model->selectAppeals($filter);

		$dataAppeals = array();
		if (!empty($data['appeals'])) {
			foreach ($data['appeals'] as $values) {
				// Definir que roles pueden editar las apelaciones
				$disabled = "disabled";
				if( in_array( $_SESSION['userData']['role']['id'], [1, 2,17] ) and $values['status'] == 'Pending' ) {
					$disabled = "";
				}
				
				$datas['clarifications'] = '';
				foreach ($values['items'] as $item) {
					$filesOpp = '';
					$dataFilesOpp = ActionPlanModel::getFilesOpp($item['audit_opp_id']);
					foreach ($dataFilesOpp as $key) {
						$filesOpp .= ' <div class="mr-3 my-2 mb-3">
										<a href="'.$key['url'].'" target="_blank">
											<img style="height:100px; width:100px" class="rounded shadow-sm of-cover cr-pointer" src="'.$key['url'].'">
										</a><br>
									</div>';
					}
					
					$filesApp = '';
					$dataFilesApp = $this->model->getFilesApp($item['audit_opp_id']);
					foreach ($dataFilesApp as $key) {
						$filesApp .= ' <div class="mr-3 my-2 mb-3">
										<a href="'.$key['url'].'" target="_blank">
											<img style="height:100px; width:100px" class="rounded shadow-sm of-cover cr-pointer" src="'.$key['url'].'">
										</a><br>
									</div>';
					}

					$datas['clarifications'] .= 
						'<div class="mb-2" style="border-left: 5px solid #28A745; padding: 5px; border-top: 1px solid #CCC; border-right: 1px solid #CCC; border-bottom: 1px solid #CCC;">
						<b><span class="badge badge-secondary">'.$item['question_prefix'].'</span> '.$item['eng'].'</b>
						<br><b><span class="text-secondary"><i class="fa fa-comment"></i>  '.$item['auditor_comment'].'</span></b>
						<br><div class="d-flex flex-wrap">'.$filesOpp.'</div>
						<br><b><span class="text-danger"><i class="fa fa-exclamation-triangle"></i> '.$fnT("Appeal").': '.$item['author_comment'].'</span></b>
						<br><div class="d-flex flex-wrap">'.$filesApp.'</div>
						<br><b><span class="text-info"> '.$fnT("Decision").': '.$item['decision_result'].'</span></b>
						<br><b><span class="text-info"> '.$fnT("Owner comment").': '.$item['decision_comment'].'</span></b></div>';
				}
				$datas['id'] = $values['id'];
				$datas['store'] = '<b>'.$values['location']['number'].' - '.$values['location']['name'].'</b>
									<br><b>'.$values['gralInfo']['round_name'].'</b>
									<br><b>'.$fnT("Region").': '.$values['gralInfo']['region'].'</b>
									<br><b>'.$fnT("Date of visit").': '.date("Y-m-d", strtotime( $values['gralInfo']['date_visit'] )).'</b>
									<br><b>'.$fnT("Audit type").': '.$fnT($values['gralInfo']['type']).'</b>';
				$datas['date'] = date("Y-m-d", strtotime( $values['date_start'] ));
				$datas['options'] = 
					'<b><span class="badge badge-info">'.$fnT($values['status']).'</span></b>
					<br><b><i class="fa fa-calendar"></i> '.$fnT("Date start").' '.date("Y-m-d", strtotime( $values['date_start'] )).'</b>
					<br><b>'.$fnT("Author").': <i class="fa fa-user"></i>  '.$values['author'].'</b>
					<br><b>'.$fnT("Owner").': <i class="fa fa-user"></i>  '.$values['owner'].'</b>
					<br><button class="btn btn-warning btnViewDetails" '.$disabled.' onclick="openModalUpd('.$values['id'].')" title="'.$fnT("Details").'">'.$fnT("Details").'</button><br>';
				array_push($dataAppeals,$datas);
			}
		}
		
		echo json_encode($dataAppeals,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function getOppsDT()
	{
		$fnT = translate($_SESSION['userData']['default_language']);
		$audit = $_GET['idAudit'];
		$data = $this->model->getOpps($audit);	

		for($i=0; $i<count($data); $i++){
			$btnAction = '';
			$badgeStatus = '';
			$auxStatus = '';
			$auxIdOpp = $data[$i]['id_audit_opp'];
			$dataFiles = ActionPlanModel::getFilesOpp($data[$i]['id_audit_opp']);
			$dataAppealItem = $this->model->selectAppealItem([], "audit_opp_id=".$data[$i]['id_audit_opp']);
			$data[$i]['id'] = $data[$i]['id_audit_opp'];

			$files = '';
			// $actions = '';
			foreach ($dataFiles as $key) {
				$files .= ' <div class="mr-3 mb-3">
								<a href="'.$key['url'].'" target="_blank">
									<img style="height:100px; width:100px" class="rounded shadow-sm of-cover cr-pointer" src="'.$key['url'].'">
								</a><br>
							</div>';
			}
			
			$answers = '';
			$answersArr = explode("|", $data[$i]['auditor_answer']);
			foreach ($answersArr as $keyA) {
				if(!empty($keyA)){
					$answers.= '<span class="text-danger"><i class="fa fa-times"></i> '.$keyA.'</span> <br>';
				}
			}

			$data[$i]['opportunity'] = '<span class="h6"><span class="badge badge-info"> '.$fnT($data[$i]['section_name']).'</span> <br>
										<span class="badge badge-secondary">'.$data[$i]['question_prefix'].'</span> '.$data[$i]['question'].' <span class="badge badge-danger">'.$fnT($data[$i]['questionV']) .'</span> <br>'.$answers;

			if(!empty($data[$i]['auditor_comment'])){
				$data[$i]['opportunity'] .= '<span class="text-secondary"><i class="fa fa-comment"></i> '.$data[$i]['auditor_comment'].'</span>';
			}
			
			if(!empty($files)){
				$data[$i]['opportunity'] .= '<br><div class="d-flex flex-wrap">'.$files.'</div>';

			}
			
			
			if ($dataAppealItem) {
				$options = '<span class="h6">'.$dataAppealItem['author_comment'].'</span> <br>
							<span class="badge badge-warning">'.$dataAppealItem['decision_result'].'</span>';
			}else {
				$options = '<div class="form-group col-md-12">
								<label for="action" class="control-label">'.$fnT("Appeal").'</label>
								<textarea class="form-control" name="appeal['.$data[$i]['id_audit_opp'].']" id="appeal'.$data[$i]['id_audit_opp'].'" cols="40" rows="3" style="resize: both;"></textarea>
								<span class="btn btn-info btn-sm my-2 input-in-btn">
									<i class="fa fa-camera"></i>'.$fnT("Evidence").'
									<input type="file" id="appealNewFile['.$data[$i]['id_audit_opp'].'][]" name="appealNewFile['.$data[$i]['id_audit_opp'].'][]" multiple="" onchange="uploadPic(this,'.$data[$i]['id_audit_opp'].')">
								</span>
								<div class="form-row justify-content-center">
									<div class="col-md-6 form-group" id="panel-pic'.$data[$i]['id_audit_opp'].'"></div>
								</div>
							</div>
							';
			}
			
			$data[$i]['options'] = '<div class="text-center">'.$options.'</div>';
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function getAppealsUpd() {
		$fnT = translate($_SESSION['userData']['default_language']);
		$appeal = $_GET['idAppeal'];
		$data = $this->model->selectAppealUpd($appeal);
		
		for($i=0; $i<count($data); $i++){
			$filesOpp = '';
			$dataFilesOpp = ActionPlanModel::getFilesOpp($data[$i]['audit_opp_id']);
			foreach ($dataFilesOpp as $key) {
				$filesOpp .= ' <div class="mr-3 my-2 mb-3">
								<a href="'.$key['url'].'" target="_blank">
									<img style="height:100px; width:100px" class="rounded shadow-sm of-cover cr-pointer" src="'.$key['url'].'">
								</a><br>
							</div>';
			}
			
			$filesApp = '';
			$dataFilesApp = $this->model->getFilesApp($data[$i]['audit_opp_id']);
			foreach ($dataFilesApp as $key) {
				$filesApp .= ' <div class="mr-3 my-2 mb-3">
								<a href="'.$key['url'].'" target="_blank">
									<img style="height:100px; width:100px" class="rounded shadow-sm of-cover cr-pointer" src="'.$key['url'].'">
								</a><br>
							</div>';
			}
			
			
			$data[$i]['id'] = $data[$i]['id_appeal_item'];
			$data[$i]['clarification'] = '<div class="mb-2" style="border-left: 5px solid #28A745; padding: 5px; border-top: 1px solid #CCC; border-right: 1px solid #CCC; border-bottom: 1px solid #CCC;">
							<b><span class="badge badge-secondary">'.$data[$i]['question_prefix'].'</span> '.$data[$i]['eng'].'</b>
							<br><b><span class="text-secondary"><i class="fa fa-comment"></i>  '.$data[$i]['auditor_comment'].'</span></b>
							<div class="d-flex flex-wrap">'.$filesOpp.'</div>
							<br><b><span class="text-danger"><i class="fa fa-exclamation-triangle"></i> '.$fnT("Appeal").': '.$data[$i]['author_comment'].'</span></b>
							<br><div class="d-flex flex-wrap">'.$filesApp.'</div>';
			$data[$i]['decision'] = 
				'<div>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text border-0">'.$fnT('Decision').'</span>
						</div>
						<select class="form-control" id="appealDes['.$data[$i]['id_appeal_item'].']" name="appealDes['.$data[$i]['id_appeal_item'].']" required>';
			
			// Definir que roloes pueden dictar si procede o no
			if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 14,17] ) ) {
				$data[$i]['decision'] .='
							<option value="" '.($data[$i]['decision_result']=='Pending'?'selected':'').'></option>
							<option value="Proceeds" '.($data[$i]['decision_result']=='Proceeds'?'selected':'').'>'.$fnT('Proceeds').'</option>
							<option value="Not proceeds" '.($data[$i]['decision_result']=='Not proceeds'?'selected':'').'>'.$fnT('Not proceeds').'</option>';
			}

			
			$data[$i]['decision'] .= '</select>
					</div><br>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text border-0">'.$fnT('Comments').'</span>
						</div>
						<textarea class="form-control" id="appealDesCom['.$data[$i]['id_appeal_item'].']" name="appealDesCom['.$data[$i]['id_appeal_item'].']" cols="40" rows="3" style="resize: both;" required></textarea>
					</div>
					<span class="btn btn-info btn-sm my-2 input-in-btn">
						<i class="fa fa-camera"></i>'.$fnT("Evidence").'
						<input type="file" id="appealNewFile['.$data[$i]['audit_opp_id'].'][]" name="appealNewFile['.$data[$i]['audit_opp_id'].'][]" multiple="" onchange="uploadPic(this,'.$data[$i]['audit_opp_id'].')">
					</span>
					<div class="form-row justify-content-center">
						<div class="col-md-6 form-group" id="panel-pic'.$data[$i]['audit_opp_id'].'"></div>
					</div>
				</div>';
		}
		
		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		die();
		
	}

	public function setAppeal()
	{
		if($_POST){

			// Buscar el apppeal 
			$isAppeal = $this->model->selectAppeal(['id'], "audit_id=$_POST[modal_audit_id]")[0];
			if ($isAppeal['id']) {
				$idAppeal = $isAppeal['id'];
			}else {
				$insertAppealValues = [
					'audit_id' => $_POST['modal_audit_id'],
					'author_user_id' => $_SESSION['userData']['user_id'],
					'status' => 'In Process',
					'date_start' => date('Y-m-d'),
				];
				$idAppeal = $this->model->insertAppeal($insertAppealValues);
			}

			$insertAppealItemValues = [
				'appeal_id' => $idAppeal,
				'audit_opp_id' => $_POST['opp_id'],
				'decision_result' => 'Pending',
				'author_comment' => $_POST['appeal'],
			];
			$request = $this->model->insertAppealItem($insertAppealItemValues);

			if($request > 0)
			{
				$arrResponse = array("status" => true, "msg" => "Data saved successfully");
			}else{
				$arrResponse = array("status" => false, "msg" => "It is not possible to store the data");
			}
		}

		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function setAllAppeals()
	{
		if($_POST){
			$tmp = $this->model->getAuditById([], 'id =' . $_POST['idAuditDT'])[0];

			$insertAppealValues = [
				'audit_id' => $_POST['idAuditDT'],
				'author_user_id' => $_SESSION['userData']['user_id'],
				'status' => 'Pending',
				'location_id' => $tmp['location_id'],
				'country_id' => $tmp['country_id'],
				'date_start' => date('Y-m-d'),
			];
			$idAppeal = $this->model->insertAppeal($insertAppealValues);

			if($idAppeal > 0) {
				foreach($_POST['appeal'] as $idOpp => $appealComment){
					if ($appealComment != ''){
						$insertAppealItemValues = [
							'appeal_id' => $idAppeal,
							'audit_opp_id' => $idOpp,
							'decision_result' => 'Pending',
							'author_comment' => $appealComment,
						];
						$appealItem = $this->model->insertAppealItem($insertAppealItemValues);

						if (isset( $_POST['urlFile'][$idOpp] )) { //Agregar imagenes por paso a apelaciones
							for ($i=0; $i < count($_POST['urlFile'][$idOpp]) ; $i++) { 
								$insertAppealFileValues = [
									'audit_id' => $_POST['idAuditDT'],
									'reference_id' => $idOpp,
									'type' => 'Appeal',
									'name' => 'New appeal for '.$idOpp,
									'description' => 'Unit evidence',
									'url' => $_POST['urlFile'][$idOpp][$i],
									'mimetype' => $_POST['typeFile'][$idOpp][$i]
								];
								//dep($insertAppealFileValues);
								$audit_file_id = Audit_FileModel::insertAudit_File($insertAppealFileValues);
							}
						}
					}
				}

				//Email inicial a MBP
				$this->appealNotification($_POST['idAuditDT']);
				$arrResponse = array("status" => true, "msg" => "Data saved successfully");
			} else {
				$arrResponse = array("status" => false, "msg" => "It is not possible to store the data");
			}
		}

		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function appealNotification($id )
	{
		
		//$id = 960;
		$audit       = AuditsModel::getAuditList([], "id=$id")[0];
		$appeal      = $this->model->selectAppeal([], "audit_id=$id")[0];
		$appealItems = $this->model->selectAppealUpd($appeal['id']);
$fnT = translate($audit['country_language']);
		$strAppeals = '<table width="100%" border="1">
                                <tr bgcolor="#E73712">
                                    <th>'.$fnT('Opportunity').'</th>
                                    <th>'.$fnT('Appeal').'</th>
                                
                                </tr>';


		foreach ($appealItems as $appeal) {
			$strAppeals .= '<tr>
								<td>
									<b>'.$appeal['question_prefix'].' '.$appeal['eng'].'</b>
									<br><b> '.$fnT('AUDITOR ANSWER').'</b> '.$appeal['auditor_answer'].'
									<br><b> '.$fnT('AUDITOR COMMENT').':</b> '.$appeal['auditor_comment'].'
								</td>
								<td>'.$appeal['author_comment'].'</td>
							
							</tr>';
		}
		$strAppeals .= '</table>';



        //$locationMails = getCountryEmails([ 'Store Manager',], $audit['country_id']);
		$locationMails = getLocationEmails(['Fanchisee' , 'Ops Director' , 'Ops Leader' , 'Area Manager' , 'Store Manager'], $audit['location_id']);
		$recipientsAppeals = emailFilter($locationMails);

		$totalOpps = countTotalOpps($audit['id']);
		$cal = getScore($audit['id'])['Calificacion'];
		$asunto =  $fnT('Appeals review')." ({$audit['country_name']}) {$audit['brand_prefix']} #{$audit['location_number']}";

		$arrMailAppeal = ['asunto' 			 => $asunto,
							'lang' 			 =>  $audit['country_language'],
						  'email' 			 => $recipientsAppeals,
						  'audit_id'		 => $audit['id'],
						  'score'			 => $cal,
						  'appeals'			 => $strAppeals,
						  'type'			 => $audit['type'],
						  'date_visit'		 => $audit['date_visit'],
						  'date_release'	 => $audit['date_release'],
						  'location_number'	 => $audit['location_number'],
						  'brand_prefix'	 => $audit['brand_prefix'],
						  'location_name'	 => $audit['location_name'],
						  'round_name'		 => $audit['round_name'],
						  'location_address' => $audit['location_address'],
						  'url_report'		 => getURLReport($audit['id'], $audit['report_layout_id']),];
						  
						
		$requestEmail = sendEmail($arrMailAppeal, 'appeal_process_mbp');
	}

	public function appealNotificationRefresh($id)
	{

		
		$audit 		 = AuditsModel::getAuditList([], "id=$id")[0];
		$appeal 	 = $this->model->selectAppeal([], "audit_id=$id")[0];
		$appealItems = $this->model->selectAppealUpd($appeal['id']);
$fnT = translate($audit['country_language']);
		$strAppeals = '<table width="100%" border="1">
                                <tr bgcolor="#E73712">
                                    <th>'.$fnT('Opportunity').'</th>
									<th>'.$fnT('Decision').'</th>
                                    <th>'.$fnT('Appeal').'</th>
                                    <th>'.$fnT('Comments').'</th>
                                </tr>';

		foreach ($appealItems as $appeal) {

			if ($appeal['decision_result'] == 'Proceeds' OR $appeal['decision_result'] == 'Proceeds by exception'){
				
				$strAppeals .= '<tr>
									<td><b>'.$appeal['question_prefix'].' '.$appeal['eng'].'</b>
										<br><b> Auditor answer:</b> '.$appeal['auditor_answer'].'
										<br><b> Auditor comment</b> '.$appeal['auditor_comment'].'</td>
									<td>'.$appeal['decision_result'].'</td>
									<td>'.$appeal['author_comment'].'</td>
									<td>'.$appeal['owner_comment'].'
										<br>'.$appeal['decision_comment'].'
									</td>
								</tr>';
			}

		}
		$strAppeals .= '</table>';

		$locationMails = getLocationEmails(['Fanchisee' , 'Ops Director' , 'Ops Leader' , 'Area Manager' , 'Store Manager'], $audit['location_id']);
		$recipientsAppeals = emailFilter($locationMails);

		$totalOpps = countTotalOpps($audit['id']);
		$cal 	   = getScore($audit['id'])['Calificacion'];
		$asunto    = $fnT('Final appeal decision')." ({$audit['country_name']}) {$audit['brand_prefix']} #{$audit['location_number']}";

		$arrMailAppeal = ['asunto' 			 => $asunto,
						  'lang' 			 =>  $audit['country_language'],
						  'email' 			 => $recipientsAppeals,
						  'audit_id'		 => $audit['id'],
						  'score'			 => $cal,
						  'appeals'			 => $strAppeals,
						  'total_opps'		 => $totalOpps['opps'],
						  'type'			 => $audit['type'],
						  'date_visit'		 => $audit['date_visit'],
						  'date_release'	 => $audit['date_release'],
						  'location_number'	 => $audit['location_number'],
						  'brand_prefix'	 => $audit['brand_prefix'],
						  'location_name'	 => $audit['location_name'],
						  'round_name'		 => $audit['round_name'],
						  'location_address' => $audit['location_address'],
						  'url_report'		 => getURLReport($audit['id'], $audit['report_layout_id'], $audit['country_language'])];

		$requestEmail = sendEmail($arrMailAppeal, 'appeal_process_refresh');

	}


	public function testAppealNotificationRefresh()
	{


		$id = 14; 
		$audit 		 = AuditsModel::getAuditList([], "id=$id")[0];
		$appeal 	 = $this->model->selectAppeal([], "audit_id=$id")[0];
		$appealItems = $this->model->selectAppealUpd($appeal['id']);

		$strAppeals = '<table width="100%" border="1">
                                <tr bgcolor="#E73712">
                                    <th>Oportunidad</th>
									<th>Decisión</th>
                                    <th>Apelación</th> 
                                    <th>Comentarios</th>
                                </tr>';

		foreach ($appealItems as $appeal) {

			if ($appeal['decision_result'] == 'Proceeds' OR $appeal['decision_result'] == 'Proceeds by exception'){
				
				$strAppeals .= '<tr>
									<td><b>'.$appeal['question_prefix'].' '.$appeal['eng'].'</b>
										<br><b> Auditor answer:</b> '.$appeal['auditor_answer'].'
										<br><b> Auditor comment</b> '.$appeal['auditor_comment'].'</td>
									<td>'.$appeal['decision_result'].'</td>
									<td>'.$appeal['author_comment'].'</td>
									<td>'.$appeal['owner_comment'].'
										<br>'.$appeal['decision_comment'].'
									</td>
								</tr>';
			}

		}
		$strAppeals .= '</table>';

		$countryMails      = getCountryEmails(['District Manager'], $audit['country_id']);
		$recipientsAppeals = emailFilter($countryMails);

		$totalOpps = countTotalOpps($audit['id']);
		$cal 	   = getScore($audit['id'])['Calificacion'];
		$asunto    = "Decisión final de apelación ({$audit['country_name']}) {$audit['brand_prefix']} #{$audit['location_number']}";

		$arrMailAppeal = ['asunto' 			 => $asunto,
						  'email' 			 => $recipientsAppeals,
						  'audit_id'		 => $audit['id'],
						  'score'			 => $cal,
						  'appeals'			 => $strAppeals,
						  'total_opps'		 => $totalOpps['opps'],
						  'type'			 => $audit['type'],
						  'date_visit'		 => $audit['date_visit'],
						  'date_release'	 => $audit['date_release'],
						  'location_number'	 => $audit['location_number'],
						  'brand_prefix'	 => $audit['brand_prefix'],
						  'location_name'	 => $audit['location_name'],
						  'round_name'		 => $audit['round_name'],
						  'location_address' => $audit['location_address'],
						  'url_report'		 => getURLReport($audit['id'], $audit['report_layout_id'])];

		//$requestEmail = sendEmail($arrMailAppeal, 'appeal_process_refresh');

	}




















	public function setAllAppealsDecisions() {
		if($_POST){
		
			$appTotStatus['total'] = 0;
			$status = NULL;
			$refreshScore = false;

			$appealIdInfo = $this->model->selectAppeal([], "id = ".$_POST['id_appeal_upd'])[0];
			if (isset( $_POST['urlFile'] )) { //Agregar imagenes por paso a apelaciones
				foreach($_POST['urlFile'] as $idOppFile => $itemFiles) {
					foreach($itemFiles as $urlFile) {
						//dep($urlFile);
						$insertAppealFileValues = ['audit_id' 	  => $appealIdInfo['audit_id'],
												   'reference_id' => $idOppFile,
												   'type' 		  => 'Appeal',
												   'name' 		  => 'Appeal file for '.$idOppFile,
												   'description'  => 'evidence',
												   'url' 		  => $urlFile
						];
						//dep($insertAppealFileValues);
						$audit_file_id = Audit_FileModel::insertAudit_File($insertAppealFileValues);
					}
				}
			}

			// Definir que roloes pueden dictar si procede o no
			if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 14,17,20] ) ) { // Regional OPS Dictamina
				$appTotStatus['Proceeds'] = 0;
				$status = "Completed";

				foreach($_POST['appealDes'] as $idItem => $appealDesItem){
					$appTotStatus['total'] ++;
					$appTotStatus[$appealDesItem] ++;
				}
				//$status = NULL;
				if( $appTotStatus['Proceeds'] > 0 ) $refreshScore = true;
			}

			//Guarda los estatus de cada punto penalizado
			foreach($_POST['appealDes'] as $idItem => $appealDesItem){
				if ( $appealDesItem == 'Approved' or $appealDesItem == 'Not approved') { //Para las decisiones de Master BP guarda en owner
					$updateAppealItemValues = [
						'decision_result' => $appealDesItem,
						'owner_comment' => $_POST['appealDesCom'][$idItem],
					];
				} else { //Para las decisiones de Regional guarda en desicion
					$updateAppealItemValues = [
						'decision_result' => $appealDesItem,
						'decision_comment' => $_POST['appealDesCom'][$idItem],
					];
				}

				$appealItem = $this->model->updateAppealItem($updateAppealItemValues, "id = ".$idItem);

				//Borrar en caso de proceeds, no debe borrar para mantener el registro
				if ( $appealDesItem == 'Proceeds') {
					$appealInfo = $this->model->selectAppealItem([], "id = ".$idItem);
					$appealOppInfo = $this->model->selectOppItem([], "id = ".$appealInfo['audit_opp_id']);
					//dep($appealOppInfo);
					$updateOppItemValues = [
						'audit_id' => ($appealIdInfo['audit_id']*-1),
						'appeal_status' => 0,
					];
					//dep($updateOppItemValues);
					$idOppUpd = $this->model->updateOpportunityAppealProc($updateOppItemValues, "id = ".$appealInfo['audit_opp_id']);

					$updatePointItemValues = [
						'audit_id' => ($appealIdInfo['audit_id']*-1),
					];
					//dep($updatePointItemValues);
					$idPointUpd = $this->model->updatePointAppealProc($updatePointItemValues, "id = ".$appealOppInfo['audit_point_id']);
				}
			}

			if( $refreshScore ) { // Recalcula si encuentra nuevos proceds
				
				$tmp = selectAudit(['scoring_id'], 'id ='. $appealIdInfo['audit_id']);
				$scoring_id = $tmp[0]['scoring_id'];
				$score = setScore($appealIdInfo['audit_id'], $scoring_id);
				
				$this->appealNotificationRefresh($appealIdInfo['audit_id']);
			}

			// Definir que roloes pueden dictar si procede o no
			if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 14,17,20] ) ) { // Regional OPS Dictamina
				$updateAppealValues = [
					'desicion_user_id' => $_SESSION['userData']['user_id'],
					'status' => $status,
					'date_completed' => date('Y-m-d'),
				];
			}

			$idAppeal = $this->model->updateAppeal($updateAppealValues, "id = ".$_POST['id_appeal_upd']);

			if($idAppeal > 0) {
				$arrResponse = array("status" => true, "msg" => "Data saved successfully");
			} else {
				$arrResponse = array("status" => false, "msg" => "It is not possible to store the data");
			}
		}

		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function getAppealItems() {
		//echo $_GET['idappeal'];
		//$dataAppeal = $this->model->selectAppeal([], "id=".$_GET['idappeal'])[0];
		$data = $this->model->selectAppealDetail($_GET['idappeal']);
		//dep($dataAppeal);
		$this->views->getView($this, "appealItems", $data);
	}

}
?>