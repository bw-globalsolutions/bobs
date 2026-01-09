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
                <i class="fa fa-clock-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
            <p><?=$fnT('View the photos corresponding to the audit')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-list-ol fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
    </div>
    <? headerTemplateAudits($_GET['id'], 'Times') ?>
    <div class="tile">
        <div class="tile-body contAllTimes">
            <?php foreach($data['times'] as $key=>$time){ 
                if(!in_array($key, ['win_time','dt_time'])){
                    if (strpos($time, ':') !== false) {
                        // Si contiene ':', dividimos en minutos y segundos
                        list($minutos, $segundos) = explode(':', $time, 2);
                    } else {
                        // Si no contiene ':', minutos es el valor y segundos 0
                        $minutos = $time;
                        $segundos = 0;
                    }
                    
                    // Convertimos a enteros por si acaso
                    $minutos = (int)$minutos;
                    $segundos = (int)$segundos;?>
                    <div class="contTime">
                        <p style="margin:0;"><?=$key?>:</p>
                        <input type="number" class="input-s1" onchange="calcularPromedio()" style="max-width: 50px;" id="<?=$key?>M" value="<?=$minutos?>" min="0">:
                        <input type="number" class="input-s1" onchange="calcularPromedio()" style="max-width: 50px;" id="<?=$key?>S" value="<?=$segundos?>" min="0">
                    </div>
                <? } ?>
            <? } ?>
            <div class="contTime" style="padding:10px; background-color:var(--color1); border-radius:10px;">
                    <p style="margin:0;"><?=$fnT('average time')?>:</p>
                    <b id="averageM" style="font-size:50px;"></b><span style="font-size:20px;">:</span>
                    <b id="averageS" style="font-size:20px;"></b>
            </div>
        </div>
        <button class="btn-s1 btn btn-md btn-primary" style="margin-top: 20px;" onclick="saveTimes(<?=$data['id']?>)"><?=$fnT('Save')?></button>
    </div>
    <a class="back-to-top"><i class="fa fa-arrow-up"></i></a>
</main>
<?php footerTemplate($data);?>