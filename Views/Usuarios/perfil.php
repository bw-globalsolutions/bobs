<?php
  headerTemplate($data);
  getModal('modalPerfil',$data);
  global $fnT;
?>

    <main class="app-content">
      <div class="row user">
        <div class="col-md-12">
          <div class="profile">
            <div class="info"><img class="app-sidebar__user-avatar of-cover shadow" src="<?=$_SESSION['userData']['profile_picture']?? media() . '/images/user.png'?>" height="100" width="100" alt="User Image">
              <h4><?=$_SESSION['userData']['name']?></h4>
              <p class="text-uppercase"><?=$_SESSION['userData']['role']['name']?></p>
            </div>
            <div class="cover-image"></div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="tile p-0">
            <ul class="nav flex-column nav-tabs user-tabs">
              <li class="nav-item">
                <a class="nav-link active" href="#user-data" data-toggle="tab"><?=$fnT('Personal information')?></a>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-md-9">
          <div class="tab-content">
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