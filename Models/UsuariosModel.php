<?php

class UsuariosModel extends Mysql{

	private $intIdUsuario;
	private $strNombre;
	private $strEmail;
	private $strAcceso;
	private $strPassword;
	private $strPais;
	private $intLevel;
	private $strIdioma;
	private $strToken;
	private $intTipoId;
	private $intStatus;

	public function __construct()
	{
		parent::__construct();
	}

	public function insertUsuario(string $name, string $email, string $brand, string $country, int $role, int $notification, int $status, string $language, string $locations=null){
		if(in_array($role, [1,2,3,30]))$locations='0';
		$sql = "SELECT * FROM user WHERE email = '$email'";
		$request = $this->select_all($sql);
		if(empty($request)){
			$sql = "INSERT INTO user(brand_id, country_id, role_id, notification, default_language, name, email, location_id, password, status) VALUES (?,?,?,?,?,?,?,?, SHA2(LEFT(UUID(), 16), 256),?)";
			$return = $this->update($sql, [$brand, $country, $role, $notification, $language, $name, $email, $locations, $status]);
			$sql = "SELECT * FROM user WHERE email = '$email'";
			$return = 0;
			$rs = $this->select_all($sql);
			$return = $rs[0]['id'];
		}else{
			$return = "exist";
		}
		return $return;
	}

	public function crearActualizarUser(string $name, string $email, string $brand, string $country, int $role, int $notification, int $status, string $language, string $locations=null){
		$sql = "SELECT * FROM user WHERE email = '$email'";
		$request = $this->select_all($sql);
		if(empty($request)){
			$sql = "INSERT INTO user(brand_id, country_id, role_id, notification, default_language, name, email, location_id, password, status) VALUES (?,?,?,?,?,?,?,?, SHA2(LEFT(UUID(), 16), 256),?)";
			$return = $this->update($sql, [$brand, $country, $role, $notification, $language, $name, $email, $locations, $status]);
			$sql = "SELECT * FROM user WHERE email = '$email'";
			$return = 0;
			$rs = $this->select_all($sql);
			$return = $rs[0]['id'];
		}else{
			if($request[0]['location_id']!=''){
				$arrLocations = explode(",", $request[0]['location_id']);
			}else{
				$arrLocations=[];
			}

			if(!in_array($locations, $arrLocations)){
				array_push($arrLocations, $locations);
				sort($arrLocations, SORT_NUMERIC);
			}
			if(count($arrLocations)>1){
				$strLocations = implode(',', $arrLocations);
			}else if(count($arrLocations)==1){
				$strLocations = $arrLocations[0];
			}else{
				$strLocations = "";
			}
			
			$sql = "UPDATE user SET brand_id=?, country_id=?, role_id=?, notification=?, default_language=?, name=?, location_id=?, status=? WHERE email = ?";
			$return = $this->update($sql, [$brand, $country, $role, $notification, $language, $name, $strLocations, $status, $email]);
			$sql = "SELECT * FROM user WHERE email = '$email'";
			$return = 0;
			$rs = $this->select_all($sql);
			$return = $rs[0]['id'];
		}
		return $return;
	}

	public function selectUsuarios($isRoot)
	{
		$country_set = array_reduce(array_keys($_SESSION['userData']['country']), function($acc, $item){
			$acc .= "OR FIND_IN_SET($item, u.country_id) ";
			return $acc;
		}, '0 ');

		$sql = "SELECT id, name, email, (SELECT GROUP_CONCAT(name SEPARATOR ', ') FROM brand WHERE FIND_IN_SET(id, u.brand_id)) AS 'brand', (SELECT GROUP_CONCAT(prefix SEPARATOR ', ') FROM country WHERE FIND_IN_SET(id, u.country_id)) AS 'country', (SELECT name FROM role WHERE id = u.role_id) AS 'role', status FROM user u WHERE 1 AND ($country_set)";
		$request = $this->select_all($sql);
		return $request;
	}

	public function selectUsuariosMasive()
    {
        $sql = "SELECT id, profile, name, email, (SELECT name FROM role WHERE id = role_id) AS 'role', (SELECT prefix FROM country WHERE id = country_id) AS 'pais', status FROM user where status = 0 limit 30";
        $res = new Mysql;
        $request = $res->select_all($sql);
        //echo $sql;
        return $request;
    }

	public function getUsers($condition = "1"){
		$sql = "SELECT * FROM user WHERE ".$condition;
		$res = new Mysql;
        $request = $res->select_all($sql);
        //echo $sql;
        return $request;
	}

	public function updateStatusUsuario(int $id){
		$sql = "UPDATE user SET status=? WHERE id = ?";
		$res = new Mysql;
		$return = $res->update($sql, [1, $id]);
		return $return;
	}

	public function selectUsuario(int $iduser)
	{
		$this->intIdUsuario = $iduser;
		$sql = "SELECT id, profile, name, email, (SELECT GROUP_CONCAT(name SEPARATOR ', ') FROM brand WHERE FIND_IN_SET(id, brand_id)) AS 'brand', brand_id, (SELECT GROUP_CONCAT(prefix SEPARATOR ', ') FROM country WHERE FIND_IN_SET(id, country_id)) AS 'country', (SELECT GROUP_CONCAT(name SEPARATOR ', ') FROM location WHERE FIND_IN_SET(id, location_id)) AS 'location', location_id, (SELECT level FROM role WHERE id = role_id) AS 'level', country_id, (SELECT name FROM role WHERE id = role_id) AS 'role', role_id, status, DATE_FORMAT(created, '%d-%m-%Y') as 'created', default_language, notification FROM user WHERE id = $this->intIdUsuario ";
		//echo $sql;exit;
		$request = $this->select($sql);
		return $request;
	}

	public function getUsuario($columns=[], $condition='')
	{
		$sql = "SELECT " . (count($columns) ? implode(', ', $columns) : "*") . " FROM user " . ($condition ? " WHERE $condition " : '');
		$request = $this->select($sql);
		return $request;
	}
	
	public function getListCountries(int $iduser)
	{
		$this->intIdUsuario = $iduser;
		$sql = "SELECT GROUP_CONCAT(c.name SEPARATOR ', ') AS 'countries' FROM user u INNER JOIN country c ON(FIND_IN_SET(c.id,u.country_id)) WHERE u.id = $this->intIdUsuario";
		$request = $this->select($sql)['countries'];
		return $request;
	}

	public function updateUsuario(int $id, string $name, string $email, string $brand, string $country, int $role, int $notification, int $status, string $language, string $locations = null){
		$sql = "SELECT * FROM user WHERE email = '$email' AND id != $id";
		$request = $this->select_all($sql);
		if(empty($request)){
			$sql = "UPDATE user SET brand_id=?, country_id=?, role_id=?, notification=?, default_language=?, name=?, email=?, location_id=?, status=? WHERE id = ?";
			$return = $this->update($sql, [$brand, $country, $role, $notification, $language, $name, $email, $locations, $status, $id]);
		}else{
			$return = "exist";
		}
		return $return;
	}

	public function deleteUsuario(int $iduser)
	{
		$sql = "DELETE FROM user WHERE id = $iduser";
		$request = $this->delete($sql);
		return $request? 'ok' : 'error';
	}

	public function inactivarUsuario(int $iduser, int $status)
	{
		$sql = "UPDATE user SET status=? WHERE id = ?";
		$request = $this->update($sql, [$status, $iduser]);
		return $request? 'ok' : 'error';
	}

	public function updateProfile(int $id, string $name, string $email, string $language, string $profile_picture)
	{
		$sql = "UPDATE user SET name=?, email=?, default_language=?, profile_picture=? WHERE id = ? ";
		$return = $this->update($sql, [$name, $email, $language, $profile_picture, $id]);
		return $return;
	}

	public function updatePassword(int $id, string $password)
	{
		$sql = "SELECT * FROM user WHERE id = $id AND password = SHA2('$password', 256)";
		if(empty($this->select_all($sql))){
			$sql = "UPDATE user SET password = SHA2(?, 256), last_upd_password = NOW() WHERE id = ?";
			return $this->update($sql, [$password, $id]);
		} else return false;
	}

	public function getMailExceptions(string $case, int $reference_id = null){
		$reference_id = $reference_id? "AND me.reference_id = $reference_id" : ''; 
		$sql = "SELECT GROUP_CONCAT(DISTINCT(IFNULL(usr.email, me.secondary_email)) SEPARATOR ',') AS 'stack' FROM mail_exceptions me LEFT JOIN (SELECT id, email FROM user WHERE status=1) usr ON(me.user_id=usr.id) WHERE me.`case`='$case' $reference_id";
		$request = $this->select($sql);
		return $request['stack'];
	}

	public function setLog(int $idUser, string $accion){
		$this->intIdUsuario = $idUser;
		$this->accion = $accion;
		$sql = "INSERT INTO system_logs(user_id, module, action) VALUES (?, ?, ?)";
		$request = $this->update($sql, [$this->intIdUsuario, 'users', $this->accion]);
		return $request;	
	}

	public function setLogParameters(int $idUser, string $accion, array $array){
		$this->intIdUsuario = $idUser;
		date_default_timezone_set('America/Mexico_City');
	
		$date_log = date('Y-m-d H:i:s');

		$datos= json_encode($array);

		$sql = "INSERT INTO system_logs(user_id, module, action,parameters, created) VALUES (?, ?, ?, ?, ?)";
		$request = $this->update($sql, [$this->intIdUsuario, 'users', $accion, $datos,$date_log]);
		return $request;	
	}

	public function getLevelRole(int $role_id){
		$sql = "SELECT level FROM role WHERE id = $role_id";
		$request = $this->select($sql)['level'];
		return $request;
	}

	public function getUsersByRole($ids){
		$ids = implode(',', $ids);
		$query_select = "SELECT u.email, u.location_id, u.country_id, u.role_id, r.level FROM user u INNER JOIN role r ON r.id = u.role_id WHERE u.status = 1 AND r.id IN($ids)";
		$obj = new Mysql;
		$request = $obj->select_all($query_select);
		return $request;
	}

	public function cleanUsersLocations($numbers){
		foreach($numbers as $number){
			$sql = "SELECT id FROM user WHERE FIND_IN_SET((SELECT id FROM location WHERE number = $number), location_id)";
			$request = $this->select_all($sql);
			foreach($request as $r){
				$query_update = "UPDATE user SET location_id = DROP_FROM_SET((SELECT id FROM location WHERE number = ?) , location_id) WHERE id=?";
				$this->update($query_update, [$number, $r['id']]);
				$sql = "SELECT location_id FROM user WHERE id=".$r['id'];
				$request2 = $this->select($sql);
				if($request2['location_id']=='' || $request2['location_id']==NULL){
					$query_update = "UPDATE user SET status=0 WHERE id=?";
					$this->update($query_update, [$r['id']]);
				}
			}
		}
	}

	public function reviseUser(string $email, int $role_id, $locations){
		$query_select = "SELECT (SELECT GROUP_CONCAT(number) FROM location WHERE FIND_IN_SET(id, u.location_id)) lnumber FROM user u WHERE email = '$email'";
		$currLocations = $this->select($query_select);
		
		if($role_id == 10){
			$locations = [end($locations)];
		}
		$strLocations = implode("','", $locations);

		if($currLocations != false){
			if(!empty($currLocations['lnumber']) && $role_id != 10){
				$strLocations = "$strLocations','" . str_replace(",", "','", $currLocations['lnumber']);
			}

			$query_update = "UPDATE user SET country_id = (SELECT GROUP_CONCAT(DISTINCT country_id SEPARATOR ',') FROM location WHERE number IN('$strLocations')), role_id=?, location_id=(SELECT GROUP_CONCAT(id SEPARATOR ',') FROM location WHERE number IN('$strLocations')) WHERE email = ?";
			$tmp = $this->update($query_update, [$role_id, $email]);
			$query_update = "UPDATE user SET status=? WHERE email = ?";
			$tmp2 = $this->update($query_update, [1, $email]);
			if($tmp){
				$request = ['action' => 'update', 'locations' => $locations];
			}
		} else{
			$query_insert = "INSERT INTO user(brand_id, country_id, role_id, default_language, name, email, password, location_id) VALUES (1, (SELECT GROUP_CONCAT(DISTINCT country_id SEPARATOR ',') FROM location WHERE number IN('$strLocations')), ?, 'esp', ?, ?, SHA2(LEFT(UUID(), 16), 256), (SELECT GROUP_CONCAT(id SEPARATOR ',') FROM location WHERE number IN('$strLocations')))";
			$tmp = $this->insert($query_insert, [$role_id, str_replace(['.', '-', '_'], [' ', ' ', ' '], ucfirst(explode('@', $email)[0])), $email]);
			if($tmp){
				$request = ['action' => 'insert', 'locations' => $locations];
			}
		}
		return $request;
	}



//Funcion que inserta excel
	public function setExcelUsuarios($data){

		/*$sql = "INSERT INTO temp_feed(id,brand_id,country_id,status,number,name,address_1,city,state_code,state_name,zip,country,phone,store_email,shop_type,franchise_name,operating_partners_name,open_date,franchisees_name,franchissees_email,area_manager_name,area_manager_email,ops_leader_name,ops_leader_email)
 					   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				
		$request = $this->insert($sql, [$data['id'],						
										$data['brand_id'],				
										$data['country_id'],				
										$data['status'],					
										$data['number'],					
										$data['name'],					
										$data['address_1'],				
										$data['city'],					
										$data['state_code'],					
										$data['state_name'],				
										$data['zip'],				
										$data['country'],				
										$data['phone'],				
										$data['store_email'],				
										$data['shop_type'],				
										$data['franchise_name'],			
										$data['operating_partners_name'],
										$data['open_date'],				
										$data['franchisees_name'],		
										$data['franchissees_email'],		
										$data['area_manager_name'],		
										$data['area_manager_email'],		
										$data['ops_leader_name'],			
										$data['ops_leader_email']]);*/
//LOCATIONS--------------------------------------------------------------------------------------------
		/*$sqlLocation = "CALL VALIDACION_FEED_LOCATIONS (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				
		return = $this->insert($sqlLocation, [
											  $data['brand_id'],				
											  $data['country_id'],				
											  $data['status'],					
											  $data['number'],					
											  $data['name'],					
											  $data['address_1'],				
											  $data['city'],					
											  $data['state_code'],					
											  $data['state_name'],				
											  $data['zip'],				
											  $data['country'],				
											  $data['phone'],				
											  $data['store_email'],				
											  $data['shop_type'],				
											  $data['franchise_name'],			
											  $data['operating_partners_name'],
											  $data['open_date'],				
											  $data['franchisees_name'],		
											  $data['franchissees_email'],		
											  $data['area_manager_name'],		
											  $data['area_manager_email'],		
											  $data['ops_leader_name'],			
											  $data['ops_leader_email']
											  ]);*/

$brand_id				 = $data['brand_id'];			
$country_id				 = $data['country_id'];			
$status					 = $data['status'];		
$number					 = $data['number'];		
$name					 = $data['name'];		
$address_1				 = $data['address_1'];			
$city					 = $data['city'];		
$state_code				 = $data['state_code'];			
$state_name				 = $data['state_name'];			
$zip					 = $data['zip'];		
$country				 = $data['country'];			
$phone					 = $data['phone'];		
$store_email			 = $data['store_email'];				
$shop_type				 = $data['shop_type'];			
$franchise_name			 = $data['franchise_name'];				
$operating_partners_name = $data['operating_partners_name'];							
$open_date				 = $data['open_date'];			
$franchisees_name		 = $data['franchisees_name'];					
$franchissees_email		 = $data['franchissees_email'];					
$area_manager_name		 = $data['area_manager_name'];					
$area_manager_email		 = $data['area_manager_email'];					
$ops_leader_name		 = $data['ops_leader_name'];					
$ops_leader_email		 = $data['ops_leader_email'];







$query = "CALL VALIDACION_FEED_LOCATIONS('$brand_id',
										 '$country_id',
										 '$status',
										 '$number',
										 '$name',
										 '$address_1',
										 '$city',
										 '$state_code',
										 '$state_name',
										 '$zip',
										 '$country',
										 '$phone',
										 '$store_email',
										 '$shop_type',
										 '$franchise_name',
										 '$operating_partners_name',
										 '$open_date',
										 '$franchisees_name',
										 '$franchissees_email',
										 '$area_manager_name',
										 '$area_manager_email',
										 '$ops_leader_name',
										 '$ops_leader_email')";

	$res = new Mysql;
	$request = $res -> select_all($query);
	return $request;
//USER--------------------------------------------------------------------------------------------
/*return $this->select_all("CALL VALIDACION_FEED_LOCATIONS('$brand_id',
														 '$country_id',
														 '$status',
														 '$number',
														 '$name',
														 '$address_1',
														 '$city',
														 '$state_code',
														 '$state_name',
														 '$zip',
														 '$country',
														 '$phone',
														 '$store_email',
														 '$shop_type',
														 '$franchise_name',
														 '$operating_partners_name',
														 '$open_date',
														 '$franchisees_name',
														 '$franchissees_email',
														 '$area_manager_name',
														 '$area_manager_email',
														 '$ops_leader_name',
														 '$ops_leader_email')");*/
							
		//return $requestLocation;	

		
	}

	public function setExcelUser($brand_id,$country_id,$id_rol,$name, $email,$locationId){
	
		//USER--------------------------------------------------------------------------------------------
		$query = "CALL VALIDACION_FEED_USER('$brand_id','$country_id','$id_rol','$name', '$email','$locationId');";

		$res = new Mysql;
		$request = $res -> select_all($query);
		return $request;
		//return $this->select_all("CALL VALIDACION_FEED_USER('$brand_id','$country_id','$id_rol','$name', '$email','$locationId');");

 

	}







public function insertDataLog($data){
	

	date_default_timezone_set('America/Mexico_City');
	
	$date_log = date('Y-m-d H:i:s');
	$datos_parameter = json_encode($data);
	

	$query = "INSERT INTO data_logs(action_log,
									date_log,
									json_parameters)
									VALUES('Feed csv',
										   '$date_log ',
									       '$datos_parameter');";
			
   $res = new Mysql;
   $request = $res -> select_all($query);
   return $request;

	

}






public function setExcelLocationCSV($data){

	
$addressLine1           = ($data['addressLine1'] === '' || $data['addressLine1'] === null) ? null : str_replace("'", "", $data['addressLine1']);
$addressLine2           = ($data['addressLine2'] === '' || $data['addressLine2'] === null) ? null : str_replace("'", "", $data['addressLine2']);
$areaManager            = ($data['areaManager'] === '' || $data['areaManager'] === null) ? null : str_replace("'", "", $data['areaManager']);
$breakfast              = ($data['breakfast'] === '' || $data['breakfast'] === null) ? null : str_replace("'", "", $data['breakfast']);
$cakes                  = ($data['cakes'] === '' || $data['cakes'] === null) ? null : str_replace("'", "", $data['cakes']);
$city                   = ($data['city'] === '' || $data['city'] === null) ? null : str_replace("'", "", $data['city']);
$concept                = ($data['concept'] === '' || $data['concept'] === null) ? null : str_replace("'", "", $data['concept']);
$coreMenu               = ($data['coreMenu'] === '' || $data['coreMenu'] === null) ? null : str_replace("'", "", $data['coreMenu']);
$country                = ($data['country'] === '' || $data['country'] === null) ? null : str_replace("'", "", $data['country']);
$dmaCode                = ($data['dmaCode'] === '' || $data['dmaCode'] === null) ? null : str_replace("'", "", $data['dmaCode']);
$dmaName                = ($data['dmaName'] === '' || $data['dmaName'] === null) ? null : str_replace("'", "", $data['dmaName']);
$districtCode           = ($data['districtCode'] === '' || $data['districtCode'] === null) ? null : str_replace("'", "", $data['districtCode']);
$districtName           = ($data['districtName'] === '' || $data['districtName'] === null) ? null : str_replace("'", "", $data['districtName']);
$driveThru              = ($data['driveThru'] === '' || $data['driveThru'] === null) ? null : str_replace("'", "", $data['driveThru']);
$escalation1            = ($data['escalation1'] === '' || $data['escalation1'] === null) ? null : str_replace("'", "", $data['escalation1']);
$escalation2            = ($data['escalation2'] === '' || $data['escalation2'] === null) ? null : str_replace("'", "", $data['escalation2']);
$franchiseeEmail       = ($data['franchiseeEmail'] === '' || $data['franchiseeEmail'] === null) ? null : str_replace("'", "", $data['franchiseeEmail']);
$franchiseeName        = ($data['franchiseeName'] === '' || $data['franchiseeName'] === null) ? null : str_replace("'", "", $data['franchiseeName']);
$franchiseePhone       = ($data['franchiseePhone'] === '' || $data['franchiseePhone'] === null) ? null : str_replace("'", "", $data['franchiseePhone']);
$lastModernizationDate = ($data['lastModernizationDate'] === '' || $data['lastModernizationDate'] === null) ? null : str_replace("'", "", $data['lastModernizationDate']);
$openDate              = ($data['openDate'] === '' || $data['openDate'] === null) ? null : str_replace("'", "", $data['openDate']);
$regionCode            = ($data['regionCode'] === '' || $data['regionCode'] === null) ? null : str_replace("'", "", $data['regionCode']);
$regionName            = ($data['regionName'] === '' || $data['regionName'] === null) ? null : str_replace("'", "", $data['regionName']);
$storeEmail            = ($data['storeEmail'] === '' || $data['storeEmail'] === null) ? null : str_replace("'", "", $data['storeEmail']);
$storeName             = ($data['storeName'] === '' || $data['storeName'] === null) ? null : str_replace("'", "", $data['storeName']);
$storeNumber           = ($data['storeNumber'] === '' || $data['storeNumber'] === null) ? null : str_replace("'", "", $data['storeNumber']);
$storePhone            = ($data['storePhone'] === '' || $data['storePhone'] === null) ? null : str_replace("'", "", $data['storePhone']);
$tempClosed            = ($data['tempClosed'] === '' || $data['tempClosed'] === null) ? null : str_replace("'", "", $data['tempClosed']);
$venueType             = ($data['venueType'] === '' || $data['venueType'] === null) ? null : str_replace("'", "", $data['venueType']);
$zip                   = ($data['zip'] === '' || $data['zip'] === null) ? null : str_replace("'", "", $data['zip']);

	







$query = "CALL CSV_VALIDACION_FEED_LOCATIONS('$addressLine1', 
											 '$addressLine2', 
											 '$areaManager', 
											 '$breakfast', 
											 '$cakes', 
											 '$city', 
											 '$concept', 
											 '$coreMenu', 
											 '$country', 
											 '$dmaCode', 
											 '$dmaName', 
											 '$districtCode', 
											 '$districtName', 
											 '$driveThru', 
											 '$escalation1', 
											 '$escalation2', 
											 '$franchiseeEmail', 
											 '$franchiseeName', 
											 '$franchiseePhone', 
											 '$lastModernizationDate', 
											 '$openDate', 
											 '$regionCode', 
											 '$regionName', 
											 '$storeEmail', 
											 '$storeName', 
											 '$storeNumber', 
											 '$storePhone', 
											 '$tempClosed', 
											 '$venueType', 
											 '$zip')";




$res = new Mysql;
$request = $res -> select_all($query);
return $request;
	

	
}


public function setExcelUserCSV($brand_id,$country_id,$id_rol,$name, $email,$locationId){
	
	//USER--------------------------------------------------------------------------------------------
	$query = "CALL CSV_VALIDACION_FEED_USER('$brand_id','$country_id','$id_rol','$name', '$email','$locationId');";

	$res = new Mysql;
	$request = $res -> select_all($query);
	return $request;
	//return $this->select_all("CALL VALIDACION_FEED_USER('$brand_id','$country_id','$id_rol','$name', '$email','$locationId');");
}





public function startUser(){
	
	$query = "UPDATE user SET temp_location_id = '' WHERE role_id in(10,14,18,19,20) AND  email NOT IN('area_manager@bw-globalsolutions.com',
																								       'escalation_1@bw-globalsolutions.com',
																								       'escalation_2@bw-globalsolutions.com',
																								       'franchisee@bw-globalsolutions.com',
																								       'main_office@bw-globalsolutions.com',
																								       'store_manager@bw-globalsolutions.com')";

	$res = new Mysql;
	$request = $res -> select_all($query);
	return $request;

}


public function locationFix(){

	$query = "UPDATE user SET location_id = (SELECT CASE
        WHEN LEFT(temp_location_id, 1) = ',' THEN
            SUBSTRING(temp_location_id, 2)
        ELSE
            temp_location_id
    END AS   location_id_sin_coma)  WHERE   role_id IN(10,14,18,19,20)";

	$res = new Mysql;
	$request = $res -> select_all($query);
	return $request;

}



public function user(){
	
	$query = "SELECT  a.name usuario,
				  	  a.email,
				  	  b.name role,
				  	  location_id
				FROM user a
				INNER JOIN role b ON a.role_id = b.id
				WHERE role_id IN(10,14,18,19,20) AND a.status IN(1) AND location_id NOT IN('')
				ORDER BY role";

	$res = new Mysql;
	$request = $res -> select_all($query);
	return $request;

}


public function location($location_id){

	
		
	$query = "SELECT number numero_tienda,
					 name nombre_tienda
			  FROM location 
			  WHERE id IN($location_id)";

	$res = new Mysql;
	$request = $res -> select_all($query);
	return $request;

}







public function insertLog($action){
	

	date_default_timezone_set('America/Mexico_City');
	
	$date_log = date('Y-m-d H:i:s');

	$query = "INSERT INTO system_logs(user_id, module, action, created) VALUES (0,'users','$action','$date_log')";
			
   $res = new Mysql;
   $request = $res -> select_all($query);
   return $request;

	

}

public function getRolAsociado($rol, $location_id){
	if(in_array($rol, [1,2,3,30])){
		$sql = "SELECT GROUP_CONCAT(email) as emails FROM user WHERE role_id = $rol";
	}else{
		$sql = "SELECT GROUP_CONCAT(email) as emails FROM user WHERE role_id = $rol AND FIND_IN_SET($location_id, location_id)";
	}
	$res = new Mysql;
	$request = $res -> select_all($sql);
	return $request[0]['emails'];
}

public function getTo($notification_id, $location_id, $esCalibracion=false, $countryStore=0){
	$sql = "SELECT country_id FROM location WHERE id = $location_id";
	$res = new Mysql;
	$rs = $res -> select_all($sql); 
	$pais = $rs[0]['country_id'];
	$limitante = "";
	if(in_array($notification_id, [1,2])){
		switch($pais){
			case 29: //Emiratos
				$limitante="AND role_id NOT IN(23) ";
				break;
			case 23: //Singapoore

				break;
			case 6: //Canada

				break;
			case 1: //Mexico

				break;
		}
	}
	$sql = "SELECT GROUP_CONCAT(email) as emails FROM user WHERE role_id IN (SELECT role_id FROM sendEmail WHERE notification_id = $notification_id AND send=1 $limitante) AND FIND_IN_SET($location_id, location_id) > 0 AND status = 1";
	$sql2 = "SELECT GROUP_CONCAT(email) as emails FROM user WHERE role_id IN (SELECT role_id FROM sendEmail WHERE notification_id = $notification_id AND send=1 AND role_id IN (1,2,30)) AND FIND_IN_SET($countryStore, country_id) > 0 AND status = 1";

	$request = $res -> select_all($sql); //usuarios relacionados a la tienda
	$request2 = $res -> select_all($sql2); //roles que tienen la notificacion activada pero no estan relacionados a la tienda
	$res="";
	if($request[0]['emails']!='' && $request[0]['emails']!=NULL){
		$res .= $request[0]['emails'];
	}
	if($request2[0]['emails']!='' && $request2[0]['emails']!=NULL){
		if($res!=""){
			$res .=','.$request2[0]['emails'];
		}else{
			$res = $request2[0]['emails'];
		}
	}
	if($esCalibracion) $res='mosorio@bw-globalsolutions.com,cordonez@bw-globalsolutions.com,alopez@arguilea.com,mmaximiliano@arguilea.com';

	return $res;
}









}
?>