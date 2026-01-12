<?php 
    headerTemplate($data);
    getModal('modalMassInsertion', null);
    getModal('modalAddLocation', null);
    getModal('modalEditLocation', $data);
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
                <i class="fa fa-map-marker" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
                <button class="btn btn-sm btn-primary" type="button" onclick="openModal();"><i class="fa fa-plus-circle" aria-hidden="true"></i> Nuevo</button>
            </h1>
            <p><?=$fnT('Cadastrar, atualizar e excluir localizações')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableLocations">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?=$fnT('Número')?></th>
                                    <th><?=$fnT('Nome')?></th>
                                    <th><?=$fnT('País')?></th>
                                    <th><?=$fnT('Cidade')?></th>
                                    <th><?=$fnT('Endereço')?></th>
                                    <th><?=$fnT('E-mail')?></th>
                                    <th><?=$fnT('Tipo')?></th>
                                    <th><?=$fnT('Status')?></th>
                                    <th><?=$fnT('Ações')?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <? if($data['permission']['u'] && $data['permission']['w'] && in_array($data['rol'], [1])): ?>
            <div class="col-12 col-lg-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $fnT('Importação em massa') ?> &#124; <a href="<?=base_url()."/plantilla_tiendas.xlsx"?>" target="_blank"><!--<a href="https://ws.bw-globalsolutions.com/WSAAA/S3_view_file/?ZBWEhaITQ3vFTtGYqZPQAtfuMwgCgZdSffRsIF/uuQ5lMAFe2AD3N+BQPJce07DDrUCk56jJAooSJAhrgjiIvg==" target="_blank">--><?= $fnT('Baixar modelo') ?></a></h5> 
                        <form onsubmit="sendStoreFile(); return false;">
                            <div class="form-group"> 
                                <label for="store-file"><?= $fnT('Arquivo da loja') ?></label>
                                <input type="file" class="form-control-file" id="store-file" onchange="readStoreFile(this)" id="store-file" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><?= $fnT('Enviar') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        <? endif ?>
    </div>
</main>
<script src="./Assets/js/plugins/xlsx.full.min.js"></script>
<?php footerTemplate($data);?>