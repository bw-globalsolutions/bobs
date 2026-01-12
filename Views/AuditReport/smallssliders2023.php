<?php
  $lan = $_GET['lan'] ?? 'eng';
  $fnT = translate($lan);
?>
<!DOCTYPE html>
<html lang="<?=$lan?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Erick P.">
        <link rel="shortcut icon" href="<?=media();?>/images/smalls-favicon.png">

        <!-- Main CSS-->
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/main.css">
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/audit_report.css">
        
        <!-- Font-icon css-->
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <title><?=$data['page_tag']?></title>
    </head>
    <body class="bg-ss">
        <div class="container">
            <div class="d-flex justify-content-center mt-3 mb-1">
                <img class="img-fluid" src="<?=media()?>/images/logo2_Smalls Sliders.png" alt="Logo" id="img-main">
            </div>
            <div class="bg-white rounded p-3 mb-5">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="card mb-3">
                            <h5 class="card-header hd-ss"><?=$fnT('Informações da avaliação')?></h5>
                            <div class="card-body p-2">
                                <table class="w-100" cellpadding="3">
                                    <tr>
                                        <td align="right"><?=$fnT('Tipo')?>:</td>
                                        <td>&nbsp;&nbsp; <b><?=$fnT($data['audit']['type'])?></b></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><?=$fnT('Rodada')?>:</td>
                                        <td>&nbsp;&nbsp; <b><?=$data['audit']['round_name']?></b></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><?=$fnT('Início')?>:</td>
                                        <td>&nbsp;&nbsp; <b><?=date_format(date_create($data['audit']['date_visit']), "M d, Y H:i")?></b></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><?=$fnT('Fim')?>:</td>
                                        <td>&nbsp;&nbsp; <b><?=date_format(date_create($data['audit']['date_visit_end']), "M d, Y H:i")?></b></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><?=$fnT('Porta da frente')?>:</td>
                                        <td>
                                            &nbsp;&nbsp;
                                            <img class="of-contain" width="92" height="121" src="<?=$data['audit']['picture_front']?? media().'/images/no-image-available.jpg' ?>" alt="Picture of the Front Door/Entrance of the Restaurant">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><?=$fnT('Localização')?>:</td>
                                        <td>&nbsp;&nbsp; <b>#<?=$data['audit']['location_number']?> - <?=$data['audit']['location_name']?></b></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><?=$fnT('Endereço')?>:</td>
                                        <td>&nbsp;&nbsp; <b><?=$data['audit']['location_address']?></b></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><?=$fnT('Nome do auditor')?>:</td>
                                        <td>&nbsp;&nbsp; <b><?=$data['audit']['auditor_name']?></b></td>
                                    </tr>
                                    <tr><td colspan="2"><hr></td></tr>
                                    <tr>
                                        <td align="right"><?=$fnT('Gerente')?>:</td>
                                        <td>&nbsp;&nbsp; <b><?=$data['audit']['manager_name']?></b></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><?=$fnT('Assinatura')?>:</td>
                                        <td>&nbsp;&nbsp; 
                                            <img width="121" height="92" class="of-contain" src="<?= empty($data['audit']['manager_signature']) || $data['audit']['manager_signature']=='Sin Firma'? media().'/images/no-image-available.jpg' : $data['audit']['manager_signature'] ?>" alt="Manager signature">
                                        </td>
                                    </tr>
                                </table>      
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card mb-3">
                            <div class="card-header hd-ss d-flex text-center">
                                <h6 class="col border-right d-none d-md-block"><?=$fnT('FALHA AUTOMÁTICA')?></h6>
                                <h6 class="col border-right d-none d-md-block"><?=$fnT('SEGURANÇA ALIMENTAR')?></h6>
                                <h6 class="col"><?=$fnT('RESULTADO')?></h6>
                            </div>
                            <div class="card-body d-flex p-2">
                                <div class="col d-none d-md-flex justify-content-center align-items-center"><b class="display-3"><?=$data['scoring']['AutomaticFail']??0?></b><span>#</span></div>
                                <div class="col d-none d-md-flex justify-content-center align-items-center"><b class="display-3"><?=$data['scoring']['FootSafety']??0?></b><span>%</span></div>
                                <div class="col d-flex justify-content-center align-items-center flex-column">
                                    <b class="text-shadow display-5" style="color: <?=getScoreDefinition($data['scoring']['Calificacion'])[0]?>; font-size: 15px;">"<?=$fnT($data['scoring']['Calificacion'])?>"</b>
                                </div>
                            </div>
                            <? if($data['prev_scoring']): ?>
                                <div class="card-footer ">
                                    <?=$fnT('Pontuação anterior')?>:&nbsp;&nbsp; <b><?=$data['prev_scoring']['Calificacion']?></b> - <?=$fnT(getScoreDefinition($data['prev_scoring']['Calificacion'])[1])?>&nbsp;&nbsp; <b>(<?=$data['prev_scoring']['AutomaticFail'] + $data['prev_scoring']['FootSafety']?>pts)</b>
                                </div>
                            <? endif ?>
                        </div>
                        <? 
                            foreach($data['mains'] as $m => $sections):
                            $points = 0; $questions = 0; $auto_fail = 0;
                        ?>
                            <div class="card mb-3">
                                <h6 class="card-header hd-ss"><?=$fnT($m)?></h6>
                                <div class="card-body p-2">
                                    <table class="w-100 score" cellpadding="4">
                                        <thead class="border-bottom">
                                            <tr>
                                                <th><?=$fnT('Nome')?></th>
                                                <th><?=$fnT('Oportunidades')?></th>
                                                <th><?=$fnT('Pontos perdidos')?></th>
                                                <th><?=$fnT('Percentual')?></th>
                                                <th><?=$fnT('Meta')?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-bottom">
                                            <? foreach($sections as $s): ?>
                                                <tr>
                                                    <td>#<?=$s['section_number']?> - <?=$fnT($s['section_name'])?></td>
                                                    <td><?=$s['questions']?> <small>#</small></td>
                                                    <td><?=$s['points']?> <small>pts</small></td>
                                                    <td><?= floor($s['points'] / $s['target'] * 100)?> <small>%</small></td>
                                                    <td><?=$s['target']?> <small>pts</small></td>
                                                </tr>
                                            <? 
                                                $points += $s['points']; $questions += $s['questions']; $target += $s['target'];
                                                endforeach; 
                                            ?>
                                        <tfoot>
                                            <tr>
                                                <th><?=$fnT('Total')?></th>
                                                <th><?=$questions?> <small>#</small></th>
                                                <th><?=$points?> <small>pts</small></th>
                                                <th></th>
                                                <th><?=$target?> <small>pts</small></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        <? endforeach ?>
                    </div>
                </div>
            </div>
            <div class="card mb-5">
                <h4 class="card-header bg-danger text-white"><i class="fa fa-bolt"></i>&nbsp;&nbsp;<?=$fnT('Detalhe da oportunidade')?></h4>
                <div class="card-body pb-0">
                    <? foreach($data['questions'] as $s => $questions): ?>
                        <ul class="list-group mb-4">
                            <li class="list-group-item bg-light"><h5 class="mb-0">&#187; <?=$fnT($s)?></h5></li>
                            <? foreach($questions  as $q): ?>
                                <li class="list-group-item">
                                    <span class="badge badge-secondary"><?=$q['prefix']?></span> - 
                                    <? if(!empty($q['priority'])): ?>
                                        <b class="<?=$q['priority']=='Critical'? 'text-danger' : '' ?>"><?=$fnT($q['priority'])?>:</b>&nbsp;
                                    <? endif ?>
                                    <?=$q['question']?>
                                    <? foreach($q['picklist'] as $p): ?>
                                        <div class="pl-4 my-2">
                                            <p class="mb-1"><i class="fa fa-bolt text-danger"></i> <b><?=$p['text']?> :</b>&nbsp;&nbsp;<?=implode(', ',$p['answers'])?></p>
                                            <? if(!empty($p['comment'])): ?>
                                                <p class="mb-1"><i class="fa fa-comment text-warning"></i> <?=$p['comment']?></span></p>
                                            <? endif ?>
                                            <? foreach($p['stack_img'] as $url): ?>
                                                <a href="<?=$url?>" target="_blank">
                                                    <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer mr-2" src="<?=$url?>">
                                                </a>
                                            <? endforeach ?>
                                        </div>
                                    <? endforeach ?>
                                </li>
                            <? endforeach ?>
                        </ul>
                    <? endforeach ?>
                </div>
            </div>
        </div>
        
        <!-- Essential javascripts for application to work-->
        <script src="<?=media()?>/js/jquery-3.3.1.min.js"></script>
        <script src="<?=media()?>/js/popper.min.js"></script>
        <script src="<?=media()?>/js/bootstrap.min.js"></script>

        <!-- The javascript plugin to display page loading on top-->
        <script src="<?=media()?>/js/plugins/pace.min.js"></script>
        <script type="text/javascript" src="<?=media()?>/js/plugins/sweetalert.min.js"></script>
    </body>
</html>