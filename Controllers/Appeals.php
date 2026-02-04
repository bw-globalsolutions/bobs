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
		$data['page_tag'] = "Apelação";
		$data['page_title'] = "Apelação";
		$data['page_name'] = "Apelação";
		$data['page-functions_js'] = "functions_appeals.js";

		$data['rounds'] = $this->model->listRounds();	
		$data['status'] = ['Pending', 'Completed'];
		$data['stores'] = $this->model->listStores();
		
		$this->views->getView($this, "appeals", $data);
	}

	public function getAudits()
	{
		
		$fil = "status='Completed' AND type IN('Standard') AND DATEDIFF(NOW(), date_visit_end) <= 7 AND id NOT IN (SELECT audit_id FROM appeal)";

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

					$disabled = "disabled";
					if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 14, 19, 20, 21] ) and $values['status'] == 'Pending' ) {
						$disabled = "";
					} else if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 14, 19, 20, 21] ) and $values['status'] == 'In review' ) {
						$disabled = "";
					}

					if( $values['status'] == 'Pending' and $values['diaspending'] > 4 ) { // Bloquear despues de dos dias pendiente
						$values['status'] = "Closed";
						$updateAppealValues = [
							'status' => $values['status'],
						];
						$idAppeal = $this->model->updateAppeal($updateAppealValues, "id = ".$values['id']);
					}

					$datas['clarifications'] = '';
					$border = "#FF8B40";
					$badgeDes = "success";
					foreach ($values['items'] as $item) {
						$files = '';
						$filesAppeals = '';
						$dataFiles = ActionPlanModel::getFilesOpp($item['audit_opp_id']);
						//dep($dataFiles);
						foreach ($dataFiles as $key) {

	$imgBlock = '
		<div class="mr-3 mb-3 cr-pointer" style="width: 120px; height: 120px;" title="'.htmlspecialchars($key['name']).'">
			<div class="rounded shadow-sm border overflow-hidden h-100 w-100">
				<img class="w-100 h-100 of-cover"
					style="object-fit: cover;"
					src="'.$key['url'].'"
					onclick="openImage(this, \''.addslashes($key['name']).'\', \''.$key['type'].'\', '.$key['reference_id'].')">
			</div>
		</div>';

	if ($key['type'] == "Opportunity") {
		$files .= $imgBlock;
	}

	if ($key['type'] == "Appeal") {
		$filesAppeals .= $imgBlock;
	}

						}

						if ( in_array($item['decision_result'], ['Proceeds by exception','Proceeds by criterion','Approved']) ) {
							$border = "#0EA50A";
							$badgeDes = "success";
						} else if ( in_array($item['decision_result'], ['Not proceeds','Not approved']) ) {
							$border = "#FA0808";
							$badgeDes = "danger";
						} else {
							$border = "#F1E307";
							$badgeDes = "warning";
						}
						if($item['decision_result']=='Proceeds by exception'){
							$desicion = 'Processos por exceção';
						}else if($item['decision_result']=='Proceeds by criterion'){
							$desicion = 'Processos por critério';
						}else if($item['decision_result']=='Approved'){
							$desicion = 'Aprovada';
						}else if($item['decision_result']=='Not proceeds'){
							$desicion = 'Não são rendimentos';
						}else if($item['decision_result']=='Not approved'){
							$desicion = 'Não aprovado';
						}

						$datas['clarifications'] .= 
							'<div class="mb-2" style="border-left: 8px solid '.$border.'; padding: 5px; border-top: 1px solid #CCC; border-right: 1px solid #CCC; border-bottom: 1px solid #CCC;">
								<div class="bg-info p-3 text-center text-white">'.$fnT("Oportunidade").'</div>
								<b><span class="badge badge-secondary">'.$item['question_prefix'].'</span> '.$item['eng'].'</b>
								<br><b><span class="text-primary"><i class="fa fa-comments"></i>  '.$item['auditor_comment'].'</span></b>
								<br><div class="d-flex flex-wrap">'.$files.'</div>
								<div class="bg-info p-3 text-center text-white"><i class="fa fa-exclamation-triangle"></i> '.$fnT("Apelação").'</div>
								<div class="text-center">
									<h3><b><span class="badge badge-'.$badgeDes.'">'.$desicion.'</span></b></h3>
									<br><b style="color:red !important;">GM '.$fnT("Comentário").'</b>
									<br><b style="color:red !important;">'.$item['author_comment'].'</b>
									<hr class="border border-primary">
									<b style="color:#F18E07 !important;">Comentário da decisão</b>
									<br><b style="color:#F18E07 !important;">'.$item['decision_comment'].'</b>
									<hr class="border border-primary">
									<div class="d-flex flex-wrap">'.$filesAppeals.'</div>
								</div>
							</div>';

						}
						if($values['gralInfo']['type']=="Standard"){
							$tipo = "Padrão";
						}else if($values['gralInfo']['type']=="Calibration-Audit"){
							$tipo = "Auditoria de Calibração";
						}else if($values['gralInfo']['type']=="Training-visits"){
							$tipo = "visitas de treinamento";
						}else if($values['gralInfo']['type']=="Self-Evaluation"){
							$tipo = "Autoavaliação";
						}
						//dep ($dataAppeals);
						//dep ($values);
						//$datas['id'] = 'AP'.$values['id'].'-AU'.$values['audit_id'];
						$datas['id'] = $values['id'];
						$datas['store'] = '<b>'.$values['location']['number'].' - '.$values['location']['name'].'</b>
											<br><b>'.("Ciclo".explode('Round', $values['gralInfo']['round_name'])[1]).'</b>
											<br><b>'.$fnT("Região").': '.$values['gralInfo']['region'].'</b>
											<br><b>'.$fnT("Data da visita").': '.date("Y-m-d", strtotime( $values['gralInfo']['date_visit'] )).'</b>
											<br><b>'.$fnT("Tipo de auditoria").': '.$tipo.'</b>
											<br><b><a href="'.getURLReport($values['audit_id'], $values['gralInfo']['report_layout_id']).'" target="_blank">'.$fnT("Ver relatório").'</a>';
						//$datas['clarifications'] = '<div class="mb-2" style="border-left: 5px solid #28A745; padding: 5px; border-top: 1px solid #CCC; border-right: 1px solid #CCC; border-bottom: 1px solid #CCC;">'.$values['items'][0]['auditor_comment'].'<br>'.$values['items'][0]['author_comment'].'</div>';
						$datas['date'] = date("Y-m-d", strtotime( $values['date_start'] ));
						// $fechaAppeal= new DateTime(date("Y-m-d", strtotime( $values['date_start'] )));
						// $fechaHoy= new DateTime(date("Y-m-d"));
						// $diffPending = $fechaAppeal->diffInDays($fechaHoy);
						// echo $diffPending;
						$strUsers = "";
		
						if( in_array( $_SESSION['userData']['role']['id'], [1, 2] )) {
							$strUsers = '<br><b>'.$fnT("Autor").': <i class="fa fa-user"></i>  '.$values['author'].'</b>
										<br><b>'.$fnT("MBP").': <i class="fa fa-user"></i>  '.$values['owner'].'</b>
										<br><b>'.$fnT("RBD").': <i class="fa fa-user"></i>  '.$values['user_desicion'].'</b>';
						} else if( in_array( $_SESSION['userData']['role']['id'], [10] )) {
							$strUsers = '<br><b>'.$fnT("Autor").': <i class="fa fa-user"></i>  '.$values['author'].'</b>';
						} else if( in_array( $_SESSION['userData']['role']['id'], [14, 19, 20, 21] )) {
							$strUsers = '<br><b>'.$fnT("MBP").': <i class="fa fa-user"></i>  '.$values['owner'].'</b>';
						} else if( in_array( $_SESSION['userData']['role']['id'], [14, 19, 20, 21] )) {
							$strUsers = '<br><b>'.$fnT("RBD").': <i class="fa fa-user"></i>  '.$values['user_desicion'].'</b>';
						}
						if($values['status']=="Completed"){
							$estado = "Completo";
						}else if($values['status']=="In Process"){
							$estado = "Em andamento";
						}else if($values['status']=="Pending"){
							$estado = "Pendente";
						}
		
						$datas['options'] = 
							'<b><span class="badge badge-info">'.$estado.'</span></b>
							<br><b><i class="fa fa-calendar"></i> '.$fnT("Data de início").' '.date("Y-m-d", strtotime( $values['date_start'] )).'</b>'.$strUsers.'
							
							<br><button class="btn btn-warning btnViewDetails" '.$disabled.' onclick="openModalUpd('.$values['id'].')" title="'.$fnT("Detalhes").'">'.$fnT("Detalhes").'</button><br>';
						//dep ($datas); <br><b><i class="fa fa-calendar"></i> '.$fnT("Data de término").' '.date("Y-m-d", strtotime( $values['date_completed'] )).'</b>
						array_push($dataAppeals,$datas);
						//dep ($dataAppeals);
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
								<label for="action" class="control-label">'.$fnT("Apelação").'</label>
								<textarea class="form-control" name="appeal['.$data[$i]['id_audit_opp'].']" id="appeal'.$data[$i]['id_audit_opp'].'" cols="40" rows="3" style="resize: both;"></textarea>
								<span class="btn btn-info btn-sm my-2 input-in-btn">
									<i class="fa fa-camera"></i>'.$fnT("Evidência").'
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
							<br><b><span class="text-danger"><i class="fa fa-exclamation-triangle"></i> '.$fnT("Apelação").': '.$data[$i]['author_comment'].'</span></b>
							<br><div class="d-flex flex-wrap">'.$filesApp.'</div>';
			$data[$i]['decision'] = 
				'<div>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text border-0">'.$fnT('Decisão').'</span>
						</div>
						<select class="form-control" id="appealDes['.$data[$i]['id_appeal_item'].']" name="appealDes['.$data[$i]['id_appeal_item'].']" required>';
			
			// Definir que roloes pueden dictar si procede o no
			if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 14, 19, 20, 21] ) ) {
				$data[$i]['decision'] .='
							<option value="" '.($data[$i]['decision_result']=='Pending'?'selected':'').'></option>
							<option value="Proceeds by exception" '.(in_array($data[$i]['decision_result'], ['Proceeds', 'Proceeds by exception'])?'selected':'').'>'.$fnT('Procede por exceção').'</option>
							<option value="Proceeds by exception" '.(in_array($data[$i]['decision_result'], ['Proceeds by criterion'])?'selected':'').'>'.$fnT('Procede por critério').'</option>
							<option value="Not proceeds" '.($data[$i]['decision_result']=='Not proceeds'?'selected':'').'>'.$fnT('Não procede').'</option>';
			}

			
			$data[$i]['decision'] .= '</select>
					</div><br>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text border-0">'.$fnT('Comentários').'</span>
						</div>
						<textarea class="form-control" id="appealDesCom['.$data[$i]['id_appeal_item'].']" name="appealDesCom['.$data[$i]['id_appeal_item'].']" cols="40" rows="3" style="resize: both;" required></textarea>
					</div>
					<span class="btn btn-info btn-sm my-2 input-in-btn">
						<i class="fa fa-camera"></i>'.$fnT("Evidência").'
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
			$previusScore = $this->model->previusScore($_POST['modal_audit_id'])[0]['value_4'];
			if ($isAppeal['id']) {
				$idAppeal = $isAppeal['id'];
			}else {
				$insertAppealValues = [
					'audit_id' => $_POST['modal_audit_id'],
					'author_user_id' => $_SESSION['userData']['user_id'],
					'status' => 'In Process',
					'date_start' => date('Y-m-d'),
					'score_previus' => $previusScore,
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
			$previusScore = $this->model->previusScore($_POST['idAuditDT'])[0]['value_4'];

			$insertAppealValues = [
				'audit_id' => $_POST['idAuditDT'],
				'author_user_id' => $_SESSION['userData']['user_id'],
				'status' => 'Pending',
				'location_id' => $tmp['location_id'],
				'country_id' => $tmp['country_id'],
				'date_start' => date('Y-m-d'),
				'score_previus' => $previusScore,
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

	public function appealNotification($id)
	{
		require_once 'Models/UsuariosModel.php';
		$audit       = AuditsModel::getAuditList([], "id=$id")[0];
		$appeal      = $this->model->selectAppeal([], "audit_id=$id")[0];
		$appealItems = $this->model->selectAppealUpd($appeal['id']);

		$strAppeals = '<table width="100%" border="1">
                                <tr bgcolor="#E73712">
                                    <th>Oportunidad</th>
                                    <th>Apelación</th>
                                    <th>Comentarios</th>
                                </tr>';

		foreach ($appealItems as $appeal) {
			$strAppeals .= '<tr>
								<td>
									<b>'.$appeal['question_prefix'].' '.$appeal['eng'].'</b>
									<br><b> Respuesta del auditor:</b> '.$appeal['auditor_answer'].'
									<br><b> Comentario del auditor:</b> '.$appeal['auditor_comment'].'
								</td>
								<td>'.$appeal['author_comment'].'</td>
								<td>'.$appeal['owner_comment'].'</td>
							</tr>';
		}
		$strAppeals .= '</table>';

        $countryMails = getCountryEmails(['District Manager'], $audit['country_id']);
		$recipientsAppeals = emailFilter($countryMails);
		$esPrueba = false;
        if(in_array($audit['type'],['Calibration Audit'])) $esPrueba=true;
		$to = UsuariosModel::getTo(8, $audit['location_id'], $esPrueba, $audit['country_id']);

		$totalOpps = countTotalOpps($audit['id']);
		$cal = getScore($audit['id'])['value_4'];
		$asunto = "Appeals under review ({$audit['country_name']}) {$audit['brand_prefix']} #{$audit['location_number']}";

		$arrMailAppeal = ['asunto' 			 => $asunto,
						  'email' 			 => $to,
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
						  'country'          => $audit['country_id'],
						  'url_report'		 => getURLReport($audit['id'], $audit['report_layout_id'], ($audit['country_id']==1?'esp':'eng'))];

		$requestEmail = sendEmail($arrMailAppeal, 'appeal_process_mbp');
	}

	public function appealNotificationRefresh($id)
	{
		require_once 'Models/UsuariosModel.php';
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

			if ($appeal['decision_result'] == 'Proceeds' || $appeal['decision_result'] == 'Proceeds by criterion' OR $appeal['decision_result'] == 'Proceeds by exception'){
				
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
		$esPrueba = false;
        if(in_array($audit['type'],['Calibration Audit'])) $esPrueba=true;
		$to = UsuariosModel::getTo(9, $audit['location_id'], $esPrueba, $audit['country_id']);

		$totalOpps = countTotalOpps($audit['id']);
		$cal 	   = getScore($audit['id'])['value_4'];
		$asunto    = "Final appeal decision ({$audit['country_name']}) {$audit['brand_prefix']} #{$audit['location_number']}";

		$arrMailAppeal = ['asunto' 			 => $asunto,
						  'email' 			 => $to,
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
						  'country'          => $audit['country_id'],
						  'url_report'		 => getURLReport($audit['id'], $audit['report_layout_id'], ($audit['country_id']==1?'esp':'eng'))];

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

			if ($appeal['decision_result'] == 'Proceeds' || $appeal['decision_result'] == 'Proceeds by criterion' OR $appeal['decision_result'] == 'Proceeds by exception'){
				
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
		$asunto    = "Final appeal decision ({$audit['country_name']}) {$audit['brand_prefix']} #{$audit['location_number']}";

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
						  'country'          => $audit['country_id'],
						  'url_report'		 => getURLReport($audit['id'], $audit['report_layout_id'], ($audit['country_id']==1?'esp':'eng'))];

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
			if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 14, 19, 20, 21] ) ) { // Regional OPS Dictamina
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
						'decision_comment' => $_POST['appealDesCom'][$idItem], //se cambio 'owner_comment' a 'decision_comment',
					];
				} else { //Para las decisiones de Regional guarda en desicion
					$updateAppealItemValues = [
						'decision_result' => $appealDesItem,
						'decision_comment' => $_POST['appealDesCom'][$idItem],
					];
				}

				$appealItem = $this->model->updateAppealItem($updateAppealItemValues, "id = ".$idItem);

				//Borrar en caso de proceeds, no debe borrar para mantener el registro
				if ( $appealDesItem == 'Proceeds' || $appealDesItem == 'Proceeds by criterion' || $appealDesItem == 'Proceeds by exception') {
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
			if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 14, 19, 20, 21] ) ) { // Regional OPS Dictamina
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