<?php global $fnT; $fnT = translate($_SESSION['userData']['default_language']); ?>
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <form id="formAllAppeals" class="form-horizontal">
                <div class="form-row justify-content-center">
                    <div class="form-group col-md-6">
                        <input type="text" id="idAuditDT" name="idAuditDT" value="">
                        <button id="btnFormAddAllAppeals" class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><?=$fnT('Send appeals')?></button>
                    </div>
                </div>
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableAppeals">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?=$fnT('Opportunity')?></th>
                                <th><?=$fnT('Appeal')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>