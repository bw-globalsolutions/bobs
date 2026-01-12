const validateEmail = inputEmail => regExEmail.test(inputEmail);
const loader = document.getElementById('lodaer');
const btnSubmitLogin = document.getElementById('btn-submit-login');

window.onload = function() {
  this.document.querySelector('.lblMarca').style.animationName='lblM';
};

document.getElementById('email').focus();

const logIn = element => {
	loader.classList.remove('d-none');
	btnSubmitLogin.disabled = true;
	
	const payload = new FormData(element);
	fetch( base_url + '/login/loginUser', {
		method: 'POST',
		body: payload
	}).then(res => res.json()).then(dat => {
		if(dat.status == 1){
			document.querySelector('.contW').style.display='flex';
			document.querySelector('.nameU').innerHTML=dat.name;
			document.querySelector('.lblW').style.animationName='lblM';
			setTimeout(()=>{window.location.reload();}, 1000);
		}else{
			console.log(dat);
			loader.classList.add('d-none');
			btnSubmitLogin.disabled = false;
			swal({
				title: 'Error',
				text: fnT('Os dados informados não estão corretos'),
				type: 'error'
			})
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

                    if(objData[0].img1!=''){
						document.getElementById('login-bg').style.background='#ffffff url('+objData[0].img1+') no-repeat left center / cover';
					}
					
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

const recoverPass = () => swal({
	title: fnT('Recuperar senha'),
	text: fnT('Por favor, informe seu e-mail'),
	type: 'input',
	confirmButtonText: fnT('Enviar'),
	showCancelButton: true,
	cancelButtonText: fnT('Cancelar'),
  }, function(inputValue){
	if(inputValue){
		if(validateEmail(inputValue)){
			const payload = new FormData();
			payload.append('email', inputValue)
			fetch( base_url + '/login/recoverPass', {
				method: 'POST',
				body: payload
			}).then(res => res.json()).then(dat => {
				if(dat.status == 1){
					swal({
						title: fnT('Sucesso'),
						text: fnT('Se o e-mail informado coincidir com nosso registro, você receberá um e-mail com mais instruções'),
						type: 'success'
					})
				} else{
					swal({
						title: 'Error',
						text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
						type: 'error'
					})
				}
			});
		}else{
			setTimeout(() => swal({
				title: 'Error',
				text: fnT('O e-mail informado não é válido'),
				type: 'error'
			}), 250);
		}
	}
});

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

const resetPassword = element => {
	if(element['password'].value == element['password2'].value){
		loader.classList.remove('d-none');
		btnSubmitLogin.disabled = true;
		
		const payload = new FormData(element);
		fetch( base_url + '/login/setPassword', {
			method: 'POST',
			body: payload
		}).then(res => res.json()).then(dat => {
			if(dat.status == 1){
				window.location.reload();
			} else{
				swal({
					title: 'Error',
					text: fnT('Ocorreu um erro no processo; se o problema persistir, entre em contato com o suporte'),
					type: 'error'
				});
				loader.classList.add('d-none');
				btnSubmitLogin.disabled = false;
			}
		});
	} else{
		swal({
			title: 'Error',
			text: fnT('As senhas não coincidem'),
			type: 'error'
		})
	}
}

sessionStorage.clear();