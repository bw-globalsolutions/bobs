<?
  headerTemplate($data);
  $pend = $data['audit_statistics']['Pending']['count']?? 0;
  $inProc = $data['audit_statistics']['In Process']['count']?? 0;
  $comp = $data['audit_statistics']['Completed']['count']?? 0;
  $zeto = $data['audit_statistics']['ZeroTolerance']['count']?? 0;
  global $fnT;
?>



<style>
.navBandera {
    display: flex;
    gap: 10px;
    padding: 0;
    margin: 0;
    list-style: none;
}

.bandera {
    display: flex;
    align-items: center;
    width: 100px;
    height: 60px;
    margin-bottom: 0;
    border: 3px solid black;
    position: relative;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.2s, border-color 0.2s, filter 0.3s ease;
    
    filter: grayscale(70%);
}

.bandera:hover::after {
    content: attr(name);
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    text-align: center;
    font-weight: 900;
    background-color: rgba(255, 255, 255, 0.9);
}

.bandera.selected {
    border-color: rgb(5, 9, 54);
    box-shadow: 0 0 10px rgb(0, 149, 255);
    transform: scale(1.05);
    
    filter: grayscale(0%);
}
</style>


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


<? if(!empty($data['permissionAudit']['r'])): ?>

<? if( in_array( $_SESSION['userData']['role']['id'], [1,2,3,17,21] )): ?>
<div class="tile">
  <ul class="navBandera">
    <?php foreach($data['country'] as $country): ?>
      <li class="nav-item">
        <div class="bandera" 
             id="<?= $country ?>" 
             name="<?= $country ?>" 
             style="background: url('<?= media() ?>/images/paises/<?= $country ?>.png') center/cover no-repeat;" 
             onclick="sendCountry(this, '<?= $country ?>')">
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
  <? endif ?>
      <div class="tile">
        <ul class="nav">
          <li class="nav-item">
            <span class="nav-link"><?=$fnT('Audit type')?>:</span>
          </li>


          <? foreach($data['auditTypes'] as $type): ?>
            <li class="nav-item">
              <a class="nav-link"  href="<?= base_url() . '/audits?type=' . base64_encode($type['type']) . '&country=' . base64_encode($_GET['country']) ?>"><?=$fnT($type['type'])?></a>
            </li>
          <? endforeach ?>

         


            <li class="nav-item">
              <a class="nav-link" href="<?=base_url(). '/moduloComunicacion?country=' . base64_encode($_GET['country']) ?>"><?=$fnT('Communication module')?></a>
            </li>
        </ul>
      </div>
 

      <div class="row">
        <? if(in_array( $_SESSION['userData']['role']['id'], [1,2] )): ?>
          <div class="col-md-6 col-lg-3">
            <div class="widget-small info coloured-icon <?=$pend? 'cr-pointer' : 'cr-not-allowed'?>" onclick="<?=$pend? '' : 'return;'?> location.href = '<?=base_url() . '/audits?type=' . base64_encode('Standard')?>&filter=<?=base64_encode('Pending')?>'"><i class="icon fa fa-circle-o-notch fa-3x"></i>
              <div class="info">
                <h4><?=$fnT('Pending')?></h4>
                <p><b><?=$pend?></b></p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="widget-small warning coloured-icon <?=$inProc? 'cr-pointer' : 'cr-not-allowed'?>" onclick="<?=$inProc? '' : 'return;'?> location.href = '<?=base_url() . '/audits?type=' . base64_encode('Standard')?>&filter=<?=base64_encode('In Process')?>'"><i class="icon fa fa-spinner fa-3x"></i>
              <div class="info">
                <h4><?=$fnT('In Process')?></h4>
                <p><b><?=$inProc?></b></p>
              </div>
            </div>
          </div>
        <? endif ?>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small success coloured-icon <?=$comp? 'cr-pointer' : 'cr-not-allowed'?>" onclick="<?=$comp? '' : 'return;'?> location.href = '<?=base_url() . '/audits?type=' . base64_encode('Standard')?>&filter=<?=base64_encode('Completed')?>'"><i class="icon fa fa-check-square-o fa-3x"></i>
            <div class="info">
              <h4><?=$fnT('Completed')?></h4>
              <p><b><?=$comp?></b></p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="widget-small danger coloured-icon"><i class="icon fa fa-times fa-3x"></i>
            <div class="info">
              <h4><?=$fnT('Zero Tolerance')?></h4>
              <p><b><?=$zeto?></b></p>
            </div>
          </div>
        </div>
      </div>
    <? endif ?>

    <!-- <b class="my-1 b">*<?= $fnT('Information from the current year') ?></b> -->
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
                    <span><?=$fnT('New')?>&#160;&#160;<i class="fa fa-plus"></i></span>
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
                        <label for="input-title" class="col-sm-2 col-form-label"><?=$fnT('Title')?></label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="input-title" name="title" maxlength="128" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-description" class="col-sm-2 col-form-label"><?=$fnT('Description')?></label>
                        <div class="col-sm-10">
                          <textarea class="form-control" rows="3" id="input-description" name="description" maxlength="512" required></textarea>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-files" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                          <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" id="customFile" onchange="addFile(this)" accept=".xlsx,.pdf,.png,.jpg">
                            <label class="custom-file-label" for="customFile"><?=$fnT('Select files')?></label>
                          </div>
                          <div id="form-panel-files"></div>
                        </div>
                      </div>
                      <input type="text" name="id" class="d-none">
                      <div class="form-group row">
                        <div class="col-sm-10">
                          <button type="submit" class="btn btn-primary" id="btn-send-af"></button>
                          <button type="button" class="btn btn-danger" onclick="$('#collapseFormFile').collapse('hide')"><?=$fnT('Cancel')?></button>
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
                    <span class="input-group-text border-0"><?=$fnT('Main section')?></span>
                </div>
                <select class="form-control selectpicker" onchange="setTopOpp(this.value)" id="select-top-opp">
                  <option value="Aseguramiento de calidad" selected><?=$fnT('Aseguramiento de calidad')?></option>
                  <option value="Estandar de la marca" selected><?=$fnT('Estandar de la marca')?></option>
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
    
    <? if(in_array($_SESSION['userData']['user_id'], [1,20])): ?>
      <div class="row">
        <div class="col-md-6">
          <? 
            dep($_SESSION['userData']);
            echo '<br>---------------<br>';
            $periodo = date("Y-m");
            $roundInfo = knowRoundInfoBy('DQ', $periodo);
            echo $roundInfo['name'].'<br>';
          ?>
        </div>
      </div>
    <? endif ?>
  </main>

<?php footerTemplate($data);?>
<!-- <script>
  <? if($data['alert_se'] != false && $ON_SELFAUDIT): ?>
    const alertSE = <?=json_encode($data['alert_se'])?>;
    const numbers = alertSE.filter(item => item.month > 0).map(item => item.number).join(', ');
    if(numbers != ''){
      swal({
          title: fnT('Alert'),
          text: fnT('A self-assessment has not been performed in more than 30 days for the following stores') + ": " + numbers,
          type: 'warning'
      });
    }
  <? endif; ?>

  permissionDoc = <?=json_encode($data['permissionDoc'])?>;
</script> -->
  <script>

    $(document).ready(function() {
    // Creamos una variable JS 'country' con el valor recibido en PHP, escapado correctamente
    console.log("Documento listo con jQuery.");
    var country = <?php echo json_encode(isset($_GET['country']) ? $_GET['country'] : ''); ?>;
    console.log("País recibido: " + country);





    $('.bandera').removeClass('selected');
    $('#'+country).addClass('selected');
  



    
    // Aquí tu código que use 'country'
});
  </script>