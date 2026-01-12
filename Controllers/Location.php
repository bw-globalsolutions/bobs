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

			$number = 'shop_number';
			$name = 'shop_name';
			$address_1 = 'address';
			$city = 'city';
			$state_code = 'state_code';
			$state_name = 'state_name';
			$zip = 'zip';
			$country = 'country';
			$phone = 'phone_number';
			$shop_type = 'shop_type';
			$status = 'status';
			$language = 'language';
			$email = 'shop_email';
			$restaurant_segment = 'restaurant_segment';
			$latitude = 'latitude';
			$longitude = 'longitude';
			$restaurant_open_date = 'restaurant_open_date';
			$restaurant_original_open_year = 'restaurant_original_open_year';
			$restaurant_close_date = 'restaurant_close_date';
			$restaurant_temp_close_date = 'restaurant_temp_close_date';
			$restaurant_reopen_date = 'restaurant_reopen_date';
			$franchisee_agreement_expiration_date = 'franchisee_agreement_expiration_date';
			$venue_type = 'venue_type';
			$venue_detail = 'venue_detail';
			$DMA_number = 'DMA_number';
			$DMA_name = 'DMA_name';
			$market_number = 'market_number';
			$zone_number = 'zone_number';
			$region_number = 'region_number';
			$NFT_region = 'NFT_region';
			$zone_director_name = 'zone_director_name';
			$zone_director_phone = 'zone_director_phone';
			$zone_director_email = 'zone_director_email';
			$market_leader_name = 'market_leader_name';
			$market_leader_phone = 'market_leader_phone';
			$market_leader_email = 'market_leader_email';
			$national_field_trainer_name = 'national_field_trainer_name';
			$national_field_trainer_phone = 'national_field_trainer_phone';
			$national_field_trainer_email = 'national_field_trainer_email';
			$field_marketing_contact_name = 'field_marketing_contact_name';
			$field_marketing_contact_phone = 'field_marketing_contact_phone';
			$field_marketing_contact_email = 'field_marketing_contact_email';
			$regional_franchise_director_name = 'regional_franchise_director_name';
			$regional_franchise_director_phone = 'regional_franchise_director_phone';
			$regional_franchise_director_email = 'regional_franchise_director_email';
			$regional_manager_name = 'regional_manager_name';
			$regional_manager_phone = 'regional_manager_phone';
			$regional_manager_email = 'regional_manager_email';
			$franchise_president_name = 'franchise_president_name';
			$franchise_president_phone = 'franchise_president_phone';
			$franchise_president_email = 'franchise_president_email';
			$franchise_vp_of_ops_name = 'franchise_vp_of_ops_name';
			$franchise_vp_of_ops_phone = 'franchise_vp_of_ops_phone';
			$franchise_vp_of_ops_email = 'franchise_vp_of_ops_email';
			$area_franchise_lead_name = 'area_franchise_lead_name';
			$area_franchise_lead_phone = 'area_franchise_lead_phone';
			$area_franchise_lead_email = 'area_franchise_lead_email';
			$training_restaurant_indicator = 'training_restaurant_indicator';
			$training_general_manager_name = 'training_general_manager_name';
			$ceo = 'ceo';
			$franchise_entity_name = 'franchise_entity_name';
			$sub_franchise_entity_name = 'sub_franchise_entity_name';
			$franchise_owner_name = 'franchise_owner_name';
			$franchise_owner_address = 'franchise_owner_address';
			$franchise_owner_city = 'franchise_owner_city';
			$franchise_owner_state = 'franchise_owner_state';
			$franchise_owner_zip_code = 'franchise_owner_zip_code';
			$franchise_owner_country_name = 'franchise_owner_country_name';
			$franchise_owner_phone = 'franchise_owner_phone';
			$franchise_owner_email = 'franchise_owner_email';
			$franchise_owner_base_of_ops_dma = 'franchise_owner_base_of_ops_dma';
			$franchise_owner_base_of_ops_state = 'franchise_owner_base_of_ops_state';
			$franchise_contact_name = 'franchise_contact_name';
			$franchise_contact_phone = 'franchise_contact_phone';
			$franchise_contact_email = 'franchise_contact_email';
			$franchise_additional_contact_emails = 'franchise_additional_contact_emails';
			$brand = 'brand';
			$pos_type = 'pos_type';
			$image_type = 'image_type';
			$reimage_completion_date = 'reimage_completion_date';
			$distribution_center = 'distribution_center';
			$restaurant_drive_thru_type = 'restaurant_drive_thru_type';
			$guest_cx_indicator = 'guest_cx_indicator';
			

			$numberInd = array_search($number, $_POST['columns']);
			$nameInd = array_search($name, $_POST['columns']);
			$address_1Ind = array_search($address_1, $_POST['columns']);
			$cityInd = array_search($city, $_POST['columns']);
			$state_codeInd = array_search($state_code, $_POST['columns']);
			$state_nameInd = array_search($state_name, $_POST['columns']);
			$zipInd = array_search($zip, $_POST['columns']);
			$countryInd = array_search($country, $_POST['columns']);
			$phoneInd = array_search($phone, $_POST['columns']);
			$shop_typeInd = array_search($shop_type, $_POST['columns']);
			$statusInd = array_search($status, $_POST['columns']);
			$languageInd = array_search($language, $_POST['columns']);
			$emailInd = array_search($email, $_POST['columns']);
			$restaurant_segmentInd = array_search($restaurant_segment, $_POST['columns']);
			$latitudeInd = array_search($latitude, $_POST['columns']);
			$longitudeInd = array_search($longitude, $_POST['columns']);
			$restaurant_open_dateInd = array_search($restaurant_open_date, $_POST['columns']);
			$restaurant_original_open_yearInd = array_search($restaurant_original_open_year, $_POST['columns']);
			$restaurant_close_dateInd = array_search($restaurant_close_date, $_POST['columns']);
			$restaurant_temp_close_dateInd = array_search($restaurant_temp_close_date, $_POST['columns']);
			$restaurant_reopen_dateInd = array_search($restaurant_reopen_date, $_POST['columns']);
			$franchisee_agreement_expiration_dateInd = array_search($franchisee_agreement_expiration_date, $_POST['columns']);
			$venue_typeInd = array_search($venue_type, $_POST['columns']);
			$venue_detailInd = array_search($venue_detail, $_POST['columns']);
			$DMA_numberInd = array_search($DMA_number, $_POST['columns']);
			$DMA_nameInd = array_search($DMA_name, $_POST['columns']);
			$market_numberInd = array_search($market_number, $_POST['columns']);
			$zone_numberInd = array_search($zone_number, $_POST['columns']);
			$region_numberInd = array_search($region_number, $_POST['columns']);
			$NFT_regionInd = array_search($NFT_region, $_POST['columns']);
			$zone_director_nameInd = array_search($zone_director_name, $_POST['columns']);
			$zone_director_phoneInd = array_search($zone_director_phone, $_POST['columns']);
			$zone_director_emailInd = array_search($zone_director_email, $_POST['columns']);
			$market_leader_nameInd = array_search($market_leader_name, $_POST['columns']);
			$market_leader_phoneInd = array_search($market_leader_phone, $_POST['columns']);
			$market_leader_emailInd = array_search($market_leader_email, $_POST['columns']);
			$national_field_trainer_nameInd = array_search($national_field_trainer_name, $_POST['columns']);
			$national_field_trainer_phoneInd = array_search($national_field_trainer_phone, $_POST['columns']);
			$national_field_trainer_emailInd = array_search($national_field_trainer_email, $_POST['columns']);
			$field_marketing_contact_nameInd = array_search($field_marketing_contact_name, $_POST['columns']);
			$field_marketing_contact_phoneInd = array_search($field_marketing_contact_phone, $_POST['columns']);
			$field_marketing_contact_emailInd = array_search($field_marketing_contact_email, $_POST['columns']);
			$regional_franchise_director_nameInd = array_search($regional_franchise_director_name, $_POST['columns']);
			$regional_franchise_director_phoneInd = array_search($regional_franchise_director_phone, $_POST['columns']);
			$regional_franchise_director_emailInd = array_search($regional_franchise_director_email, $_POST['columns']);
			$regional_manager_nameInd = array_search($regional_manager_name, $_POST['columns']);
			$regional_manager_phoneInd = array_search($regional_manager_phone, $_POST['columns']);
			$regional_manager_emailInd = array_search($regional_manager_email, $_POST['columns']);
			$franchise_president_nameInd = array_search($franchise_president_name, $_POST['columns']);
			$franchise_president_phoneInd = array_search($franchise_president_phone, $_POST['columns']);
			$franchise_president_emailInd = array_search($franchise_president_email, $_POST['columns']);
			$franchise_vp_of_ops_nameInd = array_search($franchise_vp_of_ops_name, $_POST['columns']);
			$franchise_vp_of_ops_phoneInd = array_search($franchise_vp_of_ops_phone, $_POST['columns']);
			$franchise_vp_of_ops_emailInd = array_search($franchise_vp_of_ops_email, $_POST['columns']);
			$area_franchise_lead_nameInd = array_search($area_franchise_lead_name, $_POST['columns']);
			$area_franchise_lead_phoneInd = array_search($area_franchise_lead_phone, $_POST['columns']);
			$area_franchise_lead_emailInd = array_search($area_franchise_lead_email, $_POST['columns']);
			$training_restaurant_indicatorInd = array_search($training_restaurant_indicator, $_POST['columns']);
			$training_general_manager_nameInd = array_search($training_general_manager_name, $_POST['columns']);
			$ceoInd = array_search($ceo, $_POST['columns']);
			$franchise_entity_nameInd = array_search($franchise_entity_name, $_POST['columns']);
			$sub_franchise_entity_nameInd = array_search($sub_franchise_entity_name, $_POST['columns']);
			$franchise_owner_nameInd = array_search($franchise_owner_name, $_POST['columns']);
			$franchise_owner_addressInd = array_search($franchise_owner_address, $_POST['columns']);
			$franchise_owner_cityInd = array_search($franchise_owner_city, $_POST['columns']);
			$franchise_owner_stateInd = array_search($franchise_owner_state, $_POST['columns']);
			$franchise_owner_zip_codeInd = array_search($franchise_owner_zip_code, $_POST['columns']);
			$franchise_owner_country_nameInd = array_search($franchise_owner_country_name, $_POST['columns']);
			$franchise_owner_phoneInd = array_search($franchise_owner_phone, $_POST['columns']);
			$franchise_owner_emailInd = array_search($franchise_owner_email, $_POST['columns']);
			$franchise_owner_base_of_ops_dmaInd = array_search($franchise_owner_base_of_ops_dma, $_POST['columns']);
			$franchise_owner_base_of_ops_stateInd = array_search($franchise_owner_base_of_ops_state, $_POST['columns']);
			$franchise_contact_nameInd = array_search($franchise_contact_name, $_POST['columns']);
			$franchise_contact_phoneInd = array_search($franchise_contact_phone, $_POST['columns']);
			$franchise_contact_emailInd = array_search($franchise_contact_email, $_POST['columns']);
			$franchise_additional_contact_emailsInd = array_search($franchise_additional_contact_emails, $_POST['columns']);
			$brandInd = array_search($brand, $_POST['columns']);
			$pos_typeInd = array_search($pos_type, $_POST['columns']);
			$image_typeInd = array_search($image_type, $_POST['columns']);
			$reimage_completion_dateInd = array_search($reimage_completion_date, $_POST['columns']);
			$distribution_centerInd = array_search($distribution_center, $_POST['columns']);
			$restaurant_drive_thru_typeInd = array_search($restaurant_drive_thru_type, $_POST['columns']);
			$guest_cx_indicatorInd = array_search($guest_cx_indicator, $_POST['columns']);

			

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
				if (!in_array($location[$shop_typeInd], ['Franquicia', 'Corporativa', 'Franchise', 'Corporative'])) {
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
				if($location[$statusInd]=='Open')$location[$statusInd]=1;
				if($location[$statusInd]=='Close' || $location[$statusInd]=='Closed')$location[$statusInd]=0;

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
						'status' => $location[$statusInd],
						'restaurant_segment' => $location[$restaurant_segmentInd],
						'latitude' => $location[$latitudeInd],
						'longitude' => $location[$longitudeInd],
						'restaurant_open_date' => $location[$restaurant_open_dateInd],
						'restaurant_original_open_year' => $location[$restaurant_original_open_yearInd],
						'restaurant_close_date' => $location[$restaurant_close_dateInd],
						'restaurant_temp_close_date' => $location[$restaurant_temp_close_dateInd],
						'restaurant_reopen_date' => $location[$restaurant_reopen_dateInd],
						'franchisee_agreement_expiration_date' => $location[$franchisee_agreement_expiration_dateInd],
						'venue_type' => $location[$venue_typeInd],
						'venue_detail' => $location[$venue_detailInd],
						'DMA_number' => $location[$DMA_numberInd],
						'DMA_name' => $location[$DMA_nameInd],
						'market_number' => $location[$market_numberInd],
						'zone_number' => $location[$zone_numberInd],
						'region_number' => $location[$region_numberInd],
						'NFT_region' => $location[$NFT_regionInd],
						'zone_director_phone' => $location[$zone_director_phoneInd],
						'market_leader_phone' => $location[$market_leader_phoneInd],
						'national_field_trainer_phone' => $location[$national_field_trainer_phoneInd],
						'field_marketing_contact_phone' => $location[$field_marketing_contact_phoneInd],
						'regional_franchise_director_phone' => $location[$regional_franchise_director_phoneInd],
						'regional_manager_phone' => $location[$regional_manager_phoneInd],
						'franchise_president_phone' => $location[$franchise_president_phoneInd],
						'franchise_vp_of_ops_phone' => $location[$franchise_vp_of_ops_phoneInd],
						'area_franchise_lead_phone' => $location[$area_franchise_lead_phoneInd],
						'training_restaurant_indicator' => $location[$training_restaurant_indicatorInd],
						'training_general_manager_name' => $location[$training_general_manager_nameInd],
						'ceo' => $location[$ceoInd],
						'franchise_entity_name' => $location[$franchise_entity_nameInd],
						'sub_franchise_entity_name' => $location[$sub_franchise_entity_nameInd],
						'franchise_owner_address' => $location[$franchise_owner_addressInd],
						'franchise_owner_city' => $location[$franchise_owner_cityInd],
						'franchise_owner_state' => $location[$franchise_owner_stateInd],
						'franchise_owner_zip_code' => $location[$franchise_owner_zip_codeInd],
						'franchise_owner_country_name' => $location[$franchise_owner_country_nameInd],
						'franchise_owner_phone' => $location[$franchise_owner_phoneInd],
						'franchise_owner_base_of_ops_dma' => $location[$franchise_owner_base_of_ops_dmaInd],
						'franchise_owner_base_of_ops_state' => $location[$franchise_owner_base_of_ops_stateInd],
						'franchise_contact_name' => $location[$franchise_contact_nameInd],
						'franchise_contact_phone' => $location[$franchise_contact_phoneInd],
						'franchise_contact_email' => $location[$franchise_contact_emailInd],
						'franchise_additional_contact_emails' => $location[$franchise_additional_contact_emailsInd],
						'brand' => $location[$brandInd],
						'pos_type' => $location[$pos_typeInd],
						'image_type' => $location[$image_typeInd],
						'reimage_completion_date' => $location[$reimage_completion_dateInd],
						'distribution_center' => $location[$distribution_centerInd],
						'restaurant_drive_thru_type' => $location[$restaurant_drive_thru_typeInd],
						'guest_cx_indicator' => $location[$guest_cx_indicatorInd]
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
						'zip' => $location[$zipInd],
						'country_id' => $country_id,
						'country' => $location[$countryInd],
						'phone' => $location[$phoneInd],
						'email' => $location[$emailInd],
						'shop_type' => $location[$shop_typeInd],
						'status' => $location[$statusInd],
						'restaurant_segment' => $location[$restaurant_segmentInd],
						'latitude' => $location[$latitudeInd],
						'longitude' => $location[$longitudeInd],
						'restaurant_open_date' => $location[$restaurant_open_dateInd],
						'restaurant_original_open_year' => $location[$restaurant_original_open_yearInd],
						'restaurant_close_date' => $location[$restaurant_close_dateInd],
						'restaurant_temp_close_date' => $location[$restaurant_temp_close_dateInd],
						'restaurant_reopen_date' => $location[$restaurant_reopen_dateInd],
						'franchisee_agreement_expiration_date' => $location[$franchisee_agreement_expiration_dateInd],
						'venue_type' => $location[$venue_typeInd],
						'venue_detail' => $location[$venue_detailInd],
						'DMA_number' => $location[$DMA_numberInd],
						'DMA_name' => $location[$DMA_nameInd],
						'market_number' => $location[$market_numberInd],
						'zone_number' => $location[$zone_numberInd],
						'region_number' => $location[$region_numberInd],
						'NFT_region' => $location[$NFT_regionInd],
						'zone_director_phone' => $location[$zone_director_phoneInd],
						'market_leader_phone' => $location[$market_leader_phoneInd],
						'national_field_trainer_phone' => $location[$national_field_trainer_phoneInd],
						'field_marketing_contact_phone' => $location[$field_marketing_contact_phoneInd],
						'regional_franchise_director_phone' => $location[$regional_franchise_director_phoneInd],
						'regional_manager_phone' => $location[$regional_manager_phoneInd],
						'franchise_president_phone' => $location[$franchise_president_phoneInd],
						'franchise_vp_of_ops_phone' => $location[$franchise_vp_of_ops_phoneInd],
						'area_franchise_lead_phone' => $location[$area_franchise_lead_phoneInd],
						'training_restaurant_indicator' => $location[$training_restaurant_indicatorInd],
						'training_general_manager_name' => $location[$training_general_manager_nameInd],
						'ceo' => $location[$ceoInd],
						'franchise_entity_name' => $location[$franchise_entity_nameInd],
						'sub_franchise_entity_name' => $location[$sub_franchise_entity_nameInd],
						'franchise_owner_address' => $location[$franchise_owner_addressInd],
						'franchise_owner_city' => $location[$franchise_owner_cityInd],
						'franchise_owner_state' => $location[$franchise_owner_stateInd],
						'franchise_owner_zip_code' => $location[$franchise_owner_zip_codeInd],
						'franchise_owner_country_name' => $location[$franchise_owner_country_nameInd],
						'franchise_owner_phone' => $location[$franchise_owner_phoneInd],
						'franchise_owner_base_of_ops_dma' => $location[$franchise_owner_base_of_ops_dmaInd],
						'franchise_owner_base_of_ops_state' => $location[$franchise_owner_base_of_ops_stateInd],
						'franchise_contact_name' => $location[$franchise_contact_nameInd],
						'franchise_contact_phone' => $location[$franchise_contact_phoneInd],
						'franchise_contact_email' => $location[$franchise_contact_emailInd],
						'franchise_additional_contact_emails' => $location[$franchise_additional_contact_emailsInd],
						'brand' => $location[$brandInd],
						'pos_type' => $location[$pos_typeInd],
						'image_type' => $location[$image_typeInd],
						'reimage_completion_date' => $location[$reimage_completion_dateInd],
						'distribution_center' => $location[$distribution_centerInd],
						'restaurant_drive_thru_type' => $location[$restaurant_drive_thru_typeInd],
						'guest_cx_indicator' => $location[$guest_cx_indicatorInd]
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
				//Zone Director
				if(strpos($location[$zone_director_emailInd], ',') !== false){
					foreach(explode(',', $location[$zone_director_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$zone_director_nameInd], $e, '1', $country_id, 20, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director de zona
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$zone_director_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$zone_director_nameInd], $location[$zone_director_emailInd], '1', $country_id, 20, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director de zona
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
				//Market Leader
				if(strpos($location[$market_leader_emailInd], ',') !== false){
					foreach(explode(',', $location[$market_leader_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$market_leader_nameInd], $e, '1', $country_id, 19, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al market lider
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$market_leader_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$market_leader_nameInd], $location[$market_leader_emailInd], '1', $country_id, 19, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al market lider
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
				//Regional Franchise Director
				if(strpos($location[$regional_franchise_director_emailInd], ',') !== false){
					foreach(explode(',', $location[$regional_franchise_director_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$regional_franchise_director_nameInd], $e, '1', $country_id, 18, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$regional_franchise_director_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$regional_franchise_director_nameInd], $location[$regional_franchise_director_emailInd], '1', $country_id, 18, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
				//Regional Manager
				if(strpos($location[$regional_manager_emailInd], ',') !== false){
					foreach(explode(',', $location[$regional_manager_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$regional_manager_nameInd], $e, '1', $country_id, 17, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$regional_manager_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$regional_manager_nameInd], $location[$regional_manager_emailInd], '1', $country_id, 17, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
				//Franchise President
				if(strpos($location[$franchise_president_emailInd], ',') !== false){
					foreach(explode(',', $location[$franchise_president_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$franchise_president_nameInd], $e, '1', $country_id, 22, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$franchise_president_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$franchise_president_nameInd], $location[$franchise_president_emailInd], '1', $country_id, 17, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
				//Franchise Vice President
				if(strpos($location[$franchise_vp_of_ops_emailInd], ',') !== false){
					foreach(explode(',', $location[$franchise_vp_of_ops_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$franchise_vp_of_ops_nameInd], $e, '1', $country_id, 23, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$franchise_vp_of_ops_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$franchise_vp_of_ops_nameInd], $location[$franchise_vp_of_ops_emailInd], '1', $country_id, 23, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
				//Area Franchise Lead
				if(strpos($location[$area_franchise_lead_emailInd], ',') !== false){
					foreach(explode(',', $location[$area_franchise_lead_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$area_franchise_lead_nameInd], $e, '1', $country_id, 14, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$area_franchise_lead_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$area_franchise_lead_nameInd], $location[$area_franchise_lead_emailInd], '1', $country_id, 14, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
					$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
					}
				}
				//Franchise Owner
				if(strpos($location[$franchise_owner_emailInd], ',') !== false){
					foreach(explode(',', $location[$franchise_owner_emailInd]) as $e){
						if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$franchise_owner_nameInd], $e, '1', $country_id, 21, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
						$array = array("iduser" => $tmp);
						$objUsuarios->setLogParameters($_SESSION['userData']['id'], ('Create Update user masive'), $array);
						}
					}
				}else{
					if($location[$franchise_owner_emailInd]!=''){ $tmp = $objUsuarios->crearActualizarUser($location[$franchise_owner_nameInd], $location[$franchise_owner_emailInd], '1', $country_id, 21, 1, $location[$statusInd], $location[$languageInd], $idLocation); //crea/actualiza al director regional de franquisia
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
