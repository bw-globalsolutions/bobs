<?php
//Insertar Log
$insertAudit_LogValues = [
    'audit_id' => $isAudit['id'],
    'user_id' => -1,
    'category' => 'App',
    'name' => 'Audit Received',
    'details' => 'Audit Received trough receiveInfo() and all previous info was deleted',
    'date' => date('Y-m-d H:i:s'),
];
Audit_LogModel::insertAudit_Log($insertAudit_LogValues);

//Info General
$visit_status = $appdata['json_data']['generalInfo']['visit_status'];
$updateAuditValues = [
    'date_visit' => $appdata['json_data']['generalInfo']['start_time'],
    'date_visit_end' => $appdata['json_data']['generalInfo']['end_time'],
    'daypart' => $appdata['json_data']['generalInfo']['day_part'],
    'manager_email' => $appdata['json_data']['generalInfo']['restaurant_e-mail'],
    'manager_name' => $appdata['json_data']['generalInfo']['general_manager_name'],
    'manager_signature' => $appdata['json_data']['generalInfo']['general_manager_signature'],
    'sos_times' => json_encode($appdata['json_data']['generalInfo']['sosTimes']),
    'audited_areas' => json_encode($appdata['json_data']['generalInfo']['audited_areas']),

    //estatus de la visita
    'visit_status' => $visit_status,

    //En caso de Ser cerrada
    'visit_comment' => ($visit_status=='Closed' ? $appdata['json_data']['generalInfo']['if_not_visited_cause']['auditor_comments'] : NULL),
];
$rs = AuditoriaModel::updateAudit($updateAuditValues, "id=$isAudit[id]");

//Oportunidades
foreach ($appdata['json_data']['checklistInfo'] as $item) {
    if (!empty($item['picklists'])) {

        $lostPoints = Audit_PointModel::getLostPoint(array_column($item['picklists'], 'id_item'));
        $lostPoints = is_null($lostPoints)? $item['target_points'] : $lostPoints;

        //Salvar Points descontados
        $insertAudit_PointValues = [
            'audit_id' => $isAudit['id'],
            'section_number' => $item['section_number'],
            'question_prefix' => $item['question_prefix1'],
            'gained_point' => ($item['target_points'] - $lostPoints),
            'target_point' => $item['target_points'],
            'lost_point' => $lostPoints,
        ];
        $audit_point_id = Audit_PointModel::insertAudit_Point($insertAudit_PointValues);

        //Salvar Picklist
        foreach ($item['picklists'] as $opp) {
            $insertAudit_OppValues = [
                'audit_id' => $isAudit['id'],
                'checklist_item_id' => $opp['id_item'],
                'audit_point_id' => $audit_point_id,
                'auditor_answer' => substr($opp['auditor_comments'], 0, -1),
                'auditor_comment' => $opp['auditor_comments2'],
            ];
            $audit_opp_id = Audit_OppModel::insertAudit_Opp($insertAudit_OppValues);

            //Salvar Fotos
            foreach ($opp['pictures'] as $pic) {
                $insertAudit_FileValues = [
                    'audit_id' => $isAudit['id'],
                    'reference_id' => $audit_opp_id,
                    'type' => 'Opportunity',
                    'name' => "Opportunity for $opp[picklist_prefix2]-$opp[picklist_prefix1]",
                    'url' => $pic['location'],
                ];
                Audit_FileModel::insertAudit_File($insertAudit_FileValues);
            }
        }
    }
}

//General Pictures
foreach ($appdata['json_data']['generalPictures'] as $item) {
    $insertAudit_FileValues = [
        'audit_id' => $isAudit['id'],
        'type' => 'General Pictures',
        'name' => $item['title'],
        'url' => $item['location'],
    ];
    Audit_FileModel::insertAudit_File($insertAudit_FileValues);
}

//Fotos en caso de ser Cerrada
if(is_array($appdata['json_data']['generalInfo']['if_not_visited_cause']) && count($appdata['json_data']['generalInfo']['if_not_visited_cause']['pictures'])){
    foreach($appdata['json_data']['generalInfo']['if_not_visited_cause']['pictures'] as $item){
        $insertAudit_FileValues = [
            'audit_id' => $isAudit['id'],
            'type' => 'General Pictures',
            'name' => 'Store Closed',
            'url' => $pic['location'],
        ];
        Audit_FileModel::insertAudit_File($insertAudit_FileValues);        
    }
}

//Additional Qeestions
foreach ($appdata['json_data']['additionalQuestions'] as $item) {

    $insertAudit_Addi_QuestionValues = [
        'audit_id' => $isAudit['id'],
        'additional_question_item_id' => $item['id_item'],
        'name' => $item['title'],
        'answer' => $item['answer'],
        'notes' => $item['notes1'],
    ];
    $audit_addi_question_id = Audit_Addi_QuestionModel::insertAudit_Addi_Question($insertAudit_Addi_QuestionValues);

    //Salvar Fotos
    foreach ($item['pictures'] as $pic) {
        $insertAudit_FileValues = [
            'audit_id' => $isAudit['id'],
            'reference_id' => $audit_addi_question_id,
            'type' => 'Additional Questions',
            'name' => $item['title'],
            'url' => $pic['location'],
        ];
        Audit_FileModel::insertAudit_File($insertAudit_FileValues);
    }
}

//Actualizar variable $isAudit
$isAudit = AuditoriaModel::getAudit([], "id=$isAudit[id]")[0];