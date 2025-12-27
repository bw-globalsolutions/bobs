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
		$data['page_tag'] = "Announced visits";
		$data['page_title'] = "Announced visits";
		$data['page_name'] = "announced_Visits";
        $data['page-functions_js'] = "functions_announced_visits.js";
		$data['page_content'] = "Announced visits";

		$data['franchises'] = $this->model->getFranchises([],'id IN(SELECT franchise_id FROM location a 
INNER JOIN country b ON a.country_id = b.id 
INNER JOIN audit c ON c.location_id = a.id
WHERE country NOT IN("Mexico") AND c.announced_date IS NOT NULL AND country_id IN(2,19,13,4,21,18))');

		$this->views->getView($this, "announced_visits", $data);
	}

	public function getVisitsb()
	{
		
		$data = AuditoriaModel::getAudit([], "status='Pending' ");
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
		$fnT = translate($_SESSION['userData']['default_language']);
		$filter = "";
		// $siguiente = date("W") +1;
		$filter .= " and WEEKOFYEAR(al.announced_date) in (".(date("W") +1).") AND type = 'Training-visits' AND country NOT IN ('Mexico') AND IFNULL(al.announced_mail, 0) = 0";
		if (isset($_POST['f_franchise']) ) {
			$filter .=" and lo.franchise_id in (".implode(', ', $_POST['f_franchise']).") ";
		}
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
		//dep($data);
		
		for($i=0; $i<count($data); $i++){
			
			$data[$i]['visit'] = '<span class="badge badge-info">-</span> <b class="text-success">#'.$data[$i]['location_number'].'
									<br>'.$data[$i]['country'].'<br> '.$data[$i]['location_name'].'</b><br>		
									<span style="font-size: 13px;"><b>'.$data[$i]['round_name'].'</b><br></span>';

			$data[$i]['date'] = '	<span style="font-size: 14px;"><b>'.date("Y-m-d", strtotime( $data[$i]['announced_date'] )) .' - '.$fnT("Week").' '.$data[$i]['select_week'].'</b></span><br>
									<span style="font-size: 14px;">Auditor: <b>'.$data[$i]['auditor_name'].'</b></span>';
			$btnAction = '<button class="btn btn-warning btnSendNotification" disabled title="'.$fnT("Send notification").'">'.$fnT("Send notification").'</button><br>';

			
			
			$data[$i]['action'] = '<div class="text-center">'.$btnAction.'</div>';
			
		}

		echo json_encode($data,JSON_UNESCAPED_UNICODE);
		die();
	}

	public function sendNotification()
	{	
		global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']);
		$week = date("W");
		$week ++;
		$year = date("Y");

		$fechas = getFirstLastDayWeek($year, $week);
		$inicio = date("Y-m-d", $fechas->{'first_day'}->{'date'} ); 
		$fin = date("Y-m-d", strtotime( $fechas->{'last_day'}->{'date'} )); 

		$dataAud = AuditoriaModel::getAudit([], "id=".$_GET['id'])[0];
		$data = ['asunto' => $fnT('Announced visit'), 
				 'email' => 'test@test.com', 
				 'bcc' => 'mosorio@bw-globalsolutions.com', 
				 'tienda_name' => $dataAud["location_name"],
				 'tienda_number' => '14/11/2022',
				 'inicio' => '14/11/2022',
				 'fin' => '20/11/2022'];
		$request_mail = sendAnnouncedVisit($data, 'announced_visit'); //Helpers
		if($request_mail > 0) {
			$updateAuditValues = [
				'notification_date' => date('Y-m-d')
			];
			AuditoriaModel::updateAudit($updateAuditValues, "id=".$_GET['id']);
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
		
		global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']);

		$inicio = date('m/d/Y', strtotime('last monday', strtotime(date("d-m-Y")."+ 7 days")));
		$fin = date('m/d/Y', strtotime('next sunday', strtotime(date("d-m-Y")."+ 7 days")));

		if (isset($_POST['franchises']) ) {
			$frs = explode(",",$_POST['franchises']);
			//$filter = " and WEEKOFYEAR(al.announced_date) in (".(date("W") +1).") ";
			foreach($frs as $fr) {
				$strVisitas = '';
				$filter = " and WEEKOFYEAR(al.announced_date) in (".(date("W") +1).")AND type = 'Training-visits' AND IFNULL(al.announced_mail, 0) = 0 ";
				$filter .=" and lo.franchise_id in ($fr)";
				$dataAnnounced = $this->model->getAnnouncedVisitList($filter);
				$dataFranchise = $this->model->getFranchises([], "id=".$fr)[0];
				$update = $this->model->updateAnnouncedVisits($filter);
				

				if (count($dataAnnounced)) {
					//dep($dataFranchise);
					$franchiseMails = getFranchiseEmails(['Fanchisee'], $fr);
					//dep($franchiseMails);
					$recipients = emailFilter($franchiseMails);
					//dep($recipients);
					$strVisitas = '<table width="100%" border="2">
                                    <tr style="background:#7FDFD4">
                                        <th>Store</th>
                                        <th>Date</th>
                                    </tr>';
					foreach($dataAnnounced as $visit) {
						//$strVisitas .= '<li>#'.$visit['location_number'].' - '.$visit['country'].' - '.$visit['state_name'].' - '.$visit['city'].' - <b>'.date("Y-m-d", strtotime( $visit['announced_date'] )).'</b></li>';
						// $ids_announced .= ",".$visit['id'];
						$strVisitas .= '<tr>
											<td>#'.$visit['location_number'].' - '.$visit['location_name'].'</td>
											<td><b>'.date("Y-m-d", strtotime( $visit['announced_date'] )).'</b></td>
										</tr>';
					}
					$strVisitas .= '</table>';

					$asunto = 'DQ Announced visits - '.$dataFranchise['name'];
					//$countryMails = getCountryEmails(['Master BP +','Regional OPS'], $audit['country_id']);
					$cc = $recipients;
if ($visit['country'] == 'Philippines') {
    $to = 'Wildon.Lacro@idq.com,
           businesssupport-ca@lrqa.com,
           cdabu@ppiholdingsinc.com.ph,
           nborillo@ppiholdingsinc.com.ph,
           cmlim@ppiholdingsinc.com.ph,
           deromarate@ppiholdingsinc.com.ph';

} else if ($visit['country'] == 'Indonesia') {
    $to = 'oahmed@minor.com, 
           Michael.aphaivongs@idq.com,  
           Wildon.lacro@idq.com,  
           carmen.poon@idq.com,  
           Jennifer.chew@idq.com, 
           joan.tan@idq.com,
           businesssupport-ca@lrqa.com,
		   fnatalina@minor.com ';

} else if (in_array($visit['country'], ['Qatar', 'Bahrain', 'Kuwait'])) {
    $to = 'Yasser.Ismael@idq.com, 
           Jonathan.edwards@idq.com,
           arguileaoperations@bw-globalsolutions.com,businesssupport-ca@lrqa.com';
}else if ($visit['country'] == 'Panama') {
    $to = 'ivan.hernandez@platbrands.com,
		   mayelin.chavez@platbrands.com,
		   jethnifer.lopez@platbrands.com,
		   elsy.pena@platbrands.com,
		   shanira.deer@platbrands.com,
		   omar.quevedo@platbrands.com
';

}


					$data = ['asunto' 	=> $asunto,
						 	 'email' 	=> $to,
						 	 'cc' 		=> $cc,
						 	 'bcc' 		=> 'mosorio@bw-globalsolutions.com,emaldonado@bw-globalsolutions.com,dpeza@arguilea.com, ycabello@arguilea.com',
						 	 'tiendas' 	=> $strVisitas,
						 	 'inicio' 	=> $inicio,
						 	 'fin' 		=> $fin];
					//dep($data);
					$request_mail = sendEmailMasive($data, "announced_visit");

					//$this->model->setLog($_SESSION['userData']['id'], "send email announced visit");
				}
			}
			if($request_mail > 0) {
				$arrResponse = array("status" => true, "msg" => "Notificacion enviada.");	
			} else{
				$arrResponse = array("status" => false, "msg" => "Error al enviar correo.");
			}
		} else {
			$arrResponse = array("status" => false, "msg" => "Error, seleecione al menos una franquicia.");
		}



		$this->model->setLog($_SESSION['userData']['id'], "send email announced visit",json_encode($arrResponse,JSON_UNESCAPED_UNICODE));
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();


	}








	public function test()
	{
		echo "Funcion prueba";
	}
}
?>