<? global $fnT ?>
<!-- Modal -->
<div class="modal fade" id="modalFormRol" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal"><?=$fnT('Nova função')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tile">
            <div class="tile-body">
              <form id="formRol" name="formRol">
                <input type="hidden" id="role_id" name="id" value="">
                <div class="form-group">
                  <label class="control-label" for="role_name"><?=$fnT('Nome')?></label>
                  <input class="input-s1 form-control" id="role_name" name="name" type="text" required>
                </div>
                <div class="form-group">
                  <label class="control-label" for="role_description"><?=$fnT('Descrição')?></label>
                  <textarea class="input-s1 form-control" rows="2" id="role_description" name="description" required></textarea>
                </div>
                  <div class="form-group">
                    <label for="role_status"><?=$fnT('Status')?></label>
                    <select class="input-s1 form-control" id="role_status" name="status" required>
                      <option value="1"><?=$fnT('Ativo')?></option>
                      <option value="0"><?=$fnT('Inativo')?></option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="role_level"><?=$fnT('Nível')?></label>
                    <select class="input-s1 form-control" id="role_level" name="level" required>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                    </select>
                  </div>
                <div class="tile-footer">
                  <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText"><?=$fnT('Salvar')?></span></button>&nbsp;&nbsp;&nbsp;<a class="btn btn-secondary" href="#" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i><?=$fnT('Cancelar')?></a>
                </div>
              </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalPermisos" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title h4"><?=$fnT('Permissões das funções de usuário')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <div class="tile">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th><?=$fnT('Módulo')?></th>
                    <th><?=$fnT('Ler')?></th>
                    <th><?=$fnT('Escrever')?></th>
                    <th><?=$fnT('Atualizar')?></th>
                    <th><?=$fnT('Excluir')?></th>
                  </tr>
                </thead>
                <tbody id="permission-table">
                  <? foreach($data['modules'] as $m): ?>
                    <tr>
                      <td>
                        <?=$m['id']?>
                      </td>
                      <td>
                        <?=$m['name']?>
                      </td>
                      <td>
                        <div class="toggle-flip">
                          <label><input type="checkbox" id='m<?=$m['id']?>-r' data-perms="r" data-module="<?=$m['id']?>"><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label>
                        </div>
                      </td>
                      <td>
                        <div class="toggle-flip">
                          <label><input type="checkbox" id='m<?=$m['id']?>-w' data-perms="w" data-module="<?=$m['id']?>"><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label>
                        </div>
                      </td>
                      <td>
                        <div class="toggle-flip">
                          <label><input type="checkbox" id='m<?=$m['id']?>-u' data-perms="u" data-module="<?=$m['id']?>"><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label>
                        </div>
                      </td>
                      <td>
                        <div class="toggle-flip">
                          <label><input type="checkbox" id='m<?=$m['id']?>-d' data-perms="d" data-module="<?=$m['id']?>"><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label>
                        </div>
                      </td>
                    </tr>
                  <? endforeach ?>
                </tbody>
              </table>
            </div>
            <div class="text-center">
              <button class="btn btn-success" onclick="submitPermission()"><i class="fa fa-fw fa-lg fa-check-circle" aria-hidden="true"></i> <?=$fnT('Salvar')?></button>
              <button class="btn btn-danger" data-dismiss="modal"><i class="app-menu__icon fa fa-sign-out" aria-hidden="true"></i> <?=$fnT('Sair')?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalNotifications" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title h4"><?=$fnT('Notificações por função')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <div class="tile">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th><?=$fnT('Notificação')?></th>
                    <th><?=$fnT('Enviar')?></th>
                  </tr>
                </thead>
                <tbody id="notification-table">
                  <? foreach($data['notifications'] as $m): ?>
                    <tr>
                      <td>
                        <?=$m['id']?>
                      </td>
                      <td>
                        <?=$m['name']?>
                      </td>
                      <td>
                        <div class="toggle-flip">
                          <label><input type="checkbox" id='s<?=$m['id']?>' data-perms="send" data-module="<?=$m['id']?>"><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label>
                        </div>
                      </td>
                      
                    </tr>
                  <? endforeach ?>
                </tbody>
              </table>
            </div>
            <div class="text-center">
              <button class="btn btn-success" onclick="submitNotifications()"><i class="fa fa-fw fa-lg fa-check-circle" aria-hidden="true"></i> <?=$fnT('Salvar')?></button>
              <button class="btn btn-danger" data-dismiss="modal"><i class="app-menu__icon fa fa-sign-out" aria-hidden="true"></i> <?=$fnT('Sair')?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>