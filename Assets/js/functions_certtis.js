const divLoading = document.getElementById('divLoading');
const idAudit = $('#id_auditoria').val();
document.querySelector('#audit_email').value = idAudit;

console.log('JS CERTTIS');
document.addEventListener('DOMContentLoaded', function(){
    
//CERTTIS DATA TABLE
tableCerttis = $('#tableCerttis').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/certtis/getOppsPlanCerttis?idAudit="+idAudit,
			"dataSrc":""
		},
		"columns":[
            {"data":"num"},
			{"data":"opportunity", "width": "60%"},
			{"data":"status", "className": "text-center"},
            {"data": null,"render": function (dato, type, row, meta) {
                  
                data = `<button class="btn btn-secondary btnCloseAction"  data-toggle='modal'  data-target='#modalCerttis' 
                            onclick = 'obtenerdatosCerttis(`+row['audit_id']+`,`+row['id_audit_opp']+`);
                                        lineaTiempoCerttis(`+row['audit_id']+`,`+row['id_audit_opp']+`)'>
                            <i class="fa fa-plus-circle"></i>Añadir certtis
                        </button>`
                return  data;

                }
            }
		],
		"responsive":"true",
        "autoWidth": false,
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"asc"]]
	});

//CERTTIS INSERT
	formCerttis.addEventListener('submit', e => {
        //var intIdOpp = document.querySelector('#opp_id').value;
		e.preventDefault();
		divLoading.style.display = "flex";
		const payload = new FormData(formCerttis);
		fetch(base_url + '/certtis/setCerttis', {
			method: 'POST',
			body: payload
		}).then(res => res.json()).then(dat => {
			if(dat.status){
				
				
				swal(fnT('Certtis'), fnT(dat.msg), "success");
				let id_audit_opp = document.querySelector('#id_audit_opp_certtis').value;
				let audit_id = document.querySelector('#audit_id_certtis').value;
				document.querySelector('#audit_id_certtis').value;
				$("#comentarioCerttis").val('');
				lineaTiempoCerttis(audit_id,id_audit_opp);
				tableCerttis.api().ajax.reload();
				tableCerttisEmail.api().ajax.reload();

				//formCerttis.reset();
			}else{
				swal(fnT('Error'), fnT(dat.msg), "error");
			}
			divLoading.style.display = "none";
		});
	});



	tableCerttisEmail = $('#tableCerttisEmail').dataTable({
		"aProcessing":true,
		"aServerSide":true,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/" + fnT('English') + ".json"
		},
		"ajax":{
			"url": " "+base_url+"/certtis/selectCerttis?idAudit="+idAudit,
			"dataSrc":""
		},
		"columns":[
            {"data":"nombre_certtis"},
			{"data":"comentario_certtis", "width": "60%"},
			{"data":"question_prefix", "className": "text-center"},
			{"data":"auditor_answer", "className": "text-center"},
			{"data":"auditor_comment", "className": "text-center"},
           
		],
		"responsive":"true",
        "autoWidth": false,
		"bDestroy": true,
		"iDisplayLength": 10,
		"order":[[0,"asc"]]
	});

	
	formCerttisEmail.addEventListener('submit', e => {
        //var intIdOpp = document.querySelector('#opp_id').value;
		
		e.preventDefault();
		divLoading.style.display = "flex";
		const payload = new FormData(formCerttisEmail);
		fetch(base_url + '/certtis/sendCerttis', {
			method: 'POST',
			body: payload
		}).then(res => res.json()).then(dat => {
			if(dat.status){
				
				
				swal(fnT('Certtis'), fnT(dat.msg), "success");
	
				tableCerttisEmail.api().ajax.reload();

				//formCerttis.reset();
			}else{
				swal(fnT('Error'), fnT(dat.msg), "error");
			}
			divLoading.style.display = "none";
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


//CERTTIS OBTENER DATOS
function obtenerdatosCerttis(audit_id,id_audit_opp) {
document.querySelector('#id_audit_opp_certtis').value = id_audit_opp;
document.querySelector('#audit_id_certtis').value = audit_id;
}


//CERTTIS LINEA TIEMPO

function lineaTiempoCerttis(audit_id,id_audit_opp){
   
    $.ajax({
           type: "POST",
           url:  " "+base_url+"/certtis/selectLineaCertis",
		   data: {id_audit_opp},
           dataType: "json",
    success: function(data){
	    
        $("#lineaTiempo").empty();
        $.each(data,function(key, registro) {

                let nombre_usuario     = registro.nombre_usuario;              
                let nombre_certtis     = registro.nombre_certtis;              
                let comentario_certtis = registro.comentario_certtis;                  
                let color              = registro.color.replaceAll('"', '');    
                let icono              = registro.icono.replaceAll('"', '');    
                let fecha_certtis      = registro.fecha_certtis;            
                let hora_certtis       = registro.hora_certtis;  

                $("#lineaTiempo").append(`
                <div class="time-label">
                	<span style="background:  ${color}; color:white;"> ${fecha_certtis}</span>
                </div>
                <div>
                	<i class="${icono}" style="background: ${color}; color:white; padding:5px; border-radius:50%;"></i>
                	<div class="timeline-item">
                		<span class="time"><i class="fas fa-clock"></i>${hora_certtis}</span>
                		<h3 class="timeline-header">
                			<a>${nombre_usuario}</a><br>
                			<small><b>Certtis: </b>${nombre_certtis}</small>
                		</h3>
                		<div class="timeline-body">
                		  ${comentario_certtis}
                		</div>
                	</div>
                </div>`);
        });
                },
                   error: function(data) {
                   console.log(data);
            }
        });

}


function sendEmailCerttis(){
	alert('test');
	
}



