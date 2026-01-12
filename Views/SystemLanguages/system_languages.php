<?php 
  headerTemplate($data);
  getModal('modalAddTranslate',$data);
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
                    <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
                </h1>
                <p><?=$fnT('Gerenciar idiomas do sistema')?></p>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
                <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
            </ul>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="tile">
                    <div class="tile-body">
                        <h6>
                            <?= $fnT('Palavras') ?>:
                            <span class="ml-2"><?= count($data['dictionary']) ?></span>
                        </h6>
                        <div class="btn-group-toggle d-flex flex-column" data-toggle="buttons">
                            <? foreach($data['languagues'] as $l): ?>
                                <label class="btn btn-light mt-1 active" onclick="toggleLanguage(<?=$l['id']?>, !this.classList.contains('active'))">
                                    <input type="checkbox" autocomplete="off" checked><?= $l['name'] ?>: 
                                    <span class="badge badge-pill ml-2" style="background-color: <?=$l['color']?>; color: white" id="counter<?=$l['id']?>" data-count="<?=$l['count']?>"><?=$l['count']?></span>
                                    <input type="hidden" id="bg-color<?=$l['id']?>" value="<?=$l['color']?>">
                                </label>
                            <? endforeach ?>
                        </div>
                    </div>
                </div>
                <? if($data['permission']['u']): ?>
                    <div class="btn-group dropright w-100 mb-3" role="group">
                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?= $fnT('Sincronizar tradução') ?>
                        </button>
                        <div class="dropdown-menu">
                            <? foreach($data['languagues'] as $l): ?>
                                <a class="dropdown-item" onclick="sendSynchronize(<?=$l['id']?>)"><?=$l['name']?></a>
                            <? endforeach ?>
                        </div>
                    </div>
                <? endif ?>
            </div>
            <div class="col-lg-9">
                <div class="tile">
                    <div class="tile-body">
                        <div class="d-flex justify-content-center">
                            <div class="input-group rounded mt-3" style="width: 270px;">
                                <input class="input-s1 form-control rounded" style="padding-left: 20px;" id="filter_search" placeholder="<?= $fnT('Pesquisar') ?>" onkeyup="searchString(this.value)">
                                <div class="drop-icon" style="position:absolute; top:-7px; left:-40px">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M11 2a9 9 0 1 0 5.618 16.032l3.675 3.675a1 1 0 0 0 1.414-1.414l-3.675-3.675A9 9 0 0 0 11 2m-6 9a6 6 0 1 1 12 0a6 6 0 0 1-12 0" clip-rule="evenodd"></path></svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive" style="min-height: 450px;">
                            <table class="table table-hover table-bordered mt-2" id="tableLanguages">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?=$fnT('Palavra')?></th>
                                        <th><?=$fnT('Tradução')?></th>
                                        <th><?=$fnT('Ações')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <? foreach($data['dictionary'] as $d): ?>
                                        <tr class="word-row">
                                            <td><?=$d['id']?></td>
                                            <td class="item-translate" data-translation="<?=$d['word']?>" data-language="0"><?=$d['word']?></td>
                                            <td id="cell-dictionary<?=$d['id']?>">
                                                <? foreach($d['translations'] as $t ): ?>
                                                    <span class="badge badge-pill item-translate mb-1 ml-1" style="background-color: <?= $t['color'] ?>; color: white;" data-language="<?=$t['id']?>" data-translation="<?=$t['translation']?>" ><?= $t['translation'] ?></span>
                                                <? endforeach; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group dropdown" role="group">
                                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <?= $fnT('Editar') ?>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <? foreach($data['languagues'] as $l): ?>
                                                            <a class="dropdown-item" onclick="openModalAdd(<?=$d['id']?>, <?=$l['id']?>, '<?=$l['name']?>', '<?=$d['word']?>')"><?=$l['name']?></a>
                                                        <? endforeach ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <? endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php footerTemplate($data);?>