<?php
  headerTemplate($data);
  getModal('modalPerfil',$data);
  global $fnT;
?>

    <div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
  <main class="app-content">
    <div class="app-title">
      <div>
        <h1><i class="fa fa-user fa-lg"></i> <?=$data['page_title']?></h1>
      </div>
      <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-user fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
      </ul>
    </div>
      <div class="row user">
        <div class="col-md-12">
          <div class="profile" style="display:flex; justify-content:center;">
            <div class="info"><img class="app-sidebar__user-avatar of-cover perfil" style="max-width:300px;" src="<?=$_SESSION['userData']['profile_picture']?? media() . '/images/user.png'?>" height="200" width="200" alt="User Image">
              <h4><?=$_SESSION['userData']['name']?></h4>
              <p class="text-uppercase"><?=$_SESSION['userData']['role']['name']?></p>
            </div>
          </div>
        </div>
        <!--<div class="col-md-3">
          <div class="tile p-0">
            <ul class="nav flex-column nav-tabs user-tabs">
              <li class="nav-item">
                <a class="nav-link active" href="#user-data" data-toggle="tab"><?=$fnT('Personal information')?></a>
              </li>
            </ul>
          </div>
        </div>-->
        <div class="" style="width:100%;">
          <div class="tab-content" style="margin:0; position:relative; bottom:42px;">
            <div class="tab-pane active" id="user-data">
              <div class="timeline-post">
                  <table class="table table-bordered">
                    <tbody>
                      <tr>
                        <td style="width:150px;"><?=$fnT('Name')?>:</td>
                        <td><?=$_SESSION['userData']['name']?></td>
                      </tr>
                      <tr>
                        <td style="width:150px;"><?=$fnT('Email')?>:</td>
                        <td><?=$_SESSION['userData']['email']?></td>
                      </tr>
                      <tr>
                        <td style="width:150px;"><?=$fnT('Brand')?>:</td>
                        <td><?=implode(', ', $_SESSION['userData']['brand'])?></td>
                      </tr>
                      <tr>
                        <td style="width:150px;"><?=$fnT('Country')?>:</td>
                        <td><?=$data['countries']?></td>
                      </tr>
                      <tr>
                        <td style="width:150px;"><?=$fnT('Language')?>:</td>
                        <td class="text-uppercase"><?=$_SESSION['userData']['default_language']?></td>
                      </tr>
                      <tr>
                        <td style="width:150px;"><?=$fnT('Role')?>:</td>
                        <td><?=$_SESSION['userData']['role']['name']?></td>
                      </tr>
                    </tbody>
                  </table>
                  <button class="btn btn-sm btn-info" type="button" onclick="openModalPerfil()"><i class="fa fa-pencil" aria-hidden="true"></i> <?=$fnT('Edit')?></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
<?php footerTemplate($data);?>