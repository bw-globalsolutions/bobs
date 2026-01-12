const divLoading = document.getElementById('divLoading');
const formAppeal = document.getElementById('formAppeal');
const formAllAppeals = document.getElementById('formAllAppeals');
const formAllAppealsUpd = document.getElementById('formAllAppealsUpd');
let currLocation = {};
let tableAppeals;
let tableAppealsUpd;

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

document.addEventListener('DOMContentLoaded', function(){
	tableOpps = $('#tableOpps').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('Portuguese-Brasil') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/appeals/getAppeals",
			"type"   : "POST",
			"data": function( d ) { 
					d.f_round = $('#fRound').val();
					d.f_status = $('#fStatus').val();
					d.f_store = $('#fStore').val();
			 	},
			"dataSrc":""
		},
		"columns":[
			{"data":"id"},
			{"data":"store", "width": "20%"},
			{"data":"clarifications", "width": "50%"},
			{"data":"options", "className": "text-center"}
		],
		"responsive":"true",
		"autoWidth": false,
		"bDestroy": true,
		"iDisplayLength": 50,
		"order":[[0,"desc"]]
	});

	formAllAppeals.addEventListener('submit', e => {
		e.preventDefault();
		const payload = new FormData(formAllAppeals);
		
		let valid = false;
		e.target.querySelectorAll('textarea').forEach(item => {
			if(item.value != '' && payload.get('idAuditDT') != ''){
				valid = true;
			}
		});

		if(valid){
			divLoading.style.display = "flex";
			fetch(base_url + '/appeals/setAllAppeals', {
				method: 'POST',
				body: payload
			}).then(res => res.json()).then(dat => {
				if(dat.status){
					refreshStores();
					tableOpps.api().ajax.reload();
					swal(fnT('Apelação'), fnT(dat.msg), "success");
				}else{
					swal('Error', fnT(dat.msg), "error");
				}
				$('#modalNewAppeal').modal('hide');
				divLoading.style.display = "none";
			});
		} else{
			swal('Error', fnT('Por favor, complete o formulário'), "error");
		}
	});

	formAllAppealsUpd.addEventListener('submit', e => {
		e.preventDefault();
		divLoading.style.display = "flex";
		const payload = new FormData(formAllAppealsUpd);
		fetch(base_url + '/appeals/setAllAppealsDecisions', {
			method: 'POST',
			body: payload
		}).then(res => res.json()).then(dat => {
			if(dat.status){
				tableOpps.api().ajax.reload();
				swal('Appeal', fnT(dat.msg), "success");
			}else{
				swal('Error', fnT(dat.msg), "error");
			}
			$('#modalUpdAclaracion').modal('hide');
			divLoading.style.display = "none";
		});
	});

});

function recargaDTAppeals(){
	console.log('Recargar DT Appeals');
	tableOpps.api().ajax.reload();
}

function openModalNew(){ //Se utiliza
	listAudits();
	$("#listAudits option:selected").removeAttr("selected");
	$('#listAudits').selectpicker('refresh');
	$('#modalNewAppeal').modal('show');
}

function listAudits(){ //Se utiliza
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+"/appeals/getAudits";
    request.open("GET",ajaxUrl,true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send();
    request.onreadystatechange = function(){
		if(request.readyState == 4 && request.status == 200){
			document.querySelector('#listAudits').innerHTML = request.responseText;
			$('#listAudits').selectpicker('render');
			$('#listAudits').selectpicker('refresh');
			$('#listAudits').selectpicker('val', '');
		}
    }
}



function cargarOportunidades(id_audit){
	divLoading.style.display = "flex";
	var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	var ajaxUrl = base_url+"/appeals/getOpps/";
	request.open("GET",ajaxUrl,true);
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	request.send();
	request.onreadystatechange = function(){
	  if(request.readyState == 4 && request.status == 200){
		document.querySelector('#contOppsAppeal').innerHTML = request.responseText;
		document.querySelector('#idAuditDT').value = id_audit;

		tableAppeals = $('#tableAppeals').dataTable({
			"aProcessing":true,
			"aServerSide":true,
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('Portuguese-Brasil') + ".json"
			},
			"ajax":{
				"url": " "+base_url+"/appeals/getOppsDT?idAudit="+id_audit,
	
				"dataSrc":""
			},
			"columns":[
				{"data":"id"},
				{"data":"opportunity", "width": "60%"},
				{"data":"options"}
			],
			"responsive":"true",
			"autoWidth": false,
			"searching": false,
			"paging": false,
			"info": false,
			"bDestroy": true,
			"iDisplayLength": 50,
			"order":[[0,"asc"]]
		});
	  }
	  divLoading.style.display = "none";
	  return false;
	}
}

function cargarOportunidadesAll(id_audit){
	document.querySelector('#idAuditDT').value = id_audit;
	tableAppeals = $('#tableAppeals').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('Portuguese-Brasil') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/appeals/getOppsDT?idAudit="+id_audit,

			"dataSrc":""
		},
		"columns":[
			{"data":"id"},
			{"data":"opportunity", "width": "60%"},
			{"data":"options"}
		],
		"responsive":"true",
		"autoWidth": false,
		"searching": false,
		"paging": false,
        "info": false,
		"bDestroy": true,
		"iDisplayLength": 50,
		"order":[[0,"asc"]]
	});

	const tmp = document.querySelector('#listAudits option:checked');
	currLocation.lname = tmp.getAttribute('data-lnumber');
	currLocation.lid = tmp.getAttribute('data-lid');
}

function addAppeal(idOpp,audit){
    $('#modalNewAppeal').modal('hide');
	$('#modalFormAppeal').modal('show');
    document.querySelector('#opp_id').value = idOpp;
	document.querySelector('#modal_audit_id').value = audit;
}

function openModalUpd(id_appeal) {
	document.querySelector('#id_appeal_upd').value = id_appeal;
	tableAppealsUpd = $('#tableAppealsUpd').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('Portuguese-Brasil') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/appeals/getAppealsUpd?idAppeal="+id_appeal,

			"dataSrc":""
		},
		"columns":[
			{"data":"id"},
			{"data":"clarification", "width": "60%"},
			{"data":"decision"}
		],
		"responsive":"true",
		"autoWidth": false,
		"searching": false,
		"paging": false,
        "info": false,
		"bDestroy": true,
		"iDisplayLength": 50,
		"order":[[0,"asc"]]
	});
	$('#modalUpdAclaracion').modal('show');
}

function uploadPic(element,opp_id){
	//$('#divLoading').css('display', 'flex');
	divLoading.style.display = "flex";
	console.log (opp_id);
	// divLoading.style.display = "none";
	for (let i = 0; i < element.files.length; i++) {
		const file = element.files[i];
		console.log (file.name);
		fetch('https://ws.bw-globalsolutions.com/WSAAA/receiveFile.php?token=x', {
			method: 'POST',
			body: file
		}).then(res => res.json()).then(dat => {
			if(dat.Message == "SUCCESS"){
				console.log (dat.Info);
				//$('#divLoading').css('display', 'none');
				const idImg = Date.now();
				newFile = `<div class="mr-3 mb-3" id="img${idImg}">
								<a href="${dat.Info.location}" target="_blank">
								<img style="height:100px; width:100px" class="rounded shadow-sm of-cover cr-pointer" src="${dat.Info.location}"> </a><br>
								<span class ="badge badge-pill badge-danger mt-1 cr-pointer" onclick="dropImg(${idImg})">
									<i class="fa fa-trash"></i>&nbsp; ${fnT('Excluir')}
								</span>
								<input type="hidden" name="urlFile[${opp_id}][]" value="${dat.Info.location}"><br>
								<input type="hidden" name="typeFile[${opp_id}][]" value="${dat.Info.mimetype}"><br>
							</div>`;
				$('#panel-pic'+opp_id).append(newFile);
			}else console.error(dat);
		});
	}
	$('#divLoading').css('display', 'none');
}

function dropImg(idImg){
    console.log ("Borrar "+idImg);
    $('#img'+idImg).remove();
}

function refreshStores(){
	if(document.querySelectorAll(`#fStore option[value="${currLocation.lid}"]`).length == 0){
		$('#fStore').append(`<option value="${currLocation.lid}" selected>${currLocation.lname}</option>`);
		$('#fStore').selectpicker('refresh');
	}
}

$("#modalNewAppeal").on("hidden.bs.modal", function () {
	document.querySelector('#idAuditDT').value = '';
	tableAppeals.fnClearTable();
});