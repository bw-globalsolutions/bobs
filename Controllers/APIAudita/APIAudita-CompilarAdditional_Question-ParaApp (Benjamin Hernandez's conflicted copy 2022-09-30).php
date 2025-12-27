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

require_once 'Config/Translate.php';

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

//Conocer las Audited Areas del Checklist
$areas = [];
$rs = Checklist_ItemModel::getChecklistItem(['area'], "checklist_id=1 AND area IS NOT NULL");
if(count($rs)) foreach($rs as $r){
    $areas[] = ['title'=>$r['area'], 'title_esp'=>$r['area'], 'requests'=>NULL];
} else {
    $areas[] = ['title'=>'-Sin-Areas-', 'title_esp'=>'-Sin-Areas-', 'requests'=>NULL];
}

//Informacion General
$jsonApp['result']['REQUIRED_GENERAL_INFORMATION'] = [
    'requests' => [
        ['title' => 'Visit Status', 'title_esp' => translate_live($lang, 'Visit Status'), 'request_type'=>'SELECT_OPTIONS', 'required'=>true,
         'OPTIONS' => [
            ['title' => 'Visited', 'title_esp' => translate_live($lang, 'Visited'), 'request'=>NULL],
            ['title' => 'Closed', 'title_esp' => translate_live($lang, 'Closed'), 'request'=>[
                ['title'=>'Auditor Comments', 'title_esp'=>translate_live($lang, 'Auditor Comments'), 'request_type'=>'FREE_TEXT', 'required'=>true, 'placeholder'=>translate_live($lang, 'Here auditor comments...')],
                ['title'=>'Submit a picture', 'title_esp'=>translate_live($lang, 'Submit a picture'), 'request_type'=>'UPLOAD_PICTURES', 'request_notes1'=>false, 'request_notes2'=>false, 'required'=>false]
            ]]],
        ],
        ['title' => 'Date of Visit', 'title_esp' => translate_live($lang, 'Date of Visit'), 'request_type'=>'DATE', 'required'=>true, 'placeholder'=>'YYYY-MM-DD'],
        ['title' => 'Start Time', 'title_esp' => translate_live($lang, 'Start Time'), 'request_type'=>'TIME', 'required'=>true, 'placeholder'=>'HH:MM:SS'],
        ['title' => 'End Time', 'title_esp' => translate_live($lang, 'End Time'), 'request_type'=>'TIME', 'required'=>true, 'placeholder'=>'HH:MM:SS'],
        ['title' => 'Day part', 'title_esp' => translate_live($lang, 'Day part'), 'request_type'=>'SELECT_OPTIONS', 'required'=>true, 
         'OPTIONS'=>[
            ['title'=>'Lunch', 'title_esp'=>translate_live($lang, 'Lunch'), 'requests'=>NULL],
            ['title'=>'Breakfast', 'title_esp'=>translate_live($lang, 'Breakfast'), 'requests'=>NULL],
            ['title'=>'Dinner', 'title_esp'=>translate_live($lang, 'Dinner'), 'requests'=>NULL]
            ]],
        ['title' => 'Restaurant E-mail', 'title_esp' => translate_live($lang, 'Restaurant E-mail'), 'request_type'=>'FREE_TEXT', 'required'=>true, 'placeholder'=>'mail@mail.com'],
        ['title' => 'General Manager Name', 'title_esp' => translate_live($lang, 'General Manager Name'), 'request_type'=>'FREE_TEXT', 'required'=>true, 'placeholder'=>'John Doe'],
        ['title' => 'Audited Areas', 'title_esp' => translate_live($lang, 'Audited Areas'), 'request_type'=>'CHECKBOX_OPTIONS', 'required'=>true, 'OPTIONS'=>$areas],
    ]
];

//Fotos Adicionales
$additional_photos = [];
$rs = Additional_Question_ItemModel::getAdditional_Question_Item([], "additional_question_id=$additional_question[id] AND type='General Pictures'");
foreach($rs as $r){
    $additional_photos[] = [
        'title' => $r['eng'],
        'title_esp' => $r[$lang],
        'request_type' => $r['input_type'],
        'request_notes1' => false,
        'request_notes2' => false,
        'required' => false,
    ];
}
$jsonApp['result']['REQUIRED_GENERAL_PICTURES']['requests'] = $additional_photos;

//Preguntas Adicionales
$addi_questions = [];
$rs = Additional_Question_ItemModel::getAdditional_Question_Item([], "additional_question_id=$additional_question[id] AND type='Additional Questions'");
foreach($rs as $r){

    $options = [];
    if($r[$lang.'_answer']) {
        $tmp = explode("|", $r[$lang.'_answer']);
        foreach($tmp as $a){
            $options[] = ['title'=>$a, 'title_esp'=>$a, 'requests'=>NULL];
        }
    }

    $addi_questions[] = [
        'title' => $r['eng'],
        'title_esp' => $r[$lang],
        'request_type' => $r['input_type'],
        'required' => false,
        'request_pictures' => true,
        'max_pictures' => 5,
        'request_notes1' => false,
        'request_notes2' => false,
        'OPTIONS' => $options
    ];
}
$jsonApp['result']['REQUIRED_ADDITIONAL_QUESTIONS'] = [];


$compiled = json_encode($jsonApp);
