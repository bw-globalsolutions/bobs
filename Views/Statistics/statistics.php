<?php 
    headerTemplate($data);
   // headerFilter($data);
    getModal('modalStatisticsCompare',$data);
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

<?php 

    headerFilter($data);
    
?>

    <div class="tile">
        <div class="tile-body">
            <div class="row">




                <div class="col-12">
                    <div class="tile">
                        <div class="tile-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="tableLeadership">
                                <thead>
                                    <tr>
                                        <th colspan="2"></th>
                                        <th colspan="8" class="bg-ws"><?=$fnT('Average scores')?></th>
                                    </tr>
                                    <tr>
                                        <th><?=$fnT('Franchise')?></th>
                                        <th><?=$fnT('Visits')?></th>
                                        <th class="bg-ws"><?=$fnT('Critics')?></th>
                                        <th class="bg-ws"><?=$fnT('Not critical')?></th>
                                        <th class="bg-ws"><?=$fnT('Yellow')?></th>
                                        <th class="bg-ws"><?=$fnT('Red')?></th>
                                        <th class="bg-ws"><?=$fnT('Maintenance')?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th><?=$fnT('Average')?>:</th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>



<!---------------------------------------------------------  GRAFICAS ---------------------------------------------------------------------------------->

                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Compare with period')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="genCompare(this.value, 'ActionPlanStatus'); this.value='';">
                            <option value='' selected><?= $fnT('Period to compare') ?></option>
                            <? foreach($data['periods'] as $round => $months): ?>
                                <option value="<?= $months[0] ?>"><?= $round ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                    <div id="chart-action-plan"></div>
                </div>

                <div class="col-12 col-lg-6 mb-4" id="chart-action-completion"></div>

                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Compare with period')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="genCompare(this.value, 'Daypart'); this.value='';">
                            <option value='' selected><?= $fnT('Period to compare') ?></option>
                            <? foreach($data['periods'] as $round => $months): ?>
                                <option value="<?= $months[0] ?>"><?= $round ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                    <div id="chart-daypart"></div>
                </div>

                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Compare with period')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="genCompare(this.value, 'Weekday'); this.value='';">
                            <option value='' selected><?= $fnT('Period to compare') ?></option>
                            <? foreach($data['periods'] as $round => $months): ?>
                                <option value="<?= $months[0] ?>"><?= $round ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                    <div id="chart-weekday"></div>
                </div>

                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Compare with period')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="genCompare(this.value, 'Duration'); this.value='';">
                            <option value='' selected><?= $fnT('Period to compare') ?></option>
                            <? foreach($data['periods'] as $round => $months): ?>
                                <option value="<?= $months[0] ?>"><?= $round ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                    <div id="chart-duration"></div>
                </div>

                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Main section')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="setTopOpp(this.value)" id="select-top-opp">
                            <option value='Seguridad de alimentos' selected><?=$fnT('Food safety')?></option>
                            <option value='Limpieza' selected><?=$fnT('Cleaning')?></option>
                            <option value='Mantenimiento' selected><?=$fnT('Maintenance')?></option>
                        </select>
                    </div>
                    <div id="chart-top-opp"></div>
                </div>

                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Group by')?></span>
                        </div>
                        <select class="form-control selectpicker" id='select-groupby' onchange="setProgressStatus(this.value)">
                            <option value="Country" selected><?=$fnT('Country')?></option>
                            <option value="Quarter" selected><?=$fnT('Period')?></option>
                            <option value="Month" selected><?=$fnT('Month')?></option>
                        </select>
                    </div>
                    <div id="chart-progress-status"></div>
                </div>

                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Rating score')?></span>
                        </div>
                        <select class="selectpicker form-control" onchange="setScoreTopBottom(this.value)" id="select-score-topbutton">
                            <optgroup label="Seguridad de alimentos">
                                <option value="top|SEGURIDAD DE ALIMENTOS" selected><?=$fnT('Top 10')?></option>
                                <option value="bottom|SEGURIDAD DE ALIMENTOS"><?=$fnT('Bottom 10')?></option>
                            </optgroup>
                            <optgroup label="Limpieza">
                                <option value="top|LIMPIEZA" ><?=$fnT('Top 10')?></option>
                                <option value="bottom|LIMPIEZA"><?=$fnT('Bottom 10')?></option>
                            </optgroup>
                            <optgroup label="Mantenimiento">
                                <option value="top|MANTENIMIENTO"><?=$fnT('Top 10')?></option>
                                <option value="bottom|MANTENIMIENTO"><?=$fnT('Bottom 10')?></option>
                            </optgroup>
                        </select>
                    </div>
                    <div id="chart-score-topbutton">
                        <table class="table mt-4">
                            <thead>
                                <tr class="bg-success text-white">
                                    <th><?= $fnT('Location number') ?></th>
                                    <th><?= $fnT('Location name') ?></th>
                                    <th><?= $fnT('Score') ?></th>
                                </tr>
                            </thead>
                            <tbody id="table-score-topbutton"></tbody>
                        </table>
                    </div>
                </div>

                <div id="chart-category-trend" class="col-12 col-lg-6 mb-4"></div>
                
                <div id="chart-question-trend" class="col-12 col-lg-6 mb-4"></div>


<!--------------------------------------------------------- FIN GRAFICAS ---------------------------------------------------------------------------------->


<!--------------------------------------------------------- DESCARGABLES EXCEL ---------------------------------------------------------------------------->

                <div class="col-12 col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?=$fnT('Downloads')?></h5> <br>
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportAuditGeneal', '<?=$fnT('General audit report')?>')">
                                <?=$fnT('General audit report')?>
                            </button>    
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportCompletedAudits', '<?=$fnT('Completed audits')?>')">
                                <?=$fnT('Completed audits')?>
                            </button>    
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportFrequencyOpp', '<?=$fnT('Frequency of opportunities')?>')">
                                <?=$fnT('Frequency of opportunities')?>
                            </button>    
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportAddQuestions', '<?=$fnT('Additional Questions')?>')">
                                <?=$fnT('Additional Questions')?>
                            </button>    
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportReportOpp', '<?=$fnT('Opportunity report')?>')">
                                <?=$fnT('Opportunity report')?>
                            </button>
                            
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportCalidadDq', '<?=$fnT('CALIDAD DQ')?>')">
                                <?=$fnT('CALIDAD DQ')?>
                            </button>   

                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportActionPlan', '<?=$fnT('Action plan')?>')">
                                <?=$fnT('Action plan')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportAppealItems', '<?=$fnT('Appeals')?>')">
                                <?=$fnT('Appeals')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportOppPerSection', '<?=$fnT('Opp per section')?>')">
                                <?=$fnT('Opp per section')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportOppPerAuditor', '<?=$fnT('Opp per auditor')?>')">
                                <?=$fnT('Opp per auditor')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportAuditorSurvey', '<?=$fnT('Auditor survey')?>')">
                                <?=$fnT('Auditor survey')?>
                            </button>
                            <? if(in_array($_SESSION['userData']['role']['id'], [1])): ?>
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportCerttis', '<?=$fnT('Certtis')?>')">
                                <?=$fnT('Certtis')?>
                            </button>
                            <? endif ?>
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportPending', '<?=$fnT('Visit Pending')?>')">
                                <?=$fnT('Visit Pending')?>
                            </button>  

                            <? if(in_array($_SESSION['userData']['role']['id'], [17,1])): ?>

                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportUserPass', '<?=$fnT('Restaurants Without Access')?>')">
                                <?=$fnT('Restaurants Without Access')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportUserLogin', '<?=$fnT('Login report')?>')">
                                <?=$fnT('Login report')?>
                            </button>
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportLayoutReport', '<?=$fnT('Layout report')?>')">
                                <?=$fnT('Layout report')?>
                            </button>

                            <a class="btn btn-primary mr-2 mb-2" href="<?=base_url()?>/usuariosTienda">
                                <span class="app-menu__label"><?=$fnT('User report')?></span>
                            </a>
                            <? endif ?>

                            <button class="btn btn-success mr-2 mb-2" onclick="window.open('<?= base_url() ?>/audits/auditPrint/M3lONXlzczgvSW1oa0xYc0NYWU13Zz09', '_blank')">
                                <?=$fnT('Print checklist')?>
                            </button> 
                        </div>
                    </div>
                </div>

<!--------------------------------------------------------- DESCARGABLES EXCEL ---------------------------------------------------------------------------->

            </div>
        </div>
    </div>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<script>
    const getScoreDef = score => {
        const def = <?=json_encode(getScoreDefinition())?>;
        return score? def[score] : def;
    }



    
</script>
<?php footerTemplate($data);?>