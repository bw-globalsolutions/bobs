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
					//d.f_franchise = $('#f_franchise').val();
					d.f_country = $('#f_country').val();
					d.f_from = $('#f_from').val();
					d.f_to = $('#f_to').val();
			 	},
			"dataSrc":""
		},
		"columns":[
			{"data":"id"},
			{"data":"visit"},
			{"data":"date"},
			{"data":"action"},
			{"data":"send"}
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

function fntSendNotificationGlobal(){

	payload = new FormData();
	//payload.append('franchises', $('#f_franchise').val());
	payload.append('countrys', $('#f_country').val());
	payload.append('from', $('#f_from').val());
	payload.append('to', $('#f_to').val());
	var ajaxUrl = base_url+'/announced_Visits/sendNotificationGlobal';
	var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	request.open("POST",ajaxUrl,true);
	request.send(payload);

	request.onreadystatechange = function(){
		if(request.readyState == 4 && request.status == 200){
			var objData = JSON.parse(request.responseText);
			swal(fnT('Announced visit'), fnT(objData.msg), "success");
			tableAnnouncedVisits.api().ajax.reload();
		}
	}

}

function descargable(){
	let from = $('#f_from').val();
	let to = $('#f_to').val();
	let countrys = $('#f_country').val().join(',');
	window.open('Announced_Visits/downloadAnnounceds?countrys='+countrys+'&from='+from+'&to='+to, '_blank');
}

function fntSendNotification(idVisit, location_id){

	var ajaxUrl = base_url+'/announced_Visits/sendNotification?id='+idVisit+'&locationId='+location_id;
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

function editHour(event, id){
	let hour = event.target.value;
	document.querySelector('.strHour'+id).innerHTML=hour;
	hour = document.querySelector('.strFecha'+id).innerHTML+' '+hour+':00';
	console.log(id);
	var strData = "id="+id+"&hour="+hour;
	var ajaxUrl = base_url+'/announced_Visits/updateHour';
	var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	request.open("POST",ajaxUrl,true);
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	request.send(strData);

	request.onreadystatechange = function(){
		if(request.readyState == 4 && request.status == 200){
			console.log(request.responseText);
			if(request.responseText=='ok'){
				swal(fnT('Announced visit'), fnT('Time saved successfully'), "success");
			}
		}
	}
	
}