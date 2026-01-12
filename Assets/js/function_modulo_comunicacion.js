
console.log('Modulo de comunicacion:');
document.addEventListener('DOMContentLoaded', function(){
	manualesAutomatico();	

	
	$('#formManual').on('submit', function(event) {
		event.preventDefault(); 
		var formData = $(this).serialize();
		$.ajax({
			url: base_url + '/moduloComunicacion/setManual', 
			type: 'POST',
			data: formData,
			success: function(response) {
			
				console.log('Éxito:', response);
				swal(fnT('Êxito'), 'Manual registrado correctamente', "success");
				manualesAutomatico();	
				  $('#modalFormManual').modal('hide');
			
			},
			error: function(xhr, status, error) {
				// Manejar el error
				console.error('Error:', error);
				alert('Hubo un problema al enviar el formulario.');
			}
		});
	});
	

});

/*cargarTema();

function cargarTema(){

    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/personalization/cargarTema';
            var strData = "id=1";
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){

                if(request.readyState == 4 && request.status == 200){
                    //console.log(request.responseText);
                    var objData = JSON.parse(request.responseText);

                    document.documentElement.style.setProperty("--color1", objData[0].color1);
                    document.documentElement.style.setProperty("--color2", objData[0].color2);
                    document.documentElement.style.setProperty("--color3", objData[0].color3);
                    document.documentElement.style.setProperty("--color4", objData[0].color4);

                    if(objData[0].img2!=''){
                        if(document.querySelector('.img-fluid')){
                            document.querySelector('.img-fluid').src=objData[0].img2;
                        }
                    }

                    if(objData[0].img3!=''){
                        // Obtener el elemento del favicon (si existe)
                        const favicon = document.querySelector('link[rel="icon"]') || 
                        document.createElement('link');

                        // Configurar sus atributos
                        favicon.rel = 'icon';
                        favicon.href = objData[0].img3; // Ruta del nuevo favicon
                        favicon.type = 'image/x-icon';

                        // Añadirlo al <head> si no existía
                        document.head.appendChild(favicon);
                    }

                }
            }
}*/

function openModal(){
	$('#modalFormManual').modal('show');
	document.querySelector('#formManual').reset();
	$("#nuevaCategoria").hide();
}
function manualesAutomatico(){
	
	$("#section_manual").empty();
	$("#div_boton_manual").empty();
	$("#div_boton_manual").append(`<a class="category_item" category="all"><b>TODO</b></a>`);


	// MANUALES  
	$.ajax({
	
		type: "POST",
		url:  " "+base_url+"/moduloComunicacion/getManuales",
		data: {},
		dataType: "json",
					   success: function(data){
		
					$.each(data,function(key, registro) {
	
						
						
						let id_manual     	    = registro.id_manual;  
						let ruta_manual     	= registro.ruta_manual;  
						let categoria     		= registro.categoria;  
						let descripcion_manual  = registro.descripcion_manual;  
						let nombre_manual       = registro.nombre_manual; 
						
						
						if ($("#div_boton_manual a[category='categoria" + categoria + "']").length === 0) {
							// Si no existe, agregar el botón de categoría
							$("#div_boton_manual").append(`<a class="category_item" category="categoria` + categoria + `"><b>` + categoria + `</b></a>`);
						}
						if ($("#txtCategoria option[value='" + categoria + "']").length === 0) {
							// Si no existe, agregar la opción
							$("#txtCategoria").append(`<option value="`+categoria+`">`+categoria+`</option>`);
						}
						
						$("#section_manual").append(`<div class="col-lg-12 col-12">			
														  <div class="product-item" category="categoria`+categoria+`">
															  <div class="card" >
																  <h5 class="card-header bg-dark text-center text-white">`+nombre_manual+`</h5>
																  <div class="card-body">
																	  <iframe src="`+ruta_manual+`" width="100%" height="500"></iframe>
																	  <div class="float-right">
																	  </div>
																  </div>
															  </div>
														  </div>
													  </div>`);
					});
						  
	// AGREGANDO CLASE ACTIVE AL PRIMER ENLACE 
	$('.category_list .category_item[category="all"]').addClass('ct_item-active');
	
	// FILTRANDO PRODUCTOS  
	
	$('.category_item').click(function(){
		var catProduct = $(this).attr('category');
		console.log(catProduct);
	
		// AGREGANDO CLASE ACTIVE AL ENLACE SELECCIONADO
		$('.category_item').removeClass('ct_item-active');
		$(this).addClass('ct_item-active');
	
		// OCULTANDO PRODUCTOS =========================
		$('.product-item').css('transform', 'scale(0)');
		function hideProduct(){
			$('.product-item').hide();
		} setTimeout(hideProduct,400);
	
		// MOSTRANDO PRODUCTOS =========================
		function showProduct(){
			$('.product-item[category="'+catProduct+'"]').show();
			$('.product-item[category="'+catProduct+'"]').css('transform', 'scale(1)');
		} setTimeout(showProduct,400);
	});
	
	// MOSTRANDO TODOS LOS PRODUCTOS =======================
	
	$('.category_item[category="all"]').click(function(){
		function showAll(){
			$('.product-item').show();
			$('.product-item').css('transform', 'scale(1)');
		} setTimeout(showAll,400);
	});
					
				 },
	
				error: function(data) {
					console.log(data);
				 }
	});
	
}



function uploadPic(obj, id){
	//alert(id);
	file = obj.files[0];
	$('#divLoading').css('display', 'flex');
	fetch('https://ws.bw-globalsolutions.com/WSAAA/receiveFile.php?token=x', {
		method: 'POST',
		body: file
	}).then(res => res.json()).then(dat => {
		if(dat.Message == "SUCCESS"){
			//$(obj).val('');
			$('#tmpListEvidences_'+id).append('<label><a href="'+ dat.Info.location +'" target="_blank" class="text-warning"><i class="fa fa-file fa-2x"></i></a><i onclick="dropImg(this, \''+ dat.Info.location +'\')" class="fa fa-times-circle dropEvid"></i></label>');
			// $('#visit-pic').val(dat.Info.location);0
			$('#evidencias_'+id).val($('#evidencias_'+id).val() + '' + dat.Info.location);
			$('#divLoading').css('display', 'none');

		} else {
			console.error(dat);
			$('#divLoading').css('display', 'none');
			alert('Error.');
		}
	});
}



function verificarSeleccion() {
	var select = $("#txtCategoria").val();
	
	if (select === "1") {
	
		$("#nuevaCategoria").show();
		
	}else{$("#nuevaCategoria").hide();}
}



