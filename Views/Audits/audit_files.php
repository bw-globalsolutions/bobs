<?php 
    headerTemplate($data);
    getModal('modalImage', null);
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
                <i class="fa fa-camera" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('View the photos corresponding to the audit')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-list-ol fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <? headerTemplateAudits($_GET['id'], 'Photography') ?>
    <div class="tile">
        <div class="tile-body">
        <? if(!empty($data['files'])): ?>
            <ul class="list-group list-group-flush">
                <? foreach($data['files'] as $type => $files): ?>
                    <li class="list-group-item">
                        <h2 class="h4 mb-3"><?=$fnT($type)?>:</h2>
                        <div class="row contGaleria">
                            <? foreach($files as $f): ?>
                                <div class="cr-pointer imgGal" style="height: 180px" data-toggle="tooltip" data-placement="top" title="<?=$f['name']?>">
                                    <img class="h-100 w-100 of-cover" src="<?=$f['url']?>" onclick="openImage(this, '<?=$f['name']?>', '<?=$type?>', <?=$f['reference_id']?>)">
                                </div>
                            <? endforeach ?>
                        </div>
                    </li>
                <? endforeach ?>
            </ul>
        <? else: ?>
            <h2 class="h4 mb-3"><?=$fnT('There are no images to show')?></h2>
        <? endif ?>
        </div>
    </div>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>