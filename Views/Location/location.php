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
            <p><?=$fnT('Register, update and delete locations')?></p>
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
                                    <th><?=$fnT('Number')?></th>
                                    <th><?=$fnT('Name')?></th>
                                    <th><?=$fnT('Country')?></th>
                                    <th><?=$fnT('City')?></th>
                                    <th><?=$fnT('Address')?></th>
                                    <th><?=$fnT('Email')?></th>
                                    <th><?=$fnT('Type')?></th>
                                    <th><?=$fnT('Status')?></th>
                                    <th><?=$fnT('Actions')?></th>
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
                        <h5 class="card-title"><?= $fnT('Bulk import') ?> &#124; <a href="<?=base_url()."/plantilla_tiendas.xlsx"?>" target="_blank"><!--<a href="https://ws.bw-globalsolutions.com/WSAAA/S3_view_file/?ZBWEhaITQ3vFTtGYqZPQAtfuMwgCgZdSffRsIF/uuQ5lMAFe2AD3N+BQPJce07DDrUCk56jJAooSJAhrgjiIvg==" target="_blank">--><?= $fnT('Download template') ?></a></h5> 
                        <form onsubmit="sendStoreFile(); return false;">
                            <div class="form-group"> 
                                <label for="store-file"><?= $fnT('Store archive') ?></label>
                                <input type="file" class="form-control-file" id="store-file" onchange="readStoreFile(this)" id="store-file" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><?= $fnT('Send') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        <? endif ?>
    </div>
</main>
<script src="./Assets/js/plugins/xlsx.full.min.js"></script>
<?php footerTemplate($data);?>