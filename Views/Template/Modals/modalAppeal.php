<?php global $fnT; ?>

<div class="modal fade" id="modalNewAppeal" role="dialog" aria-hidden="true" style="overflow-y: scroll;">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header text-center headerRegister">
        <h5 class="modal-title" id="titleModal"><?=$fnT('Nova apelação')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tile">
            <div class="tile-body">
                <input type="hidden" id="id_audit" name="id_audit" value="">
                <div class="form-row justify-content-center">
                  <div class="form-group col-md-10">
                    <label for="listTiendas"><?=$fnT('Auditoria')?></label>
                    <select class="form-control selectpicker" id="listAudits" name="listAudits" onChange="cargarOportunidadesAll(this.value)" data-actions-box="true" data-live-search="true">
                    </select>
                  </div>
                </div>
                <div id="contOppsAppeal">
                  <div class="row">
                    <div class="col-md-12">
                        <div class="tile">
                            <form id="formAllAppeals" class="form-horizontal">
                                
                                <div class="tile-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered" id="tableAppeals">
                                        <thead>
                                            <tr>
                                                <th><?=$fnT('ID')?></th>
                                                <th><?=$fnT('Oportunidade')?></th>
                                                <th><?=$fnT('Apelação')?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-12 text-center">
                                        <input type="hidden" id="idAuditDT" name="idAuditDT" value="">
                                        <button id="btnFormAddAllAppeals" class="btn btn-lg btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><?=$fnT('Enviar apelações')?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                  </div>
                </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalUpdAclaracion" tabindex="-1" role="dialog" aria-hidden="true" style="overflow-y: scroll;">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header headerUpdate bg-success">
        <h5 class="modal-title text-white" id="titleModal"><?=$fnT('Detalhe da apelação')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tile">
          <div class="tile-body">
            <div id="contentAppealUpdate">
              <div class="row">
                <div class="col-md-12">
                  <div class="tile">
                    <form id="formAllAppealsUpd" class="form-horizontal">
                      <div class="tile-body">
                          <div class="table-responsive">
                              <table class="table table-hover table-bordered" id="tableAppealsUpd">
                              <thead>
                                  <tr>
                                      <th><?=$fnT('ID')?></th>
                                      <th><?=$fnT('Apelação')?></th>
                                      <th><?=$fnT('Decisão')?></th>
                                  </tr>
                              </thead>
                              <tbody>
                              </tbody>
                              </table>
                          </div>
                      </div>
                      <div class="form-row justify-content-center">
                          <div class="form-group col-md-12 text-center">
                              <input type="hidden" id="id_appeal_upd" name="id_appeal_upd" value="">
                              <button id="btnFormAddAllAppeals" class="btn btn-lg  btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><?=$fnT('Salvar decisões')?></button>
                          </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>