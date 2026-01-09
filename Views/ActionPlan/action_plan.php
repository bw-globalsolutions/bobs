<?php 
    headerTemplate($data);
    getModal('modalAnswer',$data);
    global $fnT;
    $arrLostQuestion = [];
    $arrLostSection = [];
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
                <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$data['page_title']?>
            </h1>
            <p><?=$fnT('Review the particular content of an audit')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <?php if($_SESSION['userData']['id'] == 26){
        //dep($_SESSION['userData']);
        dep($data);
        //dep($_SESSION['permisosMod']);
    }?>
    
    <? headerTemplateAudits($_GET['id'], 'Action plan') ?>

    <div id="sectionContent">
        <div class="row">
            <div class="col-lg-3 my-1">
                <div class="tile">
                    <div class="tile-body">
                        <ul class="app-menu pb-0">
                            <? foreach($data['section'] as $item): ?>
                                <li class="app-menu__item section-items flex-column align-items-start success" data-snumber="<?=$item['section_number']?>" onclick="filterSection(<?=$item['section_number']?>)" style="cursor: pointer;">
                                    <span class="badge badge-light"><?=$item['main_section']?></span><a class="text-primary"><?=$item['section_name']?></a>
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
                                    <div data-snumber="<?=$q['snumber']?>" class="question-item">
                                        <div class="card-header d-flex justify-content-between">
                                            <span data-toggle="collapse" data-target="#cpicklist<?=$q['prefix']?>" aria-expanded="false" aria-controls="cpicklist<?=$q['prefix']?>" style="cursor: pointer">
                                                <span class="badge badge-secondary prefix"><?=$q['prefix']?></span> - 
                                                <? if(!empty($q['priority'])): ?>
                                                    <b><?=$q['priority']?>:</b>&nbsp;
                                                <? endif ?>
                                                <?=$q['question']?>
                                            </span>
                                            <button data-qprefix="<?=$q['prefix']?>" type="button" class="btn ml-2 btn-success" style="height: 35px">
                                                <b><?=$q['points']?></b><span>pts</span>
                                            </button>
                                        </div>
                                        <div id="cpicklist<?=$q['prefix']?>" class="collapse" data-parent="#accordion-questions">
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    <? foreach($q['picklist'] as $p): ?>
                                                        <li class="list-group-item d-flex justify-content-between" style="cursor: pointer;" onclick="openOpportunity(<?=$p['id']?>, <?=$_GET['id']?>)">
                                                            <?=$p['picklist_item']?>
                                                            <? if($p['has_opp'] > 0): ?>
                                                                <i class="fa fa-times float-right ml-2 mb-2 text-danger"></i>
                                                            <?
                                                                array_push($arrLostQuestion, $q['prefix']);
                                                                array_push($arrLostSection, $q['snumber']);
                                                                else:
                                                            ?>
                                                                <i class="fa fa-check ml-2 mb-2 text-success"></i>
                                                            <? endif; ?>
                                                        </li>
                                                    <? endforeach ?>
                                                </ul>
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
    </div>
    
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>
<script>
    filterSection('<?=$data['section'][0]['section_number']?>');
    
    const arrLostQuestion = ['<?=implode(array_filter($arrLostQuestion), "','")?>'];
    arrLostQuestion.forEach(item => $(`button[data-qprefix="${item}"]`).removeClass('btn-success').addClass('btn-danger'))
    
    const arrLostSection = [<?=implode(array_filter($arrLostSection), ",")?>];
    arrLostSection.forEach(item => $(`li[data-snumber="${item}"]`).removeClass('success').addClass('danger'))
</script>