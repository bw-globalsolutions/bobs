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


const moveAuditRound = (element) => {
    swal({
        title: fnT('Alert'),
        text: fnT('Are you sure you want to move this audit to the next round?'),
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
                    swal({
                        title: fnT('Success'),
                        text: fnT('Data saved successfully'),
                        type: 'success'
                    }).then(() => location.reload());
                }
            });
        } else{
            $('#input-status').val('')
        }
    });
}





