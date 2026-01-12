<?php 
  headerTemplate($data);
  getModal('modalUsuarios',$data);
  global $fnT;
?>

    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-users" aria-hidden="true"></i> <?=$fnT($data['page_title'])?> 
            <? if($data['permission']['w']): ?>
              &nbsp;<button class="btn btn-sm btn-primary" type="button" onclick="openModal();"><i class="fa fa-plus-circle" aria-hidden="true"></i> <?=$fnT('Novo')?></button>
            <? endif ?>
          </h1>
          <p><?=$fnT('Cadastrar, atualizar e excluir usuários')?></p>
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
                <table class="table table-hover table-bordered" id="tableUsuarios">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th><?=$fnT('Nome')?></th>
                      <th>Email</th>
                      <th><?=$fnT('Marca')?></th>
                      <th><?=$fnT('País')?></th>
                      <th><?=$fnT('Status')?></th>
                      <th><?=$fnT('Função')?></th>
                      <th><?=$fnT('Ação')?></th>
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