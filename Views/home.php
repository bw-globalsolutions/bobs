<?
  headerTemplate($data);
  $pend = $data['audit_statistics']['Pending']['count']?? 0;
  $inProc = $data['audit_statistics']['In Process']['count']?? 0;
  $comp = $data['audit_statistics']['Completed']['count']?? 0;
  $zeto = $data['audit_statistics']['Completed']['zero']?? 0;
  global $fnT;
?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
  <main class="app-content">
    <div class="app-title">
      <div>
        <h1><i class="fa fa-home fa-lg"></i> <?=$data['page_title']?></h1>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
      </ul>
    </div>

    <div class="tile">
      <div style="display:flex; gap:10px; align-items: center;">
      <? foreach($data['paises'] as $pais): ?>
        <input type="radio" class="country" hidden id="<?=$pais['name']?>" name="country" value="<?=$pais['id']?>">
        <label class="elementS noBG" for="<?=$pais['name']?>" ><img src="Assets/images/paises/<?=$pais['name']?>.png" class="pais" alt="<?=$pais['name']?>"></label>
      <? endforeach ?>
      </div>
    </div>
    
    <? if(!empty($data['permissionAudit']['r'])): ?>
      <div class="tile" style="display:flex; gap:10px;">
        <ul class="nav">
          <li class="nav-item">
            <span class="nav-link"><?=$fnT('Tipo de auditoria')?>:</span>
          </li>

          <div class="contES">
          <? foreach($data['auditTypes'] as $type): ?>
            <li class="nav-item">
                <input type="radio" <?=($type['type']=='Standard'?'checked':'')?> class="auditType" hidden id="<?=$type['type']?>" name="auditType" value="<?=$type['type']?>">
                <label class="elementS" for="<?=$type['type']?>" ><?=$fnT($type['type'])?></label>
            </li>
          <? endforeach ?>
          </div>
          
          



            <!--<li class="nav-item">
              <a class="nav-link" href="<?=base_url()?>/moduloComunicacion">Modulo comunicacion</a>
            </li>-->
        </ul>
        <div class="input-group" style="max-width:200px; max-height:34px;">
            <div class="input-group-prepend">
                <span class="input-group-text border-0"><?=$fnT('Período')?></span>
            </div>
            <select class="form-control selectpicker" id="filter_period" name="list_period[]" multiple data-actions-box="true" data-selected-text-format="count>1" required>
                <? foreach($data['periods'] as $period): ?>
                    <option value="<?=$period?>" <?=$period==end($data['periods'])? 'selected' : ''?>><?=$period?></option>
                <? endforeach ?>
            </select>
        </div>
      </div>

      <!--<div class="tile">
      <div class="col-lg-3 my-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Período')?></span>
                            </div>
                            <select class="form-control selectpicker" id="filter_period" name="list_period[]" multiple data-actions-box="true" data-selected-text-format="count>1" required>
                                <? foreach($data['periods'] as $period): ?>
                                    <option value="<?=$period?>" <?=$period==end($data['periods'])? 'selected' : ''?>><?=$period?></option>
                                <? endforeach ?>
                            </select>
                        </div>
                    </div>
      </div>
      </div>-->
      <div style="display:flex;">
        <div class="contCuentas">
          <? if(in_array( $_SESSION['userData']['role']['id'], [1,2] )): ?>
            <div style="min-width:193px; padding:0;" class="col-md-6 col-lg-3">
              <div style="min-width:193px;" class="widget-small info coloured-icon <?=$pend? 'cr-pointer' : 'cr-not-allowed'?>" onclick="<?=$pend? '' : 'return;'?> location.href = '<?=base_url() . '/audits?type=' . base64_encode('Standard')?>&filter=<?=base64_encode('Pending')?>'">
                <div class="drop-icon" style="position:absolute;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M7 13.5q.625 0 1.063-.437T8.5 12t-.437-1.062T7 10.5t-1.062.438T5.5 12t.438 1.063T7 13.5m5 0q.625 0 1.063-.437T13.5 12t-.437-1.062T12 10.5t-1.062.438T10.5 12t.438 1.063T12 13.5m5 0q.625 0 1.063-.437T18.5 12t-.437-1.062T17 10.5t-1.062.438T15.5 12t.438 1.063T17 13.5M12 22q-2.075 0-3.9-.788t-3.175-2.137T2.788 15.9T2 12t.788-3.9t2.137-3.175T8.1 2.788T12 2t3.9.788t3.175 2.137T21.213 8.1T22 12t-.788 3.9t-2.137 3.175t-3.175 2.138T12 22"/></svg>
                </div>
                <div class="info" style="padding-left: 60px;">
                  <h4><?=$fnT('Pendente')?></h4>
                  <p><b id="lblPen" class="txtR" style="background-color: var(--color6);"><?=$pend?></b></p>
                </div>
              </div>
            </div>
            <div style="min-width:193px; padding:0;" class="col-md-6 col-lg-3">
              <div style="min-width:193px;" class="widget-small warning coloured-icon <?=$inProc? 'cr-pointer' : 'cr-not-allowed'?>" onclick="<?=$inProc? '' : 'return;'?> location.href = '<?=base_url() . '/audits?type=' . base64_encode('Standard')?>&filter=<?=base64_encode('In Process')?>'">
                <div class="drop-icon" style="position:absolute;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 2048 2048"><path fill="currentColor" d="M837 844q-23 37-53 67t-68 54l51 124l-118 48l-51-123q-40 10-86 10t-86-10l-51 123l-118-48l51-124q-37-23-67-53t-54-68L63 895L15 777l123-51q-10-40-10-86t10-86L15 503l48-118l124 51q46-75 121-121l-51-124l118-48l51 123q40-10 86-10t86 10l51-123l118 48l-51 124q75 46 121 121l124-51l48 118l-123 51q10 40 10 86t-10 86l123 51l-48 118zm-325 52q53 0 99-20t82-55t55-81t20-100q0-53-20-99t-55-82t-81-55t-100-20q-53 0-99 20t-82 55t-55 81t-20 100q0 53 20 99t55 82t81 55t100 20m1408 448q0 55-14 111l137 56l-48 119l-138-57q-59 98-156 156l57 137l-119 49l-56-137q-56 14-111 14t-111-14l-56 137l-119-49l57-137q-98-58-156-156l-138 57l-48-119l137-56q-14-56-14-111t14-111l-137-56l48-119l138 57q58-97 156-156l-57-138l119-48l56 137q56-14 111-14t111 14l56-137l119 48l-57 138q97 59 156 156l138-57l48 119l-137 56q14 56 14 111m-448 320q66 0 124-25t101-68t69-102t26-125t-25-124t-69-101t-102-69t-124-26t-124 25t-102 69t-69 102t-25 124t25 124t68 102t102 69t125 25"/></svg>
                </div>
                <div class="info" style="padding-left: 60px;">
                  <h4><?=$fnT('Em processo')?></h4>
                  <p><b id="lblInP" class="txtR" style="background-color: var(--color7);"><?=$inProc?></b></p>
                </div>
              </div>
            </div>
          <? endif ?>
          <div style="min-width:193px; padding:0;" class="col-md-6 col-lg-3">
            <div style="min-width:193px;" class="widget-small success coloured-icon <?=$comp? 'cr-pointer' : 'cr-not-allowed'?>" onclick="<?=$comp? '' : 'return;'?> location.href = '<?=base_url() . '/audits?type=' . base64_encode('Standard')?>&filter=<?=base64_encode('Completed')?>'">
              <div class="drop-icon" style="position:absolute;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 21a9 9 0 1 0 0-18a9 9 0 0 0 0 18m-.232-5.36l5-6l-1.536-1.28l-4.3 5.159l-2.225-2.226l-1.414 1.414l3 3l.774.774z" clip-rule="evenodd"/></svg>
                </div>
              <div class="info" style="padding-left: 60px;">
                <h4><?=$fnT('Concluído')?></h4>
                <p><b id="lblComp" class="txtR" style="background-color: var(--color8);"><?=$comp?></b></p>
              </div>
            </div>
          </div>
          <div style="min-width:193px; padding:0;" class="col-md-6 col-lg-3">
            <div style="min-width:193px;" class="widget-small danger coloured-icon">
              <div class="drop-icon" style="position:absolute;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 512 512"><path fill="currentColor" fill-rule="evenodd" d="M256 64c106.039 0 192 85.961 192 192s-85.961 192-192 192S64 362.039 64 256S149.961 64 256 64m81.018 80.824l-81.006 81.006l-80.855-80.855l-30.17 30.17L225.842 256l-80.855 80.855l30.17 30.17l80.855-80.855l81.006 81.006l30.17-30.17L286.182 256l81.006-81.006z"/></svg>
                </div>
              <div class="info" style="padding-left: 60px;">
                <h4><?=$fnT('Reprovado')?></h4>
                <p><b id="lblZero" class="txtR" style="background-color: var(--color9);"><?=$zeto?></b></p>
              </div>
            </div>
          </div>
        </div>
        <div class="chart-container">
          <canvas id="myPieChart"></canvas>
        </div>
      </div>
    <? endif ?>

    <!-- <b class="my-1 b">*<?= $fnT('Informações do ano atual') ?></b> -->
    <div class="row">
      <? if($data['permissionDoc']['r'] == 12): ?>
        <div class="col-md-6">
          <div class="tile">
            <div class="row">
              <div class="col">
                <h2 class="mb-3"><?=$fnT('Downloads')?></h2>
              </div>
              <? if($data['permissionDoc']['w'] == 1): ?>
                <div class="col">
                  <button type="button" class="btn btn-primary float-right" type="button" data-toggle="collapse" data-target="#collapseFormFile" aria-expanded="false" aria-controls="collapseFormFile" onclick="prepareNewFile()">
                    <span><?=$fnT('Novo')?>&#160;&#160;<i class="fa fa-plus"></i></span>
                  </button>
                </div>
              <? endif ?>
            </div>
            <div class="accordion" id="accordionFile">
              <div class="card">
                <div id="collapseFormFile" class="collapse" aria-labelledby="headingOne" data-parent="#accordionFile">
                  <div class="card-body">
                    <form onsubmit="sendFormAddFile(this); return false;" id="form-files">
                      <div class="form-group row">
                        <label for="input-title" class="col-sm-2 col-form-label"><?=$fnT('Título')?></label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="input-title" name="title" maxlength="128" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-description" class="col-sm-2 col-form-label"><?=$fnT('Descrição')?></label>
                        <div class="col-sm-10">
                          <textarea class="form-control" rows="3" id="input-description" name="description" maxlength="512" required></textarea>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-files" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                          <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" id="customFile" onchange="addFile(this)" accept=".xlsx,.pdf,.png,.jpg">
                            <label class="custom-file-label" for="customFile"><?=$fnT('Selecionar arquivos')?></label>
                          </div>
                          <div id="form-panel-files"></div>
                        </div>
                      </div>
                      <input type="text" name="id" class="d-none">
                      <div class="form-group row">
                        <div class="col-sm-10">
                          <button type="submit" class="btn btn-primary" id="btn-send-af"></button>
                          <button type="button" class="btn btn-danger" onclick="$('#collapseFormFile').collapse('hide')"><?=$fnT('Cancelar')?></button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div id="panel-files">
                <div style="height: 350px" class="d-flex justify-content-center align-items-center">
                  <img src="<?=media()?>/images/loading.svg" height="60" alt="Loading">
                </div>
              </div>
            </div>
          </div>
        </div>
      <? endif ?>
      <? if(1==2): ?>
        <div class="col-md-6">
          <div class="tile">
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text border-0"><?=$fnT('Seção principal')?></span>
                </div>
                <select class="form-control selectpicker" onchange="setTopOpp(this.value)" id="select-top-opp">
                  <option value="Aseguramiento de calidad" selected><?=$fnT('Garantia de qualidade')?></option>
                  <option value="Estandar de la marca" selected><?=$fnT('Padrão da marca')?></option>
                </select>
            </div>
            <div id="chart-top-opp">
              <div style="height: 350px" class="d-flex justify-content-center align-items-center">
                <img src="<?=media()?>/images/loading.svg" height="60" alt="Loading">
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="tile">
            <div id="chart-avg-score">
              <div style="height: 395px" class="d-flex justify-content-center align-items-center">
                <img src="<?=media()?>/images/loading.svg" height="60" alt="Loading">
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="tile">
            <div id="chart-progress-action-plan">
              <div style="height: 395px" class="d-flex justify-content-center align-items-center">
                <img src="<?=media()?>/images/loading.svg" height="60" alt="Loading">
              </div>
            </div>
          </div>
        </div>
      <? endif ?>
    </div>
    
    
  </main>
<?php footerTemplate($data);?>
 <script>
  <? if($data['alert_se'] != false && $ON_SELFAUDIT): ?>
    const alertSE = <?=json_encode($data['alert_se'])?>;
    const numbers = alertSE.filter(item => item.month > 0).map(item => item.number).join(', ');
    if(numbers != ''){
      swal({
          title: fnT('Alerta'),
          text: fnT('Uma autoavaliação não foi realizada há mais de 30 dias para as seguintes lojas') + ": " + numbers,
          type: 'warning'
      });
    }
  <? endif; ?>

  permissionDoc = <?=json_encode($data['permissionDoc'])?>;
</script> 
