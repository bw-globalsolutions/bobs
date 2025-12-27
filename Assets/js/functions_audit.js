function filterSection(number){
    $('.section-items').removeClass('selected');
    $(`#section${number}`).addClass('selected');

    $('.question-item').filter(function(){
        $(this).toggle($(this).data('snumber').toString() == number);
    });
}

function openOpportunity(picklist_id, qprefix, snumber){
    $('#divLoading').css('display', 'flex');
    
    const payload = new FormData();
    payload.append('picklist_id', picklist_id);
    payload.append('audit_id', audit_id);
	
    fetch( base_url + '/audits/getAnswers', {
        method: 'POST',
        body: payload
    }).then(res => res.json()).then(dat => {
       $('#list-answers').html(dat.answers.reduce((acc, cur) => {
            acc += `<li class="list-group-item list-group-item-action d-flex justify-content-between">
                <span>${cur.text}</span>
                <div class="toggle-flip success-danger ml-3">
                    <label class="m-0"><input value="${cur.text}" type="checkbox" ${cur.opp?'checked':''} ${editRestricted? '' : 'disabled'}>
                        <span class="flip-indecator" data-toggle-on="${fnT('No')}" data-toggle-off="${fnT('Yes')}"></span>
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
                <span class ="badge badge-pill badge-danger mt-1 cr-pointer sw-he d-none" onclick="removeImg(${cur.id})">
                    <i class="fa fa-trash"></i>&nbsp; ${fnT('Delete')}
                </span>
            </div>`;
            return acc;
        }, ''));

        stackImg = [];
        currQPrefix = qprefix;
        currSNumber = snumber;
        $('#checklist_item_id').val(picklist_id);
        $('#opp_id').val(dat.opp_id);
        $('#btn-remove-opp').css('display', dat.opp_id!=''? 'block' : 'none');
        $('#divLoading').css('display', 'none');
        $('#modalViewAnwers').modal('show');
    });
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
                const idImg = Date.now();
                $('#divLoading').css('display', 'none');
                $('#auditor-files').append(`<div class="mr-2 mb-2" id="img${idImg}">
                    <a href="${dat.Info.location}" target="_blank">
                        <img style="height:85px; width:85px" class="rounded shadow-sm of-cover cr-pointer" src="${dat.Info.location}">
                    </a><br>
                    <span class ="badge badge-pill badge-danger mt-1 cr-pointer" onclick="dropImg(${idImg}, '${dat.Info.location}')">
                        <i class="fa fa-trash"></i>&nbsp; ${fnT('Delete')}
                    </span>
                </div>`);
                stackImg.push(dat.Info.location);
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
		});
    }else{
        swal({
            title: fnT('Error'),
            text: fnT('To mark an opportunity, it is necessary to select a reason'),
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
        title: fnT('Alert'),
        text: fnT('Are you sure you want to eliminate this opportunity?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
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
    $('#score-critics').html(score.Criticos);
    $('#score-nocritics').html(score.NoCriticos);
    //$('#score-green').html(score.Verdes);
    $('#score-yellow').html(score.Amarillos);
    $('#score-red').html(score.Rojos);
    $('#score-maintenance').html(score.Mantenimiento);
    $('#score-autofail').html(score.AutoFail);
    // $('#score-majors').html(score.Mayores);
    // $('#score-minors').html(score.Menores);
}

function removeImg(file_id){
    swal({
        title: fnT('Alert'),
        text: fnT('Are you sure you want to delete this image?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
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
            title: fnT('Alert'),
            text: fnT('Are you sure you want to remove this question?'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: fnT('Yes'),
            cancelButtonText: fnT('No')
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
                            title: fnT('Error'),
                            text: fnT('An error has occurred'),
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
            title: fnT('Error'),
            text: fnT('It is not possible to edit a finished audit'),
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
        title: fnT('Alert'),
        text: fnT('This question is disabled, do you want to enable it?'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: fnT('Yes'),
        cancelButtonText: fnT('No')
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
                        title: fnT('Error'),
                        text: fnT('An error has occurred'),
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