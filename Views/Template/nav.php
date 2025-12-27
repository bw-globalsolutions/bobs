<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <div style="margin-top: -50px; padding: 15px"><img class="img-fluid" src="<?=media()?>/images/logo.png" alt="Logo"></div>
  <br /><br />
  <div class="app-sidebar__user"><img height="50" width="50" class="app-sidebar__user-avatar of-cover shadow" src="<?=$_SESSION['userData']['profile_picture']?? media() . '/images/user.png'?>" alt="User Image">
    <div>
      <p class="app-sidebar__user-name"><?=$_SESSION['userData']['name']?></p>
      <p class="app-sidebar__user-designation"><?=$_SESSION['userData']['profile']?></p>
    </div>
  </div>
  
  <ul class="app-menu">
      <li>
        <a class="app-menu__item" href="<?=base_url()?>/home">
          <i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label"><?=$fnT('Dashboard')?></span>
        </a>
      </li>

      <? if(!empty($_SESSION['userData']['permission']['Auditorias']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/audits">
            <i class="app-menu__icon fa fa-list-alt"></i><span class="app-menu__label"><?=$fnT('Audits')?></span>
          </a>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Estadisticas']['r'])): ?>
        <li class="treeview">
          <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class="app-menu__icon fa fa-pie-chart"></i><span class="app-menu__label lang"><?=$fnT('Statistics')?></span><i class="treeview-indicator fa fa-angle-right"></i>
          </a>
          <ul class="treeview-menu">
            <li>
              <a class="treeview-item" href="<?=base_url()?>/statistics"><i class="icon fa fa-circle-o">
                </i> <span><?=$fnT('Main')?></span>
              </a>
            </li>
          
              <li>
                <a class="treeview-item" href="<?=base_url()?>/statistics/programPreview"><i class="icon fa fa-circle-o">
                  </i> <span><?=$fnT('Program preview')?></span>
                </a>
              </li>

              <li>
                <a class="treeview-item" href="<?=base_url()?>/statistics/actionPlan"><i class="icon fa fa-circle-o">
                  </i> <span><?=$fnT('Action plan')?></span>
                </a>
              </li>

              <li>
                <a class="treeview-item" href="<?=base_url()?>/statistics/revisitsProgress"><i class="icon fa fa-circle-o">
                  </i> <span><?=$fnT('Revisits Progress')?></span>
                </a>
              </li>

              <li>
                <a class="treeview-item" href="<?=base_url()?>/statistics/districtReport"><i class="icon fa fa-circle-o">
                  </i> <span><?=$fnT('District Report')?></span>
                </a>
              </li>
            <? endif; ?>
            <li>
              <a class="treeview-item" href="<?=base_url()?>/statistics/gallery"><i class="icon fa fa-circle-o">
                </i> <span><?=$fnT('Gallery')?></span>
              </a>
            </li>
          </ul>
        </li>
     
      
      <? if(!empty($_SESSION['userData']['permission']['Aclaraciones']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/appeals">
            <i class="app-menu__icon fa fa-bolt"></i><span class="app-menu__label"><?=$fnT('Appeals')?></span>
          </a>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Usuarios']['r'])): ?>
        <li class="treeview">
          <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label lang"><?=$fnT('Management')?></span><i class="treeview-indicator fa fa-angle-right"></i>
          </a>
          <ul class="treeview-menu">
            <li>
              <a class="treeview-item" href="<?=base_url()?>/usuarios">
                <i class="icon fa fa-circle-o"></i> <span><?=$fnT('Users')?></span>
              </a>
            </li>
            <li>
              <? if(in_array($_SESSION['userData']['role']['id'], [1,2])): ?>
                <a class="treeview-item" href="<?=base_url()?>/roles"><i class="icon fa fa-circle-o">
                  </i> <span><?=$fnT('Roles')?></span>
                </a>
              <? endif ?>

            </li>
            <li>
            <? if(in_array($_SESSION['userData']['role']['id'], [1,2,3,17])): ?>
                       
                     


                <a class="treeview-item" href="<?=base_url()?>/usuariosTienda"><i class="icon fa fa-circle-o">
                  </i> <span><?=$fnT('Feed report')?></span>
                </a>

                      
              <? endif ?>
            </li>
          </ul>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Traducciones']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/systemLanguages">
            <i class="app-menu__icon fa fa-language"></i><span class="app-menu__label"><?=$fnT('Languages')?></span>
          </a>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Tiendas']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/location">
            <i class="app-menu__icon fa fa-map-marker"></i>
            <span class="app-menu__label"><?=$fnT('Locations')?></span>
          </a>
        </li>
      <? endif ?>
      
      <? if(!empty($_SESSION['userData']['permission']['Visitas Anunciadas']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/announced_Visits">
            <i class="app-menu__icon fa fa-calendar"></i><span class="app-menu__label"><?=$fnT('Announced visits')?></span>
          </a>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Encuesta de auditor']['r']) && false): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/auditorSurvey">
            <i class="app-menu__icon fa fa-pencil-square-o"></i><span class="app-menu__label"><?=$fnT('Auditor survey')?></span>
          </a>
        </li>
      <? endif ?>
  </ul>
</aside>