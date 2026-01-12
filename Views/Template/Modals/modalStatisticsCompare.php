<? global $fnT; ?>
<!-- Modal -->
<div class="modal fade" id="modalStatisticsCompare" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal"><?=$fnT('Gráfico de comparação')?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="tile">
            <div class="tile-body">
              <div class="row">
                <div class="col-12 col-lg-6 mb-4">
                      <h4 class="text-center mb-2"><?= $fnT('Período atual') ?></h4>
                      <div id="chart-current-period"></div>
                  </div>
                <div class="col-12 col-lg-6 mb-4">
                      <h4 class="text-center mb-2"><?= $fnT('Comparar período') ?></h4>
                      <div id="chart-compare-period"></div>
                  </div>
              </div>           
            </div>
          </div>
      </div>
    </div>
  </div>
</div>