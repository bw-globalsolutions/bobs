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

function setAns(element){
	console.log('Funcion onclick');
    if($(element).is('label')){
		$(element).siblings().removeClass('btn-dark');
		$(element).addClass('btn-dark');
		$(element).siblings("input[type=hidden]").val($(element).html());
	}
}
