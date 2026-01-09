async function sendGrlInfo(element){

    const payloadInfo = new FormData(element);
    if(!validTime(payloadInfo.get('start_time'), payloadInfo.get('end_time'))){
        swal({
            title: fnT('Error'),
            text: fnT('The initial time cannot be longer than the end'),
            type: 'error'
        });
        return;
    }
    
    $('#divLoading').css('display', 'flex');
    let insInfo = fetch(base_url + '/audits/updGrlInfo', {
        method: 'POST',
        body: payloadInfo
    }).then(res => res.json());
    let arrPet = [insInfo];

    if($('#visit-pic').val() != ''){
        const payloadImg = new FormData();
        payloadImg.append('audit_id', $('#audit-id').val());
        payloadImg.append('url', $('#visit-pic').val());
        let insImg = fetch(base_url + '/audit_File/insertPicFD', {
            method: 'POST',
            body: payloadImg
        }).then(res => res.json());
        arrPet.push(insImg);
    }

    let response = await Promise.all(arrPet);
    if(response[0].status == 1){
        location.href = element['visit_status'].value == 'Visited'? urlChecklist : urlAudits;
    }else console.error(dat);
    
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

function validTime(start, end){
    var hora1 = new Date();
    hora1.setHours(start.split(':')[0]);
    hora1.setMinutes(start.split(':')[1]);

    var hora2 = new Date();
    hora2.setHours(end.split(':')[0]);
    hora2.setMinutes(end.split(':')[1]);

    return hora1.getTime() < hora2.getTime();
}

function changueStatus(val){
    switch (val) {
        case 'Visited':
            $('#manager-email').removeAttr('disabled', true);
            $('#manager-name').removeAttr('disabled', true);
            
            $("#additional-comment").prop('required', false);
            break;
        case 'Closed':
            $('#manager-email').val('');
            $('#manager-name').val('');

            $('#manager-email').attr('disabled', true);
            $('#manager-name').attr('disabled', true);
            $("#additional-comment").prop('required', true);
            break;
      }
}

function uploadPic(element){
    const file = element.files[0];
    if(file.type.includes('image/')){
        $('#divLoading').css('display', 'flex');
        fetch('https://ws.bw-globalsolutions.com/WSAAA/receivePic.php?token=x', {
            method: 'POST',
            body: file
        }).then(res => res.json()).then(dat => {
            if(dat.Message == "SUCCESS"){
                $('#divLoading').css('display', 'none');
                $('#panel-pic').html(`<div class="mr-2 mb-2">
                    <a href="${dat.Info.location}" target="_blank">
                        <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="${dat.Info.location}">
                    </a><br>
                    <span class ="badge badge-pill badge-danger mt-1 cr-pointer" onclick="dropImg()">
                        <i class="fa fa-trash"></i>&nbsp; ${fnT('Delete')}
                    </span>
                </div>`);
                $('#visit-pic').val(dat.Info.location);
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

function dropImg(){
    $('#visit-pic').val('');
    $('#panel-pic').html('');
}