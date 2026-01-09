<?php

class Login extends Controllers{

	public function __construct()
	{
		session_start();
		if(isset($_SESSION['login']))
		{
			header('location: '.base_url().'/home');
		}
		parent::__construct();
	}

	public function login()
	{
		$data['page_tag'] = "Church's Reports";
		//$data['page_tag'] = "Login";
		$data['page-functions_js'] = "functions_login.js";

		$data['regExEmail'] = Validators::getRegEx('email');
		$data['cliente'] = $this->model->getClient();
		$this->views->getView($this, "login", $data);
	}

	public function loginUser(){
		$request = ['status' => 0];
		if($user = $this->model->loginUser($_POST['email'], $_POST['password'])){
			$_SESSION['login'] = true;
			foreach($user as $key => $val){
				$_SESSION['userData'][$key] = $val;
			}
			$request = ['status' => 1, 'name' =>$_SESSION['userData']['name']];
			$this->model->setLog($user['id'], 'sing in');
		}else{

			$data = array('user'       => $_POST['email'],
						  'password'   => $_POST['password']);

			$this->model->setLogParameters(0, 'fail sing in',$data);
		}
		die(json_encode($request, JSON_UNESCAPED_UNICODE));
	}

	public function recoverPass(){
		require_once("Models/UsuariosModel.php");
		$objData = new UsuariosModel();
		if(Validators::check(['email' => $_POST['email']])){
			if($response = $this->model->setRecoverPass($_POST['email'])){
				$user = $objData->getUsuario([], "email = '".$_POST['email']."'");
				$arr = explode(",", $user['country_id']);
				$isAmerican = isAmerican2($arr);
				$titulo = (esEspanol($arr)?'Solicitud de contraseña':'Password request');
				$data = ['asunto' => $titulo, 'email' => $_POST['email'], 'token' => $response[1], 'isAmerican'=>$isAmerican]; //'cc'=>'cordonez@bw-globalsolutions.com',
				if(esEspanol($arr)){
					sendEmail($data, 'reset_password');
				}else{
					sendEmail($data, 'reset_password_eng');
				}

				$this->model->setLog($response[0], 'request password');
			}
			die(json_encode(['status' => 1], JSON_UNESCAPED_UNICODE));
		}
	}

	// Seccion para restablecer la contraseña
	public function resetPassword(){
		$user_id = false;
		if(!empty($_GET['token'])) $user_id = $this->model->getRecoverPass($_GET['token']);
		if($user_id){
			$data['page_tag'] = "Reset Password";
			$data['page-functions_js'] = "functions_login.js";
			
			$data['regExPass'] = Validators::getRegEx('password');
			$data['token'] = $_GET['token'];
			$this->views->getView($this, "reset_password", $data);
		} else header('Location: '.base_url());
	}

	public function setPassword(){
		$user = $this->model->getRecoverPass($_POST['token']);
		if($user and Validators::check(['password' => $_POST['password']])){
			$request = ['status' => $this->model->setPassword($user['user_id'], $_POST['password'])];
			$this->model->setLog($user['user_id'], 'update password');
		} else $request = ['status' => false]; 
		die(json_encode($request, JSON_UNESCAPED_UNICODE));
	}

}
?>