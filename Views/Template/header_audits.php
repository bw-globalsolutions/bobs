<?  
    global $fnT;
    $isClosed = $data['visit_status'] != 'Visited'? 'disabled' : 0;
    $permissions = ($_SESSION['userData']['permission']['Auditorias']['u'] or isMySelfEvaluation($data['id']));
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
                                <p class="mb-1"><?=$fnT('Status')?>: <b id="audit_status"><?=$fnT($data['status'])?></b></p>
                                <p class="mb-1"><?=$fnT('Date of visit')?>: <b><?=$data['date_visit']?? $fnT('No registration')?></b></p>
                                                                
                                <p class="mb-3"><?=$fnT('Type')?>: <b><?=$fnT($data['type'])?></b></p>
                                <p class="small mb-0 text-secondary">
                                    <b>ID: <?=$data['id']?></b> — CHK<?=$data['checklist_id']?> — SCG<?=$data['scoring_id']?></small>
                                </p>
                                <div class="mb-1">
                                    <div class="btn-group mb-1" role="group">
                                        <button type="button" class="btn btn-info btn-sm"  data-toggle='modal'  data-target='#modalOpp' onclick = "selectOpp('critical');" >
                                            <?=$fnT('Critics')?>: <span id="score-critics"><?=$data['score']['Criticos']?></span>
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm"  data-toggle='modal'  data-target='#modalOpp'  onclick = "selectOpp('Non-Critical');">
                                            <?=$fnT('No Critics')?>: <span id="score-nocritics"><?=$data['score']['NoCriticos']?></span>
                                        </button>
                                    </div><br>
                                    <div class="btn-group mb-1" role="group">
                                        <!-- <button type="button" class="btn btn-success btn-sm">
                                            <?=$fnT('Green')?>: <span id="score-green"><?=$data['score']['Verdes']?></span>
                                        </button> -->
                                        <button type="button" class="btn btn-warning btn-sm"  data-toggle='modal'  data-target='#modalOpp'  onclick = "selectOpp('Yellow');">
                                            <?=$fnT('Yellow')?>: <span id="score-yellow"><?=$data['score']['Amarillos']?></span>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"  data-toggle='modal'  data-target='#modalOpp'  onclick = "selectOpp('Red');">
                                            <?=$fnT('Red')?>: <span id="score-red"><?=$data['score']['Rojos']?></span>
                                        </button>
                                    </div><br>
                                    <div class="btn-group mb-1" role="group">
                                        <button type="button" class="btn btn-info btn-sm"  data-toggle='modal'  data-target='#modalOpp'  onclick = "selectOpp('Maintenance');">
                                            <?=$fnT('Maintenance')?>: <span id="score-maintenance"><?=$data['score']['Mantenimiento']?></span>
                                        </button>
                                    </div>
                                    <div class="btn-group mb-1" role="group">
                                        <button type="button" class="btn btn-danger btn-sm"  data-toggle='modal'  data-target='#modalOpp'  onclick = "selectOpp('Auto Fail');">
                                            <?=$fnT('Auto Fail')?>: <span id="score-autofail"><?=$data['score']['AutoFail']?></span>
                                        </button>
                                    </div>
                                    <!-- <div class="btn-group mb-1" role="group">
                                        <button type="button" class="btn btn-info btn-sm">
                                            <?=$fnT('Majors')?>: <span id="score-majors"><?=$data['score']['Mayores']?></span>
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm">
                                            <?=$fnT('Minors')?>: <span id="score-minors"><?=$data['score']['Menores']?></span>
                                        </button>
                                    </div> -->
                                    <? if(in_array($data['status'], ['Temp Processing', 'Pending', 'In Process']) and !$isClosed and $permissions): ?>
                                        <button type="button" class="btn btn-primary btn-sm mb-1" id="btn-next-status" onclick="nextStep()">
                                            <?=$fnT('Move to')?>: <span id="text-next-status"><?=$fnT($data['next-status'])?></span>
                                        </button>
                                    <? endif ?>
                                    <? if(in_array($data['status'], ['Pending', 'In Process']) && $data['type'] == 'Self-Evaluation' && $permissions): ?>
                                        <br><button type="button" class="btn btn-danger btn-sm mb-1" id="btn-delete-selfevaluation" onclick="deleteSelfEvaluation()">
                                            <?=$fnT('Delete')?> <?=$fnT('Self-Evaluation')?>
                                        </button>
                                    <? endif ?>
                                    <br>
                                

                  
                               <style>
:root {
    --btn-font-size: 0.7rem;       /* Tamaño base del texto */
    --btn-padding-vertical: 0.25rem;  /* Padding vertical */
    --btn-padding-horizontal: 0.5rem; /* Padding horizontal */
    --btn-border-radius: 6px;      /* Radio de bordes */
}

.btn-professional {
    border-width: 1.5px;
    border-style: solid;
    border-radius: var(--btn-border-radius);
    padding: var(--btn-padding-vertical) var(--btn-padding-horizontal);
    font-weight: 600;
    font-size: var(--btn-font-size);
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    cursor: default;
    user-select: none;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.btn-professional.disabled,
.btn-professional:disabled {
    opacity: 1; /* para que no se vea tan opaco */
}

.btn-professional.success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.btn-professional.danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
</style>
               
                     
                                    
<div >
                                   <?php if ($data['score']['Criticos'] > 1): 
    $fspc = 'Fail'; $color = 'danger';  
else: 
    $fspc = 'Pass'; $color = 'success'; 
endif; ?>
<div class="btn-group mb-1" role="group">
    <button type="button" class="btn-professional <?= $color ?>" disabled>
        <b><?= $fnT('FSPC') ?>: <span id="score-autofail"><?= $fnT($fspc) ?></span></b>
    </button>
</div>

<?php if ($data['score']['Rojos'] > 6): 
    $cpc = 'Fail'; $color = 'danger';  
else: 
    $cpc = 'Pass'; $color = 'success'; 
endif; ?>
<div class="btn-group mb-1" role="group">
    <button type="button" class="btn-professional <?= $color ?>" disabled>
        <b><?= $fnT('CPC') ?>: <span id="score-autofail"><?= $fnT($cpc) ?></span></b>
    </button>
</div>

<?php 
if ($data['score']['Result'] == 'Fail') { $color = 'danger'; } 
else if ($data['score']['Result'] == 'Pass') { $color = 'success'; } 
?>
<div class="btn-group mb-1" role="group">
    <button type="button" class="btn-professional <?= $color ?>" disabled>
        <b><?= $fnT('Overall Score') ?>: <span id="score-autofail"><?= $fnT($data['score']['Result']) ?></span></b>
    </button>
</div>
</div>
                                     
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12" id="report-panel">
                <? if(!$isClosed): ?>
                    <div class="row" id="report-panel">
                        <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                            <a target="_blank" href="<?=$data['url_report']?>" class="btn btn-dark btn-lg btn-block px-1">
                                <i class="fa fa-tasks mb-1"></i><br><small class="menu-audit-item"><?=$fnT('View report')?></small>
                            </a>
                        </div>
                        <? if( in_array( $_SESSION['userData']['role']['id'], [1] )): ?>
                            <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                                <a target="_blank" href="./auditPrint/<?= encryptId($data['checklist_id']) ?>" class="btn btn-dark btn-lg btn-block px-1">
                                    <i class="fa fa-print mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Print checklist')?></small>
                                </a>
                            </div>
                            <? if($data['type']=='Standard' && $data['status']=='Completed' && $data['score']['Calificacion']=='A'): ?>
                                <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                                    <a target="_blank" href="<?=base_url()?>/auditCertificate?tk=<?=encryptId($data['id'])?>&lan=<?=$_SESSION['userData']['default_language']?>" class="btn btn-dark btn-lg btn-block px-1">
                                        <i class="fa fa-certificate mb-1"></i><br><small class="menu-audit-item"><?=$fnT('View certificate')?></small>
                                    </a>
                                </div>
                            <? endif ?>
                        <? endif ?>
                        
                    </div>
                <? endif ?>
                <hr class="my-1">
                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                        <a href="<?=base_url()?>/audits/auditInfo?id=<?=$id?>" class="btn btn-primary btn-lg btn-block px-1 <?=$sect=='General info'?'active':''?>">
                            <i class="fa fa-info mb-1"></i><br><small class="menu-audit-item"><?=$fnT('General info')?></small>
                        </a>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                        <a href="<?=base_url()?>/audits/audit?id=<?=$id?>" class="btn btn-primary btn-lg btn-block px-1 <?=$sect=='Checklist'?'active':''?> <?=$isClosed?>">
                            <i class="fa fa-list-ol mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Checklist')?></small>
                        </a>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                        <a href="<?=base_url()?>/audits/auditFiles?id=<?=$id?>" class="btn btn-primary btn-lg btn-block px-1 <?=$sect=='Photography'?'active':''?> <?=$isClosed?>">
                            <i class="fa fa-camera mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Photography')?></small>
                        </a>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                        <a href="<?=base_url()?>/additional_Question?id=<?=$id?>" class="btn btn-primary btn-lg btn-block px-1 <?=$sect=='Informational questions'?'active':''?> <?=$isClosed?>">
                            <i class="fa fa-question mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Informational questions')?></small>
                        </a>
                    </div>

                    <? if($data['status']=='Completed'): ?>
                        <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                            <a target="_blank" href="<?=base_url()?>/auditorSurvey?tk=<?=encryptId($data['id'])?>&lan=<?=$_SESSION['userData']['default_language']?>" class="btn btn-primary btn-lg btn-block px-1">
                                <i class="fa fa-pencil-square-o mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Auditor survey')?></small>
                            </a>
                        </div>
                    <? endif ?>

                    <? if(in_array($_SESSION['userData']['role']['id'], [1,2,10,17,20])): ?>
                        <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                            <a href="<?=base_url()?>/actionPlan/auditPlan?id=<?=$id?>" class="btn btn-primary btn-lg btn-block px-1 <?=$sect=='Action plan'?'active':''?> <?=$isClosed?>">
                                <i class="fa fa-calendar-check-o mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Action plan')?></small>
                            </a>
                        </div>
                    <? endif ?>

                    <? if(in_array($_SESSION['userData']['role']['id'], [1,2])): ?>
                        <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                            <a href="<?=base_url()?>/Certtis/Certtis?id=<?=$id?>" class="btn btn-primary btn-lg btn-block px-1 <?=$sect=='Action plan'?'active':''?> <?=$isClosed?>">
                            <i class="fa fa-check-circle" aria-hidden="true"></i><br><small class="menu-audit-item"><?=$fnT('Certtis')?></small>
                            </a>
                        </div>
                    <? endif ?>
                    
                    <? if(in_array($_SESSION['userData']['role']['id'], [1, 2])): ?>
                        <div class="col-xl-4 col-lg-6 col-md-4 col-6 p-1">
                            <a href="<?=base_url()?>/audits/auditTools?id=<?=$id?>" class="btn btn-secondary btn-lg btn-block <?=$sect=='Audit Tools'?'active':''?>">
                                <i class="fa fa-cogs mb-1"></i><br><small class="menu-audit-item"><?=$fnT('Audit tools')?></small>
                            </a>
                        </div>
                    <? endif ?>
                </div>
            </div>
        </div>
    </div>
</div>







   <div class="modal fade " id="modalOpp" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog  modal-dialog-scrollable">
                                    <div class="modal-content ">
                                        <div class="modal-header header-primary">
                                            <h5 class="modal-title" id="titleModal" name="titleModal" ></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="tile" id="opp">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>





<script>
    function nextStep(){
        const payload = new FormData();
        payload.append('audit_id', <?=$id?>);
        swal({
            title: fnT('Alert'),
            text: fnT('Do you want to advance to the next status of this audit?'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: fnT('Yes'),
            cancelButtonText: fnT('No')
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
                                        <i class="fa fa-info mb-1"></i><br><small class="menu-audit-item"><?=$fnT('View certificate')?></small>
                                    </a>
                                </div>`)
                            }
                            editRestricted = false;
                            $('.can-update').remove();
                        }
                        $('#audit_status').html(dat.currStatus);
                        $('#text-next-status').html(dat.nextStatus || '');
                    } else{
                        swal({
                            title: fnT('Error'),
                            text: fnT('An error occurred in the process, if the problem persists please contact support'),
                            type: 'error'
                        });
                    }
                });            
            }
        });
    }

    function deleteSelfEvaluation(){
        swal({
            title: fnT('Alert'),
            text: fnT('Are you sure you want to delete this audit?'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: fnT('Yes'),
            cancelButtonText: fnT('No')
        }, function(isConfirm){
            if(isConfirm){
                $('#divLoading').css('display', 'flex');
                fetch(base_url + '/audits/deleteSelfEvaluation/<?=$id?>').then(res => res.json()).then(dat => {
                    $('#divLoading').css('display', 'none');
                    if(dat.status == 1){
                        window.location.href = base_url + '/audits';
                    } else{
                        swal({
                            title: fnT('Error'),
                            text: fnT('An error occurred in the process, if the problem persists please contact support'),
                            type: 'error'
                        });
                    }
                });    
            }
        });
    }


    //Boton desgloce oportunidades

function selectOpp(type){

	$("#opp").empty();
    var id = <?=$data['id']?>

    

    $.ajax({ type: "POST",
             url:  " "+base_url+"/audits/selectOpp",
		     data: {id,type},
             dataType: "json",

    success: function(data){
	    
       
        $.each(data,function(key, registro) {

        let question     = registro.question;              
        let question_prefix     = registro.question_prefix;              
        let type     = registro.type;              
        console.log(question_prefix);
        

        $('#opp').append(`
                                                    <div data-snumber="101" class="question-item">
                                                        <div class="card-header d-flex justify-content-between">
                                                            <span data-na="0" onclick="toggleQuestion('Q1')" style="cursor: pointer" id="bpicklistQ1">
                                                                <span class="badge badge-secondary">`+question_prefix+`</span> - 
                                                                &nbsp;
                                                                `+question+`
                                                            </span>
                                                            <button type="button" class="btn ml-2 btn-danger" style="height: 35px" >
                                                                <b>`+type+`</b>
                                                            </button>
                                                        </div>
                                                    </div>  
                                               
`);
        });
        },
        error: function(data) {
            console.log(data);
        }
        });
    	
    }

</script>