<?php global $fnT; 
getModal('modalCerttis',$data);?>

<!-- Modal -->
<div class="modal fade" id="modalViewAnwers" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header header-primary">
                <h5 class="modal-title" id="titleModal"><?=$fnT('Lista de respostas')?></h5>
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
                                <i class="fa fa-comment text-danger" aria-hidden="true"></i>&nbsp;&nbsp;<?=$fnT('Comentários')?>
                            </label>
                            <textarea class="form-control" id="opp_comment" rows="2" name="opp_comment" disabled required></textarea>
                        </div>
                        <? if($data['update']): ?>
                            <div class="form-group can-update">
                                <input type="file" class="form-control-file" multiple onchange="uploadPic(this)">
                            </div>
                        <? endif; ?>
                        <input type="hidden" name="checklist_item_id" id="checklist_item_id">
                        <input type="hidden" name="opp_id" id="opp_id">
                        <input type="hidden" name="audit_id" value="<?=$data['audit_id']?>">
                    </form>
                    <div id="auditor-files" class="d-flex flex-wrap"></div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <? if($data['update']): ?>
                        <span class="text-danger cr-pointer can-update" id="btn-remove-opp" onclick="removeOpp()"><?=$fnT('Remover oportunidade')?></span>
                    <? endif; ?>
                </div>
                <div style="display:flex;">
                    <? if($data['update']): ?>
                        <button type="submit" class="btn btn-primary mr-1 btnCloseAction" id="btn-save-answers" form="form-answers"><?=$fnT('Salvar')?></button>
                    <? endif; ?>
                    <? if($data['update'] && $data['type']!='Self-Evaluation'): ?>
                        <button data-toggle="modal" data-target="#modalCerttis" data-dismiss="modal" id="btn-certtis" onclick="obtenerdatosCerttis(<?=$data['audit_id']?>)" class="btn btn-primary mr-1 can-update" id="btn-certti"><?=$fnT('Certti')?></button>
                    <? endif; ?>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$fnT('Fechar')?></button>
                </div>
            </div>
        </div>
    </div>
</div>