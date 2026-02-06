<?php
  //$lan = $_GET['lan'] ?? 'eng';
  //$lan = 'eng';
  $lan = ($data['lan']==NULL?$lan='eng':$data['lan']);
  $fnT = translate($lan);
?>
<!DOCTYPE html>
<html lang="<?=$lan?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Miguel O.">
        <link rel="shortcut icon" href="<?=media();?>/images/icono.png?<?=rand(1, 15)?>">

        <!-- Main CSS-->
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/main.css">
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/colors.css">
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/audit_report.css">
        
        <!-- Font-icon css-->
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <title><?=$data['page_tag']?></title>
    </head>
    <style>
        .card-header.hd-ss {
            color: var(--color4);
            
        }
        .card-header:first-child {
            
        }

    </style>
    <body class="bg-ss">
        <div class="container">
            <div class="d-flex justify-content-center mt-1 mb-1">
                <img class="img-fluid" src="<?=media()?>/images/logo.png?<?=rand(1, 15)?>" alt="Logo BOBS" id="img-main" height="100">
            </div>
            <b style="position:absolute; top:10px; right:10px;">#<?=$data['audit']['location_number']?> - <?=$data['audit']['location_name']?></b>
            <div class="bg-white rounded p-3 mb-5">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="card mb-3">
                            <h5 class="card-header hd-ss" style="color:#fff; background-color: #000; font-weight: bold; border-radius: calc(0.25rem - 1px) calc(0.25rem - 1px) 0 0; padding: 0.75rem 1.25rem;"><?=$fnT('Informações da avaliação')?></h5>
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
                                    <? if($data['audit']['type']!='Self-Evaluation'){ ?>
                                    <tr>
                                        <td align="right"><?=$fnT('Assinatura')?>:</td>
                                        <td>&nbsp;&nbsp; 
                                            <img width="121" height="92" class="of-contain" src="<?= empty($data['audit']['manager_signature']) || $data['audit']['manager_signature']=='Sin Firma'? media().'/images/no-image-available.jpg' : $data['audit']['manager_signature'] ?>" alt="Manager signature">
                                        </td>
                                    </tr>
                                    <? } ?>
                                </table>      
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card mb-3">
                            <div class="d-flex text-center">
                                <div class="col card-header" style="background-color: #4dea62;">
                                    <h6><?=$fnT('Segurança dos Alimentos')?></h6>
                                </div>
                                <div class="col card-header" style="background-color: #4deae7;">
                                    <h6><?=$fnT('Padrões da Marca')?></h6>
                                </div>
                            </div>

                            <div class="card-body d-flex p-2">
                                <div class="col  d-md-flex justify-content-center align-items-center"><b class="display-4"><?=$data['scoring']['FootSafety']??0?></b><span>%</span></div>
                                <div class="col  d-md-flex justify-content-center align-items-center"><b class="display-4"><?=$data['scoring']['OperationsE']??0?></b><span>%</span></div>
                            </div>
                            <!-- <div class="card-header hd-red d-flex text-center">
                                <h6 class="col border-right d-none d-md-block"><?=$fnT('Verde')?></h6>
                                <h6 class="col border-right d-none d-md-block"><?=$fnT('Amarelo')?></h6>
                                <h6 class="col"><?=$fnT('Vermelho')?></h6>
                            </div> -->
                            <div class="d-flex text-center">
                                <div class="col card-header" style="background-color: var(--color1);">
                                    <h6><?=$fnT('Pontuação Geral')?></h6>
                                </div>
                            </div>
                            <div class="card-body d-flex p-2">
                                <!-- <div class="col d-none d-md-flex justify-content-center align-items-center"><b class="display-4"><?=$data['scoring']['Verdes']??0?></b><span>#</span></div> -->
                                <div class="col  d-md-flex justify-content-center align-items-center"><b class="display-4"><?=$data['scoring']['OverallScore']??0?></b><span>%</span></div>
                            </div>
                            <!--<div class="card-header hd-ss d-flex text-center">
                                <h6 class="col"><?=$fnT('Letra')?></h6>
                            </div>
                            <div class="card-body d-flex p-2">
                                <div class="col d-none d-md-flex justify-content-center align-items-center" style="background-color:<?=$data['scoring']['color']?>; color:#fff;"><b class="display-4"><?=$data['scoring']['Letra']??0?></b><span>#</span></div>
                            </div>-->
                            
                        </div>
                        <? 
                            foreach($data['mains'] as $m => $sections):
                            if(!in_array($m, ['Informações Iniciais'])){
                            $points = 0; $questions = 0;
                        ?>
                            <div class="card mb-3">
                                <h6 class="card-header hd-ss"><?=$fnT($m)?></h6>
                                <div class="card-body p-2">
                                    <table class="w-100 score" cellpadding="4">
                                        <thead class="border-bottom">
                                            <tr>
                                                <th><?=$fnT('Nome')?></th>
                                                <th><?=$fnT('Oportunidades')?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-bottom">
                                            <? foreach($sections as $s): ?>
                                                <tr>
                                                    <td><?=$fnT($s['section_name'])?></td>
                                                    <td><?=$s['questions']?></td>
                                                </tr>
                                            <? 
                                                $points += $s['points']; $questions += $s['questions']; $target += $s['target'];
                                                endforeach; 
                                            ?>
                                        <tfoot>
                                            <tr>
                                                <th><?=$fnT('Total')?></th>
                                                <th><?=$questions?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <? } ?>
                        <? endforeach ?>
                    </div>
                </div>
            </div>
            <?php
            foreach($data['mains'] as $m => $sections){
                if(!in_array($m, ['Informações Iniciais'])){
                $pointsT = 0; $targetT = 0;
            ?>
            <table class="tabla" style="margin-top:10px; margin-bottom:10px;">
                <thead>
                    <tr><th colspan="4" style="text-align:center; background-color: var(--color5);">Resumo por seção</th></tr>
                    <tr><th colspan="4" style="text-align:center;"><?=$m?></th></tr>
                    <tr>
                        <th>Nome da seção</th>
                        <th>Pontos Ganhos</th>
                        <th>Pontos possíveis</th>
                        <th>Pontuação da Seção</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($sections as $s){ 
                        if(in_array($s['section_name'], ['PERGUNTA EXTRA'])){
                            $pointsT+=$s['puntos_win'];
                        }else{
                            $pointsT+=$s['puntos_win'];
                            $targetT+=$s['target'];
                        } ?>
                        <tr>
                            <td><?=$s['section_name']?></td>
                            <td><?=$s['puntos_win']?></td>
                            <td><?=$s['target']?></td>
                            <td><?=$s['porcentaje']?></td>
                        </tr>
                    <? } 
                    $promedio = ($m=="Segurança dos Alimentos"?$data['scoring']['FootSafety']??0:round(($pointsT/$targetT)*100, 2));
                    if($promedio>100)$promedio=100;
                    ?>
                    <tr style="background-color: var(--color6); color: var(--color4);">
                        <td>Média de <?=$m?></td>
                        <td><?=$pointsT?></td>
                        <td><?=$targetT?></td>
                        <td><?=$promedio?></td>
                    </tr>
                </tbody>
            </table>
            <? } } ?>
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
                                    <?=$q['question']?> <span class="badge badge-secondary" style="background-color:var(--color9); padding: 5px;">-<?=$q['valor']?> pts</span>
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
        <script>
            const urlParams = new URLSearchParams(window.location.search);
            const download = urlParams.get('download');

            if(download==1){
                window.print();
                setTimeout(()=>{window.close()}, 1000);
                
            }
        </script>
    </body>
</html>