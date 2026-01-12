<?php 
  headerTemplate($data);
  //getModal('modalAnnouncedVisit', null);
  global $fnT;
?>
<div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
  <main class="app-content">
    <?php 
        //dep($data);
    ?>
    <div class="app-title">
        <div>
            <h1>
            <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('Consultar e filtrar as auditorias')?></p>
        </div>
        <button class="btn-excel" onclick="descargable()" style="position:absolute; right:200px; border:none;" id="btnExcel"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"><path fill="currentColor" d="M453.547 273.449H372.12v-40.714h81.427zm0 23.264H372.12v40.714h81.427zm0-191.934H372.12v40.713h81.427zm0 63.978H372.12v40.713h81.427zm0 191.934H372.12v40.714h81.427zm56.242 80.264c-2.326 12.098-16.867 12.388-26.58 12.796H302.326v52.345h-36.119L0 459.566V52.492L267.778 5.904h34.548v46.355h174.66c9.83.407 20.648-.291 29.197 5.583c5.991 8.608 5.41 19.543 5.817 29.43l-.233 302.791c-.29 16.925 1.57 34.2-1.978 50.892m-296.51-91.256c-16.052-32.57-32.395-64.909-48.39-97.48c15.82-31.698 31.408-63.512 46.937-95.327c-13.203.64-26.406 1.454-39.55 2.385c-9.83 23.904-21.288 47.169-28.965 71.888c-7.154-23.323-16.634-45.774-25.3-68.515c-12.796.698-25.592 1.454-38.387 2.21c13.493 29.78 27.86 59.15 40.946 89.104c-15.413 29.081-29.837 58.57-44.785 87.825c12.737.523 25.475 1.047 38.212 1.221c9.074-23.148 20.357-45.424 28.267-69.038c7.096 25.359 19.135 48.798 29.023 73.051c14.017.99 27.976 1.862 41.993 2.676M484.26 79.882H302.326v24.897h46.53v40.713h-46.53v23.265h46.53v40.713h-46.53v23.265h46.53v40.714h-46.53v23.264h46.53v40.714h-46.53v23.264h46.53v40.714h-46.53v26.897H484.26z"/></svg></button>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$fnT($data['page_title'])?></a></li>
        </ul>
    </div>
    <div class="tile">
        <div class="tile-body">
            <div class="form-row">
                <!--<div class="col-lg-4 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Franquia')?></span>
                        </div>
                        <select class="form-control selectpicker" id="f_franchise" name="f_franchise" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                            <? foreach($data['franchises'] as $f): ?>
                                <option value="<?=$f['id']?>" selected><?=$f['name']?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>-->
                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('País')?></span>
                        </div>
                        <select class="form-control selectpicker" id="f_country" name="f_country" multiple data-actions-box="true" data-selected-text-format="count>2" required>
                            <? foreach($data['paises'] as $f): ?>
                                <option value="<?=$f['id']?>" selected><?=$f['name']?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('De')?></span>
                        </div>
                        <input type="date" name="f_from" id="f_from" class="inpFecha" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-lg-3 my-1">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0"><?=$fnT('Para')?></span>
                        </div>
                        <input type="date" name="f_to" id="f_to" class="inpFecha" value="<?php echo date('Y-m-d', strtotime('+2 weeks')); ?>">
                    </div>
                </div>
                <div class="col-lg-3 my-1">
                    <button id="btnFilterAnnouncedVisit" class="form-control btn btn-primary" type="button" onclick="reloadTable();">
                        <i class="fa fa-filter" aria-hidden="true"></i>&nbsp;&nbsp;
                        <?=$fnT('Filtrar')?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="tile">
        <div class="tile-body">
            <div class="form-row justify-content-center">
                <div class="col-lg-10 my-1 float-right">
                    <button id="btnSendAllAnnouncedVisit" class="form-control btn btn-primary" type="button" onclick="fntSendNotificationGlobal();">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <?=$fnT('Enviar notificação')?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="tile">
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableAnnouncedVisits">
                        <thead>
                            <tr>
                            <th><?=$fnT('ID')?></th>
                            <th><?=$fnT('Visita')?></th>
                            <th><?=$fnT('Data planejada')?></th>
                            <th><?=$fnT('Ação')?></th>
                            <th><?=$fnT('Notificação enviada')?></th>
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
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>