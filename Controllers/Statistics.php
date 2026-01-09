<?php
require_once 'Models/UsuariosModel.php';
class Statistics extends Controllers{

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
		$this->permission = $_SESSION['userData']['permission']['Estadisticas'];

		if(!$this->permission['r']){
			header('Location: '.base_url());
		}
	}

    public function statistics()
	{
		require_once 'Models/CountryModel.php';
		require_once 'Models/UsuariosModel.php';
		require_once 'Models/LocationModel.php';
		$objData = new CountryModel();
		$obj2 = new UsuariosModel();
		$obj3 = new LocationModel();
		$data['page_tag'] = "Statistics";
		$data['page_title'] = "Statistics";
		$data['page_name'] = "statistics";
        $data['page-functions_js'] = "functions_statistics.js?21102024";
		
		$data['audit_types'] = listAuditTypes();
		$data['franchissees'] = $this->model->getFranchissees();
		
		$data['periods'] = $this->getPeriods('2025-01');
		$data['auditor_email'] = $this->model->getAuditors();
		$data['countrys'] = $objData->getCountry([], "active=1 AND id IN (".$_SESSION['userData']['country_id'].")");
		$data['ml'] = $obj2->getUsers('role_id = 19 AND status=1');
		$data['subF'] = $obj3->getSubF();
		//die(var_dump($data['ml']));

		$this->views->getView($this, "statistics", $data);
	}

	public function actualizarTiendasFiltro(){
		$stores = $this->model->getFranchissees(($_POST['countrys']!=''?$_POST['countrys']:'no country'), ($_POST['ml']!=''?$_POST['ml']:'no ml'), ($_POST['subF']!=''?$_POST['subF']:'no subF'));
		echo json_encode(array(
			'status' => true,
			'stores' => $stores
		));
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
				'color' => ['rgb'=>'EAB54C']	
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
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$general = $this->model->getAuditMain(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		for($i=0; $i<count($general); $i++){ //para poner los puntos perdidos y no los ganados
			$general[$i]['food_safety'] = 100-$general[$i]['food_safety'];
			if($general[$i]['food_safety']<0)$general[$i]['food_safety']=0;
			$general[$i]['operations_excellence'] = 100-$general[$i]['operations_excellence'];
			if($general[$i]['operations_excellence']<0)$general[$i]['operations_excellence']=0;
		}

		$table = [['AUDITOR EMAIL', 'ID','ROUND','LOCATION NUMBER','LOCATION NAME','COUNTRY',['TYPE', 'translate'],'LOCAL/FORANEA',['STATUS', 'translate'],['VISIT STATUS', 'translate'],['DAYPART', 'translate'],['WEEKDAY', 'translate'], ['DATE VISIT', 'date_time'], ['DATE VISIT END', 'date_time'],['VISIT DURATION', 'time_dif'], ['RELEASED DURATION', 'time_dif'],'FOOD SAFETY(LOST POINTS)','OPERATIONS EXCELLENCE(LOST POINTS)','OVERALL SCORE', 'VISIT RESULT', ['ACTION PLAN STATUS', 'translate'], 'OPORTUNITIES', 'IN PROCESS ACTIONS', 'COMPLETED ACTIONS']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'General information');
		exit;
	}
    
	public function exportCompletedAudits(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$completed = $this->model->getAuditMain(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), 'completed');
		for($i=0; $i<count($completed); $i++){ //para poner los puntos perdidos y no los ganados
			$completed[$i]['food_safety'] = 100-$completed[$i]['food_safety'];
			if($completed[$i]['food_safety']<0)$completed[$i]['food_safety']=0;
			$completed[$i]['operations_excellence'] = 100-$completed[$i]['operations_excellence'];
			if($completed[$i]['operations_excellence']<0)$completed[$i]['operations_excellence']=0;
		}

		$table = [['AUDITOR EMAIL', 'ID','ROUND','LOCATION NUMBER','LOCATION NAME','COUNTRY',['TYPE', 'translate'],'LOCAL/FORANEA',['STATUS', 'translate'],['VISIT STATUS', 'translate'],['DAYPART', 'translate'],['WEEKDAY', 'translate'], ['DATE VISIT', 'date_time'], ['DATE VISIT END', 'date_time'],['VISIT DURATION', 'time_dif'], ['RELEASED DURATION', 'time_dif'],'FOOD SAFETY(LOST POINTS)','OPERATIONS EXCELLENCE(LOST POINTS)','OVERALL SCORE','VISIT RESULT', ['ACTION PLAN STATUS', 'translate'], 'OPORTUNITIES', 'IN PROCESS ACTIONS', 'COMPLETED ACTIONS']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $completed));

		$this->genTable($table, 'General information');
		exit;
	}
	
	public function exportFrequencyOpp(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$frequency = $this->model->frequencyOpp(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $_SESSION['userData']['default_language']);

		$table = [[['SECTION NAME', 'translate'], 'PREFIX', 'QUESTION', 'PENALIZED', 'FREQUENCY']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $frequency));

		$this->genTable($table, 'General information');
		exit;
	}

	public function exportActionPlan(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$certificate = $this->model->getActionPlan(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $_SESSION['userData']['default_language']);

		$table = [['ID','ROUND',['TYPE', 'translate'],'AUDITOR NAME','LOCATION NUMBER','LOCATION NAME',['DATE VISIT', 'date_time'], ['DATE VISIT END', 'date_time'],['MAIN SECTION', 'translate'],['SECTION NAME', 'translate'],'PREFIX','TEXT',['STATUS', 'translate'],'DATE', 'COMMENT']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $certificate));

		$this->genTable($table, 'Action Plan');
		exit;
	}

	public function exportTopOppDetails($qprefix){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$oppDetails = $this->model->topOppDetails(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $_SESSION['userData']['default_language'], $qprefix);

		$total =  array_sum(array_column($oppDetails, 'count'));
		$oppDetails = array_map(function($item) use($total){
			return [$item['question_prefix'], $item['txt'], $item['count'], round($item['count'] / $total * 100, 2) . '%' ];
		}, $oppDetails);

		$table = [['QUESTION', 'TEXT', 'COUNT', 'PERCENT']];
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
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$leadership = $this->model->leadership(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		for($i=0; $i<count($leadership); $i++){
			$leadership[$i]['af'] = $this->model->getAutofails($leadership[$i]['name'], implode("','", $_POST['list_period']), implode("','", $_POST['list_type']));
		}
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
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$topOppBs = $this->model->topOpp($_SESSION['userData']['default_language'], "'".implode("','", $franchise)."'", implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($topOppBs, JSON_UNESCAPED_UNICODE));
	}
	
	public function getActionPlanStatus(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$ActionPlanStatus = $this->model->actionPlanStatus(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($ActionPlanStatus, JSON_UNESCAPED_UNICODE));
	}
	
	public function getDaypart(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$daypart = $this->model->daypart(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($daypart, JSON_UNESCAPED_UNICODE));
	}

	public function getWeekday(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$weekday = $this->model->weekday(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($weekday, JSON_UNESCAPED_UNICODE));
	}
	
	public function getDuration(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$hours = $this->model->duration(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($hours, JSON_UNESCAPED_UNICODE));
	}
	
	public function exportAddQuestions(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$addQuestions = $this->model->addQuestions(implode("','", $franchise), 
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
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$reportOpp = $this->model->reportOpp(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $_SESSION['userData']['default_language']);

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
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$general = $this->model->appealItems(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $_SESSION['userData']['default_language']);

		$table = [['ID VISIT', 'LOCATION NUMBER', 'LOCATION NAME', ['MAIN SECTION', 'translate'], ['SECTION NAME', 'translate'], 'PREFIX', 'QUESTION', 'PICKLIST', 'AUTHOR COMMENT', 'AUDITOR NAME', 'AUDITOR EMAIL', 'GENERAL STATUS', 'APPEAL STATUS', 'DECISION COMMENT']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}
	
	public function exportOppPerSection(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$general = $this->model->oppPerSection(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));

		$table = [[['MAIN SECTION', 'translate'], ['SECTION NAME', 'translate'], 'QUESTION', 'OPPORTUNITIES', 'AUDITS', 'PERCENTAGE PER AUDIT']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}
	
	public function exportOppPerAuditor(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$general = $this->model->oppPerAuditor(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));

		$table = [['AUDITOR EMAIL', ['MAIN SECTION', 'translate'], ['SECTION NAME', 'translate'], 'QUESTION', 'OPPORTUNITIES', 'AUDITS', 'PERCENTAGE PER AUDIT']];
		array_push($table, ...array_map(function ($item){ return array_values($item); }, $general));

		$this->genTable($table, 'Points details');
		exit;
	}
	
	public function exportAuditorSurvey(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$general = $this->model->auditorSurvey(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));

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
		
		$this->views->getView($this, "statistics_gallery", $data);
	}

	

	public function getGallery(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$gallery = $this->model->gallery(implode("','", $franchise), 
										 implode("','", $_POST['list_period']), 
										 $_POST['list_type'], 
										 implode("','", $_POST['list_auditor']),
										 implode("','", $_POST['list_checklist']),
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
		
		$this->views->getView($this, "statistics_program_preview", $data);
	}

	public function getProgramPreview(){
		$rounds = [
			'Round 1' => ['01','02','03','04','05','06'],
			'Round 2' => ['07','08','09','10','11','12']
		];

		$round = $rounds[substr($_POST['list_period'], 0, 7)];
		$months = array_map(function($item){ return substr($_POST['list_period'], 8, 12) . '-' . $item; }, $round);

		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$programPreview = $this->model->programPreview(implode("','", $franchise), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']), $months);
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
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$categoryTrend = $this->model->categoryTrend(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($categoryTrend, JSON_UNESCAPED_UNICODE));
	}

	public function getQuestionTrend(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$questionTrend = $this->model->questionTrend($_SESSION['userData']['default_language'], implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($questionTrend, JSON_UNESCAPED_UNICODE));
	}
		
	public function getProgressStatus(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$progressStatus = $this->model->progressStatus(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($progressStatus, JSON_UNESCAPED_UNICODE));
	}
	
	public function getFailureRate(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$failureRate = $this->model->failureRate(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		die(json_encode($failureRate, JSON_UNESCAPED_UNICODE));
	}
	
	public function getRatingByGroup(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$ratingByDP = $this->model->ratingByDP(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		$ratingByPeriod = $this->model->ratingByPeriod(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
		
		$response = [
			'Distribution of grades by part of the day'	=> $ratingByDP, 
			'Distribution of grades by period'			=> $ratingByPeriod
		];
		die(json_encode($response, JSON_UNESCAPED_UNICODE));
	}
	
	public function getActionCompletion(){
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$actionCompletion = $this->model->actionCompletion(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
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
		$franchise = str_replace("'", "\\'", $_POST['list_franchise']);
		$data = $this->model->getScoreTopBottom(implode("','", $franchise), implode("','", $_POST['list_period']), implode("','", $_POST['list_type']), implode("','", $_POST['list_auditor']));
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

}
?>