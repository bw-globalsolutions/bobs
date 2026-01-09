<?
  global $fnT;
  $fnT = translate($_SESSION['userData']['default_language'], true);
  $isBack = strpos($_SERVER['HTTP_REFERER'], BASE_URL) !== false && strpos($_SERVER['HTTP_REFERER'], '/login') === false;
?>
<!DOCTYPE html>
<html lang="<?=formLanguage()?>">
  <head>
    <meta charset="utf-8">
    <meta name="description" content="Auditorias">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Alex G.">
    <meta name="theme-color" content="#009688">
    <link rel="shortcut icon" href="<?=media();?>/images/icono.png?<?=rand(1, 15)?>">
    <link rel="shortcut icon" href="">
    <title><?= $data['page_title']?></title>
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/main.css?<?=date('yhi')?>">
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/style2.css?<?=date('yhi')?>">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/adminlte.min.css">

<!-- Font-icon css
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">-->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/2.1.0/css/searchPanes.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.5.0/css/select.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

  </head>
  <body class="app sidebar-mini">
    <div id="divLoading">
      <div><img src="<?=media()?>/images/loading.svg" alt="Loading"></div>
    </div>
    <!-- Navbar-->
    <header class="app-header"><a class="app-header__logo" href="<?=base_url()?>/home" style="font-family: Calibri,Arial;" id="header-main-logo"><?=NOMBRE_EMPESA?></a>
    <!-- Sidebar back-->
    <? if($isBack): ?>
      <script>if(document.querySelector('.app-title div h1')){
        document.querySelector('.app-title div h1').innerHTML='<button>Atras</button>'+document.querySelector('.app-title div h1').innerHTML;
      }</script>
      <!--<a class="app-sidebar__toggle btn-back" onclick="window.history.back()" href="#"></a>-->
    <? endif; ?>
    <!-- Sidebar toggle button--><a class="app-sidebar__toggle" style="z-index: 3; <?=$isBack? 'margin-right: -21.6' : '' ?>" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
      <ul class="app-nav">
        <!-- User Menu-->
        <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
          <ul class="dropdown-menu settings-menu dropdown-menu-right">
            <!--<li><a class="dropdown-item" href="<?=base_url()?>/opciones"><i class="fa fa-cog fa-lg"></i> Settings</a></li>-->
            <?php if($_SESSION['userData']['profile'] != 'dmanager' && $_SESSION['userData']['profile'] != 'tienda'){?>
            <li><a class="dropdown-item" href="<?=base_url()?>/usuarios/perfil"><i class="fa fa-user fa-lg"></i> <?=$fnT('Profile')?></a></li>
            <?php }?>
            <li><a class="dropdown-item" href="<?=base_url()?>/logout"><i class="fa fa-sign-out fa-lg"></i> <?=$fnT('Exit')?></a></li>
          </ul>
        </li>
      </ul>
    </header>
    <?php require_once("nav.php");?>