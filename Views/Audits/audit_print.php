<?php
  $fnT = translate($_SESSION['userData']['default_language']);
?>
<!DOCTYPE html>
<html lang="<?=$_SESSION['userData']['default_language']?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Erick P.">
        <link rel="shortcut icon" href="<?=media();?>/images/icono.png?<?=rand(1, 15)?>">

        <!-- Main CSS-->
        <link rel="stylesheet" type="text/css" href="<?=media()?>/css/main.css">
        
        <!-- Font-icon css-->
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <title><?=$data['page_tag']?></title>
    </head>
    <body>
        <div class="container">
            <button type="button" class="btn btn-primary btn-lg btn-block d-print-none my-2" onclick="window.print()"><?= $fnT('Pressione este botão ou CTRL + P para imprimir') ?></button>
            <? foreach($data['checklist_item'] as $section => $questions): ?>
                <h2 class="h4 mt-4"><?= $section ?></h2>                
                <hr class="mb-4">

                <? foreach($questions as $qprefix => $picklists): 
                    $currQuestion = reset(array_filter($picklists, function($item){ return $item['question']; })); ?>
                    <div class="card my-3">
                        <div class="card-header">
                            <?= $qprefix . '- ' . $currQuestion['text'] ?>
                            <button type="button" class="btn btn-danger float-right"><?= $currQuestion['points'] ?> pts</button>
                        </div>
                        <ul class="list-group list-group-flush">
                            <? foreach(array_filter($picklists, function($item){ return $item['picklist']; }) as $picklist): ?>
                                <li class="list-group-item">
                                    <?= $picklist['text'] ?>
                                    <hr>

                                    <div class="py-3 px-4 border" style="background-color: #CACFD2">
                                        <? foreach($picklist['answers'] as $answer): ?>
                                            <p><i class="fa fa-square-o" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<?= $answer ?></p>
                                        <? endforeach ?>
                                    </div>

                                </li>
                            <? endforeach ?>
                        </ul>
                    </div>
                <? endforeach ?>
            <? endforeach ?>
        </div>
    </body>
    <script> window.print() </script>
</html>