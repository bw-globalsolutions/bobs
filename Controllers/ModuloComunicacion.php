<?php

class ModuloComunicacion extends Controllers{

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

	public function moduloComunicacion()
	{
		

		$data['page_tag'] = "Manual";
		$data['page_title'] = "Modulo de comunicacion";
		$data['page_name'] = "Modulo de comunicacion";
		$data['page-functions_js'] = "function_modulo_comunicacion.js";
		
		$data['permission'] = $this->permission;
		$data['locations'] = selectLocation(['id', 'name', 'number', 'country_id']);
		
		$where = '';
		
		$data['role'] = selectRole(['id', 'name', 'level'], $where);
		//$data['role'] = selectRole(['id', 'name', 'level'], (in_array($_SESSION['userData']['role']['id'], [1,2,17])? '1' : "id IN(10,11,12)") . ' AND status = 1');
		$data['brands'] = selectBrands(['id', 'name']);
		$data['paises'] = [];
		$data['country_name'] = base64_decode($_GET['country']);
		
		$tmp = selectCountries(['id', 'name', 'region'], "id IN({$_SESSION['userData']['country_id']})");
		foreach($tmp as $i){
			if (!array_key_exists($i['region'], $data['paises'])) {
				$data['paises'][$i['region']] = [];
			}
			array_push($data['paises'][$i['region']], ['id' => $i['id'], 'name' => $i['name']]);
		}

		$this->views->getView($this, "moduloComunicacion", $data);
	}



	public function setManual(){
		
		if(empty($_POST['txtCategoria']) || empty($_POST['txtDescripcion']) || empty($_POST['txtNombre'])){
			$arrResponse = array("status" => false, "msg" => "Datos incorrecto.");
		}else{

			if( $_POST['txtCategoria'] == 1){
				$txtCategoria 	= $_POST['nuevaCategoria'];
			}else{
				$txtCategoria 	= $_POST['txtCategoria'];
			}
		
			$txtDescripcion = $_POST['txtDescripcion'];
			$txtNombre 		= $_POST['txtNombre'];
			$txtArchivo 	= $_POST['evidencias_1'];
			$txtLang 		= $_POST['txtLang'];
		
			$request_user = $this->model->insertManual($txtCategoria,
												   	   $txtDescripcion,
												   	   $txtNombre,
												   	   $txtArchivo,
													   $txtLang);
		}
			
		$arrResponse = array("status" => true, "msg" => "Datos guardados correctamente.");	
			
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

		die();
	}



	

	public function getManuales(){
										

		if (!empty($_GET['country'])) {
		    $country_name = base64_decode($_GET['country']);
		    $where = "WHERE lang IN (SELECT language FROM country WHERE name = '$country_name')";
		} else {
		    $where = "WHERE lang IN ('".$_SESSION['userData']['default_language']."')"; 
		}

		$arrData = $this->model->selectManuales($where);																						
		echo json_encode($arrData,JSON_UNESCAPED_UNICODE);												
		die();												
		
	}
	



	public function eliminarManual() {
		if (empty($_POST['id_manual'])) {
			$arrResponse = array("status" => false, "msg" => "ID del manual incorrecto.");
		} else {
			$id_manual = $_POST['id_manual'];
	
			// Llamas al método del modelo que elimina el manual
			$request_delete = $this->model->eliminarManual($id_manual);
	
			$arrResponse = array("success" => true, "msg" => "...");
		}
	
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}


public function editarManual() {
    if (empty($_POST['id_manual']) || empty($_POST['nombre']) || empty($_POST['categoria'])) {
        $arrResponse = array("success" => false, "msg" => "Datos incompletos para la edición.");
    } else {
        $id_manual = $_POST['id_manual'];
        $nombre = $_POST['nombre'];
        $categoria = $_POST['categoria'];
        $txtLang = $_POST['txtLang'] ?? ''; // <---- agregado

        // Llamada al modelo para actualizar el manual (Agregado txtLang)
        $request_update = $this->model->editarManual($id_manual, $nombre, $categoria, $txtLang);

        if ($request_update) {
            $arrResponse = array("success" => true, "msg" => "Manual actualizado correctamente.");
        } else {
            $arrResponse = array("success" => false, "msg" => "Error al actualizar el manual.");
        }
    }

    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    die();
}




	public function viewPDF()
{
    // Obtener la URL del PDF desde GET
    $pdfUrl = isset($_GET['file']) ? $_GET['file'] : '';

    // Si quieres más seguridad, decodifica base64:
    // $pdfUrl = base64_decode($_GET['file']);

    // Enviar a la vista como array asociativo
    $this->views->getView($this, "viewPDF", [
        'pdfUrl' => $pdfUrl
    ]);
}




	
}







?>