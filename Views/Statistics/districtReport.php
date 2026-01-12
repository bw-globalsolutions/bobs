<?php 
  headerTemplate($data);
  global $fnT;
?>
<main class="app-content"> 
    <?php //dep($data); ?>
    <div class="app-title">
        <div>
            <h1> <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?> </h1>
            <p><?=$fnT('Consultar relatório distrital')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <!--INICIO FILTROS-->
    <div class="tile">
        <div class="tile-body">
            <div class="form-row justify-content-center">

                <div class="col-lg-4 my-1"> 
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('País')?></span>
                        </div>
                        <select class="form-control selectpicker" id="filter_country" name="filter_country[]" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                            <? foreach($data['countries'] as $country): ?>
                                <option value="<?=$country['id']?>" selected><?=$country['name']?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-4 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?= $fnT('Anos') ?></span>
                        </div>
                        <select class="form-control selectpicker" id="filter_years" name="filter_years" required>
                            <?php 
                                $currentYear = date('Y');
                                for ($year = 2022; $year <= $currentYear; $year++): 
                            ?>
                                <option value="<?= $year ?>" <?= $year == $currentYear ? 'selected' : '' ?>><?= $year ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>


                 <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Franquia')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_franchise" name="list_franchise[]" multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
                                <? foreach($data['franchissees'] as $f): ?>
                                    <option value="<?=$f['name']?>" selected><?=$f['name']?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>


                      <!-- EMAIL AREA MANAGER -->
                    <div class="col-lg-3 my-1 <?= !in_array($_SESSION['userData']['role']['id'], [1, 2,3,17, 14,19,18])? 'd-none' : '' ?>">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Gerente de área')?></span>
                            </div>
                            <select class="form-control selectpicker" id="list_area_manager" name="list_area_manager[]"  multiple data-actions-box="true" data-selected-text-format="count>2" required data-live-search = "true">
                                <? foreach($data['email_area_manager'] as $email_area_manager): ?>
                                    <? $val = !empty($email_area_manager) ? $email_area_manager : 'N/A'; ?>
                                    <option value="<?= $val ?>" selected><?= $val ?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>

                <div class="col-lg-4 my-1">
                    <button id="btnFilterAnnouncedVisit" class="form-control btn btn-primary" type="button" onclick="reloadData();">
                        <i class="fa fa-filter" aria-hidden="true"></i>&nbsp;&nbsp;
                        <?=$fnT('Filtrar')?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--FIN FILTROS-->
    <button id="btnExportAll" class="btn btn-success"><?=$fnT('Downloads')?></button>

    <!--INICIO TABLE GLOBAL-->
    <div class="container-fluid my-4">
    <div class="card w-100">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #14406aff;">
            <h5 class="mb-0"><?=$fnT('Relatório geral')?></h5>
            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReportTable" aria-expanded="true" aria-controls="collapseReportTable">
            </button>
        </div>
                                        
        <div id="collapseReportTable" class="collapse show">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered w-100" id="tabledistrictReportGlobal">
                        
                        <thead style="background-color: #14406aff; color: white;">
                            <tr>
                                <th><?=$fnT('Pontuação')?></th>
                                <th><span><?=$fnT('Auditoria')?><sup><?=$fnT('Q1')?></sup></span></th>
                                <th><span><?=$fnT('Reauditoria')?><sup><?=$fnT('Q1')?></sup></span></th>
                                <th><span><?=$fnT('Re-Aud 2')?><sup><?=$fnT('Q1')?></sup></span></th>
                          
                                <th><span><?=$fnT('Auditoria')?><sup><?=$fnT('Q2')?></sup></span></th>
                                <th><span><?=$fnT('Reauditoria')?><sup><?=$fnT('Q2')?></sup></span></th>
                                <th><span><?=$fnT('Re-Aud 2')?><sup><?=$fnT('Q2')?></sup></span></th>
                     
                         
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contenido dinámico -->
                        </tbody>
                                <tfoot style="background-color: #14406aff; color: white;" id="tabledistrictTotal">
                                    <tr>
                                        <td><?=$fnT('Total')?></td>
                                        <td id="auditoria1"></td>
                                        <td id="re_auditoria1"></td>
                                        <td id="re_auditoria2_Q1"></td>
                                     
                                        <td id="auditoria2"></td>
                                        <td id="re_auditoria2"></td>
                                        <td id="re_auditoria2_Q2"></td>
                                    
                                       
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!--FIN TABLE GLOBAL-->

     <!--INICIO TABLE GLOBAL-->
     <div class="tile">
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tabledistrictReportStore">
                        
                        <thead style="background-color: #14406aff; color: white;">
                             <tr>
                <th colspan="4"></th>
                <th colspan="3" class="quarter-header text-center"><h2>Q1</h2></th>
                <th colspan="3" class="quarter-header text-center"><h2>Q2</h2></th>
            </tr>
                            <tr>
                                <th><?=$fnT('Número da filial')?></th>
                                <th><?=$fnT('Nome da filial')?></th>
                            
                                <th class="d-none"><?=$fnT('Consultor/Regional')?></th>
                            
                                <th><?=$fnT('Franquia')?></th>
                                <th>Area manager</th>
                                <th><span><?=$fnT('Auditoria')?><sup><?=$fnT('Q1')?></sup></span></th>
                                <th><span><?=$fnT('Reauditoria')?><sup><?=$fnT('Q1')?></sup></span></th>
                                <th><span><?=$fnT('Re-Aud 2')?><sup><?=$fnT('Q1')?></sup></span></th>
                               
                                <th><span><?=$fnT('Auditoria')?><sup><?=$fnT('Q2')?></sup></span></th>
                                <th><span><?=$fnT('Reauditoria')?><sup><?=$fnT('Q2')?></sup></span></th>
                                <th><span><?=$fnT('Re-Aud 2')?><sup><?=$fnT('Q2')?></sup></span></th>
                               
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
    </div>
    <!--FIN TABLE GLOBAL-->

    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


