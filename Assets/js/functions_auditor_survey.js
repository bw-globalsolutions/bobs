const formAuditorSurvey = document.getElementById('formAuditorSurvey');
const divLoading = document.getElementById('divLoading');
const base_url = "<?=base_url()?>";

console.log('JS AUDITOR SURVEY');

$('#btnFormAuditorSurvey').click(function (){
	var inputs = formAuditorSurvey, input = null, error = false;
	for(var i = 0, len = inputs.length; i < len; i++) {
		input = inputs[i];
		//console.log(input.value);
		if(input.value == '') {
			//alert("Please answer all questions");
			error = true;
		}
		//console.log(error);
	}
	if(error) {
		console.log('ERROR');
		swal('Error', 'Please answer all questions', "error");
	} else {

		console.log('JS SEND ANS');
		divLoading.style.display = "flex";
		const payload = new FormData(formAuditorSurvey);
		fetch(base+'/auditorSurvey/setAnswers', {
			method: 'POST',
			body: payload
		}).then(res => res.json()).then(dat => {
			if(dat.status){
				swal('Auditor Survey', dat.msg, "success");
				$('#btnFormAuditorSurvey').attr('disabled','disabled');
			}else{
				swal('Error', dat.msg, "error");
			}
			divLoading.style.display = "none";
		});
	}
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

function setAns(element){
	console.log('Funcion onclick');
    if($(element).is('label')){
		$(element).siblings().removeClass('btn-dark');
		$(element).addClass('btn-dark');
		$(element).siblings("input[type=hidden]").val($(element).html());
	}
}
