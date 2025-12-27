<?php

require 'vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Credentials\CredentialProvider;

function base_url()
{
	return BASE_URL;
}

function media()
{
	return BASE_URL."/Assets";
}

function headerTemplateActualizado($data="")
{
	$view_header = "Views/Template/headerActualizado.php";
	require_once($view_header);
}

function headerTemplate($data="")
{
	$view_header = "Views/Template/header.php";
	require_once($view_header);
}

function headerTemplateResults($data="")
{
	$view_header = "Views/Template/header_results.php";
	require_once($view_header);
}

function headerTemplatePopups($data="")
{
	$view_header = "Views/Template/header_popups.php";
	require_once($view_header);
}

function headerFilter($data="")
{
	$view_header = "Views/Template/header_filter.php";
	require_once($view_header);
}

function headerTemplateAudits($id, $sect = ""){
	$tmp = selectAuditList(['round_name', 'brand_id', 'country_id', 'region', 'country_name', 'brand_prefix', 'location_number', 'location_address', 'location_name', 'status', 'visit_status', 'date_visit', 'type', 'id', 'checklist_id', 'report_layout_id', 'scoring_id'], "id=$id");	
	if(!empty($tmp)){
		$data = $tmp[0];
		
		$steps = ['Temp Processing', 'Pending', 'In Process', 'Completed'];
		$index = array_search($data['status'], $steps);
		$data['next-status'] = $steps[$index + 1];
				
		if($data['visit_status'] != 'Visited' && !in_array($sect, ['General info', 'Audit Tools'])){
			die("<script>location.href='" . base_url() ."/audits/auditInfo?id=$id'</script>");
		}
		
		$data['url_report'] = getURLReport($id, $data['report_layout_id'], $_SESSION['userData']['default_language']);
		$data['score'] = getScore($data['id'], $data['scoring_id']);
		$data['score_previus'] = empty($data['date_visit'])? false : getScorePrevius($data['location_number'], $data['type'], $data['date_visit']);
		
		$tmp = selectAuditFiles(
			['url'],
			"audit_id = {$data['id']} AND name IN ('Foto de fachada de la tienda', 'Picture of the Front Door/Entrance of the Restaurant') 
		");
		$visit_img = $tmp[0]['url']?? base_url().'/Assets/images/generic_restaurant.jpg';;

		$view_header = "Views/Template/header_audits.php";
		require_once($view_header);
	} else {
		die("<script>location.href='".base_url()."'</script>");
	}
}

function progressTemplateActionPlan($id){

	global $fnT;
	$fnT = translate($_SESSION['userData']['default_language']);
	require_once("Models/ActionPlanModel.php");
	require_once 'Models/AuditoriaModel.php';
	$objData = new ActionPlanModel();

	$dataP = $objData->getOppsPlan($id);

	//dep($dataP);
	$totalPending=0;
	$totalReview=0;
	$totalRejected=0;
	$totalApproved=0;
	$totalFinished=0;

	$generalStatus = 'Pending';

	for($i=0; $i<count($dataP); $i++){
		//echo 'Pending - '.$dataP[$i]['id_audit_opp'];
		if ($dataP[$i]['actionplan_status'] == 'In Review') {
			$totalReview ++;
			//echo $dataP[$i]['actionplan_status'].' - '.$dataP[$i]['id_audit_opp'];
		} else if ($dataP[$i]['actionplan_status'] == 'Rejected') {
			$totalReview ++;
			$totalRejected ++;
			//echo $dataP[$i]['actionplan_status'].' - '.$dataP[$i]['id_audit_opp'];
		} else if ($dataP[$i]['actionplan_status'] == 'Approved') {
			$totalReview ++;
			$totalApproved ++;
			//echo $dataP[$i]['actionplan_status'].' - '.$dataP[$i]['id_audit_opp'];
		} else if ($dataP[$i]['actionplan_status'] == 'Finished') {
			$totalFinished ++;
			//echo $dataP[$i]['actionplan_status'].' - '.$dataP[$i]['id_audit_opp'];
		} else {
			$totalPending ++;
			//echo 'Pending - '.$dataP[$i]['id_audit_opp'];
		}
	}

	//Verificar sttaus general y actualizar el status general de la auditoria 
	

	//echo $totalPending;

	$dataP['totalOpps'] = count($dataP);
	if ($dataP['totalOpps']>0) {
		$dataP['totalPending'] = round($totalPending * 100 / $dataP['totalOpps'],2);
		$dataP['totalReview'] = round($totalReview * 100 / $dataP['totalOpps'],2);
		$dataP['totalRejected'] = round($totalRejected * 100 / $dataP['totalOpps'],2);
		$dataP['totalApproved'] = round($totalApproved * 100 / $dataP['totalOpps'],2);
		$dataP['totalFinished'] = round($totalFinished * 100 / $dataP['totalOpps'],2);
	} else {
		$dataP['totalPending'] = 0;
		$dataP['totalReview'] = 0;
		$dataP['totalRejected'] = 0;
		$dataP['totalApproved'] = 0;
		$dataP['totalFinished'] = 0;
	}

	if ( $dataP['totalReview'] > 0 && $dataP['totalReview'] < 100) {
		$generalStatus = 'In Process';
		$updateAuditValues = [
			'action_plan_status' => $generalStatus
		];
		$request_audit = AuditoriaModel::updateAudit($updateAuditValues, "id=$id");
	} else if ( $dataP['totalFinished'] == 100) {
		$generalStatus = 'Finished';
		$updateAuditValues = [
			'action_plan_status' => $generalStatus
		];
		$request_audit = AuditoriaModel::updateAudit($updateAuditValues, "id=$id");

		$audit = selectAuditList([], 'id='.$id)[0];

		 
		if($audit['country_language'] == 'esp'){
			$templateMail = 'ap_finished';
			$asunto = 'Plan de accion finalizado';
		}else{
			$templateMail = 'ap_finished_eng';
			$asunto = 'Action plan completed';


		}

		if($audit['type'] != 'Calibration Audit'){
			$locationMails = getLocationEmails(['Fanchisee' , 'Ops Director' , 'Ops Leader' , 'Area Manager' , 'Store Manager'], $audit['location_id']);
		}
		$recipients = emailFilter("{$audit['manager_email']},$locationMails");

		sendEmail([
			'asunto' 				=> "{$audit['brand_prefix']} #{$audit['location_number']} ({$audit['country_name']}) @ ".$asunto,
			'email' 				=> $recipients,
			'audit_id'				=> $id,
			'type'					=> $audit['type'],
			'location_number'		=> $audit['location_number'],
			'location_address'		=> $audit['location_address'],
			'url_report'			=> getURLReport($id, $audit['report_layout_id'],$audit['country_language'])
		], $templateMail);

		
	}
	
	
	$view_header = "Views/Template/progress_action_plan.php";

	require_once($view_header);
}


function footerTemplateActualizado($data="")
{
	$view_footer = "Views/Template/footerActualizar.php";
	require_once($view_footer);
}

function footerTemplate($data="")
{
	$view_footer = "Views/Template/footer.php";
	require_once($view_footer);
}

function dep($data)
{
	$format = print_r('<pre>');
	$format .= print_r($data);
	$format .= print_r('</pre>');
	return $format;
}

function getModal(string $nameModal, $data)
{
	$view_modal = "Views/Template/Modals/{$nameModal}.php";
	require_once($view_modal);
}

function emailFilter($emails, $retArray = false){
	$out_emails = [];
	foreach(explode(',', $emails) as $m){
		$m = trim($m);
		if (filter_var($m, FILTER_VALIDATE_EMAIL)) {
			array_push($out_emails, $m);
		}
	}
	if($retArray){
		return array_unique($out_emails);
	}
	return implode(',', array_unique($out_emails));
}

function emailFilterArg($emails){
	$out_emails2 = [];
	foreach(explode(',', $emails) as $m){
		if(strpos($m, 'arguilea')!==false || strpos($m, 'bw-globalsolutions')!==false) {
			continue;
		} else {
			array_push($out_emails2, $m);
		}
	}
	return implode(',', array_unique($out_emails2));
}

function emailSend($to = NULL, $subject, $body, $cc = NULL, $bcc = NULL)
{
	if (strpos($_SERVER['HTTP_HOST'], '-stage.') !== false) {
		
		$sendTo = emailFilter($to, $cc, $bcc);
		$subject .= " (ENTORNO DE PRUEBA)";
		$body = "<tr>
			<td style='text-align: center; padding:10px;background:#7FDFD4; color:black; font-size:11px'>
			<span><b>SEND TO: $sendTo</b></span></td>
		</tr>" . $body;
		
		//$to='mosorio@bw-globalsolutions.com,emaldonado@bw-globalsolutions.com,epena@bw-globalsolutions.com'; 
		//$to='emaldonado@bw-globalsolutions.com'; 
		$to='mosorio@bw-globalsolutions.com,dpeza@bw-globalsolutions.com,emaldonado@bw-globalsolutions.com'; 

		$cc=NULL; 
		$bcc=NULL;
		//$bcc='mosorio@bw-globalsolutions.com,dpeza@bw-globalsolutions.com,ycabello@bw-globalsolutions.com,schirino@bw-globalsolutions.com,';

	} else{
		require_once("Controllers/EmailValidator.php");
		$objEmailValidator = new EmailValidator();

		if(IS_EMAIL_VALIDATE) {
			if(!empty($to)){
				$to = $objEmailValidator->validate_email($to);
			}
			if(!empty($cc)){
				$cc = $objEmailValidator->validate_email($cc);
			}
			if(!empty($bcc)){
				$bcc = $objEmailValidator->validate_email($bcc);
			}
		}
	}

	if (!empty($to)) {
		$toArray = explode(",", str_replace(" ", "", $to));
		$toArray = array_filter($toArray);
	}
	if (!empty($cc)) {
		$ccArray = explode(",", str_replace(" ", "", $cc));
		$ccArray = array_filter($ccArray);
	}
	if (!empty($bcc)) {
		$bccArray = explode(",", str_replace(" ", "", $bcc));
		$bccArray = array_filter($bccArray);
	}
	/*
	$SesClient = SesClient::factory([
		'region' 	=> 'eu-west-1',
		'version' 	=> 'latest',
		'credentials' 	=> array(
			'key' 		=> AWS_ACCESS_KEY_ID,
			'secret'	=> AWS_SECRET_ACCESS_KEY,
		)
	]);

	$sender_email = EMAIL_REMITENTE;
	$char_set = 'UTF-8';

	$destination = [];
	if (!empty ($toArray)){
		$destination ["ToAddresses"] = $toArray;
	}
	if (!empty ($ccArray)){
		$destination ["CcAddresses"] = $ccArray;
	}
	if (!empty ($bccArray)){
		$destination ["BccAddresses"] = $bccArray;
	}

	try {
		$result = $SesClient->sendEmail([
			'Destination' => $destination,
			'ReplyToAddresses' => [$sender_email],
			'Source' => $sender_email,
			'Message' => [
				'Body' => [
					'Html' => [
						'Charset' => $char_set,
						'Data' => $body,
					],
					'Text' => [
						'Charset' => $char_set,
						'Data' => $body,
					],
				],
				'Subject' => [
					'Charset' => $char_set,
					'Data' => $subject,
				],
			],
		]);
		$messageId = $result['MessageId'];
		return true;
	} catch (AwsException $e) {
		// output error message if fails
		echo $e->getMessage();
		echo ("The email was not sent. Error message: " . $e->getAwsErrorMessage() . "\n");
		return false;
	}*/
	if (EMAIL_SERVICE == "AWS"){
		return emailSendAWS($to, $subject, $body, $cc, $bcc);
	} else if (EMAIL_SERVICE == "SG") {
		return emailSendSG($to, $subject, $body, $cc, $bcc);
	}
};


function emailSendAWS($to, $subject, $body, $cc, $bcc)
{
	$SesClient = SesClient::factory([
		'region' 	=> 'eu-west-1',
		'version' 	=> 'latest',
		'credentials' 	=> array(
			'key' 		=> AWS_ACCESS_KEY_ID,
			'secret'	=> AWS_SECRET_ACCESS_KEY,
		)
	]);

	$sender_email = 'no-reply@bw-globalsolutions.net';
	$char_set = 'UTF-8';

	$destination = [];
	if (!empty ($to)){
		$destination ["ToAddresses"] = explode(',', $to);
	}
	if (!empty ($cc)){
		$destination ["CcAddresses"] = explode(',', $cc);
	}
	if (!empty ($bcc)){
		$destination ["BccAddresses"] = explode(',', $bcc);
	}

	try {
		$SesClient->sendEmail([
			'Destination' => $destination,
			'ReplyToAddresses' => [$sender_email],
			'Source' => $sender_email,
			'Message' => [
				'Body' => [
					'Html' => [
						'Charset' => $char_set,
						'Data' => $body,
					],
					'Text' => [
						'Charset' => $char_set,
						'Data' => $body,
					],
				],
				'Subject' => [
					'Charset' => $char_set,
					'Data' => $subject,
				],
			],
		]);
		return true;
	} catch (AwsException $e) {
		return false;
	}
};

function emailSendSG($to, $subject, $body, $cc, $bcc){

	set_include_path('/usr/local/lib/php/');
	require_once "Mail.php";
	
	$headers['Content-type'] = "text/html; charset=utf-8";
	$headers['Subject'] = $subject;
	$headers['From'] = 'Audits DQ <no-reply@bw-globalsolutions.com>';
	
	if($to != NULL){ $headers['To'] = $to; $recipients .= ($recipients<>''?',':'').$to; }
	if($cc != NULL){ $headers['Cc'] = $cc; $recipients .= ($recipients<>''?',':'').$cc; }
	if($bcc != NULL){ $headers['Bcc'] = $bcc; $recipients .= ($recipients<>''?',':'').$bcc; }
	
	$smtp = Mail::factory('smtp', array('host' => HOST_MAIL,
										'port' => PORT_MAIL,
										'auth' => true,
										'username' => 'apikey',
										'password' => PASS_MAIL));
	
	if($to<>NULL) $mail = $smtp->send($recipients, $headers, $body);
	
	$msg = (PEAR::isError($mail)) ? false : true;
	
	if(PEAR::isError($mail))
		return false;
	
	return $msg;
};




function sendEmail($data, $template){
    $asunto = $data['asunto'];
    $emailDestino = $data['email'];
	$bcc = $data['bcc']?? null;
    $empresa = NOMBRE_REMITENTE;
    $remitente = EMAIL_REMITENTE;
    //ENVIO DE CORREO
    $de = "MIME-Version: 1.0\r\n";
    $de .= "Content-type: text/html; charset=UTF-8\r\n";
    $de .= "From: {$empresa} <{$remitente}>\r\n";
    ob_start();
    require_once("Views/Template/Email/".$template.".php");
    $mensaje = ob_get_clean();
    //$send = mail($emailDestino, $asunto, $mensaje, $de);
	if(NOMBRE_EMPESA == 'Arguilea'){
		$emailDestino = 'alejandro@bw-globalsolutions.com';
		$cc = NULL;
		$bcc = NULL;
	}
	if($data['type'] == 'Self-Evaluation'){
		$send = emailSend($emailDestino, $asunto, $mensaje, NULL, 'mosorio@bw-globalsolutions.com,emaldonado@bw-globalsolutions.com');
	}else{
		$send = emailSend($emailDestino, $asunto, $mensaje, NULL, 'mosorio@bw-globalsolutions.com,dpeza@bw-globalsolutions.com,emaldonado@bw-globalsolutions.com');
	}
	
    
    return $send;
}

function sendEmailMasivePlanSecond($data,$template){
    $asunto = $data['asunto'];
    $emailDestino = $data['email'];
	$bcc = $data['bcc'];
    $empresa = NOMBRE_REMITENTE;
    $remitente = EMAIL_REMITENTE;
    //ENVIO DE CORREO
    $de = "MIME-Version: 1.0\r\n";
    $de .= "Content-type: text/html; charset=UTF-8\r\n";
    $de .= "From: {$empresa} <{$remitente}>\r\n";

    $mensaje = '<!DOCTYPE html>
					<html lang="en">
					<head>
						<meta charset="UTF-8">
						<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
						<title>Second Segundo recordatorio del Plan de Acción</title>
					</head>
					<body>
						<table border="0" align="left" cellpadding="3" cellspacing="2">
							<tbody>
								<tr>
									<td style="padding:5px;border:solid 1px #eeeeee;font-size:12px">
										<table width="100%"" border="0" cellspacing="0" cellpadding="5" style="font-size:11px;font-family:Arial,Helvetica,sans-serif">
											<tbody>
												<tr><td width="717"></td></tr>
												<tr>
													<td style="text-align: center; padding:10px;background:#cf0a2c; color:#ffffff; font-size:11px">
													<span>Second Segundo recordatorio del Plan de Acción</span></td>
												</tr>
												<tr>
													<td style="font-size:14px;padding:0px">
														<div style="border:1px solid #eee;padding:10px">
															<ul>
																<li><b>Ubicación: '.$data['brand_prefix'].' #'.$data['location_number'].', '.$data['location_name'].'</b></li>
																<li><b>Ronda: '.$data['type'].', '.$data['round_name'].'</b></li>
																<li><b>Fecha de visita: '. date("F j, Y, h:m", strtotime( $data['date_visit'] )) .'</b></li>
																<li><b>Liberación del Informe Final:: '. date("F j, Y, h:m", strtotime( $data['date_release'] )) .'</b></li>
																<li><b>Puntuación: '.$data['score'].'</li>
																<li><b>Oportunidades pendientes a completar: '.$data['total_opps'].'</b></li>
															</ul>
															<hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee">
															<div>
																<span>Recuerde que tiene un máximo de '. $data['limit_days'] .' para completar el plan de acción</span><br>
																<span>Fecha límite: '. $data['date_limit'] .'</span>
															</div>
														</div>
													</td>
												</tr>
												<tr>
													<td><b>Created:</b> '.date('M d - h:i', time()).'</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</body>
					</html>';
    //$send = mail($emailDestino, $asunto, $mensaje, $de);
	if(NOMBRE_EMPESA == 'Arguilea'){
		$emailDestino = 'alejandro@bw-globalsolutions.com';
		$cc = NULL;
		$bcc = NULL;
	}
    $send = emailSend($emailDestino, $asunto, $mensaje, NULL, $bcc);
    return $send;
}

function sendEmailMasivePlanFinal($data,$template){
    $asunto = $data['asunto'];
    $emailDestino = $data['email'];
	$bcc = $data['bcc'];
    $empresa = NOMBRE_REMITENTE;
    $remitente = EMAIL_REMITENTE;
    //ENVIO DE CORREO
    $de = "MIME-Version: 1.0\r\n";
    $de .= "Content-type: text/html; charset=UTF-8\r\n";
    $de .= "From: {$empresa} <{$remitente}>\r\n";
	$divStage = '';
	if (strpos($_SERVER["HTTP_HOST"], "-stage.") !== false) {
		$divStage = '<tr>
						<td style="text-align: center; padding:10px;background:#7FDFD4; color:black; font-size:11px">
						<span><b>TO STAGE: '.$data['email'].'</b></span></td>
					</tr>';
	}
    $mensaje = '<!DOCTYPE html>
					<html lang="en">
					<head>
						<meta charset="UTF-8">
						<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
						<title>Final Action Plan Reminder</title>
					</head>
					<body>
						<table border="0" align="left" cellpadding="3" cellspacing="2">
							<tbody>
								<tr>
									<td style="padding:5px;border:solid 1px #eeeeee;font-size:12px">
										<table width="100%"" border="0" cellspacing="0" cellpadding="5" style="font-size:11px;font-family:Arial,Helvetica,sans-serif">
											<tbody>
												<tr><td width="717"></td></tr>
												<tr>
													<td style="text-align: center; padding:10px;background:#cf0a2c; color:#ffffff; font-size:11px">
													<span>'.$data['title_layout'].'</span></td>
												</tr>
												'.$divStage.'
												<tr>
													<td style="text-align: center; vertical-align: middle;">
														<img class="img-fluid" style="width: 200px;" src="'. media() .'/images/logo_'. NOMBRE_EMPESA . '.jpg" alt="Logo">
													</td>
												</tr>
												<tr>
													<td style="font-size:14px;padding:0px">
														<div style="border:1px solid #eee;padding:10px">
															<ul>
																<li><b>Location: '.$data['brand_prefix'].' #'.$data['location_number'].', '.$data['location_name'].'</b></li>
																<li><b>Round: '.$data['type'].', '.$data['round_name'].'</b></li>
																<li><b>Date of visit: '. date("F j, Y, h:m", strtotime( $data['date_visit'] )) .'</b></li>
																<li><b>Release of the Final Report: '. date("F j, Y, h:m", strtotime( $data['date_release'] )) .'</b></li>
																<li><b>Score: '.$data['score'].', '.getScoreDefinition($data['score'])[1].', Auto Fail: '.$data['auto_fail'].'</b></li>
																<li><b>Pending opportunities to fill: '.$data['total_opps'].'</b></li>
															</ul>
															<hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee">
															<div>
																<span>'.$data['disclaimer'].'</span><br>
															</div>
														</div>
													</td>
												</tr>
												<tr>
													<td><b>Created:</b> '.date('M d - h:i', time()).'</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</body>
					</html>';
    //$send = mail($emailDestino, $asunto, $mensaje, $de);
	if(NOMBRE_EMPESA == 'Arguilea'){
		$emailDestino = 'alejandro@bw-globalsolutions.com';
		$cc = NULL;
		$bcc = NULL;
	}
    $send = emailSend($emailDestino, $asunto, $mensaje, NULL, $bcc);
    return $send;
}

function sendEmailMasiveTable($data){
    $asunto = $data['asunto'];
    $emailDestino = $data['email'];
	$bcc = $data['bcc'];
    $empresa = NOMBRE_REMITENTE;
    $remitente = EMAIL_REMITENTE;
    //ENVIO DE CORREO
    $de = "MIME-Version: 1.0\r\n";
    $de .= "Content-type: text/html; charset=UTF-8\r\n";
    $de .= "From: {$empresa} <{$remitente}>\r\n";
	$divStage = '';
    $mensaje = '<!DOCTYPE html>
					<html lang="en">
					<head>
						<meta charset="UTF-8">
						<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
						<title>'.$data['title_layout'].'</title>
					</head>
					<body>
						<table border="0" align="left" cellpadding="3" cellspacing="2">
							<tbody>
								<tr>
									<td style="padding:5px;border:solid 1px #eeeeee;font-size:12px">
										<table width="100%"" border="0" cellspacing="0" cellpadding="5" style="font-size:11px;font-family:Arial,Helvetica,sans-serif">
											<tbody>
												<tr><td width="717"></td></tr>
												<tr>
													<td style="text-align: center; padding:10px;background:#cf0a2c; color:#ffffff; font-size:11px">
													<span>'.$data['title_layout'].'</span></td>
												</tr>
												<tr>
													<td style="font-size:14px;padding:0px">
														<div style="border:1px solid #eee;padding:10px">
															<hr style="margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee">
															<div>
																<span>'. $data['disclaimer'] .'</span>
															</div>
															<div style="justify-content: center;">
																<ul>'.$data['tiendas'].'</ul>
															</div>
														</div>
													</td>
												</tr>
												<tr>
													<td><b>Created:</b> '.date('M d - h:i', time()).'</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</body>
					</html>';
    //$send = mail($emailDestino, $asunto, $mensaje, $de);
	if(NOMBRE_EMPESA == 'Arguilea'){
		$emailDestino = 'alejandro@bw-globalsolutions.com';
		$cc = NULL;
		$bcc = NULL;
	}
    $send = emailSend($emailDestino, $asunto, $mensaje, NULL, $bcc);
    return $send;
}

function sendEmailMasive($data, $template){
    $asunto = $data['asunto'];
    $emailDestino = $data['email'];
	$bcc = $data['bcc']?? null;
    $empresa = NOMBRE_REMITENTE;
    $remitente = EMAIL_REMITENTE;
    
	//ENVIO DE CORREO
    $de = "MIME-Version: 1.0\r\n";
    $de .= "Content-type: text/html; charset=UTF-8\r\n";
    $de .= "From: {$empresa} <{$remitente}>\r\n";
    
	//Retorna la variable $mensaje
	require_once("Views/Template/EmailMasive/".$template.".php");
	$mensaje = call_user_func($template, $data);

	$send = emailSend($emailDestino, $asunto, $mensaje, NULL, 'mosorio@bw-globalsolutions.com,dpeza@bw-globalsolutions.com,ycabello@bw-globalsolutions.com,emaldonado@bw-globalsolutions.com');

    return $send;
}

function sendAnnouncedVisit($data,$template){
	$asunto = $data['asunto'];
    $emailDestino = $data['email'];
	$bcc = $data['bcc'];
	$cc = $data['cc'];
    //$asunto =  $data['bcc'];
    //$emailDestino='mosorio@bw-globalsolutions.com';
    //$empresa = 'ARGUILEA';
    //$remitente = '';
    //ENVIO DE CORREO
    $de = "MIME-Version: 1.0\r\n";
    $de .= "Content-type: text/html; charset=UTF-8\r\n";
    $de .= "From: {$empresa} <{$remitente}>\r\n";
    ob_start();
    require_once("Views/Template/Email/".$template.".php");
    $mensaje = ob_get_clean();
    //$send = mail($emailDestino, $asunto, $mensaje, $de);
	if(NOMBRE_EMPESA == 'Arguilea'){
		$emailDestino = 'alejandro@bw-globalsolutions.com';
		$cc = NULL;
		$bcc = NULL;
	}
    $send = emailSend($emailDestino, $asunto, $mensaje, $cc, $bcc);
    return $send;
}

function sendPlanReminder($data,$template){
	$asunto = $data['asunto'];
    $emailDestino = $data['email'];
    $cc = $data['cc'];
    //$emailDestino='mosorio@bw-globalsolutions.com';
    $empresa = 'ARGUILEA';
    $remitente = NOMBRE_EMPESA;
    //ENVIO DE CORREO
    $de = "MIME-Version: 1.0\r\n";
    $de .= "Content-type: text/html; charset=UTF-8\r\n";
    $de .= "From: {$empresa} <{$remitente}>\r\n";
    ob_start();
    require_once("Views/Template/Email/".$template.".php");
    $mensaje = ob_get_clean();
    //$send = mail($emailDestino, $asunto, $mensaje, $de);
	if(NOMBRE_EMPESA == 'Arguilea'){
		$emailDestino = 'alejandro@bw-globalsolutions.com';
		$cc = NULL;
		$bcc = NULL;
	}
    $send = emailSend($emailDestino, $asunto, $mensaje, $cc, NULL);
    return $send;
}

function sendAviso($data,$template){
		$numeroTienda = $data['numeroTienda'];
		$nombreTienda = $data['nombreTienda'];
		$titulo = $data['titulo'];
		$titulo2 = $data['titulo2'];
		$periodo = $data['periodo'];
		$pais = $data['pais'];
		$emailDestino = $data['destinatario'];
		$cc = $data['cc'];
		$asunto = $data['asunto'];
		$url = $data['url'];
		//$cc='';
		//$emailDestino='alejandro@arguilea.com,test@test.com';
    $empresa = NOMBRE_REMITENTE;
    $remitente = EMAIL_REMITENTE;
    //ENVIO DE CORREO
    $de = "MIME-Version: 1.0\r\n";
    $de .= "Content-type: text/html; charset=UTF-8\r\n";
    $de .= "From: {$empresa} <{$remitente}>\r\n";
    ob_start();
    require_once("Views/Template/Email/".$template.".php");
    $mensaje = ob_get_clean();
    //$send = mail($emailDestino, $asunto, $mensaje, $de);
	if(NOMBRE_EMPESA == 'Arguilea'){
		$emailDestino = 'alejandro@bw-globalsolutions.com';
		$cc = NULL;
		$bcc = NULL;
	}
    $send = emailSend($emailDestino, $asunto, $mensaje, $cc, NULL);
    return $send;
}

function getPermisos(int $idmodulo){
	require_once("Models/PermisosModel.php");
	$objPermisos = new PermisosModel();
	$id_rol = $_SESSION['userData']['role_id'];
	$arrPermisos = $objPermisos->permisosModulo($id_rol);
	$permisos = "";
	$permisosMod = "";
	if(count($arrPermisos) > 0){
		$permisos = $arrPermisos;
		$permisosMod = isset($arrPermisos[$idmodulo]) ? $arrPermisos[$idmodulo] : "";
	}
	$_SESSION['permisos'] = $permisos;
	$_SESSION['permisosMod'] = $permisosMod;
}

function sessionUser(int $idUsuario){
	require_once("Models/LoginModel.php");
	$objLogin = new LoginModel();
	$request = $objLogin->sessionLogin($idUsuario);
	return $request;
}

function strClear($strCadena)
{
	$string = preg_replace(['/\s+/','/^\s|\s$/'],[' ',''], $strCadena);
	$string = trim($string);
	$string = stripslashes($string);
	$string = str_ireplace("<script>","",$string);
	$string = str_ireplace("</script>","",$string);
	$string = str_ireplace("<script src>","",$string);
	$string = str_ireplace("<script type=>","",$string);
	$string = str_ireplace("SELECT * FROM","",$string);
	$string = str_ireplace("DELETE FROM","",$string);
	$string = str_ireplace("INSERT INTO","",$string);
	$string = str_ireplace("SELECT COUNT(0) FROM","",$string);
	$string = str_ireplace("DROP TABLE","",$string);
	$string = str_ireplace("OR '1'='1","",$string);
	$string = str_ireplace('OR "1"="1',"",$string);
	$string = str_ireplace('OR ´1´=´1',"",$string);
	$string = str_ireplace("is NULL; --","",$string);
	$string = str_ireplace("is NULL; --","",$string);
	$string = str_ireplace("LIKE '","",$string);
	$string = str_ireplace('LIKE "',"",$string);
	$string = str_ireplace('LIKE ´',"",$string);
	$string = str_ireplace("OR 'a'='a","",$string);
	$string = str_ireplace('OR "a"="a',"",$string);
	$string = str_ireplace('OR ´a´=´a',"",$string);
	$string = str_ireplace("--","",$string);
	$string = str_ireplace("^","",$string);
	$string = str_ireplace("[","",$string);
	$string = str_ireplace("]","",$string);
	$string = str_ireplace("==","",$string);
	return $string;
}

function passGenerator($length = 10)
{
	$pass = "";
	$longitudPass = $length;
	$cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
	$longitudCadena=strlen($cadena);

	for($i=1; $i<=$longitudPass; $i++)
	{
		$pos = rand(0,$longitudCadena-1);
		$pass .= substr($cadena,$pos,1);
	}
	return $pass;
}

function token()
{
	$r1 = bin2hex(random_bytes(10));
	$r2 = bin2hex(random_bytes(10));
	$r3 = bin2hex(random_bytes(10));
	$r4 = bin2hex(random_bytes(10));
	$token = $r1.'-'.$r2.'-'.$r3.'-'.$r4;
	return $token;
}

function formatMoney($cantidad)
{
	$cantidad = number_format($cantidad,2,SPD,SPM);
	return $cantidad;
}

function selectRolUsers($select, $where=null){
	require_once("Models/UsuariosModel.php");
	$objData = new UsuariosModel();
	$arrUsers = $objData->getUsersByRol($select, $where);
	return $arrUsers;
}

function selectCountries($select, $where=null){
	require_once("Models/CountryModel.php");
	$objData = new CountryModel();
	$arrCountry = $objData->getCountry($select, $where);
	return $arrCountry;
}

function selectBrands($select, $where=null){
	require_once("Models/BrandModel.php");
	$objData = new BrandModel();
	$arrBrands = $objData->getBrand($select, $where);
	return $arrBrands;
}

function selectModules($select, $where=null){
	require_once("Models/PermisosModel.php");
	$objData = new PermisosModel();
	$arrModule = $objData->getModule($select, $where);
	return $arrModule;
}

function selectAuditList($select, $where = null){
	require_once("Models/AuditsModel.php");
	$objData = new AuditsModel();
	$arrAudit = $objData->getAuditList($select, $where);
	return $arrAudit;
}

function selectAudit($select, $where = null){
	require_once("Models/AuditsModel.php");
	$objData = new AuditsModel();
	$arrAudit = $objData->getAudits($select, $where);
	return $arrAudit;
}

function selectAuditFiles($select, $where = null){
	require_once("Models/Audit_FileModel.php");
	$objData = new Audit_FileModel();
	$arrFiles = $objData->getAudit_File($select, $where);
	return $arrFiles;
}

function selectOpportunity($select, $where = null){
	require_once("Models/Audit_OppModel.php");
	$objData = new Audit_OppModel();
	$arrOpportunity = $objData->getAudit_Opp($select, $where);
	return $arrOpportunity;
}

function selectLocation($select, $where = null){
	require_once("Models/LocationModel.php");
	$objData = new LocationModel();
	$arrLocation = $objData->getLocation($select, $where);
	return $arrLocation;
}

function selectRole($select, $where = null){
	require_once("Models/RolesModel.php");
	$objData = new RolesModel();
	$arrRole = $objData->getRole($select, $where);
	return $arrRole;
}

function selectRound($select, $where = null){
	require_once("Models/RoundModel.php");
	$objData = new RoundModel();
	$arrRound = $objData->getRound($select, $where);
	return $arrRound;
}

function listAnswers($lan, $checklist_item_id){
	require_once("Models/Checklist_ItemModel.php");
	$objData = new Checklist_ItemModel();
	$arrAnswers = $objData->getChecklistItem(["IFNULL({$lan}_answer, eng_answer) txt"], 'id='. $checklist_item_id)[0]['txt'];
	
	$response = array_reduce(explode('|', $arrAnswers), function($acc, $item) {
		$acc[explode('.-', $item)[0]] = $item;
		return $acc;
	}, []);
	
	return $response;
}

function listAuditTypes(){
	require_once("Models/AuditsModel.php");
	$objData = new AuditsModel();
	$arrTypes = $objData->getTypes();
	return $arrTypes;
}

function listSeccions($checklist_id, $audited_areas = null){
	require_once("Models/Checklist_ItemModel.php");
	$objData = new Checklist_ItemModel();

	$decodedAreas = json_decode($audited_areas, true);
$areaList = 'AND area IS NULL';

if (!empty($decodedAreas) && is_array($decodedAreas)) {
    $areaList = '"' . implode('","', $decodedAreas) . '"';
}
	$filter_area = is_null($audited_areas)? 'AND area IS NULL ' : 'AND area IN(' . $areaList  . ') OR (area IS NULL AND checklist_id = ' . $checklist_id  . ')';


	$arrSection = $objData->getChecklistSection("checklist_id = $checklist_id $filter_area");
	return $arrSection;
}

function listMailExceptions($case, $reference_id = null){
	require_once("Models/UsuariosModel.php");
	$objData = new UsuariosModel();
	$listMails = $objData->getMailExceptions($case, $reference_id);
	return $listMails;
}

function formLanguage(){
	$arrLanguage = [
		'eng'	=> 'en',
		'esp'	=> 'es'
	];
	$request = $arrLanguage[$_SESSION['userData']['default_language']]; 
	return $request;
}

function viewAuditorias($acceso)
{
	$tiposA = "";
	if($acceso == 'dmanager' || $acceso == 'region'){
		$tiposA = array('Ordinarias','Auto');
	}else if($acceso == 'tienda'){
		$tiposA = array('Ordinarias','Auto');
	}else{
		$tiposA = array('Ordinarias','Auto','Calibracion','Partner');
	}
	return $tiposA;
}

function replacePais($p){ 
	$p = str_replace('Mexico', 'México', $p);
	$p = str_replace('Panama', 'Panamá', $p);
	$p = str_replace('Peru', 'Perú', $p);
	return $p;
}

function knowRoundInfoBy($brand='', $period=''){
	
	//periodo formato YYYY-MM
	$year = explode("-", $period)[0];
    $month = explode("-", $period)[1];
	
	if(in_array($month, array('01', '02', '03', '04', '05', '06'))){
		$return = [
			'name' => "Round 1 $year",
			'date_start' => "$year-01-01 00:00:00"];
		
	} else if(in_array($month, array('07', '08', '09', '10', '11', '12'))){
		$return = [
			'name' => "Round 2 $year",
			'date_start' => "$year-07-01 00:00:00"];

	}

    if($month == '' || $year==''){
		$return = [
			'name' => NULL,
			'date_start' => NULL];
	}

	return $return;
}

function setScore($audit_id, $scoring_id){
	require_once("Models/ScoringModel.php");
	$objScore = new ScoringModel();
	$arrScore = $objScore->setScore($audit_id, $scoring_id);

	$result = $arrScore;

	return $result;
}

function getScore($audit_id, $scoring_id = null){
	require_once("Models/ScoringModel.php");
	$objScore = new ScoringModel();
	$arrScore = $objScore->getScore($audit_id);

	$result = $arrScore;

	return $result;
}

function getScorePrevius($lnumber, $type, $dvisit){
	require_once("Models/AuditReportModel.php");
	$objData = new AuditReportModel();
	$audit = $objData->getPreviousAudit($lnumber, $type, $dvisit);
	return empty($audit)? false : getScore($audit['id']);
}

function getScoreDefinition($cal = null){

	$scoreColors = [
		'Platino' 	=> ['#778899', 'Platino'],
		'Verde' 	=> ['#008000', 'Verde'],
		'Amarillo' 	=> ['#F1C40F', 'Amarillo'],
		'Rojo' 		=> ['#FF0000', 'Rojo']
	];

	return is_null($cal)? $scoreColors : $scoreColors[$cal];
}

function getURLReport($audit_id, $report_layout_id, $lan = 'esp'){
	require_once("Models/Report_LayoutModel.php");
	$objData = new Report_LayoutModel();
	$layout = $objData->getReport_Layout(['layout_location'], 'id =' . $report_layout_id)[0]['layout_location'];
	$response = base_url() . '/' .  $layout . '?tk=' . encryptId($audit_id) . '&lan=' . $lan;
	return $response;
}

function encryptId($audit_id){
	$password="0L3RGfKT8rVY^ie7k07wvj05k";
	$encrypted_data = base64_encode(openssl_encrypt($audit_id, "AES-128-ECB", $password));
	return $encrypted_data;
}

function decryptId($token){
	$data_to_decrypt = base64_decode($token);
	$password="0L3RGfKT8rVY^ie7k07wvj05k";
	$decrypted_data = openssl_decrypt($data_to_decrypt, "AES-128-ECB", $password);
	return $decrypted_data;
}

function getAuditLan($audit_id){
	require_once("Models/AuditsModel.php");
	$objData = new AuditsModel();
	$auditLan = $objData->getAuditLanguage($audit_id);
	return $auditLan;
}

function getFirstLastDayWeek($year_number, $week_number){
    // we need to specify 'today' otherwise datetime constructor uses 'now' which includes current time
    $today = new DateTime( 'today' );

    return (object)[
        'first_day' => clone $today->setISODate( $year_number, $week_number, 1 ),
        'last_day'  => clone $today->setISODate( $year_number, $week_number, 7 )
    ];
};

function headerTemplateAnnouncedAudits($week){
	
	require_once("Models/Announced_VisitsModel.php");
	$objData = new Announced_VisitsModel();
	$week++;
	$fil = " and WEEKOFYEAR(t1.announced_date) in (".$week.") ";
	$dataAV['totalAV'] = count($objData->getAnnouncedVisitList($fil));
	$view_header = "Views/Template/header_announced_visits.php";
	require_once($view_header);
}

function isMySelfEvaluation($audit_id){
	$tmp = selectAuditList(['type', 'country_id', 'location_id'], 'id='. $audit_id);
	$audit = $tmp[0];
	
	return ($audit['type'] == 'Self-Evaluation' && $_SESSION['userData']['permission']['Auditorias']['w'] == 1 && (
		in_array($audit['location_id'], explode(',', $_SESSION['userData']['location_id'])) OR 
		(array_key_exists($audit['country_id'], $_SESSION['userData']['country']) AND $_SESSION['userData']['location_id'] == 0)
	));
}

function getAuditType($audit_id){
	$tmp = selectAuditList(['type'], 'id='. $audit_id);
	$audit = $tmp[0];
	
	return $audit;
}

function getLocationEmails($location_id, $user_types){
	require_once("Models/LocationModel.php");
	$objData = new LocationModel();
	$stringEmails = $objData->getLocationEmails($location_id, $user_types);
	return $stringEmails;
}

function getCountryEmails($country_id, $user_types){
	require_once("Models/LocationModel.php");
	$objData = new LocationModel();
	$stringEmails = $objData->getCountryEmails($country_id, $user_types);
	return $stringEmails;
}

function getFranchiseEmails($franchise_id, $user_types){
	require_once("Models/LocationModel.php");
	$objData = new LocationModel();
	$stringEmails = $objData->getFranchiseEmails($franchise_id, $user_types);
	return $stringEmails;
}

function getRegionEmails($country_id, $user_types){
	require_once("Models/LocationModel.php");
	$objData = new LocationModel();
	$stringEmails = $objData->getRegionEmails($country_id, $user_types);
	return $stringEmails;
}

function countTotalOpps($audit_id){
	require_once("Models/AuditReportModel.php");
	$objData = new AuditReportModel();
	$arrOpp = $objData->countTotalOpps($audit_id);
	return $arrOpp;
}

function matchAnswer($key, $auditor_answer){
	$regEx = "~^$key\.-.+|\|$key\.-.+~";
	return preg_match($regEx, $auditor_answer);
}

function limitString($cadena, $limite, $sufijo){
	if(strlen($cadena) > $limite){
		return substr($cadena, 0, $limite) . $sufijo;
	}
	return $cadena;
}
?>