<?php global $fnT; ?>
<div class="modal fade" tabindex="-1" role="dialog" id="show-image-modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="show-image-title" class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body d-flex justify-content-center" id="show-image-panel" style="height: 550px;"></div>
      <div class="modal-footer" id="photo-action-panel">
        <div class="w-100">
          <span class="badge badge-secondary" id="question_prefix"></span> - 
          <span id="question_text"></span><br>
          <i class="fa fa-bolt text-danger"></i>&nbsp;<b>Opp:&nbsp;&nbsp;</b><span id="question_answers"></span>
        </div>
      </div>
    </div>
  </div>
</div>