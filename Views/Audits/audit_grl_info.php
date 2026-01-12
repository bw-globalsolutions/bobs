<?php 
    headerTemplate($data);
    getModal('modalImage', null);
    global $fnT;

    if(!empty($data['audit']['date_visit'])){
        $tmp = new DateTime($data['audit']['date_visit']);
        $dateVisit = $tmp->format("Y-m-d");
        $startTime = $tmp->format("H:i");
    }
    
    if(!empty($data['audit']['date_visit_end'])){
        $tmp = new DateTime($data['audit']['date_visit_end']);
        $endTime = $tmp->format("H:i");
    }
?>
<div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
  <main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-info" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Consultar informações gerais')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <? headerTemplateAudits($_GET['id'], 'General info') ?>
    <div class="tile">
        <div class="tile-body">
            <form id="form-grl-info" onsubmit="sendGrlInfo(this); return false;">
                <input type="hidden" name="audit_id" value="<?=$_GET['id']?>" id="audit-id">
                <div class="form-row">
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="date-visit"><?=$fnT('Data da visita')?></label>
                        <input value="<?=$dateVisit?? date("Y-m-d")?>" type="date" class="form-control" name="date_visit" id="date-visit" required>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="start_time"><?=$fnT('Hora de início')?></label>
                        <input value="<?=$startTime?? date("H:i")?>" type="time" class="form-control" name="start_time" id="start_time" required>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="end_time"><?=$fnT('Hora de término')?></label>
                        <input value="<?= $endTime?? date('H:i', strtotime('+2 hours')) ?>" type="time" class="form-control" name="end_time" id="end_time" required>
                    </div>

                    <? if(ON_AA): ?>
                        <div class="col-lg-4 col-md-6 col-12 form-group">
                            <label for="audited-areas" class="mr-3"><?=$fnT('Áreas auditadas')?>: </label>
                            <? if(empty($data['audit']['visit_status'])): ?>
                                <select class="selectpicker" multiple title="<?=$fnT('Selecione uma ou mais áreas')?>" data-width="275px" name="areas[]" required>
                                    <option value="all"><?=$fnT('Todas as áreas')?></option>
                                    <? foreach($data['areas'] as $area): ?>
                                        <option value="<?= $area ?>"><?= $area ?></option>
                                    <? endforeach; ?>
                                </select>
                            <? else: ?>
                                <b><?= empty($data['audit']['audited_areas']) || in_array($data['audit']['audited_areas'], ['[]', ' ["-Sin-Areas-"]'])? $fnT('Todas as áreas') : str_replace('|', ', ', $data['audit']['audited_areas']) ?></b> 
                            <? endif ?>
                        </div>
                    <? endif; ?>

                </div>
                <hr>
                <div class="form-row">
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="name="visit-status"><?=$fnT('Status da visita')?></label>
                        <select class="form-control" name="visit_status" id="visit-status" required onchange="changueStatus(this.value)">
                            <option value="" disabled selected><?=$fnT('Selecione uma opção')?></option>
                            <option value="Visited" <?=$data['audit']['visit_status']=='Visited'? 'selected' : ''?>><?=$fnT('Visitado')?></option>
                            <option value="Closed" <?=$data['audit']['visit_status']=='Closed'? 'selected' : ''?>><?=$fnT('Fechado')?></option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="manager-email"><?=$fnT('E-mail do gerente')?></label>
                        <input value="<?=$data['audit']['manager_email']?>" type="email" class="form-control" name="manager_email" id="manager-email" placeholder="<?=$fnT('E-mail do gerente')?>" required>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="manager-name"><?=$fnT('Nome do gerente')?></label>
                        <input value="<?=$data['audit']['manager_name']?>" type="text" class="form-control" name="manager_name" id="manager-name" placeholder="<?=$fnT('Nome do gerente')?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="visit-comment"><?=$fnT('Comentário adicional')?></label>
                        <textarea class="form-control" id="visit-comment" rows="2" name="visit_comment"><?=$data['audit']['visit_comment']?></textarea>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group">
                        <label for="input-pic"><?=$fnT('Porta da frente')?></label>
                        <input type="hidden" id="visit-pic">
                        <input type="file" class="form-control-file" id="visit-pic" onchange="uploadPic(this)">
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 form-group" id="panel-pic"></div>
                </div>
                <div class="mt-1 d-flex justify-content-between">
                    <div>
                        <? if(!empty($data['audit']['manager_signature']) && $data['audit']['manager_signature']!='Sin Firma'): ?>
                            <label><?=$fnT('Assinatura do gerente')?></label>:&nbsp;&nbsp;&nbsp;
                            <a href="<?=$data['audit']['manager_signature']?>" target="_blank">
                                <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="<?=$data['audit']['manager_signature']?>">
                            </a>
                        <? endif ?>
                    </div>
                    <div>
                        <? if(!empty($data['question']['url'])): ?>
                            <label><?=$fnT('Comentários do gerente')?></label>:&nbsp;&nbsp;&nbsp;
                            <a href="<?=$data['question']['url']?>" target="_blank">
                                <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="<?=$data['question']['url']?>">
                            </a>
                        <? endif ?>
                    </div>
                    <div class="d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-1" form="form-grl-info" id="btn-send-grl-info"><?=$fnT('Salvar')?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>
<script>
    <? if(!empty($data['audit']['visit_status']) || !($data['permision']['u'] || isMySelfEvaluation($_GET['id']))): ?>
        $('#form-grl-info input, #form-grl-info textarea, #form-grl-info select').prop( "disabled", true);
        $('#btn-send-grl-info').addClass('d-none');
    <? else: ?>
        $('#btn-edit-grl-info').addClass('d-none');
    <? endif ?>
    const urlAudits = '<?=base_url()?>/audits';
    const urlChecklist = '<?=base_url()?>/audits/audit?id=<?=$_GET['id']?>';
</script>