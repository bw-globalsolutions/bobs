//alert("hola");

//permitir sólo números
function controlTag(e){
	tecla = (document.all) ? e.keyCode : e.which;
	if(tecla==8) return true;
	else if(tecla==0||tecla==9) return true;
	patron =/[0-9\s]/;
	n = String.fromCharCode(tecla);
	return patron.test(n);
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

//validar campos de texto
function testText(txtString){
	var stringText = new RegExp(/^[a-zA-ZÑñÁáÉéÍíÓóÚúÜü\s]+$/);
	if(stringText.test(txtString)){
		return true;
	}else{
		return false;
	}
}

//validar numero enteros
function testEntero(intCant){
	var intCantidad = new RegExp(/^([0-9])*$/);
	if(intCantidad.test(intCant)){
		return true;
	}else{
		return false;
	}
}

//validar Email
function fntEmailValidate(email){
	var stringEmail = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
	if(stringEmail.test(email) == false){
		return false;
	}else{
		return true;
	}
}

function fntValidText(){
	let validText = document.querySelectorAll(".validText");
	validText.forEach(function(validText){
		validText.addEventListener('keyup', function(){
			let inputValue = this.value;
			if(!testText(inputValue)){
				this.classList.add('is-invalid');
			}else{
				this.classList.remove('is-invalid');
			}
		});
	});
}

function fntValidNumber(){
	let validNumber = document.querySelectorAll(".validNumber");
	validNumber.forEach(function(validNumber){
		validNumber.addEventListener('keyup', function(){
			let inputValue = this.value;
			if(!testEntero(inputValue)){
				this.classList.add('is-invalid');
			}else{
				this.classList.remove('is-invalid');
			}
		});
	});
}

function fntValidEmail(){
	let validEmail = document.querySelectorAll(".validEmail");
	validEmail.forEach(function(validEmail){
		validEmail.addEventListener('keyup', function(){
			let inputValue = this.value;
			if(!fntEmailValidate(inputValue)){
				this.classList.add('is-invalid');
			}else{
				this.classList.remove('is-invalid');
			}
		});
	});
}

window.addEventListener('load', function(){
	fntValidText();
	fntValidNumber();
	fntValidEmail();
}, false);