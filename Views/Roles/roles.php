<?php 
  headerTemplate($data);
  getModal('modalRoles',$data);
  global $fnT;
?>
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1>
            <i class="fa fa-id-card-o" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            <? if($data['permission']['w']): ?>
              &nbsp;
               <? if(in_array($_SESSION['userData']['role']['id'], [1])): ?>
              <button class="btn btn-sm btn-primary mb-1" type="button" onclick="openModal();"><i class="fa fa-plus-circle" aria-hidden="true"></i> <?=$fnT('New')?></button>
            <? endif ?>
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
                 <? if(in_array($_SESSION['userData']['role']['id'], [1])): ?>
                       
                     
                 

                      
        
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
      <? endif ?>
       <? if(in_array($_SESSION['userData']['role']['id'], [2])): ?>
                <table class="table table-hover table-bordered" id="tableRolesVista">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th><?=$fnT('Name')?></th>
                      
                      <th><?=$fnT('Level')?></th>
                      <th><?=$fnT('Status')?></th>
                     
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
                      <? endif ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
<?php footerTemplate($data);?>