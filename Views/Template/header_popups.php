<?php $lang=traducir();?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="description" content="Auditorias">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Alex G.">
    <meta name="theme-color" content="#009688">
    <link rel="shortcut icon" href="<?=media();?>/images/favicon.ico">
    <title><?= $data['page_title']?></title>
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/mainResults.css?<?=date('yhi')?>">
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/style2.css?<?=date('yhi')?>">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>
  <body>
    <div id="divLoading">
      <div><img src="<?=media()?>/images/loading.svg" alt="Loading"></div>
    </div>

    <!--<header class="app-header"><a class="app-header__logo" href="#" style="font-family: Calibri,Arial;">Arguilea</a></header>-->