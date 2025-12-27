<?php 
    headerTemplate($data);
    $canUpdate = ($data['permision']['u'] || isMySelfEvaluation($_GET['id'])) && ($data['status'] != 'Completed' && $data['visit_status'] == 'Visited');
    getModal('modalAnswer', ['audit_id' => $_GET['id'], 'type' => $data['type'], 'update' => $canUpdate]);
    global $fnT;
    $arrLostQuestion = [];
    $arrLostSection = [];
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-list-ol" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Review the particular content of an audit')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <? headerTemplateAudits($_GET['id'], 'Checklist') ?>
    <div class="row">
        <div class="col-lg-3 my-1">
            <div class="tile">
                <div class="tile-body">
                    <ul class="app-menu pb-0">
                        
                        <? 
                        foreach($data['section'] as $item): ?>
                            <li class="app-menu__item section-items flex-column align-items-start cr-pointer success" id="section<?=$item['section_number']?>" onclick="filterSection(<?=$item['section_number']?>)">
                                <!-- <span class="badge badge-light"><?=$item['main_section']?></span> -->
                              
                                <a class="text-primary"><?=$fnT($item['section_name'])?></a>
                            </li>
                        <? endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="tile">
                <div class="tile-body">
                    <div id="accordion-questions">
                        <div class="card border-0">
                            <? foreach($data['question'] as $q): ?>
                                <div data-snumber="<?=$q['snumber']?>" class="question-item">
                                    <div class="card-header d-flex justify-content-between">
                                        <span data-na="<?=$q['na']?>" <?= $q['na'] && !$canUpdate? 'title="' . $fnT('This question is disabled') . '"' : "onclick=toggleQuestion('{$q['prefix']}')"  ?> style="cursor: pointer" id="bpicklist<?=$q['prefix']?>">
                                            <span class="badge badge-secondary"><?=$q['prefix']?></span> - 
                                            <? if(!empty($q['priority'])): ?>
                                                <b class="<?=$q['priority']=='Critical'? 'text-danger' : '' ?>"><?=$fnT($q['priority'])?>:</b>&nbsp;
                                            <? endif ?>
                                            <?=$q['question']?>
                                        </span>
                                        <button type="button" class="btn ml-2 <?=$q['na']? 'btn-dark' : 'btn-success'?>" style="height: 35px" id="points<?=$q['prefix']?>">
                                            <b><?=$q['points']?></b><span>pts</span>
                                        </button>
                                    </div>
                                    <div id="cpicklist<?=$q['prefix']?>" class="collapse">
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <? foreach($q['picklist'] as $p): ?>
                                                    <li class="list-group-item list-group-item-action d-flex justify-content-between cr-pointer" onclick="openOpportunity(<?=$p['id']?>, '<?=$q['prefix']?>', '<?=$q['snumber']?>')">
                                                        <p>
                                                            <? if(!empty($p['priority'])): ?>
                                                                <b class="<?=$p['priority']=='Critical'? 'text-danger' : '' ?>"><?=$fnT($p['priority'])?>:</b>&nbsp;
                                                            <? endif ?>
                                                            <?=$p['picklist_item']?>
                                                        </p>
                                                        <? if($p['has_opp'] > 0): ?>
                                                            <i id="picklist<?=$p['id']?>" class="fa fa-times float-right ml-2 mb-2 text-danger"></i>
                                                        <?
                                                            array_push($arrLostQuestion, $q['prefix']);
                                                            array_push($arrLostSection, $q['snumber']);
                                                            else:
                                                        ?>
                                                            <i id="picklist<?=$p['id']?>" class="fa fa-check ml-2 mb-2 text-success"></i>
                                                        <? endif; ?>
                                                    </li>
                                                <? endforeach ?>
                                                <? if($canUpdate && ON_NA): ?>
                                                    <li class="list-group-item list-group-item-action list-group-item-warning cr-pointer" onclick="sendInsertNA(<?=$q['snumber']?>, '<?=$q['prefix']?>', <?=$q['points']?>)">
                                                        <b><?=$fnT('Mark question as not applicable')?></b>
                                                    </li>
                                                <? endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <? endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>
<script>
    filterSection('<?=$data['section'][0]['section_number']?>');
    
    const arrLostQuestion = ['<?=implode(array_filter($arrLostQuestion), "','")?>'];
    arrLostQuestion.forEach(item => $('#points'+item).removeClass('btn-success').addClass('btn-danger'))
    
    const arrLostSection = [<?=implode(array_filter($arrLostSection), ",")?>];
    arrLostSection.forEach(item => $('#section'+item).removeClass('success').addClass('danger'));

    const audit_id = <?=$_GET['id']?>;

    editRestricted = <?=$canUpdate? 'true' : 'false'?>;
</script>