<? global $fnT; ?>
<!-- Modal -->
<div class="modal fade" id="modalMassInsertion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="titleModal"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                    <div class="tile-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-4" data-toggle="collapse" data-target="#collapseLocations" aria-expanded="true" aria-controls="collapseLocations">
                                <div class="widget-small primary coloured-icon cr-pointer"><i class="icon fa fa-undo fa-3x"></i>
                                    <div class="info">
                                        <h4><?= $fnT('Localizações') ?></h4>
                                        <p><b id="counter-locations"></b></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4" data-toggle="collapse" data-target="#collapseErrors" aria-expanded="false" aria-controls="collapseErrors">
                                <div class="widget-small danger coloured-icon cr-pointer"><i class="icon fa fa-exclamation-triangle fa-3x"></i>
                                    <div class="info">
                                        <h4><?= $fnT('Erros') ?></h4>
                                        <p><b id="counter-errors"></b></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4" data-toggle="collapse" data-target="#collapseUsers" aria-expanded="false" aria-controls="collapseUsers">
                                <div class="widget-small success coloured-icon cr-pointer"><i class="icon fa fa-users fa-3x"></i>
                                    <div class="info">
                                        <h4><?= $fnT('Usuários') ?></h4>
                                        <p><b id="counter-users"></b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="accordionMass">
                            <div class="card">
                                <div id="collapseLocations" class="collapse show" data-parent="#accordionMass">
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush" id="list-locations"></ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div id="collapseErrors" class="collapse" data-parent="#accordionMass">
                                    <ul class="list-group list-group-flush" id="list-errors"></ul>
                                </div>
                            </div>
                            <div class="card">
                                <div id="collapseUsers" class="collapse" data-parent="#accordionMass">
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush" id='list-users'></ul>
                                    </div>
                                </div>
                            </div>
                        </div>                     
                    </div>
                    <div class="tile-footer">
                        <button class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i><?=$fnT('Fechado')?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>