<?php
	require_once("Secrets.php");

	define("BASE_URL", $cBASE_URL);

	//Zona horaria
	date_default_timezone_set('America/Mexico_City');

	//Datos de conexion a la base de datos
	define("DB_HOST", $cDB_HOST);
	define("DB_NAME", $cDB_NAME);
	define("DB_USER", $cDB_USER);
	define("DB_PASSWORD", $cDB_PASSWORD);
	define("DB_CHARSET", "charset=utf8");
	define("NOMBRE_EMPESA", $cEMPRESA);
	define("WEB_EMPRESA", $cEMPRESA);
	
	define("ON_SELFAUDIT", $ON_SELFAUDIT);
	
	define("ON_NA", $ON_NA);
	define("ON_AA", $ON_AA);
	


//Datos Sistema de correos
	define("EMAIL_SERVICE", $cEMAIL_SERVICE);
//AWS
    define("AWS_ACCESS_KEY_ID", $cAWS_ACCESS_KEY_ID);
    define("AWS_SECRET_ACCESS_KEY", $cAWS_SECRET_ACCESS_KEY);

    //SG
    define('HOST_MAIL', $cHostMail);
    define('PORT_MAIL', $cPortMail);
    define('PASS_MAIL', $cPassMail);




	//Llave Abstact
	define('API_KEY_ABSTRACT', $cAPI_KEY_ABSTRACT);
	

	//Validacion de correos
	define('DAYS_NOTIFY_EMAIL', $cDAYS_NOTIFY_EMAIL);
	define('DAYS_VALIDATE_EMAIL', $cDAYS_VALIDATE_EMAIL);
	define('IS_EMAIL_VALIDATE', $cIS_EMAIL_VALIDATE);

	//Delimitadores decimal y millar Ej. 85,541.50
	const SPD = ".";
	const SPM = ",";

	//Simbolo de moneda
	const SMONEY = "$";

	//Datos envio de correo
	const NOMBRE_REMITENTE = "Audits DQ";
	const EMAIL_REMITENTE = "no-reply@bw-globalsolutions.net";
	const EMAIL_DEFAULT = "no-reply@bw-globalsolutions.net";
?>