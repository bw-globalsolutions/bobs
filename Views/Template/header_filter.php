<!-- FILTROS -->
<? global $fnT; ?>

<div class="tile">
    <div class="tile-body">
        <form onsubmit="reloadAll(this); return false;" id="filter_form">
            <div class="form-row">
                <!-- FILTROS PRINCIPALES -->
                <!-- Type -->
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

                <!-- Period -->
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


                <!-- Franchise -->
                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Franchise')?></span>
                        </div>
                        <select class="form-control selectpicker" id="filter_franchise" name="list_franchise[]" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search="true">
                            <? foreach($data['franchissees'] as $f): ?>
                                <option value="<?=$f['name']?>" 
                                        data-country="<?=$f['country']?>" 
                                        selected>
                                    <?=$f['name']?>
                                </option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <!-- FILTROS GEOGRÁFICOS/OPERATIVOS -->
                <!-- Area -->
                <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3,17])? 'd-none' : '' ?>">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Area')?></span>
                        </div>
                        <select class="form-control selectpicker" id="list_area" name="list_area[]"  multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">                             
                            <? foreach($data['area'] as $area): ?>
                                <? $val = !empty($area) ? $area : 'N/A'; ?>
                                <option value="<?= $val ?>" selected><?= $val ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <!-- Concept -->
                <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3,17])? 'd-none' : '' ?>">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Concept')?></span>
                        </div>
                        <select class="form-control selectpicker" id="list_concept" name="list_concept[]"  multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
                            <? foreach($data['concept'] as $concept): ?>
                                <? $val = !empty($concept) ? $concept : 'N/A'; ?>
                                <option value="<?= $val ?>" selected><?= $val ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <!-- Shop Type -->
                <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3])? 'd-none' : '' ?>" >
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Shop Type')?></span>
                        </div>
                        <select class="form-control selectpicker" id="list_shop_type" name="list_shop_type[]"  multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
                            <? foreach($data['shop_type'] as $shop_type): ?>
                                <? $val = !empty($shop_type) ? $shop_type : 'N/A'; ?>
                                <option value="<?= $val ?>" selected><?= $val ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <!-- FILTROS DE PERSONAL -->
                <!-- Auditor -->
                <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2])? 'd-none' : '' ?>">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Auditor')?></span>
                        </div>
                        <select class="form-control selectpicker" id="filter_auditor" name="list_auditor[]" multiple data-actions-box="true" data-selected-text-format="count>1" data-live-search = "true" required>
                            <? foreach($data['auditor_email'] as $auditor): ?>
                                <option value="<?=$auditor['auditor_email']?>" selected><?=$auditor['auditor_email']?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <!-- Area Manager -->
                <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3,17, 14,19,18])? 'd-none' : '' ?>">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Area Manager')?></span>
                        </div>
                        <select class="form-control selectpicker" id="list_area_manager" name="list_area_manager[]"  multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
                            <? foreach($data['email_area_manager'] as $email_area_manager): ?>
                                <? $val = !empty($email_area_manager) ? $email_area_manager : 'N/A'; ?>
                                <option value="<?= $val ?>" selected><?= $val ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <!-- Escalation 1 -->
                <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3,17])? 'd-none' : '' ?>">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Escalation 1')?></span>
                        </div>
                        <select class="form-control selectpicker" id="list_escalation1"  name="list_escalation1[]" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
                            <? foreach($data['email_ops_leader'] as $email_ops_leader): ?>
                                <? $val = !empty($email_ops_leader) ? $email_ops_leader : 'N/A'; ?>
                                <option value="<?= $val ?>" selected><?= $val ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <!-- Escalation 2 -->
                <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3,17])? 'd-none' : '' ?>">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Escalation 2')?></span>
                        </div>
                        <select class="form-control selectpicker" id="list_escalation2" name="list_escalation2[]"  multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
                            <? foreach($data['email_ops_director'] as $email_ops_director): ?>
                                <? $val = !empty($email_ops_director) ? $email_ops_director : 'N/A'; ?>
                                <option value="<?= $val ?>" selected><?= $val ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <!-- ACCIÓN -->
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