const formRole = document.getElementById("formRol");
const divLoading = document.querySelector('#divLoading');
var tableRoles;
var tableRolesVista;

document.addEventListener('DOMContentLoaded', function(){

	tableRoles = $('#tableRoles').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/roles/getRoles",

			"dataSrc":""
		},
		"columns":[
			{"data":"id"},
			{"data":"name"},
			{"data":"description"},
			{"data":"level"},
			{"data":"status"},
			{"data":"options"},
		],
		"resonsieve":"true",
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"asc"]]
	});



	tableRolesVista = $('#tableRolesVista').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/roles/getRoles",

			"dataSrc":""
		},
		"columns":[
			{"data":"id"},
			
			{"data":"description"},
			{"data":"level"},
			{"data":"status"},
		],
		"resonsieve":"true",
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"asc"]]
	});

	//nuevo - actualizacion roll
	formRole.addEventListener('submit', e => {
		e.preventDefault();
		divLoading.style.display = "flex";
		const payload = new FormData(formRole);
		fetch(base_url + '/roles/setRol', {
			method: 'POST',
			body: payload
		}).then(res => res.json()).then(dat => {
			if(dat.status){
				$('#modalFormRol').modal("hide");
				tableRoles.api().ajax.reload();
				swal(fnT('User roles'), fnT(dat.msg), "success");
				formRole.reset();
			}else{
				swal(fnT('Error'),fnT(dat.msg),"error");
			}
			divLoading.style.display = "none";
		});
	});

	$('#tableRoles').DataTable();









});

function openModal(){
	document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
	document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
	document.querySelector('#titleModal').innerHTML = fnT('New role');
	document.querySelector('#btnText').innerHTML = fnT('Save');
	formRole['id'].value = "";
	formRole.reset();	

	$('#modalFormRol').modal('show');
}

function fntEditRol(idrol){
	document.querySelector('#titleModal').innerHTML = fnT('Update role');
	document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
	document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
	document.querySelector('#btnText').innerHTML = fnT('Update');
	divLoading.style.display = "flex";

	//var idrol = this.getAttribute("rl");
	var idrol = idrol;
	var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	var ajaxUrl = base_url+'/roles/getRol/'+idrol;
	request.open("GET",ajaxUrl,true);
	request.send();

	request.onreadystatechange = function(){
		if(request.readyState == 4 && request.status == 200){
			var objData = JSON.parse(request.responseText);

			if(objData.status)
			{
				formRole['id'].value = objData.data.id;
				formRole['name'].value = objData.data.name;
				formRole['description'].value = objData.data.description;
				formRole['status'].value = objData.data.status;
				formRole['level'].value = objData.data.level;
				$('#modalFormRol').modal('show');
			}else{
				swal(fnT('Error'), fnT(objData.msg), "error");
			}
			divLoading.style.display = "none";
		}
	}
}

function fntDelRol(idrol){
	swal({
		title: fnT('Delete role'),
		text: fnT('Do you really want to delete the Role ?'),
		type: "warning",
		showCancelButton: true,
		confirmButtonText: fnT('Yes, delete'),
		cancelButtonText: fnT('No, cancel'),
		closeOnConfirm: false,
		closeOnCancel: true
	}, function(isConfirm){

		if(isConfirm)
		{
			var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
			var ajaxUrl = base_url+'/roles/delRol/';
			var strData = "idrol="+idrol;
			request.open("POST",ajaxUrl,true);
			request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			request.send(strData);
			request.onreadystatechange = function(){

				if(request.readyState == 4 && request.status == 200){
					var objData = JSON.parse(request.responseText);
					if(objData.status)
					{
						swal(fnT('Delete'), fnT(objData.msg), "success");
						tableRoles.api().ajax.reload();
					}else{
						swal(fnT('Attention'), fnT(objData.msg), "error");
					}
				}
			}
		}
	});
}

function fntPermisos(role_id){
	submitPermission = () => sendPermission(role_id);
	const checkBox = document.querySelectorAll('#permission-table input[type=checkbox]');
	checkBox.forEach(item => item.checked = false);

	divLoading.style.display = "flex";
	const url = base_url + '/permisos/getPermisosRol/' + role_id;
	fetch(url).then(res => res.json()).then(dat => {
		dat.forEach(item => {
			document.getElementById(`m${item.module_id}-r`).checked = item.r == 1;
			document.getElementById(`m${item.module_id}-w`).checked = item.w == 1;
			document.getElementById(`m${item.module_id}-u`).checked = item.u == 1;
			document.getElementById(`m${item.module_id}-d`).checked = item.d == 1;
		});
		divLoading.style.display = "none";
		$('#modalPermisos').modal('show');
	});
}

function sendPermission(role_id){
	const checkBox = document.querySelectorAll('#permission-table input[type=checkbox]:checked');
	const payload = new FormData();
	payload.append('role_id', role_id);
	checkBox.forEach(item => {
		if(!payload.has(item.getAttribute('data-module'))){
			payload.append(item.getAttribute('data-module'), item.getAttribute('data-perms'));
		} else{
			let tmpValue = payload.get(item.getAttribute('data-module')) + item.getAttribute('data-perms');
			payload.set(item.getAttribute('data-module'), tmpValue);
		}
	});

	divLoading.style.display = "flex";
	fetch(base_url + '/permisos/setPermisos/', {
		method: 'POST',
		body: payload
	}).then(res => res.json()).then(dat => {
		if(dat.status){
			swal(fnT('Role permits'), fnT(dat.msg), "success");
		}else{
			swal(fnT('Error'), fnT(dat.msg), "error");
		}
		divLoading.style.display = "none";
	});
}