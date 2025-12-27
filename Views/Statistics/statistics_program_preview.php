<?php 
    headerTemplate($data);
    getModal('modalSetTargetPP',$data);
    global $fnT;
?>
<link rel="stylesheet" type="text/css" href="<?=media()?>/css/statistics.css">
<main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-pie-chart" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('View the progress of the program')?></p>
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
                            <select class="form-control selectpicker" id="filter_type" name="list_type[]" multiple data-actions-box="true" data-selected-text-format="count>1" required>
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
                            <select class="form-control selectpicker" id="filter_period" name="list_period" required>
                                <? foreach($data['periods'] as $period): ?>
                                    <option value="<?=$period?>" selected><?=$period?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>


<!-- COUNTRY -->
<div class="col-lg-3 my-1 ">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Region')?></span>
        </div>
        <select class="form-control selectpicker" id="list_region" name="list_region[]" multiple data-actions-box="true" data-selected-text-format="count>2" required  >
            <? foreach($data['region'] as $region): ?>
                <? $val = !empty($region) ? $region : 'N/A'; ?>
                <option value="<?= $val ?>" selected><?= $val ?></option>
            <? endforeach ?>
        </select>
    </div>
</div>

<!-- COUNTRY (agrupado por región, NUEVO ID) -->
<div class="col-lg-3 my-1 ">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Country')?></span>
        </div>

        <select class="form-control selectpicker" 
            id="list_country" 
            name="list_country[]" 
            multiple 
            data-actions-box="true" 
            data-selected-text-format="count>2" 
            title="Todos seleccionados" 
            required>

            <?php foreach ($data['regions_with_countries'] as $region => $countries): ?>
                <optgroup label="<?= $region ?>" data-region="<?= $region ?>">
                    <?php foreach ($countries as $country): ?>
                        <?php $val = !empty($country) ? $country : 'N/A'; ?>
                        <option value="<?= $val ?>" selected><?= $val ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>

        </select>
    </div>
</div>




                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Franchise')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_franchise" name="list_franchise[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['franchissees'] as $f): ?>
                                    <option value="<?=$f['name']?>" selected><?=$f['name']?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 my-1">
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
                        <button type="submit" class="btn btn-primary float-right">
                            <i class="fa fa-filter" aria-hidden="true"></i>&nbsp;&nbsp;
                            <?=$fnT('Filter')?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="tile">
        <div class="tile-body">
            <div class="table-responsive" id="cotainer-table-pp"></div>
        </div>
    </div>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<script>
    const divisions = <?= json_encode($data['its3R']) ?>; 
</script>
<?php footerTemplate($data);?>