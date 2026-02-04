<?php 
    headerTemplate($data);    
    $filter = empty($_GET['filter'])? false : base64_decode($_GET['filter']);

    $data['months'] = array(
        '01' 	=> "Janeiro",
        '02' 	=> "Fevereiro",
        '03' 	=> "Marchar",
        '04' 	=> "Abril",
        '05' 	=> "Poderia",
        '06' 	=> "Junho",
        '07' 	=> "Julho",
        '08' 	=> "Agosto",
        '09' 	=> "Setembro",
        '10' 	=> "Nutubro",
        '11' 	=> "Novembro",
        '12' 	=> "Dezembro"
    );
    global $fnT;
?>
<div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
  <main class="app-content">
    <?php //if($_SESSION['userData']['id'] == 1) { //dep($_SESSION['userData']); } ?>
    <div class="app-title">
        <div>
            <h1>
            <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Consultar e filtrar as auditorias')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <div class="tile filtroFix" style="padding: 20px 20px 9px;">
        <div class="tile-body">
            <form onsubmit="applyFilter(); return false;" id="form-filter" style="margin:0;">
                <div class="form-row">
                    <div class="col-lg-3 my-1" style="flex: 0 0 20%; max-width:20%;">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Período')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_rname" multiple data-actions-box="true" data-selected-text-format="count>1" required>
                                <? foreach($data['round_name'] as $round): ?>
                                    <option value="<?=$round?>" selected><?='ciclo'.explode('Round', $round)[1]?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 my-1" style="flex: 0 0 20%; max-width:20%;">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Localização')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_location" multiple data-selected-text-format="count>2" data-live-search="<?= count($data['audit_location']) > 4? 'true' : 'false' ?>" data-actions-box="true" required>
                                <? foreach($data['audit_location'] as $nb => $ad): ?>
                                    <option value="<?=$nb?>" selected><?=$nb?> - <?=$ad?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 my-1" style="flex: 0 0 20%; max-width:20%;">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Status')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_status" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                <? foreach($data['status'] as $status => $count): ?>
                                    <option value="<?=$status?>" <?=$filter?($filter!=$status?'':'selected'):'selected'?>><?=$fnT($status)?> (<?=$count?>)</option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>  

                    <? if(in_array( $_SESSION['userData']['role']['id'], [1,2] )): ?>
                        <div class="col-lg-3 my-1" style="flex: 0 0 20%; max-width:20%;">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-0"><?=$fnT('Auditor')?></span>
                                </div>
                                <select class="form-control selectpicker" id="filter_aemail" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                                    <? foreach($data['auditor_email'] as $aemail): ?>
                                        <option value="<?=$aemail?>" selected><?=$aemail?></option>
                                    <? endforeach ?>
                                </select>
                            </div>
                        </div> 
                    <? endif ?>
                     
                    <div class="col-lg-3 my-1" style="flex: 0 0 20%; max-width:20%;">
                        <button type="submit" class="btn btn-s1">
                            <i class="fa fa-filter" aria-hidden="true"></i>&nbsp;&nbsp;
                            <?=$fnT('Filtrar')?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row" style="margin-top: 70px;">
        <div class="col-lg-3 my-1">
            <div class="tile">
                <div class="tile-body">
                    <ul class="app-menu pb-0">
                        <? foreach($data['types'] as $type): ?>
                            <li><a style="display:flex; justify-content:space-between;" class="app-menu__item <?=$type['type']==$data['type']? 'selected' : ''?>" href="<?=base_url() . '/audits?type=' . base64_encode($type['type'])?>"><span><?if($type['type']=="Self-Evaluation"){echo "Autoavaliação";}else if($type['type']=="Calibration Audit"){echo "Auditoria de Calibração";}else if($type['type']=="Training-visits"){echo "visitas de treinamento";}else if($type['type']=="Standard"){echo "Auditoria Operativa";}?></span> <b class="cantidad"><?=$data[$type['type']]?></b></a></li>
                        <? endforeach ?>
                    </ul>
                </div>

            </div>
            
            <!-- Role General Manager(tienda) -> botón SelfAudits -->
            <? if(!empty($data['locations']) || in_array( $_SESSION['userData']['role']['id'], [1,2,17] )): ?>
                <select class="selectpicker" title="<?=$fnT('Gerar autoavaliação')?>" data-live-search="<?= count($data['locations']) > 4? 'true' : 'false' ?>" data-style="btn-warning" data-width="100%" onchange="generarAutoEval(this.value)" id="selectSE">
                    <? foreach($data['locations'] as $location): ?>
                        <option value="<?=$location['id']?>" data-country="<?=$location['country_id']?>">#<?= $location['number'] ?> - <?= $location['name'] ?></option>
                    <? endforeach ?>
                </select>
            <? endif ?>
            
        </div>
        <div class="col-lg-9">
            <div class="tile">
                <div class="tile-body">
                    <div class="d-flex justify-content-between">
                        <div></div>
                        <div class="input-group rounded mb-3" style="width: 270px;">
                            <input class="input-s1" id="filter_search" style="padding: 5px 5px 5px 23px; box-shadow: 4px 5px 8px 0 #e2dddd;" placeholder="<?=$fnT('Pesquisar')?>" onkeyup="searchString(this.value)">
                            <div class="drop-icon" style="position:absolute; top:-7px; left:-40px">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M11 2a9 9 0 1 0 5.618 16.032l3.675 3.675a1 1 0 0 0 1.414-1.414l-3.675-3.675A9 9 0 0 0 11 2m-6 9a6 6 0 1 1 12 0a6 6 0 0 1-12 0" clip-rule="evenodd"/></svg>
                            </div>
                        </div>
                        <p class="lbl-s2" style="box-shadow: 4px 5px 8px 0 #e2dddd;"><b id="count"><?=($data['audit_list']?count($data['audit_list']):0)?></b></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <? if($data['audit_list']): ?>
                            <? foreach($data['audit_list'] as $audit): ?>
                                <li class="list-group-item audit-item" 
                                    data-rname="<?=$audit['round_name']?>"
                                    data-period="<?= substr($audit['period'], -2) ?>" 
                                    data-lnumber="<?=$audit['location_number']?>" 
                                    data-status="<?=$audit['status']?>" 
                                    data-lname="<?=$audit['location_name']?>" 
                                    data-aname="<?=$audit['auditor_name']?>" 
                                    data-aemail="<?=$audit['auditor_email']?>" 
                                    data-id="<?=$audit['id']?>">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-12 p-1">
                                            <span class="badge badge-info"><?=$audit['brand_prefix']?></span> <b class="text-success"> - #<?=$audit['location_number']?> <?=$audit['location_name']?></b>
                                            <br><br>
                                            <span style="font-size: 13px;">
                                                <div class="lbl-s3" style="max-width: 180px; background:none; color:var(--color3);">
                                                    <span><?=$fnT('Número')?>: <b><?=$audit['location_number']?></b></span>
                                                    <span><?=$fnT('Nome')?>: <b><?=$audit['location_name']?></b></span>
                                                    <span><?=$fnT('País')?>: <b><?=$audit['country_name']?></b></span>
                                                    <span><?=$fnT('Região')?>: <b><?=$audit['region']?></b></span>
                                                </div>
                                                <div class="lbl-s1" style="max-width: 180px; margin-top:10px;">
                                                    <span><?=$fnT('Marca')?>: <b><?=$audit['brand_name']?></b></span>
                                                    <span><?=$fnT('Rodada')?>: <b><?='ciclo'.explode('Round', $audit['round_name'])[1]?></b></span>
                                                    <span><?=$fnT('Mês')?>: <b><?= $audit['period']==''? 'NA' : substr($audit['period'], -2) ?></b></span>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="col-md-7 col-sm-12">
                                            <div class="bg-light">
                                                <div class="card-body">
                                                    <div class="flexsb">
                                                        <span><?=$fnT('Auditoria')?>:</span>
                                                        <div>
                                                            <span onclick="copiarAlPortapapeles('<?=base_url()?>/audits/audit?id=<?=$audit['id']?>')" style="cursor:pointer;" class="badge badge-dark float-right">#<?=$audit['id']?></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <span style="font-size: 13px;">
                                                                <?=$fnT('Status')?>: <b style="margin-left:5px;"><?if($audit['status']=="In Process"){echo "Em andamento";}else if($audit['status']=="Completed"){echo "Concluída";}else if($audit['status']=="Pending"){echo "Pendente";}else if($audit['status']=="Deleted!"){echo "Excluída!";}?></b><br>
                                                                <?=$fnT('Data da visita')?>: <b><?=$fnT($audit['date_visit'])?? $fnT('Sem registro')?></b><br>
                                                                <?=$fnT('Auditor')?>: <b data-toggle="tooltip" data-placement="top" title="<?=$audit['auditor_email']?>"><?=$audit['auditor_name']?></b><br>
                                                                <?=$fnT('Tipo de auditoria')?>: <b><?if($data['type']=="Self-Evaluation"){echo "Autoavaliação";}else if($data['type']=="Calibration Audit"){echo "Auditoria de Calibração";}else if($data['type']=="Training-visits"){echo "visitas de treinamento";}else if($data['type']=="Standard"){echo "Auditoria Operativa";}?></b><br>
                                                                <div <?=($audit['autofails']>0?'class="lbl-s4" style="max-width: 100px; margin-top: 5px; align-items:center;"':'')?>><span><?=$fnT('Falhas automáticas')?>: <b><?= $audit['autofails'] ?></b></span></div>
                                                                
                                                            </span>
                                                        </div>
                                                        <div class="col-md-4 d-flex justify-content-end align-items-end" style="font-size: 25px;">
                                                            <a href="<?=base_url()?>/audits/audit?id=<?=$audit['id']?>" class="mr-3"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <? endforeach ?>
                        <? else: ?>
                            <h3 class="m-4"><?=$fnT('Nenhuma auditoria para mostrar')?></h3>
                        <? endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <a class="back-to-top" style="bottom:63px;"><i class="fa fa-arrow-up"></i></a>
    <button onclick="abrirAdd(event)" class="btnAddSelf"><i class="fa fa-plus"></i></button>
</main>
<script>
    var audit_type = '<?=$data['type']?>';
    var setCountries = <?=json_encode($data['country_location'])?>;
</script>
<?php footerTemplate($data);?>