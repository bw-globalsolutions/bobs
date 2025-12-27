<? global $fnT; ?>
<!-- Modal -->
<div class="modal fade" id="modal-add-translation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$fnT('Add Translate')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <form id="add-translate-form" onsubmit="sendTranslate(this); return false;">
                <div class="form-group">
                    <label for="input-word-translate"><?= $fnT('Translated to')?> <span id="to-language"></span>: <b id="word-translate"></b></label>
                    <input type="text" class="form-control" name="word-translate" id="input-word-translate" required <?$data['permission']['u']? '' : 'disabled' ?>>
                </div>
                <input type="text" class="d-none" name="dictionary_id" id="input-dictionary_id">
                <input type="text" class="d-none" name="language_id" id="input-language_id">
            </form>

        </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <? if($data['permission']['d']): ?>
                        <button type="button" class="btn btn-danger" id="btn-remove" onclick="sendDelTranslate()"><?=$fnT('Remove')?></button>
                    <? endif ?>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$fnT('Close')?></button>
                    <? if($data['permission']['u']): ?>
                        <button type="submit" class="btn btn-primary" form="add-translate-form"><?=$fnT('Save changes')?></button>
                    <? endif ?>
                </div>
            </div>
        </div>
    </div>
</div>