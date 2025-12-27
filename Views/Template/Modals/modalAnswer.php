<?php global $fnT; ?>

<!-- Modal -->
<div class="modal fade" id="modalViewAnwers" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header header-primary">
                <h5 class="modal-title" id="titleModal"><?=$fnT('Answer list')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <ul class="list-group list-group-flush" id="list-answers"></ul>
                <div class="p-3 bg-light">
                    <form onsubmit="sendAnswers(this); return false;" id="form-answers">
                        <div class="form-group">
                            <label for="opp_comment" id="auditor-comment">
                                <i class="fa fa-comment text-danger" aria-hidden="true"></i>&nbsp;&nbsp;<?=$fnT('Comments')?>
                            </label>
                            <textarea class="form-control" id="opp_comment" rows="2" name="opp_comment" disabled required></textarea>
                        </div>
                        <? if($data['update']): ?>
                            <div class="form-group can-update">
                                <input type="file" class="form-control-file file-trigger" style="display: none;" onchange="uploadPic(this)">
                            </div>

                            <button type="button" class="btn-file btn btn-primary mr-1">
                                <?=$fnT('Choose picture')?> <!-- Texto editable -->
                            </button>
                        <? endif; ?>
                        <input name="checklist_item_id" id="checklist_item_id" hidden>
                        <input type="" name="opp_id" id="opp_id" hidden>
                        <input type="" name="audit_id" value="<?=$data['audit_id']?>" hidden>
                    </form>
                    <div id="auditor-files" class="d-flex flex-wrap"></div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <? if($data['update']): ?>
                        <span class="text-danger cr-pointer can-update" id="btn-remove-opp" onclick="removeOpp()"><?=$fnT('Remove Opp')?></span>
                    <? endif; ?>
                </div>
                <div>
                    <? if($data['update']): ?>
                        <button type="submit" class="btn btn-primary mr-1 can-update" id="btn-save-answers" form="form-answers"><?=$fnT('Save')?></button>
                    <? endif; ?>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$fnT('Close')?></button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    document.querySelector('.btn-file').addEventListener('click', function() {
    document.querySelector('.file-trigger').click();
});
    
</script>