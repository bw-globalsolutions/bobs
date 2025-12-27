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
    <meta name="author" content="Erick P.">
    <link rel="shortcut icon" href="<?=media()?>/images/favicon.ico">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?=media()?>/css/main.css">
    <link rel="stylesheet" type="text/css" href="<?=media()?>/css/login.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title><?=$data['page_tag']?></title>
  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
        <div id="login-bg" class="col-lg-6 col-12 p-3 position-relative d-flex justify-content-center align-items-center">
          <div id="login-bg-label">
            <h3 class="text-light h4 d-none">Sit amet consectetur adipisicing elit. Veritatis expedita amet ut quia.</h3>
            <h2 class="text-light h1 d-none">Lorem ipsum dolor</h2>
          </div>
        </div>
        <div id="login-wrapper" class="col-lg-6 col-12 p-3 d-flex justify-content-center align-items-center">
          <form class="bg-white  rounded-top" onsubmit="resetPassword(this); return false;">
            <h2 class="text-center m-4 h1"><?=$fnT('Reset Password')?></h2>
            <p class="text-center mb-5">
              <?=$fnT('Before continuing, do not forget that you can review our')?>
              <b> <?=$fnT('privacy notice')?></b> <?=$fnT('by clicking')?>:  
              <a target="_black" href="https://www.arguilea.com/assets/docs/<?=$fnT('privacyEN')?>.pdf"><?=$fnT('here')?></a>
            </p>
            <div class="form-group">
              <input class="bg-light border-0 px-3 w-100 toggle-pass" type="password" pattern="<?=$data['regExPass']?>" title="<?=$fnT('Minimum 12 characters, uppercase and lowercase, plus at least one number')?>" name="password" placeholder="<?=$fnT('New password')?>" maxlength="32" required>
            </div>
            <div class="form-group">
              <input class="bg-light border-0 px-3 w-100 toggle-pass" type="password" pattern="<?=$data['regExPass']?>" title="<?=$fnT('Minimum 12 characters, uppercase and lowercase, plus at least one number')?>" name="password2" placeholder="<?=$fnT('Retype new password')?>" maxlength="32" required>
            </div>
            <div class="form-group form-check mb-5">
              <input type="checkbox" class="form-check-input" id="ck-show-input" onchange="showPassword()">
              <label class="form-check-label" for="ck-show-input"><?=$fnT('Show Password')?></label>
            </div>
            <input type="hidden" name="token" value="<?=$data['token']?>">
            <button id="btn-submit-login" type="submit" class="btn btn-primary btn-lg btn-block">
              <?=$fnT('Save')?>
              &nbsp;&nbsp;<img class="d-none" id='lodaer' src="<?=media()?>/images/loading-white.svg" alt="Loading">
            </button>
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
              <select class="form-control" id="login-language" onchange="location.replace(`/login/resetPassword?token=<?=$_GET['token']?>&lan=${this.value}`)">
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
    </script>
    <!-- Essential javascripts for application to work-->
    <script src="<?=media()?>/js/jquery-3.3.1.min.js"></script>
    <script src="<?=media()?>/js/popper.min.js"></script>
    <script src="<?=media()?>/js/bootstrap.min.js"></script>

    <!-- The javascript plugin to display page loading on top-->
    <script src="<?=media()?>/js/plugins/pace.min.js"></script>
    <script type="text/javascript" src="<?= media()?>/js/plugins/sweetalert.min.js"></script>
    <script src="<?=media()?>/js/<?=$data['page-functions_js']?>"></script>
  </body>
</html>