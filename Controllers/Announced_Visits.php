<?php
require_once 'Models/AuditoriaModel.php';
require_once 'Models/AuditsModel.php';
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
require_once 'Models/UsuariosModel.php';

class Announced_Visits extends Controllers{

	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login']))
		{
			header('location: '.base_url().'/login');
		}
	}

	public function announced_visits()
	{
		require_once("Models/CountryModel.php");
		$objData = new CountryModel();
		$data['page_tag'] = "Visitas anunciadas";
		$data['page_title'] = "Visitas anunciadas";
		$data['page_name'] = "Visitas anunciadas";
        $data['page-functions_js'] = "functions_announced_visits.js";
		$data['page_content'] = "Announced visits";

		$data['franchises'] = $this->model->getFranchises([]);
		$data['paises'] = $objData->getCountry(['id','name'], "id IN (".$_SESSION['userData']['country_id'].")");

		$this->views->getView($this, "announced_visits", $data);
	}

	public function getVisitsb()
	{
		
		$data = AuditoriaModel::getAudit([], "status='Pending'");
		for($i=0; $i<count($data); $i++){
			$dataLocation = LocationModel::getLocation([], "id=".$data[$i]['location_id'])[0];
			$dataRound = RoundModel::getRound(['brand_id', 'country_id', 'name', 'type'], "id=".$data[$i]['round_id'])[0];
			$dataBrand = BrandModel::getBrand(['prefix'], "id=".$dataRound['brand_id'])[0];

			$data[$i]['visit'] = '	<span class="badge badge-info">'.$dataBrand['prefix'].'</span> <b class="text-success">#'.$dataLocation['number'].'
										<br>'.$dataLocation['name'].'</b><br>		
									<span style="font-size: 13px;"><b>'.$dataRound['name'].'</b></span>';

			$data[$i]['date'] = '	<span style="font-size: 14px;"><b>'.date("F j, Y, g:i a").' - Week '.date("W").'</b></span><br>
									<span style="font-size: 14px;"><b>'.$data[$i]['auditor_name'].'</b></span>';

			$btnAction = '<button class="btn btn-warning btnSendNotification" onClick="fntSendNotification('.$data[$i]['id'].')" title="Send notificaction">Send notification</button>';
			
			$data[$i]['action'] = '<div class="text-center">'.$btnAction.'</div>';
			
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function getVisits()
	{
		global $fnT;
		$obj = new UsuariosModel();

		$fnT = translate($_SESSION['userData']['default_language']);
		$filter = "";
		// $siguiente = date("W") +1;
		//$filter .= " and WEEKOFYEAR(al.announced_date) in (".(date("W") +1).") ";
		/*if ($_POST['f_franchise']!=NULL) {
			$filter .=" and lo.id in (".implode(', ', $_POST['f_franchise']).")";
		}else{
			$filter .=" and lo.id in (0)";
		}*/
		if ($_POST['f_country']!=NULL) {
			$filter .=" and lo.country_id in (".implode(', ', $_POST['f_country']).")";
		}else{
			$filter .=" and lo.country_id in (0)";
		}
		$filter .=" and al.announced_date BETWEEN '".$_POST['f_from']."' AND '".$_POST['f_to']."'";
		// if (isset($_POST['f_status']) ) {
		// 	if (count($_POST['f_status']) === 1) {
		// 		if ($_POST['f_status'][0] == 0) {
		// 			$filter .=" and t1.notification_date is not null ";
		// 		} else if ($_POST['f_status'][0] == 1) {
		// 			$filter .=" and t1.notification_date is null ";
		// 		}
		// 	}
		// }
		$data = $this->model->getAnnouncedVisitList($filter);
		//die($data);
		//dep($data);
		
		for($i=0; $i<count($data); $i++){
			
			$dataAud = AuditsModel::getAuditList([], "id=".$data[$i]['id_visit'])[0];
			$aud = AuditoriaModel::getAudit(['notification_date'], "id=".$data[$i]['id_visit'])[0];
			$country = CountryModel::getCountry([], "id=".$dataAud['country_id'])[0];
			//die(var_dump($dataAud));
			$data[$i]['id'] = $data[$i]['id_visit'];
			
			$data[$i]['visit'] = '<span class="badge badge-info">-</span> <b class="text-success">#'.$data[$i]['location_number'].'
									<br>'.$data[$i]['location_name'].'</b><br>	
									<b>'.$country['name'].'</b>	
									<span style="font-size: 13px;"><b>'.$data[$i]['round_name'].'</b><br></span>';

			$data[$i]['date'] = '	<span style="font-size: 14px;"><b><span class="strFecha'.$data[$i]['id_visit'].'">'.date("Y-m-d", strtotime( $data[$i]['announced_date'] )) .'</span> - '.$fnT("Semana").' '.$data[$i]['select_week'].'</b></span><br>';
			if(in_array($_SESSION['userData']['role']['id'], [1,2])){
				$data[$i]['date'] .= '<input type="time" hidden onchange="editHour(event, '.$data[$i]['id_visit'].')" id="hora'.$data[$i]['id_visit'].'">
									<label for="hora'.$data[$i]['id_visit'].'" onclick="document.getElementById(\'hora'.$data[$i]['id_visit'].'\').showPicker()" style="background-color: var(--color1); color:#fff; padding:5px; border-radius:6px; cursor: pointer;"><span style="font-size: 14px;">Hora: <b class="strHour'.$data[$i]['id_visit'].'">'.date("H:i", strtotime( $data[$i]['announced_date'] )).'</b><i class="fa fa-pencil" style="margin-left: 5px;"></i></span></label><br>
									<span style="font-size: 14px;">Auditor: <b>'.$data[$i]['auditor_name'].'</b></span>';
			}else{
				$data[$i]['date'] .= '
									<label><span style="font-size: 14px;">'.$fnT('Hora').': <b class="strHour">'.date("H:i", strtotime( $data[$i]['announced_date'] )).'</b></span></label><br>
									<span style="font-size: 14px;">Auditor: <b>'.$data[$i]['auditor_name'].'</b></span>';
			}
									
			$btnAction = '<button class="btn btn-warning btnSendNotification" onclick="fntSendNotification('.$data[$i]['id_visit'].', '.$data[$i]['location_id'].')" title="'.$fnT("Enviar notificação").'">'.$fnT("Enviar notificação").'</button><br>';

			
			
			$data[$i]['action'] = '<div class="text-center">'.$btnAction.'</div>';

			if($aud['notification_date']!=NULL){
				$dataLog = Audit_LogModel::getLastAudit_Log(['user_id'], "audit_id=".$data[$i]['id_visit']." AND name ='Visit announced'");
				$autor = $obj->selectUsuario(intval($dataLog['user_id']));
				$data[$i]['send'] = '<div class="contSendLbl"><p>'.$aud['notification_date'].'</p><b>By: '.$autor['name'].'</b></div>';
			}else{
				$data[$i]['send'] = '<div class="contSendLbl"><p>'.$fnT('Ainda não enviado').'</p></div>';
			}
			
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function updateHour(){
		$id = $_POST['id'];
		$newHour = $_POST['hour'];
		$args = array(
			"announced_date"=>$newHour
		);
		$rs = $this->model->updateAudit($args, " id = ".$id);
		if($rs){
			echo 'ok';
		}else{
			echo 'error';
		}
	}

	public function sendNotification()
	{	
		global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']);
		$week = date("W");
		$week ++;
		$year = date("Y");

		$fechas = getFirstLastDayWeek($year, $week);
		$inicio = date('m/d/Y', strtotime('last monday', strtotime(date("d-m-Y")."+ 7 days")));
		$fin = date('m/d/Y', strtotime('next sunday', strtotime(date("d-m-Y")."+ 7 days")));

		$dataAud = AuditoriaModel::getAudit([], "id=".$_GET['id'])[0];
		$dataLocation = LocationModel::getLocation([], "id=".$_GET['locationId'])[0];
		$areaFranchiseLead = UsuariosModel::getRolAsociado(14, $_GET['locationId']);
		$marketLeader = UsuariosModel::getRolAsociado(19, $_GET['locationId']);
		$regionalManger = UsuariosModel::getRolAsociado(17, $_GET['locationId']);
		$zoneDirector = UsuariosModel::getRolAsociado(20, $_GET['locationId']);
		$franVP = UsuariosModel::getRolAsociado(23, $_GET['locationId']);
		$superUser = UsuariosModel::getRolAsociado(30, $_GET['locationId']);
		$cc = (in_array($dataLocation['country_id'], [23,29])?'GFreih@texaschicken.com':'');
		$to="";
		if($dataLocation['country_id']==1){ //Para mexico tienda, areafranchiselead, marketleader, zonedirector, franchise vise president, superuser
			$to = $dataLocation['email'].(($marketLeader!=NULL && $marketLeader!="")?','.$marketLeader:'').(($areaFranchiseLead!=NULL && $areaFranchiseLead!="")?','.$areaFranchiseLead:'').(($zoneDirector!=NULL && $zoneDirector!="")?','.$zoneDirector:'').(($franVP!=NULL && $franVP!="")?','.$franVP:'').(($superUser!=NULL && $superUser!="")?','.$superUser:'');
		}else{ //para los demas paises tienda, reginal manager, areafranchiselead, marketleader, zonedirector, franchise vise president, superuser
			$to = $dataLocation['email'].(($marketLeader!=NULL && $marketLeader!="")?','.$marketLeader:'').(($regionalManger!=NULL && $regionalManger!="")?','.$regionalManger:'').(($areaFranchiseLead!=NULL && $areaFranchiseLead!="")?','.$areaFranchiseLead:'').(($zoneDirector!=NULL && $zoneDirector!="")?','.$zoneDirector:'').(($franVP!=NULL && $franVP!="")?','.$franVP:'').(($superUser!=NULL && $superUser!="")?','.$superUser:'');
		}
		
		if(in_array($dataLocation['country_id'], [1])){
			$asunto = 'Visita anunciada';
		}else{
			$asunto = 'Announced visit';
		}
		
		$data = ['asunto' => $asunto, 
				 'email' => $to, 
				 'cc' => $cc,
				 'bcc' => 'mosorio@bw-globalsolutions.com,cordonez@bw-globalsolutions.com,alopez@arguilea.com,mmaximiliano@arguilea.com', 
				 'tienda_name' => $dataLocation['name'],
				 'tienda_number' => $dataLocation['number'],
				 'fecha' => explode(" ", $dataAud["announced_date"])[0],
				 'hora' => explode(" ", $dataAud["announced_date"])[1],
				 'country' => $dataLocation['country_id'],
				 'inicio' => $inicio,
				 'fin' => $fin];
		if(in_array($dataLocation['country_id'], [1])){
			$request_mail = sendAnnouncedVisit($data, 'announced_visit'); //En español
		}else{
			$request_mail = sendAnnouncedVisit($data, 'announced_visit_eng'); //En ingles
		}
		if($request_mail > 0) {
			$updateAuditValues = [
				'notification_date' => date('Y-m-d H:i:s')
			];
			AuditoriaModel::updateAudit($updateAuditValues, "id=".$_GET['id']);
			$details = array("location_id"=>$_GET['locationId']);
			$logs = [
				'audit_id' => $_GET['id'],
				'user_id' => $_SESSION['userData']['id'],
				'category' => 'Web',
				'name' => 'Visit announced',
				'details' => json_encode($details,JSON_UNESCAPED_UNICODE),
				'date' => date('Y-m-d H:i:s'),
			];
			Audit_LogModel::insertAudit_Log($logs);
			$arrResponse = array("status" => true, "msg" => "Notificacion enviada.");	
		} else{
			$arrResponse = array("status" => false, "msg" => "Error al enviar correo.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function sendNotificationGenerel()
	{	

		global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']);
		$week = date("W");
		$week ++;
		$filter .= " and WEEKOFYEAR(t1.announced_date) = ".$week." ";
		$year = date("Y");

		$fechas = getFirstLastDayWeek($year, $week);
		$inicio = date("Y-m-d", $fechas->{'first_day'}->{'date'} ); 
		$fin = date("Y-m-d", strtotime( $fechas->{'last_day'}->{'date'} )); 
		$dataVisits = $this->model->getAnnouncedVisitList($filter);
		
		die();
		$visitas = '';
		for($i=0; $i<count($dataVisits); $i++){
			if ($dataVisits[$i]['country_prefix'] == 'AE'){
				$visitas .= '<li>#'.$dataVisits[$i]['location_number'].' - '.$dataVisits[$i]['location_name'].'</li>';
			}
			
		}
		$data = ['email' => 'mosorio@bw-globalsolutions.com',
				'tiendas' => $visitas,
				'inicio' => '03/27/2023',
				'fin' => '04/02/2023'];
		if($request_mail > 0) {
			$arrResponse = array("status" => true, "msg" => "Notificacion enviada.");	
		} else{
			$arrResponse = array("status" => false, "msg" => "Error al enviar correo.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function sendNotificationGlobal()
	{	
		//dep($_POST);
		// die();
		global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']);

		$inicio = date('m/d/Y', strtotime('last monday', strtotime(date("d-m-Y")."+ 7 days")));
		$fin = date('m/d/Y', strtotime('next sunday', strtotime(date("d-m-Y")."+ 7 days")));

		if (isset($_POST['countrys']) ) {
			//$filter = " and WEEKOFYEAR(al.announced_date) in (".(date("W") +1).") ";
			$strVisitas = '';
			//$filter = " and WEEKOFYEAR(al.announced_date) in (".(date("W") +1).") ";
			$filter = " and lo.country_id in (".$_POST['countrys'].")";
			$filter .=" and al.announced_date BETWEEN '".$_POST['from']."' AND '".$_POST['to']."'";
			$dataAnnounced = $this->model->getAnnouncedVisitList($filter);
			//die(var_dump($dataAnnounced));
			//die($dataAnnounced);
			if (count($dataAnnounced)) {
				$to="";
				$tiendas = array();
				foreach($dataAnnounced as $visit) {
					$dataLocation = LocationModel::getLocation([], "id=".$visit['location_id'])[0];
					$marketLeader = UsuariosModel::getRolAsociado(19, $visit['location_id']);
					$regionalManger = UsuariosModel::getRolAsociado(17, $visit['location_id']);
					
					$to .= $dataLocation['email'].(($marketLeader!=NULL && $marketLeader!="")?','.$marketLeader:'').(($regionalManger!=NULL && $regionalManger!="")?','.$regionalManger:'').',';
					array_push($tiendas, array(
						"number" => $dataLocation['number'],
						"name" => $dataLocation['name'],
						"fecha" => explode(" ", $visit["announced_date"])[0],
				 		'hora' => explode(" ", $visit["announced_date"])[1],
					));
					$strVisitas .= '<li>#'.$dataLocation['number'].' - '.$dataLocation['country'].' - '.$dataLocation['state_name'].' - '.$dataLocation['city'].' - <b>'.date("Y-m-d", strtotime( $visit['announced_date'] )).'</b></li>';
					// $ids_announced .= ",".$visit['id'];
					$updateAuditValues = [
						'notification_date' => date('Y-m-d')
					];
					AuditoriaModel::updateAudit($updateAuditValues, "id=".$visit['id_visit']);
					$details = array("location_id"=>$visit['location_id']);
					$logs = [
						'audit_id' => $visit['id_visit'],
						'user_id' => $_SESSION['userData']['id'],
						'category' => 'Web',
						'name' => 'Visit announced',
						'details' => json_encode($details,JSON_UNESCAPED_UNICODE),
						'date' => date('Y-m-d H:i:s'),
					];
					Audit_LogModel::insertAudit_Log($logs);
				}
				$to = substr($to, 0, -1);
				//die($to);
				$asunto = 'Announced visit';
				$cc = (in_array(explode(",", $_POST['countrys']), [23,29])?'GFreih@texaschicken.com':'');
				$data = ['asunto' => $asunto,  
					 'email' => $to, 
					 'cc' => $cc, 
					 'bcc' => 'mosorio@bw-globalsolutions.com,cordonez@bw-globalsolutions.com,alopez@arguilea.com,mmaximiliano@arguilea.com', 
					 'tiendas' => $tiendas,
					 'inicio' => $inicio,
					 'fin' => $fin];
				//dep($data);
				$request_mail = sendEmailMasive($data, "announced_visit");
			}
			if($request_mail > 0) {
				$arrResponse = array("status" => true, "msg" => "Notificacion enviada.");	
			} else{
				$arrResponse = array("status" => false, "msg" => "Error al enviar correo.");
			}
		} else {
			$arrResponse = array("status" => false, "msg" => "Error, seleecione al menos una franquicia.");
		}

		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();


		// $filter = "";
		// $siguiente = date("W") +1;
		// $filter .= " and WEEKOFYEAR(al.announced_date) in (".(date("W") +1).") ";
		// if (isset($_POST['f_franchise']) ) {
		// 	// $f_country = implode(', ', $_POST['f_franchise']);
		// 	$filter .=" and lo.franchise_id in (".implode(', ', $_POST['f_franchise']).")";
		// }

		$data = $this->model->getAnnouncedVisitList($filter);
		
		for($i=0; $i<count($data); $i++){
			
			$data[$i]['visit'] = '<span class="badge badge-info">-</span> <b class="text-success">#'.$data[$i]['location_number'].'
									<br>'.$data[$i]['location_name'].'</b><br>		
									<span style="font-size: 13px;"><b>'.$data[$i]['round_name'].'</b><br></span>';

			$data[$i]['date'] = '	<span style="font-size: 14px;"><b>'.date("Y-m-d", strtotime( $data[$i]['announced_date'] )) .' - '.$fnT("Semana").' '.$data[$i]['select_week'].'</b></span><br>
									<span style="font-size: 14px;">Auditor: <b>'.$data[$i]['auditor_name'].'</b></span>';
			$btnAction = '<button class="btn btn-warning btnSendNotification" disabled title="'.$fnT("Enviar notificação").'">'.$fnT("Enviar notificação").'</button><br>';

			
			
			$data[$i]['action'] = '<div class="text-center">'.$btnAction.'</div>';
			
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		die();


		global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']);
		$week = date("W");
		$week ++;
		$filter .= " and WEEKOFYEAR(t1.announced_date) = ".$week." ";
		$year = date("Y");

		$dataVisits = $this->model->getAnnouncedVisitList($filter);
		$aVisits = array();
		foreach($dataVisits as $v) {
			$aVisits[$v['country_prefix']] = array();
		}

		foreach($dataVisits as $v) {
			array_push($aVisits[$v['country_prefix']], $v);
		}

		

		foreach($aVisits as $country => $visitas) {
			$strVisitas = '';
			
			$template = 'announced_visit_eng';
			$cc = 'mcastillo@arguilea.com, kdejesus@bw-globalsolutions.com, schirino@bw-globalsolutions.com, test@test.com';
			if ( in_array($country, ['IND'])){
				$asunto = 'Visita anunciada - '.$country; 
				if ($country == 'IND'){
					$to = 'test@test.com, test@test.com, test@test.com, test@test.com, test@test.com, test@test.com';
					$asunto = 'Announced visit - Indonesia';
				} else if ($country == 'UK'){
					$to = 'test@test.com, test@test.com, test@test.com, test@test.com, test@test.com';
					$asunto = 'Announced visit - United Kingdom';
				} else if ($country == 'FR'){
					$to = 'test@test.com, test@test.com, test@test.com';
					$asunto = 'Announced visit - France';
				} else if ($country == 'AE'){
					$to = 'test@test.com, test@test.com, test@test.com, test@test.com, test@test.com';
					$asunto = 'Announced visit - United Arab Emirates';
				} else if ($country == 'SGP'){
					$to = 'test@test.com,test@test.com, test@test.com, test@test.com, test@test.com, test@test.com, test@test.com';
					$asunto = 'Announced visit - Singapore';
				} else if ($country == 'CA'){
					$to = 'test@test.com, test@test.com';
					$asunto = 'Announced visit - Canada';
				} else {
					$to = 'm.angel.osorio.p@gmail.com';
					$cc = 'Nadie';
				}
				for($i=0; $i<count($visitas); $i++){
					$strVisitas .= '<li>#'.$visitas[$i]['location_number'].' - '.$visitas[$i]['location_name'].'</li>';
				}


				if ($_GET['test'] == 1) {
					$to = 'kdejesus@bw-globalsolutions.com, mcastillo@arguilea.com, test@test.com';
					$cc = '';
				}

				$data = ['asunto' => $asunto,  
						 'email' => $to, 
						 'cc' => $cc, 
						 'bcc' => 'mosorio@bw-globalsolutions.com', 
						 'tiendas' => $strVisitas,
						 'inicio' => $inicio,
						 'fin' => $fin];
				//$request_mail = sendAnnouncedVisit($data, $template);
			}
		}

		if($request_mail > 0) {
			$arrResponse = array("status" => true, "msg" => "Notificacion enviada.");	
		} else{
			$arrResponse = array("status" => false, "msg" => "Error al enviar correo.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
		if($request_mail > 0) {
			$arrResponse = array("status" => true, "msg" => "Notificacion enviada.");	
		} else{
			$arrResponse = array("status" => false, "msg" => "Error al enviar correo.");
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function downloadAnnounceds(){

		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Content-type: application/x-msexcel; charset=utf-8");
		header("Content-Disposition: attachment; filename=announcedVisits-".date("YmdHis").".xls"); 
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);

		$obj = new UsuariosModel();

		$visits = [];
		if (isset($_GET['countrys']) ) {
			$filter = " and lo.country_id in (".$_GET['countrys'].")";
			$filter .=" and al.announced_date BETWEEN '".$_GET['from']."' AND '".$_GET['to']."'";
			$data = $this->model->getAnnouncedVisitList($filter);

			for($i=0; $i<count($data); $i++){
			
				$dataAud = AuditoriaModel::getAudit([], "id=".$data[$i]['id_visit'])[0];
				$visits[$i]['country'] = $data[$i]['country_name'];
				$visits[$i]['numTienda'] = $data[$i]['location_number'];
				$visits[$i]['nomTienda'] = $data[$i]['location_name'];
				$visits[$i]['estatus'] = ($dataAud['notification_date']!=NULL?'announced':'unannounced');
				$autor="";
				if($dataAud['notification_date']!=NULL){
					$dataLog = Audit_LogModel::getLastAudit_Log(['user_id'], "audit_id=".$data[$i]['id_visit']." AND name ='Visit announced'");
					$autor = $obj->selectUsuario(intval($dataLog['user_id']))['name'];
				}
				$visits[$i]['sendBy'] = $autor;
				$visits[$i]['fechaEnvio'] = $dataAud['notification_date'];
				$visits[$i]['fechaAud'] = explode(' ', $data[$i]['announced_date'])[0];
				$visits[$i]['hora'] = explode(' ', $data[$i]['announced_date'])[1];
			}
		}
		
	?>
	<table border="1" cellspacing="0" cellpadding="0" style="font-size:12px;">
	<tr style="background:#eab54c; color:#fff; text-align:center;">
		<td>Pais</td>
		<td><?=utf8_decode('Número de tienda')?></td>
		<td>Nombre de tienda</td>
		<td>Estatus</td>
		<td>Enviado por</td>
		<td>Fecha de envio</td>
		<td>Fecha de auditoria</td>
		<td>Hora de auditoria</td>
	</tr>
	<?php
		foreach($visits as $visit){ ?>
	<tr>
		<td><?=utf8_decode($visit['country'])?></td>
		<td><?=utf8_decode($visit['numTienda'])?></td>
		<td><?=utf8_decode($visit['nomTienda'])?></td>
		<td><?=utf8_decode($visit['estatus'])?></td>
		<td><?=utf8_decode($visit['sendBy'])?></td>
		<td><?=utf8_decode($visit['fechaEnvio'])?></td>
		<td><?=utf8_decode($visit['fechaAud'])?></td>
		<td><?=utf8_decode($visit['hora'])?></td>
		
	</tr>
	<?php }?>
	</table>
	<?php
	}

	public function test()
	{
		echo "Funcion prueba";
	}
}
?>