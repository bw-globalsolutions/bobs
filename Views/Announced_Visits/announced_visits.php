<?php 
  headerTemplate($data);
  //getModal('modalAnnouncedVisit', null);
  global $fnT;
?>
<main class="app-content">
    <?php 
        //dep($data);
    ?>
    <div class="app-title">
        <div>
            <h1>
            <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$data['page_title']?>
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
            <div class="form-row">
                <div class="col-lg-8 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Franchise')?></span>
                        </div>
                        <select class="form-control selectpicker" id="f_franchise" name="f_franchise" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                            <? foreach($data['franchises'] as $f): ?>
                                <option value="<?=$f['id']?>" selected><?=$f['name']?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 my-1">
                    <button id="btnFilterAnnouncedVisit" class="form-control btn btn-primary" type="button" onclick="reloadTable();">
                        <i class="fa fa-filter" aria-hidden="true"></i>&nbsp;&nbsp;
                        <?=$fnT('Filter')?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="tile">
        <div class="tile-body">
            <div class="form-row justify-content-center">
                <div class="col-lg-10 my-1 float-right">
                    <button id="btnSendAllAnnouncedVisit" class="form-control btn btn-primary" type="button" onclick="fntSendNotificationGlobal();">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <?=$fnT('Send notification')?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="tile">
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableAnnouncedVisits">
                        <thead>
                            <tr>
                            <th><?=$fnT('Id')?></th>
                            <th><?=$fnT('Visit')?></th>
                            <th><?=$fnT('Planned date')?></th>
                            
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
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>