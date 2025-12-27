<?php
require_once 'Models/AuditoriaModel.php';
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

class APIAudita extends Controllers
{

    public function __construct()
    {
        parent::__construct();
    }

    //Proceso Sync invocado por People (via Helmut)
    public function sync()
    {
        $audita_json = file_get_contents('php://input');
        $arrTipos = [
            '1'  => 'Standard',
            '11' => 'Re-Audit',
            '18' => '2nd Re-Audit',
            '28' => '3rd Re-Audit',
            '29' => '4th Re-Audit',
            '23' => 'Calibration Audit',
            '55' => 'Training-visits'
        ];

        //Datos a enviar a la Vista
        $data = [];

        if ($audita_json == '') {
            $data['response'] = "FAIL - Empty Data!";

        } else {
            $data['response'] = "SUCCESS";

            //Leer data del Sync
            $audita_data = json_decode($audita_json, true);

            $brand = BrandModel::getBrand(['id', 'prefix'], "prefix='$audita_data[marcaRef]'")[0];
            $roundInfo = knowRoundInfoBy($brand['prefix'], $audita_data['periodo']); //Helpers.php

            //Ultimas versiones de Scoring, Checklist, Layouts y AdditionalQuestion
            $scoring = ScoringModel::getScoring(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];

            //$checklist = ChecklistModel::getChecklist(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];

            $report_layout = Report_LayoutModel::getReport_Layout(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];
            $additional_question = Additional_QuestionModel::getAdditional_Question(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];

            $data['definitions']['scoring_id'] = $scoring['id'];
            $data['definitions']['checklist_id'] = '';
            $data['definitions']['report_layout_id'] = $report_layout['id'];
            $data['definitions']['additional_question_id'] = $additional_question['id'];

            //Recorrer cada visita del sync
            foreach ($audita_data['audita_data'] as $x => $row) {

                $location = LocationModel::getLocation(['id', 'country_id', 'shop_type', 'address_1'], "number='$row[Numero_Tienda]' AND brand_id='$brand[id]' AND status = 'Active'")[0];

                //Excepcion en caso de que los checklist sean por tipo de tienda
                // if(!empty($location['shop_type'])){
                //     $checklist = ChecklistModel::getChecklist(['id'], "brand_id=$brand[id] AND name like '%$location[shop_type]%' AND date_start <= '{$audita_data['periodo']}-01')")[0];
                //     $data['definitions']['checklist_id'] = $checklist['id'];
                // }
                //No existe la tienda?

                $checklist = ChecklistModel::getChecklist(['id'],"(SELECT CASE WHEN '$row[Pais]' = 'MEX' AND $row[Tipo] != '11' THEN 'Mexico'
                                                                               WHEN '$row[Pais]' != 'MEX' AND $row[Tipo] != '11' THEN 'International'
                                                                               WHEN $row[Tipo]  = '11' THEN 'Re-Audit' END) = shop_type")[0];
                $data['definitions']['checklist_id'] = $checklist['id'];


                if (empty($location)) {

                    $data['NOT Added Audits'][] = [
                        'number' => $row['Numero_Tienda'],
                        'audita_folio' => $row['Folio'],
                        'audita_id' => $row['Auditoria'],
                        'cause' => 'Store not exists or is not Active',
                    ];

                } else {
                    // $location = reset($location);
                    //Excepcion en caso de que los checklist sean por tipo de tienda
                    //$checklist = ChecklistModel::getChecklist(['id'], "brand_id={$brand['id']} AND shop_type = '{$location['shop_type']}' AND date_start <= '{$audita_data['periodo']}-01'")[0];
                    // $checklist = ChecklistModel::getChecklist(['id'], "brand_id={$brand['id']} AND shop_type = '{$location['shop_type']}' AND date_start <= '{$audita_data['periodo']}-01'")[0];
                    // $data['definitions']['checklist_id'] = $checklist['id'];

                    //Ya existe la visita?
                    //$isAudit = AuditoriaModel::getAudit(['id', 'status', 'date_visit', 'checklist_id', 'additional_question_id', 'audita_folio', 'audita_id'], "location_id=$location[id] AND audita_id=$row[Auditoria] AND audita_folio=$row[Folio]")[0];
//AceSync
                    if ($audita_data['Action'] == "AceSync") {  $logCategory = "Audit AceSync";
                                                                $isAudit = AuditoriaModel::getAudit([
                                                                    'id', 
                                                                    'status', 
                                                                    'date_visit', 
                                                                    'checklist_id', 
                                                                    'additional_question_id', 
                                                                    'audita_folio', 
                                                                    'audita_id',
                                                                    'audita_ace_id',
                                                                    'audita_ace_folio'
                                                                ], "location_id={$location['id']} AND audita_ace_id={$row['Auditoria']} AND audita_ace_folio={$row['Folio']}")[0];
                                                            
                                                            } else if ($audita_data['Action'] == "Autosync") {
                                                                $logCategory = "Audit Autosync";
                                                                $isAudit = AuditoriaModel::getAudit([
                                                                    'id', 
                                                                    'status', 
                                                                    'date_visit', 
                                                                    'checklist_id', 
                                                                    'additional_question_id', 
                                                                    'audita_folio', 
                                                                    'audita_id',
                                                                    'audit_program_id',
                                                                    'audit_program_folio'
                                                                ], "location_id={$location['id']} AND audit_program_id={$row['Auditoria']} AND audit_program_folio={$row['Folio']}")[0];
                                                            
                                                            } else {
                                                                $logCategory = "Audit Sync";
                                                                $isAudit = AuditoriaModel::getAudit([
                                                                    'id', 
                                                                    'status', 
                                                                    'date_visit', 
                                                                    'checklist_id', 
                                                                    'additional_question_id', 
                                                                    'audita_folio', 
                                                                    'audita_id',
                                                                    'audita_ace_id',
                                                                    'audita_ace_folio'
                                                                ], "location_id={$location['id']} AND audita_id={$row['Auditoria']} AND audita_folio={$row['Folio']}")[0];
                                                            }

                   

                    
                   
                    if ($isAudit['id']) {

                        //Instrucción Eliminar visita
                        if (in_array($row['Estatus'], array('Reprogramada', 'Eliminada', 'Cancelada')) || $isAudit['status'] == 'Deleted!') {

                            if(!in_array($isAudit['status'], ['In Process', 'Completed'])){
                                if ($isAudit['status'] != 'Deleted!') {
                                    $updateAuditValues = [
                                        'status' => 'Deleted!',
                                    ];
                                    AuditoriaModel::updateAudit($updateAuditValues, "id=$isAudit[id]");
    
                                    //Insertar Log
                                    $insertAudit_LogValues = [
                                        'audit_id' => $isAudit['id'],
                                        'user_id' => -1,
                                        'category' => $logCategory,
                                        'name' => 'Audit Deleted!',
                                        'details' => 'Audit was Deleted! by ' . $row['Estatus'] . ' instruction (current status: ' . $isAudit['status'] . ')',
                                        'date' => date('Y-m-d H:i:s'),
                                    ];
                                    Audit_LogModel::insertAudit_Log($insertAudit_LogValues);
                                }
    
                                $data['Deleted! Audits'][] = [
                                    'id_visit' => $isAudit['id'],
                                    'number' => $row['Numero_Tienda'],
                                    'audita_folio' => $row['Folio'],
                                    'audita_id' => $row['Auditoria'],
                                    'cause' => $row['Estatus'] . ' @ Audita',
                                ];
                            } else{

                                //Insertar Log
                                $insertAudit_LogValues = [
                                    'audit_id' => $isAudit['id'],
                                    'user_id' => -1,
                                    'category' => $logCategory,
                                    'name' => 'Audit Attempt rejected',
                                    'details' => 'Rejected change attempt by ' . $row['Estatus'] . ' instruction (current status: ' . $isAudit['status'] . ')',
                                    'date' => date('Y-m-d H:i:s'),
                                ];
                                Audit_LogModel::insertAudit_Log($insertAudit_LogValues);
                            }

                        } else if ($isAudit['status'] != 'Deleted!') {
                            //Actualizar algunos columnas
                            $updateAuditValues = [
                                'auditor_email' => $row['emailAuditor'],
                                'auditor_name' => $row['nombre_auditor'],
                                'local_foranea' => ($row['Foranea'] ? 'Foranea' : 'Local'),
                                'announced_date' => $row['Fecha_Programada'],
                            ];
                            AuditoriaModel::updateAudit($updateAuditValues, "id=$isAudit[id]");
                            $data['Already & Updated Audits'][$isAudit['id']] = [
                                'id_visit' => $isAudit['id'],
                                'number' => $row['Numero_Tienda'],
                                'round_type' => $arrTipos[$row['Tipo']],
                                'round_name' => $roundInfo['name'],

                                'audita_folio' => $isAudit['audita_folio']??$isAudit['audit_program_folio'],
                                'audita_id' => $isAudit['audita_id']??$isAudit['audit_program_id'],  

                                'status' => $isAudit['status'],
                                'date_visit' => $isAudit['date_visit'],
                                'audit_definition' => 'vID.' . $isAudit['additional_question_id'],
                                'checklist_version' => 'vID.' . $isAudit['checklist_id'],
                                'location_address' => $location['address_1'],
                            ];
                        }

                    } else if ($row['Estatus'] == 'Pendiente') {
                        //Si no existe, Insertar la Visita
                        //Identificar si ya existe el Round
                        $isRound = RoundModel::getRound(['id'], "type='" . $arrTipos[$row['Tipo']] . "' AND country_id=$location[country_id] AND name='$roundInfo[name]'")[0];

                        if (!$isRound['id']) {
                            $insertRoundValues = [
                                'brand_id' => $brand['id'],
                                'country_id' => $location['country_id'],
                                'name' => $roundInfo['name'],
                                'type' => $arrTipos[$row['Tipo']],
                                'date_start' => $roundInfo['date_start'],
                            ];
                            $newRoundID = RoundModel::insertRound($insertRoundValues);
                        } else {
                            $newRoundID = $isRound['id'];
                        }
                        



                        
                        if ($audita_data['Action'] == "AceSync") {
                            $insertAuditValues = [
                                'round_id'               => $newRoundID,
                                'location_id'            => $location['id'],
                                'checklist_id'           => $checklist['id'],
                                'scoring_id'             => $scoring['id'],
                                'additional_question_id' => $additional_question['id'],
                                'report_layout_id'       => $report_layout['id'],
                                'status'                 => 'Pending',
                                'audita_ace_folio'       => $row['Folio'],
                                'audita_ace_id'          => $row['Auditoria'],
                                'period'                 => $audita_data['periodo'],
                                'auditor_email'          => $row['emailAuditor'],
                                'auditor_name'           => $row['nombre_auditor'],
                                'local_foranea'          => ($row['Foranea'] ? 'Foranea' : 'Local'),
                            ];
                        } else if ($audita_data['Action'] == "Autosync") {
                            $insertAuditValues = [
                                'round_id'               => $newRoundID,
                                'location_id'            => $location['id'],
                                'checklist_id'           => $checklist['id'],
                                'scoring_id'             => $scoring['id'],
                                'additional_question_id' => $additional_question['id'],
                                'report_layout_id'       => $report_layout['id'],
                                'status'                 => 'Pending',
                                'audit_program_folio'    => $row['Folio'],
                                'audit_program_id'       => $row['Auditoria'],
                                'auditor_email'          => $row['emailAuditor'],
                                'auditor_name'           => $row['nombre_auditor'],
                                'announced_date'           => $row['Fecha_Programada'],
                                'local_foranea'          => ($row['Foranea'] ? 'Foranea' : 'Local'),
                            ];
                        } else {
                            $insertAuditValues = [
                                'round_id'               => $newRoundID,
                                'location_id'            => $location['id'],
                                'checklist_id'           => $checklist['id'],
                                'scoring_id'             => $scoring['id'],
                                'additional_question_id' => $additional_question['id'],
                                'report_layout_id'       => $report_layout['id'], 
                                'status'                 => 'Pending',
                                'audita_folio'           => $row['Folio'],
                                'audita_id'              => $row['Auditoria'],
                                'period'                 => $audita_data['periodo'],
                                'auditor_email'          => $row['emailAuditor'],
                                'auditor_name'           => $row['nombre_auditor'],
                                'local_foranea'          => ($row['Foranea'] ? 'Foranea' : 'Local'),
                            ];
                        }

                        



                        $newAuditID = AuditoriaModel::insertAudit($insertAuditValues);

                        if ($newAuditID) {
                            $data['New Added Audits'][$newAuditID] = [
                                'id_visit' => $newAuditID,
                                'number' => $row['Numero_Tienda'],
                                'round_type' => $arrTipos[$row['Tipo']],
                                'round_name' => $roundInfo['name'],
                                'audita_folio' => $row['Folio'],
                                'audita_id' => $row['Auditoria'],
                                'audit_definition' => 'vID.' . $additional_question['id'],
                                'checklist_version' => 'vID.' . $checklist['id'],
                                'location_address' => $location['address_1'],
                            ];

                            //Insertar Log
                            $insertAudit_LogValues = [
                                'audit_id' => $newAuditID,
                                'user_id' => -1,
                                'category' => $logCategory,
                                'name' => 'Audit Created',
                                'details' => 'Audit Created trough Sync Process',
                                'date' => date('Y-m-d H:i:s'),
                            ];
                            Audit_LogModel::insertAudit_Log($insertAudit_LogValues);
                        }
                    }
                }
            }
        }

        //carga y return ($data) a la vista
        $this->views->getView($this, "sync", $data);
    }

    //checklist_definition invocado por People (via Helmut)
    public function getChecklist()
    {
        $post = json_decode(file_get_contents('php://input'), 0);

        $brand = $post->brand;
        $tmp = explode(".", $post->checklist_version); //ej. vID.1
        $checklist_id = $tmp[1];
        $lang = strtolower($post->lang);
$fnT = translate($lang);
        //Obtener Checklist
        $checklist = ChecklistModel::getChecklist([], "id=$checklist_id")[0];

        //Si ya se encuentra en servidor el Json, utilizar ese
        $compiled = false;
        if (1 == 2 && $checklist['compiled_json_for_app']) {
            $tmp = json_decode($checklist['compiled_json_for_app'], 0);
            if (file_exists("$_SERVER[DOCUMENT_ROOT]/" . $tmp->$lang)) {
                $compiled = file_get_contents("$_SERVER[DOCUMENT_ROOT]/" . $tmp->$lang);
            }

        } else {
            //De lo contrario, compilarlo por primer vez.
            //Embeber con script aparte para no robustecer este controller
            include "APIAudita/APIAudita-CompilarChecklist-ParaApp.php";
        }

        if ($compiled) {
            $data = $compiled;
        } else {
            $data = "{ \"info\" : \"ERROR de Compilación; revisar con IT @Ben\" }";
        }

        //carga y return ($data) a la vista
        $this->views->getView($this, "getChecklist", $data);
    }

    //Additional QU3estion (audit definitions)
    public function getAdditionalQuestion()
    {
        $post = json_decode(file_get_contents('php://input'), 0);

        $brand = $post->brand;
        $tmp = explode(".", $post->audit_definition); //ej. vID.1
        $additional_question_id = $tmp[1];
        $lang = strtolower($post->lang);

        //dep($post);

        //Obtener Catalogo
        $additional_question = Additional_QuestionModel::getAdditional_Question(['id', 'compiled_json_for_app'], "id=$additional_question_id")[0];

        //Si ya se encuentra en servidor el Json, utilizar ese
        $compiled = false;
        if (1 == 2 && $additional_question['compiled_json_for_app']) {
            $tmp = json_decode($additional_question['compiled_json_for_app'], 0);
            if (file_exists("$_SERVER[DOCUMENT_ROOT]/" . $tmp->$lang)) {
                $compiled = file_get_contents("$_SERVER[DOCUMENT_ROOT]/" . $tmp->$lang);
            }

        } else {
            //De lo contrario, compilarlo por primer vez.
            //Embeber con script aparte para no robustecer este controller
            include "APIAudita/APIAudita-CompilarAdditional_Question-ParaApp.php";
        }

        if ($compiled) {
            $data = $compiled;
        } else {
            $data = "{ \"info\" : \"ERROR de Compilación; revisar con IT @Ben\" }";
        }

        //carga y return ($data) a la vista
        $this->views->getView($this, "getAdditionalQuestion", $data);
    }

    //Receive Info
    public function receiveInfo()
    {

        $appdata = json_decode(file_get_contents('php://input'), true);
        $audit_id = $appdata['id_visit'];
        $response = [];

        //Verificar si la visita existe
        $isAudit = AuditoriaModel::getAudit([], "id=$audit_id")[0];

        if (!$isAudit['id']) {
            $response['info'] = ['Message' => "INVALID ID ($audit_id) NOT EXISTS!"];

        } else if (in_array($isAudit['status'], ['Pending', 'Temp Processing'])) {

            require_once 'Models/Audit_OppModel.php';
            require_once 'Models/Audit_FileModel.php';
            require_once 'Models/Audit_PointModel.php';
            require_once 'Models/Audit_Addi_QuestionModel.php';
            require_once 'Models/Audit_ScoreModel.php';
            require_once 'Models/ScoringModel.php';

            //bloquear temporalmente mientras se procesa la data
            AuditoriaModel::updateAudit(['status' => 'Temp Processing'], "id=$isAudit[id]");

            //Eliminar toda data previa
            Audit_OppModel::deleteAudit_Opp("audit_id=$isAudit[id]");
            Audit_FileModel::deleteAudit_File("audit_id=$isAudit[id] AND type IN ('Opportunity', 'General Pictures', 'Additional Questions')");
            Audit_PointModel::deleteAudit_Point("audit_id=$isAudit[id]");
            Audit_Addi_QuestionModel::deleteAudit_Addi_Question("audit_id=$isAudit[id]");
            Audit_ScoreModel::deleteAudit_Score("audit_id=$isAudit[id]");

            //Procesar y salvar la Informacion
            include "APIAudita/APIAudita-ReceiveInfo.php";

            //Envío de E-mail Respuesta Preliminar
            $round = RoundModel::getRound([], "id=$isAudit[round_id]")[0];
            $location = LocationModel::getLocation(['id', 'country_id', 'name', 'number'], "id=$isAudit[location_id]")[0];
            $country = CountryModel::getCountry(['name', 'language'], "id=$location[country_id]")[0];
            $brand = BrandModel::getBrand(['id', 'prefix'], "id='$round[brand_id]'")[0];

            if($round['type'] != 'Calibration Audit'){
                $locationMails = getLocationEmails(['Fanchisee' , 'Ops Director' , 'Ops Leader' , 'Area Manager' , 'Store Manager'], $isAudit['location_id']);
            }
            $AdminMails = getLocationEmails(['admin arguilea'], 0);
            $recipients = emailFilter("{$isAudit['manager_email']},$locationMails,$AdminMails");
            
            if($visit_status == 'Closed'){
                //Limpiar los Scores
                ScoringModel::closedScore($isAudit['id']);
                
$audit_closed = "audit_closed_eng";


if ($country['language'] == 'esp') {

    $data_closed_visit = [
                    'asunto'            => "$appdata[brand] #$location[number] ($country[name]) @ Visita Cerrada",
                    'email'             => $recipients,
                    'audit_id'          => $isAudit['id'],
                    'content_title'     => 'Le informamos que se realizó la visita, pero el auditor encontró la tienda cerrada',
                    'content_message'   => '<p>Ronda: <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             Nombre de la Tienda: <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             Fecha de la Auditoría: <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             Nombre del Auditor: <b>' . $isAudit['auditor_name'] . '</b></p>'
                ];
$audit_closed = "audit_closed";



}else if ($country['language'] == 'eng') {

    $data_closed_visit = [
                    'asunto'            => "$appdata[brand] #$location[number] ($country[name]) @ Closed Visit",
                    'email'             => $recipients,
                    'audit_id'          => $isAudit['id'],
                    'content_title'     => 'We inform you that the visit was carried out, but the auditor found the store closed.',
                    'content_message'   => '<p>Round: <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             Store Name: <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             Audit Date: <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             Auditors Name: <b>' . $isAudit['auditor_name'] . '</b></p>'
                ];
$audit_closed = "audit_closed_eng";


 }
 else if ($country['language'] == 'ind') {

    $data_closed_visit = [
                    'asunto'            => "$appdata[brand] #$location[number] ($country[name]) @ Kunjungan Tertutup",
                    'email'             => $recipients,
                    'audit_id'          => $isAudit['id'],
                    'content_title'     => 'Kami informasikan bahwa kunjungan telah dilakukan, tetapi auditor menemukan toko dalam keadaan tutup.',
                    'content_message'   => '<p>Putaran: <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             Nama Toko: <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             Tanggal Audit: <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             Nama Auditor: <b>' . $isAudit['auditor_name'] . '</b></p>'
                ];
    $audit_closed = "audit_closed_ind";

}
else{

     $data_closed_visit = [
                    'asunto'            => "$appdata[brand] #$location[number] ($country[name]) @ Closed Visit",
                    'email'             => $recipients,
                    'audit_id'          => $isAudit['id'],
                    'content_title'     => 'We inform you that the visit was carried out, but the auditor found the store closed.',
                    'content_message'   => '<p>Round: <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             Store Name: <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             Audit Date: <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             Auditors Name: <b>' . $isAudit['auditor_name'] . '</b></p>'
                ];
$audit_closed = "audit_closed_eng";

 }


                



                sendEmail($data_closed_visit, $audit_closed);     
                
                



            } else{
                //Cálculo de Scores
                ScoringModel::setScore($isAudit['id'], $isAudit['scoring_id']);

                $url_audit_report = getURLReport($isAudit['id'], $isAudit['report_layout_id'],$country['language']);


$preliminary_email = 'audit_preliminary_results_eng';

if ($country['language'] == 'esp') {
    // Español
     $data_preliminary_email = [
                    'asunto'            => "$appdata[brand] #$location[number] ($country[name]) @ Resultados Preliminares",
                    'email'             => $recipients,
                    'audit_id'          => $isAudit['id'],
                    'content_title'     => 'Los resultados preliminares están disponibles en el siguiente enlace',
                    'content_message'   => '<p>Ronda: <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             Nombre de la tienda: <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             Fecha de la auditoría: <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             Estado de la auditoría: <b>' . $isAudit['visit_status'] . '</b><br />
                                             Nombre del auditor: <b>' . $isAudit['auditor_name'] . '</b></p>',
                    'content_url'       => '<a href="' . $url_audit_report . '">' . $url_audit_report .'</a>'
                ];

                $preliminary_email = 'audit_preliminary_results';

} else if ($country['language'] == 'eng') {
    $data_preliminary_email = [
                    'asunto'            => "$appdata[brand] #$location[number] ($country[name]) @ Preliminary Results",
                    'email'             => $recipients,
                    'audit_id'          => $isAudit['id'],
                    'content_title'     => 'Preliminary results are available at the following link',
                    'content_message'   => '<p>Round: <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             Store name: <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             Audit date: <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             Audit status: <b>' . $isAudit['visit_status'] . '</b><br />
                                             Auditor name: <b>' . $isAudit['auditor_name'] . '</b></p>',
                    'content_url'       => '<a href="' . $url_audit_report . '">' . $url_audit_report .'</a>'
                ];
                $preliminary_email = 'audit_preliminary_results_eng';

}
else if ($country['language'] == 'ind') {
    $data_preliminary_email = [
                    'asunto'            => "$appdata[brand] #$location[number] ($country[name]) @ Hasil Sementara",
                    'email'             => $recipients,
                    'audit_id'          => $isAudit['id'],
                    'content_title'     => 'Hasil sementara tersedia di tautan berikut',
                    'content_message'   => '<p>Putaran: <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             Nama Toko: <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             Tanggal Audit: <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             Status Audit: <b>' . $isAudit['visit_status'] . '</b><br />
                                             Nama Auditor: <b>' . $isAudit['auditor_name'] . '</b></p>',
                    'content_url'       => '<a href="' . $url_audit_report . '">' . $url_audit_report .'</a>'
                ];
                $preliminary_email = 'audit_preliminary_results_ind';

} else {
      $data_preliminary_email = [
                    'asunto'            => "$appdata[brand] #$location[number] ($country[name]) @ Preliminary Results",
                    'email'             => $recipients,
                    'audit_id'          => $isAudit['id'],
                    'content_title'     => 'Preliminary results are available at the following link',
                    'content_message'   => '<p>Round: <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             Store name: <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             Audit date: <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             Audit status: <b>' . $isAudit['visit_status'] . '</b><br />
                                             Auditor name: <b>' . $isAudit['auditor_name'] . '</b></p>',
                    'content_url'       => '<a href="' . $url_audit_report . '">' . $url_audit_report .'</a>'
                ];
                $preliminary_email = 'audit_preliminary_results_eng';
}
                

                sendEmail($data_preliminary_email, $preliminary_email);
            }

            //Notificar a WS de People
            $ws_data = [];
            $ws_data['token'] = 'x';
            $ws_data['WebServiceSGO_op'] = 'send_AuditaRealizada'; //'Confirm_AuditaRealizada';
            $ws_data['statusVisit'] = str_replace(array('Visited', 'Closed', 'Zero Protocol'), array('Finalizada', 'Cerrada', 'Finalizada'), $isAudit['visit_status']);
            $ws_data['estatus'] = 'En Proceso';
            $ws_data['Fecha_Real'] = $isAudit['date_visit'];
            $ws_data['id_visit'] = $isAudit['id'];
            $ws_data['marcaPrefix'] = $brand['prefix'];
            if ($cURLConnection = curl_init("https://people.bw-globalsolutions.com/API/audita_SyncAuditVisits/ws.audita.php")) {
                curl_setopt($cURLConnection, CURLOPT_POST, true);
                curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $ws_data);
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                $apiResponse = curl_exec($cURLConnection);
                
                if (curl_error($cURLConnection)) {
                    $error_msg = curl_error($cURLConnection);
                    exit;
                }
                curl_close($cURLConnection);
            }
            //$apiResponse = json_decode($apiResponse, true);		

            //Estatus general de la visita (y quitar bloquedo temporal)
            AuditoriaModel::updateAudit(['status' => ($visit_status=='Closed' ? 'Closed' : 'In Process')], "id=$isAudit[id]");

            //Response al App
            $response['info'] = ['Message' => "SUCCESS"];

        } else if ($isAudit['status'] == 'Temp Processing') {
            $response['info'] = ['Message' => "INVALID ACTION ID ($audit_id) STILL PROCESSING!"];

        } else if ($isAudit['status'] == 'Completed') {

            $response['info'] = ['Message' => "SUCCESS"];

        } else {
            $response['info'] = ['Message' => "INVALID ACTION ID ($audit_id) CURRENTLY $isAudit[status]"];

        }

        $data = json_encode($response);

        //carga y return ($data) a la vista
        $this->views->getView($this, "receiveInfo", $data);
    }

    public function fecha_programada ()
    {
        $_POST = json_decode(file_get_contents('php://input'), true);
        echo '<div style="border:solid 1px #CCC; padding:10px; background:#FBFBFB;"><h1>Date Of Visit -- Dairy Queen</h1>';
        dep($_POST);
        //echo '<pre>'; print_r($_POST); echo '</pre>';
        foreach($_POST as $i=>$v) if(is_numeric($i)){
            //echo "<p>$v[id_visit]  ---  $v[Fecha_Programada]</p>";

            $isDate = AuditoriaModel::getAudit(['id', 'announced_date'], "id=$v[id_visit]")[0];

            if ($isDate['announced_date']) {
                echo "<p>$v[id_visit]  ---  Ya tiene fecha $isDate[announced_date]</p>";
            } else {
                echo "<p>$v[id_visit]  ---  No tiene fecha se debe sincronizar</p>";
                $insertAudit_LogValues = [
                    'audit_id' => $v['id_visit'],
                    'user_id' => -1,
                    'category' => 'API Audita',
                    'name' => 'Fecha Programada',
                    'date' => date('Y-m-d H:i:s'),
                ];
                Audit_LogModel::insertAudit_Log($insertAudit_LogValues);
                //Log

                $updateAuditValues = [
                                    'announced_date' => $v['Fecha_Programada'],
                                ];
                AuditoriaModel::updateAudit($updateAuditValues, "id=$v[id_visit]");
            }

           
        }
        echo '</div>';
    }
}
