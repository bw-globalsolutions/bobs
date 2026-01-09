<?php
class Personalization extends Controllers{

	public function __construct()
	{
		parent::__construct();
        session_start();
		//session_regenerate_id(true);
		
	}

	public function personalization()
	{

		$data['page_tag'] = 'Personalization';
		$data['page_title'] = "Personalization";
		$data['page_name'] = "Personalization";
        $data['page-functions_js'] = "functions_personalization.js";

		
		$this->views->getView($this, "personalization", $data);
	}

    public function guardarTema(){
        $color1 = $_POST['color1'];
        $color2 = $_POST['color2'];
        $color3 = $_POST['color3'];
        $color4 = $_POST['color4'];
        $color5 = $_POST['color5'];
        $color6 = $_POST['color6'];
        $color7 = $_POST['color7'];
        $color8 = $_POST['color8'];
        $color9 = $_POST['color9'];
        $radius1 = $_POST['radius1'];
        $radius2 = $_POST['radius2'];
        $img1 = $_POST['img1'];
        $img2 = $_POST['img2'];
        $img3 = $_POST['img3'];
        $rutaBd1 = "";
        $rutaBd2 = "";
        $rutaBd3 = "";
        $num = rand(0,99);
        $tema = $this->model->getTema();

        $ruta_archivo = 'Assets/css/colors.css';
        $nuevo_contenido = ":root {
    --color1: $color1;
    --color2: $color2;
    --color3: $color3;
    --color4: $color4;
    --color5: $color5;
    --color6: $color6;
    --color7: $color7;
    --color8: $color8;
    --color9: $color9;
    --radius: $radius1;
    --radius2: $radius2;
}";

        // Sobrescribir el archivo CSS
        file_put_contents($ruta_archivo, $nuevo_contenido);
        
        if($img1!=''){
            if($tema[0]['img1']!=''){
                //unlink($tema[0]['img1']);
            }
            if($img1=='default'){
                $rutaBd1 = "Assets/images/fondo.png";
                $imgBinaria = file_get_contents("Assets/images/fondoOriginal.png");
                $bytes = file_put_contents($rutaBd1, $imgBinaria);

            }else{
                $img1 = str_replace(' ', '+', $img1);
                $rutaBd1 = "Assets/images/fondo.png";
                $imgBinaria = base64_decode($img1);
                $bytes = file_put_contents($rutaBd1, $imgBinaria);
                /*try {
                    $rutaBd1 = convertirBase64AWebP(
                        'Assets/images/',
                        'fondo.png', // Nombre original (será reemplazado por producto123.webp)
                        $img1,
                        85 // Calidad opcional (default 80)
                    );
                    die(json_encode([
                        'msg'	=> "ruta: " . $rutaBd1,
                        'status' => 1
                    ], JSON_UNESCAPED_UNICODE));
                } catch (Exception $e) {
                    die(json_encode([
                        'msg'	=> "Error: " . $e->getMessage(),
                        'status' => 0
                    ], JSON_UNESCAPED_UNICODE));
                }*/
            }
        }else{
            if($tema[0]['img1']!=''){
                $rutaBd1=$tema[0]['img1'];
            }
        }
        if($img2!=''){
            if($tema[0]['img2']!=''){
                //unlink($tema['img2']);
            }
            if($img2=='default'){
                $rutaBd2 = "Assets/images/logo.png";
                $imgBinaria = file_get_contents("Assets/images/logoOriginal.png");
                $bytes = file_put_contents($rutaBd2, $imgBinaria);
            }else{
                $img2 = str_replace(' ', '+', $img2);
                $rutaBd2 = "Assets/images/logo.png";
                $imgBinaria = base64_decode($img2);
                $bytes = file_put_contents($rutaBd2, $imgBinaria);
            }
        }else{
            if($tema[0]['img2']!=''){
                $rutaBd2=$tema[0]['img2'];
            }
        }
        if($img3!=''){
            if($tema[0]['img3']!=''){
                //unlink($tema[0]['img3']);
            }
            if($img3=='default'){
                $rutaBd3 = "Assets/images/icono.png";
                $imgBinaria = file_get_contents("Assets/images/iconoOriginal.png");
                $bytes = file_put_contents($rutaBd3, $imgBinaria);
            }else{
                $img3 = str_replace(' ', '+', $img3);
                $rutaBd3 = "Assets/images/icono.png";
                $imgBinaria = base64_decode($img3);
                $bytes = file_put_contents($rutaBd3, $imgBinaria);
            }
            
        }else{
            if($tema[0]['img3']!=''){
                $rutaBd3=$tema[0]['img3'];
            }
        }
        $id = $_POST['id'];
        //$this->model->saveTema($id, $color1, $color2, $color3, $color4, $rutaBd1, $rutaBd2, $rutaBd3);
        die(json_encode([
			'msg'	=> 'Tema guardado correctamente',
            'status' => 1
		], JSON_UNESCAPED_UNICODE));
    }

    public function cargarTema(){
        $result = $this->model->getTema();
        die(json_encode($result, JSON_UNESCAPED_UNICODE));
    }
	
}

function reemplazarImagenWebPFromBase64($directorio, $nombreArchivo, $imagenBase64, $calidad = 80) {
    // Configuración inicial
    $nuevoNombre = $nombreArchivo . '.webp';
    $rutaDestino = $directorio . $nuevoNombre;

    return var_dump(imagetypes() & IMG_WEBP);  // Devuelve 0 si no soporta WebP
    
    // Decodificar la imagen
    $datosImagen = base64_decode($imagenBase64);
    if ($datosImagen === false) {
        throw new Exception("Error al decodificar la imagen Base64");
    }
    
    $finfo = finfo_open();
    $mime_type = finfo_buffer($finfo, $datosImagen, FILEINFO_MIME_TYPE);
    finfo_close($finfo);

    switch($mime_type){
        case 'image/jpeg':
            $imagen = imagecreatefromjpeg('data://image/jpeg;base64,'.base64_encode($datosImagen));
            break;
        case 'image/png':
            $imagen = imagecreatefrompng('data://image/png;base64,'.base64_encode($datosImagen));
            break;
        case 'image/webp':
            $imagen = imagecreatefromwebp('data://image/webp;base64,'.base64_encode($datosImagen));
            break;
        default:
            return false;
    }

    
    // Guardar como WebP
    if (!imagewebp($imagen, $rutaDestino, $calidad)) {
        imagedestroy($imagen);
        throw new Exception("Error al guardar la imagen WebP");
    }
    
    return $rutaDestino;
}

function convertirBase64AWebP($base64_string, $ruta_salida, $calidad = 80) {
    // Eliminar encabezado (si existe)
    if (strpos($base64_string, 'base64,') !== false) {
        $base64_string = explode(',', $base64_string)[1];
    }

    // Decodificar base64
    $imagen_data = base64_decode($base64_string);

    // Crear una imagen usando Imagick
    try {
        $imagick = new Imagick();
        $imagick->readImageBlob($imagen_data);
        $imagick->setImageFormat('webp');
        $imagick->setImageCompressionQuality($calidad);
        $imagick->writeImage($ruta_salida);
        $imagick->clear();
        $imagick->destroy();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

?>