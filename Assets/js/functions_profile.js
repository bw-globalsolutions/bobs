const divLoading = document.getElementById('divLoading');

function sendProfile(element){
    if(element['password'].value || element['cpassword'].value){
        if(element['password'].value != element['cpassword'].value){
            swal({
                title: 'Error!!!',
                text: fnT('Passwords do not match'),
                type: 'error'
            });
            return;
        }
    }

    divLoading.style.display = "flex";
    const payload = new FormData(element);
    fetch(base_url + '/usuarios/setProfile', {
        method: 'POST',
        body: payload
    }).then(res => res.json()).then(dat => {
        if(dat.status){
            window.location.reload();
        }else{
            swal({
                title: fnT('Error'),
                text: fnT(dat.msg) || fnT('It was not possible to update your preferences, if the problem persists please contact support'),
                type: 'error'
            });
        }
        divLoading.style.display = "none";
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

const showPassword = () => {
	const inputPassword = document.querySelectorAll('.toggle-pass');
	inputPassword.forEach(item => {
		if (item.type === "password") {
		  item.type = "text";
		} else {
		  item.type = "password";
		}
	})
}

function openModalPerfil(){
    document.querySelectorAll('[data-defvalue]').forEach(item => item.value = item.getAttribute('data-defvalue'));
	$('#modalFormPerfil').modal('show');
}

const passChange = new Date();
passChange.setDate(lastUpdPassword.getDate() - 60);
if(lastUpdPassword < passChange){
    swal({
        title: 'Warning!!!',
        text: fnT('To continue using this platform it is necessary to update your password every 60 days'),
        type: 'warning'
    });
    openModalPerfil();
}

function uploadPic(element){
    const file = element.files[0];
    if(file.type.includes('image/') && file.size < 512000){
        $('#divLoading').css('display', 'flex');
        fetch('https://ws.bw-globalsolutions.com/WSAAA/receivePic.php?token=x', {
            method: 'POST',
            body: file
        }).then(res => res.json()).then(dat => {
            if(dat.Message == "SUCCESS"){
                $('#divLoading').css('display', 'none');
                $(`#visit-pic`).val(dat.Info.location);
            }else console.error(dat);
        });
    }else{
        swal({
            title: fnT('Error'),
            text: fnT('Format not supported'),
            type: 'error'
        });
        element.value = '';
    }
}