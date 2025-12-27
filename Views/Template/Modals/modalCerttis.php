<?php global $fnT; ?>
<!-- Modal -->

<div class="modal fade " id="modalCerttis" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-lg">
            <div class="modal-header bg-dark">
                <h5 class="modal-title" id="titleModal">Añadir Certtis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <form id="formCerttis" class="form-horizontal">
                            <input type="" id="audit_id_certtis"     name="audit_id_certtis"     hidden>
                            <input type="" id="id_audit_opp_certtis" name="id_audit_opp_certtis" hidden>
                            <div class="form-row justify-content-center">
                                    <label for="action" class="control-label">Certtis</label>
                                    <select class="form-control" name="selectCerttis" id="selectCerttis" >
                                        <option value = 0 disabled selected>Selecciona una opcion</option>
                                        <option value = 1 >Redaccion    </option>
                                        <option value = 2 >Critico      </option>
                                        <option value = 3 >Fotografía   </option>
                                        <option value = 4 >Ortografía   </option>
                                    </select>
                                    <label for="action" class="control-label"><?=$fnT('Description')?></label>
                                    <textarea class="form-control valid validText" name="comentarioCerttis" id="comentarioCerttis" cols="40" rows="3" style="resize: both;" required></textarea>
                            </div>
                            <div class="tile-footer">
                                <button id="btnFormAddAction" class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><?=$fnT('Añadir')?></button>
                                <button class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i><?=$fnT('Cancel')?></button>
                            </div>
                        </form>
                    </div>
                    <div class="container-fluid">
                        <section class="content-header"></section>
                        <section class="content"  style="width: 100%;">
                            <div class="timeline" id="lineaTiempo" name="lineaTiempo"></div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade " id="" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title" id="titleModal">Confirmar envio Certtis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                      
                </div>
            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="modalCerttisEmail" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header headerRegister  bg-dark">
        <h5 class="modal-title" id="titleModal"><?=$fnT('Enviar Certtis')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tile">
        <div class="tile-body">
                        <form id="formCerttisEmail" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tile">
                                        <div class="tile-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered " id="tableCerttisEmail">
                                            <thead >
                                                <tr>

            				                        <th><?=$fnT('CERTTIS')?></th>
            				                        <th><?=$fnT('CERTTIS COMMENT')?></th>
            				                        <th># <?=$fnT('QUESTION')?></th>
            				                        <th><?=$fnT('AUDITOR ANSWER')?></th>
            				                        <th><?=$fnT('AUDITOR COMMENT')?></th>
            				                        <th></th>
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
                            <div class="tile-footer">
                            <input type="" id="audit_email"     name="audit_email"  hidden>
                                <button id="btnFormAddAction" class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><?=$fnT('Send')?></button>
                                <button class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i><?=$fnT('Cancel')?></button>
                            </div>
                        </form>
                    </div>  
          </div>
      </div>
    </div>
  </div>
</div>