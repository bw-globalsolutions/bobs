<?php 
    headerTemplate($data);
    global $fnT;
?>
<style>
    .contColores{
        display:flex;
        justify-content:center;
        align-items:center;
        gap:10px;
    }
    .color{
        border-radius:50%;
        border: 3px solid #fff;
        cursor:pointer;
        width: 40px;
        height:40px;
        box-shadow: 1px 1px 8px 0 #b4a9a9;
    }
    .hovEdit{
        width: 100%;
        height:100%;
        display:flex;
        position: absolute;
        justify-content:center;
        align-items:center;
        background-color:#00000059;
        cursor:pointer;
        opacity:0;
        transition:.5s;
        top:0;
    }
    .hovEdit:hover{
        opacity: 1;
    }
</style>
<div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
<main class="app-content" style="position:relative; display:flex; flex-direction:column;">
    <input type="hidden" id="tipoR" value="<?=$_SESSION['userData']['role']['name']?>">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-file" aria-hidden="true"></i> <?=$fnT($data['page_title'])?>
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>/<?=$data['page_name']?>"><?=$fnT($data['page_title'])?></a></li>
        </ul>
    </div>
    
    <div id="collapseFormFile"  aria-labelledby="headingOne" data-parent="#accordionFile">
                  <div class="card-body">
                    <form onsubmit="sendFormAddFile(this); return false;" id="form-files">
                      <div class="form-group row">
                        <label for="input-title" class="col-sm-2 col-form-label"><?=$fnT('Title')?></label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control input-s1" id="input-title" name="title" maxlength="128" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-description" class="col-sm-2 col-form-label"><?=$fnT('Description')?></label>
                        <div class="col-sm-10">
                          <textarea class="form-control input-s1" rows="3" id="input-description" name="description" maxlength="512" required></textarea>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-files" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                          <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" id="customFile" onchange="addFile(this)" accept=".xlsx,.pdf,.png,.jpg">
                            <label class="custom-file-label input-s1" for="customFile"><?=$fnT('Select files')?></label>
                          </div>
                          <div id="form-panel-files"></div>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-files" class="col-sm-2 col-form-label"><?=$fnT('Countrys')?></label>
                        <div style="display:flex; gap:10px; align-items: center;" class="col-sm-10">
                        <input hidden id="countrys" name="countrys">
                        <? foreach($data['paises'] as $pais): ?>
                          <input type="checkbox" class="country" hidden onchange="actualizarPais()" id="<?=$pais['name']?>" pId="<?=$pais['id']?>" value="<?=$pais['id']?>">
                          <label class="elementS" for="<?=$pais['name']?>" ><img src="Assets/images/paises/<?=$pais['name']?>.png" class="pais" alt="<?=$pais['name']?>"></label>
                        <? endforeach ?>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-files" class="col-sm-2 col-form-label"><?=$fnT('Roles')?></label>
                        <div style="display:flex; gap:20px; align-items: center; flex-wrap:wrap;" class="col-sm-10">
                        <input hidden id="roles" name="roles">
                        <? foreach($data['roles'] as $rol): ?>
                          <input type="checkbox" class="rol" hidden onchange="actualizarRoles()" id="<?=$rol['name']?>" rId="<?=$rol['id']?>" value="<?=$rol['id']?>">
                          <label class="elementS" for="<?=$rol['name']?>" ><?=$fnT($rol['name'])?></label>
                        <? endforeach ?>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-files" class="col-sm-2 col-form-label"><?=$fnT('Status')?></label>
                        <div style="display:flex; gap:20px; align-items: center; flex-wrap:wrap;" class="col-sm-10">
                          <input type="radio" checked class="statusF" hidden id="activo" name="statusF" value="1">
                          <label class="elementS" for="activo" >Activo</label>
                          <input type="radio" class="statusF" hidden id="inactivo" name="statusF" value="0">
                          <label class="elementS" for="inactivo" >Inactivo</label>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="input-files" class="col-sm-2 col-form-label"><?=$fnT('Expiration date')?></label>
                        <div style="display:flex; gap:20px; align-items: center; flex-wrap:wrap;" class="col-sm-10">
                          <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0"><?=$fnT('Date')?></span>
                            </div>
                            <input type="date" name="expirationDate" id="expirationDate" class="inpFecha" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>">
                          </div>
                        </div>
                      </div>
                      <input type="text" name="id" class="d-none">
                      <div class="form-group row">
                        <div class="col-sm-10">
                          <button type="submit" class="btn btn-s1" id="btn-send-af">Guardar</button>
                          <!--<button type="button" class="btn btn-danger" onclick="$('#collapseFormFile').collapse('hide')"><?=$fnT('Cancel')?></button>-->
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <div id="panel-files">
                    <div style="height: 350px" class="d-flex justify-content-center align-items-center">
                        <img src="<?=media()?>/images/loading.svg" height="60" alt="Loading">
                    </div>
                </div>
</main>
<script>
    let permissionDoc = <?=json_encode($data['permissionDoc'])?>;
    let paisesUsuario = <?=json_encode($data['paises'])?>;
    let rolUsuario = '<?=$data['rol']?>';
</script>
<?php footerTemplate($data);?>