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
		require_once("Models/LocationModel.php");
		require_once("Models/RoundModel.php");
		require_once("Models/UsuariosModel.php");
		require_once("Models/AuditsModel.php");
		require_once("Models/Audit_OppModel.php");
		$obj1 = new RoundModel();
		$obj2 = new UsuariosModel();
		$obj3 = new LocationModel();
		$obj4 = new AuditsModel();
		$obj5 = new Audit_OppModel();
		$strPeriodos = "";
		$filtroFechas = "";
		if($_POST['periodos']!=[]){
			foreach($_POST['periodos'] as $p){
				$pos = strpos($p, '-');
				if($pos !== false){ // si incluye meses
					$strMes = explode('-', $p)[1];
					$year = explode(' ', explode('-', $p)[0])[2];
					$mes="";
					switch($strMes){
						case "Janeiro":
							$mes = "01";
							break;
						case "Fevereiro":
							$mes = "02";
							break;
						case "Março":
							$mes = "03";
							break;
						case "Abril":
							$mes = "04";
							break;
						case "Maio":
							$mes = "05";
							break;
						case "Junho":
							$mes = "06";
							break;
						case "Julho":
							$mes = "07";
							break;
						case "Agosto":
							$mes = "08";
							break;
						case "Setembro":
							$mes = "09";
							break;
						case "Outubro":
							$mes = "10";
							break;
						case "Novembro":
							$mes = "11";
							break;
						case "Dezembro":
							$mes = "12";
							break;
					}
					$filtroFechas.=($filtroFechas==""?"AND (":" OR (")."a.date_visit BETWEEN '$year-$mes-01' AND '$year-$mes-31')";
					$strPeriodos.="'".explode('-', $p)[0]."',";
				}else{
					$strPeriodos.="'".$p."',";
				}
			}
			$strPeriodos = substr($strPeriodos, 0, -1);
		}
		$strTipos = "";
		if($_POST['tipos']!=[]){
			foreach($_POST['tipos'] as $p){
				$strTipos.="'".$p."',";
			}
			$strTipos = substr($strTipos, 0, -1);
		}
		$strclasificaciones = "";
		if($_POST['clasificaciones']!=[]){
			foreach($_POST['clasificaciones'] as $p){
				$strclasificaciones.="'".$p."',";
			}
			$strclasificaciones = substr($strclasificaciones, 0, -1);
		}
		$strregiones = "";
		if($_POST['regiones']!=[]){
			foreach($_POST['regiones'] as $p){
				$strregiones.="'".$p."',";
			}
			$strregiones = substr($strregiones, 0, -1);
		}
		$strestados = "";
		if($_POST['estados']!=[]){
			foreach($_POST['estados'] as $p){
				$strestados.="'".$p."',";
			}
			$strestados = substr($strestados, 0, -1);
		}
		$strgerentes = "";
		if($_POST['gerentes']!=[]){
			foreach($_POST['gerentes'] as $p){
				$strgerentes.=$p.",";
			}
			$strgerentes = substr($strgerentes, 0, -1);
		}
		$strconsultores = "";
		if($_POST['consultores']!=[]){
			foreach($_POST['consultores'] as $p){
				$strconsultores.=$p.",";
			}
			$strconsultores = substr($strconsultores, 0, -1);
		}
		$strtiendas = "";
		if($_POST['tiendas']!=[]){
			foreach($_POST['tiendas'] as $p){
				$strtiendas.=$p.",";
			}
			$strtiendas = substr($strtiendas, 0, -1);
		}
		$strsecciones = "";
		if($_POST['secciones']!=[]){
			foreach($_POST['secciones'] as $p){
				$strsecciones.="'".$p."',";
			}
			$strsecciones = substr($strsecciones, 0, -1);
		}

		//Primero obtenemos los rounds
		$rounds = $obj1->getRoundsIds("name IN (".($strPeriodos!=NULL?$strPeriodos:"'x'").") AND type IN (".($strTipos!=NULL?$strTipos:"'x'").")");

		//despues obtenemos los location_id de las tiendas asignadas a los usuarios filtrados
		$usrs = $strgerentes.($strgerentes!=""?",":"").$strconsultores;
		$idsL = $obj2->getLocationsIds("id IN (".($usrs!=NULL?$usrs:0).")");

		//filtramos por regiones, estados y tiendas
		$locations = $obj3->getLocationsIds("(id IN (".($idsL!=NULL?$idsL:0).") AND id IN (".($strtiendas!=NULL?$strtiendas:0).")) AND regional IN (".($strregiones!=NULL?$strregiones:"'x'").") AND state_name IN (".($strestados!=NULL?$strestados:"'x'").")");

		//Por ultimo filtramos las visitas
		$visits = $obj4->getAuditsDashboard("a.round_id IN (".($rounds!=NULL?$rounds:0).") AND a.location_id IN (".($locations!=NULL?$locations:0).") AND s.value_3 IN (".($strclasificaciones!=NULL?$strclasificaciones:"'x'").") $filtroFechas");

		//juntamos los ids de las visitas para las siguientes consultas
		$strVisitas='';
		foreach($visits as $v){
			$strVisitas.=$v['id'].',';
		}
		$strVisitas = substr($strVisitas, 0, -1);

		//top 5 question con mas oportunidades
		$top5FS = $obj5->getTopBottomOpp("ao.audit_id IN (".($strVisitas!=''?$strVisitas:0).") AND ci.section_number>1 AND ci.section_number<12", "DESC", count($visits));
		$top5PM = $obj5->getTopBottomOpp("ao.audit_id IN (".($strVisitas!=''?$strVisitas:0).") AND ci.section_number>11", "DESC", count($visits));
		//todas las question con sus opp
		$allOpp = $obj5->getAllOpp("ao.audit_id IN (".($strVisitas!=''?$strVisitas:0).") AND ci.section_number>1 AND ci.main_section IN (".($strsecciones!=''?$strsecciones:0).")", "DESC", count($visits));
		//Promedios por secciones
		$pSecc = $obj5->getPromedioSecc("audit_id IN (".($strVisitas!=''?$strVisitas:0).") AND section_number>1", ($strVisitas!=''?$strVisitas:0), count($visits), $strsecciones);

		//die(var_dump($pSecc['data']));
		//puntuacion gerentes y consultores
		//primero obtenemos todos los gerentes y consultores segun los filtros
		$arrGerentes = [];
		$arrConsultores = [];
		$arrLocations = explode(',', $locations);
		foreach($arrLocations as $l){
			$gerentes = $obj2->getUsers("FIND_IN_SET($l, location_id) AND role_id = 17");
			foreach($gerentes as $g){
				if($arrGerentes[$g['id']]==NULL)$arrGerentes[$g['id']]=array(
					"id"=>$g['id'],
					"name"=>$g['name'],
					"location_id"=>$g['location_id']
				);
			}
			$consultores = $obj2->getUsers("FIND_IN_SET($l, location_id) AND role_id = 14");
			foreach($consultores as $g){
				if($arrConsultores[$g['id']]==NULL)$arrConsultores[$g['id']]=array(
					"id"=>$g['id'],
					"name"=>$g['name'],
					"location_id"=>$g['location_id']
				);
			}
		}
		foreach($arrGerentes as $g){
			$visitsG = $obj4->getAuditsDashboard("a.round_id IN (".($rounds!=NULL?$rounds:0).") AND a.location_id IN (".($g['location_id']!=NULL?$g['location_id']:0).") AND s.value_3 IN (".($strclasificaciones!=NULL?$strclasificaciones:"'x'").")");
			//die(count($visitsG));
			$sumaG = 0;
			foreach($visitsG as $v){
				$sumaG+=$v['value_4'];
			}
			$promedioG = (count($visitsG)>0?$sumaG/count($visitsG):0);
			$arrGerentes[$g['id']]['promedio'] = $promedioG;
		}
		foreach($arrConsultores as $g){
			$visitsC = $obj4->getAuditsDashboard("a.round_id IN (".($rounds!=NULL?$rounds:0).") AND a.location_id IN (".($g['location_id']!=NULL?$g['location_id']:0).") AND s.value_3 IN (".($strclasificaciones!=NULL?$strclasificaciones:"'x'").")");
			//die(count($visitsG));
			$sumaC = 0;
			foreach($visitsC as $v){
				$sumaC+=$v['value_4'];
			}
			$promedioC = (count($visitsC)>0?$sumaC/count($visitsC):0);
			$arrConsultores[$g['id']]['promedio'] = $promedioC;
		}
		//Puntuacion por estados
		//Primero obtenemos todos los estados y regiones con sus locations_id
		$arrEstados = [];
		$arrRegiones = [];
		foreach($arrLocations as $l){
			$tiendas = $obj3->getLocation([], "id = $l");
			foreach($tiendas as $g){
				if($arrEstados[$g['state_name']]['locations_id']==NULL){
					$arrEstados[$g['state_name']]['locations_id']=array($g['id']);
				}else{
					if(!in_array($g['id'], $arrEstados[$g['state_name']]['locations_id']))array_push($arrEstados[$g['state_name']]['locations_id'], $g['id']);
				}
				$arrEstados[$g['state_name']]['name']=$g['state_name'];
				if($arrRegiones[$g['regional']]['locations_id']==NULL){
					$arrRegiones[$g['regional']]['locations_id']=array($g['id']);
				}else{
					if(!in_array($g['id'], $arrRegiones[$g['regional']]['locations_id']))array_push($arrRegiones[$g['regional']]['locations_id'], $g['id']);
				}
				$arrRegiones[$g['regional']]['name']=$g['regional'];
			}
		}
		//luego sacamos las visitas
		foreach($arrEstados as $g){
			$strloc = ($g['locations_id']!=NULL?implode(',', $g['locations_id']):NULL);
			$visitsE = $obj4->getAuditsDashboard("a.round_id IN (".($rounds!=NULL?$rounds:0).") AND a.location_id IN (".($strloc!=NULL?$strloc:0).") AND s.value_3 IN (".($strclasificaciones!=NULL?$strclasificaciones:"'x'").")");
			//die(count($visitsG));
			$sumaE = 0;
			foreach($visitsE as $v){
				$sumaE+=$v['value_4'];
			}
			$promedioE = (count($visitsE)>0?$sumaE/count($visitsE):0);
			$arrEstados[$g['name']]['promedio'] = $promedioE;
		}
		foreach($arrRegiones as $g){
			$strlor = ($g['locations_id']!=NULL?implode(',', $g['locations_id']):NULL);
			$visitsR = $obj4->getAuditsDashboard("a.round_id IN (".($rounds!=NULL?$rounds:0).") AND a.location_id IN (".($strlor!=NULL?$strlor:0).") AND s.value_3 IN (".($strclasificaciones!=NULL?$strclasificaciones:"'x'").")");
			//die(count($visitsG));
			$sumaR = 0;
			foreach($visitsR as $v){
				$sumaR+=$v['value_4'];
			}
			$promedioR = (count($visitsR)>0?$sumaR/count($visitsR):0);
			$arrRegiones[$g['name']]['promedio'] = $promedioR;
		}

		
		$suma=0;
		$suma2=0;
		$suma3=0;
		$noEntrada=0;
		$arrLocations = explode(',',$locations);
		$stores=[];
		$zonaExceléncia=0;
		$zonaCualidade=0;
		$zonaAtencao=0;
		$zonaCritica=0;
		$fsv=0;
		$fsn=0;
		$fsr=0;
		$pmv=0;
		$pmn=0;
		$pmr=0;
		$fsgg=0;
		$pmgg=0;
		//die($visits);
		foreach($visits as $v){
			$suma+=$v['value_4'];
			$suma2+=$v['value_1'];
			$suma3+=$v['value_2'];
			if($v['value_1']>79){
				$fsgg++;
			}
			if($v['value_2']>79){
				$pmgg++;
			}
			if($v['value_4']==0)$noEntrada++;
			if(in_array($v['location_id'], $arrLocations))if(!in_array($v['location_id'], $stores))array_push($stores, $v['location_id']);
			if($v['value_3']=='ZONA DE EXCELÊNCIA'){
				$zonaExceléncia++;
			}else if($v['value_3']=='ZONA DE QUALIDADE'){
				$zonaCualidade++;
			}else if($v['value_3']=='ZONA DE ATENÇÃO'){
				$zonaAtencao++;
			}else if($v['value_3']=='ZONA CRÍTICA'){
				$zonaCritica++;
			}
			if($v['value_1']>=90){
				$fsv++;
			}else if($v['value_1']>=80){
				$fsn++;
			}else{
				$fsr++;
			}
			if($v['value_2']>=90){
				$pmv++;
			}else if($v['value_2']>=80){
				$pmn++;
			}else{
				$pmr++;
			}
		}
		$promedio = (count($visits)>0?$suma/count($visits):0);
		$promediofs = (count($visits)>0?$suma2/count($visits):0);
		$promediopm = (count($visits)>0?$suma3/count($visits):0);
		$tiendasggfs = (count($visits)>0?($fsgg/count($visits))*100:0);
		$tiendasggpm = (count($visits)>0?($pmgg/count($visits))*100:0);
		$results = array(
			"visitas"=>count($visits),
			"tiendas"=>count($stores),
			"promedio"=>round($promedio, 2),
			"noEntrada"=>$noEntrada,
			"excelencia"=>$zonaExceléncia,
			"cuidado"=>$zonaCualidade,
			"atencion"=>$zonaAtencao,
			"critica"=>$zonaCritica,
			"promediofs"=>round($promediofs, 2),
			"promediopm"=>round($promediopm, 2),
			"fsgg"=>$fsgg,
			"pmgg"=>$pmgg,
			"tiendasggfs"=>round($tiendasggfs, 2),
			"tiendasggpm"=>round($tiendasggpm, 2),
			"top5FS"=>$top5FS,
			"top5PM"=>$top5PM,
			"gerentes"=>$arrGerentes,
			"consultores"=>$arrConsultores,
			"estados"=>$arrEstados,
			"regiones"=>$arrRegiones,
			"allOpp"=>$allOpp,
			"pSecc"=>$pSecc['data'],
			"scoreG"=>$pSecc['scoreG']
		);

		echo json_encode($results);
	}

	public function home()
	{
		require_once("Models/CountryModel.php");
		require_once("Models/UsuariosModel.php");
		require_once("Models/LocationModel.php");
		require_once("Models/Checklist_ItemModel.php");
		$objData = new UsuariosModel();
		$obj2 = new CountryModel();
		$obj3 = new LocationModel();
		$obj4 = new Checklist_ItemModel();
		$data['page_id'] = 1;
		$data['page_tag'] = "Início";
		$data['page_title'] = "Início";
		$data['page_name'] = "Início";
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
		$periodos = array_unique(array_column(selectRound(['name']), 'name'));
		$data['regionales'] = $objData->getRegionales();
		$data['consultores'] = $objData->getUsers("status=1 AND role_id = 14");
		$data['gerentes'] = $objData->getUsers("status=1 AND role_id = 17");
		$data['estados'] = $obj2->getEstados();
		$data['regionales'] = $obj2->getRegionales();
		$data['lojas'] = $obj3->getLocation(['id', 'name', 'number'], "status = 1");
		$data['secciones'] = $obj4->getSeccs("main_section NOT IN ('Informações Iniciais')");

		$data['periods'] = array();
		foreach($periodos as $p){
			$period = explode('Round ', $p)[1];
			$period = explode(' ', $period)[0];
			$year = explode(' ', $period)[1];
			switch($period){
				case "1":
					$data['periods'][$p]=['Janeiro', 'Fevereiro', 'Março'];
					break;
				case "2":
					$data['periods'][$p]=['Abril', 'Maio', 'Junho'];
					break;
				case "3":
					$data['periods'][$p]=['Julho', 'Agosto', 'Setembro'];
					break;
				case "4":
					$data['periods'][$p]=['Outubro', 'Novembro', 'Dezembro'];
					break;
			}
		}

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