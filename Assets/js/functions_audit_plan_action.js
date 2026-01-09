const formPlanAction = document.getElementById('formPlanAction');
const divLoading = document.getElementById('divLoading');
const idAudit = $('#id_auditoria').val();
let mostrarOpcionesPlan = false;



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

let tableAuditPlanActions;
let rowTable = "";
console.log('JS ACTIONPLAN');
document.addEventListener('DOMContentLoaded', function(){
    
	tableAuditPlanActions = $('#tableAuditPlanActions').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/actionPlan/getOpps?idAudit="+idAudit,

			"dataSrc":""
		},
		"columns":[
            {"data":"num"},
			{"data":"opportunity", "width": "60%"},
			{"data":"status", "className": "text-center"},
			{"data": null,"render": function (dato, type, row, meta) {

                if(row['item_action_plan'] == null || row['item_action_plan'] == ''){
					data = ` `;
				}else{
					
					data = `<div class="mr-2 mb-2" id="img`+row['item_action_plan']+`">
                    			<a href="`+row['item_action_plan']+`" target="_blank">
                    			    <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="`+row['item_action_plan']+`">
                    			</a><br>
                			</div>`;
				}
		
                return  data;

                }
            },
			{"data": null,"render": function (dato, type, row, meta) {

                
					data = row['options'];
			
		
                return  data;

            }
            },
			
            //{"data":"diferencia_en_horas"},

			//{"data":"options"}
		],
		"responsive":"true",
        "autoWidth": false,
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"asc"]]
	});

    formPlanAction.addEventListener('submit', e => {
        e.preventDefault();
        if(mostrarOpcionesPlan){
            valorChecks = '';
            document.querySelectorAll('.chk').forEach(e=>{
                if(e.checked){
                    valorChecks+=e.value+',';
                }
            });
            if(valorChecks!=''){
                document.querySelector('.errorBoxC').style.display='none';
                valorChecks = valorChecks.slice(0, -1);
                document.getElementById('checks').value=valorChecks;
            }else{
                document.querySelector('.errorBoxC').style.display='block';
                return;
            }
        }else{
            if(document.getElementById('action').value==''){
                document.querySelector('.errorBoxN').style.display='block';
                return;
            }
        }
        mostrarOpcionesPlan=false;
        //var intIdOpp = document.querySelector('#opp_id').value;
		divLoading.style.display = "flex";
		const payload = new FormData(formPlanAction);
		fetch(base_url + '/actionPlan/setAction', {
			method: 'POST',
			body: payload
		}).then(res => res.json()).then(dat => {
			if(dat.status){
				$('#modalFormAction').modal("hide");
				tableAuditPlanActions.api().ajax.reload();
				swal(fnT('Action Plan'), fnT(dat.msg), "success");
				refreshStatics();
				formPlanAction.reset();
			}else{
				swal(fnT('Error'), fnT(dat.msg), "error");
			}
			divLoading.style.display = "none";
		});
	});

});

function fntAddAction(idOpp,idAudit){
    console.log(idOpp);
	console.log(idAudit);
    let prefix = document.querySelector('.prefix'+idOpp).innerHTML;
    document.querySelector('#opp_id').value = idOpp;
	document.querySelector('#opp_audit_id').value = idAudit;
    fetch(base_url + '/actionPlan/getPlanOptions?id='+idAudit+'&prefix='+prefix, {
        method: 'POST'
    }).then(res => res.json()).then(dat => {
        console.log(dat)
        let lan = dat['lan'];
        if(dat.status){
            if(dat.rs.length>0){
                mostrarOpcionesPlan=true;
                document.querySelector('.formCheck').innerHTML='';
                dat.rs.forEach(e=>{
                    document.querySelector('.formCheck').innerHTML+=`<div style="display:flex; gap:10px;"><input type="checkbox" id="${e['id']}" class="chk" value="${e['id']}">
                            <label for="${e['id']}"> ${(lan=='esp'?e['op_esp']:e['op'])}</label></div><br>`;
                });
            }else{
                mostrarOpcionesPlan=false;
            }
            $('#modalFormAction').modal('show');
            if(mostrarOpcionesPlan){
                document.querySelector('.formCheck').style.display='block';
            }else{
                document.querySelector('.formCheck').style.display='none';
            }
        }else{
            swal(fnT('Error'), fnT(dat.msg), "error");
        }
    });
}

function fntChangeStatusAction(idAction,idOpp){
    console.log(idAction);
    swal({
        title: fnT('Alert'),
        text: fnT('What decision should you make?'),
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#0B9C26',
        confirmButtonText: fnT('Approve action'),
        cancelButtonText: fnT('Decline action'),
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm){
    if (isConfirm){
        divLoading.style.display = "flex";
        fetch(base_url + '/actionPlan/updateStatus?id='+idAction+'&idOpp='+idOpp+'&status=Finished', {
			method: 'POST'
		}).then(res => res.json()).then(dat => {
			if(dat.status){
				$('#modalFormAction').modal("hide");
				tableAuditPlanActions.api().ajax.reload();
				swal(fnT('Action Plan'), fnT(dat.msg), "success");
				formPlanAction.reset();
                refreshStatics();
			}else{
				swal(fnT('Error'), fnT(dat.msg), "error");
			}
			divLoading.style.display = "none";
		});
        swal("Action Approved", "The action has been approved!", "success");
    } else {
        divLoading.style.display = "flex";
        fetch(base_url + '/actionPlan/updateStatus?id='+idAction+'&idOpp='+idOpp+'&status=Rejected', {
			method: 'POST'
		}).then(res => res.json()).then(dat => {
			if(dat.status){
				$('#modalFormAction').modal("hide");
				tableAuditPlanActions.api().ajax.reload();
				swal(fnT('Action Plan'), fnT(dat.msg), "success");
				formPlanAction.reset();
			}else{
				swal(fnT('Error'), fnT(dat.msg), "error");
			}
			divLoading.style.display = "none";
		});
        swal("Action Rejected", "The action has been rejected!", "error");
    }
    });
}

function fntCloseAction(idAction,idOpp){
    //console.log(idAction);
    swal({
        title: fnT('Alert'),
        text: fnT('What decision should you make?'),
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#0B9C26',
        confirmButtonText: fnT('Finish action'),
        cancelButtonText: fnT('Cancel'),
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm){
        if (isConfirm){
            divLoading.style.display = "flex";
            fetch(base_url + '/actionPlan/updateStatus?id='+idAction+'&idOpp='+idOpp+'&status=Finished', {
                method: 'POST'
            }).then(res => res.json()).then(dat => {
                if(dat.status){
                    $('#modalFormAction').modal("hide");
                    tableAuditPlanActions.api().ajax.reload();
                    swal(fnT('Action Plan'), fnT(dat.msg), "success");
                    formPlanAction.reset();
                }else{
                    swal(fnT('Error'), fnT(dat.msg), "error");
                }
                divLoading.style.display = "none";
            });
            //swal("Action Finished", "The action has been finished!", "success");
            refreshStatics();
        }
    });
	refreshStatics();
}

function refreshStatics(){
    //$("#statics").empty();
    $.ajax({
        method: 'POST',
        data: { "id_audit": idAudit },
        url: base_url + '/actionPlan/refreshStatistics?id='+idAudit,
        beforeSend: function(){
            divLoading.style.display = "flex";
        },
        success: function(response){
			$("#progressActionPlanDiv").html(response);
            console.log(response);
            //$("#statics").append(response);
            divLoading.style.display = "none";
        },
        contentType: "application/x-www-form-urlencoded;charset=iso-8859-1"
    });
}

function uploadPic(element){
	$('#evidencia').val('');
    const file = element.files[0];
    if(file.type.includes('image/')){
        $('#divLoading').css('display', 'flex');
        fetch('https://ws.bw-globalsolutions.com/WSAAA/receivePic.php?token=x', {
            method: 'POST',
            body: file
        }).then(res => res.json()).then(dat => {
            if(dat.Message == "SUCCESS"){
                const idImg = Date.now();
                $('#divLoading').css('display', 'none');
                $('#evidencia').val(dat.Info.location);
                $('#auditor-files').append(`<div class="mr-2 mb-2" id="img${idImg}">
                    <a href="${dat.Info.location}" target="_blank">
                        <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="${dat.Info.location}">
                    </a><br>
                    <span class ="badge badge-pill badge-danger mt-1 cr-pointer" onclick="dropImg(${idImg}, '${dat.Info.location}')">
                        <i class="fa fa-trash"></i>&nbsp; ${fnT('Delete')}
                    </span>
                </div>`);


                stackImg.push(dat.Info.location);
            }else console.error(dat);
        });
    }else{
        swal({
            title: fnT('Error'),
            text: fnT('Format not supported'),
            type: 'error'
        });
    }
    element.value = '';
}


/*
function openOpportunity(picklist_id, audit_id){
    $('#divLoading').css('display', 'flex');
    payload.append('picklist_id', picklist_id);
    payload.append('audit_id', audit_id);
	
    fetch( base_url + '/audits/getAnswers', {
        method: 'POST',
        body: payload
    }).then(res => res.json()).then(dat => {
        if(activateEdit){
            $('.sw-he').toggleClass('d-none');
            activateEdit = false;
        }

        $('#list-answers').html(dat.answers.reduce((acc, cur) => {
            acc += `<li class="list-group-item list-group-item-action d-flex justify-content-between">
                <span>${cur.text}</span>
                <div class="toggle-flip success-danger ml-3">
                    <label class="m-0"><input value="${cur.key}" type="checkbox" ${cur.opp?'checked':''} disabled>
                        <span class="flip-indecator" data-toggle-on="${fnT('No')}" data-toggle-off="${fnT('Yes')}"></span>
                    </label>
                </div>
            </li>`;
            return acc;
        },''));
        
        $('#opp_comment').val(dat.comment).prop('disabled', true);
    
        $('#auditor-files').html(dat.files.reduce((acc, cur) => { 
            acc += `<div class="mr-2 mb-2">
                <a href="${cur.url}" target="_blank">
                    <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="${cur.url}">
                </a><br>
                <span class ="badge badge-pill badge-danger mt-1 cr-pointer sw-he d-none" onclick="sendRemoveImg(${cur.id})">
                    <i class="fa fa-trash"></i>&nbsp; Eliminar
                </span>
            </div>`;
            return acc;
        }, ''));

        $('#divLoading').css('display', 'none');
        $('#modalViewAnwers').modal('show');
    });
}

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
				formUser['id'].value = objData.data.id;
				formUser['name'].value = objData.data.name;
				formUser['email'].value = objData.data.email;

				formUser['profile'].value = objData.data.profile;
				$('#user_profile').selectpicker('refresh');
				
				formUser['status'].value = objData.data.status;
				$('#user_status').selectpicker('refresh');

				$("#user_country option:selected").removeAttr("selected");
				objData.data.country_id.split(',').forEach(item => 
					$(`#user_country option[value='${item}']`).attr("selected", true));
				$('#user_country').selectpicker('refresh');
				
				$("#user_brand option:selected").removeAttr("selected");
				objData.data.brand_id.split(',').forEach(item => 
					$(`#user_brand option[value='${item}']`).attr("selected", true));
				$('#user_brand').selectpicker('refresh');

				formUser['role'].value = objData.data.role_id;
				$('#user_role').selectpicker('refresh');

				formUser['language'].value = objData.data.default_language;
				$('#user_language').selectpicker('refresh');
			}

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
*/

