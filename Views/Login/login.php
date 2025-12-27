<? 
  $lan = $_GET['lan'] ?? 'esp';
  $fnT = translate($lan, true); 
?>
<!DOCTYPE html>
<html lang="<?=$lan?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Miguel O.">
    <link rel="shortcut icon" href="<?=media()?>/images/hamburger.png">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?=media()?>/css/main.css">
    <link rel="stylesheet" type="text/css" href="<?=media()?>/css/login.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title><?=$data['page_tag']?></title>
    <?php if(date('n') == 12) { ?>
      <script src="https://www.icalsea.com/Scripts/snow/snow.js?ver=<?= date('ymdH') ?>"></script>
    <?php } ?>
  </head>
  <body>
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
          <div id="login-bg-label">
            <h3 class="text-light h4 d-none d-md-block"> </h3>
            <h2 class="text-light h1"> </h2>
          </div>
        </div>
        <div id="login-wrapper" class="col-lg-6 col-12 p-3 d-flex justify-content-center align-items-center">
          <form class="bg-white  rounded-top" onsubmit="logIn(this); return false;">
            <? if (strpos($_SERVER['HTTP_HOST'], '-stagexxx.') !== false): ?>
              <div class="bg-white text-center text-danger">
                <h4><?=$fnT('You are in a development environment, the production site for this site is the following link')?>
                <br><a target="_black" href="https://dqpridereports.bw-globalsolutions.com" class="primarytext-"><?=$fnT('DQ PRIDE Reports')?></a></h4>
              </div>
            <? endif ?>
            
            <p class="text-center mb-4"><img id="client-log" width="250" src="<?=media()?>/images/logo2.png" alt="<?=$data['cliente']['name']?>"></p>
            <!-- <h2 class="text-center m-4 h1"><?=$fnT('Loggin Account')?></h2> -->
            <div class="form-group">
              <input class="bg-light border-0 px-3 w-100" type="email" name="email" placeholder="Email ID" required>
            </div>
            <div class="form-group">
              <input class="bg-light border-0 px-3 w-100 toggle-pass" type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group form-check mb-4">
              <input type="checkbox" class="form-check-input" onchange="showPassword()" id="ck-show-input">
              <label class="form-check-label mr-4" for="ck-show-input"><?=$fnT('Show Password')?></label>
              <a class="float-right ml-4" href="#" onclick="recoverPass()"><?=$fnT('forget your password ?')?></a>
            </div>
            <button id="btn-submit-login" type="submit" class="btn btn-primary btn-lg btn-block mb-4">
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
        <button type="button" class="btn btn-sm btn-outline-primary position-absolute" id="btn-language" data-toggle="modal" data-target="#modal-laguage">
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
              <select class="form-control" id="login-language" onchange="location.replace(`/login?lan=${this.value}`)">
                <option disabled selected><?=$fnT('Select a language')?></option>
                <option value="esp">Spanish</option>
                <option value="eng">English</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$fnT('Close')?></button>
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