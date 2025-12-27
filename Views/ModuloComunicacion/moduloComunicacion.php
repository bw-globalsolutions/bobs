<?php 
   headerTemplateActualizado($data);
  getModal('modalManuales',$data);
  global $fnT;
?>
<!-- Asegúrate de tener SweetAlert2 cargado en tu HTML -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  table thead{background: linear-gradient(to right,rgb(52, 58, 64),rgb(52, 58, 64)); color:white;}  .colorBase{background: linear-gradient(to right,  #603B96, #603B96); color:white;}
</style>
<style>*{margin:0;padding:0;box-sizing:border-box;}.wrap{max-width:1100px;width:90%;margin:auto;}.wrap>h1{color:#494B4D;font-weight:400;display:flex;flex-direction:column;text-align:center;margin:15px 0;}.wrap>h1:after{content:'';width:100%;height:1px;background:#C7C7C7;margin:20px 0;}.store-wrapper{display:flex;flex-wrap:wrap;}.category_list{display:flex;flex-direction:column;width:18%;}.category_list .category_item{display:block;width:90%;padding:15px 0;margin-bottom:20px;background:#dedede;text-align:center;text-decoration:none;color:#ffffff;}.category_list .ct_item-active{background:#c0c0c0;color:#ffffff;}.products-list{width:82%;display:flex;flex-wrap:wrap;}.products-list .product-item{transition:all 0.4s;}</style>
<main class="app-content">
<input id = 'rol'value = ' <? echo $_SESSION['userData']['role']['id'] ?>' hidden></input>  
<? if( in_array( $_SESSION['userData']['role']['id'], [1,2] )): ?>
    <button class="btn bg-dark text-white" type="button" onclick="openModal();">
        <i class="fa fa-plus-circle" aria-hidden="true"></i> <?=$fnT('New')?>
    </button>
 <? endif ?>

    <br>
    <br>


    
     
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <div class="table-responsive">
                <table class="table table-hover table-bordered" id="tableManuales">
                  <thead>
                    <tr>
                        <th>ID</th>
                        <th><?=$fnT('Category')?></th>
                        <th><?=$fnT('Name')?></th>
                        <th><?=$fnT('Description')?></th>
                        <th><?=$fnT('Language')?></th>
                        <th><?=$fnT('Link')?> </th>
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
 

<? if( in_array( $_SESSION['userData']['role']['id'], [1,2] )): ?>
<!--SECCION DE MANUALES SE LLENA AUTOMATICAMENTE -->           
		<div class="store-wrapper" >
			<div class="category_list" id="div_boton_manual"></div>
			<section class="products-list" id="section_manual"></section>
		</div>
<!--SECCION DE MANUALES SE LLENA AUTOMATICAMENTE -->  
  <? endif ?>
<!--SECCION DE MANUALES SE LLENA AUTOMATICAMENTE           
		<div class="store-wrapper">
			<div class="category_list" id="div_boton_manual"></div>
			<section class="products-list" id="section_manual"></section>
		</div>
   SECCION DE MANUALES SE LLENA AUTOMATICAMENTE -->




    
</main>


<?php footerTemplateActualizado($data);?>