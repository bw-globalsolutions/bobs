function filterType(name){
    $('.section-items').removeClass('selected');
    $(`.section-items[data-tname='${name}']`).addClass('selected');

    $('.question-item').filter(function(){
        $(this).toggle($(this).data('tname') == name);
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

function sendQuestion(element, type){
    $('#divLoading').css('display', 'flex');
    const ctrl = type == 'UPLOAD_PICTURES'? 'Audit_File' : 'Audit_Addi_Question';
    const payload = new FormData(element);
    payload.append('audit_id', audit_id)
        
    fetch(`${base_url}/${ctrl}/changeResponse`, {
        method: 'POST',
        body: payload
    }).then(res => res.json()).then(dat => {
        if(dat.status == 1){
            const question_id = element['additional_question_item_id'].value;
            
            $(`#form-question${question_id} .control`).attr('disabled', true);
            $(`#form-question${question_id} .edit`).removeClass('d-none');
            $(`#form-question${question_id} .save, #form-question${question_id} .clean`).addClass('d-none');
            
        }else console.error(dat);
        $('#divLoading').css('display', 'none');
    });
}

function uploadPic(element, question_id){
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
                    $('#divLoading').css('display', 'none');
                    $(`#form-question${question_id} .panel-pic`).html(`<div class="mr-2 mb-3">
                        <a href="${dat.Info.location}" target="_blank">
                            <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="${dat.Info.location}">
                        </a>
                    </div>`);
                    $(`#form-question${question_id} [name="url_pic"]`).val(dat.Info.location);
                }else console.error(dat);
            });
        }else{
            swal({
                title: fnT('Erro'),
                text: fnT('Formato não suportado'),
                type: 'error'
            });
        }
        element.value = '';
    }
}

function dropImg(question_id){
    $(`#form-question${question_id} [name="url_pic"]`).val('');
    $(`#form-question${question_id} .panel-pic`).html('');
}

function editQuestion(question_id){
    if(editRestricted){
        swal({
            title: fnT('Alerta'),
            text: fnT('Deseja ativar o modo de edição?'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: fnT('Sim'),
            cancelButtonText: fnT('Não')
        }, function(isConfirm){
            if(isConfirm){
                $(`#form-question${question_id} .control`).removeAttr('disabled');
                $(`#form-question${question_id} .edit`).addClass('d-none');
                $(`#form-question${question_id} .save, #form-question${question_id} .clean`).removeClass('d-none');
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

function cleanQuestion(question_id){
    $(`#form-question${question_id} [name="answer"]`).val('');
    dropImg(question_id);
}