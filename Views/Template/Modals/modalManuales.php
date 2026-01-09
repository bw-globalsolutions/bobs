<!-- Modal -->
<div class="modal fade" id="modalFormManual" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nuevo Manual</h5>
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
                  <div class="form-group col-md-6">
                    <label class="control-label">Nombre</label>
                    <input class="form-control valid validText" id="txtNombre" name="txtNombre" type="text" placeholder="Nombre manual">
                  </div>
                  <div class="form-group col-md-6">
                    <label class="control-label">Descripcion</label>
                    <input class="form-control "  id="txtDescripcion" name="txtDescripcion" type="text" placeholder="Descripcion">
                  </div>
                  <div class="form-group col-md-6">
                    <label class="control-label">Categoria</label>
                    <select class="form-control "  id="txtCategoria" name="txtCategoria" type="text" placeholder="Categoria"  onchange="verificarSeleccion()">
                      <? foreach($data['periods'] as $round): ?>
                                    <option value="<?=$round['name']?>"><?=$round['name']?></option>
                      <? endforeach ?>  
                      <!--<option selected disabled>Selecciona una opcion</option>
                      <option value="1">Añadir categoria</option>-->
                      
                    </select>
                    <br>
                    <input class="form-control "  id="nuevaCategoria" name="nuevaCategoria" type="text" placeholder="Nueva categoria" >
                  </div>
                  <div class="form-group col-md-6">
                  <br>
                    <label class="control-label">Archivo</label>
                    <input class="form-control"  id="txtArchivo" name="txtArchivo" type="text" placeholder="" hidden>
                    &nbsp;&nbsp;
                    <i class="fa fa-camera fa-primary fa-2x text-info cursor-pointer" onclick="$(this).next().click();"></i>
                    <input type="file" onchange="uploadPic(this, '1')"  hidden/>
                    <textarea  name="evidencias_1" id="evidencias_1" hidden></textarea>
                  </div>
                </div>
                <div class="tile-footer">
                  <button id="btnActionForm" class="btn btn-primary" type="submit">
                    <i class="fa fa-fw fa-lg fa-check-circle"></i>
                    <span id="btnText">Guardar</span>
                  </button>&nbsp;&nbsp;&nbsp;
                  <button class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-fw fa-lg fa-times-circle"></i>Cancelar
                  </button>
                </div>
              </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>