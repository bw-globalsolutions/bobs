<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <button class="btn-s3" style="position:absolute; top:10px; left:10px;" onclick="window.location.href='<?=base_url()?>/logout'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h6q.425 0 .713.288T12 4t-.288.713T11 5H5v14h6q.425 0 .713.288T12 20t-.288.713T11 21zm12.175-8H10q-.425 0-.712-.288T9 12t.288-.712T10 11h7.175L15.3 9.125q-.275-.275-.275-.675t.275-.7t.7-.313t.725.288L20.3 11.3q.3.3.3.7t-.3.7l-3.575 3.575q-.3.3-.712.288t-.713-.313q-.275-.3-.262-.712t.287-.688z"/></svg></button>
  <div style="margin-top: -50px; padding: 15px;"><img class="img-fluid" style="mask-image: linear-gradient(black 60%, transparent); position: relative;top: 30px;padding: 10px;border-radius: 20px;" src="<?=media()?>/images/logo.png?<?=rand(1, 15)?>" alt="Logo"></div>
  <br /><br />
  <div class="app-sidebar__user contPerfil" onclick="window.location.href='<?=base_url()?>/usuarios/perfil'"><img height="50" width="50" class="app-sidebar__user-avatar of-cover perfil" src="<?=$_SESSION['userData']['profile_picture']?? media() . '/images/user.png'?>" alt="User Image">
    <div>
      <p class="app-sidebar__user-name"><?=$_SESSION['userData']['name']?></p>
      <p class="app-sidebar__user-designation"><?=$_SESSION['userData']['profile']?></p>
    </div>
  </div>
  <script>
    //alert('<?=isAmerican()?>');
    /*if('<?=isAmerican()?>'=='true' || '<?=isAmerican()?>'=='1'){
      console.log('America');
    }else{
      document.querySelector('.img-fluid').src='https://churchstexas-stage.bw-globalsolutions.com/Assets/images/otherCountrys.png';
    }*/

    function copiarAlPortapapeles(txt){
      if (!window.isSecureContext) {
        console.error('Necesita HTTPS o localhost');
        return false;
      }
      
      // Verificar si el API está disponible
      if (!navigator.clipboard) {
        console.error('Clipboard API no soportada');
        return false;
      }
      navigator.clipboard.writeText(txt)
      .then(()=>{
        smallMsg('Texto copiado al portapapeles');
      })
      .catch(err=>{
        alert('Error al copiar al portapapeles:', err.name, err.message)
      })
    }
    function smallMsg(txt){
      let cont = document.createElement('DIV');
      cont.style.width='100%';
      cont.style.display='flex';
      cont.style.justifyContent='center';
      let et = document.createElement('P');
      et.setAttribute('class', 'msg-mini');
      et.innerHTML=txt;
      cont.appendChild(et);
      document.body.appendChild(cont);
      setTimeout(function(){
        et.style.opacity="1";
        setTimeout(function(){
          et.style.opacity="0";
          setTimeout(function(){
            et.remove();
          }, 500)
        }, 1000)
      }, 100)
    }
  </script>
  <ul class="app-menu">
      <div class="sec1Nav">
      <? if(!empty($_SESSION['userData']['permission']['Archivos']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/files">
          <i class="app-menu__icon fa fa-file"></i><span class="app-menu__label"><?=$fnT('Arquivos')?></span>
          </a>
        </li>
      <? endif ?>
      <li>
        <a class="app-menu__item" href="<?=base_url()?>/home">
          <i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label"><?=$fnT('Dashboard')?></span>
        </a>
      </li>

      <? if(!empty($_SESSION['userData']['permission']['Auditorias']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/audits">
            <i class="app-menu__icon fa fa-list-alt"></i><span class="app-menu__label"><?=$fnT('Auditorias')?></span>
          </a>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Estadisticas']['r'])): ?>
        <li class="treeview">
          <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class="app-menu__icon fa fa-pie-chart"></i><span class="app-menu__label lang"><?=$fnT('Estatísticas')?></span><i class="treeview-indicator fa fa-angle-right"></i>
          </a>
          <ul class="treeview-menu">
            <li>
              <a class="treeview-item" href="<?=base_url()?>/statistics"><i class="icon fa fa-circle-o">
                </i> <span><?=$fnT('Principal')?></span>
              </a>
            </li>
            <? if(!empty($_SESSION['userData']['permission']['Estadisticas']['u'])): ?>
              <li>
                <a class="treeview-item" href="<?=base_url()?>/statistics/programPreview"><i class="icon fa fa-circle-o">
                  </i> <span><?=$fnT('Prévia do programa')?></span>
                </a>
              </li>
            <? endif; ?>
            <li>
              <a class="treeview-item" href="<?=base_url()?>/statistics/gallery"><i class="icon fa fa-circle-o">
                </i> <span><?=$fnT('Galeria')?></span>
              </a>
            </li>
          </ul>
        </li>
      <? endif ?>
      
      <? if(!empty($_SESSION['userData']['permission']['Aclaraciones']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/appeals">
            <i class="app-menu__icon fa fa-bolt"></i><span class="app-menu__label"><?=$fnT('Apelações')?></span>
          </a>
        </li>
      <? endif ?>
      </div>
      <div class="sec2Nav">
      <? if(!empty($_SESSION['userData']['permission']['Usuarios']['r'])): ?>
        <li class="treeview">
          <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label lang"><?=$fnT('Administração')?></span><i class="treeview-indicator fa fa-angle-right"></i>
          </a>
          <ul class="treeview-menu">
            <li>
              <a class="treeview-item" href="<?=base_url()?>/usuarios">
                <i class="icon fa fa-circle-o"></i> <span><?=$fnT('Usuários')?></span>
              </a>
            </li>
            <li>
              <? if($_SESSION['userData']['role']['id'] == 1 || $_SESSION['userData']['role']['id'] == 2): ?>
                <a class="treeview-item" href="<?=base_url()?>/roles"><i class="icon fa fa-circle-o">
                  </i> <span><?=$fnT('Funções')?></span>
                </a>
              <? endif ?>

            </li>
            <li>
            <? if(in_array($_SESSION['userData']['role']['id'], [1,2,17])): ?>
                       
                     


                <a class="treeview-item" href="<?=base_url()?>/usuariosTienda"><i class="icon fa fa-circle-o">
                  </i> <span><?=$fnT('Relatório Feed')?></span>
                </a>

                      
              <? endif ?>
            </li>
          </ul>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Traducciones']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/systemLanguages">
            <i class="app-menu__icon fa fa-language"></i><span class="app-menu__label"><?=$fnT('Idiomas')?></span>
          </a>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Tiendas']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/location">
            <i class="app-menu__icon fa fa-map-marker"></i>
            <span class="app-menu__label"><?=$fnT('Localizações')?></span>
          </a>
        </li>
      <? endif ?>
      
      <? if(!empty($_SESSION['userData']['permission']['Visitas Anunciadas']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/announced_Visits">
            <i class="app-menu__icon fa fa-calendar"></i><span class="app-menu__label"><?=$fnT('Visitas anunciadas')?></span>
          </a>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Encuesta de auditor']['r']) && false): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/auditorSurvey">
            <i class="app-menu__icon fa fa-pencil-square-o"></i><span class="app-menu__label"><?=$fnT('Pesquisa do auditor')?></span>
          </a>
        </li>
      <? endif ?>

      <? if(!empty($_SESSION['userData']['permission']['Personalizacion']['r'])): ?>
        <li>
          <a class="app-menu__item" href="<?=base_url()?>/personalization">
            <i class="app-menu__icon fa fa-sliders"></i><span class="app-menu__label"><?=$fnT('Personalização')?></span>
          </a>
        </li>
      <? endif ?>
      </div>
      
  </ul>
</aside>