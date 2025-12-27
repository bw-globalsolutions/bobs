<?php 
    headerTemplate($data);
    global $fnT;
    $status = array_filter(['Pending', 'In Process', 'Completed', 'Deleted!'], function($item) use($data){
        return $item != $data['audit']['status'];
    });
?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-cogs" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Consult general information')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <? headerTemplateAudits($_GET['id'], 'Audit Tools') ?>
    <div class="tile">
        <div class="tile-body">
            <div class="row">
                <? if($data['audit']['status'] != 'Closed'): ?>
                    <div class="col-12 col-lg-4">
                        <form onchange="moveAuditStatus(this)">
                            <label for="input-status"><?= $fnT('Move audit status') ?></label>
                            <select class="form-control" id="input-status" name="audit_status">
                                <option value="" disabled selected><?= $data['audit']['status'] ?></option>
                                <? foreach($status as $st): ?>
                                    <option value="<?= $st ?>"><?= $fnT($st) ?></option>
                                <? endforeach ?>
                            </select>
                            <input type="hidden" name="audit_id" value="<?= $data['audit']['id'] ?>">
                        </form>
                    </div>
                <? else: ?>
                    <div class="col-12 col-lg-4">
                        <form onchange="moveAuditStatus(this)">
                            <label for="input-status"><?= $fnT('Move audit status') ?></label>
                            <select class="form-control" id="input-status" name="audit_status">
                                <option value="" disabled selected><?= $data['audit']['status'] ?></option>
                                <option value="Pending"><?= $fnT('Pending') ?></option>
                                <option value="Deleted!"><?= $fnT('Deleted!') ?></option>
                            </select>
                            <input type="hidden" name="audit_id" value="<?= $data['audit']['id'] ?>">
                        </form>
                    </div>
                <? endif; ?>
                <div class="col-12 col-lg-4">
                    <form onchange="setSignaturePic(this)">
                        <div class="form-group">
                            <label for="signature_pic"><?= $fnT('Signature image') ?> 
                                <? if(!filter_var($data['audit']['manager_signature'], FILTER_VALIDATE_URL) === false): ?>
                                    &#124; <a href="<?= $data['audit']['manager_signature'] ?>" target="_blank"><?= $fnT('View signature') ?></a>
                                <? endif ?>
                            </label>
                            <input type="file" class="form-control-file" id="signature_pic" accept="image/*">
                        </div>
                        <input type="hidden" name="audit_id" value="<?= $data['audit']['id'] ?>">
                        <input type="hidden" name="url_pic" id="signature_url_pic">
                    </form>
                </div>
                <div class="col-12 col-lg-4">
                    <form onchange="setFrontDoorPic(this)">
                        <div class="form-group">
                            <label for="front_door_pic"><?= $fnT('Front door image') ?> 
                                <? if(!empty($data['audit']['front_door_pic'])): ?>
                                    &#124; <a href="<?= $data['audit']['front_door_pic'][0]['url'] ?>" target="_blank"><?= $fnT('View front door') ?></a>
                                <? endif ?>
                            </label>
                            <input type="file" class="form-control-file" id="front_door_pic" accept="image/*">
                        </div>
                        <input type="hidden" name="audit_id" value="<?= $data['audit']['id'] ?>">
                        <input type="hidden" name="url_pic" id="front_door_url_pic">
                    </form>
                </div>


                <div class="col-12 col-lg-4">
                    <form onchange="moveAuditRound(this)">
                        <label for="input-round"><?= $fnT('Move audit round') ?></label>
                        <select class="form-control" id="input-round" name="audit_round">
                            <option value="" selected></option>
                            <? foreach($data['rounds'] as $r): ?>
                                <option value="<?=$r['id']?>" <?=$data['audit']['round_id']==$r['id']?'selected':NULL?>>(ID: <?=$r['id']?>) <?=$r['name']?> - <?=$r['type']?></option>
                            <? endforeach ?>
                        </select>
                        <input type="hidden" name="audit_id_round" value="<?= $data['audit']['id'] ?>">
                    </form>
                </div>
             
            </div>
        </div>
    </div>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>