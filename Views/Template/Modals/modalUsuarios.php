<? global $fnT; ?>
<!-- Modal -->
<div class="modal fade" id="modalFormUser" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal"><?=$fnT('New user')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tile">
            <div class="tile-body">
              <form id="formUser" class="form-horizontal">
                <input type="hidden" id="user_id" name="id" value="">
                <p class="text-primary"><?=$fnT('All fields are required')?>.</p>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="name" class="control-label"><?=$fnT('Name')?></label>
                    <input class="input-s1 form-control valid validText" id="user_name" name="name" type="text" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="user_email" class="control-label">Email</label>
                    <input class="input-s1 form-control valid validEmail" id="user_email" name="email" type="email" required>
                  </div>
                </div>
                
                <div class="form-row">
                  <div class="form-group col-md-6">
                      <label for="user_brand"><?=$fnT('Brand')?></label>
                      <select class="form-control selectpicker" style="color:#000" multiple id="user_brand" name="list_brand[]" required>
                        <? foreach($data['brands'] AS $brand): ?>
                          <option value="<?=$brand['id']?>"><?=$brand['name']?></option>
                        <? endforeach ?>
                      </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="user_role"><?=$fnT('Role')?></label>
                    <select class="input-s1 form-control"  id="user_role" name="role" onchange="limitRole($(`#user_role [value='${this.value}']`).data('level'))" required>
                      <?php foreach($data['role'] AS $role){?>
                        <option value="<?=$role['id']?>" data-level=<?=$role['level']?>><?=$role['name']?></option>
                      <?php }?>
                    </select>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="user_country"><?=$fnT('Country')?></label>
                    <select class="form-control selectpicker" style="color:#000" multiple id="user_country" name="list_country[]" required onchange="limitCountry()" requied>
                      <? foreach($data['paises'] AS $region => $pais):?>
                        <optgroup label="<?=$region?>">
                          <? foreach($pais as $p): ?>
                            <option value="<?=$p['id']?>"><?=$p['name']?></option>
                          <? endforeach ?>
                        </optgroup>
                      <? endforeach ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="user_language"><?=$fnT('Language')?></label>
                    <select class="form-control selectpicker" id="user_language" name="language" required>
                      <option value="eng">English</option>
                      <option value="esp">Spanish</option>
                    </select>
                  </div>

                </div>
                
                <div class="form-row">
                  <div class="form-group col-md-6" style="visibility:hidden;">
                    <label for="user_status"><?=$fnT('Status')?></label>
                    <select class="form-control selectpicker" id="user_status" name="status" required>
                      <option value="1">Active</option>
                      <option value="0">Inactive</option>
                    </select>
                  </div>     
                  <div class="form-group col-md-6" id="panel_user_location">
                    <div class="d-flex justify-content-between">
                      <label for="user_location"><?=$fnT('Location')?></label>
                      <i class="fa fa-check-square-o cr-pointer text-primary" style="color:#000" aria-hidden="true" onclick="selectAllLocations()" id="btn-selected-all"></i>
                    </div>
                    <select class="form-control selectpicker" data-live-search="true" multiple id="user_location" name="list_location[]" data-selected-text-format="count>1" required>
                      <?php foreach($data['locations'] AS $location){?>
                        <option value="<?=$location['id']?>" data-country="<?=$location['country_id']?>"><?=$location['number']?> - <?=$location['name']?></option>
                      <?php }?>
                    </select>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="user_notify"><?=$fnT('Send notifications')?></label>
                    <select class="form-control selectpicker" id="user_notify" name="notification" required>
                      <option value="1">Active</option>
                      <option value="0">Inactive</option>
                    </select>
                  </div>
                </div>

                <div class="tile-footer">
                  <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText"><?=$fnT('Save')?></span></button>&nbsp;&nbsp;&nbsp;<button class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i><?=$fnT('Cancel')?></button>
                </div>
              </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalViewUser" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal"><?=$fnT('User data')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td>ID</td>
              <td id="cel-id"></td>
            </tr>
            <tr>
              <td><?=$fnT('Name')?></td>
              <td id="cel-name"></td>
            </tr>
            <tr>
              <td>Email</td>
              <td id="cel-email"></td>
            </tr>
            <tr>
              <td><?=$fnT('Brand')?></td>
              <td id="cel-brand"></td>
            </tr>
            <tr>
              <td><?=$fnT('Country')?></td>
              <td id="cel-country"></td>
            </tr>
            <tr>
              <td><?=$fnT('Language')?></td>
              <td class="text-uppercase" id="cel-language"></td>
            </tr>
            <tr>
              <td><?=$fnT('Role')?></td>
              <td class="text-capitalize" id="cel-role"></td>
            </tr>
            <tr>
              <td><?=$fnT('Location')?></td>
              <td class="text-capitalize" id="cel-location"></td>
            </tr>
            <tr>
              <td><?=$fnT('Status')?></td>
              <td id="cel-status"></td>
            </tr>
            <tr>
              <td><?=$fnT('Registration date')?></td>
              <td id="cel-regDate"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=$fnT('Close')?></button>
      </div>
    </div>
  </div>
</div>