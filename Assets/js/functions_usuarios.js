const formUser = document.getElementById('formUser');
const divLoading = document.getElementById('divLoading');

let tableUsuarios;
let rowTable = "";
console.log('function_usuarios');
document.addEventListener('DOMContentLoaded', function(){

	tableUsuarios = $('#tableUsuarios').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/usuarios/getUsuarios",

			"dataSrc":""
		},
		"columns":[
			{"data":"id"},
			{"data":"name"},
			{"data":"email"},
			{"data":"brand"},
			{"data":"country"},
			{"data":"status"},
			{"data":"role"},
			{"data":"options"}
		],
		"dom": "lfBrtip",
        "buttons": [],
		"resonsieve":"true",
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"asc"]]
	});






	tableUsuariosTienda = $('#tableUsuariosTienda').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
		},
		"ajax":{
			"url": " "+base_url+"/usuarios/user",

			"dataSrc":""
		},

		searchPanes:{
        
            dtOpts:{
                dom:'tp',
                paging:'true',
                pagingType:'simple',
                searching:true
            }
        },
        dom:'Pfrtip',
        columnDefs: [
    {
        searchPanes: {
            show: true,
            initCollapsed: true

        },
        targets: [2]
    },
    {
        searchPanes: {
            show: false,
			initCollapsed: true
        },
        targets: [0,1,3,4]
    }
],






		"columns":[
			{"data":"usuario"},
			{"data":"email"},
			{"data":"role"},
			{"data":"number"},
			{"data":"location_name"}
		
			
		],
		"dom": "BPlfZtip",
      
		"resonsieve":"true",
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"desc"]],
		buttons:
		[
            {
                extend:    'excelHtml5',
                text:      '<i class="fas fa-file-excel"></i> Excel',
                titleAttr: 'Encuesta auditor',
                className: 'btn btn-success',
                title: 'Excel'
            },
			{
                text:  `<a  data-toggle='modal' data-target='#modalCSV'>
                        <i class="fa-solid fa-file-csv"></i> Download archive csv</a>`,
   
                className: `btn bg-gray`
            }
        ],/**/
        
	});

function confirmationQuestion(id, audit_id, type) {
    var id_item           = id;
    var id_auditoria      = audit_id;
    var modulo            = type;
    Swal.fire({
      title: fnT('Confirmation'),
      text: fnT('Select an option and add a comment'),
      input: 'textarea',
      inputPlaceholder: fnT('Write your comment here...'),
      showCancelButton: true,
      confirmButtonText: fnT('Confirm'),
      cancelButtonText: fnT('Cancel'),
      reverseButtons: false,
      html: `
      <div style="display: flex; gap: 20px; justify-content: center;">
      <label><input type="radio" name="action" value="1" /> `+fnT('Fulfills')+`</label>
      <label><input type="radio" name="action" value="0" /> `+fnT('No, Fulfills')+`</label>
      </div>
      `,
      preConfirm: (comment) => {  
        const selectedOption = document.querySelector('input[name="action"]:checked');
        if (!selectedOption) {
          Swal.showValidationMessage(fnT('Please select an option'));
          return false;
        }
        if (!comment) {
          Swal.showValidationMessage(fnT('Please add a comment'));
          return false;
        }
        return { comment, selectedOption: selectedOption.value };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        $('#divLoading').css('display', 'flex');
        $.ajax({
            type: "POST",
               url:  " "+base_url+"/additional_Question/confirmationQuestion",
               data:  'comentario='+result.value.comment
               +'&confirmacion='+result.value.selectedOption,
               success: function(response) {
               console.log('Éxito:', response);
               const Toast = Swal.mixin({
               toast: true,
               position: "top-end",
               showConfirmButton: false,
               timer: 1300,
               timerProgressBar: true,
               didOpen: (toast) => {
               toast.onmouseenter = Swal.stopTimer;
               toast.onmouseleave = Swal.resumeTimer;
                }
              });
              Toast.fire({
                icon: 'success',
                title: fnT('Successful confirmation')
              });
              $('#divLoading').css('display', 'none');
              location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Hubo un problema al enviar los datos.');
                $('#divLoading').css('display', 'none');
            }
        });
        //alert(id_item+' '+id_auditoria+''+result.value.comment+''+result.value.selectedOption);
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.close();
      }
    });
}

 


































	formUser.addEventListener('submit', e => {
		e.preventDefault();
		
		divLoading.style.display = "flex";
		const payload = new FormData(formUser);
		fetch(base_url + '/usuarios/setUsuario', {
			method: 'POST',
			body: payload
		}).then(res => res.json()).then(dat => {
			if(dat.status){
				$('#modalFormUser').modal("hide");
				tableUsuarios.api().ajax.reload();
				swal(fnT('Users'), fnT(dat.msg), "success");
				formUser.reset();
			}else{
				swal(fnT('Error'), fnT(dat.msg), "error");
			}
			divLoading.style.display = "none";
		});

	});
});

function fntViewUsuario(idUser){
	var iduser = idUser;
	var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	var ajaxUrl = base_url+'/usuarios/getUsuario/'+iduser;
	request.open("GET",ajaxUrl,true);
	request.send();

	request.onreadystatechange = function(){
		if(request.readyState == 4 && request.status == 200){
			var objData = JSON.parse(request.responseText);
			if(objData.status)
			{
				document.getElementById('cel-id').innerHTML = objData.data.id;
				document.getElementById('cel-name').innerHTML = objData.data.name;
				document.getElementById('cel-email').innerHTML = objData.data.email;
				document.getElementById('cel-brand').innerHTML = objData.data.brand;
				document.getElementById('cel-country').innerHTML = objData.data.country;
				document.getElementById('cel-language').innerHTML = objData.data.default_language;
				document.getElementById('cel-role').innerHTML = objData.data.role;
				document.getElementById('cel-location').innerHTML = objData.data.location;
				document.getElementById('cel-status').innerHTML = objData.data.status == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>';
				document.getElementById('cel-regDate').innerHTML = objData.data.created;
				$('#modalViewUser').modal('show');
			}else{
				swal(fnT('Error'),fnT(objData.msg),"error");
			}
		}
	}
}

function fntEditUsuario(element, idUser){
	rowTable = element.parentNode.parentNode.parentNode;
	formUser.reset();

	document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
	document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
	document.querySelector('#titleModal').innerHTML = fnT('Update user');
	document.querySelector('#btnText').innerHTML = fnT('Save');

	var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	var ajaxUrl = base_url+'/usuarios/getUsuario/'+idUser;
	request.open("GET",ajaxUrl,true);
	request.send();

	request.onreadystatechange = function(){
		if(request.readyState == 4 && request.status == 200){
			var objData = JSON.parse(request.responseText);

			if(objData.status)
			{
				formUser['id'].value = objData.data.id;
				formUser['name'].value = objData.data.name;
				formUser['email'].value = objData.data.email;
				formUser['status'].value = objData.data.status;
				formUser['notification'].value = objData.data.notification;
				formUser['language'].value = objData.data.default_language;
				
				formUser['role'].value = objData.data.role_id;
				limitRole(objData.data.level);
				$('#user_country').selectpicker('val', objData.data.country_id.split(','));
				limitCountry();

				$('#user_brand').selectpicker('val', objData.data.brand_id.split(','));
				$('#user_location').selectpicker('val', objData.data.location_id.split(','));
			}
			$('#formUser .selectpicker').selectpicker('refresh');
			$('#modalFormUser').modal('show');
		}
	}
}

function fntDelUsuario(idUser){
	var iduser = idUser;
	swal({
		title: fnT('Delete user'),
		text: fnT('Do you really want to delete this User ?'),
		type: "warning",
		showCancelButton: true,
		confirmButtonText: fnT('Yes, delete'),
		cancelButtonText: fnT('No, cancel'),
		closeOnConfirm: false,
		closeOnCancel: true
	}, function(isConfirm){
		if(isConfirm){
			var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
			var ajaxUrl = base_url+'/usuarios/delUsuario/';
			var strData = "iduser="+iduser;
			request.open("POST",ajaxUrl,true);
			request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			request.send(strData);
			request.onreadystatechange = function(){
				if(request.readyState == 4 && request.status == 200){
					var objData = JSON.parse(request.responseText);
					if(objData.status)
					{
						swal(fnT('Delete'), fnT(objData.msg), "success");
						tableUsuarios.api().ajax.reload();
					}else{
						swal(fnT('Attention'), fnT(objData.msg), "error");
					}
				}
			}
		}
	});
}

function openModal(){
	rowTable = "";
	document.getElementById('user_id').value = "";
	document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
	document.getElementById('btnActionForm').classList.replace("btn-info", "btn-primary");
	document.getElementById('titleModal').innerHTML = fnT("New user");
	document.getElementById('btnText').innerHTML = fnT("Save");
	formUser.reset();

	limitRole();
	$('.selectpicker').selectpicker('deselectAll');
	$('#formUser .selectpicker').selectpicker('refresh');
	$('#modalFormUser').modal('show');
}

function country_area(id){
	
	if(id==3){
$("#user_country optgroup[label='AMR']").remove();
	}
	
 }
function limitRole(level = false){
	if(!level){
		level = $(`#user_role [value='${$('#user_role').val()}']`).data('level');
	}
	level = +level;

	$('#user_country').selectpicker('deselectAll');
	$('#user_country').selectpicker({
		maxOptions: level > 2? false : 1
	});

	if([1,4,5,6].includes(level)){
		$('#user_location').prop('disabled', true);
	}else{
		$('#user_location').selectpicker('deselectAll');
		$('#user_location').removeAttr('disabled');
		$('#user_location').selectpicker({
			maxOptions: (level == 3? false : 1)
		});
	}

	$('#btn-selected-all').toggle(level == 3);
	$('#user_location').selectpicker('refresh');
}

function limitCountry(){
	// Quitar el optgroup AMR del select de países:
	
	$('#user_location').selectpicker('deselectAll');

	const paisesSeleccionados = $('#user_country').val() ?? [];

	// Oculta todos los options que no están relacionados
	$('#user_location [data-country]').each(function(){
		const pais = $(this).data('country').toString();
		$(this).toggle(paisesSeleccionados.includes(pais));
	});

	

	// Refrescar ambos selectpickers
	$('#user_country').selectpicker('refresh');
	$('#user_location').selectpicker('refresh');
}
function selectAllLocations(){
	const countSelected = $('#user_location').val().length;
	let aux = [];
	document.querySelectorAll('#user_location option:not([style="display: none;"])').forEach(item => {
		aux.push(item.value);
	});

	if(countSelected != aux.length){
		$('#user_location').selectpicker('val', aux);
	} else{
		$('#user_location').selectpicker('deselectAll');
	}
	$('#user_location').selectpicker('refresh');

}

