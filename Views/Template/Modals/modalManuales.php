<!-- Modal -->
<?php global $fnT; ?>
<div class="modal fade" id="modalFormManual" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header headerRegister bg-dark text-white">
        <h5 class="modal-title" id="titleModal"><?=$fnT('New')?> manual</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tile">
            <div class="tile-body">
              <form id="formManual" name="formManual" class="form-horizontal">
                <input type="hidden" id="id_usuario" name="id_usuario" value="">
                
                <div class="form-row">
                  <div class="form-group col-md-12">
                    <label class="control-label"><?=$fnT('Name')?> </label>
                    <input class="form-control valid validText" id="txtNombre" name="txtNombre" type="text" placeholder="<?=$fnT('Name')?>">
                  </div>
                  <div class="form-group col-md-12">
                    <label class="control-label"><?=$fnT('Description')?></label>
                    <input class="form-control "  id="txtDescripcion" name="txtDescripcion" type="text" placeholder="<?=$fnT('Description')?>">
                  </div>
                  <div class="form-group col-md-12">
                    <label class="control-label"><?=$fnT('Category')?></label>
                    <select class="form-control "  id="txtCategoria" name="txtCategoria" type="text"  onchange="verificarSeleccion()">
                      <option selected disabled><?=$fnT('Select an option')?></option>
                      <option value="1"><?=$fnT('Add category')?></option>
                    </select>
                    <br>
                    <input class="form-control "  id="nuevaCategoria" name="nuevaCategoria" type="text"  >
                  </div>
                  <div class="form-group col-md-12">
                    <label class="control-label"><?=$fnT('Language')?></label>
                    <select class="form-control "  id="txtLang" name="txtLang" type="text">
                      <option value="eng"><?=$fnT('English')?></option>
                      <option value="esp"><?=$fnT('Spanish')?></option>
                      <option value="ind"><?=$fnT('Indonesian')?></option>
                    </select>
                  </div>
                  <div class="form-group col-md-12">
                  <br>
                    <label class="control-label"><?=$fnT('Archive')?></label>
                    <input class="form-control"  id="txtArchivo" name="txtArchivo" type="text" placeholder="" hidden>
                    &nbsp;&nbsp;
                    <i class="fa fa-camera fa-primary fa-2x text-info cursor-pointer" onclick="$(this).next().click();"></i>
                    <input type="file" onchange="uploadPic(this, '1')"  hidden/>
                    <textarea  name="evidencias_1" id="evidencias_1" hidden></textarea>
                  </div>
                </div>
                <div class="tile-footer">
                  <button id="btnActionForm" class="btn bg-dark text-white" type="submit">
                    <i class="fa fa-fw fa-lg fa-check-circle"></i>
                    <span id="btnText"><?=$fnT('Save')?></span>
                  </button>
                  <button class="btn btn-danger" data-dismiss="modal">
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