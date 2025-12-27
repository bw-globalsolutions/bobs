<?php
require_once("Config/Config.php");
require_once("Helpers/Helpers.php");
require_once("Helpers/Validators.php");
require_once("Config/Translate.php");

$url = !empty($_GET['url']) ? $_GET['url'] : "home/home";
$arrUrl = explode("/", $url);
$controller = $arrUrl[0];
$method = $arrUrl[0];
$params = "";
//echo $controller.'<br>';
if(!empty($arrUrl[1])){
	if($arrUrl[1] != "") $method = $arrUrl[1];
}

if(!empty($arrUrl[2])){
	if($arrUrl[2] != "")
	{
		for($i=2; $i<count($arrUrl); $i++){
			$params .= str_replace(',','_',$arrUrl[$i]).',';
		}
		$params = trim($params,',');
	}
}

$idioma = $_SESSION['userData']['idioma'] ?? 'latino';

require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");

/*
echo $controller."<br>";
echo $method."<br>";
echo $params."<br>";
*/
//echo ceil(0.42);
?>