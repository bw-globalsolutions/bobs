const validateEmail = inputEmail => regExEmail.test(inputEmail);
const loader = document.getElementById('lodaer');
const btnSubmitLogin = document.getElementById('btn-submit-login');

const logIn = element => {
	loader.classList.remove('d-none');
	btnSubmitLogin.disabled = true;
	
	const payload = new FormData(element);
	fetch( base_url + '/login/loginUser', {
		method: 'POST',
		body: payload
	}).then(res => res.json()).then(dat => {
		if(dat.status == 1){
			window.location.reload();
		}else{
			loader.classList.add('d-none');
			btnSubmitLogin.disabled = false;
			swal({
				title: 'Error',
				text: fnT('The data entered is not correct'),
				type: 'error'
			})
		}
	});
}

const recoverPass = () => swal({
	title: fnT('Recover password'),
	text: fnT('Please enter your email'),
	type: 'input',
	confirmButtonText: fnT('Send'),
	showCancelButton: true,
	cancelButtonText: fnT('Cancel'),
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
						title: fnT('Success'),
						text: fnT('If the entered email coincides with our registry, you will receive an email with further instructions'),
						type: 'success'
					})
				} else{
					swal({
						title: 'Error',
						text: fnT('An error occurred in the process, if the problem persists please contact support'),
						type: 'error'
					})
				}
			});
		}else{
			setTimeout(() => swal({
				title: 'Error',
				text: fnT('The email entered is not valid'),
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
					text: fnT('An error occurred in the process, if the problem persists please contact support'),
					type: 'error'
				});
				loader.classList.add('d-none');
				btnSubmitLogin.disabled = false;
			}
		});
	} else{
		swal({
			title: 'Error',
			text: fnT('Passwords do not match'),
			type: 'error'
		})
	}
}

sessionStorage.clear();