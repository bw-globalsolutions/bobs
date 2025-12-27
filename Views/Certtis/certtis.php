<?php 
    headerTemplate($data);
    getModal('modalCerttis',$data);
    global $fnT;
    $arrLostQuestion = [];
    $arrLostSection = [];
?>
<!-- ADMINLTE-->
<link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/adminlte.min.css">

<style>  .colorBase{background: linear-gradient(to right,  #000856, #003956); color:white;}</style>

<main class="app-content">
    <input type="hidden" id="id_auditoria" name="id_auditoria" value="<?php echo $_GET['id']; ?>">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Review the particular content of an audit')?></p>
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
    
    <? headerTemplateAudits($_GET['id'], 'Certtis') ?>

    <button class="btn btn-secondary btnCloseAction"  data-toggle='modal'  data-target='#modalCerttisEmail'>
    <i class="fa fa-envelope-o" aria-hidden="true"></i>Confirmar envio certtis
    </button>
    <br>
    <br>
  


    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered " id="tableCerttis">
                    <thead class="colorBase">
                        <tr>
                        <th><?=$fnT('#')?></th>
                        <th><?=$fnT('Opportunity')?></th>
                        <th><?=$fnT('Status')?></th>
                        <th><?=$fnT('Action')?></th>
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