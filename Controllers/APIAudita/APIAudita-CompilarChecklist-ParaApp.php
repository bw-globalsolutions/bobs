<?php
//Por @Ben Sept. 2022.
//Script embebido en el controller APIAudita.php :: getchecklist().
//Este script es para Compilar y guardar en disco el json de Checklist para la App
//y asi no sea necesario invocar los modelos cada vez que el app solicite el json

//NOTAS:
//1. Armar el Json segun estructura que requiere el App
//2. Salvarlo en un archivo fisico con extension .json
//4. Actualizar la tabla checklist.compiled_json_for_app con las diferentes compilaciones (archivo fisico) para cada idioma.
//3. El Controler APIAudita.php :: getchecklist() si detecta que ya existe la compilacion, entonces obtienbe el json del arhivo

$jsonApp = [];
$jsonApp['info'] = [
    'Message' => 'QUERY SUCCESS',
    'ReceivedParams' => [
        'brand' => $post->brand,
        'checklist_version' => $post->checklist_version,
        'token' => $post->token,
        'lang' => $post->lang
    ]
];

$jsonApp['result']['DESCRIPTION'] = [
    'brand' => $brand,
    'version' => "vID." . $checklist['id'],
    'released' => $checklist['date_start'],
    'owner' => 'Operaciones',
    'comments' => $checklist['name'],
];

$jsonApp['result']['CHECKLISTINFO'] = [
    'general_scores' => [],
    'general_sections' => [],
    'count_general_sections' => '',
    'questions' => [],
];

//Armar Manualmente el Scoring para tal o cual version
//Cada nuevo checklist, revisar esta parte manualmente.
$jsonApp['result']['CHECKLISTINFO']['general_scores'] = [
    ['title' => 'SEGURIDAD DE ALIMENTOS',
     'title_esp' => 'SEGURIDAD DE ALIMENTOS',
     'how_to' => [
        'datatype' => 'FLOAT(5,2)',
        'unit' => 'points',
        'formula' => 
            'SUM_SECTIONS_TARGET_POINTS-SUM_SECTIONS_EARNED_POINTS',
            'variables' => [
                'SUM_SECTIONS_TARGET_POINTS' => ['active'=>'1', 'from'=>'1', 'to'=>'5'],
                'SUM_SECTIONS_EARNED_POINTS' => ['active'=>'1', 'from'=>'1', 'to'=>'5']]
        ]
    ],
    ['title' => 'LIMPIEZA',
     'title_esp' => 'LIMPIEZA',
     'how_to' => [
        'datatype' => 'FLOAT(5,2)',
        'unit' => 'points',
        'formula' => 
            'SUM_SECTIONS_TARGET_POINTS-SUM_SECTIONS_EARNED_POINTS',
            'variables' => [
                'SUM_SECTIONS_TARGET_POINTS' => ['active'=>'1', 'from'=>'6', 'to'=>'9'],
                'SUM_SECTIONS_EARNED_POINTS' => ['active'=>'1', 'from'=>'6', 'to'=>'9']]
        ]
    ],
    ['title' => 'MANTENIMIENTO',
     'title_esp' => 'MANTENIMIENTO',
     'how_to' => [
        'datatype' => 'FLOAT(5,2)',
        'unit' => 'points',
        'formula' => 
            'SUM_SECTIONS_TARGET_POINTS-SUM_SECTIONS_EARNED_POINTS',
            'variables' => [
                'SUM_SECTIONS_TARGET_POINTS' => ['active'=>'1', 'from'=>'10', 'to'=>'15'],
                'SUM_SECTIONS_EARNED_POINTS' => ['active'=>'1', 'from'=>'10', 'to'=>'15']]
        ]
    ]
];

//Armar las Secciones del Checklist
$sections = Checklist_ItemModel::getChecklistSection("checklist_id=$checklist[id] AND type='Question'");
foreach ($sections as $r) {
    $jsonApp['result']['CHECKLISTINFO']['general_sections'][] = [
        'section_number' => $r['section_number'],
        'section_prefix' => NULL,
        'title' => "(" . $r['main_section'] . ") " . $r['section_name'],
        'title_esp' => $r['section_name'],
        'target_points' => $r['tot_points'],
        'count_questions' => (int)$r['tot_questions'],
    ];
}
$jsonApp['result']['CHECKLISTINFO']['count_general_sections'] = count($sections);

//Conocer Picklist y Preguntas del Checklist
$items = Checklist_ItemModel::getChecklistItem([], "checklist_id=$checklist[id]");
$questions = [];
$picklists = [];
foreach($items as $r){
    if($r['type']=='Question'){
        $tmp_questionPts = $r['points'];
        $questions[$r['question_prefix']] = [
            "id_question" => $r['id'],
            'section_number' => $r['section_number'],
            'section_name' => "(" . $r['main_section'] . ") " . $r['section_name'],
            'type' => $r['type'],
            'question_prefix1' => $r['question_prefix'],
            'question_prefix2' => NULL,
            'title' => $r['eng'],
            'title_esp' => $r[$lang],
            'target_points' => $r['points'],
            'max_selected_picklists' => '--se define lineas mas abajo--',
            'audited_area' => $r['area'],
            'request_notes1' => false,
            'request_notes2' => false,
            'not_applicable_option' => true,
            'count_picklists' => '--se define lineas mas abajo--',
            'picklists' => '',
        ];

    } else if($r['type']=='Picklist'){
        //preparar las posibles respuestas
        $tmp = explode("|", $r[$lang."_answer"]);
        $tmp_answers = [];
        foreach($tmp as $a) $tmp_answers[] = ['title'=>$a, 'title_esp'=>$a, 'request'=>NULL];
        $answer = [];
        $answer[] = ['title'=>'YES', 'title_esp'=>'YES', 'requests'=>NULL];
        $answer[] = ['title'=>'NO', 'title_esp'=>'NO', 'requests'=> [
            ['title' => 'Select Answers',
            'title_esp' => 'Select Answers',
            'request_type' => 'CHECKBOX_OPTIONS',
            'required' => true,
            'count_options' => count($tmp_answers),
            'OPTIONS' => $tmp_answers],
            ['title'=>'Auditor Comments', 'title_esp'=>'Auditor Comments', 'request_type'=>'FREE_TEXT', 'required'=>false, 'placeholder'=>'Here auditor comments...'],
            ['title'=>'Submit a picture', 'title_esp'=>'Submit a picture', 'request_type'=>'UPLOAD_PICTURES', 'request_notes1'=>false, 'request_notes2'=>false, 'required'=>false]]
        ];
        //
        $picklists[$r['question_prefix']][] = [
            'id_item' => $r['id'],
            'title' => $r['eng'],
            'title_esp' => $r[$lang],
            'type' => $r['type'],
            'picklist_prefix1' => $r['picklist_prefix'],
            'picklist_prefix2' => $r['question_prefix'],
            'deducted_points' => $tmp_questionPts == '0'? null : (empty($r['points_partial'])? $tmp_questionPts : $r['points_partial']),
            'deducted_points_ica' => 'false',
            'request_notes1' => false,
            'request_notes2' => false,
            'copy_to' => NULL,
            'answers' => $answer,
            'cero_protocol_ss' => false,
            'max_pictures' => NULL,
        ];
    }
}
//Armar las Questions
foreach($questions as $prefix => $r){
    $r['picklists'] = $picklists[$prefix];
    $r['max_selected_picklists'] = count($picklists[$prefix]);
    $r['count_picklists'] = count($picklists[$prefix]); 
    $jsonApp['result']['CHECKLISTINFO']['questions'][] = $r;
}

$compiled = json_encode($jsonApp);
