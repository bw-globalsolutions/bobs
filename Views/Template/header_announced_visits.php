<? global $fnT;
    $siguiente = date("W") +1; ?>
<div class="tile">
    <div class="tile-body">
        <div class="form-row">
            <div class="col-lg-4 my-1">
                <p class="h4"><span class="badge badge-info"><?=$fnT('Current week')?> <?= date("W") ?></span></p> 
            </div>
            <div class="col-lg-4 my-1">
                <p><b class="text-success"><?= $dataAV['totalAV']?> <?=$fnT('Visits for the next week')?> <?= $siguiente ?></b></p> 
            </div>
            <div class="col-lg-4 my-1 float-right">
                <!-- <button id="btnSendAllAnnouncedVisit" class="form-control btn btn-primary" type="button" onclick="sendAllAnnouncedVisits(<?= date('W')?>);"> -->
                <button id="btnSendAllAnnouncedVisit" class="form-control btn btn-primary" type="button" onclick="fntSendNotificationGeneral(<?= date('W')?>);">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <?=$fnT('Send general notification')?>
                </button>
            </div>
        </div>
    </div>
</div>