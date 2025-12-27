<?php 
    headerTemplate($data);
    getModal('modalAppeal',$data);
    global $fnT;
?>
<style>
    .input-in-btn input{
        opacity: 0;
        width: 90px;
        height: 30px;
        position: absolute;
        z-index: 2;
        top: 0;
        left: 0;
    }
    
    span.input-in-btn{
        position: relative;
    }
</style>
<div id="contentAjax"></div>
<main class="app-content">
    <input type="hidden" id="tipoR" value="<?=$_SESSION['userData']['role']['name']?>">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
                <!-- Definir que roles pueden crear apelaciones -->
                <? if( in_array( $_SESSION['userData']['role']['id'], [1, 2, 10,20,18,19] ) ): ?>
                    <button class="btn btn-md btn-primary" type="button" onclick="openModalNew();"><i class="fa fa-plus-circle" aria-hidden="true"></i><?=$fnT('New appeal')?></button>
                <? endif ?>
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$fnT($data['page_title'])?></a></li>
        </ul>
    </div>
    
    <div class="tile">
        <div class="tile-body">
            <div class="form-row">
                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Round')?></span>
                        </div>
                        <select class="form-control selectpicker" id="fRound" name="fRound" multiple data-actions-box="true" data-selected-text-format="count>1">
                            <? foreach($data['rounds'] as $id => $r): ?>
                                <option value="<?=$r['name']?>" selected><?=$r['name']?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Store')?></span>
                        </div>
                        <select class="form-control selectpicker" id="fStore" name="fStore" multiple data-actions-box="true" data-selected-text-format="count>1">
                            <? foreach($data['stores'] as $id => $sto): ?>
                                <option value="<?=$sto['location_id']?>" selected><?=$sto['name']?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Status')?></span>
                        </div>
                        <select class="form-control selectpicker" id="fStatus" name="fStatus" multiple data-actions-box="true" data-selected-text-format="count>1">
                            <? foreach($data['status'] as $st): ?>
                                <option value="<?=$st?>" selected><?=$st?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 my-1">
                    <button id="btnFilterAnnouncedVisit" class="form-control btn btn-primary" type="button" onclick="recargaDTAppeals();">
                        <i class="fa fa-filter" aria-hidden="true"></i>&nbsp;&nbsp;
                        <?=$fnT('Filter')?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tableOpps">
                    <thead>
                        <tr>
                            <th><?=$fnT('Id')?></th>
                            <th><?=$fnT('Store')?></th>
                            <th><?=$fnT('Appeals')?></th>
                            <th><?=$fnT('Options')?></th>
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
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>