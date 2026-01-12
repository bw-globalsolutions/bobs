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
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/colors.css?<?=date('yhi')?>">
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/main.css?<?=date('yhi')?>">
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo media()?>/css/style2.css?<?=date('yhi')?>">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>
  <script>
    //cargarTema();

function cargarTema(){

    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/personalization/cargarTema';
            var strData = "id=1";
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){

                if(request.readyState == 4 && request.status == 200){
                    //console.log(request.responseText);
                    var objData = JSON.parse(request.responseText);

                    document.documentElement.style.setProperty("--color1", objData[0].color1);
                    document.documentElement.style.setProperty("--color2", objData[0].color2);
                    document.documentElement.style.setProperty("--color3", objData[0].color3);
                    document.documentElement.style.setProperty("--color4", objData[0].color4);

                    if(objData[0].img2!=''){
                        if(document.querySelector('.img-fluid')){
                            document.querySelector('.img-fluid').src=objData[0].img2;
                        }
                    }

                    if(objData[0].img3!=''){
                        // Obtener el elemento del favicon (si existe)
                        const favicon = document.querySelector('link[rel="icon"]') || 
                        document.createElement('link');

                        // Configurar sus atributos
                        favicon.rel = 'icon';
                        favicon.href = objData[0].img3; // Ruta del nuevo favicon
                        favicon.type = 'image/x-icon';

                        // Añadirlo al <head> si no existía
                        document.head.appendChild(favicon);
                    }

                }
            }
}
  </script>
  <body class="app sidebar-mini">
    <div id="divLoading">
      <div class="loop cubes">
        <div class="item cubes"></div>
        <div class="item cubes"></div>
        <div class="item cubes"></div>
        <div class="item cubes"></div>
        <div class="item cubes"></div>
        <div class="item cubes"></div>
      </div>
    </div>
    <!-- Navbar-->
    <header class="app-header"><a class="app-header__logo" href="<?=base_url()?>/home" style="font-family: Calibri,Arial;" id="header-main-logo"><?=NOMBRE_EMPESA?></a>
    <!-- Sidebar back-->
    <? if($isBack): ?>
      <script>
      window.onload = function() {
        if(document.querySelector('.app-title div h1')){
          document.querySelector('.app-title div h1').innerHTML='<button class="btn-s3" style="margin-right:10px;" onclick="window.history.back()"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 512 512"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M328 112L184 256l144 144"/></svg></button>'+document.querySelector('.app-title div h1').innerHTML;
        }
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
            <li><a class="dropdown-item" href="<?=base_url()?>/usuarios/perfil"><i class="fa fa-user fa-lg"></i> <?=$fnT('Perfil')?></a></li>
            <?php }?>
            <li><a class="dropdown-item" href="<?=base_url()?>/logout"><i class="fa fa-sign-out fa-lg"></i> <?=$fnT('Sair')?></a></li>
          </ul>
        </li>
      </ul>
    </header>
    <?php require_once("nav.php");?>