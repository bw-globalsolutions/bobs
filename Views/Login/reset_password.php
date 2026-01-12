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
    <link rel="shortcut icon" href="<?=media()?>/images/icono.png?<?=rand(1, 15)?>">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?=media()?>/css/colors.css">
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
            <h2 class="text-center m-4 h1"><?=$fnT('Redefinir senha')?></h2>
            <p class="text-center mb-5">
              <?=$fnT('Antes de continuar, não se esqueça de que você pode consultar nosso')?>
              <b> <?=$fnT('aviso de privacidade')?></b> <?=$fnT('clicando')?>:  
              <a target="_black" href="https://www.arguilea.com/assets/docs/<?=$fnT('privacyPT')?>.pdf"><?=$fnT('aqui')?></a>
            </p>
            <div class="form-group">
              <input class="bg-light border-0 px-3 w-100 toggle-pass" type="password" pattern="<?=$data['regExPass']?>" title="<?=$fnT('Mínimo de 12 caracteres, maiúsculas e minúsculas, e pelo menos um número')?>" name="password" placeholder="<?=$fnT('Nova senha')?>" maxlength="32" required>
            </div>
            <div class="form-group">
              <input class="bg-light border-0 px-3 w-100 toggle-pass" type="password" pattern="<?=$data['regExPass']?>" title="<?=$fnT('Mínimo de 12 caracteres, maiúsculas e minúsculas, e pelo menos um número')?>" name="password2" placeholder="<?=$fnT('Digite novamente a nova senha')?>" maxlength="32" required>
            </div>
            <div class="form-group form-check mb-5">
              <input type="checkbox" class="form-check-input" id="ck-show-input" onchange="showPassword()">
              <label class="form-check-label" for="ck-show-input"><?=$fnT('Mostrar senha')?></label>
            </div>
            <input type="hidden" name="token" value="<?=$data['token']?>">
            <button id="btn-submit-login" type="submit" class="btn btn-primary btn-lg btn-block">
              <?=$fnT('Salvar')?>
              &nbsp;&nbsp;<img class="d-none" id='lodaer' src="<?=media()?>/images/loading-white.svg" alt="Loading">
            </button>
          </form>
        </div>
        <img id="argui-log" width="110" class="position-absolute" src="<?=media()?>/images/logo_min_arguilea.png" alt="Arguilea">
        <button type="button" class="btn btn-sm btn-outline-primary position-absolute" id="btn-language" data-toggle="modal" data-target="#modal-laguage">
          <i class="fa fa-language" aria-hidden="true"></i>
          <?=$fnT('Idioma')?>
        </button>
      </div>
    </div>

    <div class="modal fade" id="modal-laguage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><?=$fnT('Menu de idioma')?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="login-language"><?=$fnT('Selecione o idioma')?></label>
              <select class="form-control" id="login-language" onchange="location.replace(`/login/resetPassword?token=<?=$_GET['token']?>&lan=${this.value}`)">
                <option disabled selected><?=$fnT('Selecione um idioma')?></option>
                <option value="esp">Espanhol</option>
                <option value="eng">Inglês</option>
                <option value="por">Português</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$fnT('Fechar')?></button>
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
