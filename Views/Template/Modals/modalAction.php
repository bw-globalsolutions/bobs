<?php global $fnT; ?>
<!-- Modal -->
<div class="modal fade" id="modalFormAction" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header header-primary">
                <h5 class="modal-title" id="titleModal"><?=$fnT('Add action')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                    <div id="previousActions" class="d-flex flex-wrap"></div>
                    <div class="tile-body">
                        <div class="formCheck">
                            
                        </div>
                        <div class="formNormal">
                            <form id="formPlanAction" name="formPlanAction" class="form-horizontal">
                                <input type="hidden" id="opp_id" name="opp_id" value="">
                                <input type="hidden" id="opp_audit_id" name="opp_audit_id" value="">
                                <div class="form-row justify-content-center">
                                    <input type="hidden" name="checks" id="checks">
                                    <div class="form-group col-md-10">
                                        <label for="action" class="control-label"><?=$fnT('Action')?></label>
                                        <textarea class="form-control valid validText" name="action" id="action" cols="40" rows="3" style="resize: both;"></textarea>
                                    </div>

                                    
                                    <div class="form-group can-update">
                                    <input type="file" class="form-control-file" onchange="uploadPic(this)">
                                    <div id="auditor-files" class="d-flex flex-wrap"></div>
                                    <input type="" class="form-control-file" id="evidencia" name="evidencia" hidden>
                                </div>
                                </div>
                            </form>
                        </div>
                        <p class="errorBox errorBoxC" style="display:none;"><?=$fnT('You need to select at least one option')?></p>
                        <p class="errorBox errorBoxN" style="display:none;"><?=$fnT('You must fill in the text field')?></p>
                        <div class="tile-footer">
                            <button id="btnFormAddAction" form="formPlanAction" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><?=$fnT('Save')?></button>
                            <button class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i><?=$fnT('Cancel')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>