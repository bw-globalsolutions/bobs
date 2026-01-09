<?php global $fnT; ?>
<!-- Modal -->
<style>
    .timeline>div {
        margin-bottom: 15px;
        margin-right: 10px;
        position: relative;
    }
    .timeline>div>.timeline-item {
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        border-radius: .25rem;
        background-color: #fff;
        color: #495057;
        margin-left: 60px;
        margin-right: 15px;
        margin-top: 0;
        padding: 0;
        position: relative;
    }
    .timeline>div>.timeline-item>.time {
        color: #999;
        float: right;
        font-size: 12px;
        padding: 10px;
    }
    .timeline>div>.timeline-item>.timeline-header {
        border-bottom: 1px solid rgba(0, 0, 0, .125);
        color: #495057;
        font-size: 16px;
        line-height: 1.1;
        margin: 0;
        padding: 10px;
    }
    .timeline>div>.timeline-item>.timeline-body, .timeline>div>.timeline-item>.timeline-footer {
        padding: 10px;
    }
    .timeline::before {
        border-radius: .25rem;
        background-color: #dee2e6;
        bottom: 0;
        content: "";
        left: 31px;
        margin: 0;
        position: absolute;
        top: 0;
        width: 4px;
    }
    .timeline>div::after, .timeline>div::before {
        content: "";
        display: table;
    }
    .container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl {
        width: 100%;
        padding-right: 7.5px;
        padding-left: 7.5px;
        margin-right: auto;
        margin-left: auto;
    }
    .content-header {
        padding: 15px .5rem;
    }
    *, ::after, ::before {
        box-sizing: border-box;
    }
    .timeline {
        margin: 0 0 45px;
        padding: 0;
        position: relative;
    }
    .timeline>.time-label>span {
        border-radius: 4px;
        background-color: #fff;
        display: inline-block;
        font-weight: 600;
        padding: 5px;
    }
    .modal-open .modal {
        overflow-x: hidden;
        overflow-y: auto;
    }
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        display: none;
        width: 100%;
        height: 100%;
        overflow: hidden;
        outline: 0;
    }
    .fade {
        transition: opacity .15s linear;
    }
</style>
<div class="modal fade " style="overflow-x: hidden;overflow-y: auto;" id="modalCerttis" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-lg">
            <div class="modal-header bg-dark">
                <h5 class="modal-title" style="color:#fff" id="titleModal">Add Certtis</h5>
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
                                        <option value = 1 >Drafting    </option>
                                        <option value = 2 >Critical      </option>
                                        <option value = 3 >Photograph   </option>
                                        <option value = 4 >Spelling   </option>
                                    </select>
                                    <label for="action" class="control-label"><?=$fnT('Description')?></label>
                                    <textarea class="form-control valid validText" name="comentarioCerttis" id="comentarioCerttis" cols="40" rows="3" style="resize: both;" required></textarea>
                            </div>
                            <div class="tile-footer">
                                <button id="btnFormAddAction" class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><?=$fnT('Add')?></button>
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