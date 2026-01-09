<?php 
  headerTemplate($data);
  getModal('modalRoles',$data);
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
          <h1>
            <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            <? if($data['permission']['w']): ?>
              &nbsp;
              <button class="btn btn-sm btn-primary mb-1" type="button" onclick="openModal();"><i class="fa fa-plus-circle" aria-hidden="true"></i> <?=$fnT('New')?></button>
            <? endif ?>
          </h1>
          <p><?=$fnT('Register, update and delete user roles')?></p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$data['page_title']?></a></li>
        </ul>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <div class="table-responsive">
                <table class="table table-hover table-bordered" id="tableRoles">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th><?=$fnT('Name')?></th>
                      <th><?=$fnT('Description')?></th>
                      <th><?=$fnT('Level')?></th>
                      <th><?=$fnT('Status')?></th>
                      <th><?=$fnT('Action')?></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
<?php footerTemplate($data);?>