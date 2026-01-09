<? 
  $lan = $_GET['lan'] ?? 'eng';
  $fnT = translate($lan, true); 
?>
<!DOCTYPE html>
<html lang="<?=$lan?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Miguel O.">
    <link rel="shortcut icon" href="<?=media()?>/images/icono.png?<?=rand(1, 15)?>">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?=media()?>/css/colors.css">
    <link rel="stylesheet" type="text/css" href="<?=media()?>/css/main.css">
    <link rel="stylesheet" type="text/css" href="<?=media()?>/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title><?=$data['page_tag']?></title>
    <?php if(date('n') == 12) { ?>
      <script src="https://www.icalsea.com/Scripts/snow/snow.js?ver=<?= date('ymdH') ?>"></script>
    <?php } ?>
  </head>
  <body>
    <div class="contW">
      <h2 class="lblW">Welcome <span class="nameU"></span></h2>
    </div>
    <div class="container-fluid">
      
      <!-- <div class="row">
        <div id="login-bg21" class="col-12 text-center">
          <div class="alert alert-danger" role="alert">
            <h3 class="text-dark d-md-block">Dear user, the system is going through maintenance, the service will be reestablished shortly.<br>Estimado usuario, el sistema se encuentra en mantenimiento, el servicio se restablecerá en breve.</h3>
          </div>
        </div>
      </div> -->

      <div class="row">
        <div id="login-bg" class="col-lg-6 col-12 p-3 position-relative d-flex justify-content-center align-items-center">
          <div>
            <h1 class="lblMarca"><?=WEB_EMPRESA?></h1>
            <h3 class="text-light h4 d-none d-md-block"> </h3>
            <h2 class="text-light h1"> </h2>
          </div>
        </div>
        <div id="login-wrapper" class="col-lg-6 col-12 p-3 d-flex justify-content-center align-items-center">
          <form class="bg-white  rounded-top" onsubmit="logIn(this); return false;">
            <? if (strpos($_SERVER['HTTP_HOST'], '-stage.') !== false): ?>
              <div style="display:none;" class="bg-white text-center text-danger">
                <h4><?=$fnT('You are in a development environment')?>
              </div>
            <? endif ?>
            
            <p class="text-center mb-4"><img id="client-log" width="250" src="<?=media()?>/images/logo.png?<?=rand(1, 15)?>" alt="<?=$data['cliente']['name']?>"></p>
            <!-- <h2 class="text-center m-4 h1"><?=$fnT('Loggin Account')?></h2> -->
            <div class="form-group">
              <div class="drop-icon" style="position:absolute;">
                <svg xmlns="http://www.w3.org/2000/svg" class="icono" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M12 4a4 4 0 1 1 0 8a4 4 0 0 1 0-8m0 16s8 0 8-2c0-2.4-3.9-5-8-5s-8 2.6-8 5c0 2 8 2 8 2"/></svg>
              </div>
              <input class="bg-light border-0 px-3 w-100" type="email" id="email" name="email" placeholder="Email ID" required>
            </div>
            <div class="form-group">
              <div class="drop-icon" style="position:absolute;">
                <svg xmlns="http://www.w3.org/2000/svg" class="icono" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M6 22q-.825 0-1.412-.587T4 20V10q0-.825.588-1.412T6 8h1V6q0-2.075 1.463-3.537T12 1t3.538 1.463T17 6v2h1q.825 0 1.413.588T20 10v10q0 .825-.587 1.413T18 22zm6-5q.825 0 1.413-.587T14 15t-.587-1.412T12 13t-1.412.588T10 15t.588 1.413T12 17M9 8h6V6q0-1.25-.875-2.125T12 3t-2.125.875T9 6z"/></svg>
              </div>
              <input class="bg-light border-0 px-3 w-100 toggle-pass" type="password" id="pass" name="password" placeholder="Password" required>
            </div>
            <div class="form-group flex">
              <div class="flexcy">
                <label class="checkBox">
                  <input id="ch1" onchange="showPassword()" type="checkbox">
                  <div class="transition"></div>
                </label>
                <label class="form-check-label" for="ch1"><?=$fnT('Show Password')?></label>
              </div>
              <a class="float-right ml-4" href="#" onclick="recoverPass()"><?=$fnT('forget your password ?')?></a>
            </div>
            <button id="btn-submit-login" type="submit" class="btn btn-lg btn-block mb-4 btn-s1">
              <?=$fnT('Login')?>
              &nbsp;&nbsp;<img class="d-none" id='lodaer' src="<?=media()?>/images/loading-white.svg" alt="Loading">
            </button>
            <p class="text-center">
              <?=$fnT('See our privacy policy')?>: 
              <a target="_black" href="https://www.arguilea.com/assets/docs/<?=$fnT('privacyEN')?>.pdf"><?=$fnT('here')?></a>
            </p>
          </form>
        </div>
        <img id="argui-log" width="110" class="position-absolute" src="<?=media()?>/images/logo_min_arguilea.png" alt="Arguilea">
        <button type="button" class="btn btn-sm btn-s2 position-absolute" id="btn-language" data-toggle="modal" data-target="#modal-laguage">
          <i class="fa fa-language" aria-hidden="true"></i>
          <?=$fnT('Language')?>
        </button>
      </div>
    </div>

    <div class="modal fade" id="modal-laguage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><?=$fnT('Language menu')?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="login-language"><?=$fnT('Select the language')?></label>
              <select class="form-control input-s1" id="login-language" onchange="location.replace(`/login?lan=${this.value}`)">
                <option disabled selected><?=$fnT('Select a language')?></option>
                <option value="esp">Spanish</option>
                <option value="eng">English</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-s2" data-dismiss="modal"><?=$fnT('Close')?></button>
          </div>
        </div>
      </div>
    </div>

    <script>
        const base_url = "<?=base_url()?>";
        const regExEmail = /<?=$data['regExEmail']?>/;
    </script>
    <!-- Essential javascripts for application to work-->
    <script src="<?=media()?>/js/jquery-3.3.1.min.js"></script>
    <script src="<?=media()?>/js/popper.min.js"></script>
    <script src="<?=media()?>/js/bootstrap.min.js"></script>

    <!-- The javascript plugin to display page loading on top-->
    <script src="<?=media()?>/js/plugins/pace.min.js"></script>
    <script type="text/javascript" src="<?=media()?>/js/plugins/sweetalert.min.js"></script>
    <script src="<?=media()?>/js/<?=$data['page-functions_js']?>"></script>
  </body>
</html>