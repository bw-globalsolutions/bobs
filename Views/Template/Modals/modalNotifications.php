<div class="modal fade modalNotifications" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title h4">Notifications by Rol</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body">
        <?php
            //dep($data);
        ?>
        <div class="col-md-12">
          <div class="tile">
            <form action="" id="formNotifications" name="formNotifications">
              <input type="hidden" id="id_rol" name="id_rol" value="<?=$data['idrol']?>" required="">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Notification</th>
                      <th>Send</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $no=1;
                      $notifications = $data['notifications'];
                      for($i=0; $i < count($notifications); $i++){
                        $permisos = $notifications[$i]['permisos'];
                        $rCheck = $permisos['send'] == 1 ? " checked " : "";

                        $idmod = $notifications[$i]['id'];
                    ?>
                    <tr>
                      <td>
                        <?=$no;?>
                        <input type="hidden" name="notifications[<?=$i?>][id_modulo]" value="<?=$idmod?>" required>
                      </td>
                      <td>
                        <?=$notifications[$i]['name']?>
                      </td>
                      <td>
                        <div class="toggle-flip">
                          <label><input type="checkbox" name="notifications[<?=$i?>][send]" <?=$rCheck?>><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label>
                        </div>
                      </td>
                    </tr>
                  <?php 
                      $no++;
                  }?>
                  </tbody>
                </table>
              </div>

              <div class="text-center">
                <button class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle" aria-hidden="true"></i> Save</button>
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="app-menu__icon fa fa-sign-out" aria-hidden="true"></i> Exit</button>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>