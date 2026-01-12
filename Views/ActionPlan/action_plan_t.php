<?php 
    headerTemplate($data);
    getModal('modalAction',$data);
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
    <input type="hidden" id="id_auditoria" name="id_auditoria" value="<?php echo $_GET['id']; ?>">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Revisar o conteúdo específico de uma auditoria')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    
    <?php
        //dep($_SESSION);
        //dep($data);
        //dep($_GET);
        //echo $_GET['id'];
        //dep($_SESSION['permisosMod']);
?>
    
    <? headerTemplateAudits($_GET['id'], 'Action plan') ?>

    <div id="progressActionPlanDiv" name="progressActionPlanDiv"> <? progressTemplateActionPlan($_GET['id']) ?> </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tableAuditPlanActions">
                    <thead>
                        <tr>
                        <th><?=$fnT('#')?></th>
                        <th><?=$fnT('Oportunidade')?></th>
                        <th><?=$fnT('Status')?></th>
                        <th></th>
                        <th><?=$fnT('Ação')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>
    
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>