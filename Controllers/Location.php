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

		$data['countries'] = array_unique(array_column($this->model->getLocation(['country'], 'country IS NOT NULL'), 'country'));

		$this->views->getView($this, "location", $data);
	}

	public function getLocations()
	{
		$fnT = translate($_SESSION['userData']['default_language']);

		$tmp = $this->model->getLocation(['id', 'number', 'name', 'country', 'city', 'address_1', 'email', 'shop_type', 'status', 'id actions']);
		$locations = array_map(function ($item) use ($fnT) {
			$updStatus = $item['status'] == 'Active' ? 0 : 1;
			$item['actions'] = "<div class='btn-group dropleft' role='group'>
				<button type='button' class='btn btn-sm btn-secondary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>" . $fnT('Actions') . "</button>
				<div class='dropdown-menu'>
					<button class='dropdown-item' onclick='UpdLocation({$item['actions']})'" . ($this->permission['u']? '' : 'disabled') . ">". $fnT('Update') ."</button>
					<button class='dropdown-item' onclick='UpdStatusLocation({$item['actions']}, $updStatus)'" . ($this->permission['u']? '' : 'disabled') . ">". $fnT($updStatus? 'Activate' : 'Inactivate') ."</button>
					<button class='dropdown-item text-danger' onclick='delLocation({$item['actions']})'" . ($this->permission['d']? '' : 'disabled') . ">" . $fnT('Delete') . " <i class='fa fa-trash'></i></button>
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

			$number = 'Shop_Number';
			$name = 'Shop_Name';
			$address_1 = 'Address';
			$city = 'City';
			$state_code = 'State_Code';
			$state_name = 'State_Name';
			$zip = 'Zip';
			$country = 'Country';
			$phone = 'Phone_Number';
			$email = 'Email';
			$shop_type = 'Shop_Type';
			$status = 'Status';

			$numberInd = array_search($number, $_POST['columns']);
			$nameInd = array_search($name, $_POST['columns']);
			$address_1Ind = array_search($address_1, $_POST['columns']);
			$cityInd = array_search($city, $_POST['columns']);
			$state_codeInd = array_search($state_code, $_POST['columns']);
			$state_nameInd = array_search($state_name, $_POST['columns']);
			$zipInd = array_search($zip, $_POST['columns']);
			$countryInd = array_search($country, $_POST['columns']);
			$phoneInd = array_search($phone, $_POST['columns']);
			$emailInd = array_search($email, $_POST['columns']);
			$shop_typeInd = array_search($shop_type, $_POST['columns']);
			$statusInd = array_search($status, $_POST['columns']);

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
				if (!in_array($location[$shop_typeInd], ['Food Court', 'In Line', 'Drive Thru'])) {
					$request['errors'][] = [$location[$numberInd], 'Invalid shop type', $index];
					continue;
				}
				if (!preg_match("/^[0-9\/]{4,}$/", $location[$numberInd])) {
					$request['errors'][] = [$location[$numberInd], 'Invalid shop number', $index];
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

				$country_id = $this->getDataCountry($location[$countryInd])['id'];
				if (empty($country_id)) {
					$request['errors'][] = [$location[$numberInd], 'Invalid country', $index];
					continue;
				}

				$insertUsers = false;
				if (in_array($location[$numberInd], $existsLocation)) {
					$tmp = [
						'name' => $location[$nameInd],
						'address_1' => $location[$address_1Ind],
						'city' => $location[$cityInd],
						'state_code' => $location[$state_codeInd],
						'state_name' => $location[$state_nameInd],
						'zip' => $location[$zipInd],
						'country_id' => $country_id,
						'country' => $location[$countryInd],
						'phone' => $location[$phoneInd],
						'email' => $location[$emailInd],
						'shop_type' => $location[$shop_typeInd],
						'status' => $location[$statusInd]
					];
					$update = $this->model->updateLocation($tmp, "number = '$location[$numberInd]'");
					if ($update) {
						$request['locations'][] = [$location[$numberInd], 'update'];
						$insertUsers = true;
						$numbers_locations_u[] = $location[$numberInd];
					}
				} else {
					$tmp = [
						'number' => $location[$numberInd],
						'name' => $location[$nameInd],
						'address_1' => $location[$address_1Ind],
						'city' => $location[$cityInd],
						'state_code' => $location[$state_codeInd],
						'state_name' => $location[$state_nameInd],
						'zip' => $location[$zipInd],
						'country_id' => $country_id,
						'country' => $location[$countryInd],
						'phone' => $location[$phoneInd],
						'email' => $location[$emailInd],
						'shop_type' => $location[$shop_typeInd],
						'status' => $location[$statusInd]
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

					foreach (explode(',', $email) as $eGM) {
						$users[$eGM . '|' . 10][] = $location[$numberInd];
					}

				}
			}

			$objUsuarios->cleanUsersLocations($cleanLocations);
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
			'email'				=> $_POST['emails_gm']
		];

		$update = $this->model->updateLocation($tmp, "number = '{$_POST['number']}'");
		$objUsuarios->cleanUsersLocations([$_POST['number']]);

		if($update){
			foreach(emailFilter($_POST['emails_gm'], true) as $e){
				$objUsuarios->reviseUser($e, 10, [$_POST['number']]);
			}
		}
		
		die(json_encode(['status' => $update? 1 : 0]));
	}

	private function getDataCountry($key){
		$dictionary = [
			'MEXICO' 					=> [1, 'Mexico']
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
