<?php 
  headerTemplate($data);
  getModal('modalManuales',$data);
  global $fnT;
?>


<style>
	* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}



.wrap {
    max-width: 1100px;
    width: 90%;
    margin: auto;
}

.wrap > h1 {
    color: #494B4D;
    font-weight: 400;
    display: flex;
    flex-direction: column;
    text-align: center;
    margin: 15px 0;
}

.wrap > h1:after {
    content: '';
    width: 100%;
    height: 1px;
    background: #C7C7C7;
    margin: 20px 0;
}

.store-wrapper {
    display: flex;
    flex-wrap: wrap;
}

.category_list {
    display: flex;
    flex-direction: column;
    width: 18%;
}

.category_list .category_item {
    display: block;
    width: 90%;
    padding: 15px 0;
    margin-bottom: 20px;
    background: #dedede;
    text-align: center;
    text-decoration: none;
    color: #ffffff;
}

.category_list .ct_item-active {
    background: #c0c0c0;
    color: #ffffff; /* Cambia el color de la letra a blanco */
}

.products-list {
    width: 82%;
    display: flex;
    flex-wrap: wrap;
}

.products-list .product-item {
    
    transition: all 0.4s;
}






 table thead{background: linear-gradient(to right,  #E10000, #E10000); color:white;}  .colorBase{background: linear-gradient(to right,  #603B96, #603B96); color:white;}</style>
<div class="fig1"></div>
  <div class="fig2"></div>
  <div class="fig3"></div>
  <div class="fig4"></div>
  <div class="fig5"></div>
  <div class="fig6"></div>
  <main class="app-content">
<button class="btn btn-primary" type="button" onclick="openModal();"><i class="fa fa-plus-circle" aria-hidden="true"></i> Nuevo</button>
<br>
<br>
 
<!--SECCION DE MANUALES SE LLENA AUTOMATICAMENTE -->           
		<div class="store-wrapper">
			<div class="category_list" id="div_boton_manual">
			</div>
			<section class="products-list" id="section_manual"></section>
		</div>
<!--SECCION DE MANUALES SE LLENA AUTOMATICAMENTE -->  

    
</main>


<?php footerTemplate($data);?>