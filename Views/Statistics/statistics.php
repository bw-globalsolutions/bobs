<?php 
    headerTemplate($data);
    getModal('modalStatisticsCompare',$data);
    global $fnT;
?>
<link rel="stylesheet" type="text/css" href="<?=media()?>/css/statistics.css">
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
                <i class="fa fa-pie-chart" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Visualize as informações coletadas em forma de gráficos')?></p>
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
                                <span class="input-group-text border-0"><?=$fnT('Tipo')?></span>
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
                                <span class="input-group-text border-0"><?=$fnT('Período')?></span>
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
                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Países')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_countrys" name="list_countrys[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['countrys'] as $f): ?>
                                    <option value="<?=$f['id']?>" selected><?=$f['name']?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Nome da entidade subfranquia')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_subF" name="list_subF[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['subF'] as $f): ?>
                                    <option value="<?=$f['sub_franchise_entity_name']?>" selected><?=$f['sub_franchise_entity_name']?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Líder de mercado')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_ml" name="list_ml[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['ml'] as $f): ?>
                                    <option value="<?=$f['location_id']?>" selected><?=$f['name']?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Loja')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_franchise" name="list_franchise[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['franchissees'] as $f): ?>
                                    <option value="<?=$f['name']?>" selected><?=$f['name']?></option>
                                <? endforeach ?>
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
                            <?=$fnT('Filtrar')?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
                                        <th colspan="8" class="bg-ws"><?=$fnT('Pontuações médias')?></th>
                                    </tr>
                                    <tr>
                                        <th><?=$fnT('Loja')?></th>
                                        <th><?=$fnT('Visitas')?></th>
                                        <th><?=$fnT('Falhas automáticas')?></th>
                                        <th class="bg-ws"><?=$fnT('Segurança alimentar')?></th>
                                        <th class="bg-ws"><?=$fnT('Excelência operacional')?></th>
                                        <th class="bg-ws"><?=$fnT('Pontuação geral')?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th><?=$fnT('Média')?>:</th>
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
                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Comparar com o período')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="genCompare(this.value, 'ActionPlanStatus'); this.value='';">
                            <option value='' selected><?= $fnT('Período a comparar') ?></option>
                            <? foreach($data['periods'] as $key=>$period){ 
                                $val = implode(',', array_map(function($v) {
                                    return "'" . $v . "'";
                                }, $period)); ?>
                                <option value="<?=$val?>"><?=$key?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div id="chart-action-plan"></div>
                </div>
                <div class="col-12 col-lg-6 mb-4" id="chart-action-completion"></div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Comparar com o período')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="genCompare(this.value, 'Daypart'); this.value='';">
                            <option value='' selected><?= $fnT('Período a comparar') ?></option>
                            <? foreach($data['periods'] as $key=>$period){ 
                                $val = implode(',', array_map(function($v) {
                                    return "'" . $v . "'";
                                }, $period)); ?>
                                <option value="<?=$val?>"><?=$key?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div id="chart-daypart"></div>
                </div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Comparar com o período')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="genCompare(this.value, 'Weekday'); this.value='';">
                            <option value='' selected><?= $fnT('Período a comparar') ?></option>
                            <? foreach($data['periods'] as $key=>$period){ 
                                $val = implode(',', array_map(function($v) {
                                    return "'" . $v . "'";
                                }, $period)); ?>
                                <option value="<?=$val?>"><?=$key?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div id="chart-weekday"></div>
                </div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Comparar com o período')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="genCompare(this.value, 'Duration'); this.value='';">
                            <option value='' selected><?= $fnT('Período a comparar') ?></option>
                            <? foreach($data['periods'] as $key=>$period){ 
                                $val = implode(',', array_map(function($v) {
                                    return "'" . $v . "'";
                                }, $period)); ?>
                                <option value="<?=$val?>"><?=$key?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div id="chart-duration"></div>
                </div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Seção principal')?></span>
                        </div>
                        <select class="form-control selectpicker" onchange="setTopOpp(this.value)" id="select-top-opp">
                            <option value='Food safety' selected><?=$fnT('Segurança alimentar')?></option>
                            <option value='Operations excellence' selected><?=$fnT('Excelência operacional')?></option>
                        </select>
                    </div>
                    <div id="chart-top-opp"></div>
                </div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Agrupar por')?></span>
                        </div>
                        <select class="form-control selectpicker" id='select-groupby' onchange="setProgressStatus(this.value)">
                            <option value="Country" selected><?=$fnT('País')?></option>
                            <option value="Quarter" selected><?=$fnT('Período')?></option>
                            <option value="Month" selected><?=$fnT('Mês')?></option>
                        </select>
                    </div>
                    <div id="chart-progress-status"></div>
                </div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Pontuação')?></span>
                        </div>
                        <select class="selectpicker form-control" onchange="setScoreTopBottom(this.value)" id="select-score-topbutton">
                            <optgroup label="Food Safety">
                                <option value="top|Food Safety" selected><?=$fnT('10 melhores')?></option>
                                <option value="bottom|Food Safety"><?=$fnT('10 piores')?></option>
                            </optgroup>
                            <optgroup label="Operations Excellence">
                                <option value="top|Operations Excellence" ><?=$fnT('10 melhores')?></option>
                                <option value="bottom|Operations Excellence"><?=$fnT('10 piores')?></option>
                            </optgroup>
                        </select>
                    </div>
                    <div id="chart-score-topbutton">
                        <table class="table mt-4">
                            <thead>
                                <tr class="bg-success text-white">
                                    <th><?= $fnT('Número da localização') ?></th>
                                    <th><?= $fnT('Nome da localização') ?></th>
                                    <th><?= $fnT('Pontuação') ?></th>
                                </tr>
                            </thead>
                            <tbody id="table-score-topbutton"></tbody>
                        </table>
                    </div>
                </div>
                <div id="chart-category-trend" class="col-12 col-lg-6 mb-4"></div>
                <div id="chart-question-trend" class="col-12 col-lg-6 mb-4"></div>
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?=$fnT('Downloads')?></h5> <br>
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportAuditGeneal', '<?=$fnT('Relatório geral de auditoria')?>')">
                                <?=$fnT('Relatório geral de auditoria')?>
                            </button>    
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportCompletedAudits', '<?=$fnT('Auditorias concluídas')?>')">
                                <?=$fnT('Auditorias concluídas')?>
                            </button>    
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportFrequencyOpp', '<?=$fnT('Frequência de oportunidades')?>')">
                                <?=$fnT('Frequência de oportunidades')?>
                            </button>    
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportAddQuestions', '<?=$fnT('Perguntas adicionais')?>')">
                                <?=$fnT('Perguntas adicionais')?>
                            </button>    
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportReportOpp', '<?=$fnT('Relatório de oportunidade')?>')">
                                <?=$fnT('Relatório de oportunidade')?>
                            </button>   
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportActionPlan', '<?=$fnT('Plano de ação')?>')">
                                <?=$fnT('Plano de ação')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportAppealItems', '<?=$fnT('Apelações')?>')">
                                <?=$fnT('Apelações')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportOppPerSection', '<?=$fnT('Op por seção')?>')">
                                <?=$fnT('Op por seção')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportOppPerAuditor', '<?=$fnT('Op por auditor')?>')">
                                <?=$fnT('Op por auditor')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportAuditorSurvey', '<?=$fnT('Pesquisa do auditor')?>')">
                                <?=$fnT('Pesquisa do auditor')?>
                            </button>
                            <? if(in_array($_SESSION['userData']['role']['id'], [1])): ?>
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportCerttis', '<?=$fnT('Certtis')?>')">
                                <?=$fnT('Certtis')?>
                            </button>
                            <? endif ?>
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportPending', '<?=$fnT('Visita pendente')?>')">
                                <?=$fnT('Visita pendente')?>
                            </button>  


                            <? if(in_array($_SESSION['userData']['role']['id'], [17,1])): ?>
                       
                     


                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportUserPass', '<?=$fnT('Restaurantes sem acesso')?>')">
                                <?=$fnT('Restaurantes sem acesso')?>
                            </button> 
                            <button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportUserLogin', '<?=$fnT('Relatório de login')?>')">
                                <?=$fnT('Relatório de login')?>
                            </button>
                            <!--<button class="btn btn-primary mr-2 mb-2" onclick="getExportable('exportLayoutReport', '<?=$fnT('Relatório de layout')?>')">
                                <?=$fnT('Relatório de layout')?>
                            </button>-->
                            

                            <a class="btn btn-primary mr-2 mb-2" href="<?=base_url()?>/usuariosTienda">
                                <span class="app-menu__label"><?=$fnT('Relatório de usuários')?></span>
                            </a>
                            <? endif ?>

                            <button class="btn btn-success mr-2 mb-2" onclick="window.open('<?= base_url() ?>/audits/auditPrint/M3lONXlzczgvSW1oa0xYc0NYWU13Zz09', '_blank')">
                                <?=$fnT('Imprimir checklist')?>
                            </button> 
                        </div>
                    </div>
                </div>
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