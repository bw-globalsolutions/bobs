<?php 
    headerTemplate($data);    
    $filter = empty($_GET['filter'])? false : base64_decode($_GET['filter']);
$countryName = $data['country_name'];
    $data['months'] = array(
        '01' 	=> "January",
        '02' 	=> "February",
        '03' 	=> "March",
        '04' 	=> "April",
        '05' 	=> "May",
        '06' 	=> "June",
        '07' 	=> "July",
        '08' 	=> "August",
        '09' 	=> "September",
        '10' 	=> "October",
        '11' 	=> "November",
        '12' 	=> "December"
    );
    global $fnT;
?>
<main class="app-content">
    <style>
    .score-box {
        display: inline-block;
        padding: 8px 16px;
        margin: 4px 0;
        border-radius: 6px;
        font-weight: bold;
        color: #fff;
        width: 100%;
    }
    .pass { background-color: #28a745; } /* Verde */
    .fail { background-color: #dc3545; } /* Rojo */
</style>

<div>
    <?php //if($_SESSION['userData']['id'] == 1) { //dep($_SESSION['userData']); } ?>
    <div class="app-title">
        <div>
            <h1>
            <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Consult and filter the audits')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <div class="tile">
        <div class="tile-body">
            <form onsubmit="applyFilter(); return false;" id="form-filter">
                <div class="form-row">
                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Period')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_rname" multiple data-actions-box="true" data-selected-text-format="count>1" required>
                                <? foreach($data['round_name'] as $round): ?>
                                    <option value="<?=$round?>" selected><?=$round?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Location')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_location" multiple data-selected-text-format="count>2" data-live-search="<?= count($data['audit_location']) > 4? 'true' : 'false' ?>" data-actions-box="true" required>
                                <? foreach($data['audit_location'] as $nb => $ad): ?>
                                    <option value="<?=$nb?>" selected><?=$nb?> - <?=$ad?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 my-1">
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
                        <div class="col-lg-3 my-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-0"><?=$fnT('Auditor')?></span>
                                </div>
                                <select class="form-control selectpicker" id="filter_aemail" multiple data-actions-box="true" data-selected-text-format="count>2" required  data-live-search = "true">
                                    <? foreach($data['auditor_email'] as $aemail): ?>
                                        <option value="<?=$aemail?>" selected><?=$aemail?></option>
                                    <? endforeach ?>
                                </select>
                            </div>
                        </div> 
                    <? endif ?>

<? if(in_array( $_SESSION['userData']['role']['id'], [14,19,18] )): ?>

<!-- EMAIL AREA MANAGER -->
<div class="col-lg-3 my-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Area Manager')?></span>
        </div>
        <select class="form-control selectpicker" id="filter_email_area_manager" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
            <? foreach($data['email_area_manager'] as $email_area_manager): ?>
                <? $val = !empty($email_area_manager) ? $email_area_manager : 'N/A'; ?>
                <option value="<?= $email_area_manager ?>" selected><?= $val ?></option>
            <? endforeach ?>
        </select>
    </div>
</div>
      <? endif ?>
<? if(in_array( $_SESSION['userData']['role']['id'], [1,2,17] )): ?>
                       


<!-- NEEW FILTERS -->
                        
                  

<!-- SHOP TYPE -->
<div class="col-lg-3 my-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Shop Type')?></span>
        </div>
        <select class="form-control selectpicker" id="filter_shop_type" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
            <? foreach($data['shop_type'] as $shop_type): ?>
                <? $val = !empty($shop_type) ? $shop_type : 'N/A'; ?>
                <option value="<?= $shop_type ?>" selected><?= $val ?></option>
            <? endforeach ?>
        </select>
    </div>
</div>

<!-- COUNTRY -->
<div class="col-lg-3 my-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Country')?></span>
        </div>
        <select class="form-control selectpicker" id="filter_country" multiple data-actions-box="true" data-selected-text-format="count>2" required>
            <? foreach($data['country'] as $country): ?>
                <? $val = !empty($country) ? $country : 'N/A'; ?>
                <option value="<?= $country ?>" selected><?= $val ?></option>
            <? endforeach ?>
        </select>
    </div>
</div>

<!-- FRANCHISSES -->
<div class="col-lg-3 my-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Franchisee')?></span>
        </div>
        <select class="form-control selectpicker" id="filter_franchissees_name" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
            <? foreach($data['franchissees_name'] as $franchissees_name): ?>
                <? $val = !empty($franchissees_name) ? $franchissees_name : 'N/A'; ?>
                <option value="<?= $franchissees_name ?>" selected><?= $val ?></option>
            <? endforeach ?>
        </select>
    </div>
</div>

<!-- AREA -->
<div class="col-lg-3 my-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Area')?></span>
        </div>
        <select class="form-control selectpicker" id="filter_area" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">                             
        <? foreach($data['area'] as $area): ?>
            <? $val = !empty($area) ? $area : 'N/A'; ?>
            <option value="<?= $area ?>" selected><?= $val ?></option>
        <? endforeach ?>
        </select>
    </div>
</div>

<!-- CONCEPT -->
<div class="col-lg-3 my-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Concept')?></span>
        </div>
        <select class="form-control selectpicker" id="filter_concept" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
            <? foreach($data['concept'] as $concept): ?>
                <? $val = !empty($concept) ? $concept : 'N/A'; ?>
                <option value="<?= $concept ?>" selected><?= $val ?></option>
            <? endforeach ?>
        </select>
    </div>
</div>

<!-- EMAIL AREA MANAGER -->
<div class="col-lg-3 my-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Area Manager')?></span>
        </div>
        <select class="form-control selectpicker" id="filter_email_area_manager" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
            <? foreach($data['email_area_manager'] as $email_area_manager): ?>
                <? $val = !empty($email_area_manager) ? $email_area_manager : 'N/A'; ?>
                <option value="<?= $email_area_manager ?>" selected><?= $val ?></option>
            <? endforeach ?>
        </select>
    </div>
</div>

<!-- Escalation 1 -->
<div class="col-lg-3 my-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Escalation 1')?></span>
        </div>
        <select class="form-control selectpicker" id="filter_email_ops_leader" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
            <? foreach($data['email_ops_leader'] as $email_ops_leader): ?>
                <? $val = !empty($email_ops_leader) ? $email_ops_leader : 'N/A'; ?>
                <option value="<?= $email_ops_leader ?>" selected><?= $val ?></option>
            <? endforeach ?>
        </select>
    </div>
</div>

<!-- Escalation 2 -->
<div class="col-lg-3 my-1">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text border-0"><?=$fnT('Escalation 2')?></span>
        </div>
        <select class="form-control selectpicker" id="filter_email_ops_director" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
            <? foreach($data['email_ops_director'] as $email_ops_director): ?>
                <? $val = !empty($email_ops_director) ? $email_ops_director : 'N/A'; ?>
                <option value="<?= $email_ops_director ?>" selected><?= $val ?></option>
            <? endforeach ?>
        </select>
    </div>
</div>




                    <? endif ?>
                     
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



    <pre><?php print_r($data['country_name']) ?></pre>



    <div class="row">
        <div class="col-lg-3 my-1">
            <div class="tile">
                <div class="tile-body">
                    <ul class="app-menu pb-0">
                        <? foreach($data['types'] as $type): ?>
                            <li><a class="app-menu__item <?=$type['type']==$data['type']? 'selected' : ''?>" href="<?=base_url() . '/audits?type=' . base64_encode($type['type']). '&country=' . base64_encode($countryName) ?>"><?=$fnT($type['type'])?></a></li>
                        <? endforeach ?>
                    </ul>
                </div>

            </div>
            
            <!-- Role General Manager(tienda) -> botón SelfAudits -->
            <? if(!empty($data['locations']) || in_array( $_SESSION['userData']['role']['id'], [1,2,17] )): ?>
                <select class="selectpicker" title="<?=$fnT('Generate Self-evaluation')?>" data-live-search="<?= count($data['locations']) > 4? 'true' : 'false' ?>" data-style="btn-warning" data-width="100%" onchange="generarAutoEval(this.value)" id="selectSE">
                    <? foreach($data['locations'] as $location): ?>
                        <option value="<?=$location['id']?>" data-country="<?=$location['country_id']?>">#<?= $location['number'] ?> - <?= $location['name'] ?></option>
                    <? endforeach ?>
                </select>
            <? endif ?>
<br>
<br>
            <!-- Role General Manager(tienda) -> botón SelfAudits -->
            <? if(in_array( $_SESSION['userData']['role']['id'], [1,2,17] )): ?>
                <select class="selectpicker" title="<?=$fnT('Generate IDQ Internal Audit')?>" data-live-search="<?= count($data['locations']) > 4? 'true' : 'false' ?>" data-style="btn-primary" data-width="100%" onchange="generarIDQ(this.value)" id="selectSE">
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
                            <input class="form-control rounded" id="filter_search" placeholder="<?=$fnT('Search')?>" onkeyup="searchString(this.value)">
                            <span class="input-group-text border-0 bg-transparent" id="search-addon">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                        <p>(<b id="count"><?=count($data['audit_list'])?></b>)</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <? if(count($data['audit_list'])): ?>
                            <? foreach($data['audit_list'] as $audit): ?>
                                <li class="list-group-item audit-item" 
                                    data-rname="<?=$audit['round_name']?>"
                                    data-period="<?= substr($audit['period'], -2) ?>" 
                                    data-lnumber="<?=$audit['location_number']?>" 
                                    data-status="<?=$audit['status']?>" 
                                    data-lname="<?=$audit['location_name']?>" 
                                    data-aname="<?=$audit['auditor_name']?>" 
                                    data-aemail="<?=$audit['auditor_email']?>" 
                                   
                                    data-acountry="<?=$audit['country_name']?>" 
                                    data-farea="<?=$audit['area']?>" 
                                    data-fconcept="<?=$audit['concept']?>" 
                                    data-ffranchissees="<?=$audit['franchissees_name']?>" 
                                    data-fareamanager="<?=$audit['email_area_manager']?>" 
                                   
                                    data-id="<?=$audit['id']?>"
                                    data-shoptype="<?=$audit['shop_type']?>"  
                                    data-emailopsdirector="<?=$audit['email_ops_director']?>"  
                                    data-emailopsleader="<?=$audit['email_ops_leader']?>"  
                                    >

                                    
                                    
                                   
                                    <div class="row">
                                        <div class="col-md-5 col-sm-12 p-1">
                                            <span class="badge badge-info"><?=$audit['brand_prefix']?></span> <b class="text-success"> - #<?=$audit['location_number']?> <?=$audit['location_name']?></b>
                                            <br><br>
                                            <span style="font-size: 13px;">
                                                <?=$fnT('Number')?>: <b><?=$audit['location_number']?></b><br>
                                                <?=$fnT('Name')?>: <b><?=$audit['location_name']?></b><br>
                                                <?=$fnT('Country')?>: <b><?=$audit['country_name']?></b><br>
                                                <?=$fnT('Region')?>: <b><?=$audit['region']?></b><br><br>

                                             
                                                <?=$fnT('Concept')?>: <b><?=$audit['concept']?></b><br>
                                                <?=$fnT('Shop Type')?>: <b><?=$audit['shop_type']?></b><br>
                                                <?=$fnT('Area')?>: <b><?=$audit['area']?></b><br>
                                                <?=$fnT('Franchisees Name')?>: <b><?=$audit['franchissees_name']?></b><br><br>

                                                <?=$fnT('Brand')?>: <b><?=$audit['brand_name']?></b><br>
                                                <?=$fnT('Round')?>: <b><?=$audit['round_name']?></b><br>
                                                <?=$fnT('Month')?>: <b><?= $audit['period']==''? 'NA' : substr($audit['period'], -2) ?></b><br>

                  </span>
                                        </div>
                                        <div class="col-md-7 col-sm-12">
                                            <div class="bg-light">
                                                <div class="card-body">
                                                    <span><?=$fnT('Audit')?>:</span>
                                                    <span class="badge badge-dark float-right">#<?=$audit['id']?></span>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <span style="font-size: 13px;">
                                                                <?=$fnT('Status')?>: <b><?=$fnT($audit['status'])?></b><br>
                                                                <?=$fnT('Date of visit')?>: <b><?=$fnT($audit['date_visit'])?? $fnT('No registration')?></b><br>
                                                                <?=$fnT('Auditor')?>: <b data-toggle="tooltip" data-placement="top" title="<?=$audit['auditor_email']?>"><?=$audit['auditor_name']?></b><br>
                                                                <?=$fnT('Audit type')?>: <b><?= $fnT($data['type']) ?></b><br>
                                                                <b class="text-secondary"><?=$audit['local_foranea']?></b><br><br>
                                                             
                                                                
                                                                <? if ($audit['audit_rojo'] > 6): $cpc = 'Fail';  else: $cpc = 'Pass';  endif; ?>
                                                                <? if ($audit['audit_criticos'] > 1): $fspc = 'Fail'; $color = 'danger';  else: $fspc = 'Pass'; $color = 'success'; endif; ?>


                                                                <b><?=$fnT('FSPC')?>: <?=$fspc?></b><br>
                                                                <b><?=$fnT('CPC')?>: <?= $cpc?></b><br>
                                                                <b><?=$fnT('Overall Score')?>: <?=$audit['audit_result']?></b><br>
                                                              
                                                                
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="col-md-4 d-flex justify-content-end align-items-end" style="font-size: 25px;">
                                                            <a href="<?=base_url()?>/audits/audit?id=<?=$audit['id']?>" class="mr-3"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 col-sm-12 p-1">
                                          
                                            <span style="font-size: 13px;">
                                                <?=$fnT('Area Manager')?>: <b><?=$audit['email_area_manager']?></b><br>
                                                <?=$fnT('Escalation 1')?>: <b><?=$audit['email_ops_leader']?></b><br>
                                                <?=$fnT('Escalation 2')?>: <b><?=$audit['email_ops_director']?></b><br>
                                            </span>



                                            

                                        </div>
                                        </div>
                                    </div>
                                </li>
                            <? endforeach ?>
                        <? else: ?>
                            <h3 class="m-4"><?=$fnT('No audits to show')?></h3>
                        <? endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<script>
    var audit_type = '<?=$data['type']?>';
    var setCountries = <?=json_encode($data['country_location'])?>;
</script>
<?php footerTemplate($data);?>