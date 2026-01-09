<?php
require_once 'Models/AuditoriaModel.php';
require_once 'Models/AuditsModel.php';
require_once 'Models/RoundModel.php';
require_once 'Models/BrandModel.php';
require_once 'Models/LocationModel.php';
require_once 'Models/CountryModel.php';
require_once 'Models/ScoringModel.php';
require_once 'Models/ChecklistModel.php';
require_once 'Models/Checklist_ItemModel.php';
require_once 'Models/Report_LayoutModel.php';
require_once 'Models/Additional_QuestionModel.php';
require_once 'Models/Additional_Question_ItemModel.php';
require_once 'Models/Audit_LogModel.php';
require_once 'Models/Announced_VisitsModel.php';
require_once 'Models/UsuariosModel.php';
require_once 'Models/LoginModel.php';

require 'vendor/autoload.php';
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;

class APIBrand extends Controllers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function sendUser() {

        $data = UsuariosModel::selectUsuariosMasive();
        dep ($data);
        //die('Masivo usuarios');
        foreach ($data as $row) {
            $arrPass = LoginModel::setRecoverPass($row['email'], 1);
            //dep ($arrPass);
            if($arrPass != false){
                $arrPaises = explode(',',$row['country_id']);
                $data = ['asunto' => (($row['country_id']=='1' || $row['country_id']==1)?'Nuevo acceso generado':'New access generated'), 'email' => $row['email'], 'bcc' => 'mosorio@bw-globalsolutions.com', 'token' => $arrPass[1], 'country'=>((in_array(1, $arrPaises) || in_array(6, $arrPaises))?1:0)];
                //$data = ['asunto' => 'New access generated to '.$row['email'].' :)', 'email' => 'm.angel.osorio.p@gmail.com', 'bcc' => 'mosorio@bw-globalsolutions.com', 'token' => $arrPass[1]];
                if($row['country_id']=='1' || $row['country_id']==1){
                    $response = sendEmailMasive($data, 'new_user_notice_fn_esp');
                }else{
                    $response = sendEmailMasive($data, 'new_user_notice_fn');
                }

                echo '<br>API Enviado a '.$row['email'].' --- '.$row['pais'];
                sleep(2);
            }
            UsuariosModel::updateStatusUsuario($row['id']);  
            
            //echo '<br>Traido a '.$row['email'].' --- '.$row['pais'];
        }
        //
    }

    public function sendWelcome(){
        ini_set("max_execution_time", "-1"); // Sin límite de tiempo de ejecución
        ini_set("memory_limit", "-1"); // Sin límite de memoria
        set_time_limit(0);
        $data = UsuariosModel::getUsers("status = 1 AND FIND_IN_SET(1, country_id)");//FIND_IN_SET(29, country_id) solo emiratos

        $contador = 0;
        $total_correos = count($data);

        foreach ($data as $row) {
            $arrPass = LoginModel::setRecoverPass($row['email'], 1);
            //dep ($arrPass);
            if($arrPass != false){
                $arrPaises = explode(',',$row['country_id']);
                $data = ['asunto' => (($row['country_id']=='1' || $row['country_id']==1)?'Nuevo acceso generado':'New access generated'), 'email' => $row['email'], 'bcc' => 'mosorio@bw-globalsolutions.com,cordonez@bw-globalsolutions.com', 'token' => $arrPass[1], 'country'=>((in_array(1, $arrPaises) || in_array(6, $arrPaises))?1:0)];
                //$data = ['asunto' => 'New access generated to '.$row['email'].' :)', 'email' => 'm.angel.osorio.p@gmail.com', 'bcc' => 'mosorio@bw-globalsolutions.com', 'token' => $arrPass[1]];
                if($row['country_id']=='1' || $row['country_id']==1){
                    $response = sendEmailMasive($data, 'new_user_notice_fn_esp');
                }else{
                    $response = sendEmailMasive($data, 'new_user_notice_fn');
                }

                echo '<br>API Enviado a '.$row['email'].' --- '.$row['country_id'];
                sleep(2);
                $contador++;

            }
        }
    }

    // public function planNotification() {
    //     $data = AuditoriaModel::getAuditsActionPlanReminderSecond();
    //     foreach ($data as $audit) {
    //         $totalOpps = countTotalOpps($audit['id']);
            
    //         $tmp = getScore($_POST['audit_id']);
    //         $cal = $tmp['Calificacion'];
    //         $auto_fail = $tmp['AutomaticFail'];
            
    //         $asunto = "{$audit['brand_prefix']} #{$audit['location_number']} ({$audit['country_name']}) @ Segundo recordatorio del plan de acción";

    //         $locationMails = getLocationEmails(['Shop / GM', 'District manager / Area manage'], $audit['location_id']);
    //         $recipientsReminderPlan = emailFilter("$locationMails,$countryMails");

    //         $arrMailPlanReminder = ['asunto' 				=> $asunto,
    //                                 'email' 				=> $recipientsReminderPlan,
    //                                 'audit_id'				=> $audit['id'],
    //                                 'score'					=> $cal,
    //                                 'round_name'			=> $audit['round_name'],
    //                                 'auto_fail'				=> $auto_fail,
    //                                 'type'					=> $audit['type'],
    //                                 'date_visit'			=> $audit['date_visit'],
    //                                 'date_release'			=> $audit['date_release'],
    //                                 'date_limit'			=> date("d-m-Y",strtotime($audit['date_release']."+ 7 days")),
    //                                 'total_opps'			=> $totalOpps['opps'],
    //                                 'location_number'		=> $audit['location_number'],
    //                                 'location_name'		    => $audit['location_name'],
    //                                 'location_address'		=> $audit['location_address'] ];
    //         sendEmailMasivePlanSecond($arrMailPlanReminder, 'second_plan_reminder');
    //     }
    // }

    // public function planNotificationFinal() {
    //     $data = AuditoriaModel::getAuditsActionPlanReminderFinal();
    //     foreach ($data as $audit) {
    //         $cal = getScore($audit['id'])['Calificacion'];
    //         $dateLimit = date("d-m-Y",strtotime($audit['date_release']."+ 7 days"));  //Agregar date release
            
    //         $totalOpps = countTotalOpps($audit['id']);

    //         $tmp = getScore($_POST['audit_id']);
    //         $cal = $tmp['Calificacion'];
    //         $auto_fail = $tmp['AutomaticFail'];

    //         $asunto = "{$audit['brand_prefix']} #{$audit['location_number']} ({$audit['country_name']}) @ Recordatorio final del plan de acción";

    //         $locationMails = getLocationEmails(['Shop / GM', 'District manager / Area manager'], $audit['location_id']);
    //         $countryMails = getCountryEmails(['US Director of Operations'], $audit['country_id']);

    //         $recipientsReminderPlan = emailFilter("$locationMails,$countryMails");

    //         $arrMailPlanReminder = ['asunto' 				=> $asunto,
    //                                 'title_layout' 			=> 'Final Action Plan Reminder',
    //                                 'email' 				=> $recipientsReminderPlan,
    //                                 'audit_id'				=> $audit['id'],
    //                                 'score'					=> $cal,
    //                                 'round_name'			=> $audit['round_name'],
    //                                 'auto_fail'				=> $auto_fail,
    //                                 'type'					=> $audit['type'],
    //                                 'date_visit'			=> $audit['date_visit'],
    //                                 'date_release'			=> $audit['date_release'],
    //                                 'date_limit'			=> $dateLimit,
    //                                 'disclaimer' 			=> 'Today is the last day to complete the action plan.',
    //                                 'total_opps'			=> $totalOpps['opps'],
    //                                 'location_number'		=> $audit['location_number'],
    //                                 'location_name'		    => $audit['location_name'],
    //                                 'location_address'		=> $audit['location_address'] ];
    //         sendEmailMasivePlanFinal($arrMailPlanReminder, 'second_plan_reminder');
    //     }
        
    // }

    public function planNotificationExpired() {
        $users = UsuariosModel::getUsersByRole([2]);
        $sends = 0;
        foreach($users as $u){
            switch ($u['level']) {
                case 4: case 5:
                    $u['audits'] = AuditoriaModel::getAuditsActionPlanReminderExpired($u['country_id']);
                    break;
                case 3: case 2:
                    $u['audits'] = AuditoriaModel::getAuditsActionPlanReminderExpired(null, $u['location_id']);
                    break;
            }
            
            if(!empty($u['audits'])){
                $strVisitas = '<table width="500" border="1">
                    <tr>
                        <th>Store</th>
                        <th>AP Status</th>
                        <th>Date Release</th>
                    </tr>';
                foreach($u['audits'] as $a){
                    $strVisitas .= '<tr>
                        <td>#'.$a['location_number'].' - '.$a['location_name'].'</td>
                        <td>'.$a['action_plan_status'].'</td>
                        <td>'.date("d-m-Y",strtotime($a['date_release'])) .'</td>
                    </tr>';
                }
                $strVisitas .= '</table>';
                $asunto = "CJD @ Action plan expired";
                $arrMailPlanReminder = ['asunto' 				=> $asunto,
                                        'title_layout' 			=> 'Action Plan Expired',
                                        'email' 				=> $u['email'],
                                        'tiendas'				=> $strVisitas,
                                        'disclaimer' 			=> 'The time to complete the action plan has expired.' ];
                sendEmailMasiveTable($arrMailPlanReminder);
                $sends++;
            }
        }
        die(json_encode(['sends' => $sends]));
    }

    public function quitarGuiones(){
        $rs = Checklist_ItemModel::quitarGuiones(1);
    }

    public function crearPor(){
        $rs = Checklist_ItemModel::crearPor(1);
    }

    public function planNotificationGM() {

        $data = AuditoriaModel::getAuditsActionPlanReminder();
        foreach ($data as $row) {
            if ($row['diferencia'] >= 3 ) {
                echo 'Enviar recordatorio';
                $data = ['asunto' => 'Recordatorio Plan de Acción', 'email' => 'mosorio@bw-globalsolutions.com'];
                        //sendEmail($data, 'announced_visit');
                //$request_mail = sendPlanReminder($data, 'plan_reminder'); //Helpers
                dep($request_mail);
                if($request_mail > 0) {
                    echo "Notificacion enviada.";	
                }
            }
        }
        dep ($data);
    }

    public function setCompletedAudits(){
        require_once 'Models/UsuariosModel.php';
        $audits = AuditoriaModel::getInProcessAudits();
        $moveCompeted = [];
        $audits100 = [];

        foreach($audits as $audit){
            if($audit['score'] < 100){
                $arrUpdate = [
					"status" 			=> 'Completed',
					"date_release"		=> date('Y-m-d H:i:s')
				];
				$status = AuditoriaModel::updateAudit($arrUpdate, 'id='. $audit['id']);
                array_push($moveCompeted, $audit);


          
                $reAudit = AuditoriaModel::reAudit($audit['id']);
            
////////////////////////               
                foreach ($reAudit as $data) {
                
                    $id_visit      = $data['id_visit'];        
                    $round         = $data['round'];    
                    $period        = $data['period'];    
                    $type          = $data['type'];    
                    $nombre_tienda = $data['nombre_tienda'];            
                    $numero_tienda = $data['numero_tienda'];            
                    $auditor_name  = $data['auditor_name'];            
                    $auditor_email = $data['auditor_email'];            
                    $status        = $data['status'];  
                    $limpieza      = $data['limpieza'];  
                    $seguridad_alimentos = $data['seguridad_alimentos'];
                    
                   
            
            
                    $location_id              = $data['location_id'];     
                    $id_round                 = $data['id_round']; 
                    $brand_id                 = $data['brand_id']; 
                    $additional_question_id   = $data['additional_question_id'];                 
                    $scoring_id               = $data['scoring_id']; 
                    
                    $checklist_id       =  $data['checklist_id'];
                    $report_layout_id   =  $data['report_layout_id'];
                    $local_foranea      =  $data['local_foranea'];
                    
                    
                    $address_1          =  $data['address_1'];
                    $franchissees_name  =  $data['franchissees_name'];
                    $date_visit         =  $data['date_visit'];
                    
                    $email_main_office    = $data['email_main_office'];
                    $email_store_manager  = $data['email_store_manager'];
                    $email_franchisee     = $data['email_franchisee'];
                    $email_area_manager   = $data['email_area_manager'];
                    $email_ops_leader     = $data['email_ops_leader'];
                    $email_ops_director   = $data['email_ops_director'];

                    $email_location     =  "$email_main_office, $email_store_manager, $email_franchisee, $email_area_manager, $email_ops_leader, $email_ops_director,Blanca.Flores@idq.com,JuanCarlos.Roux@idq.com,Armando.Castro@idq.com";

                    
      
                    $data = ['asunto'              => 'Re-audit',
                             'email'               => $email_location,
                             'id_visit'            => $id_visit,        
                             'round'               => $round,    
                             'period'              => $period,    
                             'type'                => $type,    
                             'nombre_tienda'       => $nombre_tienda,            
                             'numero_tienda'       => $numero_tienda,            
                             'auditor_name'        => $auditor_name,            
                             'auditor_email'       => $auditor_email,            
                             'status'               => $status,  
                             'limpieza'             => $limpieza,  
                             'seguridad_alimentos' => $seguridad_alimentos,
                             'location_id'         => $location_id,     
                             'id_round'            => $id_round, 
                             'brand_id'            => $brand_id, 
                             'additional_question_id' => $additional_question_id,                 
                             'scoring_id'          => $scoring_id, 
                             'checklist_id'        => $checklist_id,
                             'report_layout_id'    => $report_layout_id,
                             'local_foranea'       => $local_foranea,
                             'address_1'           => $address_1,
                             'franchissees_name'   => $franchissees_name,
                             'date_visit'          => $date_visit ];
                                      
                             if(!in_array($type,['Calibration Audit'])){
                                sendEmailMasive($data, 'reaudit');
                             }
                        }
////////////////////////////////////




                if($status){
                    $tmp = getScore($audit['id']);
                    //$cal = $tmp['Calificacion'];
                    $cal = $tmp['value_4'];


                    $criticos		 = $tmp['Criticos'];
			        $rojos			 = $tmp['Rojos']; 
			        $zero_tolerancia = $tmp['zero_tolerancia'];
                    
			  
                    if($criticos > 1 || $rojos > 10 || $zero_tolerancia > 0){
                        $visit_result = "Reprobado";
                    }else{
                        $visit_result = "Aprobado";
                    }



                    /*	
				Variable temporalidad condiciones
				2160 horas(90 dias) Mantenimiento
				72   horas No Críticas y de Limpieza la temporalidad
				24   horas Penalizaciones Críticas 
				$horas.' '.$data[$i]['diferencia_en_horas'].
			*/
            $mantenimiento = $tmp['Mantenimiento'];
            $nocritico = $tmp['NoCriticos']+$tmp['Amarillos'];
            $critico = $tmp['Criticos'];
            
			if($mantenimiento > 0){
				$horas = 2160;
				$mensajelimite = "90 dias para completar el plan de acción.";
			}
			if($nocritico > 0){
				$mensajelimite = "72 horas para completar el plan de acción.";
			}
			if($critico){
				$horas = 24;
				$mensajelimite = "24 para completar el plan de acción";
			}

    
                    $exceptionsMails = listMailExceptions('final_report');
                    $locationMails   = getLocationEmails(['Shop / GM'], $audit['location_id']);
                    $countryMails    = getCountryEmails(['District Manager'], $audit['country_id']);
                    $AdminMails      = getLocationEmails(['admin arguilea'], 0);
                    $location        = LocationModel::getLocation([], "id = ".$audit['location_id']);


                    
                    $mail = $audit['email_store_manager'].",".$audit['email_franchisee'].",". $audit['email_area_manager'].",Blanca.Flores@idq.com,JuanCarlos.Roux@idq.com,Armando.Castro@idq.com";
                    
							
                    //$recipients = emailFilter("$exceptionsMails,$locationMails,$countryMails,$AdminMails");
                    $recipients = emailFilter("$AdminMails,$mail");
                    $esPrueba=false;
                    if(in_array($type,['Calibration Audit']))$esPrueba=true;
                    $to = UsuariosModel::getTo(2, $audit['location_id'], $esPrueba, $location['country_id']);
                    $plantilla = (in_array($location['country_id'], [1,10,18,33,35,36])?'aviso_liberacion_esp':'aviso_liberacion');
                    $titulo = (in_array($location['country_id'], [1,10,18,33,35,36])?'Reporte final':'Final Report');

                    sendEmailMasive([
                        'asunto' => "{$audit['brand_prefix']} #{$audit['location_number']} ({$audit['country_name']}) @ $titulo",
                        'email' 				=> $to,
                        'audit_id'				=> $audit['id'],
                        'score'					=> $tmp['value_4'],
                        'result'			    => $tmp['value_3'],
                        'type'					=> $audit['type'],
                        'location_number'		=> $audit['location_number'],
                        'location_address'		=> $audit['location_address'],
                        'country'               => $location['country_id'],
                        'url_report'			=> getURLReport($audit['id'], $audit['report_layout_id'], (in_array($location['country_id'], [1,10,18,33,35,36])?'esp':'eng'))
                    ],  $plantilla);

                    /*if($tmp['value_3']=='F'){
                        $to = UsuariosModel::getTo(3, $audit['location_id'], $esPrueba, $audit['country_id']);
                        $plantilla = (in_array($location['country_id'], [1,10,18,33,35,36])?'failed_notification_esp':'failed_notification');

                        sendEmailMasive([
                            'asunto' => "{$audit['brand_prefix']} #{$audit['location_number']} ({$audit['country_name']}) @ $titulo",
                            'email' 				=> $to,
                            'audit_id'				=> $audit['id'],
                            'score'					=> $tmp['value_4'],
                            'result'			    => $tmp['value_3'],
                            'type'					=> $audit['type'],
                            'location_number'		=> $audit['location_number'],
                            'location_address'		=> $audit['location_address'],
                            'country'               => $location['country_id'],
                            'url_report'			=> getURLReport($audit['id'], $audit['report_layout_id'], (in_array($location['country_id'], [1,10,18,33,35,36])?'esp':'eng'))
                        ],  $plantilla);

                        if($tmp['value_4']=='0' || $tmp['value_4']==0){
                            $to = UsuariosModel::getTo(11, $audit['location_id'], $esPrueba, $audit['country_id']);
                            $plantilla = (in_array($location['country_id'], [1,10,18,33,35,36])?'zero_tolerance_esp':'zero_tolerance');

                            sendEmailMasive([
                                'asunto' => "{$audit['brand_prefix']} #{$audit['location_number']} ({$audit['country_name']}) @ $titulo",
                                'email' 				=> $to,
                                'audit_id'				=> $audit['id'],
                                'score'					=> $tmp['value_4'],
                                'result'			    => $tmp['value_3'],
                                'type'					=> $audit['type'],
                                'location_number'		=> $audit['location_number'],
                                'location_address'		=> $audit['location_address'],
                                'country'               => $location['country_id'],
                                'url_report'			=> getURLReport($audit['id'], $audit['report_layout_id'], (in_array($location['country_id'], [1,10,18,33,35,36])?'esp':'eng'))
                            ],  $plantilla);
                        }
                    }*/

                    $totalOpps = countTotalOpps($audit['id']);
                    $titulo = (in_array($location['country_id'], [1,10,18,33,35,36])?'Recordatorio del plan de acción':'Action Plan Reminder');
                    $asunto = "{$audit['brand_prefix']} #{$audit['location_number']} ({$audit['country_name']}) @ $titulo";
                    $to = UsuariosModel::getTo(5, $audit['location_id'], $esPrueba, $audit['country_id']);

                    
                    sendEmailMasive([
                        'asunto' 				=> $asunto,
                        'email' 				=> $to,
                        'audit_id'				=> $audit['id'],
                        'score'					=> $cal,
                        'round_name'			=> $audit['round_name'],
                        'type'					=> $audit['type'],
                        'date_visit'			=> $audit['date_visit'],
                        'date_release'			=> date('Y-m-d H:i:s'),
                        'date_limit'			=> date('d-m-Y', strtotime('+7 days')),
                        'total_opps'			=> $totalOpps['opps'],
                        'location_number'		=> $audit['location_number'],
                        'location_name'			=> $audit['location_name'],
                        'location_address'		=> $audit['location_address'],
                        'country'               => $location['country_id'],
                        'limit_days'		    => $mensajelimite
			
                    ], (in_array($location['country_id'], [1,10,18,33,35,36])?'aplan_reminder':'aplan_reminder_eng'));
                }

            } else{
                array_push($audits100, $audit);
            }
        }

        if(!empty($audits100)){
            sendEmailMasive([
                'asunto' => "CJD @ Audits to validate",
                'email'     => "",
                'audits'    => $audits100
            ], 'audits_to_validate');
        }

        // die(json_encode($audits));
        die(json_encode($moveCompeted));
    }

    public function test()
	{
        require_once $_SERVER['DOCUMENT_ROOT'] .'/Assets/libraries/PHPExcel/Classes/PHPExcel/IOFactory.php';
		require_once $_SERVER['DOCUMENT_ROOT'] .'/Assets/libraries/PHPExcel/Classes/PHPExcel.php';

        ini_set("max_execution_time", "-1");
        ini_set("memory_limit", "-1");
        ignore_user_abort(true);
        set_time_limit(0);
		
        echo "SFTP\n";
        echo "<br>";
        include('Libraries/phpseclib/Net/SFTP.php');

        /*$sftp = new Net_SFTP('drop.smallssliders.com');
        if (!$sftp->login('Arguilea', 'L3monP3pper')) {
            exit('Login Failed');
        }*/
        //echo('<pre>');
        // Listamos los archivos en el directorio remoto
        //print_r($sftp->nlist());
        //echo('</pre>');
        $file = fopen($_SERVER['DOCUMENT_ROOT'] .'/Assets/tmp/Arguilea_Ops_List.csv',"r");
        $linea = 1;
        while(! feof($file)) {
            
            if ($linea > 2 ) {
                $line = str_replace('"', "", fgets($file));
                $line = explode("|", $line);
                if (is_numeric($line[0])) {
                    echo $line[0]. " Columna valida";
                } else {
                    echo $line[0]. " Columna invalida";
                }
                //echo $line[0];
                //echo " --- ";
                //echo $line[1];
                echo "<br>";
                //echo $line[2];
                //echo "<br>";
                //echo $line[3];
                //echo "<br>";
            }
            
            $linea ++ ;
            
        }
        fclose($file);
        //fclose($file);
        
        /*$rows = explode("|", $sftp->get('Arguilea_Ops_List.csv'));

        $rows = explode("|", $sftp->get('Arguilea_Ops_List.csv'));
        $i = 0;
        foreach($rows as $row){
            $data = str_getcsv($row);
            dep ($data);
            $i++;
            if ($i==10) break;
        }*/
        //dep($rows);

        /*$inputFileType = 'CSV';
        $inputFileName = 'Arguilea_Ops_List.csv';
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($sftp->get('Arguilea_Ops_List.csv'));
        $objWorksheet=$objPHPExcel->getActiveSheet();
		$objPHPExcel->setActiveSheetIndex(0);

        $handle = fopen($filename, "r");

        $highestRow=$objWorksheet->getHighestRow();
        for($row=2;$row<=10;++$row){
            echo $objWorksheet->getCellByColumnAndRow(1,$row)->getFormattedValue();
        }*/

        //$fileUnits = file_get_contents($sftp->get('Arguilea_Ops_List.csv'));
        //dep ($objPHPExcel);
        
        /**
         * Cerramos la conexión, como debe hacer un buen
         * programador
         */
        //$sftp->disconnect();
	}

    public function test2()
	{
		echo "Funcion prueba";
        set_include_path(implode(PATH_SEPARATOR, array(
            realpath(dirname(__FILE__) . '/phpseclib'),
            get_include_path(),
    )));
    
    // Incluimos la librería
    require_once 'Net/SFTP.php';
    
     // Este bloque de 4 líneas no requiere explicación
    $strServer = '127.0.0.1';
    $intPort = 22;
    $strUsername = 'root';
    $strPassword = 'toor';
    
    /**
     * Estos serían los archivos local y remoto con los que
     * vamos a trabajar
     */
    $strLocalFile = 'localfile.txt';
    $strRemoteFile = 'remotefile.txt';
    
    // Instanciamos la clase
    $objFtp = new Net_SFTP( $strServer , $intPort );
    
    // Realizamos el logeo
    if (!$objFtp ->login( $strUsername , $strPassword )) {
             exit( 'Login Failed' );
    }
    
    // Obtenemos el directorio remoto actual
    echo $objFtp->pwd() . "\r\n";
    
    /**
     * Leemos los datos del archivo local que que queremos
     * enviar al servidor
     */
    $strData = file_get_contents( $strLocalFile );
    
    /**
     * Creamos un archivo en el servidor y escribimos lo datos
     * del archivo local que queremos enviar al servidor
     */
    $objFtp->put( $strLocalFile, $strData );
    
    /**
     * Descargamos un archivo remoto y lo guardamos nuestro
     * servidor local
     */
    $objFtp->get( $strRemoteFile, $strRemoteFile );
    
    echo('<pre>');
    // Listamos los archivos en el directorio remoto
    print_r($objFtp->nlist());
    echo('</pre>');
    
    /**
     * Cerramos la conexión, como debe hacer un buen
     * programador
     */
    $objFtp->disconnect();
	}



    public function test3()
	{
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set("max_execution_time", "-1");
        ini_set("memory_limit", "-1");
        ignore_user_abort(true);
        set_time_limit(0);

        include('Libraries/phpseclib/Net/SFTP.php');
        $sftp = new Net_SFTP('drop.smallssliders.com');
        if ($sftp->login('Arguilea', 'L3monP3pper')) {
            $file =  $sftp->get('Arguilea_Ops_List.csv');
            $arrDataLog = [
                "data" 		=> "Success connection",
                "date_data"	=> date('Y-m-d')
            ];
            $requestDataLog = Announced_VisitsModel::insertDataLog($arrDataLog);
            //$data = Announced_VisitsModel::getData(111);
            //$lines = explode("\n", $data['data']);
            $units = explode("\n", $file);
            //dep($units);
            //ie();

            $adds = 0;
            $upds = 0;
            $errors = "";
            foreach ($units as $key) {
                dep ($key);
                $line = explode('","', $key);
                $line = str_replace('"', "", $line);
                
                if (is_numeric($line[0])) {
                    //dep ($line);
                    $country = CountryModel::getCountry(['id'], "name='$line[6]'")[0];
                    if ( $country['id'] ) {
                        $arrUnitValues = [
                            'brand_id' => 1,
                            'country_id' => $country['id'],
                            'status' => 'Active',
                            'number' => $line[0],
                            'name' => $line[1]!=''?$line[1]:NULL,
                            'address_1' => $line[2]!=''?$line[2]:NULL,
                            'city' => $line[3]!=''?$line[3]:NULL,
                            'state_code' => $line[4]!=''?$line[4]:NULL,
                            'zip' => $line[5]!=''?$line[5]:NULL,
                            'country' => $line[6]!=''?$line[6]:NULL,
                            'longitude' => $line[7]!=''?$line[7]:NULL,
                            'latitude' => $line[8]!=''?$line[8]:NULL,
                            'phone' => $line[9]!=''?$line[9]:NULL,
                            'email' => $line[10]!=''?$line[10]:NULL,
                            'sun_open' => $line[11]!=''?$line[11]:NULL,
                            'sun_close' => $line[12]!=''?$line[12]:NULL,
                            'mon_open' => $line[13]!=''?$line[13]:NULL,
                            'mon_close' => $line[14]!=''?$line[14]:NULL,
                            'tue_open' => $line[15]!=''?$line[15]:NULL,
                            'tue_close' => $line[16]!=''?$line[16]:NULL,
                            'wed_open' => $line[17]!=''?$line[17]:NULL,
                            'wed_close' => $line[18]!=''?$line[18]:NULL,
                            'thu_open' => $line[19]!=''?$line[19]:NULL,
                            'thu_close' => $line[20]!=''?$line[20]:NULL,
                            'fri_open' => $line[21]!=''?$line[21]:NULL,
                            'fri_close' => $line[22]!=''?$line[22]:NULL,
                            'sat_open' => $line[23]!=''?$line[23]:NULL,
                            'sat_close' => $line[24]!=''?$line[24]:NULL,
                            'qsc_exceptions' => $line[25]!=''?$line[25]:NULL,
                            'master' => $line[26]!=''?$line[26]:NULL,
                            'entity_name' => $line[27]!=''?$line[27]:NULL
                        ];
                        $isLocation = LocationModel::getLocation(['id', 'number'], "number='$line[0]'")[0];
                        if($isLocation['id']) { // Actualiza existente
                            $upds++; 
                            $request_location = LocationModel::updateLocation($arrUnitValues, "id=$isLocation[id]");
                        } else { // Agrega nuevo
                            $adds++;
                            $request_location = LocationModel::insertLocation($arrUnitValues);
                        }
                        //dep ($arrUnitValues);
                    } else {
                        $errors .= "FAIL Get Country Info DB: $line[6] | ";
                    }
                    $arrDataLog = [
                        "add_record" 		=> $adds,
                        "update_record"	    => $upds,
                        "errors"	        => $errors
                    ];
                    
                }
            }
            $requestDataLog = Announced_VisitsModel::updateDataLog($arrDataLog, "id_data_log=$requestDataLog");
        } else {
            $arrDataLog = [
                "data" 		=> "Failed connection",
                "date_data"	=> date('Y-m-d')
            ];
            $requestDataLog = Announced_VisitsModel::insertDataLog($arrDataLog);
        }
        $sftp->disconnect();
        die();
	}

    
    public function getConn(){

        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set("max_execution_time", "-1");
        ini_set("memory_limit", "-1");
        ignore_user_abort(true);
        set_time_limit(0);

        include('Libraries/phpseclib/Net/SFTP.php');

        $sftp = new Net_SFTP('drop.smallssliders.com');
        if (!$sftp->login('Arguilea', 'L3monP3pper')) {
            exit('Login Failed');
        }
        $remote_file = 'Audit/arguilea_audits_'. date('y_m_d') . '.csv';
        $local_file = 'Assets/audit_report_csv/audit_report_' . date("w") . '.csv';
        $sftp->put($remote_file, $local_file, NET_SFTP_LOCAL_FILE); 

        $sftp->disconnect();
        die('success');
        
    }

 

    public function testDate() {
        die(date('H:i'));
    }

    public function processData() {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set("max_execution_time", "-1");
        ini_set("memory_limit", "-1");
        ignore_user_abort(true);
        set_time_limit(0);

        include('Libraries/phpseclib/Net/SFTP.php');
        $sftp = new Net_SFTP('drop.smallssliders.com');
        if ($sftp->login('Arguilea', 'L3monP3pper')) {
            $file =  $sftp->get('Arguilea_Ops_List.csv');
            $arrDataLog = [
                "data" 		=> "Success connection",
                "date_data"	=> date('Y-m-d')
            ];
            $requestDataLog = Announced_VisitsModel::insertDataLog($arrDataLog);
            $units = explode("\n", $file);
            $adds = 0;
            $upds = 0;
            $errors = "";
            foreach ($units as $key) {
                dep ($key);
                $line = explode('","', $key);
                $line = str_replace('"', "", $line);
                
                if (is_numeric($line[0])) {
                    //dep ($line);
                    $country = CountryModel::getCountry(['id'], "name='$line[6]'")[0];
                    if ( $country['id'] ) {
                        $arrUnitValues = [
                            'brand_id' => 1,
                            'country_id' => $country['id'],
                            'status' => 'Active',
                            'number' => $line[0],
                            'name' => $line[1]!=''?$line[1]:NULL,
                            'address_1' => $line[2]!=''?$line[2]:NULL,
                            'city' => $line[3]!=''?$line[3]:NULL,
                            'state_code' => $line[4]!=''?$line[4]:NULL,
                            'zip' => $line[5]!=''?$line[5]:NULL,
                            'country' => $line[6]!=''?$line[6]:NULL,
                            'longitude' => $line[7]!=''?$line[7]:NULL,
                            'latitude' => $line[8]!=''?$line[8]:NULL,
                            'phone' => $line[9]!=''?$line[9]:NULL,
                            'email' => $line[10]!=''?$line[10]:NULL,
                            'sun_open' => $line[11]!=''?$line[11]:NULL,
                            'sun_close' => $line[12]!=''?$line[12]:NULL,
                            'mon_open' => $line[13]!=''?$line[13]:NULL,
                            'mon_close' => $line[14]!=''?$line[14]:NULL,
                            'tue_open' => $line[15]!=''?$line[15]:NULL,
                            'tue_close' => $line[16]!=''?$line[16]:NULL,
                            'wed_open' => $line[17]!=''?$line[17]:NULL,
                            'wed_close' => $line[18]!=''?$line[18]:NULL,
                            'thu_open' => $line[19]!=''?$line[19]:NULL,
                            'thu_close' => $line[20]!=''?$line[20]:NULL,
                            'fri_open' => $line[21]!=''?$line[21]:NULL,
                            'fri_close' => $line[22]!=''?$line[22]:NULL,
                            'sat_open' => $line[23]!=''?$line[23]:NULL,
                            'sat_close' => $line[24]!=''?$line[24]:NULL,
                            'qsc_exceptions' => $line[25]!=''?$line[25]:NULL,
                            'master' => $line[26]!=''?$line[26]:NULL,
                            'entity_name' => $line[27]!=''?$line[27]:NULL
                        ];
                        $isLocation = LocationModel::getLocation(['id', 'number'], "number='$line[0]'")[0];
                        if($isLocation['id']) { // Actualiza existente
                            $upds++; 
                            $request_location = LocationModel::updateLocation($arrUnitValues, "id=$isLocation[id]");
                        } else { // Agrega nuevo
                            $adds++;
                            $request_location = LocationModel::insertLocation($arrUnitValues);
                        }
                    } else {
                        $errors .= "FAIL Get Country Info DB: $line[6] | ";
                    }
                    $arrDataLog = [
                        "add_record" 		=> $adds,
                        "update_record"	    => $upds,
                        "errors"	        => $errors
                    ];
                    
                }
            }

            $requestDataLog = Announced_VisitsModel::updateDataLog($arrDataLog, "id_data_log=$requestDataLog");
        } else {
            $arrDataLog = [
                "data" 		=> "Failed connection",
                "date_data"	=> date('Y-m-d')
            ];
            $requestDataLog = Announced_VisitsModel::insertDataLog($arrDataLog);
        }
        $sftp->disconnect();
        die();
    }

    public function processDataLast()
	{
        $idData = $this->test3();
        //$idData = 87;
        $data = Announced_VisitsModel::getData($idData);
        $lines = preg_replace("[\n|\r|\n\r]", "", $data['data']);


         //dep ($data['data']);
       

        //$lines = explode('"""', $lines);
        $lines = explode("\n", $data['data']);
        //dep ($lines);
        //die();
        $adds = 0;
        $upds = 0;
        $errors = 0;
        foreach ($lines as $key) {
            $line = str_replace('"', "", $key);
            $line = explode("|", $line);
            //dep ($line);
            if (is_numeric($line[0])) {

                //Buscar si la tienda ya existe

                $brand_id = 1;
                $country_id = $line[6]=='Mexico'?1: ($line[6]=='Indonesia'?2: 3);
                $status = 'Active';
                $number = $line[0]?$line[0]:NULL;
                $name = $line[1]?$line[1]:NULL;
                $address_1 = $line[2]?$line[2]:NULL;
                $city = $line[3]?$line[3]:NULL;
                $state_code = $line[4]?$line[4]:NULL;
                $zip = $line[5]?$line[5]:NULL;
                $country = $line[6]?$line[6]:NULL;
                $longitude = $line[7]?$line[7]:NULL;
                $latitude = $line[8]?$line[8]:NULL;
                $phone = $line[9]?$line[9]:NULL;
                $email = $line[10]?$line[10]:NULL;
                $sun_open = $line[11]?$line[11]:NULL;
                $sun_close = $line[12]?$line[12]:NULL;
                $mon_open = $line[13]?$line[13]:NULL;
                $mon_close = $line[14]?$line[14]:NULL;
                $tue_open = $line[15]?$line[15]:NULL;
                $tue_close = $line[16]?$line[16]:NULL;
                $wed_open = $line[17]?$line[17]:NULL;
                $wed_close = $line[18]?$line[18]:NULL;
                $thu_open = $line[19]?$line[19]:NULL;
                $thu_close = $line[20]?$line[20]:NULL;
                $fri_open = $line[21]?$line[21]:NULL;
                $fri_close = $line[22]?$line[22]:NULL;
                $sat_open = $line[23]?$line[23]:NULL;
                $sat_close = $line[24]?$line[24]:NULL;
                $qsc_exceptions = $line[25]?$line[25]:NULL;
                $master = $line[26]?$line[26]:NULL;
                $entity_name = $line[27]?$line[27]:NULL;

                $isLocation = LocationModel::getLocation(['id', 'number'], "number='$number'")[0];
                if($isLocation['id']) { 
                    //echo $isLocation['id'].' se encontro '.$isLocation['number']; 
                    $upds++; 
                    //Se encontro, se actualiza location
                    $updateUnitValues = [
                        'brand_id' => $brand_id,
                        //'country_id' => $country_id,
                        'status' => $status,
                        'number' => $number,
                        'name' => $name,
                        'address_1' => $address_1,
                        'city' => $city,
                        'state_code' => $state_code,
                        'zip' => $zip,
                        'country' => $country,
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'phone' => $phone,
                        'email' => $email,
                        'sun_open' => $sun_open,
                        'sun_close' => $sun_close,
                        'mon_open' => $mon_open,
                        'mon_close' => $mon_close,
                        'tue_open' => $tue_open,
                        'tue_close' => $tue_close,
                        'wed_open' => $wed_open,
                        'wed_close' => $wed_close,
                        'thu_open' => $thu_open,
                        'thu_close' => $thu_close,
                        'fri_open' => $fri_open,
                        'fri_close' => $fri_close,
                        'sat_open' => $sat_open,
                        'sat_close' => $sat_close,
                        'qsc_exceptions' => $qsc_exceptions,
                        'master' => $master,
                        'entity_name' => $entity_name
                    ];
                    $request_location = LocationModel::updateLocation($updateUnitValues, "id=$isLocation[id]");

                } else { 
                    //echo $isLocation['id'].' no se encontro '.$number; 
                    $adds++;
                    
                    //No se encontro, se guarda nueva location
                    $insertUnitValues = [
                        'brand_id' => $brand_id,
                        //'country_id' => $country_id,
                        'status' => $status,
                        'number' => $number,
                        'name' => $name,
                        'address_1' => $address_1,
                        'city' => $city,
                        'state_code' => $state_code,
                        'zip' => $zip,
                        'country' => $country,
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'phone' => $phone,
                        'email' => $email,
                        'sun_open' => $sun_open,
                        'sun_close' => $sun_close,
                        'mon_open' => $mon_open,
                        'mon_close' => $mon_close,
                        'tue_open' => $tue_open,
                        'tue_close' => $tue_close,
                        'wed_open' => $wed_open,
                        'wed_close' => $wed_close,
                        'thu_open' => $thu_open,
                        'thu_close' => $thu_close,
                        'fri_open' => $fri_open,
                        'fri_close' => $fri_close,
                        'sat_open' => $sat_open,
                        'sat_close' => $sat_close,
                        'qsc_exceptions' => $qsc_exceptions,
                        'master' => $master,
                        'entity_name' => $entity_name
                    ];
                    $request_location = LocationModel::insertLocation($insertUnitValues);
                }

                //
                //if($request_unit > 0) { $adds++; }
                //else { $errors++; }
                //$countryId = $line[0]?$line[0]:NULL;
                //echo $line[0]. " Columna valida ".$line[6].' - '.$countryId;
            } else {
                //echo $line[0]. " Columna invalida concatena al anterior";
            }
            //echo $line[0];
            //echo " --- ";
            //echo $line[1];
            //echo "<br>";
            //echo $line[2];
            //echo "<br>";
            //echo $line[3];
            //echo "<br>";
        }
        echo "Se agregaron  ".$adds.' - Actualizaron '.$upds;
        
        
        /* $linea = 1;
        while(! feof($file)) {
            
            if ($linea > 2 ) {
                $line = str_replace('"', "", fgets($file));
                $line = explode("|", $line);
                if (is_numeric($line[0])) {
                    echo $line[0]. " Columna valida";
                } else {
                    echo $line[0]. " Columna invalida";
                }
                //echo $line[0];
                //echo " --- ";
                //echo $line[1];
                echo "<br>";
                //echo $line[2];
                //echo "<br>";
                //echo $line[3];
                //echo "<br>";
            }
            
            $linea ++ ;
            
        } */
        //$sftp->get('Arguilea_Ops_List.csv', $path);

        //$sftp->disconnect();
	}

    public function testConn() {

        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set("max_execution_time", "-1");
        ini_set("memory_limit", "-1");
        ignore_user_abort(true);
        set_time_limit(0);

        require_once $_SERVER['DOCUMENT_ROOT'] .'/Assets/libraries/PHPExcel/Classes/PHPExcel/IOFactory.php';
		require_once $_SERVER['DOCUMENT_ROOT'] .'/Assets/libraries/PHPExcel/Classes/PHPExcel.php';
        include('Libraries/phpseclib/Net/SFTP.php');

        //echo getcwd(); echo "<br>";
        //echo $_SERVER['DOCUMENT_ROOT']; echo "<br>";

        $sftp = new Net_SFTP('drop.smallssliders.com');
        if (!$sftp->login('Arguilea', 'L3monP3pper')) {
            exit('Login Failed');
        }
        //echo('<pre>');
        // Listamos los archivos en el directorio remoto
        print_r($sftp->nlist());
        echo('</pre>');

        //$path = $_SERVER['DOCUMENT_ROOT'] .'/Assets/tmp/dataUnits.csv';
        //echo $path; echo "<br>";
        $file =  $sftp->get('Arguilea_Ops_List.csv');
        $sftp->disconnect();

        $lines = explode('" "', $file);
        echo $lines;

    }

    public function testScore()
	{
        require_once 'Models/ScoringModel.php';
        //die("Test Score");
        $scores = ScoringModel::setScore(4, 1);
        dep($scores);
        die();
	}

   


    public function testSftpFeed() { 

        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        ini_set("max_execution_time", "-1");
        ini_set("memory_limit", "-1");
        ignore_user_abort(true);
        set_time_limit(0);

        require_once $_SERVER['DOCUMENT_ROOT'] .'/Assets/libraries/PHPExcel/Classes/PHPExcel/IOFactory.php';
		require_once $_SERVER['DOCUMENT_ROOT'] .'/Assets/libraries/PHPExcel/Classes/PHPExcel.php';   

        //echo getcwd(); echo "<br>";
        //echo $_SERVER['DOCUMENT_ROOT']; echo "<br>";

        $sftp = new SFTP('37.59.23.3');
        if (!$sftp->login('dq_datafeed', 'Jaa$dQpxL&75$Sa6fuEG')) {
            exit('Login Failed');
        }
        //echo('<pre>');
        // Listamos los archivos en el directorio remoto
        print_r($sftp->nlist());
        echo('</pre>');

        //$path = $_SERVER['DOCUMENT_ROOT'] .'/Assets/tmp/dataUnits.csv';
        //echo $path; echo "<br>";
        $file =  $sftp->get('datafeed/store-export-2024-09-25.csv');
        $sftp->disconnect();

        $lines = explode('" "', $file);
        echo $lines;

    }


   
    

  

    public function testLayout() {
     
$sftp = new SFTP('37.59.23.3');
//$sftp->login('cmr_datafeed', 'AFbxc3JB5*T&9&5WFa5^X');

if (!$sftp->login('dq_datafeed', 'Jaa$dQpxL&75$Sa6fuEG')) {
    exit( 'Login Failed' );
}else{
    echo 'ok';
}
    }
    











public function setExcelUsuarios()
{
    // Incluye los archivos de PHPExcel manualmente
require_once $_SERVER['DOCUMENT_ROOT'] . '/Assets/js/plugins/PHPExcel/Classes/PHPExcel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Assets/js/plugins/PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once 'Models/UsuariosModel.php';
// Ruta al archivo Excel en tu servidor
//$archivoExcel = $_SERVER['DOCUMENT_ROOT'] . '/Controllers/feed/feed_layout.xlsx';
$archivoExcel = $archivo;
//$archivoExcel = $_SERVER['DOCUMENT_ROOT'] . '/Controllers/feed/store-export-2024-09-25.csv';

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


            $insertDataLog = UsuariosModel::insertDataLog($valoresEsperados,1,$dataErrorLog,'Encabezados');
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

   
        /*
        echo "<br><br><br> --------------------------<br><br><br><br>";
        echo " num fila: $fila 				 <br>";				
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
        */

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
                
                $setExcelLocation = UsuariosModel::setExcelUsuarios($data);
                
                foreach ($setExcelLocation as $si) {


                    $locationId = $si['locationId'];
echo "------------------------------------------- $fila ----id tienda: " . htmlspecialchars($si['locationId']) . "-------------------------------------------<br>";

                    
                    //echo "<br>Parameter $brand_id --$country_id --10 --$name --$store_email --$locationId ";
                    if (filter_var($store_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
                    $modelstoreManager = UsuariosModel::setExcelUser($brand_id,$country_id,10,$name, $store_email,$locationId);
                    foreach ($modelstoreManager as $storeManager) {
                        echo "storeManager: $store_email " . htmlspecialchars($storeManager['validacionEmail']) . "<br>";
                        if($storeManager['validacionEmail']==1){array_push($dataEmail,   $store_email);}
                    }} else {echo "error de correo $store_email <br>";array_push($dataErrorLog, array('correo_incorrecto_storeManager'    => $store_email));}

                    //echo "<br>Parameter $brand_id --$country_id --14 --$franchisees_name --$franchissees_email --$locationId ";
                    if (filter_var($franchissees_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
                    $modelfranchisees  = UsuariosModel::setExcelUser($brand_id,$country_id,14,$franchisees_name, $franchissees_email,$locationId);
                    foreach ($modelfranchisees as $franchisees) {
                        echo "franchisees: $franchissees_email " . htmlspecialchars($franchisees['validacionEmail']) . "<br>";
                        if($franchisees['validacionEmail']==1){array_push($dataEmail,   $franchissees_email);}

                    }} else {echo "error de correo $franchissees_email <br>";array_push($dataErrorLog, array('correo_incorrecto_franchisees'    => $franchissees_email));}

                    //echo "<br>Parameter $brand_id --$country_id --20 --$area_manager_name --$area_manager_email --$locationId";
                    if (filter_var($area_manager_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
                    $modelareaManager  = UsuariosModel::setExcelUser($brand_id,$country_id,20,$area_manager_name, $area_manager_email,$locationId);
                    foreach ($modelareaManager as $areaManager) {
                        echo "areaManager: $area_manager_email " . htmlspecialchars($areaManager['validacionEmail']) . "<br>";
                        if($areaManager['validacionEmail']==1){array_push($dataEmail,   $area_manager_email);}

                    }}else {echo "error de correo $area_manager_email <br>";array_push($dataErrorLog, array('correo_incorrecto_areaManager'    => $area_manager_email));}

                    //echo "<Parameter $brand_id --$country_id --19 --$ops_leader_name --$ops_leader_email --$locationId <br>";
                    if (filter_var($ops_leader_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
                    $modelopsleader 	  = UsuariosModel::setExcelUser($brand_id,$country_id,19,$ops_leader_name, $ops_leader_email,$locationId);
                    foreach ($modelopsleader as $opsleader) {
                        echo "opsleader: $ops_leader_email" . htmlspecialchars($opsleader['validacionEmail']) . "<br>";
                        if($opsleader['validacionEmail']==1){array_push($dataEmail,   $ops_leader_email);}
                    }}else {echo "error de correo $ops_leader_email <br>"; array_push($dataErrorLog, array('correo_incorrecto_opsleader'    => $ops_leader_email));}

                    //echo "<Parameter $brand_id --$country_id --18 --$ops_director_name --$ops_director_email --$locationId <br>";
                    if (filter_var($ops_director_email, FILTER_VALIDATE_EMAIL)) { // FILTER_VALIDATE_EMAIL validamos el correo corecto
                    $modelopsdirector 	  = UsuariosModel::setExcelUser($brand_id,$country_id,18,$ops_director_name, $ops_director_email,$locationId);
                    foreach ($modelopsdirector as $opsDirector) {
                        echo "opsDirector: $ops_director_email" . htmlspecialchars($opsDirector['validacionEmail']) . "<br>";
                        if($opsDirector['validacionEmail']==1){array_push($dataEmail,   $ops_director_email);}
                    }}else {echo "error de correo $ops_director_email <br>"; array_push($dataErrorLog, array('correo_incorrecto_opsleader'    => $ops_director_email));}
    

                }
        
                $insertDataLog = UsuariosModel::insertDataLog($data,1,$dataErrorLog,'Actualizacion');
        }


// Elimina los correos repetidos
$dataEmail = array_unique($dataEmail);

$i = 1;

foreach ($dataEmail as $email) {
    // Validamos que no venga vacío ni sea '0'
    if (!empty($email) && $email != '0') {
    
        echo "<br>--------------------------------------- SE ENVIA CORREO A: ".$i . "-" . $email . "<br>";
       
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




public function readCSV() {
      
    date_default_timezone_set('America/Mexico_City');
    $fechaActual = date('Y-m-d');
    //$fechaActual = '2025-02-20';

    $sftp = new SFTP('37.59.23.3');
    $usuario = 'dq_datafeed'; 
    $contrasena = 'Jaa$dQpxL&75$Sa6fuEG'; 

    if (!$sftp->login($usuario, $contrasena)) {
        exit('No se pudo conectar al servidor SFTP.');
    }
    
    $rutaArchivoXLSX = 'datafeed/store-export-'.$fechaActual.'.csv';
    $contenidoXLSX = $sftp->get($rutaArchivoXLSX);
    
    if ($contenidoXLSX === false) {
        exit('No se pudo descargar el archivo XLSX.');
    } 
    
   $archivo = tempnam(sys_get_temp_dir(), 'xlsx');
   file_put_contents($archivo, $contenidoXLSX);
     

$dataEmail = array();

$dataErrorLog = array();
 
    if (file_exists($archivo)) {
       
        if (($gestor = fopen($archivo, 'r')) !== false) {
           
            $encabezados = fgetcsv($gestor, 1000, ',');

            $addressLine1 = '';$addressLine2 = '';$areaManager = '';$breakfast = '';$cakes = '';$city = '';$concept = '';$coreMenu = '';$country = '';$dmaCode = '';$dmaName = '';$districtCode = '';$districtName = '';$driveThru = '';$escalation1 = '';$escalation2 = '';$franchiseeEmail = '';$franchiseeName = '';$franchiseePhone = '';$lastModernizationDate = '';$openDate = '';$regionCode = '';$regionName = '';$storeEmail = '';$storeName = '';$storeNumber = '';$storePhone = '';$tempClosed = '';$venueType = '';$zip = '';
            $i = 0;

            while (($fila = fgetcsv($gestor, 1000, ',')) !== false) {
            $i++;

             
                if (count($fila) == count($encabezados)) {

                    list($addressLine1, $addressLine2, $areaManager, $breakfast, $cakes, $city, $concept, $coreMenu, $country, $dmaCode, $dmaName, $districtCode, $districtName, $driveThru, $escalation1, $escalation2, $franchiseeEmail, $franchiseeName, $franchiseePhone, $lastModernizationDate, $openDate, $regionCode, $regionName, $storeEmail, $storeName, $storeNumber, $storePhone, $tempClosed, $venueType, $zip) = $fila;
                   
                    echo "<br><br><br><br> -------------<b>$i</b>----$fechaActual --------<br><br><br><br>";
                    echo "<strong>Address Line 1:</strong> $addressLine1<br>";
                    echo "<strong>Address Line 2:</strong> $addressLine2<br>";
                    echo "<strong>Area Manager:</strong> $areaManager<br>";
                    echo "<strong>Breakfast:</strong> $breakfast<br>";
                    echo "<strong>Cakes:</strong> $cakes<br>";
                    echo "<strong>City:</strong> $city<br>";
                    echo "<strong>Concept:</strong> $concept<br>";
                    echo "<strong>Core Menu:</strong> $coreMenu<br>";
                    echo "<strong>Country:</strong> $country<br>";
                    echo "<strong>DMA Code:</strong> $dmaCode<br>";
                    echo "<strong>DMA Name:</strong> $dmaName<br>";
                    echo "<strong>District Code:</strong> $districtCode<br>";
                    echo "<strong>District Name:</strong> $districtName<br>";
                    echo "<strong>Drive-thru:</strong> $driveThru<br>";
                    echo "<strong>Escalation 1:</strong> $escalation1<br>";
                    echo "<strong>Escalation 2:</strong> $escalation2<br>";
                    echo "<strong>Franchisee Email:</strong> $franchiseeEmail<br>";
                    echo "<strong>Franchisee Name:</strong> $franchiseeName<br>";
                    echo "<strong>Franchisee Phone:</strong> $franchiseePhone<br>";
                    echo "<strong>Last Modernization Date:</strong> $lastModernizationDate<br>";
                    echo "<strong>Open Date:</strong> $openDate<br>";
                    echo "<strong>Region Code:</strong> $regionCode<br>";
                    echo "<strong>Region Name:</strong> $regionName<br>";
                    echo "<strong>Store Email:</strong> $storeEmail<br>";
                    echo "<strong>Store Name:</strong> $storeName<br>";
                    echo "<strong>Store Number:</strong> $storeNumber<br>";
                    echo "<strong>Store Phone:</strong> $storePhone<br>";
                    echo "<strong>Temp Closed:</strong> $tempClosed<br>";
                    echo "<strong>Venue Type:</strong> $venueType<br>";
                    echo "<strong>ZIP:</strong> $zip<br>";
                    echo "<br><br><br> --------------------------<br><br><br><br>";

                } else {
                    echo "La cantidad de columnas no coincide en una fila.<br>";
                }
            }

       
            fclose($gestor);
        } else {
            echo "No se pudo abrir el archivo.";
        }
    } else {
        echo "El archivo no existe.";
    }
  
}

public function cargarTiendasSFTP(){
    ini_set("max_execution_time", "-1"); // Sin límite de tiempo de ejecución
    ini_set("memory_limit", "-1"); // Sin límite de memoria
    ignore_user_abort(true);       // El script continuará ejecutándose incluso si el usuario cierra el navegador
    set_time_limit(0);             // Sin límite de tiempo de ejecución

    date_default_timezone_set('America/Mexico_City');
    $fechaActual = date('Y-m-d');
    $carpetaLocal = 'datafeed';
    $inicio = date('Y-m-d H:i:s');
    $archivoLocal = $carpetaLocal . '/store-export-' . $fechaActual . '.csv';
	$objUsuarios = new UsuariosModel();

    function eliminarArchivosViejos($directorio, $dias = 5) {
        $limite = time() - ($dias * 24 * 60 * 60); // Timestamp límite
        
        if (!is_dir($directorio)) {
            return "El directorio no existe ".$directorio.'<br>';
        }
        
        $archivos = scandir($directorio);
        $archivosEliminados = 0;
        
        foreach ($archivos as $archivo) {
            if ($archivo == '.' || $archivo == '..') continue;
            
            $rutaCompleta = $directorio . '/' . $archivo;
            
            // Verificar si es archivo (no directorio)
            if (is_file($rutaCompleta)) {
                $tiempoCreacion = filectime($rutaCompleta);
                
                if ($tiempoCreacion < $limite) {
                    if (unlink($rutaCompleta)) {
                        $archivosEliminados++;
                        echo "Eliminado: $archivo<br>";
                    } else {
                        echo "Error al eliminar: $archivo<br>";
                    }
                }
            }
        }
        
        return "Archivos eliminados: $archivosEliminados<br>";
    }

    $log = LocationModel::existeLogSFTP($fechaActual);
    $limpiar = eliminarArchivosViejos($carpetaLocal, 5);
    echo $limpiar;
    if(!$log){ //si no se ha procesado
    try {
        if(!file_exists($carpetaLocal . '/store-export-' . $fechaActual . '.csv')){
            $sftp = new SFTP('dlsccshared.blob.core.windows.net');
            $usuario = 'dlsccshared.churchs.psvcdlksftparguilea'; 

            // Verificar que el archivo .pem existe
            $pemFile = 'Config/psvcdlksftparguilea.pem';
            if (!file_exists($pemFile)) {
                throw new Exception("El archivo PEM no existe: $pemFile");
            }

            // Cargar la clave privada
            $key = PublicKeyLoader::load(
                file_get_contents($pemFile)
                // Si tiene passphrase: 'tu_passphrase_aqui'
            );

            if (!$sftp->login($usuario, $key)) {
                throw new Exception('Login falló con clave PEM');
            }

            $fechaJunta = date('Ymd');

            $directorio = '/egress/master/restaurants/';
            $archivos = $sftp->nlist($directorio);

            // Obtener el nombre del único archivo
            $nombreArchivo = reset($archivos);
            $rutaArchivoXLSX = $directorio . $nombreArchivo;
            
            $contenidoXLSX = $sftp->get($rutaArchivoXLSX);
            /*$rutaArchivoXLSX = '/egress/master/restaurants/Churchs_Restaurants_Master_'.$fechaJunta.'T035002.csv';
            $contenidoXLSX = $sftp->get($rutaArchivoXLSX);*/

            if (!$sftp->file_exists($rutaArchivoXLSX)) {
                throw new Exception("El archivo no existe: $rutaArchivoXLSX");
            }

            if ($contenidoXLSX === false) {
                throw new Exception('No se pudo descargar el archivo');
            } 

            // Crear carpeta local datafeed si no existe
            
            if (!file_exists($carpetaLocal)) {
                if (!mkdir($carpetaLocal, 0755, true)) {
                    throw new Exception("No se pudo crear la carpeta local: $carpetaLocal");
                }
                echo "📁 Carpeta local creada: $carpetaLocal\n";
            }

            // Guardar archivo en la carpeta datafeed
            
            if (file_put_contents($archivoLocal, $contenidoXLSX) === false) {
                throw new Exception("No se pudo guardar el archivo localmente: $archivoLocal");
            }
            $fechaActual = date('Y-m-d H:i:s');
            $size = filesize($archivoLocal);
            $log = LocationModel::setLogSFTP($size, $fechaActual, $archivoLocal, 1, 0, 0, 0, $inicio, $fechaActual);
            
            echo "Archivo descargado exitosamente: $archivoLocal <br>";
        }else{

            function procesarCSVLento($rutaArchivo, $callback) {
                $handle = fopen($rutaArchivo, 'r');
                
                if ($handle !== false) {
                    $encabezados = fgetcsv($handle, 15000, ',');
                    $numeroFila = 0;
                    
                    while (($fila = fgetcsv($handle, 15000, ',')) !== false) {
                        $numeroFila++;
                        $filaProcesada = [];
                        
                        foreach ($encabezados as $indice => $encabezado) {
                            $filaProcesada[$encabezado] = isset($fila[$indice]) ? trim($fila[$indice]) : '';
                        }
                        
                        // Procesar cada fila con el callback
                        $callback($filaProcesada, $numeroFila);
                    }
                    
                    fclose($handle);
                }
                
                return $numeroFila;
            }

            $users = [];
            $locations = 0;
            $size = filesize($archivoLocal);
            // Uso:
            $totalFilas = procesarCSVLento($archivoLocal, function($fila, $numeroFila) use(&$users, &$locations) {
                //echo "Procesando fila $numeroFila:<br>";
                $existsLocation = LocationModel::existeLocationIndividual($fila['Restaurant Number']);
                $pasa = true;
                if (empty($fila['Restaurant Number'])) {
                    //echo 'no number ';
                    $pasa = false;
                }
                if (empty($fila['Restaurant Name'])) {
                    //echo 'no name ';
                    $fila['Restaurant Name'] = $fila['Restaurant Number'];
                }
                $email = emailFilter($fila['Restaurant Email']);
                if (empty($email)) {
                    echo 'invalid email '.$fila['Restaurant Number'].' <br>';
                    //$pasa = false;
                }
                $country_obj = $this->getDataCountry($fila['Restaurant Country Name']);
                $country_id = (count($country_obj)>0?$country_obj['id']:0);
                if ($country_id==0 || !in_array($country_id, [1,6,23,29])) {
                    echo 'invalid country '.$fila['Restaurant Number'].' <br>';
                    $pasa = false;
                }

                $language = 'eng';
                if(in_array($country_id, [1,10,18,33,35,36])){
                    $language = 'esp';
                }

                if($fila['Restaurant Status']=='Open')$fila['Restaurant Status']=1;
                if($fila['Restaurant Status']=='Close' || $fila['Restaurant Status']=='Closed' || $fila['Restaurant Status']=='Temporary Closed')$fila['Restaurant Status']=0;

                //echo ($existsLocation?'existe':'no existe').'<br>';

                if($pasa){

                        $insertUsers = false;
                        if ($existsLocation) {
                            $tmp = [
                                'name' => $fila['Restaurant Name'],
                                'address_1' => $fila['Restaurant Address'],
                                'city' => $fila['Restaurant Address'],
                                'state_code' => $fila['Restaurant State'],
                                'state_name' => $fila['Restaurant City'],
                                'zip' => $fila['Restaurant ZipCode'],
                                'country_id' => $country_id,
                                'country' => $fila['Restaurant Country Name'],
                                'phone' => $fila['Restaurant Phone'],
                                'email' => $fila['Restaurant Email'],
                                'shop_type' => $fila['Restaurant Ownership'],
                                'status' => $fila['Restaurant Status'],
                                'restaurant_segment' => $fila['Restaurant Segment'],
                                'latitude' => $fila['Latitude'],
                                'longitude' => $fila['Longitude'],
                                'restaurant_open_date' => $fila['Restaurant Open Date'],
                                'restaurant_original_open_year' => $fila['Restaurant Original Open Year'],
                                'restaurant_close_date' => $fila['Restaurant Close Date'],
                                'restaurant_temp_close_date' => $fila['Restaurant Temp Close Date'],
                                'restaurant_reopen_date' => $fila['Restaurant Reopen Date'],
                                'franchisee_agreement_expiration_date' => $fila['Franchisee Agreement Expiration Date'],
                                'venue_type' => $fila['Venue Type'],
                                'venue_detail' => $fila['Venue Detail'],
                                'DMA_number' => $fila['DMA Number'],
                                'DMA_name' => $fila['DMA Name'],
                                'market_number' => $fila['Market Number'],
                                'zone_number' => $fila['Zone Number'],
                                'region_number' => $fila['Region Number'],
                                'NFT_region' => $fila['NFT Region'],
                                'zone_director_phone' => $fila['Zone Director Phone'],
                                'market_leader_phone' => $fila['Market Leader Phone'],
                                'national_field_trainer_phone' => $fila['National Field Trainer Phone'],
                                'field_marketing_contact_phone' => $fila['Field Marketing Contact Phone'],
                                'regional_franchise_director_phone' => $fila['Regional Franchise Director Phone'],
                                'regional_manager_phone' => $fila['Regional Manager Phone'],
                                'franchise_president_phone' => $fila['Franchise President Phone'],
                                'franchise_vp_of_ops_phone' => $fila['Franchise Vp Of Ops Phone'],
                                'area_franchise_lead_phone' => $fila['Area Franchise Lead Phone'],
                                'training_restaurant_indicator' => $fila['Training Restaurant Indicator'],
                                'training_general_manager_name' => $fila['Training General Manager Name'],
                                'ceo' => $fila['CEO'],
                                'franchise_entity_name' => $fila['Franchise Entity Name'],
                                'sub_franchise_entity_name' => $fila['Sub Franchise Entity Name'],
                                'franchise_owner_address' => $fila['Franchise Owner Address'],
                                'franchise_owner_city' => $fila['Franchise Owner City'],
                                'franchise_owner_state' => $fila['Franchise Owner State'],
                                'franchise_owner_zip_code' => $fila['Franchise Owner ZipCode'],
                                'franchise_owner_country_name' => $fila['Franchise Owner Country Name'],
                                'franchise_owner_phone' => $fila['Franchise Owner Phone'],
                                'franchise_owner_base_of_ops_dma' => $fila['Franchise Owner Base Of Ops Dma'],
                                'franchise_owner_base_of_ops_state' => $fila['Franchise Owner Base Of Ops State'],
                                'franchise_contact_name' => $fila['Franchise Contact Name'],
                                'franchise_contact_phone' => $fila['Franchise Contact Phone'],
                                'franchise_contact_email' => $fila['Franchise Contact Email'],
                                'franchise_additional_contact_emails' => $fila['Franchise Additional Contact Emails'],
                                'brand' => $fila['Brand'],
                                'pos_type' => $fila['Pos Type'],
                                'image_type' => $fila['Image Type'],
                                'reimage_completion_date' => $fila['Reimage Completion Date'],
                                'distribution_center' => $fila['Distribution Center'],
                                'restaurant_drive_thru_type' => $fila['Restaurant Drive Thru Type'],
                                'guest_cx_indicator' => $fila['Guest CX Indicator']
                            ];
                            $update = LocationModel::updateLocation($tmp, "number = '".$fila['Restaurant Number']."'");
                            if ($update) {
                                if($email!='' && $email!=NULL){
                                    $insertUsers = true;
                                    $locations++;
                                }
                            }
                        } else {
                            $tmp = [
                                'name' => $fila['Restaurant Name'],
                                'number' => $fila['Restaurant Number'],
                                'address_1' => $fila['Restaurant Address'],
                                'city' => $fila['Restaurant Address'],
                                'state_code' => $fila['Restaurant State'],
                                'state_name' => $fila['Restaurant City'],
                                'zip' => $fila['Restaurant ZipCode'],
                                'country_id' => $country_id,
                                'country' => $fila['Restaurant Country Name'],
                                'phone' => $fila['Restaurant Phone'],
                                'email' => $fila['Restaurant Email'],
                                'shop_type' => $fila['Restaurant Ownership'],
                                'status' => $fila['Restaurant Status'],
                                'restaurant_segment' => $fila['Restaurant Segment'],
                                'latitude' => $fila['Latitude'],
                                'longitude' => $fila['Longitude'],
                                'restaurant_open_date' => $fila['Restaurant Open Date'],
                                'restaurant_original_open_year' => $fila['Restaurant Original Open Year'],
                                'restaurant_close_date' => $fila['Restaurant Close Date'],
                                'restaurant_temp_close_date' => $fila['Restaurant Temp Close Date'],
                                'restaurant_reopen_date' => $fila['Restaurant Reopen Date'],
                                'franchisee_agreement_expiration_date' => $fila['Franchisee Agreement Expiration Date'],
                                'venue_type' => $fila['Venue Type'],
                                'venue_detail' => $fila['Venue Detail'],
                                'DMA_number' => $fila['DMA Number'],
                                'DMA_name' => $fila['DMA Name'],
                                'market_number' => $fila['Market Number'],
                                'zone_number' => $fila['Zone Number'],
                                'region_number' => $fila['Region Number'],
                                'NFT_region' => $fila['NFT Region'],
                                'zone_director_phone' => $fila['Zone Director Phone'],
                                'market_leader_phone' => $fila['Market Leader Phone'],
                                'national_field_trainer_phone' => $fila['National Field Trainer Phone'],
                                'field_marketing_contact_phone' => $fila['Field Marketing Contact Phone'],
                                'regional_franchise_director_phone' => $fila['Regional Franchise Director Phone'],
                                'regional_manager_phone' => $fila['Regional Manager Phone'],
                                'franchise_president_phone' => $fila['Franchise President Phone'],
                                'franchise_vp_of_ops_phone' => $fila['Franchise Vp Of Ops Phone'],
                                'area_franchise_lead_phone' => $fila['Area Franchise Lead Phone'],
                                'training_restaurant_indicator' => $fila['Training Restaurant Indicator'],
                                'training_general_manager_name' => $fila['Training General Manager Name'],
                                'ceo' => $fila['CEO'],
                                'franchise_entity_name' => $fila['Franchise Entity Name'],
                                'sub_franchise_entity_name' => $fila['Sub Franchise Entity Name'],
                                'franchise_owner_address' => $fila['Franchise Owner Address'],
                                'franchise_owner_city' => $fila['Franchise Owner City'],
                                'franchise_owner_state' => $fila['Franchise Owner State'],
                                'franchise_owner_zip_code' => $fila['Franchise Owner ZipCode'],
                                'franchise_owner_country_name' => $fila['Franchise Owner Country Name'],
                                'franchise_owner_phone' => $fila['Franchise Owner Phone'],
                                'franchise_owner_base_of_ops_dma' => $fila['Franchise Owner Base Of Ops Dma'],
                                'franchise_owner_base_of_ops_state' => $fila['Franchise Owner Base Of Ops State'],
                                'franchise_contact_name' => $fila['Franchise Contact Name'],
                                'franchise_contact_phone' => $fila['Franchise Contact Phone'],
                                'franchise_contact_email' => $fila['Franchise Contact Email'],
                                'franchise_additional_contact_emails' => $fila['Franchise Additional Contact Emails'],
                                'brand' => $fila['Brand'],
                                'pos_type' => $fila['Pos Type'],
                                'image_type' => $fila['Image Type'],
                                'reimage_completion_date' => $fila['Reimage Completion Date'],
                                'distribution_center' => $fila['Distribution Center'],
                                'restaurant_drive_thru_type' => $fila['Restaurant Drive Thru Type'],
                                'guest_cx_indicator' => $fila['Guest CX Indicator']
                            ];
                            $insert = LocationModel::insertLocation($tmp);
                            if ($insert) {
                                $insertUsers = true;
                                $locations++;
                            }
                        }

                        $objUsuarios = new UsuariosModel();
                        if ($insertUsers) {
                            $objUsuarios->cleanUsersLocations([$fila['Restaurant Number']]); //le quitamos la tienda a los usuarios que previamente ya la tenian asignada
                            if($email!='' && $email!=NULL){
                                foreach (explode(',', $email) as $eGM) {
                                    $users[$eGM . '|' . 10][] = $fila['Restaurant Number'];
                                }
                            }
                        }        
                        
                        $rs = LocationModel::getLocation(['id'], "number='".$fila['Restaurant Number']."'");
                        $idLocation = $rs[0]['id'];
                        //die('idL:'.$idLocation);
                        //Zone Director
                        if(strpos($fila['Zone Director Email'], ',') !== false){
                            foreach(explode(',', $fila['Zone Director Email']) as $e){
                                if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Zone Director Name'], $e, '1', $country_id, 20, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director de zona
                                $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                                }
                            }
                        }else{
                            if($fila['Zone Director Email']!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Zone Director Name'], $fila['Zone Director Email'], '1', $country_id, 20, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director de zona
                            $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                            }
                        }
                        //Market Leader
                        if(strpos($fila['Market Leader Email'], ',') !== false){
                            foreach(explode(',', $fila['Market Leader Email']) as $e){
                                if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Market Leader Name'], $e, '1', $country_id, 19, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al market lider
                                $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                                }
                            }
                        }else{
                            if($fila['Market Leader Email']!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Market Leader Name'], $fila['Market Leader Email'], '1', $country_id, 19, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al market lider
                            $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                            }
                        }
                        //Regional Franchise Director
                        if(strpos($fila['Regional Franchise Director Email'], ',') !== false){
                            foreach(explode(',', $fila['Regional Franchise Director Email']) as $e){
                                if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Regional Franchise Director Name'], $e, '1', $country_id, 18, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                                $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                                }
                            }
                        }else{
                            if($fila['Regional Franchise Director Email']!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Regional Franchise Director Name'], $fila['Regional Franchise Director Email'], '1', $country_id, 18, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                            $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                            }
                        }
                        //Regional Manager
                        if(strpos($fila['Regional Manager Email'], ',') !== false){
                            foreach(explode(',', $fila['Regional Manager Email']) as $e){
                                if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Regional Manager Name'], $e, '1', $country_id, 17, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                                $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                                }
                            }
                        }else{
                            if($fila['Regional Manager Email']!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Regional Manager Name'], $fila['Regional Manager Email'], '1', $country_id, 17, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                            $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                            }
                        }
                        //Franchise President
                        if(strpos($fila['Franchise President Email'], ',') !== false){
                            foreach(explode(',', $fila['Franchise President Email']) as $e){
                                if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Franchise President Name'], $e, '1', $country_id, 22, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                                $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                                }
                            }
                        }else{
                            if($fila['Franchise President Email']!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Franchise President Name'], $fila['Franchise President Email'], '1', $country_id, 17, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                            $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                            }
                        }
                        //Franchise Vice President
                        if(strpos($fila['Franchise Vp Of Ops Email'], ',') !== false){
                            foreach(explode(',', $fila['Franchise Vp Of Ops Email']) as $e){
                                if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Franchise Vp Of Ops Name'], $e, '1', $country_id, 23, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                                $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                                }
                            }
                        }else{
                            if($fila['Franchise Vp Of Ops Email']!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Franchise Vp Of Ops Name'], $fila['Franchise Vp Of Ops Email'], '1', $country_id, 23, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                            $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                            }
                        }
                        //Area Franchise Lead
                        if(strpos($fila['Area Franchise Lead Email'], ',') !== false){
                            foreach(explode(',', $fila['Area Franchise Lead Email']) as $e){
                                if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Area Franchise Lead Name'], $e, '1', $country_id, 14, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                                $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                                }
                            }
                        }else{
                            if($fila['Area Franchise Lead Email']!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Area Franchise Lead Name'], $fila['Area Franchise Lead Email'], '1', $country_id, 14, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                            $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                            }
                        }
                        //Franchise Owner
                        if(strpos($fila['Franchise Owner Email'], ',') !== false){
                            foreach(explode(',', $fila['Franchise Owner Email']) as $e){
                                if($e!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Franchise Owner Name'], $e, '1', $country_id, 21, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                                $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                                }
                            }
                        }else{
                            if($fila['Franchise Owner Email']!=''){ $tmp = $objUsuarios->crearActualizarUser($fila['Franchise Owner Name'], $fila['Franchise Owner Email'], '1', $country_id, 21, 1, $fila['Restaurant Status'], $language, $idLocation); //crea/actualiza al director regional de franquisia
                            $array = array("iduser" => $tmp);
                                $objUsuarios->setLogParameters(0, ('Create Update user masive'), $array);
                            }
                        }
                    
                    
                    // Aquí puedes hacer algo con cada fila
                    // Ej: insertar en BD, validar datos, etc.
                    
                    //echo $fila['Restaurant Name']."<br>";
                }
                
            });

            $tmpUsers = [];
            foreach ($users as $us => $loc) { //inserta los usuarios tienda
                $us = explode('|', $us);
                $tmp = $objUsuarios->reviseUser($us[0], $us[1], $loc);
                $tmpUsers[$us[0]] = [$tmp['locations'], $tmp['action'], $us[0], $this->getNameRole($us[1])];
            }
            $fechaActual = date('Y-m-d H:i:s');
            $log = LocationModel::setLogSFTP($size, $fechaActual, $archivoLocal, 0, 1, 0, $locations, $inicio, $fechaActual);
            echo 'total insertadas:'.$locations.'<br>';

            echo "✅ Total filas procesadas: $totalFilas\n";
        }

    } catch (Exception $e) {
        exit('Error: ' . $e->getMessage());
    } finally {
        if (isset($sftp)) {
            $sftp->disconnect();
        }
    }
    }else{
        echo 'se omite';
        $fin = date('Y-m-d H:i:s');
        $size = 0;
        $log = LocationModel::setLogSFTP($size, $fin, $archivoLocal, 0, 0, 1, 0, $inicio, $fin);
    }

}

public function actionPlans(){
    date_default_timezone_set('America/Mexico_City');
    $fechaActual = date('Y-m-d H:m:s');
    $hoy = new DateTime($fechaActual);
    $notStarted = AuditoriaModel::getPlanActionNotStarted();
    foreach($notStarted as $n){
        $fechaLiberacion = new DateTime($n['date_release']);
        $diferencia = $hoy->diff($fechaLiberacion);
        $send = false;
        $mensaje = "";
        if($diferencia->days==7){
            echo $n['id'].' 7<br>';
            $send = true;
            $mensaje = "";
            
        }else if($diferencia->days==3){
            echo $n['id'].' 3<br>';
            $send = true;
            $mensaje = "";
        }else{
            echo $n['id'].' '.$diferencia->days.'<br>';
        }
        if($send){
            $audit = AuditsModel::getAuditList([], "id=".$n['id'])[0];
            $location = LocationModel::getLocation([], "id = ".$audit['location_id']);
            $esPrueba=false;
            if(in_array($audit['type'],['Calibration Audit']))$esPrueba=true;
            $totalOpps = countTotalOpps($n['id']);
            $titulo = (in_array($location['country_id'], [1,10,18,33,35,36])?'Recordatorio del plan de acción':'Action Plan Reminder');
            $asunto = "{$audit['brand_prefix']} #{$audit['location_number']} ({$audit['country_name']}) @ $titulo";
            $to = UsuariosModel::getTo(5, $audit['location_id'], $esPrueba, $audit['country_id']);
            
            
            sendEmailMasive([
                'asunto' 				=> $asunto,
                'email' 				=> $to,
                'audit_id'				=> $audit['id'],
                'score'					=> $cal,
                'round_name'			=> $audit['round_name'],
                'type'					=> $audit['type'],
                'date_visit'			=> $audit['date_visit'],
                'date_release'			=> date('Y-m-d H:i:s'),
                'date_limit'			=> date('d-m-Y', strtotime('+7 days')),
                'total_opps'			=> $totalOpps['opps'],
                'location_number'		=> $audit['location_number'],
                'location_name'			=> $audit['location_name'],
                'location_address'		=> $audit['location_address'],
                'country'               => $location['country_id'],
                'mensaje'		    => $mensaje
	
            ], (in_array($location['country_id'], [1,10,18,33,35,36])?'aplan_reminder':'aplan_reminder_eng'));
        }
    }
    $notRevised = AuditoriaModel::getPlanActionNotRevised();
    foreach($notRevised as $n){
        $fechaPlanReady = new DateTime($n['date_finish_action_plan']);
        $diferencia = $hoy->diff($fechaPlanReady);
        if($diferencia->days==5){
            echo $n['id'].' 5<br>';
        }else if($diferencia->days==3){
            echo $n['id'].' 3<br>';
        }else{
            echo $n['id'].' '.$diferencia->days.'<br>';
        }
    }
    echo 'not started:'.count($notStarted).' not revised:'.count($notRevised);
}

private function getDataCountry($key){
        $country = CountryModel::getCountry(['id, name'], "name='$key' AND active=1")[0];
        if($key=='United States'){
            $country['id']=37;
            $country['name']='United States of America';
        }

		$request = [
			'id' => $country['id'],
			'name' => $country['name']
		];
		return $request;
}

private function getNameRole($key)
{
	$dictionary = [
		10 => 'Shop / GM'
	];
	return $dictionary[$key];
}

public function csvFeedLayout() {
    
    date_default_timezone_set('America/Mexico_City');
    //$fechaActual = date('Y-m-d');
    $fechaActual = date('Y-m-d');
    
    //AUMENTAR TIEMPO DE EJECUCION
    ini_set("max_execution_time", "-1"); // Sin límite de tiempo de ejecución
    ini_set("memory_limit", "-1"); // Sin límite de memoria
    ignore_user_abort(true);       // El script continuará ejecutándose incluso si el usuario cierra el navegador
    set_time_limit(0);             // Sin límite de tiempo de ejecución

    $resetUser = UsuariosModel::startUser(); // reseteamos los usuarios
    $startlog = UsuariosModel::insertLog('start_feed_csv'); 

    date_default_timezone_set('America/Mexico_City');
    //$fechaActual = date('Y-m-d');
    $fechaActual = date('Y-m-d');
   
 
    $sftp = new SFTP('37.59.23.3');
    $usuario = 'dq_datafeed'; 
    $contrasena = 'Jaa$dQpxL&75$Sa6fuEG'; 
 
    // STAGE
    /*
    $sftp = new SFTP('37.59.18.194');
    $usuario = 'dq_stage_datafeed'; 
    $contrasena = '!66cWPoq%pK6jX2#g$xC';  
    */

    if (!$sftp->login($usuario, $contrasena)) {
        exit('No se pudo conectar al servidor SFTP.');
    }
    
    $rutaArchivoXLSX = 'datafeed/store-export-'.$fechaActual.'.csv';
    $contenidoXLSX = $sftp->get($rutaArchivoXLSX);
    
    if ($contenidoXLSX === false) {
        exit('No se pudo descargar el archivo XLSX.');
    } 
    
    // Guardar el contenido en un archivo temporal
   $archivo = tempnam(sys_get_temp_dir(), 'xlsx');
   file_put_contents($archivo, $contenidoXLSX);
     

    // RUTA LOCAL EN EL SERVIDOR
  //$archivo = $_SERVER['DOCUMENT_ROOT'] . '/Controllers/feed/store-export-'.$fechaActual.'.csv';


//DECALRO EL ARREGLO PARA LAMACENAR TODOS LOS EMAILS
$dataEmail = array();
//DECLARAMOS UN ARREGLO PARA ALMACENAR LOS ERRORES POR FILA 
$dataErrorLog = array();
    // Verifica si el archivo existe
    if (file_exists($archivo)) {
        // Abre el archivo para lectura
        if (($gestor = fopen($archivo, 'r')) !== false) {
            // Lee la primera línea para obtener los encabezados
            $encabezados = fgetcsv($gestor, 1000, ',');

            // Inicializa variables para almacenar los datos
            $addressLine1 = '';$addressLine2 = '';$areaManager = '';$breakfast = '';$cakes = '';$city = '';$concept = '';$coreMenu = '';$country = '';$dmaCode = '';$dmaName = '';$districtCode = '';$districtName = '';$driveThru = '';$escalation1 = '';$escalation2 = '';$franchiseeEmail = '';$franchiseeName = '';$franchiseePhone = '';$lastModernizationDate = '';$openDate = '';$regionCode = '';$regionName = '';$storeEmail = '';$storeName = '';$storeNumber = '';$storePhone = '';$tempClosed = '';$venueType = '';$zip = '';
            $i = 0;
            // Lee cada línea del archivo CSV
            while (($fila = fgetcsv($gestor, 1000, ',')) !== false) {
            $i++;

                // Asigna los valores de cada columna a las variables correspondientes
                if (count($fila) == count($encabezados)) {

                    list($addressLine1, $addressLine2, $areaManager, $breakfast, $cakes, $city, $concept, $coreMenu, $country, $dmaCode, $dmaName, $districtCode, $districtName, $driveThru, $escalation1, $escalation2, $franchiseeEmail, $franchiseeName, $franchiseePhone, $lastModernizationDate, $openDate, $regionCode, $regionName, $storeEmail, $storeName, $storeNumber, $storePhone, $tempClosed, $venueType, $zip) = $fila;
                   
                    echo "<br><br><br><br> -------------<b>$i</b>------------<br><br><br><br>";
                    echo "<strong>Address Line 1:</strong> $addressLine1<br>";
                    echo "<strong>Address Line 2:</strong> $addressLine2<br>";
                    echo "<strong>Area Manager:</strong> $areaManager<br>";
                    echo "<strong>Breakfast:</strong> $breakfast<br>";
                    echo "<strong>Cakes:</strong> $cakes<br>";
                    echo "<strong>City:</strong> $city<br>";
                    echo "<strong>Concept:</strong> $concept<br>";
                    echo "<strong>Core Menu:</strong> $coreMenu<br>";
                    echo "<strong>Country:</strong> $country<br>";
                    echo "<strong>DMA Code:</strong> $dmaCode<br>";
                    echo "<strong>DMA Name:</strong> $dmaName<br>";
                    echo "<strong>District Code:</strong> $districtCode<br>";
                    echo "<strong>District Name:</strong> $districtName<br>";
                    echo "<strong>Drive-thru:</strong> $driveThru<br>";
                    echo "<strong>Escalation 1:</strong> $escalation1<br>";
                    echo "<strong>Escalation 2:</strong> $escalation2<br>";
                    echo "<strong>Franchisee Email:</strong> $franchiseeEmail<br>";
                    echo "<strong>Franchisee Name:</strong> $franchiseeName<br>";
                    echo "<strong>Franchisee Phone:</strong> $franchiseePhone<br>";
                    echo "<strong>Last Modernization Date:</strong> $lastModernizationDate<br>";
                    echo "<strong>Open Date:</strong> $openDate<br>";
                    echo "<strong>Region Code:</strong> $regionCode<br>";
                    echo "<strong>Region Name:</strong> $regionName<br>";
                    echo "<strong>Store Email:</strong> $storeEmail<br>";
                    echo "<strong>Store Name:</strong> $storeName<br>";
                    echo "<strong>Store Number:</strong> $storeNumber<br>";
                    echo "<strong>Store Phone:</strong> $storePhone<br>";
                    echo "<strong>Temp Closed:</strong> $tempClosed<br>";
                    echo "<strong>Venue Type:</strong> $venueType<br>";
                    echo "<strong>ZIP:</strong> $zip<br>";
                    echo "<br><br><br> --------------------------<br><br><br><br>"; // Nueva línea entre registros




                    //variables
                    $data = array('addressLine1' => $addressLine1, 'addressLine2' => $addressLine2, 'areaManager' => $areaManager, 'breakfast' => $breakfast, 'cakes' => $cakes, 'city' => $city, 'concept' => $concept, 'coreMenu' => $coreMenu, 'country' => $country, 'dmaCode' => $dmaCode, 'dmaName' => $dmaName, 'districtCode' => $districtCode, 'districtName' => $districtName, 'driveThru' => $driveThru, 'escalation1' => $escalation1, 'escalation2' => $escalation2, 'franchiseeEmail' => $franchiseeEmail, 'franchiseeName' => $franchiseeName, 'franchiseePhone' => $franchiseePhone, 'lastModernizationDate' => $lastModernizationDate, 'openDate' => $openDate, 'regionCode' => $regionCode, 'regionName' => $regionName, 'storeEmail' => $storeEmail, 'storeName' => $storeName, 'storeNumber' => $storeNumber, 'storePhone' => $storePhone, 'tempClosed' => $tempClosed, 'venueType' => $venueType, 'zip' => $zip);

                    $setExcelLocation = UsuariosModel::setExcelLocationCSV($data);

                foreach ($setExcelLocation as $si) {
	
	
                    $locationId      = $si['locationId'];
                    $actualizaStatus = $si['actualizaStatus'];
                    $statusAnterior  = $si['statusAnterior'];
                    $statusActual    = $si['statusActual'];

                    echo 'se envia estatus '.$actualizaStatus;

                    
                   
                    if($actualizaStatus == 1){

                        $tabla_sucursal =  "<table border='1' cellpadding='5' cellspacing='0'>
            				                    <tr>    
            				                        <th>#</th>
            				                        <th>Location</th>
            				                        <th>Previous status</th>
            				                        <th>Current status</th>
            				                    </tr>

                                                <tr>
                                                    <td>".$storeNumber."</td>
                                                    <td>".$storeName."</td>
                                                    <td>".$statusAnterior."</td>
                                                    <td>".$statusActual."</td>
                                                </tr>
                                            </table>";



                        $data = ['asunto' => 'Status change announcement', 'email' => 'ycabello@arguilea.com, dpeza@arguilea.com', 'tabla_sucursal' => $tabla_sucursal];
                        sendEmail($data, 'announced_status_location');
                    }
//echo "------------------------------------------- $fila ----id tienda: " . htmlspecialchars($si['locationId']) . "-------------------------------------------<br>";
                    //AREA MANAGER              
                   


                   // $franchiseeEmail; //5. Franquiciado    
                   // $escalation2;     //4. Escalacion 2    
                   // $escalation1;     //3. Escalacion 1        
                   // $areaManager;     //2. Area Manager    
                   // $storeEmail;      //1. Store Manager    

                   
                   
                   
                    if (filter_var($areaManager, FILTER_VALIDATE_EMAIL)) { 
                        $modelareaManager = UsuariosModel::setExcelUserCSV(1,1,20,$storeName, $areaManager,$locationId);
                    foreach ($modelareaManager as $areaManager) {
                        if($areaManager['validacionEmail']==1){array_push($dataEmail,   $areaManager);}
                    }} else {
                        array_push($dataErrorLog, array('correo_incorrecto_areaManager'    => $areaManager));}

                    //STORE MANAGER
                    if (filter_var($storeEmail, FILTER_VALIDATE_EMAIL)) { 
                        $modelstoreEmail = UsuariosModel::setExcelUserCSV(1,1,10,$storeName, $storeEmail,$locationId);
                        foreach ($modelstoreEmail as $storeEmail) {
                            if($storeEmail['validacionEmail']==1){array_push($dataEmail,   $storeEmail);}
                        }} else {
                            array_push($dataErrorLog, array('correo_incorrecto_storeEmail'    => $storeEmail));}
                    
                    //FRANCHISSE
                    if (filter_var($franchiseeEmail, FILTER_VALIDATE_EMAIL)) { 
                        $modelfranchiseeEmail = UsuariosModel::setExcelUserCSV(1,1,14,$storeName, $franchiseeEmail,$locationId);
                        foreach ($modelfranchiseeEmail as $franchiseeEmail) {
                            if($franchiseeEmail['validacionEmail']==1){array_push($dataEmail,   $franchiseeEmail);}
                        }} else {
                            array_push($dataErrorLog, array('correo_incorrecto_franchiseeEmail'    => $franchiseeEmail));}
                
                    //OPPS LEADER
                    if (filter_var($escalation1, FILTER_VALIDATE_EMAIL)) { 
                        $modelescalation1 = UsuariosModel::setExcelUserCSV(1,1,19,$escalation1,$escalation1,$locationId);
                        foreach ($modelescalation1 as $escalation1) {
                            if($escalation1['validacionEmail']==1){array_push($dataEmail,   $escalation1);}
                        }} else {
                            array_push($dataErrorLog, array('correo_incorrecto_franchiseeEmail'    => $escalation1));}
                    //OPS DIRECTOR
                    if (filter_var($escalation2, FILTER_VALIDATE_EMAIL)) { 
                        $modelescalation2 = UsuariosModel::setExcelUserCSV(1,1,18,$escalation2, $escalation2,$locationId);
                        foreach ($modelescalation2 as $escalation2) {
                            if($escalation2['validacionEmail']==1){array_push($dataEmail,   $escalation2);}
                        }} else {
                            array_push($dataErrorLog, array('correo_incorrecto_franchiseeEmail'    => $escalation2));}
                    

                }

                $datos_parameter = json_encode($data);
                //echo $datos_parameter;
                $insertDataLog = UsuariosModel::insertDataLog($data);
            

                } else {
                    echo "La cantidad de columnas no coincide en una fila.<br>";
                }
            }

            // Cierra el archivo
            fclose($gestor);
        } else {
            echo "No se pudo abrir el archivo.";
        }
    } else {
        echo "El archivo no existe.";
        $log = UsuariosModel::insertLog('The file does not exist'); 
    }

    $locationFix = UsuariosModel::locationFix(); // ajusta las comas en el location_id
    $log = UsuariosModel::insertLog('finish_feed_csv'); 


//REPORTE DE USUARIOS

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
            $outputRows[] = "<tr>
                                <td>" . $usuario . "</td>
                                <td>" . $email . "</td>
                                <td>" . $role . "</td>
                                <td>" . $i . "</td>
                                <td style='width: 600px;'><ol>" . $outputLocation . "</ol></td>
                            </tr>";
        //echo "Total de tiendas - ".$i ."<br>----------------------------------<br>";
       
    }

    $output = implode("", $outputRows);
    $tabla = "<table style='border-collapse: collapse; width: 100%;' border='1' cellpadding='5' cellspacing='0'>
                <thead>
                    <tr>
                        <th>USER</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th>#</th>
                        <th>STORE</th>
                    </tr>
                </thead>
                <tbody>
                    " . $output . "
                </tbody>
              </table>";

    echo $tabla;



    $data = ['asunto' => 'Users report', 'email' => '', 'tabla' => $tabla];

    sendEmail($data, 'user_report');



}















public function reAudit() {

    $id_visit = 960;
    $reAudit = AuditoriaModel::reAudit($id_visit);

    $outputRows = [];
    foreach ($reAudit as $data) {
	
        $id_visit      = $data['id_visit'];        
        $round         = $data['round'];    
        $period        = $data['period'];    
        $type          = $data['type'];    
        $nombre_tienda = $data['nombre_tienda'];            
        $numero_tienda = $data['numero_tienda'];            
        $auditor_name  = $data['auditor_name'];            
        $auditor_email = $data['auditor_email'];            
        $status        = $data['status'];  
        $limpieza      = $data['limpieza'];  
        $seguridad_alimentos = $data['seguridad_alimentos'];  


        $location_id              = $data['location_id'];     
		$id_round                 = $data['id_round']; 
		$brand_id                 = $data['brand_id']; 
		$additional_question_id   = $data['additional_question_id'];                 
		$scoring_id               = $data['scoring_id']; 
        
        $checklist_id       =  $data['checklist_id'];
		$report_layout_id   =  $data['report_layout_id'];
		$local_foranea      =  $data['local_foranea'];
		
        
        $address_1          =  $data['address_1'];
		$franchissees_name  =  $data['franchissees_name'];
		$date_visit         =  $data['date_visit'];
		$email_location     =  $data['email_location'];

      
    

        //$insertReAudit = AuditoriaModel::insertReAudit($id_round, $checklist_id,  $scoring_id, $additional_question_id,$location_id,$report_layout_id,$auditor_name,$auditor_email,$local_foranea);
         
             $outputRows[] = "<tr>
                                <td>" . $id_visit . "</td>
                                <td>" . $round . "</td>
                                <td>" . $period . "</td>
                                <td>" . $type . "</td>
                                <td>" . $numero_tienda . "</td>
                                <td>" . $nombre_tienda . "</td>
                                <td>" . $auditor_name . "</td>
                                <td>" . $auditor_email . "</td>
                                <td>" . $limpieza  . "</td>
                                <td>" . $seguridad_alimentos . "</td>
                            </tr>";
      }
         
         
             $output = implode("", $outputRows);
         
             $tabla =  "<table border='1' cellpadding='5' cellspacing='0'>
                                     <tr>    
                                         <th>id</th>
                                         <th>Round comment</th>
                                         <th>Period</th>
                                         <th>Type</th>
                                         <th>#</th>
                                         <th>Location</th>
                                         <th>Auditor</th>
                                         <th>Auditor Email</th>
                                         <th>Cleaning critics</th>
                                         <th>Food Safety critics</th>
                                     </tr>" . $output . "</table>";
            
         echo $tabla ;
                     $data = ['asunto' => 'Re-audit ', 
                               'email' =>  '',
                               'tabla' =>  $tabla];
                     
                               $data = [
                                'asunto'               => 'Re-audit',
                                //'email'                => $email_location,
                                'id_visit'             => $id_visit,        
                                'round'                => $round,    
                                'period'               => $period,    
                                'type'                 => $type,    
                                'nombre_tienda'       => $nombre_tienda,            
                                'numero_tienda'       => $numero_tienda,            
                                'auditor_name'        => $auditor_name,            
                                'auditor_email'       => $auditor_email,            
                                'status'               => $status,  
                                'limpieza'             => $limpieza,  
                                'seguridad_alimentos' => $seguridad_alimentos,
                                'location_id'         => $location_id,     
                                'id_round'            => $id_round, 
                                'brand_id'            => $brand_id, 
                                'additional_question_id' => $additional_question_id,                 
                                'scoring_id'          => $scoring_id, 
                                'checklist_id'        => $checklist_id,
                                'report_layout_id'    => $report_layout_id,
                                'local_foranea'       => $local_foranea,
                                'address_1'           => $address_1,
                                'franchissees_name'   => $franchissees_name,
                                'date_visit'          => $date_visit
                            ];
                            
                     //sendEmail($data, 'announced_revisit');

                     if(!in_array($type,['Calibration Audit'])){
                        sendEmailMasive($data, 'reaudit');
                     }
     
}

public function testCarta() {
    $reAudit = AuditoriaModel::reAudit(23);
            
    ////////////////////////               
                    foreach ($reAudit as $data) {
                    
                        $id_visit      = $data['id_visit'];        
                        $round         = $data['round'];    
                        $period        = $data['period'];    
                        $type          = $data['type'];    
                        $nombre_tienda = $data['nombre_tienda'];            
                        $numero_tienda = $data['numero_tienda'];            
                        $auditor_name  = $data['auditor_name'];            
                        $auditor_email = $data['auditor_email'];            
                        $status        = $data['status'];  
                        $limpieza      = $data['limpieza'];  
                        $seguridad_alimentos = $data['seguridad_alimentos'];
                        
                       
                
                
                        $location_id              = $data['location_id'];     
                        $id_round                 = $data['id_round']; 
                        $brand_id                 = $data['brand_id']; 
                        $additional_question_id   = $data['additional_question_id'];                 
                        $scoring_id               = $data['scoring_id']; 
                        
                        $checklist_id       =  $data['checklist_id'];
                        $report_layout_id   =  $data['report_layout_id'];
                        $local_foranea      =  $data['local_foranea'];
                        
                        
                        $address_1          =  $data['address_1'];
                        $franchissees_name  =  $data['franchissees_name'];
                        $date_visit         =  $data['date_visit'];
                        
                        $email_main_office    = $data['email_main_office'];
                        $email_store_manager  = $data['email_store_manager'];
                        $email_franchisee     = $data['email_franchisee'];
                        $email_area_manager   = $data['email_area_manager'];
                        $email_ops_leader     = $data['email_ops_leader'];
                        $email_ops_director   = $data['email_ops_director'];

                        //echo '------';
                        //echo $email_main_office;  
                        //echo $email_store_manager;
                        //echo $email_franchisee;   
                        //echo $email_area_manager; 
                        //echo $email_ops_leader;   
                        //echo $email_ops_director; 
    
                        //$email_location     =  "$email_main_office, $email_store_manager, $email_franchisee, $email_area_manager, $email_ops_leader, $email_ops_director,Blanca.Flores@idq.com,JuanCarlos.Roux@idq.com,Armando.Castro@idq.com";
                        $email_location = "$email_main_office, $email_store_manager, $email_franchisee, $email_area_manager, $email_ops_leader, $email_ops_director,Blanca.Flores@idq.com,JuanCarlos.Roux@idq.com,Armando.Castro@idq.com";
                        $valid_emails = "";
                        
                        // Dividir los correos por coma
                        $emails = explode(",", $email_location);
                        
                        // Expresión regular para validar correo
                        $email_regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
                        
                        foreach ($emails as $email) {
                            $email = trim($email); // Eliminar espacios extra
                            // Validar el correo
                            if (preg_match($email_regex, $email)) {
                                if ($valid_emails != "") {
                                    $valid_emails .= ","; // Agregar coma si no es el primer correo
                                }
                                $valid_emails .= $email; // Agregar el correo válido a la cadena
                            }
                        }
                        
                       
                        echo $valid_emails.' <br>';
                        
          
                        $data = ['asunto'              => 'Re-audit',
                                 'email'               => $valid_emails,
                                 'id_visit'            => $id_visit,        
                                 'round'               => $round,    
                                 'period'              => $period,    
                                 'type'                => $type,    
                                 'nombre_tienda'       => $nombre_tienda,            
                                 'numero_tienda'       => $numero_tienda,            
                                 'auditor_name'        => $auditor_name,            
                                 'auditor_email'       => $auditor_email,            
                                 'status'               => $status,  
                                 'limpieza'             => $limpieza,  
                                 'seguridad_alimentos' => $seguridad_alimentos,
                                 'location_id'         => $location_id,     
                                 'id_round'            => $id_round, 
                                 'brand_id'            => $brand_id, 
                                 'additional_question_id' => $additional_question_id,                 
                                 'scoring_id'          => $scoring_id, 
                                 'checklist_id'        => $checklist_id,
                                 'report_layout_id'    => $report_layout_id,
                                 'local_foranea'       => $local_foranea,
                                 'address_1'           => $address_1,
                                 'franchissees_name'   => $franchissees_name,
                                 'email_franchisee'   => $email_franchisee,
                                 'date_visit'          => $date_visit ];
                                            
                                 if(!in_array($type,['Calibration Audit'])){
                                    sendEmailMasive($data, 'reaudit');
                                 }
                            }
    ////////////////////////////////////
    
}



public function user() {

    $user = UsuariosModel::user();

    $outputRows = [];

   

    foreach ($user as $data) {

        $usuario     = $data['usuario'];     
        $email       = $data['email'];     
        $role        = $data['role'];     
        $location_id = $data['location_id'];        

        /*echo "----------------------------------<br>";echo "Name: " .$usuario."<br>". 
             "Email: ".$email."<br>". 
             "Role: ".$role."<br>". 
             "Location ID: ".$location_id."<br>";*/

        
        $i = 0;
        $outputRowLocation = [];
        $outputRowLocationNumber = [];

        $location = UsuariosModel::location($location_id);

            foreach ($location as $data_location) {

                $numero_tienda = $data_location['numero_tienda'];
                $nombre_tienda = $data_location['nombre_tienda'];
            
                //echo $i."- #".$numero_tienda." "."Tienda: ".$nombre_tienda."<br>";
          
                $outputRowLocation[] = "<li>" . $nombre_tienda . "</li>";
                $outputRowLocationNumber[] = "<li>" . $numero_tienda . "</li>";

                $i ++;

            }

            $outputLocation = implode("", $outputRowLocation);
            $outputLocationNumber = implode("", $outputRowLocationNumber);
            $outputRows[] = "<tr>
                                <td>" . $usuario . "</td>
                                <td>" . $email . "</td>
                                <td>" . $role . "</td>
                                <td>" . $i . "</td>
                                <td><ol><b>" . $outputLocationNumber . "</b></ol></td>
                                <td style='width: 600px;'><ol>" . $outputLocation . "</ol></td>
                            </tr>";
        //echo "Total de tiendas - ".$i ."<br>----------------------------------<br>";
       
    }

    $output = implode("", $outputRows);
    $tabla = "<table style='border-collapse: collapse; width: 100%;' border='1' cellpadding='5' cellspacing='0'>
                <thead>
                    <tr>
                        <th>USER</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th>#</th>
                        <th>STORE NUMBER</th>
                        <th>STORE NAME</th>
                    </tr>
                </thead>
                <tbody>
                    " . $output . "
                </tbody>
              </table>";

    echo $tabla;
    

}

    public function synchronizeStores() {

        $allowed_origin = strpos($_SERVER["HTTP_HOST"], "-stage.") !== false? 'auditprogram-stage.bw-globalsolutions.com' : 'auditprogram.bw-globalsolutions.com';

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';


        if ($origin == $allowed_origin) {
            $data = selectLocation(['number', 'name', 'address_1', 'city', 'country', 'email', 'phone', 'country_id', "'Active' as 'status'", "'5' as brand_id"], "status = 1 AND country_id IN (SELECT id FROM country WHERE region NOT IN ('AMR') ) ");
            ///dep($data);
            die(json_encode($data));
            
        } 
        
    }

    public function delAudit() {
        header("Access-Control-Allow-Origin: " . (strpos($_SERVER["HTTP_HOST"], "-stage.") !== false? 'https://auditprogram-stage.bw-globalsolutions.com' : 'https://auditprogram.bw-globalsolutions.com'));
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: Origin");

        $response = AuditoriaModel::updateAudit([
            'status' => 'Deleted!'
        ], "audit_program_id =" . $_POST['assignments_id']);

        die(json_encode(['status' => $response > 0]));
    }







}
