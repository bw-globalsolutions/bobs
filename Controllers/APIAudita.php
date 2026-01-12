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
            $checklist = ChecklistModel::getChecklist(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];
            $report_layout = Report_LayoutModel::getReport_Layout(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];
            $additional_question = Additional_QuestionModel::getAdditional_Question(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];

            $data['definitions']['scoring_id'] = $scoring['id'];
            $data['definitions']['checklist_id'] = $checklist['id'];
            $data['definitions']['report_layout_id'] = $report_layout['id'];
            $data['definitions']['additional_question_id'] = $additional_question['id'];

            //Recorrer cada visita del sync
            foreach ($audita_data['audita_data'] as $x => $row) {

                $location = LocationModel::getLocation(['id', 'country_id', 'shop_type', 'address_1'], "number='$row[Numero_Tienda]' AND brand_id='$brand[id]' AND (status = 'Active' OR status = 1)")[0];
                //Excepcion en caso de que los checklist sean por tipo de tienda
                // if(!empty($location['shop_type'])){
                //     $checklist = ChecklistModel::getChecklist(['id'], "brand_id=$brand[id] AND name like '%$location[shop_type]%' AND date_start <= '{$audita_data['periodo']}-01')")[0];
                //     $data['definitions']['checklist_id'] = $checklist['id'];
                // }
                //No existe la tienda?
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
                    if ($audita_data['Action'] == "AceSync") {
                        $logCategory = "Audit AceSync";
                        $isAudit = AuditoriaModel::getAudit(['id', 
                                                             'status', 
                                                             'date_visit', 
                                                             'checklist_id', 
                                                             'additional_question_id', 
                                                             'audita_folio', 
                                                             'audita_id',
                                                             'audita_ace_id',
                                                             'audita_ace_folio'], 
                                                             "location_id=".$location['id']." AND audita_ace_id=$row[Auditoria] AND audita_ace_folio=$row[Folio]")[0];
                                                             
                                                             
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
                        $isAudit = AuditoriaModel::getAudit(['id', 
                                                             'status', 
                                                             'date_visit', 
                                                             'checklist_id', 
                                                             'additional_question_id', 
                                                             'audita_folio', 
                                                             'audita_id',
                                                             'audita_ace_id',
                                                             'audita_ace_folio'], 
                                                             "location_id=".$location['id']." AND audita_id=$row[Auditoria] AND audita_folio=$row[Folio]")[0];
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
                                'announced_date' => $row['Fecha_Programada']
                            ];
                            AuditoriaModel::updateAudit($updateAuditValues, "id=$isAudit[id]");
                            $data['Already & Updated Audits'][$isAudit['id']] = [
                                'id_visit' => $isAudit['id'],
                                'number' => $row['Numero_Tienda'],
                                'round_type' => $arrTipos[$row['Tipo']],
                                'round_name' => $roundInfo['name'],

                                'audita_folio' => $row['Folio'],
                                'audita_id' => $row['Auditoria'],

                                'status' => $isAudit['status'],
                                'date_visit' => $row['Fecha_Programada'],
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
                            $insertAuditValues = ['round_id'               => $newRoundID,
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
                                'announced_date'         => $row['Fecha_Programada'],
                                'local_foranea'          => ($row['Foranea'] ? 'Foranea' : 'Local'),
                            ];
                        } else {
                            $insertAuditValues = ['round_id'               => $newRoundID,
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

    public function sync_ace()
    {
        $audita_json = file_get_contents('php://input');
        $arrTipos = ['1' => 'Standard', '11' => 'Re-Audit', '18' => '2nd Re-Audit', '28' => '3rd Re-Audit', '29' => '3rd Re-Audit', '23' => 'Calibration Audit'];
        // dep($audita_json);
        // die();
        //Datos a enviar a la Vista
        $data = [];

        if ($audita_json == '') {
            $data['response'] = "FAIL - Empty Data!";

        } else {
            $audita_data = json_decode($audita_json, true);
            if ($audita_data['action'] == "AceSync") {
                //$data['response'] = "SUCCESS";
                $data['action'] = $audita_data['action'];
                $data['periodo'] = $audita_data['periodo'];

                //Leer data de ACE ya procesada en people
                
    
                // dep($audita_data);
    
                // if ($audita_data['Action'] = "Autosync") {
                //     echo "<br>Action a usar";
                // }
                // die();
    
                $brand = BrandModel::getBrand(['id', 'prefix'], "prefix='$audita_data[marcaRef]'")[0];
                $roundInfo = knowRoundInfoBy($brand['prefix'], $audita_data['periodo']); //Helpers.php
    
                //Ultimas versiones de Scoring, Checklist, Layouts y AdditionalQuestion
                $scoring = ScoringModel::getScoring(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];
                $checklist = ChecklistModel::getChecklist(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];
                $report_layout = Report_LayoutModel::getReport_Layout(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];
                $additional_question = Additional_QuestionModel::getAdditional_Question(['id'], "brand_id=$brand[id] AND date_start <= '{$audita_data['periodo']}-01'")[0];
    
                /*$data['definitions']['scoring_id'] = $scoring['id'];
                $data['definitions']['checklist_id'] = $checklist['id'];
                $data['definitions']['report_layout_id'] = $report_layout['id'];
                $data['definitions']['additional_question_id'] = $additional_question['id'];*/
                $data['periodos_considerar'] = $audita_data['periodos_considerar'];
                $data['audita_data_nuevas_visitas'] = $audita_data['audita_data_nuevas_visitas'];
                $data['marcaRef'] = $audita_data['marcaRef'];
    
                //Recorrer cada visita del sync
                $summary = array();
                foreach ($audita_data['audita_data'] as $x => $row) {
    
                    $location = LocationModel::getLocation(['id', 'country_id', 'address_1', 'shop_type'], "number='$row[Numero_Tienda]' AND brand_id='$brand[id]' (and status = 'Active' or status = 1)")[0];
    
                    // if(!empty($location['shop_type'])){
                    //     $checklist = ChecklistModel::getChecklist(['id'], "brand_id=$brand[id] AND name like '%$location[shop_type]%' AND date_start <= '{$audita_data['periodo']}-01')")[0];
                    //     $data['definitions']['checklist_id'] = $checklist['id'];
                    // }
    
                    //No existe la tienda?
                    if (!$location['id'] ) {
    
                        $data['NOT Added Audits'][] = [
                            'number' => $row['Numero_Tienda'],
                            'ace_folio' => $row['Folio'],
                            'ace_id' => $row['Auditoria'],
                            'cause' => 'Store not exists or is not Active',
                        ];
    
                    } else { 
                        //La tienda existe y esta activa
                        //Ya existe la visita/asignacion?
                        //End point unico de ACE ya no hay validacion para ver de donde proviene
                        $logCategory = "Audit Ace";
                        $isAudit = AuditoriaModel::getAuditAce([
                            'id', 'status', 'round_id', 'date_visit', 'checklist_id', 'additional_question_id', 'ace_folio', 'ace_id', 'auditor_name'], "location_id=$location[id] AND ace_id=$row[Auditoria] AND ace_folio=$row[Folio]")[0];
                        //die(var_dump($isAudit));
                        

                        /*if ($row['id']) {
                            // echo "Existe row";
                            // dep($isAudit);
                            // die();
                            //Estos extatus no llegan de ACe de la forma normal de audita
                            //if (in_array($row['Estatus'], array('Reprogramada', 'Eliminada', 'Cancelada'))) {

                            //if ($isAudit['status'] == 'Pending') { //Aqui podria entrar el update, validar que no duplique informacion de ids de ace y que retorna
                                //Actualizar algunos columnas
                                $updateAuditValues = [
                                    'auditor_email' => $row['emailAuditor'],
                                    'auditor_name' => $row['nombre_auditor'],
                                    'local_foranea' => ($row['Foranea'] ? 'Foranea' : 'Local'),
                                    'announced_date' => $row['Fecha_Inicial']
                                ];
                                AuditoriaModel::updateAudit($updateAuditValues, "id=$isAudit[id]");
                                /*$data['Already & Updated Audits'][$isAudit['id']] = [
                                    'id_visit' => $isAudit['id'],
                                    'number' => $row['Numero_Tienda'],
                                    'round_type' => $arrTipos[$row['Tipo']],
                                    'round_name' => $roundInfo['name'],
                                    'ace_folio' => $isAudit['ace_folio'],
                                    'aace_id' => $isAudit['ace_id'],
                                    'status' => $isAudit['status'],
                                    'date_visit' => $isAudit['date_visit'],
                                    'announced_date' => $row['Fecha_Inicial'],
                                    'audit_definition' => 'vID.' . $isAudit['additional_question_id'],
                                    'checklist_version' => 'vID.' . $isAudit['checklist_id'],
                                    'location_address' => $location['address_1'],
                                ];*/
                                
                            //}
    
                        //} else 
                        if ($row['Estatus'] == 'Pendiente') {
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
    
                            $insertAuditValues = [
                                'round_id' => $newRoundID,
                                'location_id' => $location['id'],
                                'checklist_id' => $checklist['id'],
                                'scoring_id' => $scoring['id'],
                                'additional_question_id' => $additional_question['id'],
                                'report_layout_id' => $report_layout['id'],
                                'status' => 'Pending',
                                'ace_folio' => $row['Folio'],
                                'ace_id' => $row['Auditoria'],
                                'announced_date' => $row['Fecha_Inicial'],
                                'auditor_email' => $row['emailAuditor'],
                                'auditor_name' => $row['nombre_auditor'],
                                'local_foranea' => ($row['Foranea'] ? 'Foranea' : 'Local'),
                            ];
                            
                            $newAuditID = AuditoriaModel::insertAudit($insertAuditValues);
    
                            if ($newAuditID) {
                                /*$data['New Added Audits'][$newAuditID] = [
                                    'id_visit' => $newAuditID,
                                    'number' => $row['Numero_Tienda'],
                                    'round_type' => $arrTipos[$row['Tipo']],
                                    'round_name' => $roundInfo['name'],
                                    'ace_folio' => $row['Folio'],
                                    'ace_id' => $row['Auditoria'],
                                    'audit_definition' => 'vID.' . $additional_question['id'],
                                    'checklist_version' => 'vID.' . $checklist['id'],
                                    'location_address' => $location['address_1'],
                                ];*/
    
                                //Insertar Log
                                $insertAudit_LogValues = [
                                    'audit_id' => $newAuditID,
                                    'user_id' => -1,
                                    'category' => $logCategory,
                                    'name' => 'Audit Created',
                                    'details' => 'Audit Created trough Sync Ace Process',
                                    'date' => date('Y-m-d H:i:s'),
                                ];
                                Audit_LogModel::insertAudit_Log($insertAudit_LogValues);
                                $isAudit = AuditoriaModel::getAuditAce([
                            'id', 'status', 'round_id', 'date_visit', 'checklist_id', 'additional_question_id', 'ace_folio', 'ace_id', 'auditor_name'], "location_id=$location[id] AND ace_id=$row[Auditoria] AND ace_folio=$row[Folio]")[0];
                                $data['audita_data'][$x]['Estatus'] = str_replace(array('Pending', 'Completed', 'In Process', 'Closed'), 
                                    array('Pendiente', 'Finalizada', 'En Proceso', 'Cerrada'), 
                                    $isAudit['status']);
                                $data['audita_data'][$x]['id_visit'] = intval($isAudit['id']);
                                $data['audita_data'][$x]['nombre_auditor'] = $isAudit['auditor_name'];
                                $data['audita_data'][$x]['Numero_Tienda'] = $row['Numero_Tienda'];
                                $data['audita_data'][$x]['ID_Tienda'] = $row['ID_Tienda'];
                                $data['audita_data'][$x]['E_Mail'] = $row['E_Mail'];
                                $data['audita_data'][$x]['Tipo'] = $row['Tipo'];
                                $data['audita_data'][$x]['Foranea'] = $row['Foranea'];
                                $data['audita_data'][$x]['Fecha_Inicial'] = $row['Fecha_Inicial'];
                                $data['audita_data'][$x]['Fecha_Final'] = $row['Fecha_Final'];
                                $data['audita_data'][$x]['Pais'] = $row['Pais'];
                                $data['audita_data'][$x]['Folio'] = $row['Folio'];
                                $data['audita_data'][$x]['Auditoria'] = $row['Auditoria'];
                                $data['audita_data'][$x]['RFuturo'] = $row['RFuturo'];
                                $data['audita_data'][$x]['people_id_asignacion'] = $row['people_id_asignacion'];
                                $data['audita_data'][$x]['flujo_reprogramada'] = $row['flujo_reprogramada'];
                                $data['audita_data'][$x]['id_auditor'] = $row['id_auditor'];
                                $data['audita_data'][$x]['emailAuditor'] = $row['emailAuditor'];
                                $data['audita_data'][$x]['periodo'] = $row['periodo'];
                                $data['audita_data'][$x]['checklist_version'] = 'vID.' . $checklist['id'];
                                $data['audita_data'][$x]['audit_definition'] = 'vID.' . $additional_question['id'];
                                $data['audita_data'][$x]['tienda_direccion'] = $location['address_1'];
                                $data['audita_data'][$x]['round'] = $roundInfo['name'];
                                $data['audita_data'][$x]['roundName'] = $arrTipos[$row['Tipo']];
                                $roundJ = RoundModel::getRound([], "id = ".$isAudit['round_id'])[0];
                                $pais = CountryModel::getCountry([], "id = ".$roundJ['country_id'])[0];

                                //Hacer algo para las ya asignadas
                                $summary['current'][$roundJ['type']." ".$pais['name']]++;
                            }
                        }
                        //$data['summary'] = $summary;
                    }
                }
            } else {
                $data['response'] = "FAIL - Invalid Action!";
            }    
        }
        //carga y return ($data) a la vista
        //die(var_dump($data));
        $this->views->getView($this, "sync", $data);
    }

    public function updateAce(){
        $raw_input = file_get_contents("php://input");
        $_POST = json_decode($raw_input, true);
        $audit = $_POST['audita_data'][0];
        //die(var_dump($audit));
        $audit['marcaRef'] = $_POST['marcaRef'];
        $nombreRound = RoundModel::getRoundInfo($audit['periodo'], NULL);

        $roundNameToFind = $nombreRound['nm'];
        $country = $audit['Pais'];
        if ($country == 'MEX') $country = 'MEXICO';
        $tmpRound = RoundModel::getRoundAce(intval($audit['Tipo']), addslashes($roundNameToFind));
        //die(var_dump($tmpRound));
        /*----------  Creación del Round si no existe  ----------*/
        if (count($tmpRound)<1) {
            $tipo = "";
            switch($audit['Tipo']){
                case 1:
                    $tipo = 'Standard';
                    break;
                case 11:
                    $tipo = 'Re-Audit';
                    break;
                case 18:
                    $tipo = 'Re auditoria 2da.';
                    break;
                case 23:
                    $tipo = 'Calibration Audit';
                    break;
                case 28:
                    $tipo = 'Re auditoria 3ra.';
                    break;
                case 29:
                    $tipo = 'Re auditoria 4ta.';
                    break;
            }
            $args = array(
                "brand_id" => 1,
                "country_id" => 1,
                "name" => $roundNameToFind,
                "type" => $tipo,
                "date_start" => $nombreRound['desde']
            );
            $rsRound = RoundModel::insertRound($args);
            //die(var_dump($rsRound));
            // EJECUTAR NUEVAMENTE EL QUERY DE SELECT PARA TRAER LOS DATOS FINALES
            $tmpRound = RoundModel::getRoundAce(intval($audit['Tipo']), addslashes($roundNameToFind));
        }
        $round = $tmpRound[0];
        //die(var_dump(addslashes($roundNameToFind)));
        if( (in_array($audit['Tipo'],array(1,11,18,28,29,23)) && $audit['marcaRef']=='DLP')){
            $col = ['id', 'status', 'checklist_id', 'scoring_id'];
            $tmpAudita = AuditoriaModel::getAuditAce($col, "ace_folio='".$audit['Folio']."'");
            if(!empty($tmpAudita)){
                $auditsOperativas = $tmpAudita[0];
                if($auditsOperativas['status']=='Pending'){
                    $local_foranea = $audit['Foranea']? 'Foranea' : 'Local';
                    $monthName = date('F', strtotime($audit['periodo'] . '-01'));
                    $args = array(
                        "auditor_email" => $audit['emailAuditor'],
                        "auditor_name" => $audit['nombre_auditor'],
                        "local_foranea" => $local_foranea,
                        "round_id" => $round['id_round'],
                        "checklist_id" => $auditsOperativas['checklist_id'],
                        "scoring_id" => $auditsOperativas['scoring_id'],
                        "announced_date" => $audit['Fecha_Inicial']
                    );
                    $update = AuditoriaModel::updateAudit($args, "id = ".$auditsOperativas['id']);
                    //var_dump($update);
                    if($update){
                        $response = [
                            'Estatus' => 'Visita actualizada exitosamente',
                            'Checklist_version' => 'vID.'.$auditsOperativas['checklist_id'],
                            'Checklist_definition' => 'vID.'.$auditsOperativas['checklist_id'],
                            'Round' => $round['round'],
                            'Round_name' => $round['round'],
                            'Tipo_visita' => $round['type']
                        ];
                    } else{
                        // En caso de error al actualizar
                        $response = ['Estatus' => 'Error al actualizar'];
                    }
                } else{
                    // En caso de la visita no estar en pendiente
                    $response = ['Estatus' => 'Visita fuera de estatus'];
                }
            }
        }else{
            // Al no estar acorde el servicio a la marca manda error a people
            $response = ['Estatus' => 'Servicio no soportado'];
        }
        echo json_encode($response);
    }

    public function deleteAce(){
        header('Content-Type: application/json; charset=utf-8');
        $raw_input = file_get_contents("php://input");
        $data = json_decode($raw_input, true);
        // Validación fuerte
        if (!$data || !isset($data['Folio']) || !is_array($data['Folio'])) {
            echo json_encode(['Estatus' => 'No se encontraron folios']);
            return;
        }
        $folios = $data['Folio'];
        $response = [];
        foreach ($folios as $folio) {
            $folio = trim((string)$folio);
            if ($folio === '') {
                $response[] = ['Estatus' => 'Folio inválido', 'folio' => $folio];
                continue;
            }
            $result = AuditoriaModel::getAuditAce([], "ace_folio = '$folio'");
            $response[] = ['Estatus' => 'error', 'folio' => $folio];
            if (is_array($result) && count($result) > 0) {
                if($result[0]['status'] == 'Pending'){
                    $args = array(
                        "status" => "Deleted!"
                    );
                    $ok = AuditoriaModel::updateAudit($args, "ace_folio=".$folio);
                    if ($ok) {
                        $response[] = ['Estatus' => 'Visita eliminada exitosamente', 'folio' => $folio];
                    } else {
                        $response[] = ['Estatus' => 'Error al eliminar', 'folio' => $folio];
                    }
                }else{
                    $response[] = ['Estatus' => 'Visita fuera de estatus', 'folio' => $folio];
                }
            } else {
                $response[] = ['Estatus' => 'Folio not exists in people', 'folio' => $folio];
            }
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    //checklist_definition invocado por People (via Helmut)
    public function getChecklist()
    {
        $post = json_decode(file_get_contents('php://input'), 0);

        $brand = $post->brand;
        $tmp = explode(".", $post->checklist_version); //ej. vID.1
        $checklist_id = $tmp[1];
        $lang = strtolower($post->lang);
        global $fnT;
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
        global $fnT;

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
            require_once 'Models/UsuariosModel.php';

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
            $country = CountryModel::getCountry(['name', 'language'], "id=".$location['country_id'])[0];
            $brand = BrandModel::getBrand(['id', 'prefix'], "id='$round[brand_id]'")[0];
            $esPrueba = false;
            if(in_array($round['type'],['Calibration Audit'])) $esPrueba=true;
            $to = UsuariosModel::getTo(1, $isAudit['location_id'], $esPrueba, $location['country_id']);

            /*if($round['type'] != 'Calibration Audit'){
                $locationMails = getLocationEmails(['Fanchisee' , 'Ops Director' , 'Ops Leader' , 'Area Manager' , 'Store Manager'], $isAudit['location_id']);
            }*/
            $AdminMails = getLocationEmails(['admin arguilea'], 0);
            //$recipients = emailFilter("{$isAudit['manager_email']},$locationMails,$AdminMails");
            
            /*function esEspanol($arr){
                foreach($arr as $key){
                    if(in_array($key, [1,10,18,33,35,36])){
                    return true;
                    }
                } 
                return false;
            }*/

            if($visit_status == 'Closed'){
                //Limpiar los Scores
                ScoringModel::closedScore($isAudit['id']);
                
                if(esEspanol([$location['country_id']])){
                    $fnT = translate('esp');
                }else{
                    $fnT = translate('eng');
                }
                $titulo=$fnT("Visita fechada");
                    $contentTitle = $fnT("Informamos que a visita foi realizada, mas o auditor encontrou a loja fechada.");
                    $contenido = '<p>'.$fnT('Rodada').': <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             '.$fnT('Nome da loja').': <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             '.$fnT('Data da auditoria').': <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             '.$fnT('Nome do auditor').': <b>' . $isAudit['auditor_name'] . '</b></p>';

                $data_closed_visit = [
                    'asunto'            => "$appdata[brand] #".$location['number']." ($country[name]) @ $titulo",
                    'email'             => $to,
                    'audit_id'          => $isAudit['id'],
                    'country'           => $country['country_id'],
                    'titulo'            => $titulo,
                    'content_title'     => $contentTitle,
                    'content_message'   => $contenido
                ];
                sendEmail($data_closed_visit, "audit_closed");                
            } else{
                //Cálculo de Scores
                ScoringModel::setScore($isAudit['id'], $isAudit['scoring_id']);

                if(esEspanol([$location['country_id']])){
                    $fnT = translate('esp');
                    $url_audit_report = getURLReport($isAudit['id'], $isAudit['report_layout_id'], 'esp');
                }else{
                    $fnT = translate('eng');
                    $url_audit_report = getURLReport($isAudit['id'], $isAudit['report_layout_id'], 'eng');
                }

                $titulo = $fnT('Resultados preliminares');
                    $contentTitle = $fnT("Os resultados preliminares estão disponíveis no seguinte link");
                    $contenido = '<p>'.$fnT('Rodada').': <b>'.$round['name'].' / '.$round['type'].'</b><br />
                                             '.$fnT('Nome da loja').': <b>('.$country['name'].') '.$location['name'].' #'.$location['number'].'</b><br />
                                             '.$fnT('Data da auditoria').': <b>' . date('F d Y, H:i', strtotime($isAudit['date_visit'])) . '</b><br />
                                             '.$fnT('Status da auditoria').': <b>' . $isAudit['visit_status'] . '</b><br />
                                             '.$fnT('Nome do auditor').': <b>' . $isAudit['auditor_name'] . '</b></p>';

                $data_preliminary_email = [
                    'asunto'            => "$appdata[brand] #".$location['number']." ($country[name]) @ $titulo",
                    'email'             => $to,
                    'audit_id'          => $isAudit['id'],
                    'country'           => $location['country_id'],
                    'titulo'            => $titulo,
                    'content_title'     => $contentTitle,
                    'content_message'   => $contenido,
                    'content_url'       => '<a href="' . $url_audit_report . '">' . $url_audit_report .'</a>'
                ];
                sendEmail($data_preliminary_email, "audit_preliminary_results");
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
        echo '<div style="border:solid 1px #CCC; padding:10px; background:#FBFBFB;"><h1>Date Of Visit -- CHURCHS TEXAS CHICKEN</h1>';
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
