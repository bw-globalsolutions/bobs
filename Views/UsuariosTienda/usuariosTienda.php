<?php 
  headerTemplateActualizado($data);
  getModal('modalUsuarios',$data);
  global $fnT;
?>
<style>
  table thead{background: linear-gradient(to right,  #335bff, #335bff); color:white;}  .colorBase{background: linear-gradient(to right,  #603B96, #603B96); color:white;}
</style>
    <div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
  <main class="app-content">
      
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <div class="table-responsive">
                <table class="table table-hover table-bordered" id="tableUsuariosTienda">
                  <thead>
                    <tr>
                    <th>USER</th>
                        <th>EMAIL</th>
                        <th>ROLE</th>
                        <th>#</th>
                        <th>STORE </th>
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
  </main>
<?php footerTemplateActualizado($data);?>