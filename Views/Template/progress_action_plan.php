<? global $fnT;
		$fnT = translate($_SESSION['userData']['default_language']); ?>
<div class="row">
    <? //echo $_SESSION['userData']['default_language'] ; ?>
    <div class="col-sm-12">
        <div class="tile">
            <div class="tile-body">
                <div class="d-flex justify-content-center">
                    <div class="card" style="width: 80%;">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-warning text-dark font-weight-bold" role="progressbar" style="width: <?=$dataP['totalPending']?>%" aria-valuenow="<?=$dataP['totalPending']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalPending']?>% <?= $fnT('Pending') ?></div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-info text-dark font-weight-bold" role="progressbar" style="width: <?=$dataP['totalReview']?>%" aria-valuenow="<?=$dataP['totalReview']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalReview']?>% <?= $fnT('In process') ?></div>
                                </div>
                            </li>
                            <!--<li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?=$dataP['totalApproved']?>%" aria-valuenow="<?=$dataP['totalApproved']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalApproved']?>% Rejected</div>
                                </div>
                            </li>-->
                            <li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-success text-dark font-weight-bold" role="progressbar" style="width: <?=$dataP['totalFinished']?>%" aria-valuenow="<?=$dataP['totalFinished']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalFinished']?>% <?= $fnT('Finished') ?></div>
                                </div>
                            </li>
                            <!--<li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?=$dataP['totalRejected']?>%" aria-valuenow="<?=$dataP['totalRejected']?>" aria-valuemin="0" aria-valuemax="100"><?=$dataP['totalRejected']?>% Rejected</div>
                                </div>
                            </li>-->
                            <li class="list-group-item">
                                <div class="progress">
                                    <div class="progress-bar bg-primary text-dark font-weight-bold" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"><?= $fnT('Total opportunities') ?> (<?=$dataP['totalOpps']?>)</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>