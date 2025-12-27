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
			{"data": null,"render": function (dato, type, row, meta) {
                  
                data = `<a class="btn btn-danger "  
                            onclick = 'deleteCerttis(`+row['id_certtis']+`);'>
                            <i class="fa fa-minus-circle" aria-hidden="true"></i>
                        </a>`
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


//CERTTIS OBTENER DATOS
function obtenerdatosCerttis(audit_id,id_audit_opp) {
document.querySelector('#id_audit_opp_certtis').value = id_audit_opp;
document.querySelector('#audit_id_certtis').value = audit_id;
}

function deleteCerttis(id_certtis) {
	alert(id_certtis);


	$.ajax({
		type: "POST",
           url:  " "+base_url+"/certtis/deleteCerttis",
		   data: {id_certtis},
		success: function(response) {
		
			console.log('Éxito:', response);
			swal(fnT('Exito'), 'Eliminado correctamente', "success");
			tableCerttisEmail.api().ajax.reload();
			
			 
		
		},
		error: function(xhr, status, error) {
			// Manejar el error
			console.error('Error:', error);
			alert('Hubo un problema al enviar el formulario.');
		}
	});
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
                let color              = registro.color;    
                let icono              = registro.icono;    
                let fecha_certtis      = registro.fecha_certtis;            
                let hora_certtis       = registro.hora_certtis;  

                $("#lineaTiempo").append(`
                <div class="time-label">
                	<span style="background:  `+color+`; color:white;"> `+fecha_certtis+`</span>
                </div>
                <div>
                	<i class="`+icono+`" style="background: `+color+`; color:white;"></i>
                	<div class="timeline-item">
                		<span class="time"><i class="fas fa-clock"></i>`+hora_certtis+`</span>
                		<h3 class="timeline-header">
                			<a>`+nombre_usuario+`</a><br>
                			<small><b>Certtis: </b>`+nombre_certtis+`</small>
                		</h3>
                		<div class="timeline-body">
                		  `+comentario_certtis+`
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
	alert('hola');
	
}



