<?php 
  headerTemplate($data);
  global $fnT;
?>
<main class="app-content">
    <?php //dep($data); ?>
    <div class="app-title">
        <div>
            <h1> <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?> </h1>
            <p><?=$fnT('Consultar plano de ação')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <!--INICIO FILTROS-->
    <div class="tile">
        <div class="tile-body">
            <div class="form-row justify-content-center">
                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('País')?></span>
                        </div>
                        <select class="form-control selectpicker" id="filter_country" name="filter_country[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                            <? foreach($data['countries'] as $country): ?>
                                <option value="<?=$country['id']?>" selected><?=$country['name']?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Período')?></span>
                        </div>
                        <select class="form-control selectpicker" id="filter_period" name="filter_period" required>
                            <? foreach($data['periods'] as $period): ?>
                                <option value="<?=$period?>" <?=$period==$data['periods'][0]? 'selected' : ''?>><?=$period?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Tipo')?></span>
                        </div>

                        <select class="form-control selectpicker" id="filter_type" name="filter_type[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                            <? foreach($data['typeVisit'] as $typeVisit): ?>
                                <option value="<?=$typeVisit['type']?>" selected><?=$typeVisit['type']?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Franquia')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_franchise" name="list_franchise[]" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
                                <? foreach($data['franchissees'] as $f): ?>
                                    <option value="<?=$f['name']?>" selected><?=$f['name']?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>


                      <!-- EMAIL AREA MANAGER -->
                    <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3,17, 14,19,18])? 'd-none' : '' ?>">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Gerente de área')?></span>
                            </div>
                            <select class="form-control selectpicker" id="list_area_manager" name="list_area_manager[]"  multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
                                <? foreach($data['email_area_manager'] as $email_area_manager): ?>
                                    <? $val = !empty($email_area_manager) ? $email_area_manager : 'N/A'; ?>
                                    <option value="<?= $val ?>" selected><?= $val ?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>


            



                

                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Status do plano de ação')?></span>
                        </div>
                        <select class="form-control selectpicker" id="filter_status" name="filter_status[]" multiple data-actions-box="true" data-selected-text-format="count>1" required>
                            <option value="Finished" selected><?=$fnT('Finalizado')?></option>
                            <option value="Pending" selected><?=$fnT('Pendente')?></option>
                            <option value="In Process" selected><?=$fnT('Em processo')?></option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-12 my-1">
                    <button id="btnFilterAnnouncedVisit" class="form-control btn btn-primary" type="button" onclick="reloadData();">
                        <i class="fa fa-filter" aria-hidden="true"></i>&nbsp;&nbsp;
                        <?=$fnT('Filtrar')?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--FIN FILTROS-->
    
    <!--INICIO TABLE-->
    <div class="tile">
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableActionPlan">
                        <thead style="background-color: #143c6aff; color: white;">
                            <tr>
                                <th><?=$fnT('ID')?></th>
                                <th><?=$fnT('Informações da visita')?></th>
                                <th><?=$fnT('Link')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--FIN TABLE-->

    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>