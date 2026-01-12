<? global $fnT; ?>
<!-- Modal -->
<div class="modal fade" id="modal-set-targetpp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$fnT('Definir meta')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <form id="set-target-form" onsubmit="sendTarget(this); return false;">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="input-country"><?= $fnT('País') ?></label>
                    </div>
                    <select class="custom-select" id="input-country" name="country" onchange="getTarget()" required>
                        <option value="" disabled selected></option>
                        <? foreach($data['countries'] as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="input-period"><?= $fnT('Período') ?></label>
                    </div>
                    <select class="custom-select" id="input-period" name="period" onchange="getTarget()" required>
                        <option value="" disabled selected></option>
                        <? foreach($data['periods'] as $p): ?>
                            <option value="<?= $p ?>"><?= $p ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="input-target"><?= $fnT('Meta') ?></label>
                    </div>
                    <input type="number" class="form-control" id="input-target" name="target" required>
                </div>
            </form>
        </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$fnT('Fechar')?></button>
                <button type="submit" id="btn-send-target" class="btn btn-primary" form="set-target-form" disabled><?=$fnT('Salvar alterações')?></button>
            </div>
        </div>
    </div>
</div>