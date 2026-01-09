const moveAuditStatus = (element) => {
    swal({
        title: fnT('Alert'),
        text: fnT('Are you sure you want to move the status of this audit, this action will not send emails?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
    }, function(isConfirm){
        if(isConfirm){
            $('#divLoading').css('display', 'flex');
            const payload = new FormData(element);

            fetch(base_url + '/audits/moveAuditStatus', {
                method: 'POST',
                body: payload
            }).then(res => res.json()).then(dat => {
                $('#divLoading').css('display', 'none');
                console.log(dat);
                if(dat.status != 1){
                    swal({
                        title: fnT('Error'),
                        text: fnT('An error has occurred'),
                        type: 'error'
                    });
                }else{
                    location.reload();
                }
            });
        } else{
            $('#input-status').val('')
        }
    });
}

const moveAuditRound = (element) => {
    swal({
        title: fnT('Alert'),
        text: fnT('Are you sure you want to move this audit round?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
    }, function(isConfirm){
        if(isConfirm){
            $('#divLoading').css('display', 'flex');
            const payload = new FormData(element);

            fetch(base_url + '/audits/moveAuditRound', {
                method: 'POST',
                body: payload
            }).then(res => res.json()).then(dat => {
                $('#divLoading').css('display', 'none');
                console.log(dat);
                if(dat.status != 1){
                    swal({
                        title: fnT('Error'),
                        text: fnT('An error has occurred'),
                        type: 'error'
                    });
                }else{
                    location.reload();
                }
            });
        } else{
            
        }
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

const setSignaturePic = async (element) => {
    var success = false;
    const file = document.getElementById('signature_pic').files[0];
    const petUrlFile = fetch('https://ws.bw-globalsolutions.com/WSAAA/receivePic.php?token=x', {
        method: 'POST',
        body: file
    }).then(res => res.json());
    $('#divLoading').css('display', 'flex');
    const datUrlFile = await petUrlFile;

    if(datUrlFile.Message == "SUCCESS"){
        document.getElementById('signature_url_pic').value = datUrlFile.Info.location;
        const payload = new FormData(element);
        const petSetSignature = fetch(base_url + '/audits/setSignaturePic', {
            method: 'POST',
            body: payload
        }).then(res => res.json())
        const datSetSignature = await petSetSignature;
        if(datSetSignature.status == 1){
            success = true;
        }
    }

    if(success){
        location.reload();
        return;
    }

    swal({
        title: fnT('Error'),
        text: fnT('An error has occurred'),
        type: 'error'
    });
    document.getElementById('signature_pic').value = '';
    $('#divLoading').css('display', 'none');
}

const setFrontDoorPic = async (element) => {
    var success = false;
    const file = document.getElementById('front_door_pic').files[0];
    const petUrlFile = fetch('https://ws.bw-globalsolutions.com/WSAAA/receivePic.php?token=x', {
        method: 'POST',
        body: file
    }).then(res => res.json());
    $('#divLoading').css('display', 'flex');
    const datUrlFile = await petUrlFile;

    if(datUrlFile.Message == "SUCCESS"){
        document.getElementById('front_door_url_pic').value = datUrlFile.Info.location;
        const payload = new FormData(element);
        const petSetFrontDoor = fetch(base_url + '/audits/setFrontDoorPic', {
            method: 'POST',
            body: payload
        }).then(res => res.json())
        const datSetFrontDoor = await petSetFrontDoor;
        if(datSetFrontDoor.status == 1){
            success = true;
        }
    }

    if(success){
        location.reload();
        return;
    }

    swal({
        title: fnT('Error'),
        text: fnT('An error has occurred'),
        type: 'error'
    });
    document.getElementById('front_door_pic').value = '';
    $('#divLoading').css('display', 'none');
}

