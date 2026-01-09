<?php

class Usuarios extends Controllers{

	private $permission;

	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login']))
		{
			header('location: '.base_url().'/login');
		}
		$this->permission = $_SESSION['userData']['permission']['Usuarios'];
	}

	public function usuarios()
	{
		if(!$this->permission['r']){
			header('Location: '.base_url());
		}

		$data['page_tag'] = "Users";
		$data['page_title'] = "Users";
		$data['page_name'] = "users";
		$data['page-functions_js'] = "functions_usuarios.js";
		
		$data['permission'] = $this->permission;
		$data['locations'] = selectLocation(['id', 'name', 'number', 'country_id']);
		
		$where = '';
		switch ($_SESSION['userData']['role']['id']) {
			case 1:  
				break;
			case 2:
				$where = "id NOT IN(1) AND status = 1";
				break;
			case 17:
				$where = "id NOT IN(1,2,17) AND status = 1";
				break;
			default:
				$where = "id NOT IN(1,2) AND status = 1";
		}
		$data['role'] = selectRole(['id', 'name', 'level'], $where);
		//$data['role'] = selectRole(['id', 'name', 'level'], (in_array($_SESSION['userData']['role']['id'], [1,2,17])? '1' : "id IN(10,11,12)") . ' AND status = 1');
		$data['brands'] = selectBrands(['id', 'name']);
		$data['paises'] = [];
		
		$tmp = selectCountries(['id', 'name', 'region'], " active=1");
		foreach($tmp as $i){
			if (!array_key_exists($i['region'], $data['paises'])) {
				$data['paises'][$i['region']] = [];
			}
			array_push($data['paises'][$i['region']], ['id' => $i['id'], 'name' => $i['name']]);
		}

		$this->views->getView($this, "usuarios", $data);
	}

	public function setUsuario()
	{
		if($_POST){
			if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['list_brand']) || empty($_POST['list_country']) || empty($_POST['role']) || empty($_POST['language'])){
				$arrResponse = array("status" => false, "msg" => "Wrong data");
			}else{
				$level = $this->model->getLevelRole($_POST['role']);
				/*switch ($level) {
					case 6: case 5:
						$_POST['list_location'] = [0];
						break;
					case 4:
						if(count($_POST['list_country']) > 1){
							die(json_encode(['status' => false, "msg" => "An error occurred in the process, if the problem persists please contact support"],JSON_UNESCAPED_UNICODE));
						}
						$_POST['list_location'] = [0];
						break;
					case 3:
						if(count($_POST['list_country']) > 1 || empty($_POST['list_location'])){
							die(json_encode(['status' => false, "msg" => "An error occurred in the process, if the problem persists please contact support"],JSON_UNESCAPED_UNICODE));
						}
						break;
					case 2:
						if(count($_POST['list_country']) > 1 || count($_POST['list_location']) > 1){
							die(json_encode(['status' => false, "msg" => "An error occurred in the process, if the problem persists please contact support"],JSON_UNESCAPED_UNICODE));
						}
						break;
				}	*/			  

				$id = intVal($_POST['id']);
				$name = ucwords(strClear($_POST['name']));
				$email = strtolower(strClear($_POST['email']));
				$brand = implode(",", $_POST['list_brand']);
				$country = implode(",",$_POST['list_country']);
				$role = intVal($_POST['role']);
				$notification = intVal($_POST['notification']);
				$status = intVal($_POST['status']);
				$language = strClear($_POST['language']);
				if($_POST['list_location']!=''){
					$location = implode(',', $_POST['list_location']);
				}else{
					$location = '';
				}
				$request_user = "";


				if($id == 0)
				{
					$option = 1;
					if($this->permission['w']){
						$request_user = $this->model->insertUsuario($name, $email, $brand, $country, $role, $notification, $status, $language, $location);
						if($request_user!=0 && $request_user!='exist'){

							require_once("Models/LoginModel.php");
							$objLogin = new LoginModel();
							$arrPass = $objLogin->setRecoverPass($email, 1);

							if($arrPass != false){
								$data = ['asunto' => 'New access generated', 'email' => $email, 'token' => $arrPass[1], 'country'=>$country];
								if(esEspanol([$country])){
									sendEmail($data, 'new_user_notice');
								}else{
									sendEmail($data, 'new_user_notice_eng');
								}
							}
						}
					}
				}else{
					if($this->permission['w']){
						$request_user = $this->model->updateUsuario($id, $name, $email, $brand, $country, $role, $notification, $status, $language, $location);
					}
				}

				if($request_user > 0)
				{
					if($option == 1)
					{
						$arrResponse = array("status" => true, "msg" => "Data saved successfully");
						$array = array('iduser'     =>  $request_user);
						$this->model->setLogParameters($_SESSION['userData']['id'], "insert user", $array);
					}else{
						$arrResponse = array("status" => true, "msg" => "Data updated successfully");
						$this->model->setLog($_SESSION['userData']['id'], "update user id:$id");
					}
				}else if($request_user == 'exist'){
					$arrResponse = array("status" => false, "msg" => "Attention! the access with entered email already exists, try with another");
				}else{
					$arrResponse = array("status" => false, "msg" => "It is not possible to store the data");
				}
			}

			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function getUsuarios()
	{
		if(!$this->permission['r']){
			echo "Restricted access";
			exit;
		}
		$arrData = $this->model->selectUsuarios($_SESSION['userData']['role']['id']);

		for($i=0; $i<count($arrData); $i++){
			$btnView = '';
			$btnEdit = '';
			$btnDelete = '';

			$status = $arrData[$i]['status'];
			if($arrData[$i]['status'] == 1)
			{
				$arrData[$i]['status'] = '<span class="badge badge-success">Active</span>';
			}else{
				$arrData[$i]['status'] = '<span class="badge badge-danger">Inactive</span>';
			}
			
			if($arrData[$i]['notification'] == 1)
			{
				$arrData[$i]['notification'] = '<span class="badge badge-success">Active</span>';
			}else{
				$arrData[$i]['notification'] = '<span class="badge badge-danger">Inactive</span>';
			}

			$btnView = '<button class="btn mr-1 mb-1 btn-secondary btn-sm btnViewUsuario" onClick="fntViewUsuario('.$arrData[$i]['id'].')" title="View user"> <i class="fa fa-eye"></i></button>';
			
			if($this->permission['u']){
				$btnEdit = '<button class="btn mr-1 mb-1 btn-primary btn-sm btnEditUsuario" onClick="fntEditUsuario(this,'.$arrData[$i]['id'].')" title="Edit"> <i class="fa fa-pencil"></i></button>';
			}

			if($this->permission['d']){
				$btnDelete = '<button class="btn mr-1 mb-1 btn-danger btn-sm btnDelUsuario" status="'.$status.'" '.($status==1?'':'style="background-color:#f4d05b;"').' onClick="fntStatusUsuario('.$arrData[$i]['id'].', '.($status==1?0:1).')" title="Delete"> '.($status==1?'<i class="fa fa-trash"></i>':'<i class="fa fa-bolt" aria-hidden="true"></i>').'</button>';
			}

			$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
		}

		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
		die();
	}


	



	

	public function getUsuario(int $iduser)
	{
		if(!$this->permission['r']){
			echo "Restricted access";
			exit;
		}
		$idusuario = intVal($iduser);
		if($idusuario > 0)
		{
			$arrData = $this->model->selectUsuario($idusuario);
			if(empty($arrData))
			{
				$arrResponse = array("status" => false, "msg" => "Data not found");
			}else{
				$arrData['pais'] = str_replace(',',', ',$arrData['pais']);
				$arrResponse = array("status" => true, "data" => $arrData);
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function delUsuario()
	{
		if($_POST){
			if($this->permission['d']){
				$intIduser = intVal($_POST['iduser']);
				$requestDelete = $this->model->deleteUsuario($intIduser);
				if($requestDelete == 'ok')
				{
					$arrResponse = array('status' => true, 'msg' => 'The user has been deleted');
				}else{
					$arrResponse = array('status' => true, 'msg' => 'Error deleting user');
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	public function inactivarUsuario(){
		if($_POST){
			if($this->permission['d']){
				$intIduser = intVal($_POST['iduser']);
				$status = $_POST['statusx'];
				$requestDelete = $this->model->inactivarUsuario($intIduser, $status);
				if($requestDelete == 'ok')
				{
					$array = array('iduser'     =>  $intIduser);
					$r = $this->model->setLogParameters($_SESSION['userData']['id'], ($status==1?'Activate user':'Inactivate user'), $array);
					$arrResponse = array('status' => true, 'msg' => 'The user has been inactivated');
				}else{
					$arrResponse = array('status' => true, 'msg' => 'Error inactivate user');
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	public function perfil()
	{
		$data['page_tag'] = "Profile";
		$data['page_title'] = "User profile";
		$data['page_name'] = "profile";
		$data['page-functions_js'] = "functions_profile.js";

		$data['countries'] = $this->model->getListCountries($_SESSION['userData']['id']);
		$data['regExPass'] = Validators::getRegEx('password');
		$data['titlePass'] = "Minimum 12 characters, uppercase and lowercase, plus at least one number";
		$this->views->getView($this, "perfil", $data);
	}

	public function setProfile()
	{
		$passFlag = false;
		if(Validators::check(['password' => $_POST['password']])){
			$passFlag = true;
			if($this->model->updatePassword($_SESSION['userData']['id'], $_POST['password'])){
				$passFlag = false;
				$_SESSION['userData']['last_upd_password'] = date("Y-m-d H:i:s");
				$this->model->setLog($_SESSION['userData']['id'], 'update password');
			}
		}

		if($passFlag){
			$arrResponse = ['status' => false, 'msg' => 'It was not possible to update your password, check that it is not the same as the current one'];
			die(json_encode($arrResponse, JSON_UNESCAPED_UNICODE));
		}

		$request = false;
		if(Validators::check(['email' => $_POST['email']]) and !empty($_POST['name']) and !empty($_POST['language'])){
			if($this->model->updateProfile($_SESSION['userData']['id'], $_POST['name'], $_POST['email'], $_POST['language'], $_POST['profile_picture'])){
				$array = array('name'     =>  $_POST['name'],
							   'email'    =>  $_POST['email'],
							   'profile_picture'    =>  $_POST['profile_picture'],
							   'language'    =>  $_POST['language']);
				$this->model->setLogParameters($_SESSION['userData']['id'], 'update profile', $array);
				$_SESSION['userData']['name'] = $_POST['name'];
				$_SESSION['userData']['email'] = $_POST['email'];
				$_SESSION['userData']['profile_picture'] = $_POST['profile_picture'];
				$_SESSION['userData']['default_language'] = $_POST['language'];

				$request = true;
			}
		}

		die(json_encode(['status' => $request], JSON_UNESCAPED_UNICODE));
	}







	

	






	public function setExcelUsuarios()
	{
		// Incluye los archivos de PHPExcel manualmente
	require_once $_SERVER['DOCUMENT_ROOT'] . '/Assets/js/plugins/PHPExcel/Classes/PHPExcel.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/Assets/js/plugins/PHPExcel/Classes/PHPExcel/IOFactory.php';
	
	// Ruta al archivo Excel en tu servidor
	//$archivoExcel = $_SERVER['DOCUMENT_ROOT'] . '/Controllers/feed/prueba_feed_layout.xlsx';
	$archivoExcel = $_SERVER['DOCUMENT_ROOT'] . '/Controllers/feed/feed_layout.xlsx';
	
	
	
	
		try {
			// Cargar el archivo Excel
			$objPHPExcel = PHPExcel_IOFactory::load($archivoExcel);
	
			// Seleccionar la primera hoja
			$hoja = $objPHPExcel->getSheet(0);
	
			// Obtener el número de filas
			$numFilas = $hoja->getHighestRow();
	
	
	//DECALRO EL ARREGLO PARA LAMACENAR TODOS LOS EMAILS
	$dataEmail = array();	
	
	//DECLARAMOS UN ARREGLO PARA ALMACENAR LOS ERRORES POR FILA 
	$dataErrorLog = array();
	
	// Leer cada fila
	
	
			$valoresEsperados = ['id', 
								 'brand_id', 
								 'country_id', 
								 'status', 
								 'number',
								 'name', 
								 'address_1', 
								 'city', 
								 'state_code', 
								 'state_name',
								 'zip', 
								 'country', 
								 'phone', 
								 'store_email', 
								 'shop_type',
								 'franchise_name', 
								 'operating_partners_name', 
								 'open_date',
								 'franchisees_name', 
								 'franchissees_email', 
								 'area_manager_name',
								 'area_manager_email', 
								 'ops_leader_name', 
								 'ops_leader_email',
								 'ops_director_name',
								 'ops_director_email'];
		
			$coincidenTodas = true;
			for ($col = 0; $col < count($valoresEsperados); $col++) {
				$celdaValor = $hoja->getCellByColumnAndRow($col, 1)->getValue();
				if ($celdaValor !== $valoresEsperados[$col]) {

	
					array_push($dataErrorLog, array('encabezado_error'    => $valoresEsperados[$col]));
					array_push($dataErrorLog, array('encabezado_correcto' => $celdaValor));

					$coincidenTodas = false;
					break;
				}
			}
			
			if ($coincidenTodas) {
				echo "Los encabezados coinciden";
				
			} else {
				foreach ($dataErrorLog as $errorLog) {
					
					echo "Errores  en encabezados ".$errorLog."<br>";
	
	
				}
	
	
				$insertDataLog = $this->model->insertDataLog($valoresEsperados,1,$dataErrorLog,'Encabezados');
				die();
			}
		
	
	
	 for ($fila = 2; $fila <= $numFilas; $fila++) {
			  
		
	
	//añadir validacion correo y vacio
	
	// Leer los valores de las celdas
			
	$id						 = $hoja->getCellByColumnAndRow(0,  $fila)->getValue()?: '';
	$brand_id				 = $hoja->getCellByColumnAndRow(1,  $fila)->getValue()?: '';
	$country_id				 = $hoja->getCellByColumnAndRow(2,  $fila)->getValue()?: '';
	$status					 = $hoja->getCellByColumnAndRow(3,  $fila)->getValue()?: '';
	$number					 = $hoja->getCellByColumnAndRow(4,  $fila)->getValue()?: '';
	$name					 = $hoja->getCellByColumnAndRow(5,  $fila)->getValue()?: '';
	$address_1				 = $hoja->getCellByColumnAndRow(6,  $fila)->getValue()?: '';
	$city					 = $hoja->getCellByColumnAndRow(7,  $fila)->getValue()?: '';
	$state_code				 = $hoja->getCellByColumnAndRow(8,  $fila)->getValue()?: '';
	$state_name				 = $hoja->getCellByColumnAndRow(9,  $fila)->getValue()?: '';
	$zip					 = $hoja->getCellByColumnAndRow(10, $fila)->getValue()?: '';
	$country				 = $hoja->getCellByColumnAndRow(11, $fila)->getValue()?: '';
	$phone					 = $hoja->getCellByColumnAndRow(12, $fila)->getValue()?: '';
	$store_email			 = trim($hoja->getCellByColumnAndRow(13, $fila)->getValue())?: '0';
	$shop_type				 = $hoja->getCellByColumnAndRow(14, $fila)->getValue()?: '';
	$franchise_name			 = $hoja->getCellByColumnAndRow(15, $fila)->getValue()?: '0';
	$operating_partners_name = $hoja->getCellByColumnAndRow(16, $fila)->getValue()?: '';
	$open_date				 = $hoja->getCellByColumnAndRow(17, $fila)->getValue()?: '';
	$franchisees_name		 = $hoja->getCellByColumnAndRow(18, $fila)->getValue()?: '0';
	$franchissees_email		 = trim($hoja->getCellByColumnAndRow(19, $fila)->getValue())?: '0';
	$area_manager_name		 = $hoja->getCellByColumnAndRow(20, $fila)->getValue()?: '0';
	$area_manager_email		 = trim($hoja->getCellByColumnAndRow(21, $fila)->getValue())?: '0';
	$ops_leader_name		 = $hoja->getCellByColumnAndRow(22, $fila)->getValue()?: '0';
	$ops_leader_email		 = trim($hoja->getCellByColumnAndRow(23, $fila)->getValue())?: '0';
	$ops_director_name		 = trim($hoja->getCellByColumnAndRow(24, $fila)->getValue())?: '0';
	$ops_director_email		 = trim($hoja->getCellByColumnAndRow(25, $fila)->getValue())?: '0';
	
	
	  // Utiliza filter_var para validar la dirección de correo electrónico
	
	   
			/**/
			echo "<br><br><br> --------------------------<br><br><br><br>";
	
			echo "id:   					$id						 <br>";				
			echo "brand_id:  	 			$brand_id				 <br>";			
			echo "country_id:   			$country_id				 <br>";				
			echo "status:   				$status					 <br>";				
			echo "number:   				$number					 <br>";				
			echo "name:   					$name					 <br>";			
			echo "address_1:   				$address_1				 <br>";				
			echo "city:   					$city					 <br>";			
			echo "state_name:   			$state_name				 <br>";				
			echo "store_email:   			$store_email			 <br>";			
			echo "shop_type:   				$shop_type				 <br>";				
			echo "franchise_name:   		$franchise_name			 <br>";				
			echo "operating_partners_name:  $operating_partners_name <br>";			
			echo "open_date:   				$open_date				 <br>";				
			echo "franchisees_name:   		$franchisees_name		 <br>";			
			echo "franchissees_email:   	$franchissees_email		 <br>";				
			echo "area_manager_name:   		$area_manager_name		 <br>";				
			echo "area_manager_email:   	$area_manager_email		 <br>";				
			echo "ops_leader_name:   		$ops_leader_name		 <br>";			
			echo "ops_leader_email:   		$ops_leader_email		 <br>";
			echo "<br><br><br> --------------------------<br><br><br><br>";
			
	
	  $data = array('id'					  => $id,														
					'brand_id'				  => $brand_id,															
					'country_id'			  => $country_id,																
					'status'				  => $status,															
					'number'				  => $number,															
					'name'					  => $name,														
					'address_1'				  => $address_1,															
					'city'					  => $city,														
					'state_code'			  => $state_code,																
					'state_name'			  => $state_name,																
					'zip'					  => $zip,														
					'country'				  => $country,															
					'phone'					  => $phone,														
					'store_email'			  => $store_email,																
					'shop_type'				  => $shop_type,															
					'franchise_name'		  => $franchise_name,																	
					'operating_partners_name' => $operating_partners_name, 																			
					'open_date'				  => $open_date,															
					'franchisees_name'		  => $franchisees_name,																	
					'franchissees_email'	  => $franchissees_email,																		
					'area_manager_name'		  => $area_manager_name,																	
					'area_manager_email'	  => $area_manager_email,																		
					'ops_leader_name'		  => $ops_leader_name,																	
					'ops_leader_email'		  => $ops_leader_email,
					'ops_director_name'		  => $ops_director_name,
					'ops_director_email'	  => $ops_director_email);
	
					$setExcelLocation = $this->model->setExcelUsuarios($data);
					
					foreach ($setExcelLocation as $si) {
	
	
						$locationId = $si['locationId'];
	//echo "------------------------------------------- $fila ----id tienda: " . htmlspecialchars($si['locationId']) . "-------------------------------------------<br>";
	
						
						//echo "<br>Parameter $brand_id --$country_id --10 --$name --$store_email --$locationId ";
						if (filter_var($store_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
						$modelstoreManager = $this->model->setExcelUser($brand_id,$country_id,10,$name, $store_email,$locationId);
						foreach ($modelstoreManager as $storeManager) {
							//echo "storeManager: $store_email " . htmlspecialchars($storeManager['validacionEmail']) . "<br>";
							if($storeManager['validacionEmail']==1){array_push($dataEmail,   $store_email);}
						}} else {//echo "error de correo $store_email <br>";
							array_push($dataErrorLog, array('correo_incorrecto_storeManager'    => $store_email));}
	
						//echo "<br>Parameter $brand_id --$country_id --14 --$franchisees_name --$franchissees_email --$locationId ";
						if (filter_var($franchissees_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
						$modelfranchisees  = $this->model->setExcelUser($brand_id,$country_id,14,$franchisees_name, $franchissees_email,$locationId);
						foreach ($modelfranchisees as $franchisees) {
							//echo "franchisees: $franchissees_email " . htmlspecialchars($franchisees['validacionEmail']) . "<br>";
							if($franchisees['validacionEmail']==1){array_push($dataEmail,   $franchissees_email);}
	
						}} else {//echo "error de correo $franchissees_email <br>";
							array_push($dataErrorLog, array('correo_incorrecto_franchisees'    => $franchissees_email));}
	
						//echo "<br>Parameter $brand_id --$country_id --20 --$area_manager_name --$area_manager_email --$locationId";
						if (filter_var($area_manager_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
						$modelareaManager  = $this->model->setExcelUser($brand_id,$country_id,20,$area_manager_name, $area_manager_email,$locationId);
						foreach ($modelareaManager as $areaManager) {
							//echo "areaManager: $area_manager_email " . htmlspecialchars($areaManager['validacionEmail']) . "<br>";
							if($areaManager['validacionEmail']==1){array_push($dataEmail,   $area_manager_email);}
	
						}}else {//echo "error de correo $area_manager_email <br>";
							array_push($dataErrorLog, array('correo_incorrecto_areaManager'    => $area_manager_email));}
	
						//echo "<Parameter $brand_id --$country_id --19 --$ops_leader_name --$ops_leader_email --$locationId <br>";
						if (filter_var($ops_leader_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
						$modelopsleader 	  = $this->model->setExcelUser($brand_id,$country_id,19,$ops_leader_name, $ops_leader_email,$locationId);
						foreach ($modelopsleader as $opsleader) {
							//echo "opsleader: $ops_leader_email" . htmlspecialchars($opsleader['validacionEmail']) . "<br>";
							if($opsleader['validacionEmail']==1){array_push($dataEmail,   $ops_leader_email);}
						}}else {//echo "error de correo $ops_leader_email <br>"; 
							array_push($dataErrorLog, array('correo_incorrecto_opsleader'    => $ops_leader_email));}
	
						//echo "<Parameter $brand_id --$country_id --18 --$ops_director_name --$ops_director_email --$locationId <br>";
						if (filter_var($ops_director_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
						$modelopsdirector 	  = $this->model->setExcelUser($brand_id,$country_id,18,$ops_director_name, $ops_director_email,$locationId);
						foreach ($modelopsdirector as $opsDirector) {
							//echo "opsDirector: $ops_director_email" . htmlspecialchars($opsDirector['validacionEmail']) . "<br>";
							if($opsDirector['validacionEmail']==1){array_push($dataEmail,   $ops_director_email);}
						}}else {//echo "error de correo $ops_director_email <br>"; 
							array_push($dataErrorLog, array('correo_incorrecto_opsleader'    => $ops_director_email));}
		
	
					}
			
					$insertDataLog = $this->model->insertDataLog($data,1,$dataErrorLog,'Actualizacion');
			}
	
	
	// Elimina los correos repetidos
	$dataEmail = array_unique($dataEmail);
	
	$i = 1;
	
	foreach ($dataEmail as $email) {
		// Validamos que no venga vacío ni sea '0'
		if (!empty($email) && $email != '0') {
		
			//echo "<br>--------------------------------------- SE ENVIA CORREO A: ".$i . "-" . $email . "<br>";
		   
	/*
			require_once("Models/LoginModel.php");
			$objLogin = new LoginModel();
			$arrPass = $objLogin->setRecoverPass($email, 1);
	
			if($arrPass != false){
				$data = ['asunto' => 'Nuevo acceso generado', 'email' => $email, 'token' => $arrPass[1]];
				sendEmail($data, 'new_user_notice');
			}
	*/
			$i++;
	
		}
	}
	
	
	
		} catch (Exception $e) {
			echo 'Error al leer el archivo Excel: ', $e->getMessage();
		}
	}






	public function setExcelLocation()
	{
		// Incluye los archivos de PHPExcel manualmente
	require_once $_SERVER['DOCUMENT_ROOT'] . '/Assets/js/plugins/PHPExcel/Classes/PHPExcel.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/Assets/js/plugins/PHPExcel/Classes/PHPExcel/IOFactory.php';
	
	// Ruta al archivo Excel en tu servidor
	//$archivoExcel = $_SERVER['DOCUMENT_ROOT'] . '/Controllers/feed/prueba_feed_layout.xlsx';
	$archivoExcel = $_SERVER['DOCUMENT_ROOT'] . '/Controllers/feed/layout_feed.xlsx';
	
	
	
	
		try {
			// Cargar el archivo Excel
			$objPHPExcel = PHPExcel_IOFactory::load($archivoExcel);
	
			// Seleccionar la primera hoja
			$hoja = $objPHPExcel->getSheet(0);
	
			// Obtener el número de filas
			$numFilas = $hoja->getHighestRow();
	
	
	//DECALRO EL ARREGLO PARA LAMACENAR TODOS LOS EMAILS
	$dataEmail = array();	
	
	//DECLARAMOS UN ARREGLO PARA ALMACENAR LOS ERRORES POR FILA 
	$dataErrorLog = array();
	
	// Leer cada fila
	
	
			$valoresEsperados = ['id', 'brand_id', 'country_id', 'status', 'number','name', 'address_1', 'city', 'state_code', 'state_name','zip', 'country', 'phone', 'store_email', 'shop_type','franchise_name', 'operating_partners_name', 'open_date','franchisees_name', 'franchissees_email', 'area_manager_name','area_manager_email', 'ops_leader_name', 'ops_leader_email'];
		
			$coincidenTodas = true;
			for ($col = 0; $col < count($valoresEsperados); $col++) {
				$celdaValor = $hoja->getCellByColumnAndRow($col, 1)->getValue();
				if ($celdaValor !== $valoresEsperados[$col]) {

	
					array_push($dataErrorLog, array('encabezado_error'    => $valoresEsperados[$col]));
					array_push($dataErrorLog, array('encabezado_correcto' => $celdaValor));

					$coincidenTodas = false;
					break;
				}
			}
			
			if ($coincidenTodas) {
				echo "Los encabezados coinciden";
				
			} else {
				foreach ($dataErrorLog as $errorLog) {
					
					echo "Errores  en encabezados ".$errorLog."<br>";
	
	
				}
	
	
				$insertDataLog = $this->model->insertDataLog($valoresEsperados,1,$dataErrorLog,'Encabezados');
				die();
			}
		
	
	
	 for ($fila = 2; $fila <= $numFilas; $fila++) {
			  
		
	
	//añadir validacion correo y vacio
	
	// Leer los valores de las celdas
			
	$id						 = $hoja->getCellByColumnAndRow(0,  $fila)->getValue()?: '';
	$brand_id				 = $hoja->getCellByColumnAndRow(1,  $fila)->getValue()?: '';
	$country_id				 = $hoja->getCellByColumnAndRow(2,  $fila)->getValue()?: '';
	$status					 = $hoja->getCellByColumnAndRow(3,  $fila)->getValue()?: '';
	$number					 = $hoja->getCellByColumnAndRow(4,  $fila)->getValue()?: '';
	$name					 = $hoja->getCellByColumnAndRow(5,  $fila)->getValue()?: '';
	$address_1				 = $hoja->getCellByColumnAndRow(6,  $fila)->getValue()?: '';
	$city					 = $hoja->getCellByColumnAndRow(7,  $fila)->getValue()?: '';
	$state_code				 = $hoja->getCellByColumnAndRow(8,  $fila)->getValue()?: '';
	$state_name				 = $hoja->getCellByColumnAndRow(9,  $fila)->getValue()?: '';
	$zip					 = $hoja->getCellByColumnAndRow(10, $fila)->getValue()?: '';
	$country				 = $hoja->getCellByColumnAndRow(11, $fila)->getValue()?: '';
	$phone					 = $hoja->getCellByColumnAndRow(12, $fila)->getValue()?: '';
	$store_email			 = trim($hoja->getCellByColumnAndRow(13, $fila)->getValue())?: '0';
	$shop_type				 = $hoja->getCellByColumnAndRow(14, $fila)->getValue()?: '';
	$franchise_name			 = $hoja->getCellByColumnAndRow(15, $fila)->getValue()?: '0';
	$operating_partners_name = $hoja->getCellByColumnAndRow(16, $fila)->getValue()?: '';
	$open_date				 = $hoja->getCellByColumnAndRow(17, $fila)->getValue()?: '';
	$franchisees_name		 = $hoja->getCellByColumnAndRow(18, $fila)->getValue()?: '0';
	$franchissees_email		 = trim($hoja->getCellByColumnAndRow(19, $fila)->getValue())?: '0';
	$area_manager_name		 = $hoja->getCellByColumnAndRow(20, $fila)->getValue()?: '0';
	$area_manager_email		 = trim($hoja->getCellByColumnAndRow(21, $fila)->getValue())?: '0';
	$ops_leader_name		 = $hoja->getCellByColumnAndRow(22, $fila)->getValue()?: '0';
	$ops_leader_email		 = trim($hoja->getCellByColumnAndRow(23, $fila)->getValue())?: '0';
	
	
	  
	
	  $data = array('id'					  => $id,														
					'brand_id'				  => $brand_id,															
					'country_id'			  => $country_id,																
					'status'				  => $status,															
					'number'				  => $number,															
					'name'					  => $name,														
					'address_1'				  => $address_1,															
					'city'					  => $city,														
					'state_code'			  => $state_code,																
					'state_name'			  => $state_name,																
					'zip'					  => $zip,														
					'country'				  => $country,															
					'phone'					  => $phone,														
					'store_email'			  => $store_email,																
					'shop_type'				  => $shop_type,															
					'franchise_name'		  => $franchise_name,																	
					'operating_partners_name' => $operating_partners_name, 																			
					'open_date'				  => $open_date,															
					'franchisees_name'		  => $franchisees_name,																	
					'franchissees_email'	  => $franchissees_email,																		
					'area_manager_name'		  => $area_manager_name,																	
					'area_manager_email'	  => $area_manager_email,																		
					'ops_leader_name'		  => $ops_leader_name,																	
					'ops_leader_email'		  => $ops_leader_email	);
	
					$setExcelLocation = $this->model->setExcelUsuarios($data);
					
					foreach ($setExcelLocation as $si) {
	
	
					$locationId = $si['locationId'];
	echo "------------------------------------------- $fila ----id tienda: " . htmlspecialchars($si['locationId']) . "-------------------------------------------<br>";
	
	
	
					}
			
					$insertDataLog = $this->model->insertDataLog($data,1,$dataErrorLog,'Actualizacion');
			}

	
		} catch (Exception $e) {
			echo 'Error al leer el archivo Excel: ', $e->getMessage();
		}
	}
	
	public function user() {

		$user = UsuariosModel::user();
	
		$outputRows = [];
	
	   
	
		foreach ($user as $data) {
	
			$usuario     = $data['usuario'];     
			$email       = $data['email'];     
			$role        = $data['role'];     
			$location_id = $data['location_id'];        
	
			
			
			$i = 0;
			$outputRowLocation = [];
			$outputRowLocationNumber = [];
	
			$location = UsuariosModel::location($location_id);
	
				foreach ($location as $data_location) {
	
					$numero_tienda = $data_location['numero_tienda'];
					$nombre_tienda = $data_location['nombre_tienda'];
				
					//echo $i."- #".$numero_tienda." "."Tienda: ".$nombre_tienda."<br>";
			  
					//$outputRowLocation[] = "<li>" . $nombre_tienda . "</li>";
					//$outputRowLocationNumber[] = "<li>" . $numero_tienda . "</li>";
					$outputRowLocation[] = "<li>" . $numero_tienda . " Tienda: " . $nombre_tienda . "</li>";
	
					$i ++;
	
				}
	
				$outputLocation = implode("", $outputRowLocation);
				$outputLocationNumber = implode("", $outputRowLocationNumber);
				
			//echo "Total de tiendas - ".$i ."<br>----------------------------------<br>";
			array_push($outputRows, [

				'usuario'		    => $usuario,
				'email'			    => $email,
				'role'		        => $role,
				'number'		    => $i,
				'location_number'	=> "<ol><b>" . $outputLocationNumber . "</b></ol>",
				'location_name'	    => "<ol>" . $outputLocation . "</ol>"
			
			]);
		   
		}
		echo json_encode($outputRows,JSON_UNESCAPED_UNICODE);
		
		
	
	}
	
	
	
	
	
	}







?>