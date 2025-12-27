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