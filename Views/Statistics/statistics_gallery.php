<?php 
    headerTemplate($data);
    getModal('modalImage', null);
    global $fnT;
?>
<link rel="stylesheet" type="text/css" href="<?=media()?>/css/statistics.css">
<main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-pie-chart" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Visualize the information collected in the form of graphs')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-list-ol fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <div class="tile">
        <div class="tile-body">
            <form onsubmit="reloadAll(this); return false;" id="filter_form">
                <div class="form-row">
                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Type')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_type" name="list_type" required>
                                <? foreach($data['audit_types'] as $type): ?>
                                    <option value="<?=$type['type']?>" selected><?=$type['type']?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Period')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_period" name="list_period[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['periods'] as $pd_key => $pd_val): ?>
                                    <optgroup label="<?= $pd_key ?>">
                                        <? foreach($pd_val as $m): ?>
                                            <option val="<?= $m ?>" <?= array_key_first($data['periods']) == $pd_key? 'selected' : '' ?>><?= $m ?></option>
                                        <? endforeach; ?>
                                    </optgroup>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
<!-- REGION -->
                    <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3,17])? 'd-none' : '' ?>">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Region')?></span>
                            </div>
                            <select class="form-control selectpicker" id="list_country" name="list_region[]"   multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['region'] as $country): ?>
                                    <? $val = !empty($country) ? $country : 'N/A'; ?>
                                    <option value="<?= $val ?>" selected><?= $val ?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
<!-- COUNTRY -->
                    <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3,17])? 'd-none' : '' ?>">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Country')?></span>
                            </div>
                            <select class="form-control selectpicker" id="list_country" name="list_country[]"   multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['country'] as $country): ?>
                                    <? $val = !empty($country) ? $country : 'N/A'; ?>
                                    <option value="<?= $val ?>" selected><?= $val ?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>

<!-- AUDIT FILE -->
               
<div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1,2,3,17]) ? 'd-none' : '' ?>">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?= $fnT('Audit file') ?></span>
        </div>
        <select class="form-control selectpicker" id="audit_file" name="audit_file[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
            <?php foreach ($data['auditFile'] as $group => $files): ?>
                
                    <?php foreach ((array)$files as $f): ?>
                        <option value="<?= htmlspecialchars($f) ?>" <?= array_key_first($data['auditFile']) == $group ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f) ?>
                        </option>
                    <?php endforeach; ?>
              
            <?php endforeach; ?>
        </select>
    </div>
</div>



                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Franchise')?></span>
                            </div>
                            <select class=" selectpicker" id="filter_franchise" name="list_franchise[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['franchissees'] as $f): ?>
                                    <option value="<?=$f['name']?>" selected><?=$f['name']?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>


                    <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2])? 'd-none' : '' ?>">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Section')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_checklist" name="list_checklist[]" multiple data-actions-box="true" data-selected-text-format="count>1" required>
                                    <? foreach($data['checklist'] as $checklist): ?>
                                        <option value="<?=$checklist['main_section']?>" selected><?=$fnT($checklist['main_section'])?></option>
                                    <? endforeach ?>
                            </select>
                        </div>
                    </div>

                   <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2]) ? 'd-none' : '' ?>">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Checklist item')?></span>
        </div>
        <select 
            class="form-control selectpicker" 
            id="filter_checklist_item" 
            name="list_checklist_item[]" 
            multiple 
            data-actions-box="true" 
            data-selected-text-format="count>1" 
            data-live-search="true"  
            required
        >
            <?php foreach($data['checklist_item'] as $checklist_item): ?>
                <option value="<?=$checklist_item['question_prefix']?>" selected>
                    <?=$checklist_item['question_prefix']?>: <?=$checklist_item['section_name']?> - <?=$checklist_item['eng']?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
</div>


                    <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2])? 'd-none' : '' ?>">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Auditor')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_auditor" name="list_auditor[]" multiple data-actions-box="true" data-selected-text-format="count>1" required>
                                <? foreach($data['auditor_email'] as $auditor): ?>
                                    <option value="<?=$auditor['auditor_email']?>" selected><?=$auditor['auditor_email']?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>

                    
                    


                    <div class="col-lg-3 my-1">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter" aria-hidden="true"></i>&nbsp;&nbsp;
                            <?=$fnT('Filter')?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="panel-gallery"></div>
    <span id="upload-img"></span>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>