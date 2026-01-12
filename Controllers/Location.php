<?php

class Location extends Controllers
{

	public function __construct()
	{
		parent::__construct();
		session_start();
		//session_regenerate_id(true);
		if (empty($_SESSION['login'])) {
			header('location: ' . base_url() . '/login');
		}
		$this->permission = $_SESSION['userData']['permission']['Tiendas'];
	}

	public function location()
	{
		if (!$this->permission['r']) {
			header('Location: ' . base_url());
		}

		$data['page_tag'] = "Locations";
		$data['page_title'] = "Locations";
		$data['page_name'] = "locations";
		$data['page-functions_js'] = "functions_locations.js?301023";
		$data['permission'] = $this->permission;
		$data['rol'] = $_SESSION['userData']['role']['id'];

		$data['countries'] = array_unique(array_column($this->model->getLocation(['country'], 'country IS NOT NULL'), 'country'));

		$this->views->getView($this, "location", $data);
	}

	public function getLocations()
	{
		$fnT = translate($_SESSION['userData']['default_language']);

		$tmp = $this->model->getLocation(['id', 'number', 'name', 'country', 'city', 'address_1', 'email', 'shop_type', 'status', 'id actions']);
		$locations = array_map(function ($item) use ($fnT) {
			$item['status'] = ($item['status']==1?'Open':'Closed');
			$updStatus = $item['status'] == 'Active' ? 0 : 1;
			$item['actions'] = "<div class='btn-group dropleft' role='group'>
				<button type='button' class='btn btn-sm btn-secondary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>" . $fnT('Ações') . "</button>
				<div class='dropdown-menu'>
					<button class='dropdown-item' onclick='UpdLocation({$item['actions']})'" . ($this->permission['u']? '' : 'disabled') . ">". $fnT('Atualizar') ."</button>
					<button class='dropdown-item' onclick='UpdStatusLocation({$item['actions']}, $updStatus)'" . ($this->permission['u']? '' : 'disabled') . ">". $fnT($updStatus? 'Activate' : 'Inactivate') ."</button>
					<button class='dropdown-item text-danger' onclick='delLocation({$item['actions']})'" . ($this->permission['d']? '' : 'disabled') . ">" . $fnT('Excluir') . " <i class='fa fa-trash'></i></button>
				</div>
			</div>";
			return $item;
		}, $tmp);
		die(json_encode($locations));
	}

	public function updStatusLocation()
	{
		if (!$this->permission['u']) {
			die(http_response_code(401));
		}
		$status = $this->model->updateLocation(['status' => $_POST['status'] ? 'Active' : 'Inactive'], "id=" . $_POST['location_id']);
		$this->model->setLog($_SESSION['userData']['id'], $_POST['status'] ? "Active location id: $_POST[location_id]" : "Inactive location id: $_POST[location_id]");
		die(json_encode(['status' => $status]));
	}

	public function delLocation()
	{
		if (!$this->permission['d']) {
			die(http_response_code(401));
		}

		$tmp = selectAuditList(['id'], "location_id=" . $_POST['location_id']);
		if (!empty($tmp)) {
			die(json_encode(['status' => -1]));
		}

		$status = $this->model->deleteLocation("id=" . $_POST['location_id']);
		$this->model->setLog($_SESSION['userData']['id'], 'delete location id: ' . $_POST['location_id']);
		die(json_encode(['status' => $status ? 1 : 0]));
	}

	public function massInsertion()
	{
		require_once 'Models/UsuariosModel.php';
		$objUsuarios = new UsuariosModel();

		if (!$this->permission['u'] && !$this->permission['w']) {
			die(http_response_code(401));
		}

		if (
			!empty($_POST['columns']) &&
			!empty($_POST['data'])
		) {
			$_POST['data'] = json_decode($_POST['data']);
			$_POST['columns'] = json_decode($_POST['columns']);

			$number = 'TOKEN';
			$name = 'NOME DO PDV';
			$address_1 = 'ENDEREÇO';
			$city = 'CIDADE';
			$state_code = 'ESTADO';
			$state_name = 'ESTADO';
			$status = 'status';
			$language = 'language';
			$email = 'E-MAIL DA LOJA';
			$tipoLoja = 'TIPO DE LOJA';
			$regional = 'REGIONAL';
			$executivo = 'EXECUTIVO';
			$executivo_email = 'E-MAIL EXECUTIVO';
			$gerente_regional = 'GERENTE REGIONAL';
			$gerente_regional_email = 'E-MAIL GERENTE REGIONAL';
			$consultor = 'CONSULTOR';
			$consultor_email = 'E-MAIL CONSULTOR';
			$open_date = 'DATA DE ABERTURA';
			

			$numberInd = array_search($number, $_POST['columns']);
			$nameInd = array_search($name, $_POST['columns']);
			$address_1Ind = array_search($address_1, $_POST['columns']);
			$cityInd = array_search($city, $_POST['columns']);
			$state_codeInd = array_search($state_code, $_POST['columns']);
			$state_nameInd = array_search($state_name, $_POST['columns']);
			$statusInd = array_search($status, $_POST['columns']);
			$languageInd = array_search($language, $_POST['columns']);
			$emailInd = array_search($email, $_POST['columns']);
			$tipoLojaInd = array_search($tipoLoja, $_POST['columns']);
			$regionalInd = array_search($regional, $_POST['columns']);
			$executivoInd = array_search($executivo, $_POST['columns']);
			$executivo_emailInd = array_search($executivo_email, $_POST['columns']);
			$gerente_regionalInd = array_search($gerente_regional, $_POST['columns']);
			$gerente_regional_emailInd = array_search($gerente_regional_email, $_POST['columns']);
			$consultorInd = array_search($consultor, $_POST['columns']);
			$consultor_emailInd = array_search($consultor_email, $_POST['columns']);
			$open_dateInd = array_search($open_date, $_POST['columns']);

			

			$existsLocation = $this->model->existsLocation(array_column($_POST['data'], $numberInd));
			$cleanLocations = [];
			$request = [];
			$users = [];
			$index = 1;

			foreach ($_POST['data'] as $location) {
				$location = $this->dataPrepare($location);
				$index++;

				if (empty($location[$numberInd])) {
					continue;
				}
				if (!preg_match("/^.{1,512}$/", $location[$nameInd])) {
					$request['errors'][] = [$location[$numberInd], 'Invalid shop name', $index];
					continue;
				}
				$email = emailFilter($location[$emailInd]);
				if (empty($email)) {
					$request['errors'][] = [$location[$numberInd], 'Invalid email', $index];
					continue;
				}

				if($location[$statusInd]=='Open' || $location[$statusInd]=='Active')$location[$statusInd]=1;
				if($location[$statusInd]=='Close' || $location[$statusInd]=='Closed')$location[$statusInd]=1;
				$country_id = 38;
				$status = 1;
				$lan = 'por';

				$insertUsers = false;
				if (in_array($location[$numberInd], $existsLocation)) {
					$tmp = [
						'name' => $location[$nameInd],
						'address_1' => $location[$address_1Ind],
						'city' => $location[$cityInd],
						'state_code' => $location[$state_codeInd],
						'state_name' => $location[$state_nameInd],
						'country_id' => $country_id,
						'email' => $location[$emailInd],
						'status' => $status,
						'tipo_de_loja' => $location[$tipoLojaInd],
						'regional' => $location[$regionalInd],
						'open_date' => $location[$open_dateInd],
						
					];
					$update = $this->model->updateLocation($tmp, "number = '".$location[$numberInd]."'");
					if ($update) {
						$request['locations'][] = [$location[$numberInd], 'update'];
						$insertUsers = true;
						$numbers_locations_u[] = $location[$numberInd];
					}else{
					}
				} else {
					$tmp = [
						'name' => $location[$nameInd],
						'number' => $location[$numberInd],
						'address_1' => $location[$address_1Ind],
						'city' => $location[$cityInd],
						'state_code' => $location[$state_codeInd],
						'state_name' => $location[$state_nameInd],
						'country_id' => $country_id,
						'email' => $location[$emailInd],
						'status' => $status,
						'tipo_de_loja' => $location[$tipoLojaInd],
						'regional' => $location[$regionalInd],
						'open_date' => $location[$open_dateInd],
					];
					$insert = $this->model->insertLocation($tmp);
					if ($insert) {

						$request['locations'][] = [$location[$numberInd], 'insert'];
						$insertUsers = true;
						$numbers_locations_i[] = $location[$numberInd];
					}
				}

				if ($insertUsers) {
					$cleanLocations[] = $location[$numberInd];
					$objUsuarios->cleanUsersLocations([$location[$numberInd]]); //le quitamos la tienda a los usuarios que previamente ya la tenian asignada

					foreach (explode(',', $email) as $eGM) {
						$users[$eGM . '|' . 10][] = $location[$numberInd];
					}

				}

				$rs = $this->model->getLocation(['id'], "number='".$location[$numberInd]."'");
				$idLocation = $rs[0]['id'];
				//die('idL:'.$idLocation);

				//Executivo
				if(strpos($location[$executivo_emailInd], ',') !== false){
					foreach(explode(',', $location[$executivo_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$executivoInd], $e, '1', $country_id, 18, 1, 1, $lan, $idLocation); //crea/actualiza al Executivo
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$executivo_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$executivoInd], $location[$executivo_emailInd], '1', $country_id, 18, 1, 1, $lan, $idLocation); //crea/actualiza al Executivo
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
				//Gerente Regional
				if(strpos($location[$gerente_regional_emailInd], ',') !== false){
					foreach(explode(',', $location[$gerente_regional_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$gerente_regionalInd], $e, '1', $country_id, 17, 1, 1, $lan, $idLocation); //crea/actualiza al director regional de franquisia
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$gerente_regional_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$gerente_regionalInd], $location[$gerente_regional_emailInd], '1', $country_id, 17, 1, 1, $lan, $idLocation); //crea/actualiza al director regional de franquisia
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
				//Consultor
				if(strpos($location[$consultor_emailInd], ',') !== false){
					foreach(explode(',', $location[$consultor_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$consultorInd], $e, '1', $country_id, 14, 1, 1, $lan, $idLocation); //crea/actualiza al director regional de franquisia
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$consultor_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$consultorInd], $location[$consultor_emailInd], '1', $country_id, 14, 1, 1, $lan, $idLocation); //crea/actualiza al director regional de franquisia
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
			}

			//$objUsuarios->cleanUsersLocations($cleanLocations);
			$tmpUsers = [];
			foreach ($users as $us => $loc) {
				$us = explode('|', $us);
				$tmp = $objUsuarios->reviseUser($us[0], $us[1], $loc);
				$tmpUsers[$us[0]] = [$tmp['locations'], $tmp['action'], $us[0], $this->getNameRole($us[1])];
			}
			$request['users'] = array_values($tmpUsers);

			die(json_encode($request));
		}

		die(json_encode(['status' => 0]));
	}

	public function addLocation()
	{
		if (!$this->permission['u']) {
			die(http_response_code(401));
		}

		require_once 'Models/LocationModel.php';

		$_POST = $this->dataPrepare($_POST);

		if($_POST['inpNew']=='1'){ //si es nuevo el registro
			// Convertir el string a un array para que funcione con el modelo
			$number_location = $this->model->existsLocation([$_POST['number']]);

			if (!$number_location) {
				$tmp = [
					'number' => $_POST['number'],
					'name' => $_POST['name'],
					'phone' => $_POST['phone'],
					'status' => $_POST['status'],
					'shop_type' => $_POST['shop_type'],
					'country' => $_POST['country'],
					'email' => $_POST['email'],
					'country_id' => 1,
					'city' => $_POST['city'],
					'address_1' => $_POST['address_1'],
					'state_code' => $_POST['state_code'],
					'state_name' => $_POST['state_name']
				];
				$insert = $this->model->insertLocation($tmp);

				$logResult = $this->model->setLog($_SESSION['userData']['id'], "Insert new location number: {$_POST['number']}");

				die(json_encode(['status' => $insert ? 1 : 0]));
			} else {
				die(json_encode(['status' => 2]));
			}
		}else{ //si ya existe y se va a editar
			$tmp = [
				'number' => $_POST['number'],
				'name' => $_POST['name'],
				'phone' => $_POST['phone'],
				'status' => $_POST['status'],
				'shop_type' => $_POST['shop_type'],
				'country' => $_POST['country'],
				'email' => $_POST['email'],
				'country_id' => 1,
				'city' => $_POST['city'],
				'address_1' => $_POST['address_1'],
				'state_code' => $_POST['state_code'],
				'state_name' => $_POST['state_name']
			];

			$insert = $this->model->updateLocation($tmp, "number = '".$_POST['number']."'");

			$logResult = $this->model->setLog($_SESSION['userData']['id'], "Edit store: {$_POST['number']}");

			die(json_encode(['status' => $insert ? 1 : 0]));
		}
	}


	public function insLocation(){
		if(!$this->permission['u']){
			die(http_response_code(401));	
		}
	
		require_once 'Models/UsuariosModel.php';
		$objUsuarios = new UsuariosModel();

		$_POST = $this->dataPrepare($_POST);
		$country_id = $this->getDataCountry($_POST['country'])['id'];
		$tmp = [
			'country_id'		=> $country_id,
			'status'			=> $_POST['status'],
			'name'				=> $_POST['name'],
			'address_1'			=> $_POST['address_1'],
			'city'				=> $_POST['city'],
			'state_code'		=> $_POST['state_code'],
			'state_name'		=> $_POST['state_name'],
			'country'			=> $_POST['country'],
			'shop_type'			=> $_POST['shop_type'],
			'phone'				=> $_POST['phone'],
			'email'				=> $_POST['email']
		];

		$update = $this->model->updateLocation($tmp, "number = '{$_POST['number']}'");
		$objUsuarios->cleanUsersLocations([$_POST['number']]);

		if($update){
			foreach(emailFilter($_POST['email'], true) as $e){
				$objUsuarios->reviseUser($e, 10, [$_POST['number']]);
			}
		}
		
		die(json_encode(['status' => $update? 1 : 0]));
	}

	private function getDataCountry($key){
		$dictionary = [
			'MEXICO' 					=> [1, 'Mexico'],
			'CANADA' 					=> [6, 'Canada'],
			'SINGAPORE' 					=> [23, 'Singapore'],
			'UNITED ARAB EMIRATES' 					=> [29, 'United Arab Emirates'],
		];

		$key = strtoupper($key);
		$request = [
			'id' => $dictionary[$key][0],
			'name' => $dictionary[$key][1]
		];
		return $request;
	}

	private function getNameRole($key)
	{
		$dictionary = [
			10 => 'Shop / GM'
		];
		return $dictionary[$key];
	}

	public function getFullDataLocation($locationId){
		require_once 'Models/StatisticsModel.php';
		$objStatistics = new StatisticsModel();

		$response = $objStatistics->getLocations($locationId);
		die(json_encode($response[0]));
	}

	private function dataPrepare($data){
		$request = array_map(function($item){
			$tmp = trim($item);
			$tmp = str_replace(["'", '"'], ['´', '´'], $tmp);
			$tmp = empty($tmp) ? null : $tmp;
			return $tmp;
		}, $data);
		return $request;
	}
}
