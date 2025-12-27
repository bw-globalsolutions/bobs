
console.log('Modulo de comunicacion:');
document.addEventListener('DOMContentLoaded', function(){
	manualesAutomatico();
	getManuales();
	
$('#formManual').on('submit', function(event) {
	event.preventDefault(); 
	var formData = $(this).serialize();
	$.ajax({
		url: base_url + '/moduloComunicacion/setManual', 
		type: 'POST',
		data: formData,
		success: function(response) {
		
			console.log('Éxito:', response);
			Swal.fire({
				title: 'Éxito',
				text: 'Manual registrado correctamente',
				icon: 'success',
				confirmButtonText: 'OK'
			});
			
			manualesAutomatico();	
			getManuales();

			$('#modalFormManual').modal('hide');
		},
		error: function(xhr, status, error) {
			console.error('Error:', error);
			Swal.fire({
				title: 'Error',
				text: 'Hubo un problema al enviar el formulario.',
				icon: 'error'
			});
		}
	});
});

	

});


function getManuales() {



    // Obtener el parámetro "country" de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const country = urlParams.get('country'); // Ej: ?country=MX

    tableManuales = $('#tableManuales').dataTable({
        "aProcessing": true,
        "aServerSide": true,
       "language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		
        "ajax": {
            "url": base_url + "/moduloComunicacion/getManuales",
            "data": function (d) {
                d.country = country; // agrega el parámetro de la URL
            },
            "dataSrc": ""
        },

        searchPanes: {
            dtOpts: {
                dom: 'tp',
                paging: 'true',
                pagingType: 'simple',
                searching: true
            }
        },
        dom: 'Pfrtip',
        columnDefs: [
            {
                searchPanes: { show: true, initCollapsed: true },
                targets: [1]
            },
            {
                searchPanes: { show: false, initCollapsed: true },
                targets: [0, 2, 3, 4]
            }
        ],

        "columns": [
            { "data": "id_manual" },
            { "data": "categoria" },
            { "data": "nombre_manual" },
            { "data": "descripcion_manual" },
            { "data": "lang" },
            { 
                "data": "ruta_manual",
                "render": function (dato, type, row, meta) {
                    return `<center>
                        <button class="btn btn-outline-danger" 
                            onclick="window.open('/moduloComunicacion/viewPDF?file=${encodeURIComponent(row['ruta_manual'])}', '_blank')">
                            <i class="fa-solid fa-file-pdf"></i>
                        </button>
                    </center>`;
                }
            }
        ],

        "dom": "PlfZtip",
        "resonsieve": "true",
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]],
    });

}


function openModal(){
	$('#modalFormManual').modal('show');
	document.querySelector('#formManual').reset();
	$("#nuevaCategoria").hide();
}
/*function manualesAutomatico(){
	
	$("#section_manual").empty();
	$("#div_boton_manual").empty();
	$("#div_boton_manual").append(<a class="category_item" category="all"><b>TODO</b></a>);


	// MANUALES  
	$.ajax({
	
		type: "POST",
		url:  " "+base_url+"/moduloComunicacion/getManuales",
		data: {},
		dataType: "json",
					   success: function(data){
		
					$.each(data,function(key, registro) {
	var rol = $("#rol").val();
	console.log('ejuemplo');
	console.log(rol);
						if (rol > 2){
 var oculto = 'hidden';
}else {
	var oculto = '';
}
	console.log(oculto);					
						let id_manual     	    = registro.id_manual;  
						let ruta_manual     	= registro.ruta_manual;  
						let categoria     		= registro.categoria;  
						let descripcion_manual  = registro.descripcion_manual;  
						let nombre_manual       = registro.nombre_manual; 
						
						
						if ($("#div_boton_manual a[category='categoria" + categoria + "']").length === 0) {
							// Si no existe, agregar el botón de categoría
							$("#div_boton_manual").append(<a class="category_item" category="categoria + categoria + "><b> + categoria + </b></a>);
						}
						if ($("#txtCategoria option[value='" + categoria + "']").length === 0) {
							// Si no existe, agregar la opción
							$("#txtCategoria").append(<option value="+categoria+">+categoria+</option>);
						}
						
						$("#section_manual").append(<div class="col-lg-12 col-12">			
													  <div class="product-item" category="+categoria+">
													    <div class="card">
													      <h5 class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
													        <span>+nombre_manual+</span>
													        <div>
													          <button class="btn btn-primary btn-sm mr-2" onclick="editarManual('+id_manual+')" +oculto+>
													            <i class="fa fa-edit" aria-hidden="true"></i>
													          </button>
													          <button class="btn btn-danger btn-sm" onclick="eliminarManual('+id_manual+')" +oculto+>
													            <i class="fa fa-trash" aria-hidden="true"></i>
													          </button>
													        </div>
													      </h5>
													      <div class="card-body">
													        <iframe src="+ruta_manual+" width="100%" height="500"></iframe>
													        <div class="float-right">
													        </div>
													      </div>
													    </div>
													  </div>
													</div>);
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
*/

function manualesAutomatico() {

    // Limpiar secciones
    $("#section_manual").empty();
    $("#div_boton_manual").empty().append(`<a class="category_item" category="all"><b>TODO</b></a>`);
    

    // Obtener rol del usuario
    const rol = $("#rol").val();

    // Llamada AJAX
    $.ajax({
        type: "POST",
        url: base_url + "/moduloComunicacion/getManuales",
        dataType: "json",
        success: function(data) {

            $.each(data, function(_, registro) {
                
                let id_manual          = registro.id_manual;  
                let ruta_manual        = registro.ruta_manual;  
                let categoria          = registro.categoria;  
                let descripcion_manual = registro.descripcion_manual;  
                let nombre_manual      = registro.nombre_manual; 

                // Botón de categoría si no existe
                if ($("#div_boton_manual a[category='" + categoria + "']").length === 0) {
                    $("#div_boton_manual").append(
                        `<a class="category_item" category="${categoria}"><b>${categoria}</b></a>`
                    );
                }

                // Opción del select si no existe
                if ($("#txtCategoria option[value='" + categoria + "']").length === 0) {
                    $("#txtCategoria").append(`<option value="${categoria}">${categoria}</option>`);
                }

                // Determinar si se muestra el botón de editar/eliminar
                let oculto = (rol > 2) ? 'hidden' : '';

                // Agregar tarjeta de manual
                $("#section_manual").append(`
                    <div class="col-lg-6 col-6">			
                        <div class="product-item" category="${categoria}">
                            <div class="card">
                                <h5 class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                    <span>${nombre_manual}</span>
                                    <div>
                                        <button class="btn btn-primary btn-sm mr-2" onclick="editarManual('${id_manual}')" ${oculto}>
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="eliminarManual('${id_manual}')" ${oculto}>
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </h5>
                                <div class="card-body">
                                    <iframe src="${ruta_manual}" width="100%" height="500"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            });

            // Activar la categoría "all" al inicio
            $('.category_item[category="all"]').addClass('ct_item-active');

            // Evento para filtrar productos por categoría
            $('.category_item').off('click').on('click', function() {
                let catProduct = $(this).attr('category');

                // Marcar activa
                $('.category_item').removeClass('ct_item-active');
                $(this).addClass('ct_item-active');

                if (catProduct === "all") {
                    $('.product-item').fadeIn().css('transform', 'scale(1)');
                } else {
                    $('.product-item').hide().css('transform', 'scale(0)');
                    $('.product-item[category="'+catProduct+'"]').fadeIn().css('transform', 'scale(1)');
                }
            });

        },
        error: function(err) {
            console.error("Error cargando manuales:", err);
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
function editarManual(id_manual) {
	const card = document.querySelector(`[onclick="editarManual('${id_manual}')"]`).closest('.card');
	const nombreActual = card.querySelector('.card-header span').innerText;
	const categoriaActual = card.closest('.product-item').getAttribute('category');
  
	let nombreInput, categoriaInput, langInput;
  
	Swal.fire({
	  title: 'Editar',
	  html: `<div style="display: flex; align-items: center; margin-bottom: 10px;">
      			<label for="nombre" style="width: 100px; text-align: right; margin-right: 10px;">Nombre:</label>
      			<input type="text" id="nombre" class="swal2-input" style="width: 250px;" value="${nombreActual}">
    		</div>
    		<div style="display: flex; align-items: center;">
    		  	<label for="categoria" style="width: 100px; text-align: right; margin-right: 10px;">Categoría:</label>
    		  	<input type="text" id="categoria" class="swal2-input" style="width: 250px;" value="${categoriaActual}">
    		</div>
			<div style="display: flex; align-items: center;">
    		  	<label for="categoria" style="width: 100px; text-align: right; margin-right: 10px;">Idioma:</label>
    		  	<select class="form-control" id="txtLang" name="txtLang" type="text">
                      <option value="eng">English</option>
                      <option value="esp">Spanish</option>
                      <option value="ind">Indonesian</option>
                </select>
    		</div>`,
				
	  confirmButtonText: 'Guardar',
	  focusConfirm: false,
	  didOpen: () => {
		const popup = Swal.getPopup();
		nombreInput = popup.querySelector('#nombre');
		categoriaInput = popup.querySelector('#categoria');
		langInput = popup.querySelector('#txtLang'); // <---- agregado

		nombreInput.onkeyup = (event) => event.key === 'Enter' && Swal.clickConfirm();
		categoriaInput.onkeyup = (event) => event.key === 'Enter' && Swal.clickConfirm();
	  },
	  preConfirm: () => {
		const nombre = nombreInput.value.trim();
		const categoria = categoriaInput.value.trim();
		const txtLang = langInput.value; // <---- agregado
  
		if (!nombre || !categoria) {
		  Swal.showValidationMessage('Por favor, completa todos los campos');
		  return false;
		}
  
		return { nombre, categoria, txtLang }; // <---- agregado
	  }
	}).then(result => {
	  if (result.isConfirmed) {
		const { nombre: nuevoNombre, categoria: nuevaCategoria, txtLang } = result.value; // <---- agregado
  
		const formData = {
		  id_manual: id_manual,
		  nombre: nuevoNombre,
		  categoria: nuevaCategoria,
		  txtLang: txtLang // <---- agregado
		};
  
		$.ajax({
		  url: base_url + '/moduloComunicacion/editarManual',
		  type: 'POST',
		  data: formData,
		  success: function(response) {
			console.log('Éxito:', response);
  
			Swal.fire('Actualizado', 'El manual fue actualizado exitosamente.', 'success').then(() => {
			  card.querySelector('.card-header span').innerText = nuevoNombre;
			  card.closest('.product-item').setAttribute('category', nuevaCategoria);
			});
  
			$('#modalFormManual').modal('hide');
			manualesAutomatico();
			getManuales();
		  },
		  error: function(xhr, status, error) {
			console.error('Error:', error);
			Swal.fire('Error', 'Hubo un problema al enviar el formulario.', 'error');
		  }
		});
	  }
	});
}

  
  function eliminarManual(id_manual) {
	Swal.fire({
	  title: "¿Estás seguro?",
	  text: "¡No podrás revertir esto!",
	  icon: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Sí, eliminar",
	  cancelButtonText: "Cancelar"
	}).then((result) => {
	  if (result.isConfirmed) {
		$.ajax({
		  url: base_url + '/moduloComunicacion/eliminarManual', // Ajusta la URL si es necesario
		  type: 'POST',
		  data: { id_manual: id_manual },
		  success: function(response) {
			
			  Swal.fire("Eliminado", "El manual ha sido eliminado.", "success");
			  manualesAutomatico();	
			  getManuales();
			  const card = document.querySelector(`[onclick="eliminarManual('${id_manual}')"]`).closest('.col-12');
			  if (card) card.remove();
			
		  },
		  error: function(xhr, status, error) {
			console.error(error);
			Swal.fire("Error", "Ocurrió un error en la conexión.", "error");
		  }
		});
	  }
	});
  }



