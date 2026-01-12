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
        <meta name="author" content="Miguel O.">
        <link rel="shortcut icon" href="<?=media()?>/imagenes/icono.png?<?=rand(1, 15)?>">

        <!-- Main CSS-->
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/colors.css">
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/main.css">
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/audit_report.css">
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/style2.css">
        
        <!-- Font-icon css-->
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <title><?=$data['page_tag']?></title>
    </head>
    <body>
        <div id="divLoading">
            <div><img src="<?=media()?>/images/loading.svg" alt="Loading"></div>
        </div>
        <div class="container">
            <div class="d-flex justify-content-center mt-3 mb-1">
                <img class="img-fluid" src="<?=media()?>/images/logo.png?<?=rand(1, 15)?>" alt="Logo" id="img-main">
            </div>
            <br>
            <div class="bg-white rounded p-3 mb-5">
                <form id="formAuditorSurvey">
                    <input type="hidden" name="id_audit" value=<?=$data['audit_id']?> />
                    <div class="row">
                        <div class="col-lg-12">
                            <? foreach($data['questions'] as $q): ?>

                                <? if($q['type'] == 'options'): ?>


                                <div class="card mb-3">
                                    <h5 class="card-header bg-ws"><?=$lan!='eng'?$q['question_esp']:$q['question_eng']?></h5>
                                    <div class="card-body p-2">
                                        <? foreach($q['answers'] as $a): ?>

                                            <? if($data['ok']): ?>
                                                <label class="btn <?=$q['answer']==$a?'btn-dark':'btn-info'?> optRespuesta" onclick="setAns(this)"><?=$fnT($a)?></label>
                                            <? else: ?>
                                                <label class="btn <?=$q['answer']==$a?'btn-dark':'btn-info'?> optRespuesta"><?=$fnT($a)?></label>
                                            <? endif ?>

                                        <? endforeach ?>
                                        <input type="hidden" name="qID[][<?=$q['id']?>]"/>
                                    </div>
                                </div>
                                <? endif ?>
                                
                            <? endforeach ?>
                            
                            <!--<div class="card mb-3">
                                <h5 class="card-header bg-ws"><?=$fnT('COMENTÁRIO DO AUDITOR')?></h5>
                                <div class="card-body p-2">        
                                    <textarea class="form-control comentario_auditor" name="comentario_auditor" ><?=$q['answer']?></textarea>
                                </div>
                            </div>-->



                        </div>
                        <div class="col-lg-12">
                            <? if($data['ok']): ?>
                                <button id="btnFormAuditorSurvey" class="btnFormAuditorSurvey float-right btn btn-primary" type="button" value="Button"><i class="fa fa-fw fa-lg fa-check-circle"></i><?=$fnT('Enviar respostas')?></button>
                            <? endif ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Essential javascripts for application to work-->
        <script src="<?=media()?>/js/jquery-3.3.1.min.js"></script>
        <script src="<?=media()?>/js/popper.min.js"></script>
        <script src="<?=media()?>/js/bootstrap.min.js"></script>

        <!-- The javascript plugin to display page loading on top-->
        <script src="<?=media()?>/js/plugins/pace.min.js"></script>
        <script type="text/javascript" src="<?=media()?>/js/plugins/sweetalert.min.js"></script>
        <script type="text/javascript" src="<?=media()?>/js/functions_generals.js"></script>
        <script type="text/javascript" src="<?=media()?>/js/<?=$data['page-functions_js']?>"></script>
        <script>
            base = "<?=base_url()?>";
        </script>
    </body>
</html>