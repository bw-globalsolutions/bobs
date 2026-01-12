<?php 
    headerTemplate($data);
    global $fnT;
?>
<div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
  <main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-question" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Revisar as perguntas adicionais de uma auditoria')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <? headerTemplateAudits($_GET['id'], 'Informational questions') ?>
    <div class="row">
        <div class="col-lg-3 my-1">
            <div class="tile">
                <div class="tile-body">
                    <ul class="app-menu pb-0">
                        <? foreach($data['type'] as $item): ?>
                            <li class="app-menu__item section-items flex-column align-items-start cr-pointer" data-tname="<?=$item?>" onclick="filterType('<?=$item?>')">
                                <a class="text-primary"><?=$fnT($item)?></a>
                            </li>
                        <? endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="tile">
                <div class="tile-body">
                    <div id="accordion-questions">
                        <div class="card border-0">
                            <? foreach($data['question'] as $q): ?>
                                <div data-tname="<?=$q['type']?>" class="question-item">
                                    <div class="card-header d-flex justify-content-between cr-pointer" data-toggle="collapse" data-target="#cpicklist<?=$q['id']?>" aria-expanded="false" aria-controls="cpicklist<?=$q['id']?>">
                                        <?=$q['text']?>
                                    </div>
                                    <div id="cpicklist<?=$q['id']?>" class="collapse" data-parent="#accordion-questions">
                                        <div class="card-body">
                                            <form onsubmit="sendQuestion(this, '<?=$q['input_type']?>'); return false;" id="form-question<?=$q['id']?>">
                                                <input type="hidden" name="additional_question_item_id" value="<?=$q['id']?>">
                                                <div class="form-group">
                                                <? switch ($q['input_type']): 
                                                    case 'UPLOAD_PICTURES': ?>                                           
                                                        <div class="panel-pic">
                                                            <? if(!empty($q['url'])): ?>
                                                                <div class="mr-2 mb-3">
                                                                    <a href="<?=$q['url']?>" target="_blank">
                                                                        <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="<?=$q['url']?>">
                                                                    </a>
                                                                </div>
                                                            <? endif?>
                                                        </div>
                                                        <input type="hidden" name="url_pic">
                                                        <input type="hidden" name="pic_name" value="<?=$q['eng']?>">
                                                        <input type="file" class="form-control-file control" onchange="uploadPic(this, <?=$q['id']?>)" disabled>
                                                    <? break;
                                                    case 'FREE_TEXT': ?>
                                                        <textarea class="form-control control"rows="2" name="answer" disabled><?=$q['answer']?></textarea>
                                                    <? break;
                                                    case 'SELECT_OPTIONS': ?>
                                                        <select class="form-control control" name="answer" disabled>
                                                            <option value="" disabled selected><?=$fnT('Selecione uma opção')?></option>
                                                            <? foreach(explode('|', $q['qanswer']) as $a): ?>
                                                                <option value="<?=$a?>" <?=$q['answer']==$a? 'selected' : ''?>><?=$a?></option>
                                                            <? endforeach ?>
                                                        </select>
                                                    <? break;
                                                endswitch; ?>
                                                </div>
                                                <? if($data['permission']['u'] or isMySelfEvaluation($_GET['id'])): ?>
                                                    <button type="button" class="btn btn-danger d-none clean" onclick="cleanQuestion(<?=$q['id']?>)"><?=$fnT('Limpar')?></button>
                                                    <button type="submit" class="btn btn-primary d-none save"><?=$fnT('Salvar')?></button>
                                                    <button type="button" class="btn btn-warning edit" onclick="editQuestion(<?=$q['id']?>)"><?=$fnT('Editar')?></button>
                                                <? endif ?>
                                            </form>                                          
                                        </div>
                                    </div>
                                </div>
                            <? endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>
<script>
    filterType('<?=$data['type'][0]?>');
    const audit_id = <?=$_GET['id']?>;
    editRestricted = <?=($data['status'] != 'Completed' && $data['visit_status'] == 'Visited')? 'true' : 'false'?>;
</script>