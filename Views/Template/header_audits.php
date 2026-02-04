<?  
    global $fnT;
    $isClosed = $data['visit_status'] != 'Visited'? 'disabled' : 0;
    $permissions = ($_SESSION['userData']['permission']['Auditorias']['u'] or isMySelfEvaluation($data['id']));

    if($data['status']=='Pending'){
        $estatus = 'Pendente';
    }else if($data['status']=='In Process'){
        $estatus = 'Em andamento';
    }else if($data['status']=='Completed'){
        $estatus = 'Concluída';
    }else if($data['status']=='Deleted!'){
        $estatus = 'Excluída!';
    }

    if($data['type']=='Self-Evaluation'){
        $tipo = 'Autoavaliação';
    }else if($data['type']=='Standard'){
        $tipo = 'Padrão';
    }else if($data['type']=='Calibration-Audit'){
        $tipo = 'Auditoria de Calibração';
    }else if($data['type']=='Training-visits'){
        $tipo = 'visitas de treinamento';
    }
?>
<?php if($_SESSION['userData']['id'] == 1){
        //dep($_SESSION['userData']);
        //dep($data);
        //dep($_SESSION['permisosMod']);
    }?>
<div class="tile">
    <div class="tile-body">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <img class="w-100 of-cover" src="<?=$visit_img?>" style="max-height: 350px;" alt="">
                            </div>
                            <div class="col-lg-9">
                                <p class="small mb-0 text-secondary"><?=$data['round_name']?></p>
                                <p class="h5">
                                    <span class="badge badge-info"><?=$data['brand_prefix']?></span>
                                    <b class="text-success"> <?=$data['location_number']?> - <?=$data['location_name']?></b></span>
                                </p>
                                <p class="mb-1"><?=$fnT('Status')?>: <b id="audit_status" class="etlbl"><?=$estatus?></b></p>
                                <p class="mb-1"><?=$fnT('Data da visita')?>: <b><?=$data['date_visit']?? $fnT('Sem registro')?></b></p>
                                                                
                                <p class="mb-3"><?=$fnT('Tipo')?>: <b><?=$tipo?></b></p>
                                <p class="small text-secondary" style="display:flex; align-items:center;">
                                    <b class="lbl-s5" style="padding: 5px 15px; cursor:pointer;" onclick="copiarAlPortapapeles('<?=base_url()?>/audits/audit?id=<?=$_GET['id']?>')">ID: <?=$data['id']?></b> — CHK<?=$data['checklist_id']?> — SCG<?=$data['scoring_id']?></small>
                                </p>
                                <div class="mb-1">
                                    <div class="btn-group mb-1" role="group">
                                        <button type="button" class="btn btn-info btn-sm">
                                            <?=$fnT('Segurança dos alimentos')?>: <span id="score-critics"><?=$data['score']['FootSafety']?></span>
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm">
                                            <?=$fnT('Padrões da marca')?>: <span id="score-nocritics"><?=$data['score']['OperationsE']?></span>
                                        </button>
                                    </div><br>
                                    <div class="btn-group mb-1" role="group">
                                        <!-- <button type="button" class="btn btn-success btn-sm">
                                            <?=$fnT('Verde')?>: <span id="score-green"><?=$data['score']['Verdes']?></span>
                                        </button> -->
                                        <button type="button" style="background-color: var(--color5); color:var(--color4); border-radius:var(--radius); padding: 5px 15px; border:none;">
                                            <?=$fnT('Pontuação geral')?>: <span id="score-yellow"><?=$data['score']['OverallScore']?></span>
                                        </button>
                                        <button type="button" style="background-color: <?=$data['score']['color']?>; color:var(--color4); border-radius:var(--radius); padding: 5px 15px; border:none;">
                                            <span id="score-red"><?=$data['score']['Letra']?></span>
                                        </button>
                                    </div><br>
                                    <div class="btn-group mb-1" role="group">
                                        <button type="button" class="btn btn-danger btn-sm">
                                            <?=$fnT('Regra de diamante')?>: <span id="score-autofail"><?=$data['score']['AutoFail']?></span>
                                        </button>
                                    </div>
                                    <!-- <div class="btn-group mb-1" role="group">
                                        <button type="button" class="btn btn-info btn-sm">
                                            <?=$fnT('Maiores')?>: <span id="score-majors"><?=$data['score']['Mayores']?></span>
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm">
                                            <?=$fnT('Menores')?>: <span id="score-minors"><?=$data['score']['Menores']?></span>
                                        </button>
                                    </div> -->
                                    <? if(in_array($data['status'], ['Temp Processing', 'Pending', 'In Process']) and !$isClosed and $permissions): ?>
                                        <button type="button" class="btn btn-primary btn-sm mb-1 btn-s1" id="btn-next-status" onclick="nextStep()">
                                            <?=$fnT('Mover para')?>: <span id="text-next-status"><?=/*$fnT($data['next-status'])*/"Completo"?></span>
                                        </button>
                                    <? endif ?>
                                    <? if(in_array($data['status'], ['Pending', 'In Process']) && $data['type'] == 'Self-Evaluation' && $permissions): ?>
                                        <br><button type="button" class="btn btn-danger btn-sm mb-1" id="btn-delete-selfevaluation" onclick="deleteSelfEvaluation()">
                                            <?=$fnT('Excluir autoavaliação')?>
                                        </button>
                                    <? endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12" id="report-panel">
                <? if(!$isClosed): ?>
                    <div class="row contTopOpA" id="report-panel">
                        <div class="TOpAR">
                            <a target="_blank" href="<?=$data['url_report']?>" class="TOpAbtn">
                                <i class="fa fa-tasks mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Ver relatório')?></small>
                            </a>
                        </div>
                        <div class="TOpA">
                            <a target="_blank" href="<?=$data['url_report']?>&download=1" class="TOpAbtn">
                                <i class="fa fa-download mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Baixar relatório')?></small>
                            </a>
                        </div>
                        <? if( in_array( $_SESSION['userData']['role']['id'], [1] )): ?>
                            <div class="TOpA">
                                <a target="_blank" href="./auditPrint/<?= encryptId($data['checklist_id']) ?>" class="TOpAbtn">
                                    <i class="fa fa-print mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Imprimir checklist')?></small>
                                </a>
                            </div>
                            <? if($data['type']=='Standard' && $data['status']=='Completed' && $data['score']['Calificacion']=='A'): ?>
                                <div class="TOpA">
                                    <a target="_blank" href="<?=base_url()?>/auditCertificate?tk=<?=encryptId($data['id'])?>&lan=<?=$_SESSION['userData']['default_language']?>" class="TOpAbtn">
                                        <i class="fa fa-certificate mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Ver certificado')?></small>
                                    </a>
                                </div>
                            <? endif ?>
                        <? endif ?>
                        
                    </div>
                <? endif ?>
                <hr class="my-1">
                <div class="row contTopOpA">
                    <div class="TOpA">
                        <a href="<?=base_url()?>/audits/auditInfo?id=<?=$id?>" class="OpAbtn <?=$sect=='General info'?'active':''?>">
                            <i class="fa fa-info mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Informações gerais')?></small>
                        </a>
                    </div>
                    <div class="TOpARV">
                        <a href="<?=base_url()?>/audits/audit?id=<?=$id?>" class="OpAbtn <?=$sect=='Checklist'?'active':''?> <?=$isClosed?>">
                            <i class="fa fa-list-ol mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Checklist')?></small>
                        </a>
                    </div>
                    <div class="TOpA">
                        <a href="<?=base_url()?>/audits/auditFiles?id=<?=$id?>" class="OpAbtn <?=$sect=='Photography'?'active':''?> <?=$isClosed?>">
                            <i class="fa fa-camera mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Fotografia')?></small>
                        </a>
                    </div>
                    <? if($data['status']=='In Process'): ?>
                        <div class="TOpA">
                            <a href="<?=base_url()?>/audits/times?id=<?=$id?>" class="OpAbtn <?=$sect=='Times'?'active':''?> <?=$isClosed?>">
                                <i class="fa fa-clock-o mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Tempos')?></small>
                            </a>
                        </div>
                    <? endif ?>
                    <? if($data['type']!='Self-Evaluation' && in_array($_SESSION['userData']['role']['id'], [1,2,3])){ ?>  
                    <div class="TOpA">
                        <a href="<?=base_url()?>/additional_Question?id=<?=$id?>" class="OpAbtn <?=$sect=='Informational questions'?'active':''?> <?=$isClosed?>">
                            <i class="fa fa-question mb-1" style="position:relative;left:13px;"></i><small class="menu-audit-item" style="text-align:center"><?=$fnT('Perguntas informativas')?></small>
                        </a>
                    </div>
                    <? } ?>
                    <? if($data['status']=='Completed' && $data['type']!='Self-Evaluation'): ?>
                        <div class="TOpA">
                            <a target="_blank" href="<?=base_url()?>/auditorSurvey?tk=<?=encryptId($data['id'])?>&lan=<?=$_SESSION['userData']['default_language']?>" class="OpAbtn">
                                <i class="fa fa-pencil-square-o mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Pesquisa do auditor')?></small>
                            </a>
                        </div>
                    <? endif ?>

                    <? if(in_array($_SESSION['userData']['role']['id'], [1,2,3,10,14,17,18,19,20,21]) && $data['type']!='Self-Evaluation'): ?>
                        <div class="TOpA">
                            <a href="<?=base_url()?>/actionPlan/auditPlan?id=<?=$id?>" class="OpAbtn <?=$sect=='Action plan'?'active':''?> <?=$isClosed?>">
                                <i class="fa fa-calendar-check-o mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Plano de ação')?></small>
                            </a>
                        </div>
                    <? endif ?>

                    <? if(in_array($_SESSION['userData']['role']['id'], [1,2]) && $data['type']!='Self-Evaluation'): ?>
                        <div class="TOpA">
                            <a href="<?=base_url()?>/Certtis/Certtis?id=<?=$id?>" class="OpAbtn <?=$sect=='Certtis'?'active':''?> <?=$isClosed?>">
                            <i class="fa fa-check-circle" aria-hidden="true"></i><br><small class="menu-audit-item"><?=$fnT('Certtis')?></small>
                            </a>
                        </div>
                    <? endif ?>
                    
                    <? if(in_array($_SESSION['userData']['role']['id'], [1, 2])): ?>
                        <div class="TOpAR">
                            <a href="<?=base_url()?>/audits/auditTools?id=<?=$id?>" class="OpAbtn <?=$sect=='Audit Tools'?'active':''?>">
                                <i class="fa fa-cogs mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Ferramentas de auditoria')?></small>
                            </a>
                        </div>
                    <? endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let rootStyles = getComputedStyle(document.documentElement);
    let color1v = rootStyles.getPropertyValue('--color1').trim();
    let color2v = rootStyles.getPropertyValue('--color2').trim();
    let color3v = rootStyles.getPropertyValue('--color3').trim();
    let color4v = rootStyles.getPropertyValue('--color4').trim();
    let color5v = rootStyles.getPropertyValue('--color5').trim();
    let color6v = rootStyles.getPropertyValue('--color6').trim();
    let color7v = rootStyles.getPropertyValue('--color7').trim();
    let color8v = rootStyles.getPropertyValue('--color8').trim();
    let color9v = rootStyles.getPropertyValue('--color9').trim();
    let dataG;
    let ctx;
    let chart;
    let config;
    let status = document.getElementById('audit_status').innerHTML;
    switch(status){
        case 'Pendente':
            document.getElementById('audit_status').style.backgroundColor='var(--color6)';
            break;
        case 'Em andamento':
            document.getElementById('audit_status').style.backgroundColor='var(--color7)';
            break;
        case 'Concluída':
            document.getElementById('audit_status').style.backgroundColor='var(--color8)';
            break;
        case 'Excluída!':
            document.getElementById('audit_status').style.backgroundColor='var(--color9)';
            break;
    }
    function nextStep(){
        const payload = new FormData();
        payload.append('audit_id', <?=$id?>);
        swal({
            title: fnT('Alerta'),
            text: fnT('Deseja avançar para o próximo status desta auditoria?'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: fnT('Sim'),
            cancelButtonText: fnT('Não')
        }, function(isConfirm){
            if(isConfirm){
                $('#divLoading').css('display', 'flex');
                fetch(base_url + '/audits/nextStep', {
                    method: 'POST',
                    body: payload
                }).then(res => res.json()).then(dat => {
                    $('#divLoading').css('display', 'none');
                    if(dat.status == 1){
                        if(dat.currStatus == 'Completed'){
                            $('#btn-next-status').remove();
                            $('#btn-delete-selfevaluation').remove();
                            if(dat.cal == 'A' && <?=$data['type']=='Standard'? 'true' : 'false'?>){
                                $('#report-panel').append(`<div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                                    <a target="_blank" href="<?=base_url()?>/auditCertificate?tk=<?=encryptId($data['id'])?>&lan=<?=$_SESSION['userData']['default_language']?>" class="btn btn-dark btn-lg btn-block px-1">
                                        <i class="fa fa-info mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Ver certificado')?></small>
                                    </a>
                                </div>`)
                            }
                            editRestricted = false;
                            $('.can-update').remove();
                        }
                        $('#audit_status').html(dat.currStatus);
                        switch(dat.currStatus){
                            case 'Pending':
                                document.getElementById('audit_status').style.backgroundColor='var(--color6)';
                                break;
                            case 'In Process':
                                document.getElementById('audit_status').style.backgroundColor='var(--color7)';
                                break;
                            case 'Completed':
                                document.getElementById('audit_status').style.backgroundColor='var(--color8)';
                                break;
                            case 'Deleted!':
                                document.getElementById('audit_status').style.backgroundColor='var(--color9)';
                                break;
                        }
                        $('#text-next-status').html(dat.nextStatus || '');
                    } else{
                        swal({
                            title: fnT('Erro'),
                            text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
                            type: 'error'
                        });
                    }
                });            
            }
        });
    }

    function deleteSelfEvaluation(){
        swal({
            title: fnT('Alerta'),
            text: fnT('Tem certeza de que deseja excluir esta auditoria?'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: fnT('Sim'),
            cancelButtonText: fnT('Não')
        }, function(isConfirm){
            if(isConfirm){
                $('#divLoading').css('display', 'flex');
                fetch(base_url + '/audits/deleteSelfEvaluation/<?=$id?>').then(res => res.json()).then(dat => {
                    $('#divLoading').css('display', 'none');
                    if(dat.status == 1){
                        window.location.href = base_url + '/audits';
                    } else{
                        swal({
                            title: fnT('Erro'),
                            text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
                            type: 'error'
                        });
                    }
                });    
            }
        });
    }

</script>