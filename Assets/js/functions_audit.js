function filterSection(number){
    $('.section-items').removeClass('selected');
    $(`#section${number}`).addClass('selected');

    $('.question-item').filter(function(){
        $(this).toggle($(this).data('snumber').toString() == number);
    });
}

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

function openOpportunity(picklist_id, qprefix, snumber, isAutoFail=0){
    $('#divLoading').css('display', 'flex');
    
    const payload = new FormData();
    payload.append('picklist_id', picklist_id);
    payload.append('audit_id', audit_id);
    payload.append('isAutoFail', isAutoFail);
	
    fetch( base_url + '/audits/getAnswers', {
        method: 'POST',
        body: payload
    }).then(res => res.json()).then(dat => {
       $('#list-answers').html(dat.answers.reduce((acc, cur) => {
            acc += `<li class="list-group-item list-group-item-action d-flex justify-content-between">
                <span>${cur.text}</span>
                <div class="toggle-flip success-danger ml-3">
                    <label class="m-0"><input value="${cur.text}" type="checkbox" ${cur.opp?'checked':''} ${editRestricted? '' : 'disabled'}>
                        <span class="flip-indecator" data-toggle-on="${fnT('Não')}" data-toggle-off="${fnT('Sim')}"></span>
                    </label>
                </div>
            </li>`;
            return acc;
        },''));
        
        $('#opp_comment').val(dat.comment).prop('disabled', !editRestricted);
    
        $('#auditor-files').html(dat.files.reduce((acc, cur) => { 
            acc += `<div class="mr-2 mb-2" id="img${cur.id}">
                <a href="${cur.url}" target="_blank">
                    <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="${cur.url}">
                </a><br>
                <span class ="badge badge-pill badge-danger mt-1 cr-pointer sw-he" onclick="removeImg(${cur.id})">
                    <i class="fa fa-trash"></i>&nbsp; ${fnT('Excluir')}
                </span>
            </div>`;
            return acc;
        }, ''));

        stackImg = [];
        currQPrefix = qprefix;
        currSNumber = snumber;
        $('#checklist_item_id').val(picklist_id);
        $('#opp_id').val(dat.opp_id);
        $('#id_audit_opp_certtis').val(dat.opp_id);
        $('#btn-remove-opp').css('display', dat.opp_id!=''? 'block' : 'none');
        $('#btn-certtis').css('display', dat.opp_id!=''? 'block' : 'none');
        $('#divLoading').css('display', 'none');
        $('#modalViewAnwers').modal('show');
    });
}

function uploadPic(element){
    console.log(element.files.length);
    for(let i=0; i<element.files.length; i++){
        const file = element.files[i];
        console.log(file);
        if(file.type.includes('image/')){
            $('#divLoading').css('display', 'flex');
            fetch('https://ws.bw-globalsolutions.com/WSAAA/receivePic.php?token=x', {
                method: 'POST',
                body: file
            }).then(res => res.json()).then(dat => {
                if(dat.Message == "SUCCESS"){
                    const idImg = Date.now();
                    $('#divLoading').css('display', 'none');
                    $('#auditor-files').append(`<div class="mr-2 mb-2" id="img${idImg}">
                        <a href="${dat.Info.location}" target="_blank">
                            <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="${dat.Info.location}">
                        </a><br>
                        <span class ="badge badge-pill badge-danger mt-1 cr-pointer" onclick="dropImg(${idImg}, '${dat.Info.location}')">
                            <i class="fa fa-trash"></i>&nbsp; ${fnT('Excluir')}
                        </span>
                    </div>`);
                    stackImg.push(dat.Info.location);
                }else console.error(dat);
            });
        }else{
            swal({
                title: fnT('Erro'),
                text: fnT('Formato não suportado'),
                type: 'error'
            });
        }
    }
    element.value = '';
    
}

function sendAnswers(element){
    let arrValues= [];
    const checkAnswers = document.querySelectorAll('#list-answers input[type="checkbox"]:checked');
    checkAnswers.forEach(item => arrValues.push(item.value));
    if(arrValues.length){
        $('#divLoading').css('display', 'flex');
		const payload = new FormData(element);
		payload.append('opp_answers', arrValues.join('|'));
        
        fetch(base_url + '/audit_Opp/changeOpp', {
			method: 'POST',
			body: payload
		}).then(res => res.json()).then(dat => {
            console.log(dat);
            if(dat.status == 1){
                $('#picklist'+$('#checklist_item_id').val()).removeClass('fa-check text-success').addClass('fa-times text-danger');
                $('#points'+currQPrefix).removeClass('btn-success').addClass('btn-danger');
                $('#section'+currSNumber).removeClass('success').addClass('danger');
                $('#divLoading').css('display', 'none');
                $('#modalViewAnwers').modal('hide');
                
                if(dat.score)
                    refreshScore(dat.score);
                if(stackImg.length)
                    sendImg(dat.opp_id);

            }else console.error(dat);
            if(dat.refresh == 1) window.location.reload();
		});
    }else{
        swal({
            title: fnT('Erro'),
            text: fnT('Para marcar uma oportunidade, é necessário selecionar um motivo'),
            type: 'error'
        }); 
    }
}

function sendImg(opp_id){
    const payload = new FormData();
    payload.append('opp_id', opp_id);
    payload.append('audit_id', audit_id);
    payload.append('stack_img', stackImg.join('|'));
    
    fetch(base_url + '/audit_File/insertOppFiles', {
        method: 'POST',
        body: payload
    }).then(res => res.json()).then(dat => console.info(dat));
}

function removeOpp(){
    swal({
        title: fnT('Alerta'),
        text: fnT('Tem certeza de que deseja eliminar esta oportunidade?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Sim'),
        cancelButtonText: fnT('Não')
    }, function(isConfirm){
        if(isConfirm){
            $('#divLoading').css('display', 'flex');
            const payload = new FormData();
            payload.append('opp_id', $('#opp_id').val());
            payload.append('section_number', currSNumber);
            payload.append('audit_id', audit_id);
            fetch(base_url + '/audit_Opp/removeOpp', {
                method: 'POST',
                body: payload
            }).then(res => res.json()).then(dat => {
                if(dat.status == 1){
                    $('#picklist'+$('#checklist_item_id').val()).removeClass("fa-times text-danger").addClass('fa-check text-success');
                    console.log(dat.score);
                    refreshScore(dat.score);
                    if(dat.questions_opp.length){
                        if(!dat.questions_opp.includes(currQPrefix)){
                            $('#points'+currQPrefix).removeClass('btn-danger').addClass('btn-success')
                        }
                    }else{
                        $('#points'+currQPrefix).removeClass('btn-danger').addClass('btn-success');
                        $('#section'+currSNumber).removeClass('danger').addClass('success');
                    }
                    $('#divLoading').css('display', 'none');
                    $('#modalViewAnwers').modal('hide');
                }else console.error(dat);
            });            
        }
    });
}

function refreshScore(score){
    $('#score-critics').html(score.FootSafety);
    $('#score-nocritics').html(score.OperationsE);
    //$('#score-green').html(score.Verdes);
    $('#score-yellow').html(score.OverallScore);
    if(document.getElementById('score-red')){
        $('#score-red').html(score.Letra);
        document.getElementById('score-red').parentNode.style.backgroundColor=score.color;
    }
    $('#score-autofail').html(score.AutoFail);
    // $('#score-majors').html(score.Mayores);
    // $('#score-minors').html(score.Menores);
}

function removeImg(file_id){
    swal({
        title: fnT('Alerta'),
        text: fnT('Tem certeza de que deseja excluir esta imagem?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Sim'),
        cancelButtonText: fnT('Não')
    }, function(isConfirm){
        if(isConfirm){
            $('#divLoading').css('display', 'flex');
            const payload = new FormData();
            payload.append('file_id', file_id);
            fetch(base_url + '/audit_File/removeOppFile', {
                method: 'POST',
                body: payload
            }).then(res => res.json()).then(dat => {
                if(dat.status == 1){
                    $('#img' + file_id).remove();
                    $('#divLoading').css('display', 'none');
                }
            });            
        }
    });
}

function dropImg(idImg, url){
    stackImg = stackImg.filter(item => item != url);
    $('#img'+idImg).remove();
}

function sendInsertNA(snumber, qprefix, points){
    if(editRestricted){
        swal({
            title: fnT('Alerta'),
            text: fnT('Tem certeza de que deseja remover esta pergunta?'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: fnT('Sim'),
            cancelButtonText: fnT('Não')
        }, function(isConfirm){
            if(isConfirm){
                $('#divLoading').css('display', 'flex');
                const payload = new FormData();
                payload.append('audit_id', audit_id);
                payload.append('section_number', snumber);
                payload.append('question_prefix', qprefix);
                payload.append('points', points);
                fetch(base_url + '/audit_Opp/insertNA', {
                    method: 'POST',
                    body: payload
                }).then(res => res.json()).then(dat => {
                    $('#divLoading').css('display', 'none');
                    if(dat.status != 1){
                        swal({
                            title: fnT('Erro'),
                            text: fnT('Ocorreu um erro'),
                            type: 'error'
                        });
                    }else{
                        console.log('success');
                        $('#points'+qprefix).removeClass('btn-danger btn-success').addClass('btn-dark');
                        $(`#cpicklist${qprefix} .fa-times`).removeClass('text-danger fa-times').addClass('text-success fa-check')
                        $('#cpicklist' + qprefix).collapse('hide');
                        $('#bpicklist' + qprefix).data('na', 1);
                        refreshScore(dat.score);

                        if(!dat.questions_opp.length){
                            $('#section'+snumber).removeClass('danger').addClass('success');
                        }
                    }
                });
            }
        });
    } else{
        swal({
            title: fnT('Erro'),
            text: fnT('Não é possível editar uma auditoria finalizada'),
            type: 'error'
        });
    }
}

function toggleQuestion(qprefix){
    if(!$('#bpicklist' + qprefix).data('na')){
        $('#cpicklist' + qprefix).collapse('toggle');
    }else{
        sendRemoveNA(qprefix);        
    }
}

function sendRemoveNA(qprefix){
    swal({
        title: fnT('Alerta'),
        text: fnT('Esta pergunta está desativada; deseja habilitá-la?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Sim'),
        cancelButtonText: fnT('Não')
    }, function(isConfirm){
        if(isConfirm){
            $('#divLoading').css('display', 'flex');
            const payload = new FormData();
            payload.append('audit_id', audit_id);
            payload.append('question_prefix', qprefix);
            fetch(base_url + '/audit_Opp/removeNA', {
                method: 'POST',
                body: payload
            }).then(res => res.json()).then(dat => {
                $('#divLoading').css('display', 'none');
                if(dat.status != 1){
                    swal({
                        title: fnT('Erro'),
                        text: fnT('Ocorreu um erro'),
                        type: 'error'
                    });
                }else{
                    console.log('success');
                    $('#points'+qprefix).removeClass('btn-dark').addClass('btn-success');
                    $('#cpicklist' + qprefix).collapse('show');
                    $('#bpicklist' + qprefix).data('na', 0);
                    refreshScore(dat.score);
                }
            });
        }
    });
}

//CERTTIS OBTENER DATOS
function obtenerdatosCerttis(audit_id) {
document.querySelector('#audit_id_certtis').value = audit_id;
let id_audit_opp = document.getElementById('id_audit_opp_certtis').value
lineaTiempoCerttis(audit_id, id_audit_opp);
}


//CERTTIS LINEA TIEMPO

function lineaTiempoCerttis(audit_id,id_audit_opp){
   
    $.ajax({
           type: "POST",
           url:  base_url+"/certtis/selectLineaCertis",
		   data: {id_audit_opp},
           dataType: "json",
    success: function(data){
	    console.log(data);
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

				//formCerttis.reset();
			}else{
				swal(fnT('Erro'), fnT(dat.msg), "error");
			}
			divLoading.style.display = "none";
		});
	});