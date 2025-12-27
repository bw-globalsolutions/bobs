<?php
require_once 'Models/UsuariosModel.php';
class Statistics extends Controllers{

	private $permission;
	public $model;

	public function __construct()
	{
		parent::__construct();
		session_start();
		//session_regenerate_id(true);
		if(empty($_SESSION['login']))
		{
			header('location: '.base_url().'/login');
		}
		$this->permission = $_SESSION['userData']['permission']['Estadisticas'];

		if(!$this->permission['r']){
			header('Location: '.base_url());
		}
	}

    public function statistics()
	{
		$data['page_tag'] = "Statistics";
		$data['page_title'] = "Statistics";
		$data['page_name'] = "statistics";
        $data['page-functions_js'] = "functions_statistics.js?21102024";
		
		$data['audit_types'] = listAuditTypes();
		$data['franchissees'] = $this->model->getFranchissees();
		$data['periods'] = $this->getPeriods('2024-01');
		$data['auditor_email'] = $this->model->getAuditors();
		$data['audit_list'] = $this->model->getAuditList(['id', 'checklist_id', 'location_id', 'round_name', 'period', 'auditor_name', 'auditor_email', 'status', 'date_visit', 'local_foranea', 'location_number', 'location_name', 'location_address','country_id', 'country_name', 'region', 'brand_id', 'brand_name', 'brand_prefix' ,'email_ops_director','email_ops_leader','email_area_manager','email_franchisee','concept','shop_type','area','franchissees_name'], "", true);
		//Nuevos filtros
		/*$data['area'] = $this->model->getArea();
		$data['shop_type'] = $this->model->getShopType();
		$data['country'] = $this->model->getCountry();
		$data['concept'] = $this->model->getConcept();
		$data['franchissees_name'] = $this->model->getEmailFranchisee();
		$data['email_area_manager'] = $this->model->getEmailAreaManager();
		$data['email_ops_leader'] = $this->model->getEmailOpsLeader();
		$data['email_ops_director'] = $this->model->getEmailOpsDirector();*/
		$data['area'] = [];
		$data['shop_type'] = [];
		$data['country'] = [];
		$data['concept'] = [];
		$data['franchissees_name'] = [];
		$data['email_area_manager'] = [];
		$data['email_ops_leader'] = [];
		$data['email_ops_director'] = [];
		$data['regions_with_countries'] = []; // NUEVO: Estructura para agrupar


		foreach($data['audit_list'] as $item){
			
		
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

			 
        // NUEVO: Agrupar países por región
        $region = !empty($item['region']) ? $item['region'] : 'Sin Región';
        $country = !empty($item['country_name']) ? $item['country_name'] : 'N/A';
        
        if (!isset($data['regions_with_countries'][$region])) {
            $data['regions_with_countries'][$region] = [];
        }
        
        if (!in_array($country, $data['regions_with_countries'][$region])) {
            $data['regions_with_countries'][$region][] = $country;
        }
			

		}

		$this->views->getView($this, "statistics", $data);
		
	}

	private function getPeriods($endDate) {
		$currentDate = new DateTime();
		$endDate = new DateTime($endDate . '-01');
		$rounds = [];
	
		while ($currentDate >= $endDate) {
			$year = $currentDate->format('Y');
			$month = (int)$currentDate->format('m');
			$round = ($month <= 6) ? 'Round 1' : 'Round 2';
			$roundKey = "$round $year";
	
			if (!isset($rounds[$roundKey])) {
				$rounds[$roundKey] = [];
			}
	
			$rounds[$roundKey][] = $currentDate->format('Y-m');
			$currentDate->modify('-1 month');
		}
	
		return $rounds;
	}

	private function genTable($table, $title){
		require_once $_SERVER['DOCUMENT_ROOT'] .'/Assets/libraries/PHPExcel/Classes/PHPExcel.php';
		$fnT = translate($_SESSION['userData']['default_language']); 
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle($title);

		$headerStyle = [
			'fill' => [
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => ['rgb'=>'124DD5']	
			],
			'font' => [
				'bold' => true,
				'color' => ['rgb'=>'FFFFFF'],
				'size'  => 12
			]
		];
		$exceptions = [];

		for ($i = 0; $i < count($table); $i++) {
			for ($j = 0; $j < count($table[$i]); $j++) {
				if($i>0){
					switch ($exceptions[$j]) {
						case 'date_time':
							if($table[$i][$j] > 0){
								$date = new DateTime($table[$i][$j]);
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i + 1, PHPExcel_Shared_Date::PHPToExcel($date));
								$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j, $i + 1)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
							}
							break;
						case 'time_dif':
							if($table[$i][$j] > 0){
								$date = new DateTime();
								$date->setTimestamp($table[$i][$j]);
								$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i + 1, PHPExcel_Shared_Date::PHPToExcel($date->getTimestamp()));
								$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j, $i + 1)->getNumberFormat()->setFormatCode('hh:mm:ss');
							}
							break;
						case 'translate':
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j , $i + 1, $fnT($table[$i][$j]));
							break;
						default:
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j , $i + 1, $table[$i][$j]);
					}
				}else{
					if(is_array($table[$i][$j])){
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j , $i + 1, $fnT($table[$i][$j][0]));
						$exceptions[$j] = $table[$i][$j][1];
					}else{
						$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j , $i + 1, $fnT($table[$i][$j]));
					}
					$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($j, 1)->applyFromArray($headerStyle);
					$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($j)->setAutoSize(true);	
				}
			}
		}
		
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $title . '.xls"');
		header('Cache-Control: max-age=0');
		$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
		$objWriter->save('php://output');
	}

    public function exportAuditGeneal(){
		$general = $this->model->getAuditMain(implode("','", $_POST['list_franchise']), 
											  implode("','", $_POST['list_period']), 
											  implode("','", $_POST['list_type']), 
											  implode("','", $_POST['list_auditor']), 
											  implode("','", $_POST['list_shop_type']),
											  implode("','", $_POST['list_country']),
											  implode("','", $_POST['list_area']),
											  implode("','", $_POST['list_concept']),
											  implode("','", $_POST['list_area_manager']),
											  implode("','", $_POST['list_escalation1']),
											  implode("','", $_POST['list_escalation2']));

		$table = [['AUDITOR EMAIL', 'ID','ROUND','LOCATION NUMBER','LOCATION NAME','AUDITA FOLIO',['TYPE', 'translate'],'LOCAL/FORANEA',['STATUS', 'translate'],['VISIT STATUS', 'translate'],['DAYPART', 'translate'],['WEEKDAY', 'translate'], ['DATE VISIT', 'date_time'], ['DATE VISIT END', 'date_time'],['VISIT DURATION', 'time_dif'], ['RELEASED DURATION', 'time_dif'],'CRITICS','NO CRITICS','YELLOW','RED','MAINTENANCE', ['ACTION PLAN STATUS', 'translate'], 'OPORTUNITIES', 'IN PROCESS ACTIONS', 'COMPLETED ACTIONS']];
		
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'General information');
		exit;
	}
    
	public function exportCompletedAudits(){
		$completed = $this->model->getAuditMain(implode("','", $_POST['list_franchise']), 
											  implode("','", $_POST['list_period']), 
											  implode("','", $_POST['list_type']), 
											  implode("','", $_POST['list_auditor']), 
											  implode("','", $_POST['list_shop_type']),
											  implode("','", $_POST['list_country']),
											  implode("','", $_POST['list_area']),
											  implode("','", $_POST['list_concept']),
											  implode("','", $_POST['list_area_manager']),
											  implode("','", $_POST['list_escalation1']),
											  implode("','", $_POST['list_escalation2']), 'completed');

		$table = [['AUDITOR EMAIL', 'ID','ROUND','COUNTRY','LOCATION NUMBER','LOCATION NAME','TYPE','AREA','FRANCHISSES NAME','CORE MENU','COUNTRY','AUDITA FOLIO',['TYPE', 'translate'],'LOCAL/FORANEA',['STATUS', 'translate'],['VISIT STATUS', 'translate'],['DAYPART', 'translate'],['WEEKDAY', 'translate'], ['DATE VISIT', 'date_time'], ['DATE VISIT END', 'date_time'],['VISIT DURATION', 'time_dif'], ['RELEASED DURATION', 'time_dif'],'CRITICS','NO CRITICS','YELLOW','RED','MAINTENANCE','ZERO TOLERANCE','VISIT RESULT', ['ACTION PLAN STATUS', 'translate'], 'OPORTUNITIES', 'IN PROCESS ACTIONS', 'COMPLETED ACTIONS']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $completed));

		$this->genTable($table, 'General information');
		exit;
	}
	
	public function exportFrequencyOpp(){
		$frequency = $this->model->frequencyOpp(implode("','", $_POST['list_franchise']), 
											  implode("','", $_POST['list_period']), 
											  implode("','", $_POST['list_type']), 
											  implode("','", $_POST['list_auditor']), 
											  implode("','", $_POST['list_shop_type']),
											  implode("','", $_POST['list_country']),
											  implode("','", $_POST['list_area']),
											  implode("','", $_POST['list_concept']),
											  implode("','", $_POST['list_area_manager']),
											  implode("','", $_POST['list_escalation1']),
											  implode("','", $_POST['list_escalation2'])
											  , $_SESSION['userData']['default_language']);

		$table = [[['SECTION NAME', 'translate'], 'PREFIX', 'QUESTION', 'PENALIZED', 'FREQUENCY', 'CRITICS', 'NO CRITICS', 'POINTS']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $frequency));

		$this->genTable($table, 'General information');
		exit;
	}

	public function exportActionPlan(){
		$certificate = $this->model->getActionPlan(implode("','", $_POST['list_franchise']), 
											 	   implode("','", $_POST['list_period']), 
											 	   implode("','", $_POST['list_type']), 
											 	   implode("','", $_POST['list_auditor']), 
											 	   implode("','", $_POST['list_shop_type']),
											 	   implode("','", $_POST['list_country']),
											 	   implode("','", $_POST['list_area']),
											 	   implode("','", $_POST['list_concept']),
											 	   implode("','", $_POST['list_area_manager']),
											 	   implode("','", $_POST['list_escalation1']),
											 	   implode("','", $_POST['list_escalation2']), 
												   $_SESSION['userData']['default_language']);

		$table = [['ID','ROUND',['TYPE', 'translate'],'AUDITOR NAME','LOCATION NUMBER','LOCATION NAME',['DATE VISIT', 'date_time'], ['DATE VISIT END', 'date_time'],'DATE ACTION PLAN','HOURS',['MAIN SECTION', 'translate'],['SECTION NAME', 'translate'],'PREFIX','TEXT',['STATUS', 'translate'], 'COMMENT']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $certificate));

		$this->genTable($table, 'Action Plan');
		exit;
	}

	public function exportTopOppDetails($qprefix){
		$oppDetails = $this->model->topOppDetails(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $_SESSION['userData']['default_language'], $qprefix);

		$total =  array_sum(array_column($oppDetails, 'count'));
		$oppDetails = array_map(function($item) use($total){
			return [$item['question_prefix'], $item['txt'],$item['auditor_answer'], $item['count'], round($item['count'] / $total * 100, 2) . '%' ];
		}, $oppDetails);

		$table = [['QUESTION', 'TEXT','ANSWER', 'COUNT', 'PERCENT']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $oppDetails));

		$this->genTable($table, 'Top opp details');
		exit;
	}
	
	public function exportLocations(){
		$locations = $this->model->getLocations();
	
		$table = [['Shop_Number','Shop_Name', 'Address', 'City', 'State_Code', 'State_Name', 'Zip', 'Country', 'Phone_Number', 'Email', 'Shop_Type', 'Status']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $locations));
	
		$this->genTable($table, 'Locations');
		exit;
	}
    
	public function getLeadership(){
		$leadership = $this->model->leadership(implode("','", $_POST['list_franchise']), 
										       implode("','", $_POST['list_period']), 
										       implode("','", $_POST['list_type']), 
										       implode("','", $_POST['list_auditor']), 
										       implode("','", $_POST['list_shop_type']),
										       implode("','", $_POST['list_country']),
										       implode("','", $_POST['list_area']),
										       implode("','", $_POST['list_concept']),
										       implode("','", $_POST['list_area_manager']),
										       implode("','", $_POST['list_escalation1']),
										       implode("','", $_POST['list_escalation2']));
		die(json_encode($leadership, JSON_UNESCAPED_UNICODE));
	}

	public function exportUsers(){
		$frequency = $this->model->fullUsers();

		$table = [['ID USER', 'COUNTRY', 'LOCATION', 'ROLE', 'NAME', 'EMAIL', 'STATUS', 'CREATED']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $frequency));

		$this->genTable($table, 'Full users');
		exit;
	}
	
	public function getTopOpp(){
 

		$topOppBs = $this->model->topOpp($_SESSION['userData']['default_language'], 
										 implode("','", $_POST['list_franchise']), 
										 implode("','", $_POST['list_period']), 
										 implode("','", $_POST['list_type']), 
										 implode("','", $_POST['list_auditor']), 
										 implode("','", $_POST['list_shop_type']),
										 implode("','", $_POST['list_country']),
										 implode("','", $_POST['list_area']),
										 implode("','", $_POST['list_concept']),
										 implode("','", $_POST['list_area_manager']),
										 implode("','", $_POST['list_escalation1']),
										 implode("','", $_POST['list_escalation2']));
										 
		die(json_encode($topOppBs, JSON_UNESCAPED_UNICODE));
	}
	
	public function getActionPlanStatus(){
		$ActionPlanStatus = $this->model->actionPlanStatus(implode("','", $_POST['list_franchise']), 
														   implode("','", $_POST['list_period']), 
														   implode("','", $_POST['list_type']), 
														   implode("','", $_POST['list_auditor']), 
										 				   implode("','", $_POST['list_shop_type']),
										 				   implode("','", $_POST['list_country']),
										 				   implode("','", $_POST['list_area']),
										 				   implode("','", $_POST['list_concept']),
										 				   implode("','", $_POST['list_area_manager']),
										 				   implode("','", $_POST['list_escalation1']),
										 				   implode("','", $_POST['list_escalation2']));
														   
		die(json_encode($ActionPlanStatus, JSON_UNESCAPED_UNICODE));
	}
	
	public function getDaypart(){
		$daypart = $this->model->daypart(implode("','", $_POST['list_franchise']), 
									     implode("','", $_POST['list_period']), 
									     implode("','", $_POST['list_type']), 
									     implode("','", $_POST['list_auditor']), 
									     implode("','", $_POST['list_shop_type']),
									     implode("','", $_POST['list_country']),
									     implode("','", $_POST['list_area']),
									     implode("','", $_POST['list_concept']),
									     implode("','", $_POST['list_area_manager']),
									     implode("','", $_POST['list_escalation1']),
									     implode("','", $_POST['list_escalation2']));

		die(json_encode($daypart, JSON_UNESCAPED_UNICODE));
	}

	public function getWeekday(){
		$weekday = $this->model->weekday(implode("','", $_POST['list_franchise']), 
										 implode("','", $_POST['list_period']), 
										 implode("','", $_POST['list_type']), 
										 implode("','", $_POST['list_auditor']), 
										 implode("','", $_POST['list_shop_type']),
										 implode("','", $_POST['list_country']),
										 implode("','", $_POST['list_area']),
										 implode("','", $_POST['list_concept']),
										 implode("','", $_POST['list_area_manager']),
										 implode("','", $_POST['list_escalation1']),
										 implode("','", $_POST['list_escalation2']));
										 
		die(json_encode($weekday, JSON_UNESCAPED_UNICODE));
	}
	
	public function getDuration(){

		$hours = $this->model->duration(implode("','", $_POST['list_franchise']), 
										implode("','", $_POST['list_period']), 
										implode("','", $_POST['list_type']), 
										implode("','", $_POST['list_auditor']), 
										implode("','", $_POST['list_shop_type']),
										implode("','", $_POST['list_country']),
										implode("','", $_POST['list_area']),
										implode("','", $_POST['list_concept']),
										implode("','", $_POST['list_area_manager']),
										implode("','", $_POST['list_escalation1']),
										implode("','", $_POST['list_escalation2']));

		die(json_encode($hours, JSON_UNESCAPED_UNICODE));
	}
	
	public function exportAddQuestions(){

		$addQuestions = $this->model->addQuestions(implode("','", $_POST['list_franchise']), 
												   implode("','", $_POST['list_period']), 
												   implode("','", $_POST['list_type']), 
												   implode("','", $_POST['list_auditor']), 
												   $_SESSION['userData']['default_language']);

		$table = [['ID','ROUND',['TYPE','translate'], ['VISIT STATUS','translate'],'LOCATION NUMBER','LOCATION NAME','AUDITOR NAME','TEXT','ANSWER']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $addQuestions));

		$this->genTable($table, 'General information');
		exit;
	}
	
	public function exportReportOpp(){
		$reportOpp = $this->model->reportOpp(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $_SESSION['userData']['default_language']);

		$table = [['ID VISIT','ID CHECKLIST','LOCATION NUMBER','LOCATION NAME','COUNTRY','AREA MANAGER', 'FRANCHISSE',['MAIN SECTION', 'translate'],['SECTION NAME', 'translate'],'PREFIX','QUESTION', 'PICKLIST', 'OPPORTUNITY', 'COMMENT','AUDITOR NAME','AUDITOR EMAIL', ['DATE VISIT', 'date_time'], ['DATE VISIT END', 'date_time'],['VISIT DURATION', 'time_dif']]];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $reportOpp));

		$this->genTable($table, 'General information');
		exit;
	}



	public function exportCalidadDq(){
		$reportOpp = $this->model->reportCalidadDq(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $_SESSION['userData']['default_language']);

		$table = [['ID VISIT','ID CHECKLIST','LOCATION NUMBER','LOCATION NAME',['MAIN SECTION', 'translate'],['SECTION NAME', 'translate'],'PREFIX','QUESTION', 'PICKLIST', 'OPPORTUNITY', 'COMMENT','AUDITOR NAME','AUDITOR EMAIL', ['DATE VISIT', 'date_time'], ['DATE VISIT END', 'date_time'],['VISIT DURATION', 'time_dif']]];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $reportOpp));

		$this->genTable($table, 'General information');
		exit;
	}

	public function exportPPDetails($token){
		$general = $this->model->getAuditPPDetails(base64_decode($token));

		$table = [['ID','ROUND','TYPE','AUDITOR NAME','COUNTRY','LOCATION NUMBER','LOCATION ADDRESS','STATUS','VISIT STATUS','DAYPART','WEEKDAY', ['DATE VISIT', 'date_time'], ['DATE VISIT END', 'date_time'],['VISIT DURATION', 'time_dif'], ['RELEASED DURATION', 'time_dif']]];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'General Program Preview');
		exit;
	}
	
	public function exportPtsDetails($token){
		$general = $this->model->getPtsDetails(base64_decode($token));

		$table = [['AUDIT ID', 'LOCATION NUMBER', 'QUESTION PREFIX', 'PICKLIST PREFIX', 'PICKLIST', 'LOST POINTS', 'AUDITOR ANSWER', 'AUDITOR COMMENT', 'ACTION PLAN STATUS']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}
	
	public function exportAppealItems(){
		$general = $this->model->appealItems(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $_SESSION['userData']['default_language']);

		$table = [['ID VISIT', 'LOCATION NUMBER', 'LOCATION NAME', ['MAIN SECTION', 'translate'], ['SECTION NAME', 'translate'], 'PREFIX', 'QUESTION', 'PICKLIST', 'AUTHOR COMMENT', 'AUDITOR NAME', 'AUDITOR EMAIL', 'GENERAL STATUS', 'APPEAL STATUS', 'DECISION COMMENT']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}
	
	public function exportOppPerSection(){
		$general = $this->model->oppPerSection(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));

		$table = [[['MAIN SECTION', 'translate'], ['SECTION NAME', 'translate'], 'QUESTION', 'OPPORTUNITIES', 'AUDITS', 'PERCENTAGE PER AUDIT']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}
	
	public function exportOppPerAuditor(){
		$general = $this->model->oppPerAuditor(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));

		$table = [['AUDITOR EMAIL', ['MAIN SECTION', 'translate'], ['SECTION NAME', 'translate'], 'QUESTION', 'OPPORTUNITIES', 'AUDITS', 'PERCENTAGE PER AUDIT']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}
	
	public function exportAuditorSurvey(){
		$general = $this->model->auditorSurvey(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));

		$table = [['LOCATION NUMBER', 'LOCATION NAME', 'AUDITOR NAME', 'AUDITOR EMAIL', 'DATE VISIT', 'CALIFICATION']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}

	public function exportCerttis(){

		$general = $this->model->exportCerttis();

		$table = [['ID OPPORTUNITY',  'AUDITOR',  'CERTTIS', 'CERTTIS COMMENT','QUESTION','AUDITOR ANSWER','AUDITOR COMMENT']];

		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}
	


	public function exportPending(){
		$general = $this->model->exportPending();
		
		$table = [['ID ',  'ROUND',  '#', 'LOCATION','AUDITOR','EMAIL','DATE START','TYPE']];

		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}

	public function gallery()
	{
		$data['page_tag'] = "Gallery";
		$data['page_title'] = "Gallery";
		$data['page_name'] = "gallery";
        $data['page-functions_js'] = "functions_gallery.js?22072024";

		$data['audit_types'] = listAuditTypes();
		$data['franchissees'] = $this->model->getFranchissees();
		$data['periods'] = $this->getPeriods('2024-01');
		$data['auditor_email'] = $this->model->getAuditors();
		$data['checklist'] = $this->model->getChecklist();

		$data['checklist_item'] = $this->model->getChecklistItem();
		$data['auditFile'] = $this->model->getAuditFile();
		
		$data['audit_list'] = $this->model->getAuditList(['id', 'checklist_id', 'location_id', 'round_name', 'period', 'auditor_name', 'auditor_email', 'status', 'date_visit', 'local_foranea', 'location_number', 'location_name', 'location_address','country_id', 'country_name', 'region', 'brand_id', 'brand_name', 'brand_prefix' ,'email_ops_director','email_ops_leader','email_area_manager','email_franchisee','concept','shop_type','area','franchissees_name'], "", true);
		//Nuevos filtros
		$data['country'] = [];
		$data['region'] = [];
		

			foreach($data['audit_list'] as $item){
			
		
			if (!in_array($item['country_name'], $data['country'])) {
				array_push($data['country'], $item['country_name']);
			}

				if (!in_array($item['region'], $data['region'])) {
				array_push($data['region'], $item['region']);
			}
			
			

		}

		$this->views->getView($this, "statistics_gallery", $data);
	}

	

	public function getGallery(){

		$gallery = $this->model->gallery(implode("','", $_POST['list_franchise']), 
										 implode("','", $_POST['list_period']), 
										 implode("','", $_POST['list_country']), 
										 implode("','", $_POST['list_region']), 
										 $_POST['list_type'], 
										 implode("','", $_POST['list_auditor']),
										 implode('","', $_POST['list_checklist']),
										 implode('","', $_POST['audit_file']),
										 implode('","', $_POST['list_checklist_item']));

		die(json_encode($gallery, JSON_UNESCAPED_UNICODE));
		
	}


public function programPreview()
{
    $data['page_tag'] = "Program preview";
    $data['page_title'] = "Program preview";
    $data['page_name'] = "programPreview";
    $data['page-functions_js'] = "functions_programpreview.js";
    $data['audit_types'] = listAuditTypes();
    $data['franchissees'] = $this->model->getFranchissees();

    $data['periods'] = array_unique(array_column(selectRound(['name']), 'name'));
    $data['auditor_email'] = $this->model->getAuditors();

    $data['audit_list'] = $this->model->getAuditList(['id', 'checklist_id', 'location_id', 'round_name', 'period', 'auditor_name', 'auditor_email', 'status', 'date_visit', 'local_foranea', 'location_number', 'location_name', 'location_address','country_id', 'country_name', 'region', 'brand_id', 'brand_name', 'brand_prefix' ,'email_ops_director','email_ops_leader','email_area_manager','email_franchisee','concept','shop_type','area','franchissees_name'], "", true);
    
    $data['country'] = [];
    $data['region'] = [];
    $data['regions_with_countries'] = []; // NUEVO: Estructura para agrupar
    
    foreach($data['audit_list'] as $item){
        // Países
        if (!in_array($item['country_name'], $data['country'])) {
            array_push($data['country'], $item['country_name']);
        }
        
        // Regiones
        if (!in_array($item['region'], $data['region'])) {
            array_push($data['region'], $item['region']);
        }
        
        // NUEVO: Agrupar países por región
        $region = !empty($item['region']) ? $item['region'] : 'Sin Región';
        $country = !empty($item['country_name']) ? $item['country_name'] : 'N/A';
        
        if (!isset($data['regions_with_countries'][$region])) {
            $data['regions_with_countries'][$region] = [];
        }
        
        if (!in_array($country, $data['regions_with_countries'][$region])) {
            $data['regions_with_countries'][$region][] = $country;
        }
    }
    
    $this->views->getView($this, "statistics_program_preview", $data);
}
	public function getProgramPreview(){
		$rounds = [
			'Round 1' => ['01','02','03','04','05','06'],
			'Round 2' => ['07','08','09','10','11','12']
		];

		$round = $rounds[substr($_POST['list_period'], 0, 7)];
		$months = array_map(function($item){ return substr($_POST['list_period'], 8, 12) . '-' . $item; }, $round);



		


		$programPreview = $this->model->programPreview(implode("','", $_POST['list_franchise']), 
													   implode("','", $_POST['list_type']), 
													   implode("','", $_POST['list_auditor']), 
													   implode("','", $_POST['list_country']), 
													   implode("','", $_POST['list_region']), 
													   $months);
		$response = array_map(function($item) use($months){
			$m = [];
			foreach($item as $key => $val){
				if(in_array($key, $months)){
					$m[$key] = $val;
				}
			}
			return [
				'auditor_email'	=> $item['auditor_email'],
				'avg'			=> $item['avg'],
				'periods'		=> $m
			];
		}, $programPreview);

		die(json_encode($response, JSON_UNESCAPED_UNICODE));
	}
	
	public function getCategoryTrend(){
		$categoryTrend = $this->model->categoryTrend(implode("','", $_POST['list_franchise']), 
													 implode("','", $_POST['list_period']), 
													 implode("','", $_POST['list_type']), 
													 implode("','", $_POST['list_auditor']), 
													 implode("','", $_POST['list_shop_type']),
													 implode("','", $_POST['list_country']),
													 implode("','", $_POST['list_area']),
													 implode("','", $_POST['list_concept']),
													 implode("','", $_POST['list_area_manager']),
													 implode("','", $_POST['list_escalation1']),
													 implode("','", $_POST['list_escalation2']));

		die(json_encode($categoryTrend, JSON_UNESCAPED_UNICODE));
	}

	public function getQuestionTrend(){
		$questionTrend = $this->model->questionTrend($_SESSION['userData']['default_language'],
													implode("','", $_POST['list_franchise']), 
													implode("','", $_POST['list_period']), 
													implode("','", $_POST['list_type']), 
													implode("','", $_POST['list_auditor']), 
													implode("','", $_POST['list_shop_type']),
													implode("','", $_POST['list_country']),
													implode("','", $_POST['list_area']),
													implode("','", $_POST['list_concept']),
													implode("','", $_POST['list_area_manager']),
													implode("','", $_POST['list_escalation1']),
													implode("','", $_POST['list_escalation2']) );
		die(json_encode($questionTrend, JSON_UNESCAPED_UNICODE));
	}
		
	public function getProgressStatus(){

		$progressStatus = $this->model->progressStatus(implode("','", $_POST['list_franchise']), 
													   implode("','", $_POST['list_period']), 
													   implode("','", $_POST['list_type']), 
													   implode("','", $_POST['list_auditor']), 
													   implode("','", $_POST['list_shop_type']),
													   implode("','", $_POST['list_country']),
													   implode("','", $_POST['list_area']),
													   implode("','", $_POST['list_concept']),
													   implode("','", $_POST['list_area_manager']),
													   implode("','", $_POST['list_escalation1']),
													   implode("','", $_POST['list_escalation2']));

		die(json_encode($progressStatus, JSON_UNESCAPED_UNICODE));

	}
	
	public function getFailureRate(){
		$failureRate = $this->model->failureRate(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($failureRate, JSON_UNESCAPED_UNICODE));
	}
	
	public function getRatingByGroup(){
		$ratingByDP = $this->model->ratingByDP(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		$ratingByPeriod = $this->model->ratingByPeriod(implode("','", $_POST['list_franchise']), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		
		$response = [
			'Distribution of grades by part of the day'	=> $ratingByDP, 
			'Distribution of grades by period'			=> $ratingByPeriod
		];
		die(json_encode($response, JSON_UNESCAPED_UNICODE));
	}
	
	public function getActionCompletion(){
		$actionCompletion = $this->model->actionCompletion(implode("','", $_POST['list_franchise']), 
														   implode("','", $_POST['list_period']), 
														   implode("','", $_POST['list_type']), 
														   implode("','", $_POST['list_auditor']), 
														   implode("','", $_POST['list_shop_type']),
														   implode("','", $_POST['list_country']),
														   implode("','", $_POST['list_area']),
														   implode("','", $_POST['list_concept']),
														   implode("','", $_POST['list_area_manager']),
														   implode("','", $_POST['list_escalation1']),
														   implode("','", $_POST['list_escalation2']));

		die(json_encode($actionCompletion, JSON_UNESCAPED_UNICODE));
	}

	public function getTarget(){
		$target = $this->model->getTarget($_POST['country'], $_POST['period']);
		die(json_encode($target, JSON_UNESCAPED_UNICODE));
	}
	
	public function setTarget(){
		$target = $this->model->setTarget($_POST['country'], $_POST['period'], $_POST['target']);
		die(json_encode(['status' => $target? 1 : 0], JSON_UNESCAPED_UNICODE));
	}

	public function getScoreTopBottom(){
		$data = $this->model->getScoreTopBottom(implode("','", $_POST['list_franchise']), 
											    implode("','", $_POST['list_period']), 
											    implode("','", $_POST['list_type']), 
											    implode("','", $_POST['list_auditor']), 
											    implode("','", $_POST['list_shop_type']),
											    implode("','", $_POST['list_country']),
											    implode("','", $_POST['list_area']),
											    implode("','", $_POST['list_concept']),
											    implode("','", $_POST['list_area_manager']),
											    implode("','", $_POST['list_escalation1']),
											    implode("','", $_POST['list_escalation2']));
		die(json_encode($data));
	}
	public function exportUserPass(){
		$general = $this->model->exportUserPass();
		
					

		
		$table = [['user_name', 'email', 'pass', 'role']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}
	public function exportUserLogin(){
		$general = $this->model->exportUserLogin();
		
		
		$table = [['id','number location','name location', 'franchissees', 'email', 'role', 'log count']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}


	public function exportLayoutReport(){

		$general = $this->model->exportLayoutReport();
		
		$table = [['date',
				   'zip',
		           'city',
		           'cakes',
		           'concept',
		           'country',
		           'dmaCode',
		           'dmaName',
		           'coreMenu',
		           'openDate',
		           'breakfast',
		           'driveThru',
		           'storeName',
		           'venueType',
		           'regionCode',
		           'regionName',
		           'storeEmail',
		           'storePhone',
		           'tempClosed',
		           'areaManager',
		           'escalation1',
		           'escalation2',
		           'storeNumber',
		           'addressLine1',
		           'addressLine2',
		           'districtCode',
		           'districtName',
		           'franchiseeName',
		           'franchiseeEmail',
		           'franchiseePhone',
		           'lastModernizationDate']];

				   

		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}

	
public function exportUser() {

    $user = UsuariosModel::user();


    
	$table = [['User',	
			   'Email',	
			   'Role',	
			   '#',	
			   'Location']];

	

array_push($table, ...array_map(function ($item){ return array_values($item); }, $user));

$this->genTable($table, 'Points details');
exit;

}


//ACTION PLAN VIEW
	public function actionPlan()
	{
		$data['page_tag'] = "Action plan";
		$data['page_title'] = "Action plan";
		$data['page_name'] = "Action plan";
        $data['page-functions_js'] = "funtions_stadistics_action_plan.js";

		$data['countries'] = selectCountries(['id', 'name', 'region'], "name IN('Mexico',
'Indonesia',
'Bahrain',
'Kuwait',
'Panama',
'Philippines',
'Qatar')");
		$data['typeVisit'] = $this->model->selectTypeVisits();
		
		
		$data['audit_types'] = listAuditTypes();

		$data['periods'] = array_unique(array_column(selectRound(['name']), 'name'));
		usort($data['periods'], function($a, $b) {
			$yearA = substr($a, -4);
			$yearB = substr($b, -4);
			if ($yearA != $yearB) {
				return $yearB - $yearA;
			}
		
			$roundA = substr($a, 6, 1);
			$roundB = substr($b, 6, 1);
			return $roundB - $roundA;
		});
		$data['franchissees'] = $this->model->getFranchissees();
		$data['audit_list'] = $this->model->getAuditList(['id', 'checklist_id', 'location_id', 'round_name', 'period', 'auditor_name', 'auditor_email', 'status', 'date_visit', 'local_foranea', 'location_number', 'location_name', 'location_address','country_id', 'country_name', 'region', 'brand_id', 'brand_name', 'brand_prefix' ,'email_ops_director','email_ops_leader','email_area_manager','email_franchisee','concept','shop_type','area','franchissees_name'], "", true);
		//Nuevos filtros
	
		$data['email_area_manager'] = [];



		foreach($data['audit_list'] as $item){
			
			if (!in_array($item['email_area_manager'], $data['email_area_manager'])) {
				array_push($data['email_area_manager'], $item['email_area_manager']);
			}

		}





		$this->views->getView($this, "actionPlan", $data);
	}
//MOSTRAR DATATABLE ACTION PLAN
    public function actionPlanTable()
    {
    $fnT = translate($_SESSION['userData']['default_language']);
    $idi = $_SESSION['userData']['default_language'];

   //VARIABLES DE FILTROS
	$f_type = "'" . implode("','", $_POST['f_type']) . "'";
	$f_status = "'" . implode("','", $_POST['f_status']) . "'";

	 // Obtiene los datos de la tabla
    $data = $this->model->actionPlanTable( implode(",", $_POST['f_country']), 
										   $_POST['f_period'], 
										   $f_type, 
										   $f_status,
										   implode("','", $_POST['list_franchise']),
										   implode("','", $_POST['list_area_manager'])
										    );
	$dataActionPlan = array();
	if (!empty($data)) {
		foreach ($data as $visit) {
			$datas['id'] = $visit['id'];

			//COLUMNA STORE
			$datas['store'] = '<b>'.$visit['brand_prefix'].' #'.$visit['location_number'].'- '.$visit['country_name'].'<br>'.$visit['location_name'].'</b>
			<br>
			<b>'.$visit['type'].' - '.$visit['round_name'].'</b>
			<br>
			<b><span class="badge badge-success">'.date("Y-m-d", strtotime( $visit['date_visit'] )).'</span></b><br>
			<b><span class="badge badge-info">'.$fnT('Action plan status').': '.$visit['action_plan_status'].'</span></b>';

			//COLUMNA LINKS
			$datas['visit'] = '
			<a target="_blank" href="'.getURLReport($visit['id'], $visit['report_layout_id'], $_SESSION['userData']['default_language']).'">
				<button type="button" class="btn btn-success btn-sm">'.$fnT('View report').'</button>
			</a>
			<br>
			<a target="_blank" href="'.base_url().'/actionPlan/auditPlan?id='.$visit['id'].'">
                <button type="button" class="btn btn-warning btn-sm">'.$fnT('Action Plan').'</button>
            </a>';

			array_push($dataActionPlan,$datas);
		}
	}
	// Devuelve los datos en formato JSON
	echo json_encode($dataActionPlan, JSON_UNESCAPED_UNICODE);
	die();
    }


//DISTRICT REPORT VIEW
	public function districtReport()
	{
		$data['page_tag'] = "District report";
		$data['page_title'] = "District report";
		$data['page_name'] = "District report";
        $data['page-functions_js'] = "funtions_stadistics_district_report.js";

		$data['countries'] = selectCountries(['id', 'name', 'region'], "name IN('Mexico',
'Indonesia',
'Bahrain',
'Kuwait',
'Panama',
'Philippines',
'Qatar')");


		$data['franchissees'] = $this->model->getFranchissees();
		$data['audit_list'] = $this->model->getAuditList(['id', 'checklist_id', 'location_id', 'round_name', 'period', 'auditor_name', 'auditor_email', 'status', 'date_visit', 'local_foranea', 'location_number', 'location_name', 'location_address','country_id', 'country_name', 'region', 'brand_id', 'brand_name', 'brand_prefix' ,'email_ops_director','email_ops_leader','email_area_manager','email_franchisee','concept','shop_type','area','franchissees_name'], "", true);
		//Nuevos filtros
	
		$data['email_area_manager'] = [];



		foreach($data['audit_list'] as $item){
			
			if (!in_array($item['email_area_manager'], $data['email_area_manager'])) {
				array_push($data['email_area_manager'], $item['email_area_manager']);
			}

		}


		$this->views->getView($this, "districtReport", $data);
	}

	 //MOSTRAR DATATABLE DISTRICT REPORT GLOBAL
	 public function districtReportGlobalTable()
	 {
	 $fnT = translate($_SESSION['userData']['default_language']);
	 $idi = $_SESSION['userData']['default_language'];
	 $f_country = "'" . implode("','", $_POST['f_country']) . "'";
	 $data = $this->model->districtReportGlobalTable($f_country,$_POST['f_years'],
										   implode("','", $_POST['list_franchise']),
										   implode("','", $_POST['list_area_manager']));
	 echo json_encode($data, JSON_UNESCAPED_UNICODE);
	 die();
	 }

	 //MOSTRAR TOTALES DATATABLE DISTRICT REPORT GLOBAL
	 public function districtReportTotal()
	 {
	 $fnT = translate($_SESSION['userData']['default_language']);
	 $idi = $_SESSION['userData']['default_language'];
	 $f_country = "'" . implode("','", $_POST['f_country']) . "'";
	 $data = $this->model->districtReportTotal($f_country,$_POST['f_years'],
										   implode("','", $_POST['list_franchise']),
										   implode("','", $_POST['list_area_manager']));
	 echo json_encode($data, JSON_UNESCAPED_UNICODE);
	 die();
	 }

	 //MOSTRAR DATATABLE DISTRICT REPORT TIENDAS
	 public function districtReportStoreTable()
	 {
	 $fnT = translate($_SESSION['userData']['default_language']);
	 $idi = $_SESSION['userData']['default_language'];
	 $f_country = "'" . implode("','", $_POST['f_country']) . "'";
	 $data = $this->model->districtReportStoreTable($f_country,$_POST['f_years'],
										   implode("','", $_POST['list_franchise']),
										   implode("','", $_POST['list_area_manager']));
	 echo json_encode($data, JSON_UNESCAPED_UNICODE);
	 die();
	 }







	 	public function revisitsProgress()
	{
		$data['page_tag'] = "Revisits progress";
		$data['page_title'] = "Revisits progress";
		$data['page_name'] = "revisits progress";
        $data['page-functions_js'] = "functions_revisits_progress.js?16022024";

		$data['countries'] = $this->model->selectCountriesValidates();

		$data['audits_types'] = $this->model->selectAuditTypes();
		
		
		$data['audit_types'] = listAuditTypes();

		$data['periods'] = array_unique(array_column(selectRound(['name']), 'name'));
		usort($data['periods'], function($a, $b) {
			$yearA = substr($a, -4);
			$yearB = substr($b, -4);
			if ($yearA != $yearB) {
				return $yearB - $yearA;
			}
		
			$roundA = substr($a, 6, 1);
			$roundB = substr($b, 6, 1);
			return $roundB - $roundA;
		});


		$data['franchissees'] = $this->model->getFranchissees();
		$data['audit_list'] = $this->model->getAuditList(['id', 'checklist_id', 'location_id', 'round_name', 'period', 'auditor_name', 'auditor_email', 'status', 'date_visit', 'local_foranea', 'location_number', 'location_name', 'location_address','country_id', 'country_name', 'region', 'brand_id', 'brand_name', 'brand_prefix' ,'email_ops_director','email_ops_leader','email_area_manager','email_franchisee','concept','shop_type','area','franchissees_name'], "", true);
		//Nuevos filtros
	
		$data['email_area_manager'] = [];



		foreach($data['audit_list'] as $item){
			
			if (!in_array($item['email_area_manager'], $data['email_area_manager'])) {
				array_push($data['email_area_manager'], $item['email_area_manager']);
			}

		}


		// dep($data);
		// die();

		$this->views->getView($this, "revisitsProgress", $data);
	}

	public function getRevisitsProgress() {
		// dep($_POST);
		//die();

		global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']);

		//$f_status = "'" . implode("','", $_POST['f_status']) . "'";
		$f_type = "'" . implode("','", $_POST['f_type']) . "'";

		$data = $this->model->getRevisitsStatus( implode(",", $_POST['f_country']), $_POST['f_period'], $f_type,
										   implode("','", $_POST['list_franchise']),
										   implode("','", $_POST['list_area_manager']));
		$dataRevisits = array();
		if (!empty($data)) {
			foreach ($data as $visit) {		
			
				
				$datas['id'] = $visit['id'];

				$data['score'] = getScore($datas['id'] );





				
				$datas['store'] = '<b>'.$visit['brand_prefix'].' #'.$visit['location_number'].'- '.$visit['country_name'].'
									<br>'.$visit['location_name'].'</b>
									
									<br><b>'.$visit['type'].' - '.$visit['round_name'].'</b>
									<br><b>'.$fnT("Auditor name").': '.$visit['auditor_name'].'</b>
									<br><b><span class="badge badge-success">'.$fnT('Date visit').': '.date("Y-m-d h:i:s", strtotime( $visit['date_visit'] )).'</span></b>
									<br><a target="_blank" href="'.getURLReport($visit['id'], $visit['report_layout_id'], $_SESSION['userData']['default_language']).'">
											<button type="button" class="btn btn-primary btn-sm">'.$fnT('View report').'</button>
										</a>';
			
				$datas['visit'] = '<button type="button" class="btn btn-info btn-sm">
										'.$fnT('Criticals').': <span id="score-bs">'.$data['score']['Criticos'].'</span>
									</button>
									<br><button type="button" class="btn btn-info btn-sm">
										'.$fnT('Red').': <span id="score-fs">'.$data['score']['Rojos'].'</span>
									</button>

							
									<br><button type="button" class="btn btn-sm btn-secondary text-light" id="btn-score-oa">
										'.$fnT('Overall score').': <span id="score-oa">'.$data['score']['Result'].'</span>
									</button>';
		
				$datas['end_time'] = '<b><span class="badge badge-info">N/A</span></b>';

			

				$dataProgressHistorical = $this->model->getInfoHistorical( $visit['location_id'], $visit['date_visit'], $_POST['f_number'] );
				
				if (empty($dataProgressHistorical)) {
					$datas['historical'] = '<b><span class="badge badge-warning">No historical</span></b>';
				} else {
					$bodyHistorical = '';
					foreach ($dataProgressHistorical as $visitHistorical) {


						
				$score['score'] = getScore($visitHistorical['id'] );

				


						$bodyHistorical .= '<tr>
												<th><b><span class="badge badge-info">'.date("Y-m-d", strtotime( $visitHistorical['date_visit_end'] )).'</span></b>
													<br><a target="_blank" href="'.getURLReport($visitHistorical['id'], $visit['report_layout_id'], $_SESSION['userData']['default_language']).'">
														<span class="badge badge-success">'.$fnT('View report').'</span></a>
												</th>
												<th>'.$visitHistorical['type'].'</th>
												<th>'.$score['score']['Criticos'].'</th>
												<th>'.$score['score']['Rojos'].'</th>
												<th>'.$score['score']['Result'].'</th>
											</tr>';
					}
					
					$datas['historical'] = '<div class="form-row">
												<div class="col">
													<div class="bg-info text-center text-white">'.$fnT('Historical').'</div>
													<hr class="border border-primary">
													<div class="table-responsive">
													<table class="table table-hover table-bordered">
														<thead>
														<tr>
															<th>'.$fnT('Previous visits').'</th>
															<th>'.$fnT('Type').'</th>
															<th>'.$fnT('Criticals').'</th>
															<th>'.$fnT('Red').'</th>
															<th>'.$fnT('Overall score').'</th>
														</tr>
														</thead>
														<tbody>'.$bodyHistorical.'</tbody>
													</table>
													</div>
												</div>
											</div>';
				}

		

				array_push($dataRevisits,$datas);
			}
		}

		echo json_encode($dataRevisits,JSON_UNESCAPED_UNICODE);
		die();
	}

}
?>