<?php


class Controllers {

	public function __construct()
	{
		session_start();
		$this->verificarInactividad(); // 👈 Agregamos la verificación aquí

		$this->views = new Views();
		$this->loadModel();
	}

	private function verificarInactividad()
	{
		$inactividad = 1800; // 30 minutos

		if (isset($_SESSION['ultimo_tiempo'])) {
			$tiempoInactivo = time() - $_SESSION['ultimo_tiempo'];

			if ($tiempoInactivo > $inactividad) {
				session_unset();
				session_destroy();
				header("Location: /login?mensaje=sesion_expirada");
				exit();
			}
		}

		// Actualizamos el tiempo de última actividad
		$_SESSION['ultimo_tiempo'] = time();
	}

	public function loadModel()
	{
		$model = get_class($this)."Model";
		$routClass = "Models/".$model.".php";

		$routClassException = str_replace("Model.php", ".php", $routClass);
		if(file_exists($routClassException)){
			require_once($routClassException);
			$this->model = new $model();
		} else if(file_exists($routClass)){
			require_once($routClass);
			$this->model = new $model();
		}
	}
}


?>