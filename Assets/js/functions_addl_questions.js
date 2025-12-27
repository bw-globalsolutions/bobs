function filterType(name){
    $('.section-items').removeClass('selected');
    $(`.section-items[data-tname='${name}']`).addClass('selected');

    $('.question-item').filter(function(){
        $(this).toggle($(this).data('tname') == name);
    });
}

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
    const file = element.files[0];
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
            title: fnT('Error'),
            text: fnT('Format not supported'),
            type: 'error'
        });
    }
    element.value = '';
}

function dropImg(question_id){
    $(`#form-question${question_id} [name="url_pic"]`).val('');
    $(`#form-question${question_id} .panel-pic`).html('');
}

function editQuestion(question_id){
    if(editRestricted){
        swal({
            title: fnT('Alert'),
            text: fnT('Do you want to activate the editing mode?'),
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: fnT('Yes'),
            cancelButtonText: fnT('No')
        }, function(isConfirm){
            if(isConfirm){
                $(`#form-question${question_id} .control`).removeAttr('disabled');
                $(`#form-question${question_id} .edit`).addClass('d-none');
                $(`#form-question${question_id} .save, #form-question${question_id} .clean`).removeClass('d-none');
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

function cleanQuestion(question_id){
    $(`#form-question${question_id} [name="answer"]`).val('');
    dropImg(question_id);
}