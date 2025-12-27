<? global $fnT; ?>
<!-- Modal -->
<div class="modal fade" id="modalFormPerfil" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header headerUpdate">
        <h5 class="modal-title" id="titleModal"><?=$fnT('Update data')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tile">
            <div class="tile-body">
              <form onsubmit="sendProfile(this); return false;">
                <p class="text-primary"><?=$fnT('All fields with asterisk')?> (<span class="required">*</span>) <?=$fnT('they are mandatory')?>.</p>
                
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label class="control-label" for="profile_name"><?=$fnT('Name')?> <span class="required">*</span></label>
                    <input class="form-control valid validText" id="profile_name" name="name" type="text" data-defvalue="<?=$_SESSION['userData']['name']?>" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label class="control-label" for="profile_email"><?=$fnT('Email')?> <span class="required">*</span></label>
                    <input class="form-control valid validEmail" rows="2" id="profile_email" name="email" type="email" data-defvalue="<?=$_SESSION['userData']['email']?>" required>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label class="control-label" for="profile_password"><?=$fnT('New password')?></label>
                    <input class="form-control toggle-pass" id="profile_password" name="password" type="password" pattern="<?=$data['regExPass']?>" title="<?=$fnT('Minimum 12 characters, uppercase and lowercase, plus at least one number')?>" data-defvalue="" autocomplete="new-password">
                  </div>
                  <div class="form-group col-md-6">
                    <label class="control-label" for="profile_cpassword"><?=$fnT('Retype new password')?></label>
                    <input class="form-control toggle-pass" id="profile_cpassword" name="cpassword" type="password" pattern="<?=$data['regExPass']?>" title="<?=$fnT('Minimum 12 characters, uppercase and lowercase, plus at least one number')?>" data-defvalue="" autocomplete="new-password">
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <input type="checkbox" class="" id="ck-show-input" onchange="showPassword()">
                    <label class="form-check-label" for="ck-show-input"><?=$fnT('Show Password')?></label>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="profile_language"><?=$fnT('Language')?></label>
                    <select class="form-control" id="profile_language" data-defvalue="<?=$_SESSION['userData']['default_language']?>" name="language" required>
                      <option value="eng">English</option>
                      <option value="esp">Spanish</option>
                      <option value="ind">Indonesian</option>
                    </select>
                  </div>  
                  <div class="form-group col-md-6">
                    <label for="input-pic"><?=$fnT('Profile picture')?></label>
                    <input type="text" class="d-none" id="visit-pic" data-defvalue="<?=$_SESSION['userData']['profile_picture']?>" name="profile_picture">
                    <input type="file" class="form-control-file" id="visit-pic" data-defvalue="" onchange="uploadPic(this)">
                  </div>
                </div>

                <div class="tile-footer">
                  <button class="btn btn-info mr-2" type="submit">
                    <i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText"><?=$fnT('Save')?></span>
                  </button>
                  <button class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-fw fa-lg fa-times-circle"></i><?=$fnT('Cancel')?>
                  </button>
                </div>

              </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>