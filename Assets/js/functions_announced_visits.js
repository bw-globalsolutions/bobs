const divLoading = document.getElementById('divLoading');

let tableAnnouncedVisits;
let rowTable = "";

document.addEventListener('DOMContentLoaded', function(){
	tableAnnouncedVisits = $('#tableAnnouncedVisits').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/announced_Visits/getVisits",
			"type"   : "POST",
			"data": function( d ) { 
					d.f_franchise = $('#f_franchise').val();
			 	},
			"dataSrc":""
		},
		"columns":[
			{"data":"id"},
			{"data":"visit"},
			{"data":"date"}
		],
		// "dom": "lfBrtip",
        // "buttons": [
        // 	//{ 	"extend": "copyHtml5",
        // 	//	"text": "<i class='fa fa-files-o'></i> " + fnT('Copy'),
        // 	//	"titleAttr": fnT('Copy'),
        // 	//	"className": "btn btn-secondary" },
		// 	{	"extend": "excelHtml5",
        // 		"text": "<i class='fa fa-file-excel-o'></i> Excel",
        // 		"titleAttr": fnT('Export to') + " Excel",
        // 		"className": "btn btn-success" },
		// 	{	"extend": "pdfHtml5",
        // 		"text": "<i class='fa fa-file-pdf-o'></i> PDF",
        // 		"titleAttr": fnT('Export to') + "  PDF",
        // 		"className": "btn btn-danger" }
		// 	//{	"extend": "csvHtml5",
        // 	//	"text": "<i class='fa fa-file-text-o'></i> CSV",
        // 	//	"titleAttr": fnT('Export to') + "  CSV",
        // 	//	"className": "btn btn-info" }
        // ],
		"responsive":true,
		"bDestroy": true,
		"iDisplayLength": 25,
		"order":[[0,"asc"]]
	});
	//Funciones para cargar selects inicialmente una vez cargado el DOM
});

function fntSendNotificationGlobal(){

	payload = new FormData();
	payload.append('franchises', $('#f_franchise').val());
	var ajaxUrl = base_url+'/announced_Visits/sendNotificationGlobal';
	var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	request.open("POST",ajaxUrl,true);
	request.send(payload);

	request.onreadystatechange = function(){
		if(request.readyState == 4 && request.status == 200){
			var objData = JSON.parse(request.responseText);
			if(objData.status){
				swal(fnT('Announced visit'), fnT(objData.msg), "success");
				tableAnnouncedVisits.api().ajax.reload();
			} else {
				swal(fnT('Announced visit'), fnT(objData.msg), "error");
			}
		}
	}

}

function fntSendNotification(idVisit){

	var ajaxUrl = base_url+'/announced_Visits/sendNotification?id='+idVisit;
	var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	request.open("GET",ajaxUrl,true);
	request.send();

	request.onreadystatechange = function(){
		if(request.readyState == 4 && request.status == 200){
			var objData = JSON.parse(request.responseText);
			swal(fnT('Announced visit'), fnT(objData.msg), "success");
			tableAnnouncedVisits.api().ajax.reload();
		}
	}
}

function fntSendNotificationGeneral(month,idCountry){
	console.log (month);
	console.log (idCountry);
	var ajaxUrl = base_url+'/announced_Visits/sendNotificationGenerel?month='+month+'&country='+idCountry;
	var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	request.open("GET",ajaxUrl,true);
	request.send();

	request.onreadystatechange = function(){
		if(request.readyState == 4 && request.status == 200){
			var objData = JSON.parse(request.responseText);
			swal(fnT('Announced visit'), fnT(objData.msg), "success");
			tableAnnouncedVisits.api().ajax.reload();
		}
	}
}

function reloadTable(){
	tableAnnouncedVisits.api().ajax.reload();
}